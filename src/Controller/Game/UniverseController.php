<?php

namespace EtoA\Controller\Game;

use EtoA\Controller\Game;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\UI\Tooltip;
use EtoA\Universe\Cell\Cell;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\GalaxyMap;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\SectorMapRenderer;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UniverseController extends Game\AbstractGameController
{
    public function __construct(
        private readonly ConfigurationService $config,
    )
    {
    }

    #[Route('/game/galaxy', name: 'game.galaxy')]
    public function galaxy() {
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
    public function sector(
        EntityRepository $entityRepository,
        Request $request,
    ): Response
    {
        $cp = $entityRepository->getEntity($request->getSession()->get('cpid'));

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

    #[Route('/game/cell/{id}', name: 'game.cell')]
    public function cell(int $id) {
        dd($id);
    }
}