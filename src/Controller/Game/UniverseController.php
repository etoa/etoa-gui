<?php

namespace EtoA\Controller\Game;

use EtoA\Admin\AdminUserRepository;
use EtoA\Controller\Game;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\StringUtils;
use EtoA\UI\Tooltip;
use EtoA\Universe\Cell\Cell;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\CellRenderer;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Entity\EntitySort;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\GalaxyMap;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResIcons;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\Universe\SectorMapRenderer;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UniverseController extends Game\AbstractGameController
{
    public function __construct(
        private readonly ConfigurationService $config,
        private readonly EntityRepository $entityRepository,
        private readonly UserUniverseDiscoveryService $userUniverseDiscoveryService,
        private readonly EntityService $entityService,
        private readonly CellRepository $cellRepository,
        private readonly PlanetRepository $planetRepo,
        private readonly UserRepository $userRepository
    )
    {
    }

    #[Route('/game/galaxy', name: 'game.galaxy')]
    public function galaxy(): Response {
        $sx_num = $this->config->param1Int('num_of_sectors');
        $sy_num = $this->config->param2Int('num_of_sectors');

        ob_start();

        $sec_x_size = GalaxyMap::WIDTH / $sx_num;
        $sec_y_size = GalaxyMap::WIDTH / $sy_num;
        $xcnt = 1;
        for ($x = 0; $x < GalaxyMap::WIDTH; $x += $sec_x_size) {
            $ycnt = 1;
            for ($y = 0; $y < GalaxyMap::WIDTH; $y += $sec_y_size) {
                $tt = new Tooltip();
                $tt->addTitle("Sektor $xcnt/$ycnt");
                $tt->addText('Klicken um Karte anzuzeigen');
                echo "<area shape=\"rect\" coords=\"$x," . (GalaxyMap::WIDTH - $y) . "," . ($x + $sec_x_size) . "," . (GalaxyMap::WIDTH - $y - $sec_y_size) . "\" href=\"".$this->generateUrl('game.sector', array('sx' => $xcnt,'sy'=>$ycnt)) . "\" alt=\"Sektor $xcnt / $ycnt\" " . $tt->toString() . "><br/>";
                $ycnt++;
            }
            $xcnt++;
        }

        return $this->render('game/universe/galaxy.html.twig',[
            'map' => ob_get_clean(),
        ]);
    }

    #[Route('/game/sector', name: 'game.sector')]
    public function sector(Request $request): Response
    {
        $cp = $this->entityRepository->getEntity($request->getSession()->get('cpid'));

        // Coordinates by request
        if($request->query->has('sx') && $request->query->has('sy')) {
            $sx = $request->query->get('sx');
            $sy = $request->query->get('sy');
        }
        // Current Planet
        elseif ($cp) {
            $sx = $cp->sx;
            $sy = $cp->sy;
        } // Default coordinates (galactic center)
        else {
            $sx = $this->config->param1Int('map_init_sector');
            $sy = $this->config->param2Int('map_init_sector');
        }

        return $this->render('game/universe/sector.html.twig',[
            'xy' => $sx.','.$sy,
        ]);
    }

    // TODO: auto mapping
    #[Route('/game/cell/{id}', name: 'game.cell')]
    public function cell(
        int $id,
        CellRepository $cellRepository,
        CellRenderer $cellRenderer
    ): Response {
        $cell = $cellRepository->getCellById($id);
        if ($cell) {

            $entities = $this->entityRepository->searchEntities(EntitySearch::create()->cellId($id), EntitySort::pos());
            $sx_num = $this->config->param1Int('num_of_sectors');
            $sy_num = $this->config->param2Int('num_of_sectors');
            $cx_num = $this->config->param1Int('num_of_cells');
            $cy_num = $this->config->param2Int('num_of_cells');

            $abs = $cell->getAbsoluteCoordinates( $this->config->param1Int('num_of_cells'), $this->config->param2Int('num_of_cells'));

            if ($this->userUniverseDiscoveryService->discovered($this->getUser()->getData(), $abs[0], $abs[1])) {
                $renderedCells = $cellRenderer->render($entities);
            }
        } else {
            $msg['error'] = "System nicht gefunden!";
            echo "<input type=\"button\" value=\"Zur&uuml;ck zur Raumkarte\" onclick=\"document.location='?page=sector'\" />";
        }

        return $this->render('game/universe/cell.html.twig',[
            'msg' => $msg??null,
            'cell' =>$cell,
            'cellRepository' => $cellRepository,
            'renderedCells' => $renderedCells??null
        ]);
    }

    #[Route('/game/entity/{id}', name: 'game.entity')]
    public function entity($id): Response {
        $ent = $this->entityRepository->getEntity($id);
        if($ent && $fullEnt =$this->entityService->getEntity($ent)) {
            $cell = $this->cellRepository->getCellById($ent->cellId);
            $abs = $cell->getAbsoluteCoordinates($cell->sx,$cell->sy);
            if ($this->userUniverseDiscoveryService->discovered($this->getUser()->getData(), $abs[0], $abs([1]))) {
                    if ($ent->code == EntityType::PLANET) {
                        $rowSpan = 7;
                        if (filled($fullEnt->name)) {
                            $rowSpan++;
                        }
                        if (filled($fullEnt->description)) {
                            $rowSpan++;
                        }
                        if ($fullEnt->hasDebrisField()) {
                            $rowSpan++;
                        }

                        tableStart("Planetendaten");
                        echo "<tr>
                        <td style=\"background:#000;;vertical-align:middle\" rowspan=\"" . $rowSpan . "\">
                            <img src=\"" . $fullEnt->getImagePath('b') . "\" alt=\"planet\" width=\"310\" height=\"310\"/>
                        </td>";
                        echo "<th>Besitzer:</th>
                    <td>";
                        if ($fullEnt-getUserId() > 0)
                            echo "<a href=\"?page=userinfo&amp;id=" . $fullEnt-getUserId() . "\">" . $this->userRepository->getUser($fullEnt-getUserId())->getNick() . "</a>";
                        else
                            echo 'Niemand';
                        echo "</td>
                    </tr>";

                        if (filled($fullEnt->name)) {
                            echo "<tr>
                            <th>Name:</th>
                            <td>" . $fullEnt->name . "</td></tr>";
                        }

                        echo "<tr>
                        <th>Sonnentyp:</th>
                        <td>" . '$ent->starTypeName' . "</td></tr>";
                        echo "<tr>
                        <th>Planettyp:</th>
                        <td>" . $ent->typeName . "</td></tr>";
                        echo "<tr>
                        <th>Felder:</th>
                        <td>" . $fullEnt->fields . " total</td></tr>";
                        echo "<tr>
                        <th>Grösse:</th>
                        <td>" . StringUtils::formatNumber($this->config->getInt('field_squarekm') * $fullEnt->fields) . " km&sup2;</td></tr>";
                        echo "<tr>
                        <th>Temperatur:</th>
                        <td>" . $fullEnt->tempFrom . "&deg;C bis " . $fullEnt->tempTo . "&deg;C <br/><br/>";
                        echo "<img src=\"images/heat_small.png\" alt=\"Heat\" style=\"width:16px;float:left;\" />
                        Wärmebonus: " . helpLink("tempbonus") . "<br/> ";
                        $solarProdBonus = $fullEnt->solarPowerBonus();
                        $color = $solarProdBonus >= 0 ? '#0f0' : '#f00';
                        echo "<span style=\"color:" . $color . "\">" . ($solarProdBonus > 0 ? '+' : '') . $solarProdBonus . "</span>";
                        echo " Energie pro Solarsatellit <br style=\"clear:both;\"/><br/>
                        <img src=\"images/ice_small.png\" alt=\"Cold\" style=\"width:16px;float:left;\" />
                        Kältebonus: " . helpLink("tempbonus") . "<br/> ";
                        $fuelProdBonus = $fullEnt->fuelProductionBonus();
                        $color = $fuelProdBonus >= 0 ? '#0f0' : '#f00';
                        echo "<span style=\"color:" . $color . "\">" . ($fuelProdBonus > 0 ? '+' : '') . $fuelProdBonus . "%</span>";
                        echo " " . ResourceNames::FUEL . "-Produktion </td></tr>";

                        if ($fullEnt->description) {
                            echo "<tr>
                            <th width=\"100\">Beschreibung:</th>
                            <td>" . $fullEnt->description . "</td></tr>";
                        }

                        if ($fullEnt->hasDebrisField()) {
                            echo '<tr>
                        <th class="tbltitle">Trümmerfeld:</th><td>
                        ' . ResIcons::METAL . "" . StringUtils::formatNumber($fullEnt->wfMetal) . '<br style="clear:both;" />
                        ' . ResIcons::CRYSTAL . "" . StringUtils::formatNumber($fullEnt->wfCrystal) . '<br style="clear:both;" />
                        ' . ResIcons::PLASTIC . "" . StringUtils::formatNumber($fullEnt->wfPlastic) . '<br style="clear:both;" />
                        </td></tr>';
                        }

                        tableEnd();
                    } elseif ($ent->code == 's') {
                        tableStart("Sterndaten");
                        echo "<tr>
                        <td width=\"220\" style=\"background:#000;vertical-align:middle\" rowspan=\"2\">
                            <img src=\"" . $fullEnt->getImagePath("b") . "\" alt=\"star\" width=\"220\" height=\"220\"/>
                        </td>";
                        echo "<th style=\"height:20px;\">Typ:</th>
                    <td>" . $fullEnt->type() . " " . helpLink("stars") . "</td>
                    </tr>";

                        $data = $ent->typeData();

                        echo "<tr><th>Beschreibung:</th><td>" . $data['comment'] . "</td></tr>";


                        tableEnd();
                    } else {
                        iBoxStart("Objektdaten");
                        echo "Über dieses Objekt sind keine weiteren Daten verfügbar!";
                        iBoxEnd();
                    }

                    // Previous and next entity
                    $idprev = $id - 1;
                    $idnext = $id + 1;
                    $pmarr = $entityRepository->getMaxEntityId();
                    if ($idprev > 0) {
                        $str_prev =    "<td><input type=\"button\" value=\"&lt;\" onclick=\"document.location='?page=$page&amp;id=" . $idprev . "'\" /></td>";
                    }
                    if ($idnext <= $pmarr) {
                        $str_next = "<td><input type=\"button\" value=\"&gt;\" onclick=\"document.location='?page=$page&amp;id=" . $idnext . "'\" /></td>";
                    }
            } else {
                echo "<h1>Raumobjekt-Datenbank</h1>";
                error_msg("Das Objekt mit der Kennung [b]" . $id . "[/b] wurde noch nicht entdeckt!");
            }
        } else {
            echo "<h1>Raumobjekt-Datenbank</h1>";
            error_msg("Das Objekt mit der Kennung [b]" . $id . "[/b] existiert nicht!");
        }


        return $this->render('game/universe/cell.html.twig',[
            'msg' => $msg??null,
        ]);
    }
}