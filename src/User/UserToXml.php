<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Alliance\AllianceRepository;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearchParameters;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetTypeRepository;
use Exception;

class UserToXml
{
    private UserRepository $userRepository;
    private AllianceRepository $allianceRepository;
    private RaceDataRepository $raceDataRepository;
    private PlanetRepository $planetRepository;
    private PlanetTypeRepository $planetTypeRepository;
    private BuildingListItemRepository $buildingRepository;
    private TechnologyRepository $technologyRepository;
    private TechnologyDataRepository $technologyDataRepository;
    private ShipRepository $shipRepository;
    private ShipDataRepository $shipDataRepository;
    private FleetRepository $fleetRepository;
    private DefenseRepository $defenseRepository;
    private DefenseDataRepository $defenseDataRepository;
    private string $cacheDir;

    public function __construct(
        UserRepository             $userRepository,
        AllianceRepository         $allianceRepository,
        RaceDataRepository         $raceDataRepository,
        PlanetRepository           $planetRepository,
        PlanetTypeRepository       $planetTypeRepository,
        BuildingListItemRepository $buildingRepository,
        TechnologyRepository       $technologyRepository,
        TechnologyDataRepository   $technologyDataRepository,
        ShipRepository             $shipRepository,
        ShipDataRepository         $shipDataRepository,
        FleetRepository            $fleetRepository,
        DefenseRepository          $defenseRepository,
        DefenseDataRepository      $defenseDataRepository,
        string                     $cacheDir
    ) {
        $this->userRepository = $userRepository;
        $this->allianceRepository = $allianceRepository;
        $this->raceDataRepository = $raceDataRepository;
        $this->planetRepository = $planetRepository;
        $this->planetTypeRepository = $planetTypeRepository;
        $this->buildingRepository = $buildingRepository;
        $this->technologyRepository = $technologyRepository;
        $this->technologyDataRepository = $technologyDataRepository;
        $this->shipRepository = $shipRepository;
        $this->shipDataRepository = $shipDataRepository;
        $this->fleetRepository = $fleetRepository;
        $this->defenseRepository = $defenseRepository;
        $this->defenseDataRepository = $defenseDataRepository;
        $this->cacheDir = $cacheDir;
    }

    public function getDataDirectory(): string
    {
        $dir = $this->cacheDir . "/user_xml";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }

    public function toCacheFile(int $userId): string
    {
        $filename = $userId . "_" . date("Y-m-d_H-i") . ".xml";
        $file = $this->getDataDirectory() . "/" . $filename;
        $xml = $this->generate($userId);
        if (!filled($xml)) {
            throw new Exception("XML Export fehlgeschlagen. User " . $userId . " nicht gefunden!");
        }
        if (file_put_contents($file, $xml)) {
            return $filename;
        }

        throw new Exception("Konnte Datei $file nicht zum XML Export öffnen!");
    }

    public function generate(int $userId): string
    {
        $user = $this->userRepository->getUser($userId);
        if ($user === null) {
            return '';
        }

        $alliance = $user->getAllianceId() !== 0 ? $this->allianceRepository->getAlliance($user->getAllianceId()) : null;
        $race = $user->getRaceId() !== 0 ? $this->raceDataRepository->getRace($user->getRaceId()) : null;

        $xml = "<userbackup>
    <export date=\"" . date("d.m.Y, H:i") . "\" timestamp=\"" . time() . "\" />
    <account>
        <id>" . $user->getId() . "</id>
        <nick>" . $user->getNick() . "</nick>
        <name>" . $user->getName() . "</name>
        <email>" . $user->getEmail() . "</email>
        <points>" . $user->getPoints() . "</points>
        <rank>" . $user->getRank() . "</rank>
        <online>" . date("d.m.Y, H:i", $user->getLogoutTime()) . "</online>
        <ip>" . $user->getIp() . "</ip>
        <host>" . $user->getHostname() . "</host>
        <alliance id=\"" . $user->getAllianceId() . "\" tag=\"" . ($alliance !== null ? $alliance->tag : '') . "\">" . ($alliance !== null ? $alliance->name : '') . "</alliance>
        <race id=\"" . $user->getRaceId() . "\">" . ($race !== null ? $race->getName() : '') . "</race>
    </account>";

        $xml .= "<planets>";
        $planets = $this->planetRepository->getUserPlanets($userId);
        $types = $this->planetTypeRepository->getPlanetTypeNames(true);
        $mainPlanet = 0;
        foreach ($planets as $planet) {
            if ($planet->isMainPlanet()) {
                $mainPlanet = $planet->getId();
            }
            $xml .= "
        <planet id=\"" . $planet->getId() . "\" name=\"" . $planet->getName() . "\" main=\"" . (int) $planet->isMainPlanet() . "\">
            <type id=\"" . $planet->getTypeId() . "\">" . $types[$planet->getTypeId()] . "</type>
            <metal>" . $planet->getResMetal() . "</metal>
            <crystal>" . $planet->getResCrystal() . "</crystal>
            <plastic>" . $planet->getResPlastic() . "</plastic>
            <fuel>" . $planet->getResFuel() . "</fuel>
            <food>" . $planet->getResFood() . "</food>
            <people>" . $planet->getPeople() . "</people>
        </planet>";
        }
        $xml .= "</planets>";

        $xml .= $this->getBuildings($userId);
        $xml .= $this->getTechnologies($userId);
        $xml .= $this->getShips($userId, $mainPlanet);
        $xml .= $this->getDefenses($userId);
        $xml .= "</userbackup>";

        return $xml;
    }

    private function getBuildings(int $userId): string
    {
        $xml = "<buildings>";
        $buildings = $this->buildingRepository->buildingNames();
        $buildListItems = $this->buildingRepository->findForUser($userId);
        foreach ($buildListItems as $item) {
            $xml .= "<building planet=\"" . $item->entityId . "\" id=\"" . $item->buildingId . "\" level=\"" . $item->currentLevel . "\">" . $buildings[$item->buildingId] . "</building>";
        }
        $xml .= "</buildings>";

        return $xml;
    }

    private function getTechnologies(int $userId): string
    {
        $xml = "<technologies>";
        $technologies = $this->technologyDataRepository->getTechnologyNames(true);
        $techListItems = $this->technologyRepository->findForUser($userId);
        foreach ($techListItems as $item) {
            $xml .= "<technology id=\"" . $item->getTechnologyId() . "\" level=\"" . $item->getCurrentLevel() . "\">" . $technologies[$item->getTechnologyId()] . "</technology>";
        }
        $xml .= "</technologies>";

        return $xml;
    }

    private function getShips(int $userId, int $mainPlanet): string
    {
        $xml = "<ships>";
        $ships = $this->shipDataRepository->getShipNames(true);
        $shipListItems = $this->shipRepository->findForUser($userId);
        foreach ($shipListItems as $item) {
            $xml .= "<ship planet=\"" . $item->entityId . "\" id=\"" . $item->shipId . "\" count=\"" . $item->count . "\">" . $ships[$item->shipId] . "</ship>";
        }
        $fleets = $this->fleetRepository->findByParameters((new FleetSearchParameters())->userId($userId));
        foreach ($fleets as $fleet) {
            $shipsInFleet = $this->fleetRepository->findAllShipsInFleet($fleet->getId());
            foreach ($shipsInFleet as $entry) {
                $xml .= "<ship planet=\"" . $mainPlanet . "\" id=\"" . $entry->shipId . "\" count=\"" . $entry->count . "\">" . $ships[$entry->shipId] . "</ship>";
            }
        }
        $xml .= "</ships>";

        return $xml;
    }

    private function getDefenses(int $userId): string
    {
        $xml = "<defenses>";
        $defenses = $this->defenseDataRepository->getDefenseNames(true);
        $defenseItems = $this->defenseRepository->findForUser($userId);
        foreach ($defenseItems as $item) {
            $xml .= "<defense planet=\"" . $item->entityId . "\" id=\"" . $item->defenseId . "\" count=\"" . $item->count . "\">" . $defenses[$item->defenseId] . "</defense>";
        }
        $xml .= "</defenses>";

        return $xml;
    }
}
