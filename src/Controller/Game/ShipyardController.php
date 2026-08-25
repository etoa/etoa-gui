<?php

namespace EtoA\Controller\Game;

use EtoA\Form\Type\Core\SingleSubmitType;
use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\ShipQueueItem;
use EtoA\Log\GameLogFacility;
use EtoA\Log\GameLogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipQueueSearch;
use EtoA\Ship\ShipSort;
use EtoA\Shipyard\ShipyardService;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserPropertiesRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShipyardController extends AbstractGameController
{
    public function __construct(
        private readonly BuildingListItemRepository   $buildingListItemRepository,
        private readonly PlanetRepository             $planetRepository,
        private readonly ShipyardService              $shipyardService,
        private readonly UserPropertiesRepository     $userPropertiesRepository,
        private readonly ShipQueueRepository          $shipQueueRepository,
        private readonly ShipDataRepository           $shipDataRepository,
        private readonly ConfigurationService         $configurationService,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly GameLogRepository            $gameLogRepository
    )
    {
    }

    #[Route('/game/shipyard', name: 'game.shipyard')]
    public function list(Request $request): Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $yard = $this->buildingListItemRepository->findOneBy(['building' => BuildingId::SHIPYARD->value, 'entity' => $cp]);
        $properties = $this->getUser()->getData()->getUserProperties();

        if ($yard && $yard->getCurrentLevel() > 0) {
            if (!$yard->isDeactivated()) {
                $sortForm = $this->container->get('form.factory')->createNamed('sort_form', FormType::class, $properties)
                    ->add('submit', SubmitType::class, [
                        'label' => 'Sortieren',
                    ])
                    ->add('itemOrderShip', ChoiceType::class, [
                        'choices' => array_flip(ShipSort::USER_SORT_VALUES)
                    ])
                    ->add('itemOrderWay', ChoiceType::class, [
                        'choices' => [
                            'Aufsteigend' => 'ASC',
                            'Absteigend' => 'DESC'
                        ]
                    ])
                    ->handleRequest($request);


                if ($sortForm->isSubmitted() && $sortForm->isValid()) {
                    $this->userPropertiesRepository->save();
                }

                $shipyardData = $this->shipyardService->getShipyardData();
                $shipForm = $this->container->get('form.factory')->createNamed('ship_form', FormType::class);

                foreach ($shipyardData['categories'] as $category) {
                    foreach ($category['ships'] as $ship) {
                        //TODO: use formCollection and refactor template
                        $shipForm = $shipForm->add($ship['id'], TextType::class, [
                            'attr' => [
                                'size' => 4,
                                'maxlength' => 9,
                                'onkeyup' => "FormatNumber(this.id,this.value, " . $ship['maxBuildable'] . ", '', '');",
                                'onmouseout' => 'hideTT();'
                            ],
                            'data' => 0,
                            'empty_data' => 0
                        ]);
                    }
                }

                $shipForm = $shipForm
                    ->add('submit', SubmitType::class, [
                        'label' => 'Bauaufträge übernehmen',
                    ])
                    ->add('checker', SingleSubmitType::class)
                    ->handleRequest($request);


                if ($shipForm->isSubmitted() && $shipForm->isValid()) {
                    //Log variablen setzten
                    $log_ships = "";

                    $totalMetal = 0;
                    $totalCrystal = 0;
                    $totalPlastic = 0;
                    $totalFuel = 0;
                    $totalFood = 0;

                    $queue = $this->shipQueueRepository->findOneBy(['entity' => $cp], ['endTime' => 'DESC']);
                    $end_time = $queue ? $queue->getEndTime() : time();
                    $specialist = $this->getUser()->getData()->getSpecialist();
                    $gen_tech_level = $this->technologyListItemRepository->getTechnologyLevel($this->getUser()->getData(), TechnologyId::GEN) ?? 0;

                    // level zählen welches die schiffswerft über dem angegeben level ist und faktor berechnen
                    $need_bonus_level = $yard->getCurrentLevel() - $this->configurationService->param1Int('build_time_boni_schiffswerft');
                    if ($need_bonus_level <= 0) {
                        $time_boni_factor = 1;
                    } else {
                        $time_boni_factor = 1 - ($need_bonus_level * ($this->configurationService->getInt('build_time_boni_schiffswerft') / 100));
                    }

                    //
                    // Bauaufträge speichern
                    //
                    $counter = 0;
                    foreach ($shipyardData['categories'] as $category) {
                        foreach ($category['ships'] as $ship) {
                            $ship_id = intval($ship['id']);
                            $build_cnt = StringUtils::parseFormattedNumber($shipForm->get($ship['id'])->getData());
                            $buildShip = $this->shipDataRepository->find($ship_id);
                            if ($build_cnt > 0 && $buildShip) {
                                $buildCountOriginal = $build_cnt;
                                $ship_count = $this->shipyardService->getAllShipCount($ship_id);

                                //Anzahl überprüfen, ob diese die maximalzahl übersteigt, gegebenenfalls ändern
                                if ($build_cnt + $ship_count > $buildShip->getMaxCount() && $buildShip->getMaxCount() != 0) {
                                    $build_cnt = max(0, $buildShip->getMaxCount() - $ship_count);
                                }

                                //Wenn der User nicht genug Ress hat, die Anzahl Schiffe drosseln
                                $bf = [];
                                $bc = [];

                                //Titan
                                if ($buildShip->getCosts()->metal > 0) {
                                    $bf['metal'] = $cp->getResMetal() / $buildShip->getCosts()->metal;
                                } else {
                                    $bc['metal'] = 0;
                                }
                                //Silizium
                                if ($buildShip->getCosts()->crystal > 0) {
                                    $bf['crystal'] = $cp->getResCrystal() / $buildShip->getCosts()->crystal;
                                } else {
                                    $bc['crystal'] = 0;
                                }
                                //PVC
                                if ($buildShip->getCosts()->plastic > 0) {
                                    $bf['plastic'] = $cp->getResPlastic() / $buildShip->getCosts()->plastic;
                                } else {
                                    $bc['plastic'] = 0;
                                }
                                //Tritium
                                if ($buildShip->getCosts()->fuel > 0) {
                                    $bf['fuel'] = $cp->getResFuel() / $buildShip->getCosts()->fuel;
                                } else {
                                    $bc['fuel'] = 0;
                                }
                                //Nahrung
                                $additional_food_costs = $yard->getPeopleWorking() * $this->configurationService->getInt('people_food_require');
                                if ($additional_food_costs > 0 || $buildShip->getCosts()->food > 0) {
                                    $bf['food'] = $cp->getResFood() / (intval($additional_food_costs) + $buildShip->getCosts()->food);
                                } else {
                                    $bc['food'] = 0;
                                }

                                //Anzahl Drosseln ???
                                if ($build_cnt > floor(min($bf))) {
                                    $build_cnt = floor(min($bf));
                                }

                                //Check for Rene-Bug
                                if ($additional_food_costs < 0) {
                                    $build_cnt = 0;
                                }

                                //Anzahl muss grösser als 0 sein
                                if ($build_cnt > 0) {
                                    //Errechne Kosten pro auftrag schiffe
                                    $bc['metal'] = $buildShip->getCosts()->metal * $build_cnt;
                                    $bc['crystal'] = $buildShip->getCosts()->crystal * $build_cnt;
                                    $bc['plastic'] = $buildShip->getCosts()->plastic * $build_cnt;
                                    $bc['fuel'] = $buildShip->getCosts()->fuel * $build_cnt;
                                    $bc['food'] = ($additional_food_costs + $buildShip->getCosts()->food) * $build_cnt;

                                    // Bauzeit pro Schiff berechnen
                                    $btime = $buildShip->getCosts()->getSum()
                                        / $this->configurationService->getInt('global_time') * $this->configurationService->getFloat('ship_build_time')
                                        * $time_boni_factor
                                        * ($specialist ? $specialist->getTimeShips() : 1);

                                    //Rechnet zeit wenn arbeiter eingeteilt sind
                                    $btime_min = $btime * (0.1 - ($gen_tech_level / 100));
                                    if ($btime_min < $this->configurationService->getInt('shipyard_min_build_time')) $btime_min = $this->configurationService->getInt('shipyard_min_build_time');
                                    $btime = ceil($btime - $yard->getPeopleWorking() * $this->configurationService->getInt('people_work_done'));
                                    if ($btime < $btime_min) $btime = $btime_min;
                                    $obj_time = $btime;

                                    // Gesamte Bauzeit berechnen
                                    $duration = $build_cnt * $obj_time;

                                    // Setzt Starzeit des Auftrages, direkt nach dem letzten Auftrag
                                    $start_time = $end_time;
                                    $end_time = $start_time + $duration;

                                    // Auftrag speichern
                                    $this->shipQueueRepository->add($this->getUser()->getData(), $buildShip, $cp, $build_cnt, $start_time, (int)$end_time, (int)$obj_time);
                                    $this->buildingListItemRepository->markBuildingWorkingStatus($this->getUser()->getData(), $cp, BuildingId::SHIPYARD->value, true);

                                    //Rohstoffe summieren, diese werden nach der Schleife abgezogen
                                    $totalMetal += $bc['metal'];
                                    $totalCrystal += $bc['crystal'];
                                    $totalPlastic += $bc['plastic'];
                                    $totalFuel += $bc['fuel'];
                                    $totalFood += $bc['food'];

                                    //Log schreiben
                                    $log_text = "[b]Schiffsauftrag Bauen[/b]

                        [b]Start:[/b] " . date("d.m.Y H:i:s", $end_time) . "
                        [b]Ende:[/b] " . date("d.m.Y H:i:s", $end_time) . "
                        [b]Dauer:[/b] " . StringUtils::formatTimespan($duration) . "
                        [b]Dauer pro Einheit:[/b] " . StringUtils::formatTimespan($obj_time) . "
                        [b]Schiffswerft Level:[/b] " . $yard->getCurrentLevel() . "
                        [b]Eingesetzte Bewohner:[/b] " . StringUtils::formatNumber($yard->getPeopleWorking()) . "
                        [b]Gen-Tech Level:[/b] " . $gen_tech_level . "
                        [b]Eingesetzter Spezialist:[/b] " . ($specialist ? $specialist->getName() : "Kein Spezialist") . "

                        [b]Kosten[/b]
                        [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($bc['metal']) . "
                        [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($bc['crystal']) . "
                        [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($bc['plastic']) . "
                        [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($bc['fuel']) . "
                        [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($bc['food']) . "

                        [b]Rohstoffe auf dem Planeten[/b]
                        [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($cp->getResMetal() - $totalMetal) . "
                        [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($cp->getResCrystal() - $totalCrystal) . "
                        [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($cp->getResPlastic() - $totalPlastic) . "
                        [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($cp->getResFuel() - $totalFuel) . "
                        [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($cp->getResFood() - $totalFood);

                                    $this->gameLogRepository->add(GameLogFacility::SHIP, LogSeverity::INFO, $log_text,$this->getUser()->getData(), $this->getUser()->getData()->getAlliance(), $cp->getEntity(), $ship_id, 1, $build_cnt);
                                } else {
                                    $this->addFlash('error',$buildShip->getName() . ": Zu wenig Rohstoffe für diese Anzahl (".$buildCountOriginal.")!");
                                }
                                $counter++;
                            }
                        }
                    }

                    $this->planetRepository->addResources($cp, -$totalMetal, -$totalCrystal, -$totalPlastic, -$totalFuel, -$totalFood);

                    if ($counter == 0) {
                        $this->addFlash('error','Keine Schiffe gewählt!');
                    }

                    return $this->redirectToRoute('game.shipyard');
                }

                return $this->render('game/shipyard/list.html.twig', [
                    'planet' => $cp,
                    'shipyardData' => $shipyardData,
                    'sortForm' => $sortForm,
                    'level' => $yard->getCurrentLevel(),
                    'shipForm' => $shipForm
                ]);
            }

            return $this->render('game/error.html.twig', [
                'msg' => 'Diese Schiffswerft ist bis ' . date("d.m.Y H:i", $yard->getDeactivated()) . ' deaktiviert.',
                'path' => $this->generateUrl('game.overview'),
                'headline' => 'Raumschiffwerft'
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Die Raumschiffswerft wurde noch nicht gebaut!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Raumschiffwerft'
        ]);
    }

    #[Route('/game/shipyard/cancel/{id}', name: 'game.shipyard.cancel')]
    public function cancel(Request $request, ?ShipQueueItem $shipQueueItem): Response
    {
        $cp = $this->shipyardService->getCurrentPlanet();
        $cancel_res_factor = $this->shipyardService->getCancelResFactor();

        if ($shipQueueItem && $cancel_res_factor > 0 && $shipQueueItem->getEntity() === $cp) {
            //Zu erhaltende Rohstoffe errechnen
            $obj_cnt = min(ceil(($shipQueueItem->getEndTime() - max(time(), $shipQueueItem->getStartTime())) / $shipQueueItem->getObjectTime()), $shipQueueItem->getCount());
            $ship_id = $shipQueueItem->getId();

            $ret = [];
            $ret['metal'] = $shipQueueItem->getShip()->getCosts()->metal * $obj_cnt * $cancel_res_factor;
            $ret['crystal'] = $shipQueueItem->getShip()->getCosts()->crystal * $obj_cnt * $cancel_res_factor;
            $ret['plastic'] = $shipQueueItem->getShip()->getCosts()->plastic * $obj_cnt * $cancel_res_factor;
            $ret['fuel'] = $shipQueueItem->getShip()->getCosts()->fuel * $obj_cnt * $cancel_res_factor;
            $ret['food'] = $shipQueueItem->getShip()->getCosts()->food * $obj_cnt * $cancel_res_factor;

            // Daten für Log speichern
            $queue_count = $shipQueueItem->getCount();
            $queue_objtime = $shipQueueItem->getObjectTime();
            $start_time = $shipQueueItem->getStartTime();
            $end_time = $shipQueueItem->getEndTime();

            //Auftrag löschen
            $this->shipQueueRepository->deleteQueueItem($shipQueueItem);
            $this->buildingListItemRepository->markBuildingWorkingStatus($this->getUser()->getData(), $cp, BuildingId::SHIPYARD->value, false);

            // Nachkommende Aufträge werden Zeitlich nach vorne verschoben
            $queueItems = $this->shipQueueRepository->searchQueueItems(ShipQueueSearch::create()->entityId($cp)->startEqualAfter($end_time));
            if (count($queueItems) > 0) {
                $new_starttime = max($start_time, time());
                foreach ($queueItems as $item) {
                    $item->setStartTime($new_starttime);
                    $item->setEndTime($new_starttime + $item->getEndTime() - $item->getStartTime());
                    $new_starttime = $item->getEndTime();
                }
                $this->shipQueueRepository->save();
            }

            //Rohstoffe dem Planeten gutschreiben und aktualisieren
            $this->planetRepository->addResources($cp, $ret['metal'], $ret['crystal'], $ret['plastic'], $ret['fuel'], $ret['food']);

            //Log schreiben
            $log_text = "[b]Schiffsauftrag Abbruch[/b]

                [b]Auftragsdauer:[/b] " . StringUtils::formatTimespan($queue_objtime * $queue_count) . "

                [b]Erhaltene Rohstoffe[/b]
                [b]Faktor:[/b] " . $cancel_res_factor . "
                [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($ret['metal']) . "
                [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($ret['crystal']) . "
                [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($ret['plastic']) . "
                [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($ret['fuel']) . "
                [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($ret['food']) . "

                [b]Rohstoffe auf dem Planeten[/b]
                [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($cp->getResMetal() + $ret['metal']) . "
                [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($cp->getResCrystal() + $ret['crystal']) . "
                [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($cp->getResPlastic() + $ret['plastic']) . "
                [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($cp->getResFuel() + $ret['fuel']) . "
                [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($cp->getResFood() + $ret['food']);

            //Log Speichern
            $this->gameLogRepository->add(GameLogFacility::SHIP, LogSeverity::INFO, $log_text, $this->getUser()->getData(), $this->getUser()->getData()->getAlliance(), $cp->getEntity(), $ship_id, 0, $queue_count);
        }

        return $this->redirectToRoute('game.shipyard');
    }
}