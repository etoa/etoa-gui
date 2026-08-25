<?php

namespace EtoA\Controller\Game;

use EtoA\Form\Type\Core\SingleSubmitType;
use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseQueueSearch;
use EtoA\Defense\DefenseSort;
use EtoA\Defense\DefenseyardService;
use EtoA\Entity\DefenseQueueItem;
use EtoA\Log\GameLogFacility;
use EtoA\Log\GameLogRepository;
use EtoA\Log\LogSeverity;
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

class DefenseController extends AbstractGameController
{


    public function __construct(
        private readonly PlanetRepository             $planetRepository,
        private readonly BuildingListItemRepository   $buildingListItemRepository,
        private readonly UserPropertiesRepository     $userPropertiesRepository,
        private readonly ConfigurationService         $configurationService,
        private readonly DefenseQueueRepository       $defenseQueueRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly DefenseDataRepository        $defenseDataRepository,
        private readonly GameLogRepository            $gameLogRepository,
        private readonly DefenseyardService           $defenseyardService
    )
    {
    }

    #[Route('/game/defense', name: 'game.defense')]
    public function list(Request $request): Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $yard = $this->buildingListItemRepository->findOneBy(['building' => BuildingId::DEFENSE->value, 'entity' => $cp]);
        $properties = $this->getUser()->getData()->getUserProperties();

        if ($yard && $yard->getCurrentLevel() > 0) {
            if (!$yard->isDeactivated()) {
                $sortForm = $this->container->get('form.factory')->createNamed('sort_form', FormType::class, $properties)
                    ->add('submit', SubmitType::class, [
                        'label' => 'Sortieren',
                    ])
                    ->add('itemOrderDef', ChoiceType::class, [
                        'choices' => array_flip(DefenseSort::USER_SORT_VALUES)
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

                $defenseyardData = $this->defenseyardService->getDefenseyardData();
                $defenseForm = $this->container->get('form.factory')->createNamed('defense_form', FormType::class);

                foreach ($defenseyardData['categories'] as $category) {
                    foreach ($category['defenses'] as $defense) {
                        //TODO: use formCollection and refactor template
                        $defenseForm = $defenseForm->add($defense['id'], TextType::class, [
                            'attr' => [
                                'size' => 4,
                                'maxlength' => 9,
                                'onkeyup' => "FormatNumber(this.id,this.value, " . $defense['maxBuildable'] . ", '', '');",
                                'onmouseout' => 'hideTT();'
                            ],
                            'data' => 0,
                            'empty_data' => 0
                        ]);
                    }
                }

                $defenseForm = $defenseForm
                    ->add('submit', SubmitType::class, [
                        'label' => 'Bauaufträge übernehmen',
                    ])
                    ->add('checker', SingleSubmitType::class)
                    ->handleRequest($request);


                if ($defenseForm->isSubmitted() && $defenseForm->isValid()) {
                    $totalMetal = 0;
                    $totalCrystal = 0;
                    $totalPlastic = 0;
                    $totalFuel = 0;
                    $totalFood = 0;

                    $queueEnd = $this->defenseQueueRepository->findOneBy(['entity' => $cp], ['endTime' => 'DESC']);

                    // Bauliste vom Planeten laden und nach Verteidigung zusammenfassen
                    $queueFields = 0;
                    foreach ($this->defenseQueueRepository->findAll() as $item) {
                        $queueFields += $item->getCount() * $item->getDefense()->getFields();
                    }

                    //Berechnet freie Felder
                    $fields_available = $cp->getFields() + $cp->getFieldsExtra() - $cp->getFieldsUsed() - $queueFields;

                    $end_time = $queueEnd ? $queueEnd->getEndTime() : time();
                    $specialist = $this->getUser()->getData()->getSpecialist();
                    $gen_tech_level = $this->technologyListItemRepository->getTechnologyLevel($this->getUser()->getData(), TechnologyId::GEN) ?? 0;

                    // level zählen welches die waffenfabrik über dem angegeben level ist und faktor berechnen
                    $need_bonus_level = $yard->getCurrentLevel() - $this->configurationService->param1Int('build_time_boni_waffenfabrik');
                    if ($need_bonus_level <= 0) {
                        $time_boni_factor = 1;
                    } else {
                        $time_boni_factor = 1 - ($need_bonus_level * ($this->configurationService->getInt('build_time_boni_waffenfabrik') / 100));
                    }

                    //
                    // Bauaufträge speichern
                    //
                    $counter = 0;
                    foreach ($defenseyardData['categories'] as $category) {
                        foreach ($category['defenses'] as $defense) {
                            $defense_id = intval($defense['id']);
                            $build_cnt = StringUtils::parseFormattedNumber($defenseForm->get($defense['id'])->getData());
                            $buildDefense = $this->defenseDataRepository->find($defense_id);
                            if ($build_cnt > 0 && $buildDefense) {
                                $buildCountOriginal = $build_cnt;
                                $defense_count = $this->defenseyardService->getAllDefenseCount($defense_id);

                                //Anzahl überprüfen, ob diese die maximalzahl übersteigt, gegebenenfalls ändern
                                if ($build_cnt + $defense_count > $buildDefense->getMaxCount() && $buildDefense->getMaxCount() != 0) {
                                    $build_cnt = max(0, $buildDefense->getMaxCount() - $defense_count);
                                }

                                //Wenn der User nicht genug freie Felder hat, die Anzahl Anlagen drosseln
                                if ($buildDefense->getFields() > 0 && $fields_available - $buildDefense->getFields() * $build_cnt < 0) {
                                    $build_cnt = floor($fields_available / $buildDefense->getFields());
                                }

                                //Wenn der User nicht genug Ress hat, die Anzahl Schiffe drosseln
                                $bf = [];
                                $bc = [];

                                //Titan
                                if ($buildDefense->getCosts()->metal > 0) {
                                    $bf['metal'] = $cp->getResMetal() / $buildDefense->getCosts()->metal;
                                } else {
                                    $bc['metal'] = 0;
                                }
                                //Silizium
                                if ($buildDefense->getCosts()->crystal > 0) {
                                    $bf['crystal'] = $cp->getResCrystal() / $buildDefense->getCosts()->crystal;
                                } else {
                                    $bc['crystal'] = 0;
                                }
                                //PVC
                                if ($buildDefense->getCosts()->plastic > 0) {
                                    $bf['plastic'] = $cp->getResPlastic() / $buildDefense->getCosts()->plastic;
                                } else {
                                    $bc['plastic'] = 0;
                                }
                                //Tritium
                                if ($buildDefense->getCosts()->fuel > 0) {
                                    $bf['fuel'] = $cp->getResFuel() / $buildDefense->getCosts()->fuel;
                                } else {
                                    $bc['fuel'] = 0;
                                }
                                //Nahrung
                                $additional_food_costs = $yard->getPeopleWorking() * $this->configurationService->getInt('people_food_require');
                                if ($additional_food_costs > 0 || $buildDefense->getCosts()->food > 0) {
                                    $bf['food'] = $cp->getResFood() / (intval($additional_food_costs) + $buildDefense->getCosts()->food);
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
                                    $bc['metal'] = $buildDefense->getCosts()->metal * $build_cnt;
                                    $bc['crystal'] = $buildDefense->getCosts()->crystal * $build_cnt;
                                    $bc['plastic'] = $buildDefense->getCosts()->plastic * $build_cnt;
                                    $bc['fuel'] = $buildDefense->getCosts()->fuel * $build_cnt;
                                    $bc['food'] = ($additional_food_costs + $buildDefense->getCosts()->food) * $build_cnt;

                                    // Bauzeit pro Schiff berechnen
                                    $btime = $buildDefense->getCosts()->getSum()
                                        / $this->configurationService->getInt('global_time') * $this->configurationService->getFloat('def_build_time')
                                        * $time_boni_factor
                                        * ($specialist ? $specialist->getTimeDefense() : 1);

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
                                    $this->defenseQueueRepository->add($this->getUser()->getData(), $buildDefense, $cp, $build_cnt, $start_time, (int)$end_time, (int)$obj_time);
                                    $this->buildingListItemRepository->markBuildingWorkingStatus($this->getUser()->getData(), $cp, BuildingId::DEFENSE->value, true);

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

                                    $this->gameLogRepository->add(GameLogFacility::DEF, LogSeverity::INFO, $log_text, $this->getUser()->getData(), $this->getUser()->getData()->getAlliance(), $cp->getEntity(), $defense_id, 1, $build_cnt);
                                } else {
                                    $this->addFlash('error', $buildDefense->getName() . ": Zu wenig Rohstoffe für diese Anzahl (" . $buildCountOriginal . ")!");
                                }
                                $counter++;
                            }
                        }
                    }

                    $this->planetRepository->addResources($cp, -$totalMetal, -$totalCrystal, -$totalPlastic, -$totalFuel, -$totalFood);

                    if ($counter == 0) {
                        $this->addFlash('error', 'Keine Schiffe gewählt!');
                    }

                    return $this->redirectToRoute('game.defense');
                }
                return $this->render('game/defense/list.html.twig', [
                    'planet' => $cp,
                    'defenseData' => $defenseyardData,
                    'sortForm' => $sortForm,
                    'level' => $yard->getCurrentLevel(),
                    'defenseForm' => $defenseForm
                ]);
            }

            return $this->render('game/error.html.twig', [
                'msg' => 'Diese Waffenfabrik ist bis ' . date("d.m.Y H:i", $yard->getDeactivated()) . ' deaktiviert.',
                'path' => $this->generateUrl('game.overview'),
                'headline' => 'Waffenfabrikt'
            ]);
        }
        return $this->render('game/error.html.twig', [
            'msg' => 'Die Waffenfabrik wurde noch nicht gebaut!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Waffenfabrik'
        ]);
    }

    #[Route('/game/defense/cancel/{id}', name: 'game.defense.cancel')]
    public function cancel(?DefenseQueueItem $defenseQueueItem): Response
    {
        $cp = $this->defenseyardService->getCurrentPlanet();
        $cancel_res_factor = $this->defenseyardService->getCancelResFactor();

        if ($defenseQueueItem && $cancel_res_factor > 0 && $defenseQueueItem->getEntity() === $cp) {
            //Zu erhaltende Rohstoffe errechnen
            $obj_cnt = min(ceil(($defenseQueueItem->getEndTime() - max(time(), $defenseQueueItem->getStartTime())) / $defenseQueueItem->getObjectTime()), $defenseQueueItem->getCount());
            $defense_id = $defenseQueueItem->getId();

            $ret = [];
            $ret['metal'] = $defenseQueueItem->getdefense()->getCosts()->metal * $obj_cnt * $cancel_res_factor;
            $ret['crystal'] = $defenseQueueItem->getdefense()->getCosts()->crystal * $obj_cnt * $cancel_res_factor;
            $ret['plastic'] = $defenseQueueItem->getdefense()->getCosts()->plastic * $obj_cnt * $cancel_res_factor;
            $ret['fuel'] = $defenseQueueItem->getdefense()->getCosts()->fuel * $obj_cnt * $cancel_res_factor;
            $ret['food'] = $defenseQueueItem->getdefense()->getCosts()->food * $obj_cnt * $cancel_res_factor;

            // Daten für Log speichern
            $queue_count = $defenseQueueItem->getCount();
            $queue_objtime = $defenseQueueItem->getObjectTime();
            $start_time = $defenseQueueItem->getStartTime();
            $end_time = $defenseQueueItem->getEndTime();

            //Auftrag löschen
            $this->defenseQueueRepository->deleteQueueItem($defenseQueueItem);
            $this->buildingListItemRepository->markBuildingWorkingStatus($this->getUser()->getData(), $cp, BuildingId::DEFENSE->value, false);

            // Nachkommende Aufträge werden Zeitlich nach vorne verschoben
            $queueItems = $this->defenseQueueRepository->searchQueueItems(DefenseQueueSearch::create()->entity($cp)->startEqualAfter($end_time));
            if (count($queueItems) > 0) {
                $new_starttime = max($start_time, time());
                foreach ($queueItems as $item) {
                    $item->setStartTime($new_starttime);
                    $item->setEndTime($new_starttime + $item->getEndTime() - $item->getStartTime());
                    $new_starttime = $item->getEndTime();
                }
                $this->defenseQueueRepository->save();
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
            $this->gameLogRepository->add(GameLogFacility::DEF, LogSeverity::INFO, $log_text, $this->getUser()->getData(), $this->getUser()->getData()->getAlliance(), $cp->getEntity(), $defense_id, 0, $queue_count);
        }

        return $this->redirectToRoute('game.defense');
    }
}