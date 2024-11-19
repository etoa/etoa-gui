<?php

namespace EtoA\Controller\Game;

use EtoA\Controller\Game;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Cell;
use EtoA\Entity\Entity;
use EtoA\UI\Tooltip;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\CellRenderer;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\GalaxyMap;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UniverseController extends Game\AbstractGameController
{
    public function __construct(
        private readonly ConfigurationService $config,
        private readonly EntityRepository $entityRepository,
        private readonly UserUniverseDiscoveryService $userUniverseDiscoveryService,
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
        $cp = $this->entityRepository->find($request->getSession()->get('cpid'));

        // Coordinates by request
        if($request->query->has('sx') && $request->query->has('sy')) {
            $sx = $request->query->get('sx');
            $sy = $request->query->get('sy');
        }
        // Current Planet
        elseif ($cp) {
            $sx = $cp->getCell()->getSx();
            $sy = $cp->getCell()->getSy();
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
    public function cell(
        CellRepository $cellRepository,
        CellRenderer $cellRenderer,
        ?Cell $cell = null
    ): Response {
        if ($cell) {

            $entities = $this->entityRepository->findBy(['cellId'=>$cell->getId()],['pos'=>'ASC']);
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
    public function entity(Request $request, ?Entity $ent=null): Response {
        $idprev = null;
        $idnext = null;

        $form = $this->createFormBuilder()
            ->add('id', TextType::class, [
                'required' => false,
                'data' => $request->attributes->get('id'),
                'attr' => [
                    'size'=>5,
                    'maxlength' => 7
                ]
            ])
            ->add('search', SubmitType::class, ['label' => 'Objekt anzeigen'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->redirectToRoute('game.entity',['id'=>$form->get('id')->getData()]);
        }

        if($ent) {
            // Previous and next entity
            $idmax = $this->entityRepository->findOneBy([],['id'=>'DESC'])->getId();
            $idprev = ($request->attributes->get('id') - 1) > 0 ? ($request->attributes->get('id') - 1):null;
            $idnext = ($request->attributes->get('id') + 1) <= $idmax ? ($request->attributes->get('id') + 1):null;

            $cell = $ent->getCell();
            $abs = $cell->getAbsoluteCoordinates($cell->getSx(),$cell->getSy());
            if ($this->userUniverseDiscoveryService->discovered($this->getUser()->getData(), $abs[0], $abs[1])) {
                if ($ent->getCode() === EntityType::PLANET) {
                    $rowSpan = 7;
                    if (filled($ent->getType()->getName())) {
                        $rowSpan++;
                    }
                    if (filled($ent->getType()->getDescription())) {
                        $rowSpan++;
                    }
                    if ($ent->getType()->hasDebrisField()) {
                        $rowSpan++;
                    }

                    return $this->render('game/universe/entity/entity_planet.html.twig',[
                        'rowSpan' => $rowSpan,
                        'planet' => $ent->getType(),
                        'star' => $this->entityRepository->findOneBy(['code'=>'s','cellId'=>$ent->getCell()->getId()])->getType(),
                        'search'=>[
                            'form' =>$form,
                            'idnext' => $idnext,
                            'idprev' =>$idprev
                        ]
                    ]);
                } elseif ($ent->getCode() == 's') {
                    return $this->render('game/universe/entity/entity_star.html.twig',[
                        'star' => $ent->getType(),
                        'search'=>[
                            'form' =>$form,
                            'idnext' => $idnext,
                            'idprev' =>$idprev
                        ]
                    ]);
                } else {
                    return $this->render('game/universe/entity/entity_message.html.twig',[
                        'headline' => $ent->coordinatesString(). ' ('.$ent->codeString().')',
                        'title' => 'Objektdaten',
                        'message' => "Über dieses Objekt sind keine weiteren Daten verfügbar!",
                        'search'=>[
                            'form' =>$form,
                            'idnext' => $idnext,
                            'idprev' =>$idprev
                        ]
                    ]);
                }
            } else {
                return $this->render('game/universe/entity/entity_message.html.twig',[
                    'headline' => 'Raumobjekt-Datenbank',
                    'title' => 'Fehler',
                    'message' => "Das Objekt mit der Kennung [b]" . $ent->getId() . "[/b] wurde noch nicht entdeckt!",
                    'search'=>[
                        'form' =>$form,
                        'idnext' => $idnext,
                        'idprev' =>$idprev
                    ]
                ]);
            }
        }

        return $this->render('game/universe/entity/entity_message.html.twig',[
            'headline' => 'Raumobjekt-Datenbank',
            'title' => 'Fehler',
            'message' => "Das Objekt mit der Kennung [b]" . $request->attributes->get('id') . "[/b] existiert nicht!",
            'search'=>[
                'form' =>$form,
                'idnext' => $idnext,
                'idprev' =>$idprev
            ]
        ]);
    }
}