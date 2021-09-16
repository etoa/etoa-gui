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
    private RaceDataRepository $raceRepository;
    private ShipDataRepository $shipRepository;
    private DefenseDataRepository $defenseRepository;

    public function __construct(RaceDataRepository $raceRepository, ShipDataRepository $shipRepository, DefenseDataRepository $defenseRepository)
    {
        $this->raceRepository = $raceRepository;
        $this->shipRepository = $shipRepository;
        $this->defenseRepository = $defenseRepository;
    }

    /**
     * @Route("/api/races/info", methods={"GET"}, name="api.race.info")
     */
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

        echo BBCodeUtils::toHTML($race->comment) . "<br/><br/>";
        tableStart('', 300);
        echo "<tr><th colspan=\"2\">St&auml;rken / Schw&auml;chen:</th></tr>";
        if ($race->metal !== 1.00) {
            echo "<tr><th>" . ResIcons::METAL . "Produktion von " . ResourceNames::METAL . ":</td><td>" . StringUtils::formatPercentString($race->metal, true) . "</td></tr>";
        }
        if ($race->crystal !== 1.0) {
            echo "<tr><th>" . ResIcons::CRYSTAL . "Produktion von " . ResourceNames::CRYSTAL . ":</td><td>" . StringUtils::formatPercentString($race->crystal, true) . "</td></tr>";
        }
        if ($race->plastic !== 1.0) {
            echo "<tr><th>" . ResIcons::PLASTIC . "Produktion von " . ResourceNames::PLASTIC . ":</td><td>" . StringUtils::formatPercentString($race->plastic, true) . "</td></tr>";
        }
        if ($race->fuel !== 1.0) {
            echo "<tr><th>" . ResIcons::FUEL . "Produktion von " . ResourceNames::FUEL . ":</td><td>" . StringUtils::formatPercentString($race->fuel, true) . "</td></tr>";
        }
        if ($race->food !== 1.0) {
            echo "<tr><th>" . ResIcons::FOOD . "Produktion von " . ResourceNames::FOOD . ":</td><td>" . StringUtils::formatPercentString($race->food, true) . "</td></tr>";
        }
        if ($race->power !== 1.0) {
            echo "<tr><th>" . ResIcons::POWER . "Produktion von Energie:</td><td>" . StringUtils::formatPercentString($race->power, true) . "</td></tr>";
        }
        if ($race->population !== 1.0) {
            echo "<tr><th>" . ResIcons::PEOPLE . "Bevölkerungswachstum:</td><td>" . StringUtils::formatPercentString($race->population, true) . "</td></tr>";
        }
        if ($race->researchTime !== 1.0) {
            echo "<tr><th>" . ResIcons::TIME . "Forschungszeit:</td><td>" . StringUtils::formatPercentString($race->researchTime, true, true) . "</td></tr>";
        }
        if ($race->buildTime !== 1.0) {
            echo "<tr><th>" . ResIcons::TIME . "Bauzeit:</td><td>" . StringUtils::formatPercentString($race->buildTime, true, true) . "</td></tr>";
        }
        if ($race->fleetTime !== 1.0) {
            echo "<tr><th>" . ResIcons::TIME . "Fluggeschwindigkeit:</td><td>" . StringUtils::formatPercentString($race->fleetTime, true) . "</td></tr>";
        }
        tableEnd();
        tableStart('', 500);

        echo  "<tr><th colspan=\"3\">Spezielle Schiffe:</th></tr>";
        $ships = $this->shipRepository->searchShips(ShipSearch::create()->buildable()->raceId($race->id)->special(false));
        if (count($ships) > 0) {
            foreach ($ships as $ship) {
                echo "<tr><td style=\"background:black;\"><img src=\"" . $ship->getImagePath() . "\" style=\"width:40px;height:40px;border:none;\" alt=\"ship" . $ship->id . "\" /></td>
            <th style=\"width:180px;\">" . BBCodeUtils::toHTML($ship->name) . "</th>
            <td>" . BBCodeUtils::toHTML($ship->shortComment) . "</td></tr>";
            }
        } else
            echo "<tr><td colspan=\"3\">Keine Rassenschiffe vorhanden</td></tr>";

        tableEnd();
        tableStart('', 500);
        echo  "<tr><th colspan=\"3\">Spezielle Verteidigung:</th></tr>";
        $defense = $this->defenseRepository->searchDefense(DefenseSearch::create()->raceId($race->id)->buildable());
        if (count($defense) > 0) {
            foreach ($defense as $def) {
                echo "<tr><td style=\"background:black;\"><img src=\"" . $def->getImagePath() . "\" style=\"width:40px;height:40px;border:none;\" alt=\"def" . $def->id . "\" /></td>
            <th style=\"width:180px;\">" . BBCodeUtils::toHTML($def->name) . "</th>
            <td>" . BBCodeUtils::toHTML($def->shortComment) . "</td></tr>";
            }
        } else
            echo "<tr><td colspan=\"3\">Keine Rassenverteidigung vorhanden</td></tr>";


        tableEnd();

        return new JsonResponse([
            'content' => ob_get_clean(),
        ]);
    }
}
