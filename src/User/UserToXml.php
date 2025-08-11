<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Building\BuildingListItemRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Entity\User;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearchParameters;
use EtoA\Fleet\FleetShipRepository;
use EtoA\Ship\ShipListRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetTypeRepository;
use Exception;

class UserToXml
{
    public function __construct(
        private readonly UserRepository               $userRepository,
        private readonly PlanetRepository             $planetRepository,
        private readonly PlanetTypeRepository         $planetTypeRepository,
        private readonly BuildingListItemRepository   $buildingRepository,
        private readonly FleetRepository              $fleetRepository,
        private readonly DefenseRepository            $defenseRepository,
        private string                                $cacheDir,
        private readonly ShipListRepository           $shipListRepository,
        private readonly FleetShipRepository          $fleetShipRepository,
    ) {}

    public function getDataDirectory(): string
    {
        $dir = $this->cacheDir . "/user_xml";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }

    public function toCacheFile(User $user): string
    {
        $filename = $user->getId() . "_" . date("Y-m-d_H-i") . ".xml";
        $file = $this->getDataDirectory() . "/" . $filename;
        $xml = $this->generate($user->getId());
        if (!filled($xml)) {
            throw new Exception("XML Export fehlgeschlagen. User " . $user->getId() . " nicht gefunden!");
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

        $alliance = $user->getAlliance();
        $race = $user->getRace();

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
        <alliance id=\"" . $user->getAlliance()?->getId() . "\" tag=\"" . ($alliance !== null ? $alliance->getTag() : '') . "\">" . ($alliance !== null ? $alliance->getName() : '') . "</alliance>
        <race id=\"" . $user->getRace()?->getId() . "\">" . ($race !== null ? $race->getName() : '') . "</race>
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
            <type id=\"" . $planet->getPlanetType()->getId() . "\">" . $types[$planet->getPlanetType()->getId()] . "</type>
            <metal>" . $planet->getResMetal() . "</metal>
            <crystal>" . $planet->getResCrystal() . "</crystal>
            <plastic>" . $planet->getResPlastic() . "</plastic>
            <fuel>" . $planet->getResFuel() . "</fuel>
            <food>" . $planet->getResFood() . "</food>
            <people>" . $planet->getPeople() . "</people>
        </planet>";
        }
        $xml .= "</planets>";

        $xml .= $this->getBuildings($user);
        $xml .= $this->getTechnologies($user);
        $xml .= $this->getShips($user, $mainPlanet);
        $xml .= $this->getDefenses($user);
        $xml .= "</userbackup>";

        return $xml;
    }

    private function getBuildings(User $user): string
    {
        $xml = "<buildings>";
        $buildListItems = $this->buildingRepository->findForUser($user);
        foreach ($buildListItems as $item) {
            $xml .= "<building planet=\"" . $item->getEntity()->getId() . "\" id=\"" . $item->getBuilding()->getId(). "\" level=\"" . $item->getCurrentLevel() . "\">" . $item->getBuilding()->getName() . "</building>";
        }
        $xml .= "</buildings>";

        return $xml;
    }

    private function getTechnologies(User $user): string
    {
        $xml = "<technologies>";
        $techListItems = $user->getTechlist();
        foreach ($techListItems as $item) {
            $xml .= "<technology id=\"" . $item->getTechnology()->getId() . "\" level=\"" . $item->getCurrentLevel() . "\">" . $item->getTechnology()->getName() . "</technology>";
        }
        $xml .= "</technologies>";

        return $xml;
    }

    private function getShips(User $user, int $mainPlanet): string
    {
        $xml = "<ships>";
        $shipListItems = $this->shipListRepository->findForUser($user);
        foreach ($shipListItems as $item) {
            $xml .= "<ship planet=\"" . $item->getEntity()->getId() . "\" id=\"" . $item->getShip()->getId() . "\" count=\"" . $item->getCount() . "\">" . $item->getShip()->getName() . "</ship>";
        }
        $fleets = $this->fleetRepository->findByParameters((new FleetSearchParameters())->user($user));
        foreach ($fleets as $fleet) {
            $shipsInFleet = $this->fleetShipRepository->findAllShipsInFleet($fleet);
            foreach ($shipsInFleet as $entry) {
                $xml .= "<ship planet=\"" . $mainPlanet . "\" id=\"" . $entry->getShip()->getId() . "\" count=\"" . $entry->getCount() . "\">" . $entry->getShip()->getName() . "</ship>";
            }
        }
        $xml .= "</ships>";

        return $xml;
    }

    private function getDefenses(User $user): string
    {
        $xml = "<defenses>";
        $defenseItems = $this->defenseRepository->findForUser($user);
        foreach ($defenseItems as $item) {
            $xml .= "<defense planet=\"" . $item->getEntity()->getId() . "\" id=\"" . $item->getDefense()->getId() . "\" count=\"" . $item->getCount() . "\">" . $item->getDefense()->getName() . "</defense>";
        }
        $xml .= "</defenses>";

        return $xml;
    }
}
