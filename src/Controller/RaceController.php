<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\Core\TokenContext;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseSearch;
use EtoA\Race\RaceDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipSearch;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\Universe\Resources\ResIcons;
use EtoA\Universe\Resources\ResourceNames;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RaceController extends AbstractController
{

    public function __construct(
        private readonly RaceDataRepository    $raceRepository,
        private readonly ShipDataRepository    $shipRepository,
        private readonly DefenseDataRepository $defenseRepository,
    )
    {
    }


    #[Route("/api/races/info", name: "api.race.info", methods: "GET")]

    public function getInfo(TokenContext $context, Request $request): JsonResponse
    {
        $raceId = $request->query->getInt('id');

        if ($raceId <= 0) {
            return new JsonResponse();
        }


        $race = $this->raceRepository->getRace($raceId);
        if ($race === null) {
            return new JsonResponse();
        }

        ob_start();

        echo BBCodeUtils::toHTML($race->getComment()) . "<br/><br/>";
        tableStart('', 300);
        echo "<tr><th colspan=\"2\">St&auml;rken / Schw&auml;chen:</th></tr>";
        if ($race->getMetal() !== 1.00) {
            echo "<tr><th>" . ResIcons::METAL . "Produktion von " . ResourceNames::METAL . ":</td><td>" . StringUtils::formatPercentString($race->getMetal(), true) . "</td></tr>";
        }
        if ($race->getCrystal() !== 1.0) {
            echo "<tr><th>" . ResIcons::CRYSTAL . "Produktion von " . ResourceNames::CRYSTAL . ":</td><td>" . StringUtils::formatPercentString($race->getCrystal(), true) . "</td></tr>";
        }
        if ($race->getPlastic() !== 1.0) {
            echo "<tr><th>" . ResIcons::PLASTIC . "Produktion von " . ResourceNames::PLASTIC . ":</td><td>" . StringUtils::formatPercentString($race->getPlastic(), true) . "</td></tr>";
        }
        if ($race->getFuel() !== 1.0) {
            echo "<tr><th>" . ResIcons::FUEL . "Produktion von " . ResourceNames::FUEL . ":</td><td>" . StringUtils::formatPercentString($race->getFuel(), true) . "</td></tr>";
        }
        if ($race->getFood() !== 1.0) {
            echo "<tr><th>" . ResIcons::FOOD . "Produktion von " . ResourceNames::FOOD . ":</td><td>" . StringUtils::formatPercentString($race->getFood(), true) . "</td></tr>";
        }
        if ($race->getPower() !== 1.0) {
            echo "<tr><th>" . ResIcons::POWER . "Produktion von Energie:</td><td>" . StringUtils::formatPercentString($race->getPower(), true) . "</td></tr>";
        }
        if ($race->getPopulation() !== 1.0) {
            echo "<tr><th>" . ResIcons::PEOPLE . "Bevölkerungswachstum:</td><td>" . StringUtils::formatPercentString($race->getPopulation(), true) . "</td></tr>";
        }
        if ($race->getResearchTime() !== 1.0) {
            echo "<tr><th>" . ResIcons::TIME . "Forschungszeit:</td><td>" . StringUtils::formatPercentString($race->getResearchTime(), true, true) . "</td></tr>";
        }
        if ($race->getBuildTime() !== 1.0) {
            echo "<tr><th>" . ResIcons::TIME . "Bauzeit:</td><td>" . StringUtils::formatPercentString($race->getBuildTime(), true, true) . "</td></tr>";
        }
        if ($race->getFleetTime() !== 1.0) {
            echo "<tr><th>" . ResIcons::TIME . "Fluggeschwindigkeit:</td><td>" . StringUtils::formatPercentString($race->getFleetTime(), true) . "</td></tr>";
        }
        tableEnd();
        tableStart('', 500);

        echo "<tr><th colspan=\"3\">Spezielle Schiffe:</th></tr>";
        $ships = $this->shipRepository->searchShips(ShipSearch::create()->buildable()->raceId($race->getId())->special(false));
        if (count($ships) > 0) {
            foreach ($ships as $ship) {
                echo "<tr><td style=\"background:black;\"><img src=\"" . $ship->getImagePath() . "\" style=\"width:40px;height:40px;border:none;\" alt=\"ship" . $ship->getId() . "\" /></td>
            <th style=\"width:180px;\">" . BBCodeUtils::toHTML($ship->getName()) . "</th>
            <td>" . BBCodeUtils::toHTML($ship->getShortComment()) . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan=\"3\">Keine Rassenschiffe vorhanden</td></tr>";
        }

        tableEnd();
        tableStart('', 500);
        echo "<tr><th colspan=\"3\">Spezielle Verteidigung:</th></tr>";
        $defense = $this->defenseRepository->searchDefense(DefenseSearch::create()->raceId($race->getId())->buildable());
        if (count($defense) > 0) {
            foreach ($defense as $def) {
                echo "<tr><td style=\"background:black;\"><img src=\"" . $def->getImagePath() . "\" style=\"width:40px;height:40px;border:none;\" alt=\"def" . $def->getId() . "\" /></td>
            <th style=\"width:180px;\">" . BBCodeUtils::toHTML($def->getName()) . "</th>
            <td>" . BBCodeUtils::toHTML($def->getShortComment()) . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan=\"3\">Keine Rassenverteidigung vorhanden</td></tr>";
        }


        tableEnd();

        return new JsonResponse([
            'content' => ob_get_clean(),
        ]);
    }
}
