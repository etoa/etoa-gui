<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Missile\MissileDataRepository;
use EtoA\Missile\MissileListSearch;
use EtoA\Missile\MissileRepository;
use EtoA\Missile\MissileService;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MissileController extends AbstractGameController
{


    public function __construct(
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly PlanetRepository $planetRepository,
        private readonly ConfigurationService $configurationService,
        private readonly MissileDataRepository $missileDataRepository,
        private readonly MissileRepository $missileRepository,
        private readonly MissileService $missileService
    )
    {
    }

    #[Route('/game/missiles', name: 'game.missiles')]
    public function list(Request $request): Response
    {
        $cu = $this->getUser()->getData();
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));

        // Gebäude Level und Arbeiter laden
        $missileBuilding = $this->buildingListItemRepository->getEntityBuilding($cu->getId(), $planet, BuildingId::MISSILE->value);

        // Prüfen ob Gebäude gebaut ist
        if ($missileBuilding && $missileBuilding->getCurrentLevel() > 0) {
            // New exponential missile number algorithm by river
            // $max_space = per_level * algo_base ^ (silo_level - 1)
            $max_space = ceil($this->configurationService->getInt('missile_silo_missiles_per_level') * pow($this->configurationService->getFloat('missile_silo_missiles_algo_base'), $missileBuilding->getCurrentLevel() - 1));
            $max_flights = $missileBuilding->getCurrentLevel() * $this->configurationService->getInt('missile_silo_flights_per_level');

            if ($planet->getProdPower() - $planet->getUsePower() >= 0 && $planet->getProdPower() > 0 && $missileBuilding->getProdPercent() === "1.00") {
                if (!$missileBuilding->isDeactivated()) {

                    if (isset($_GET['selfdestruct']) && $_GET['selfdestruct'] > 0) {
                        if ($missileFlightRepository->deleteFlight((int) $_GET['selfdestruct'], $planet->id)) {
                            success_msg("Die Raketen haben sich selbst zerstört!");
                        }
                    }

                    // Load missiles
                    $missiles = $this->missileDataRepository->getMissiles();

                    // Launch missiles
                    if (isset($_POST['launch']) && checker_verify() && $cnt > 0) {
                        // Load missiles
                        $launch = array();
                        $lcnt = 0;
                        foreach ($_POST['count'] as $k => $v) {
                            $v = intval(StringUtils::parseFormattedNumber($v));
                            $k = intval($k);

                            if ($v > 0) {
                                if (isset($missilelist[$k])) {
                                    $t = min($missilelist[$k], $v);
                                    if ($t > 0) {
                                        $launch[$k] = $t;
                                    }
                                }
                            }
                        }

                        if (count($launch) > 0) {
                            // Save flight
                            $missileFlightRepository->startFlight($planet->id, (int) $_POST['targetplanet'], (int) $_POST['timeforflight'], $launch); // TODO: timeforflight comes from client? srsly?

                            foreach ($launch as $missileId => $count) {
                                // Update list
                                $missileRepository->addMissile($missileId, -$count, $cu->getId(), $planet->id);
                                $missilelist[$missileId] -= $count;
                                $lcnt += $count;
                            }

                            $cnt -= $lcnt;
                            success_msg("Raketen gestartet!");
                            $app['dispatcher']->dispatch(new \EtoA\Missile\Event\MissileLaunch($launch), \EtoA\Missile\Event\MissileLaunch::LAUNCH_SUCCESS);
                        } else {
                            error_msg("Raketen konnten nicht gestartet werden, keine Raketen gewählt!");
                        }
                    }

                    $missilelist = $planet->getMissilelist();
                    $cnt = 0;
                    foreach ($missilelist as $item) {
                        $cnt += $item->getCount();
                    }

                    $form = $this->createFormBuilder();

                    // Raketen anzeigen
                    if (count($missiles) > 0) {
                        $availableMissiles = [];

                        foreach ($missiles as $missile) {
                            if($this->missileService->checkRequirements($missile,$cu,$planet )) {
                                //Errechnet wie viele Raketen von diesem Typ maximal gekauft werden können mit den aktuellen Rohstoffen

                                // Silokapazität
                                $store = $max_space - $cnt;

                                //Titan
                                if ($missile->getCostsMetal() > 0) {
                                    $build_cnt_metal = floor($planet->getResMetal() / $missile->getCostsMetal());
                                } else {
                                    $build_cnt_metal = 99999999999;
                                }

                                //Silizium
                                if ($missile->getCostsCrystal() > 0) {
                                    $build_cnt_crystal = floor($planet->getResCrystal() / $missile->getCostsCrystal());
                                } else {
                                    $build_cnt_crystal = 99999999999;
                                }

                                //PVC
                                if ($missile->getCostsPlastic() > 0) {
                                    $build_cnt_plastic = floor($planet->getResPlastic() / $missile->getCostsPlastic());
                                } else {
                                    $build_cnt_plastic = 99999999999;
                                }

                                //Tritium
                                if ($missile->getCostsFuel() > 0) {
                                    $build_cnt_fuel = floor($planet->getResFuel() / $missile->getCostsFuel());
                                } else {
                                    $build_cnt_fuel = 99999999999;
                                }

                                //Nahrung
                                if ($missile->getCostsFood() > 0) {
                                    $build_cnt_food = floor($planet->getResFood() / $missile->getCostsFood());
                                } else {
                                    $build_cnt_food = 99999999999;
                                }

                                //Effetiv max. kaufbare Raketen in Betrachtung der Rohstoffe und der Silokapazität
                                $missile_max_build = min($build_cnt_metal, $build_cnt_crystal, $build_cnt_plastic, $build_cnt_fuel, $build_cnt_food, $store);

                                // Grösste Zahl die eingegeben werden kann (Da man auch verschrotten kann)
                                $available = intval($this->missileRepository->findOneBy(['entity'=>$planet,'missile'=>$missile])?->getCount());
                                $missile_max_number = max($missile_max_build, $available);

                                $missile->amount = $available;
                                $missile->maxBuild = $missile_max_build;

                                //Tippbox Nachricht generieren
                                //X Anlagen baubar
                                if ($missile_max_build > 0) {
                                    $tm_cnt = "Es können maximal " . StringUtils::formatNumber($missile_max_build) . " Raketen gekauft werden.";
                                }
                                //Zu wenig Felder.
                                elseif ($store == 0) {
                                    $tm_cnt = "Das Silo ist zu klein für weitere Raketen!";
                                }
                                //Zuwenig Rohstoffe. Wartezeit errechnen
                                elseif ($missile_max_build === 0) {
                                    //Wartezeit Titan
                                    $bwait = [];
                                    if ($planet->getProdMetal() > 0) {
                                        $bwait['metal'] = ceil(($missile->getCostsMetal() - $planet->getResMetal()) / $planet->getProdMetal() * 3600);
                                    } else {
                                        $bwait['metal'] = 0;
                                    }

                                    //Wartezeit Silizium
                                    if ($planet->getProdCrystal() > 0) {
                                        $bwait['crystal'] = ceil(($missile->getCostsCrystal() - $planet->getResCrystal()) / $planet->getProdCrystal() * 3600);
                                    } else {
                                        $bwait['crystal'] = 0;
                                    }

                                    //Wartezeit PVC
                                    if ($planet->getProdPlastic() > 0) {
                                        $bwait['plastic'] = ceil(($missile->getCostsPlastic() - $planet->getResPlastic()) / $planet->getProdPlastic() * 3600);
                                    } else {
                                        $bwait['plastic'] = 0;
                                    }

                                    //Wartezeit Tritium
                                    if ($planet->getProdFuel() > 0) {
                                        $bwait['fuel'] = ceil(($missile->getCostsFuel() - $planet->getResFuel()) / $planet->getProdFuel() * 3600);
                                    } else {
                                        $bwait['fuel'] = 0;
                                    }

                                    //Wartezeit Nahrung
                                    if ($planet->getProdFood() > 0) {
                                        $bwait['food'] = ceil(($missile->getCostsFood() - $planet->getResFood()) / $planet->getProdFood() * 3600);
                                    } else {
                                        $bwait['food'] = 0;
                                    }

                                    //Maximale Wartezeit ermitteln
                                    $bwmax = max($bwait['metal'], $bwait['crystal'], $bwait['plastic'], $bwait['fuel'], $bwait['food']);

                                    $tm_cnt = "Rohstoffe verfügbar in " . StringUtils::formatTimespan($bwmax);
                                } else {
                                    $tm_cnt = "";
                                }

                                //Stellt Rohstoff Rot dar, wenn es von diesem zu wenig auf dem Planeten hat
                                //Titan
                                if ($missile->getCostsMetal() > $planet->getResMetal()) {
                                    $missile->ressStyleMetal = "style=color:red;";
                                } else {
                                    $missile->ressStyleMetal = "";
                                }

                                //Silizium
                                if ($missile->getCostsCrystal() > $planet->getResCrystal()) {
                                    $missile->ressStyleCrystal = "style=color:red;";
                                } else {
                                    $missile->ressStyleCrystal = "";
                                }

                                //PVC
                                if ($missile->getCostsPlastic() > $planet->getResPlastic()) {
                                    $missile->ressStylePlastic = "style=color:red;";
                                } else {
                                    $missile->ressStylePlastic = "";
                                }

                                //Tritium
                                if ($missile->getCostsFuel() > $planet->getResFuel()) {
                                    $missile->ressStyleFuel = "style=color:red;";
                                } else {
                                    $missile->ressStyleFuel = "";
                                }

                                //Nahrung
                                if ($missile->getCostsFood() > $planet->getResFood()) {
                                    $missile->ressStyleFood = "style=color:red;";
                                } else {
                                    $missile->ressStyleFood = "";
                                }

                                $form = $form->add($missile->getId(), TextType::class, [
                                    'attr' => [
                                        'size' => 5,
                                        'maxlength' => 9,
                                        'onKeyUp' => "FormatNumber(this.id,this.value, $missile_max_number, '', '');",
                                        'onMouseOut' => "hideTT()",
                                        'onMouseOver' => "showTT('','".StringUtils::replaceBR(StringUtils::encodeDBStringToJS($tm_cnt))."',1,event,this)",
                                    ],
                                    'data'=>0
                                ]);

                                $availableMissiles[] = $missile;
                            }
                        }

                        $form = $form
                            ->add('buy', SubmitType::class, [
                                'label' => 'Ausgewählte Anzahl kaufen',
                            ])
                            ->add('scrap', SubmitType::class, [
                                'label' => 'Ausgewählte Anzahl verschrotten',
                                'attr' => [
                                    'onClick' => "return confirm('Sollen die gewählten Raketen wirklich verschrottet werden? Es werden keine Ressourcen zurückerstattet!')"
                                ]
                            ])
                            ->getForm()
                            ->handleRequest($request);

                        if ($form->isSubmitted() && $form->isValid()) {
                            //Kaufen
                            if($form->get('buy')->isClicked()) {
                                $valid = false;
                                $buymissiles = array();
                                foreach ($form->getData() as $k => $v) {
                                    $v = intval($v);
                                    $k = intval($k);
                                    if ($v > 0) {
                                        $valid = true;
                                        if ($v + $cnt <= $max_space) {
                                            $bc = $v;
                                        } else {
                                            $bc = $max_space - $cnt;
                                        }
                                        $bc = max($bc, 0);
                                        if ($bc > 0) {
                                            $buymissiles[$k] = $bc;
                                        }
                                        $cnt += $bc;
                                    }
                                }

                                if ($valid) {
                                    $bc = 0;
                                    foreach ($buymissiles as $k => $v) {
                                        $bc += $v;
                                        $missile = $this->missileDataRepository->find($k);

                                        if($this->missileService->checkRequirements($missile,$cu,$planet )) {
                                            $mcosts = [];
                                            $mcosts[0] = $missile->getCostsMetal() * $v;
                                            $mcosts[1] = $missile->getCostsCrystal() * $v;
                                            $mcosts[2] = $missile->getCostsPlastic() * $v;
                                            $mcosts[3] = $missile->getCostsFuel() * $v;
                                            $mcosts[4] = $missile->getCostsFood() * $v;

                                            if (
                                                $planet->getResMetal() >= $mcosts[0] &&
                                                $planet->getResCrystal() >= $mcosts[1] &&
                                                $planet->getResPlastic() >= $mcosts[2] &&
                                                $planet->getResFuel() >= $mcosts[3] &&
                                                $planet->getResFood() >= $mcosts[4]
                                            ) {
                                                $this->missileRepository->addMissile($missile, $v, $cu, $planet);
                                                $this->planetRepository->addResources($planet, -$mcosts[0], -$mcosts[1], -$mcosts[2], -$mcosts[3], -$mcosts[4]);
                                                $this->addFlash('success', $v . " " . $missile->getName() . " wurden gekauft!");
                                            } else {
                                                $this->addFlash('error',"Konnte " . $missile->getName() . " nicht kaufen, zu wenig Ressourcen!");
                                            }
                                        } else {
                                            $this->addFlash('error','Rakete nicht vorhanden!');
                                        }
                                    }
                                    if ($bc == 0) {
                                        $this->addFlash('error','Es konten keine Raketen gekauft werden, zuwenig Platz!');
                                    }
                                } else {
                                    $this->addFlash('error',"Keine oder ungültige Anzahl gewählt!");
                                }
                           }

                            // Remove
                            if($form->get('scrap')->isClicked()) {
                                $valid = false;
                                foreach ($form->getData() as $k => $v) {
                                    $v = StringUtils::parseFormattedNumber($v);
                                    $k = intval($k);

                                    if ($v > 0) {
                                        $valid = true;
                                        $search = new MissileListSearch();
                                        $missilelist = $this->missileRepository->searchOne($search->missileId($k)->userId($cu)->entityId($planet));
                                        $bc = min($v, $missilelist->getCount());

                                        $missilelist->setCount($missilelist->getCount()-$bc);
                                        $this->missileRepository->save();

                                        $cnt -= $bc;
                                        $this->addFlash('success',$bc . " " . $missilelist->getMissile()->getName() . " wurden verschrottet!");
                                    }
                                }

                                if (!$valid) {
                                    $this->addFlash('error',"Keine oder ungültige Anzahl gewählt!");
                                }
                            }
                        }

                        return $this->render('game/missiles/missiles.html.twig',[
                            'flights' => $planet->getMissileFlights(),
                            'availableMissiles' => $availableMissiles,
                            'cnt' => $cnt,
                            'maxSpace' => $max_space,
                            'form' => $form,
                            'name' => $planet->displayName(),
                            'level' => $missileBuilding->getCurrentLevel(),
                            'battleBan' => $this->configurationService->getBoolean('battleban') && $this->configurationService->param1Int('battleban_time') <= time() && $this->configurationService->param2Int('battleban_time') > time(),
                            'maxFlights' => $max_flights
                        ]);


                        /*

                                    // Bookmarks laden
                                    $bookmarks = array();
                                    // Gespeicherte Bookmarks
                                    $bookmarkedEntities = $bookmarkRepository->getBookmarkedEntities($cu->getId());
                                    foreach ($bookmarkedEntities as $bookmarkedEntity) {
                                        array_push(
                                            $bookmarks,
                                            array(
                                                "cell_sx" => $bookmarkedEntity->sx,
                                                "cell_sy" => $bookmarkedEntity->sy,
                                                "cell_cx" => $bookmarkedEntity->cx,
                                                "cell_cy" => $bookmarkedEntity->cy,
                                                "planet_solsys_pos" => $bookmarkedEntity->pos,
                                                "planet_name" => $bookmarkedEntity->planetName,
                                                "automatic" => 0,
                                                "bookmark_comment" => $bookmarkedEntity->comment
                                            )
                                        );
                                    }

                                    $entity = $entityRepository->findIncludeCell($planet->id);
                                    $coords = [];
                                    if (isset($_GET['target'])) {
                                        $target = $entityRepository->getEntity((int) $_GET['target']);
                                    } else {
                                        $target = $entity;
                                    }

                                    $keyup_command = 'xajax_getFlightTargetInfo(xajax.getFormValues(\'targetForm\'),' . $entity->sx . ',' . $entity->sy . ',' . $entity->cx . ',' . $entity->cy . ',' . $entity->pos . ');';
                                    echo '<form action="?page=' . $page . '" method="post" id="targetForm">';
                                    echo $cstr;
                                    tableStart("Raketen starten");
                                    echo '<tr><th style="width:260px;">Raketen wählen</th><th colspan="2" style="width:440px;">Ziel wählen</th></tr>
                            <tr><td rowspan="6">';
                                    $lblcnt = 0;
                                    foreach ($missilelist as $k => $v) {
                                        if ($v > 0 && $missiles[$k]->launchable) {
                                            echo '<input type="hidden" value="' . $missiles[$k]->speed . '" name="speed[' . $k . ']" />';
                                            echo '<input type="hidden" value="' . $missiles[$k]->range . '" name="range[' . $k . ']" />';
                                            echo '<input type="text" value="0" id="missle_' . $k . '" name="count[' . $k . ']" size="4" onkeyup="FormatNumber(this.id,this.value, \'' . $v . '\', \'\', \'\');' . $keyup_command . '"/>
                                    ' . $missiles[$k]->name . ' (' . $v . ' vorhanden)<br/>';
                                            $lblcnt++;
                                        }
                                    }
                                    if ($lblcnt == 0) {
                                        echo 'Momentan befinden sich keine startbaren Raketen in deinem Silo!';
                                    }
                                    echo '</td><th>:</th>
                            <td>
                                <input type="text"  onkeyup="' . $keyup_command . '" name="sx" id="sx" value="' . $target->sx . '" size="2" autocomplete="off" maxlength="2" /> /
                                <input type="text"  onkeyup="' . $keyup_command . '" name="sy" id="sy" value="' . $target->sy . '" size="2" autocomplete="off" maxlength="2" /> :
                                <input type="text"  onkeyup="' . $keyup_command . '" name="cx" id="cx" value="' . $target->cx . '" size="2" autocomplete="off" maxlength="2" /> /
                                <input type="text"  onkeyup="' . $keyup_command . '" name="cy" id="cy" value="' . $target->cy . '" size="2" autocomplete="off" maxlength="2" /> :
                                <input type="text"  onkeyup="' . $keyup_command . '" name="p" id="p" value="' . $target->pos . '" size="2" autocomplete="off" maxlength="2" />
                            </td></tr>';

                                    // Bookmarkliste anzeigen
                                    echo "<tr><th>Favorit wählen:</th><td><select id=\"bookmarkselect\" onchange=\"applyBookmark();\">";
                                    if (count($bookmarks) > 0) {
                                        $a = 1;
                                        echo "<option value=\"\">W&auml;hlen...</option>";
                                        foreach ($bookmarks as $i => $b) {
                                            echo "<option value=\"$i\">";
                                            if ($b['automatic'] == 1) echo "Eigener Planet: ";
                                            echo $b['cell_sx'] . "/" . $b['cell_sy'] . " : " . $b['cell_cx'] . "/" . $b['cell_cy'] . " : " . $b['planet_solsys_pos'] . " " . $b['planet_name'];
                                            if ($b['bookmark_comment'] != "") echo " (" . stripslashes($b['bookmark_comment']) . ")";
                                            echo "</option>";
                                        }
                                    } else
                                        echo "<option value=\"\">(Nichts vorhaden)</option>";
                                    echo "</select></td></tr>";

                                    echo '<tr><th>Zielinfo:</th><td id="targetinfo">
                            Wähle bitte ein Ziel...
                            </td></tr>
                            <tr><th>Entfernung:</th><td id="distance">
                            -
                            </td></tr>
                            <tr><th>Geschwindigkeit:</th><td id="speed">
                            -
                            </td></tr>
                            <tr><th>Zeit:</th><td id="time">
                            -
                            </td></tr>';
                                    tableEnd();
                                    echo '<input style="color:#f00" type="submit" name="launch" id="launchbutton" value="Starten" disabled="disabled" />';
                                    echo '<input type="hidden" name="timeforflight" value="0" id="timeforflight" />
                            <input type="hidden" name="targetcell" value="0" id="targetcell" />
                            <input type="hidden" name="targetplanet" value="0" id="targetplanet" /></form>';
                                    echo '<script type="text/javascript">' . $keyup_command . '</script>';
                                    echo "<script type=\"text/javascript\">
                            function applyBookmark()
                            {
                                select_id=document.getElementById('bookmarkselect').selectedIndex;
                                select_val=document.getElementById('bookmarkselect').options[select_id].value;
                                a=1;
                                if (select_val!='')
                                {
                                    switch(select_val)
                                    {
                                        ";
                                    foreach ($bookmarks as $i => $b) {
                                        echo "case \"$i\":\n";
                                        echo "document.getElementById('sx').value='" . $b['cell_sx'] . "';\n";
                                        echo "document.getElementById('sy').value='" . $b['cell_sy'] . "';\n";
                                        echo "document.getElementById('cx').value='" . $b['cell_cx'] . "';\n";
                                        echo "document.getElementById('cy').value='" . $b['cell_cy'] . "';\n";
                                        echo "document.getElementById('p').value='" . $b['planet_solsys_pos'] . "';\n";
                                        echo "break;\n";
                                    }
                                    echo "
                                    }

                                }
                                " . $keyup_command . "
                            }
                            </script>";
                            }
                        */
                    } else {
                        return $this->render('game/error.html.twig',[
                            'msg' => 'Keine Raketen verfügbar!',
                            'path' => $this->generateUrl('game.overview'),
                            'headline' => 'Raketensilo'
                        ]);
                    }
                } else {
                    return $this->render('game/error.html.twig',[
                        'msg' => "Dieses Gebäude ist noch bis " . StringUtils::formatDate($missileBuilding->getDeactivated()) . " deaktiviert!",
                        'path' => $this->generateUrl('game.overview'),
                        'headline' => 'Raketensilo'
                    ]);
                }
            } else {
                return $this->render('game/error.html.twig',[
                    'msg' => 'Zu wenig Energie verfügbar! Gebäude ist deaktiviert!',
                    'path' => $this->generateUrl('game.overview'),
                    'headline' => 'Raketensilo'
                ]);
            }
        } else {
            // Titel
            return $this->render('game/error.html.twig',[
                'msg' => 'Das Raketensilo wurde noch nicht gebaut!',
                'path' => $this->generateUrl('game.overview'),
                'headline' => 'Raketensilo'
            ]);
        }
    }
}