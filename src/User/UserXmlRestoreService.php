<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\ORM\EntityManagerInterface;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipListRepository;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Entity\User;
use EtoA\Universe\Planet\PlanetRepository;
use Exception;
use SimpleXMLElement;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Rekonstruiert einen endgültig gelöschten User (inkl. Planeten, Gebäude,
 * Technologien, Schiffe, Verteidigung) aus dem XML-Backup, das beim Löschen
 * automatisch erstellt wurde (siehe UserToXml).
 *
 * Das XML ist ein verlustbehafteter Export (kein vollständiges Serialisierungs-
 * format): Passwort, Planeten-Koordinaten, Speicherkapazitäten, Warteschlangen,
 * Nachrichten/Reports/Logs und der Flugstatus unterwegs befindlicher Flotten
 * werden nicht wiederhergestellt. Planeten, deren ursprüngliche Zelle inzwischen
 * neu besiedelt wurde, können mangels Koordinaten im XML nicht wiederhergestellt
 * werden und werden übersprungen. Die Allianz-Zugehörigkeit wird nicht wieder
 * gesetzt, da der dafür nötige Beitritts-Code aktuell nicht funktionsfähig ist.
 */
class UserXmlRestoreService
{
    public function __construct(
        private readonly EntityManagerInterface       $entityManager,
        private readonly UserRepository                $userRepository,
        private readonly UserPasswordHasherInterface   $passwordHasher,
        private readonly RaceDataRepository            $raceRepository,
        private readonly PlanetRepository              $planetRepository,
        private readonly BuildingRepository            $buildingRepository,
        private readonly BuildingListItemRepository    $buildingListItemRepository,
        private readonly TechnologyDataRepository      $technologyRepository,
        private readonly TechnologyListItemRepository  $technologyListItemRepository,
        private readonly ShipDataRepository            $shipRepository,
        private readonly ShipListRepository            $shipListRepository,
        private readonly DefenseDataRepository         $defenseRepository,
        private readonly DefenseRepository             $defenseListRepository,
    ) {
    }

    public function restore(string $xmlFile): UserXmlRestoreResult
    {
        $xml = simplexml_load_file($xmlFile);
        if ($xml === false) {
            throw new Exception("XML-Datei konnte nicht gelesen werden: " . $xmlFile);
        }

        return $this->entityManager->wrapInTransaction(function () use ($xml) {
            return $this->restoreFromXml($xml);
        });
    }

    private function restoreFromXml(SimpleXMLElement $xml): UserXmlRestoreResult
    {
        $account = $xml->account;

        $raceId = (int) $account->race['id'];
        $race = $raceId > 0 ? $this->raceRepository->find($raceId) : null;

        $plainPassword = bin2hex(random_bytes(6));
        $hashedPassword = $this->passwordHasher->hashPassword(new User(), $plainPassword);

        $user = $this->userRepository->create(
            (string) $account->nick,
            (string) $account->name,
            (string) $account->email,
            $hashedPassword,
            $race,
            false
        );

        $result = new UserXmlRestoreResult($user, $plainPassword);

        $allianceTag = (string) $account->alliance['tag'];
        $allianceName = (string) $account->alliance;
        if ($allianceTag !== '' || $allianceName !== '') {
            $result->originalAlliance = trim($allianceName . ($allianceTag !== '' ? " [$allianceTag]" : ''));
        }

        /** @var array<int, \EtoA\Entity\Planet> $restoredPlanets entityId => Planet */
        $restoredPlanets = [];
        $mainEntityId = null;

        foreach ($xml->planets->planet as $planetXml) {
            $entityId = (int) $planetXml['id'];
            $name = (string) $planetXml['name'];
            $isMain = (int) $planetXml['main'] === 1;

            $planet = $this->planetRepository->find($entityId);

            if ($planet === null) {
                $result->skippedPlanets[] = "Planet #$entityId ($name): Zelle existiert nicht mehr";
                continue;
            }

            if ($planet->getUser() !== null) {
                $result->skippedPlanets[] = "Planet #$entityId ($name): Zelle ist bereits von einem anderen Spieler besiedelt";
                continue;
            }

            $this->planetRepository->assignToUser($planet, $user, $isMain);
            $planet->setName($name);
            $this->planetRepository->setResources(
                $planet,
                (int) $planetXml->metal,
                (int) $planetXml->crystal,
                (int) $planetXml->plastic,
                (int) $planetXml->fuel,
                (int) $planetXml->food,
                (int) $planetXml->people
            );

            $restoredPlanets[$entityId] = $planet;
            $result->restoredPlanets[] = "Planet #$entityId ($name)";

            if ($isMain) {
                $mainEntityId = $entityId;
            }
        }

        if ($mainEntityId === null && count($restoredPlanets) > 0) {
            $mainEntityId = array_key_first($restoredPlanets);
        }

        foreach ($xml->buildings->building as $buildingXml) {
            $entityId = (int) $buildingXml['planet'];
            if (!isset($restoredPlanets[$entityId])) {
                continue;
            }

            $building = $this->buildingRepository->find((int) $buildingXml['id']);
            if ($building === null) {
                $result->skippedItems[] = "Gebäude '" . (string) $buildingXml . "' existiert nicht mehr";
                continue;
            }

            $this->buildingListItemRepository->addBuilding(
                $building,
                (int) $buildingXml['level'],
                $user,
                $restoredPlanets[$entityId]
            );
        }

        if ($mainEntityId !== null) {
            foreach ($xml->technologies->technology as $techXml) {
                $technology = $this->technologyRepository->find((int) $techXml['id']);
                if ($technology === null) {
                    $result->skippedItems[] = "Technologie '" . (string) $techXml . "' existiert nicht mehr";
                    continue;
                }

                $this->technologyListItemRepository->addTechnology(
                    $technology,
                    (int) $techXml['level'],
                    $user,
                    $restoredPlanets[$mainEntityId]->getEntity()
                );
            }
        } elseif (count(iterator_to_array($xml->technologies->technology)) > 0) {
            $result->skippedItems[] = "Technologien konnten nicht wiederhergestellt werden, da kein Planet verfügbar ist";
        }

        foreach ($xml->ships->ship as $shipXml) {
            $entityId = (int) $shipXml['planet'];
            if (!isset($restoredPlanets[$entityId])) {
                continue;
            }

            $ship = $this->shipRepository->find((int) $shipXml['id']);
            if ($ship === null) {
                $result->skippedItems[] = "Schiff '" . (string) $shipXml . "' existiert nicht mehr";
                continue;
            }

            $this->shipListRepository->addShip(
                $ship,
                (int) $shipXml['count'],
                $user,
                $restoredPlanets[$entityId]
            );
        }

        foreach ($xml->defenses->defense as $defenseXml) {
            $entityId = (int) $defenseXml['planet'];
            if (!isset($restoredPlanets[$entityId])) {
                continue;
            }

            $defense = $this->defenseRepository->find((int) $defenseXml['id']);
            if ($defense === null) {
                $result->skippedItems[] = "Verteidigung '" . (string) $defenseXml . "' existiert nicht mehr";
                continue;
            }

            $this->defenseListRepository->addDefense(
                $defense,
                (int) $defenseXml['count'],
                $user,
                $restoredPlanets[$entityId]
            );
        }

        return $result;
    }
}
