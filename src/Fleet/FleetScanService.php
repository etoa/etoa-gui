<?php

declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceTechnologyId;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseRepository;
use EtoA\Fleet\Exception\FleetScanFailedException;
use EtoA\Fleet\Exception\FleetScanPreconditionsNotMetException;
use EtoA\Fleet\Exception\InvalidFleetScanParameterException;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Specialist\SpecialistDataRepository;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Entity\Entity;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Planet\Planet;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\User;
use EtoA\User\UserRepository;

/**
 * Conducts crypto center scan functionality
 */
class FleetScanService
{
    private ConfigurationService $config;
    private UserRepository $userRepository;
    private PlanetRepository $planetRepository;
    private EntityRepository $entityRepository;
    private FleetRepository $fleetRepository;
    private EntityService $entityService;
    private DefenseRepository $defenseRepository;
    private TechnologyRepository $technologyRepository;
    private ShipDataRepository $shipDataRepository;
    private MessageRepository $messageRepository;
    private AllianceBuildingRepository $allianceBuildingRepository;
    private AllianceTechnologyRepository $allianceTechnologyRepository;
    private AllianceRepository $allianceRepository;
    private AllianceHistoryRepository $allianceHistoryRepository;
    private SpecialistDataRepository $specialistDataRepository;

    private const FLEET_DIRECTION_ARRIVING = 'arriving';
    private const FLEET_DIRECTION_DEPARTING = 'departing';

    private const DECRYPT_LEVEL_NUMBER_OF_FLEETS = 0;
    private const DECRYPT_LEVEL_INDIVIDUAL_FLEETS = 10;
    private const DECRYPT_LEVEL_INDIVIDUAL_SHIPS = 15;
    private const DECRYPT_LEVEL_INDIVIDUAL_SHIPS_TOTAL = 20;
    private const DECRYPT_LEVEL_INDIVIDUAL_SHIP_COUNT = 25;
    private const DECRYPT_LEVEL_TIME_QUARTER_HOUR = 15;
    private const DECRYPT_LEVEL_TIME_MINUTES = 20;
    private const DECRYPT_LEVEL_TIME_SECONDS = 25;
    private const DECRYPT_LEVEL_FLEET_ACTION = 30;

    public function __construct(
        ConfigurationService $config,
        UserRepository $userRepository,
        PlanetRepository $planetRepository,
        EntityRepository $entityRepository,
        FleetRepository $fleetRepository,
        EntityService $entityService,
        DefenseRepository $defenseRepository,
        TechnologyRepository $technologyRepository,
        ShipDataRepository $shipDataRepository,
        MessageRepository $messageRepository,
        AllianceBuildingRepository $allianceBuildingRepository,
        AllianceTechnologyRepository $allianceTechnologyRepository,
        AllianceRepository $allianceRepository,
        AllianceHistoryRepository $allianceHistoryRepository,
        SpecialistDataRepository $specialistDataRepository
    ) {
        $this->config = $config;
        $this->userRepository = $userRepository;
        $this->planetRepository = $planetRepository;
        $this->entityRepository = $entityRepository;
        $this->fleetRepository = $fleetRepository;
        $this->entityService = $entityService;
        $this->defenseRepository = $defenseRepository;
        $this->technologyRepository = $technologyRepository;
        $this->shipDataRepository = $shipDataRepository;
        $this->messageRepository = $messageRepository;
        $this->allianceBuildingRepository = $allianceBuildingRepository;
        $this->allianceTechnologyRepository = $allianceTechnologyRepository;
        $this->allianceRepository = $allianceRepository;
        $this->allianceHistoryRepository = $allianceHistoryRepository;
        $this->specialistDataRepository = $specialistDataRepository;
    }

    public function getUserCooldownDifference(int $userId): int
    {
        $userCooldown = $this->allianceBuildingRepository->getUserCooldown($userId, AllianceBuildingId::CRYPTO);
        if ($userCooldown > time()) {
            return $userCooldown - time();
        }

        return 0;
    }

    public function calculateCooldown(int $cryptoCenterLevel): int
    {
        return max(
            $this->config->getInt("crypto_min_cooldown"),
            $this->config->getInt("crypto_default_cooldown") - ($this->config->getInt("crypto_cooldown_reduction_per_level") * ($cryptoCenterLevel - 1))
        );
    }

    public function scanFleets(User $currentUser, Planet $planet, int $cryptoCenterLevel, ?Entity $targetEntity): string
    {
        $userCooldownDiff = $this->getUserCooldownDifference($currentUser->id);
        if ($userCooldownDiff > 0) {
            throw new FleetScanPreconditionsNotMetException("Diese Funktion wurde vor kurzem benutzt. Sie ist wieder verfügbar in " . StringUtils::formatTimespan($userCooldownDiff) . ".");
        }

        $cryptoFuelCostsPerScan = $this->config->getInt('crypto_fuel_costs_per_scan');
        if ($planet->resFuel < $cryptoFuelCostsPerScan) {
            throw new FleetScanPreconditionsNotMetException("Zuwenig " . ResourceNames::FUEL . ", " . StringUtils::formatNumber($cryptoFuelCostsPerScan) . " benötigt, " . StringUtils::formatNumber($planet->resFuel) . " vorhanden!");
        }

        $alliance = $this->allianceRepository->getAlliance($currentUser->allianceId);
        if ($alliance->resFuel < $cryptoFuelCostsPerScan) {
            throw new FleetScanPreconditionsNotMetException("Zuwenig Allianzrohstoffe " . ResourceNames::FUEL . ", " . StringUtils::formatNumber($cryptoFuelCostsPerScan) . " benötigt, " . StringUtils::formatNumber($alliance->resFuel) . " vorhanden!");
        }

        if ($targetEntity === null || $targetEntity->code != EntityType::PLANET) {
            throw new InvalidFleetScanParameterException('Am gewählten Ziel existiert kein Planet!');
        }

        $sourceEntity = $this->entityRepository->findIncludeCell($planet->id);
        $dist = $this->entityService->distance($sourceEntity, $targetEntity);
        $cryptoRangePerLevel = $this->config->getInt('crypto_range_per_level');
        if ($dist > $cryptoRangePerLevel * $cryptoCenterLevel) {
            throw new InvalidFleetScanParameterException("Das Ziel ist zu weit entfernt (" . StringUtils::formatNumber(ceil($dist)) . " AE, momentan sind " . StringUtils::formatNumber($cryptoRangePerLevel * $cryptoCenterLevel) . " möglich, " . $cryptoRangePerLevel . " pro Gebäudestufe)!");
        }

        $cooldownTime = time() + $this->calculateCooldown($cryptoCenterLevel);
        $this->allianceBuildingRepository->setUserCooldown($currentUser->id, AllianceBuildingId::CRYPTO, $cooldownTime);

        $targetPlanet = $this->planetRepository->find($targetEntity->id);
        $this->allianceHistoryRepository->addEntry($currentUser->allianceId, "Der Spieler [b]" . $currentUser->nick . "[/b] hat den Planeten " . $targetPlanet->name . "[/b] (" . $targetEntity->coordinatesString() . ") gescannt!");

        $targetOwner = $this->userRepository->getUser($targetPlanet->userId);
        $opJam = $this->defenseRepository->countJammingDevicesOnEntity($targetEntity->id);
        $opStealth = $this->getStealthTechLevel($targetOwner);
        $opComputer = $this->getComputerTechLevel($targetOwner);

        $selfSpy = $this->getSpyTechLevel($currentUser);
        $selfComputer = $this->getComputerTechLevel($currentUser);

        $chance = $this->calculateChance($cryptoCenterLevel, $selfSpy, $opJam, $opStealth);
        if ($chance < 0) {
            if ($opJam > 0 && $targetOwner !== null) {
                $this->messageRepository->createSystemMessage(
                    $targetOwner->id,
                    MessageCategoryId::SHIP_SPY,
                    "Störsender erfolgreich",
                    "Eure Techniker haben festgestellt, dass von einem anderen Planeten eine Entschlüsselung eures Funkverkehrs versucht wurde. Daraufhin haben eure Störsender die Funknetze mit falschen Werten überlastet, so dass die gegnerische Analyse fehlschlug!"
                );
            }

            throw new FleetScanFailedException("Die Analyse schlug leider fehl! Eure Empfangsgeräte haben zu viel Rauschen aufgenommen; anscheinend hat der Zielplanet ein aktives Störfeld oder die dortige Flottenkontrolle ist zu gut getarnt (Chance: " . $chance . ")!");
        }

        $decryptLevel = $this->calculateDecryptLevel($cryptoCenterLevel, $selfSpy, $selfComputer, $opJam, $opStealth, $opComputer);

        $out = "[b]Flottenscan vom Planeten " . $targetPlanet->name . "[/b] (" . $targetEntity->coordinatesString() . ")\n\n";
        $out .= $this->fleetReport($targetEntity, self::FLEET_DIRECTION_ARRIVING, $decryptLevel);
        $out .= $this->fleetReport($targetEntity, self::FLEET_DIRECTION_DEPARTING, $decryptLevel);
        $out .= "\n\nEntschlüsselchance: $decryptLevel";

        // Subtract resources
        $this->planetRepository->addResources($planet->id, 0, 0, 0, -$cryptoFuelCostsPerScan, 0);
        $this->allianceRepository->addResources($currentUser->allianceId, 0, 0, 0, -$cryptoFuelCostsPerScan, 0);

        if ($targetOwner !== null) {
            $this->messageRepository->createSystemMessage(
                $targetOwner->id,
                MessageCategoryId::SHIP_SPY,
                "Funkstörung",
                "Eure Flottenkontrolle hat soeben eine kurzzeitige Störung des Kommunikationsnetzes festgestellt. Es kann sein, dass fremde Spione in das Netz eingedrungen sind und Flottendaten geklaut haben."
            );
        }

        $this->messageRepository->createSystemMessage(
            $currentUser->id,
            MessageCategoryId::MISC,
            "Kryptocenter-Bericht",
            $out
        );

        return $out;
    }

    private function getStealthTechLevel(?User $user): int
    {
        if ($user === null) {
            return 0;
        }

        $value = $this->technologyRepository->getTechnologyLevel($user->id, TechnologyId::TARN);

        if ($user->allianceId > 0) {
            $value += $this->allianceTechnologyRepository->getLevel($user->allianceId, AllianceTechnologyId::TARN);
        }

        $specialist = $this->specialistDataRepository->getSpecialist($user->specialistId);
        if ($specialist !== null) {
            $value += $specialist->tarnLevel;
        }

        return $value;
    }

    private function getComputerTechLevel(?User $user): int
    {
        if ($user === null) {
            return 0;
        }

        return $this->technologyRepository->getTechnologyLevel($user->id, TechnologyId::COMPUTER);
    }

    private function getSpyTechLevel(?User $user): int
    {
        if ($user === null) {
            return 0;
        }

        $value = $this->technologyRepository->getTechnologyLevel($user->id, TechnologyId::SPY);

        if ($user->allianceId > 0) {
            $value += $this->allianceTechnologyRepository->getLevel($user->allianceId, AllianceTechnologyId::SPY);
        }

        $specialist = $this->specialistDataRepository->getSpecialist($user->specialistId);
        if ($specialist !== null) {
            $value += $specialist->spyLevel;
        }

        return $value;
    }
    private function calculateChance(int $cryptoCenterLevel, int $selfSpy, int $opJam, int $opStealth): float
    {
        return ($cryptoCenterLevel - $opJam) + (0.3 * ($selfSpy - $opStealth)) + random_int(0, 2) - 1;
    }

    private function calculateDecryptLevel(int $cryptoCenterLevel, int $selfSpy, int $selfComputer, int $opJam, int $opStealth, int $opComputer): float
    {
        return ($cryptoCenterLevel - $opJam) + (0.75 * ($selfSpy + $selfComputer - $opStealth - $opComputer)) + random_int(0, 2) - 1;
    }

    private function fleetReport(Entity $targetEntity, string $direction, float $decryptLevel): string
    {
        $out = "";
        if ($direction == self::FLEET_DIRECTION_ARRIVING) {
            $out .= "[b]Eintreffende Flotten[/b]\n\n";
        } elseif ($direction == self::FLEET_DIRECTION_DEPARTING) {
            $out .= "[b]Wegfliegende Flotten[/b]\n\n";
        }
        $params = new FleetSearchParameters();
        if ($direction == self::FLEET_DIRECTION_ARRIVING) {
            $params->entityTo = $targetEntity->id;
        } elseif ($direction == self::FLEET_DIRECTION_DEPARTING) {
            $params->entityFrom = $targetEntity->id;
        }

        $fleets = $this->fleetRepository->findByParameters($params);
        if (count($fleets) > 0) {
            if ($decryptLevel >= self::DECRYPT_LEVEL_INDIVIDUAL_FLEETS) {
                $out .= "Es sind " . count($fleets) . " Flotten unterwegs:\n\n";
                foreach ($fleets as $fleet) {
                    $out .= $this->individualFleetReport($fleet, self::FLEET_DIRECTION_ARRIVING, $decryptLevel);
                }
            } elseif ($decryptLevel >= self::DECRYPT_LEVEL_NUMBER_OF_FLEETS) {
                $out .= "Es sind " . count($fleets) . " Flotten unterwegs\n\n";
            } else {
                $out .= "Es sind Flotten unterwegs\n\n";
            }
        } else {
            if ($direction == self::FLEET_DIRECTION_ARRIVING) {
                $out .= "Keine eintreffenden Flotten gefunden!\n\n";
            } elseif ($direction == self::FLEET_DIRECTION_DEPARTING) {
                $out .= 'Keine abfliegenden Flotten gefunden!';
            }
        }

        return $out;
    }

    private function individualFleetReport(Fleet $fleet, string $direction, float $decryptLevel): string
    {
        $fleetSourceEntity = $this->entityRepository->findIncludeCell($fleet->entityFrom);
        $fleetOwner = $this->userRepository->getUser($fleet->userId);

        $out = '[b]Besitzer:[/b] ' . ($fleetOwner !== null ? $fleetOwner->nick : 'Unbekannt') . "\n";

        if ($direction == self::FLEET_DIRECTION_ARRIVING) {
            $out .= '[b]Herkunft:[/b] ' . $fleetSourceEntity->toString();
        } elseif ($direction == self::FLEET_DIRECTION_DEPARTING) {
            $out .= '[b]Ziel:[/b] ' . $fleetSourceEntity->toString();
        }

        $out .= "\n[b]Ankunft:[/b] ";
        if ($decryptLevel >= self::DECRYPT_LEVEL_TIME_SECONDS) {
            $out .= date("d.m.Y H:i:s", $fleet->landTime) . " Uhr";
        } elseif ($decryptLevel >= self::DECRYPT_LEVEL_TIME_MINUTES) {
            $out .= date("d.m.Y H:i", $fleet->landTime) . " Uhr";
        } elseif ($decryptLevel >= self::DECRYPT_LEVEL_TIME_QUARTER_HOUR) {
            $rand = random_int(0, 2 * 7 * 60);
            $out .= "Zwischen " . date("d.m.Y H:i", $fleet->landTime - $rand) . " und " . date("d.m.Y H:i", $fleet->landTime + (2 * 7 * 60) - $rand) . " Uhr";
        } else {
            $rand = random_int(0, 30 * 60 * 2);
            $out .= "Zwischen " . date("d.m.Y H:i", $fleet->landTime - $rand) . " und " . date("d.m.Y H:i", $fleet->landTime + (2 * 30 * 60) - $rand) . " Uhr";
        }

        if ($decryptLevel >= self::DECRYPT_LEVEL_FLEET_ACTION) {
            $action = LegacyFleetAction::createFactory($fleet->action);
            $out .= "\n[b]Aktion:[/b] " . substr((string) $action, 25, -7) . "\n";
        } else {
            $out .= "\n";
        }

        if ($decryptLevel >= self::DECRYPT_LEVEL_INDIVIDUAL_SHIPS) {
            $shipEntries = $this->fleetRepository->findAllShipsInFleet($fleet->id);
            $totalShips = 0;
            $shipNames = $this->shipDataRepository->getAllShips();
            foreach ($shipEntries as $shipEntry) {
                $shipName = $shipNames[$shipEntry->id] ?? 'Unbekannt';
                if ($decryptLevel >= self::DECRYPT_LEVEL_INDIVIDUAL_SHIP_COUNT) {
                    $out .= $shipEntry->count . " " . $shipName . "\n";
                } else {
                    $out .= $shipName . "\n";
                }
                $totalShips += $shipEntry->count;
            }
            if ($decryptLevel >= self::DECRYPT_LEVEL_INDIVIDUAL_SHIPS_TOTAL) {
                $out .= $totalShips . " Schiffe total\n";
            }
        }
        $out .= "\n";

        return $out;
    }
}
