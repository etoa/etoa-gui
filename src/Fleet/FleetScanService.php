<?php

declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Alliance\AllianceBuildingCooldownRepository;
use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceTechnologyId;
use EtoA\Alliance\AllianceTechnologyListRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseRepository;
use EtoA\Entity\Entity;
use EtoA\Entity\Fleet;
use EtoA\Entity\Planet;
use EtoA\Entity\User;
use EtoA\Fleet\Exception\FleetScanFailedException;
use EtoA\Fleet\Exception\FleetScanPreconditionsNotMetException;
use EtoA\Fleet\Exception\InvalidFleetScanParameterException;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResourceNames;

/**
 * Conducts crypto center scan functionality
 */
class FleetScanService
{
    private const FLEET_DIRECTION_ARRIVING = 'arriving';
    private const FLEET_DIRECTION_DEPARTING = 'departing';

    public function __construct(
        private readonly ConfigurationService         $config,
        private readonly PlanetRepository             $planetRepository,
        private readonly FleetRepository              $fleetRepository,
        private readonly EntityService                $entityService,
        private readonly DefenseRepository            $defenseRepository,
        private readonly TechnologyListItemRepository $technologyRepository,
        private readonly MessageRepository            $messageRepository,
        private readonly AllianceRepository           $allianceRepository,
        private readonly AllianceHistoryRepository    $allianceHistoryRepository,
        private readonly AllianceBuildingCooldownRepository $allianceBuildingCooldownRepository,
        private readonly AllianceTechnologyListRepository $allianceTechnologyListRepository,
        private readonly MessageCategoryRepository $messageCategoryRepository,
        private readonly AllianceBuildingRepository $allianceBuildingRepository

    ) {}

    public function getUserCooldownDifference(User $user): int
    {
        $userCooldown = $this->allianceBuildingCooldownRepository->getUserCooldown($user, AllianceBuildingId::CRYPTO->value);
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
        $userCooldownDiff = $this->getUserCooldownDifference($currentUser);
        if ($userCooldownDiff > 0) {
            throw new FleetScanPreconditionsNotMetException("Diese Funktion wurde vor kurzem benutzt. Sie ist wieder verfügbar in " . StringUtils::formatTimespan($userCooldownDiff) . ".");
        }

        $cryptoFuelCostsPerScan = $this->config->getInt('crypto_fuel_costs_per_scan');
        if ($planet->getResFuel() < $cryptoFuelCostsPerScan) {
            throw new FleetScanPreconditionsNotMetException("Zuwenig " . ResourceNames::FUEL . ", " . StringUtils::formatNumber($cryptoFuelCostsPerScan) . " benötigt, " . StringUtils::formatNumber($planet->getResFuel()) . " vorhanden!");
        }

        $alliance = $currentUser->getAlliance();
        if ($alliance->getResFuel() < $cryptoFuelCostsPerScan) {
            throw new FleetScanPreconditionsNotMetException("Zuwenig Allianzrohstoffe " . ResourceNames::FUEL . ", " . StringUtils::formatNumber($cryptoFuelCostsPerScan) . " benötigt, " . StringUtils::formatNumber($alliance->getResFuel()) . " vorhanden!");
        }

        if ($targetEntity === null || $targetEntity->getCode() != EntityType::PLANET) {
            throw new InvalidFleetScanParameterException('Am gewählten Ziel existiert kein Planet!');
        }

        $sourceEntity = $planet->getEntity();
        $dist = $this->entityService->distance($sourceEntity, $targetEntity);
        $cryptoRangePerLevel = $this->config->getInt('crypto_range_per_level');
        if ($dist > $cryptoRangePerLevel * $cryptoCenterLevel) {
            throw new InvalidFleetScanParameterException("Das Ziel ist zu weit entfernt (" . StringUtils::formatNumber(ceil($dist)) . " AE, momentan sind " . StringUtils::formatNumber($cryptoRangePerLevel * $cryptoCenterLevel) . " möglich, " . $cryptoRangePerLevel . " pro Gebäudestufe)!");
        }

        $cooldownTime = time() + $this->calculateCooldown($cryptoCenterLevel);
        $this->allianceBuildingCooldownRepository->setUserCooldown($currentUser,  $this->allianceBuildingRepository->find(AllianceBuildingId::CRYPTO->value), $cooldownTime);

        $targetPlanet = $this->planetRepository->find($targetEntity->getId());
        $this->allianceHistoryRepository->addEntry($currentUser->getAlliance(), "Der Spieler [b]" . $currentUser->getNick() . "[/b] hat den Planeten " . $targetPlanet->getName() . "[/b] (" . $targetEntity->coordinatesString() . ") gescannt!");

        $targetOwner = $targetPlanet->getUser();
        $opJam = $this->defenseRepository->countJammingDevicesOnEntity($targetPlanet);
        $opStealth = $this->getStealthTechLevel($targetOwner);
        $opComputer = $this->getComputerTechLevel($targetOwner);

        $selfSpy = $this->getSpyTechLevel($currentUser);
        $selfComputer = $this->getComputerTechLevel($currentUser);

        $chance = $this->calculateChance($cryptoCenterLevel, $selfSpy, $opJam, $opStealth);
        if ($chance < 0) {
            if ($opJam > 0 && $targetOwner) {
                $this->messageRepository->createSystemMessage(
                    $targetOwner,
                    $this->messageCategoryRepository->find(MessageCategoryId::SHIP_SPY),
                    "Störsender erfolgreich",
                    "Eure Techniker haben festgestellt, dass von einem anderen Planeten eine Entschlüsselung eures Funkverkehrs versucht wurde. Daraufhin haben eure Störsender die Funknetze mit falschen Werten überlastet, so dass die gegnerische Analyse fehlschlug!"
                );
            }

            throw new FleetScanFailedException("Die Analyse schlug leider fehl! Eure Empfangsgeräte haben zu viel Rauschen aufgenommen; anscheinend hat der Zielplanet ein aktives Störfeld oder die dortige Flottenkontrolle ist zu gut getarnt (Chance: " . $chance . ")!");
        }

        $decryptLevel = $this->calculateDecryptLevel($cryptoCenterLevel, $selfSpy, $selfComputer, $opJam, $opStealth, $opComputer);
        $arrivingFleets = $this->getFleets($targetEntity, self::FLEET_DIRECTION_ARRIVING);
        $departingFleets = $this->getFleets($targetEntity, self::FLEET_DIRECTION_DEPARTING);
        $totalFleets = count($arrivingFleets) + count($departingFleets);

        $out = "[b]Flottenscan vom Planeten " . $targetPlanet->name . "[/b] (" . $targetEntity->coordinatesString() . ")\n\n";

        if ($decryptLevel >= $this->config->getInt("crypto_number_of_fleets_level")) {
            $out .= "Es sind " . $totalFleets . " Flotten unterwegs.\n\n";
        } else {
            if ($totalFleets > 0) {
                $out .= "Es sind Flotten unterwegs.\n\n";
            } else {
                $out .= "Es sind keine Flotten unterwegs.\n\n";
            }
        }

        if ($decryptLevel >= $this->config->getInt("crypto_fleets_incoming_level")) {
            $out .= $this->fleetReport($arrivingFleets, self::FLEET_DIRECTION_ARRIVING, $decryptLevel);
        }
        if ($decryptLevel >= $this->config->getInt("crypto_fleets_send_level")) {
            $out .= $this->fleetReport($departingFleets, self::FLEET_DIRECTION_DEPARTING, $decryptLevel);
        }

        $out .= "\nEntschlüsselchance: $decryptLevel";

        // Subtract resources
        $this->planetRepository->addResources($planet, 0, 0, 0, -$cryptoFuelCostsPerScan, 0);
        $this->allianceRepository->addResources($currentUser->getAlliance(), 0, 0, 0, -$cryptoFuelCostsPerScan, 0);

        if ($targetOwner !== null) {
            $this->messageRepository->createSystemMessage(
                $targetOwner,
                $this->messageCategoryRepository->find(MessageCategoryId::SHIP_SPY),
                "Funkstörung",
                "Eure Flottenkontrolle hat soeben eine kurzzeitige Störung des Kommunikationsnetzes festgestellt. Es kann sein, dass fremde Spione in das Netz eingedrungen sind und Flottendaten geklaut haben."
            );
        }

        $this->messageRepository->createSystemMessage(
            $currentUser,
            $this->messageCategoryRepository->find(MessageCategoryId::MISC),
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

        $value = $this->technologyRepository->getTechnologyLevel($user, TechnologyId::TARN);

        if ($user->getAlliance()) {
            $value += $this->allianceTechnologyListRepository->getLevel($user->getAlliance(), AllianceTechnologyId::TARN);
        }

        $specialist = $user->getSpecialist();
        if ($specialist !== null) {
            $value += $specialist->getTarnLevel();
        }

        return $value;
    }

    private function getComputerTechLevel(?User $user): int
    {
        if ($user === null) {
            return 0;
        }

        return $this->technologyRepository->getTechnologyLevel($user, TechnologyId::COMPUTER);
    }

    private function getSpyTechLevel(?User $user): int
    {
        if ($user === null) {
            return 0;
        }

        $value = $this->technologyRepository->getTechnologyLevel($user, TechnologyId::SPY);

        if ($user->getAlliance()) {
            $value += $this->allianceTechnologyListRepository->getLevel($user->getAlliance(), AllianceTechnologyId::SPY);
        }

        $specialist = $user->getSpecialist();
        if ($specialist) {
            $value += $specialist->getSpyLevel();
        }

        return $value;
    }

    private function calculateChance(int $cryptoCenterLevel, int $selfSpy, int $opJam, int $opStealth): float
    {
        $minRandomChance = $this->config->getInt("crypto_chance_rand_mod_min");
        $maxRandomChance = $this->config->getInt("crypto_chance_rand_mod_max");

        $infrastructureChance = $cryptoCenterLevel - $opJam;
        $researchChance = 0.3 * ($selfSpy - $opStealth);
        $randomChance = random_int($minRandomChance, $maxRandomChance);

        return $infrastructureChance + $researchChance + $randomChance;
    }

    private function calculateDecryptLevel(int $cryptoCenterLevel, int $selfSpy, int $selfComputer, int $opJam, int $opStealth, int $opComputer): float
    {
        $minRandomLevel = $this->config->getInt("crypto_level_rand_mod_min");
        $maxRandomLevel = $this->config->getInt("crypto_level_rand_mod_max");

        $infrastructureLevel = $cryptoCenterLevel - $opJam;
        $researchLevel = 0.75 * ($selfSpy + $selfComputer - $opStealth - $opComputer);
        $randomLevel = random_int($minRandomLevel, $maxRandomLevel);

        return $infrastructureLevel + $researchLevel + $randomLevel;
    }

    /**
     * @return array<Fleet>
     */
    private function getFleets(Entity $targetEntity, string $direction): array
    {
        $params = new FleetSearchParameters();
        if ($direction == self::FLEET_DIRECTION_ARRIVING) {
            $params->entityTo = $targetEntity->getId();
        } elseif ($direction == self::FLEET_DIRECTION_DEPARTING) {
            $params->entityFrom = $targetEntity->getId();
        }

        return $this->fleetRepository->findByParameters($params);
    }

    /**
     * @param Fleet[] $fleets
     * @param string $direction
     * @param float $decryptLevel
     * @return string
     */
    private function fleetReport(array $fleets, string $direction, float $decryptLevel): string
    {
        $out = "";
        if ($direction == self::FLEET_DIRECTION_ARRIVING) {
            $out .= "[b]Eintreffende Flotten[/b]\n\n";
        } elseif ($direction == self::FLEET_DIRECTION_DEPARTING) {
            $out .= "[b]Wegfliegende Flotten[/b]\n\n";
        }

        if (count($fleets) > 0) {
            $out .= "Es sind " . count($fleets) . " Flotten unterwegs:\n\n";
            foreach ($fleets as $fleet) {
                $out .= $this->individualFleetReport($fleet, $direction, $decryptLevel);
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
        $fleetSourceEntity = $fleet->getEntityFrom();
        $fleetOwner = $fleet->getLeader();

        $out = '[b]Besitzer:[/b] ' . ($fleetOwner ? $fleetOwner->getUser()->getNick() : 'Unbekannt') . "\n";

        if ($direction == self::FLEET_DIRECTION_ARRIVING) {
            $out .= '[b]Herkunft:[/b] ' . $fleetSourceEntity->toString();
        } elseif ($direction == self::FLEET_DIRECTION_DEPARTING) {
            $out .= '[b]Ziel:[/b] ' . $fleetSourceEntity->toString();
        }

        $out .= "\n[b]Ankunft:[/b] ";
        if ($decryptLevel >= $this->config->getInt("crypto_time_sec_level")) {
            $out .= date("d.m.Y H:i:s", $fleet->getLandTime()) . " Uhr";
        } elseif ($decryptLevel >= $this->config->getInt("crypto_time_min_level")) {
            $out .= date("d.m.Y H:i", $fleet->getLandTime()) . " Uhr";
        } elseif ($decryptLevel >= $this->config->getInt("crypto_time_15_level")) {
            $rand = random_int(0, 15 * 60); // 15 times 60 seconds
            $out .= "Zwischen " . date("d.m.Y H:i", $fleet->getLandTime() - $rand) . " und " . date("d.m.Y H:i", $fleet->getLandTime() + (15 * 60) - $rand) . " Uhr";
        } elseif ($decryptLevel >= $this->config->getInt("crypto_time_30_level")) {
            $rand = random_int(0, 30 * 60); // 30 times 60 seconds
            $out .= "Zwischen " . date("d.m.Y H:i", $fleet->getLandTime() - $rand) . " und " . date("d.m.Y H:i", $fleet->getLandTime() + (30 * 60) - $rand) . " Uhr";
        } elseif ($decryptLevel >= $this->config->getInt("crypto_time_60_level")) {
            $rand = random_int(0, 60 * 60); // 60 times 60 seconds
            $out .= "Zwischen " . date("d.m.Y H:i", $fleet->getLandTime() - $rand) . " und " . date("d.m.Y H:i", $fleet->getLandTime() + (60 * 60) - $rand) . " Uhr";
        }

        if ($decryptLevel >= $this->config->getInt("crypto_action_level")) {
            $action = FleetAction::createFactory($fleet->getAction());
            $out .= "\n[b]Aktion:[/b] " . substr((string) $action, 25, -7) . "\n";
        } else {
            $out .= "\n";
        }

        if ($decryptLevel >= $this->config->getInt("crypto_ships_type_level") ||
            $decryptLevel >= $this->config->getInt("crypto_ships_count_all_level") ||
            $decryptLevel >= $this->config->getInt("crypto_ships_count_single_level")
        ) {
            $shipEntries = $fleet->getFleetShips();
            $totalShips = 0;
            foreach ($shipEntries as $shipEntry) {
                if (($decryptLevel >= $this->config->getInt("crypto_ships_count_single_level")) &&
                    ($decryptLevel >= $this->config->getInt("crypto_ships_type_level"))) {
                    $out .= "" . $shipEntry->getCount() . " " . $shipEntry->getShip()->getName() . "\n";
                } elseif ($decryptLevel >= $this->config->getInt("crypto_ships_count_single_level")) {
                    $out .= "" . $shipEntry->getCount() . "\n";
                } elseif ($decryptLevel >= $this->config->getInt("crypto_ships_type_level")) {
                    $out .= "" . $shipEntry->getShip()->getName() . "\n";
                }

                $totalShips += $shipEntry->getCount();
            }
            if ($decryptLevel >= $this->config->getInt("crypto_ships_count_all_level")) {
                $out .= $totalShips . " Schiffe total\n";
            }
        }

        if ($decryptLevel >= $this->config->getInt("crypto_resources_level")) {
            $out .= "[b]Ressourcen:[/b]";
            $out .= " Titan: " . number_format($fleet->getResMetal());
            $out .= " Silizium: " . number_format($fleet->getResCrystal());
            $out .= " PVC: " . number_format($fleet->getResPlastic());
            $out .= " Tritium: " . number_format($fleet->getResFuel());
            $out .= " Nahrung: " . number_format($fleet->getResFood());
            $out .= "\n";
        }

        $out .= "\n";

        return $out;
    }
}
