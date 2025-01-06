<?php

namespace EtoA\Controller\Game;

use _PHPStan_70b6e53dc\Nette\Neon\Entity;
use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceBuildListRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceSpendRepository;
use EtoA\Alliance\Base\AllianceBase;
use EtoA\Alliance\Base\AllianceItemBuildStatus;
use EtoA\Alliance\Base\AllianceItemRequirementStatus;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\AllianceSpend;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetShipRepository;
use EtoA\Fleet\FleetStatus;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipListItemCount;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class AllianceBaseController extends AbstractGameController
{
    public function __construct(
        private readonly AllianceBuildListRepository $allianceBuildListRepository,
        private readonly AllianceBuildingRepository $allianceBuildingRepository,
        private readonly AllianceBase $allianceBase,
        private readonly PlanetRepository $planetRepository,
        private readonly UserRepository $userRepository,
        private readonly AllianceSpendRepository $allianceSpendRepository,
        private readonly AllianceRepository $allianceRepository,
        private readonly ConfigurationService $config,
        private readonly ShipDataRepository $shipDataRepository,
        private readonly ShipListRepository $shipListRepository,
        private readonly ShipQueueRepository $shipQueueRepository,
        private readonly FleetRepository $fleetRepository,
        private readonly EntityRepository $entityRepository,
        private readonly FleetShipRepository $fleetShipRepository,
        private readonly ShipRepository $shipRepository,
        private readonly AllianceHistoryRepository $allianceHistoryRepository
    )
    {
    }

    #[Route('/game/alliance/base/buildings', name: 'game.alliance.base.buildings')]
    public function buildings(Request $request): Response {
        $buildings = $this->allianceBuildingRepository->findAll();

        return $this->render('game/alliance/base/alliance_base_buildings.html.twig', [
            'buildings' => $this->allianceBase->renderBuildings($buildings)
        ]);
    }

    #[Route('/game/alliance/base/research', name: 'game.alliance.base.research')]
    public function research(Request $request): Response {
        return $this->render('game/alliance/base/alliance_base.html.twig');
    }

    #[Route('/game/alliance/base/storage', name: 'game.alliance.base.storage')]
    public function storage(Request $request): Response {

        $cp = $this->planetRepository->findOneBy(['id' =>$request->getSession()->get('cpid')]);
        $sum = false;
        $limit = 10;
        $user_message = "";
        $user = null;

        $form_storage = $this->createFormBuilder()
            ->add('metal', TextType::class, [
                'attr' => [
                    'size'=>9,
                    'maxlength'=>15,
                    'onkeyup'=> "FormatNumber(this.id,this.value," . $cp->getResMetal() . ",'','')"
                ],
                'required' => false,
                'data' => 0
            ])
            ->add('crystal', TextType::class, [
                'attr' => [
                    'size'=>9,
                    'maxlength'=>15,
                    'onkeyup'=> "FormatNumber(this.id,this.value," . $cp->getResCrystal() . ",'','')"
                ],
                'required' => false,
                'data' => 0
            ])
            ->add('plastic', TextType::class, [
                'attr' => [
                    'size'=>9,
                    'maxlength'=>15,
                    'onkeyup'=> "FormatNumber(this.id,this.value," . $cp->getResplastic() . ",'','')"
                ],
                'required' => false,
                'data' => 0
            ])
            ->add('fuel', TextType::class, [
                'attr' => [
                    'size'=>9,
                    'maxlength'=>15,
                    'onkeyup'=> "FormatNumber(this.id,this.value," . $cp->getResFuel() . ",'','')"
                ],
                'required' => false,
                'data' => 0
            ])
            ->add('food', TextType::class, [
                'attr' => [
                    'size'=>9,
                    'maxlength'=>15,
                    'onkeyup'=> "FormatNumber(this.id,this.value," . $cp->getResFood() . ",'','')"
                ],
                'required' => false,
                'data' => 0
            ])
            ->add('save', SubmitType::class, ['label' => 'Einzahlen'])
            ->getForm()
            ->handleRequest($request);

        if ($form_storage->isSubmitted() && $form_storage->isValid()) {
            $data = $form_storage->getData();

            $resources = new BaseResources();
            $resources->metal = StringUtils::parseFormattedNumber($data['metal']);
            $resources->crystal = StringUtils::parseFormattedNumber($data['crystal']);
            $resources->plastic = StringUtils::parseFormattedNumber($data['plastic']);
            $resources->fuel = StringUtils::parseFormattedNumber($data['fuel']);
            $resources->food = StringUtils::parseFormattedNumber($data['food']);

            // Prüft, ob Rohstoffe angegeben wurden
            if ($resources->getSum() > 0) {
                // Prüft, ob Rohstoffe noch vorhanden sind
                if (
                    $cp->getResMetal() >= $resources->metal
                    && $cp->getResCrystal() >= $resources->crystal
                    && $cp->getResPlastic() >= $resources->plastic
                    && $cp->getResFuel() >= $resources->fuel
                    && $cp->getResFood() >= $resources->food
                ) {
                    // Rohstoffe vom Planet abziehen
                    $this->planetRepository->removeResources($cp, $resources);

                    // Rohstoffe der Allianz gutschreiben
                    $this->allianceRepository->addResources($this->getUser()->getData()->getAlliance(), $resources->metal, $resources->crystal, $resources->plastic, $resources->fuel, $resources->food);

                    // Spende speichern
                    $this->allianceSpendRepository->addEntry($this->getUser()->getData()->getAlliance(), $this->getUser()->getData(), $resources);
                    $msg['success'] = "Rohstoffe erfolgreich eingezahlt!";
                } else
                    $msg['error'] = "Es sind zu wenig Rohstoffe auf dem Planeten!";
            } else
                $msg['error'] = "Du hast keine Rohstoffe angegeben!";
        }

        $form_filter = $this->createFormBuilder()
            ->add('sum', ChoiceType::class, [
                'expanded' => true,
                'choices' => [
                    'Einzeln /' => 0,
                    'Summiert' => 1
                ],
                'data' => 0
            ])
            ->add('limit', ChoiceType::class, [
                'choices' => [
                    'alle' => 10,
                    'die letzte' => 1,
                    'die letzten 5' => 5,
                    'die letzten 20' => 20,
                ],
                'data' => 0
            ])
            ->add('user', ChoiceType::class, [
                'choices' => $this->userRepository->findBy(['alliance'=>$this->getUser()->getData()->getAlliance()]),
                'choice_value' => 'id',
                'choice_label' => 'nick',
                'placeholder' => 'alle',
                'required' => false
            ])


            ->add('save', SubmitType::class, ['label' => 'Anzeigen'])
            ->getForm()
            ->handleRequest($request);

        if ($form_filter->isSubmitted() && $form_filter->isValid()) {
            $sum = $form_filter->get('sum')->getData();
            $user = $form_filter->get('user')->getData();
            $limit = $form_filter->get('limit')->getData();
        }

        if ($user) {
            $user_message = "von " . $user . " ";
        }

        if($sum) {
            $result = $this->allianceSpendRepository->getTotalSpent($this->getUser()->getData()->getAlliance(), $user);
            $info = "Es werden die bisher eingezahlten Rohstoffe $user_message angezeigt.";
        }
        else {
            if ($limit > 0) {
                if ($limit == 1) {
                    $info =  "Es wird die letzte Einzahlung $user_message gezeigt.";
                } else {
                    $info =  "Es werden die letzten $limit Einzahlungen $user_message gezeigt.";
                }
            } else {
                $info =  "Es werden alle bisherigen Einzahlungen $user_message gezeigt.";
            }

            $constrain = $user?['alliance'=>$this->getUser()->getData()->getAlliance(),'user'=>$user]:['alliance'=>$this->getUser()->getData()->getAlliance()];
            $result = $this->allianceSpendRepository->findBy($constrain,['time'=>'DESC'],$limit);
        }

        return $this->render('game/alliance/base/alliance_base_storage.html.twig', [
            'form_storage'=> $form_storage,
            'cp' => $cp,
            'form_filter' => $form_filter,
            'result' => $result,
            'info' => $info,
            'user_message' => $user_message,
            'sum' => $sum,
            'msg' => $msg??null
        ]);
    }

    #[Route('/game/alliance/base/shipyard', name: 'game.alliance.base.shipyard')]
    public function shipyard(Request $request): Response {
        $alliance = $this->getUser()->getData()->getAlliance();
        $allianceShipyardLevel = $this->allianceBuildListRepository->getLevel($alliance, AllianceBuildingId::SHIPYARD);
        $ships = [];
        $cu = $this->getUser()->getData();
        $form = $this->createFormBuilder();

        if ($allianceShipyardLevel) {
            $allianceShips = $this->shipDataRepository->getAllianceShips();

            foreach ($allianceShips as $ship) {
                if ($ship->getAllianceShipyardLevel() <= $allianceShipyardLevel) {
                    $ships[$ship->getId()] = $ship;
                    $form->add($ship->getId(), NumberType::class, [
                        'attr' => [
                            'size'=>4,
                            'maxlength'=>6,
                            'onkeyup'=> "FormatNumber(this.id,this.value, '', '', '')"
                        ],
                        'required' => false,
                        'data' => 0,
                        'scale' => 0
                    ]);
                }
            }

            $form = $form->add('buy', SubmitType::class, [
                    'label' => 'Schiffe herstellen',
                    'attr' => [
                        'onmouseover' => "showTT('" . StringUtils::encodeDBStringToJS('Schiffe herstellen') . "','" . StringUtils::replaceBR(StringUtils::encodeDBStringToJS('Stellt aus den vorhandenen Teilen die gewünschten Schiffe für den ausgewählten User her.'))."',1,event,this)",
                        'onmouseout' => 'hideTT()'
                    ]
                ])
                ->getForm()
                ->handleRequest($request);

            // Userschiffe laden (wenn Schiffswerft gebaut)
            // Gebaute Schiffe laden
            $shiplist = array_map(fn (ShipListItemCount $count) => $count->sum(), $this->shipListRepository->getUserShipCounts($cu));

            // Bauliste von allen Planeten laden und nach Schiffe zusammenfassen
            $queue_total = $this->shipQueueRepository->getUserQueuedShipCounts($cu);

            // Flotten laden und nach Schiffe zusammenfassen
            $fleet = $this->fleetRepository->getUserFleetShipCounts($cu);

            if (count($ships) > 0) {
                foreach ($ships as $ship) {
                    // Zählt die Anzahl Schiffe dieses Typs im ganzen Account...
                    $ship_count = 0;
                    // ... auf den Planeten
                    if (isset($shiplist[$ship->getId()])) {
                        $ship_count += $shiplist[$ship->getId()];
                    }
                    // ... in der Bauliste
                    if (isset($queue_total[$ship->getId()])) {
                        $ship_count += $queue_total[$ship->getId()];
                    }
                    // ... in der Luft
                    if (isset($fleet[$ship->getId()])) {
                        $ship_count += $fleet[$ship->getId()];
                    }

                    //Kostenfaktor Schiffe
                    $cost_factor = pow($this->config->getFloat("alliance_shipcosts_factor"), $ship_count);
                    $ship->setAllianceCosts($ship->getAllianceCosts() * $cost_factor);
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Gebaute Schiffe laden
            $ship_costs = 0;
            $total_build_cnt = 0;
            $to_much = false;
            foreach ($form->getData() as $ship_id => $build_cnt) {
                // Formatiert die eingegebene Zahl (entfernt z.B. die Trennzeichen)
                $build_cnt = StringUtils::parseFormattedNumber($build_cnt);

                if ($build_cnt > 0) {
                    // Zählt die Anzahl Schiffe dieses Typs im ganzen Account...
                    $ship_count = 0;
                    // ... auf den Planeten
                    if (isset($shiplist[$ship_id])) {
                        $ship_count += $shiplist[$ship_id];
                    }
                    // ... in der Bauliste
                    if (isset($queue_total[$ship_id])) {
                        $ship_count += $queue_total[$ship_id];
                    }
                    // ... in der Luft
                    if (isset($fleet[$ship_id])) {
                        $ship_count += $fleet[$ship_id];
                    }

                    // Total Schiffe mit den zu bauenden
                    $total_count = $build_cnt + $ship_count;

                    // Prüft ob Anzahl grösser ist als Schiffsmaximum
                    if ($ships[$ship_id]->getMaxCount() >= $total_count || $ships[$ship_id]->getMaxCount() === 0) {
                        for ($i = $build_cnt - 1; $i >= 0; $i--) {
                            //Kostenfaktor Schiffe
                            $cost_factor = pow($this->config->getFloat("alliance_shipcosts_factor"), $ship_count + $i);
                            // Berechnet die Kosten
                            $ship_costs += $cost_factor * $ships[$ship_id]->getAllianceCosts();
                        }
                    }
                    // Die Anzahl übersteigt die Max. Anzahl -> Nachricht wird ausgegeben
                    else {
                        $to_much = true;
                    }
                    $total_build_cnt += $build_cnt;
                }
            }

            // Prüft, ob die Maximalanzahl nicht überschritten wird
            if (!$to_much) {
                if ($total_build_cnt > 0) {
                    // Prüft ob Schiffspunkte noch ausreichend sind
                    if ($cu->getAllianceShipPoints() >= $ship_costs) {
                        // Zieht Punkte vom Konto ab
                        $this->userRepository->markAllianceShipPointsAsUsed($cu, $ship_costs);
                        $ship_costed = $ship_costs;

                        // Lädt das Allianzentity
                        $allianceMarketId = $this->entityRepository->getAllianceMarketId();

                        // Speichert Flotte
                        $launchtime = time(); // Startzeit
                        $duration = 3600; // Dauer 1h
                        $landtime = $launchtime + $duration; // Landezeit
                        $fleet = $this->fleetRepository->add($cu, $launchtime, $landtime, $this->entityRepository->find($allianceMarketId), $this->entityRepository->find($request->getSession()->get('cpid')), \EtoA\Fleet\FleetAction::DELIVERY, FleetStatus::DEPARTURE, new BaseResources());

                        // Speichert Schiffe in der Flotte
                        $log = "";
                        $cnt = 0;
                        foreach ($form->getData() as $ship_id => $build_cnt) {
                            // Formatiert die eingegebene Zahl (entfernt z.B. die Trennzeichen)
                            $build_cnt = StringUtils::parseFormattedNumber($build_cnt);

                            if ($build_cnt > 0) {
                                $ship = $this->shipRepository->find($ship_id);
                                $this->fleetShipRepository->addShipsToFleet($fleet, $ship, $build_cnt);
                                if ($cnt == 0) {
                                    $fleet[$ship_id] = ($fleet[$ship_id] ?? 0) + $build_cnt;
                                    // Gibt einmalig eine OK-Medlung aus
                                    $msg['success'] = "Schiffe wurden erfolgreich hergestellt!";
                                }

                                // Listet gewählte Schiffe für Log auf
                                $log .= "[b]" . $ship->getName() . ":[/b] " . StringUtils::formatNumber($build_cnt) . "\n";

                                $cnt++;
                            }
                        }

                        // Zur Allianzgeschichte hinzufügen
                        $this->allianceHistoryRepository->addEntry($cu->getAlliance(), "Folgende Schiffe wurden für [b]" . $cu->getNick() . "[/b] hergestellt:\n" . $log . "\n" . StringUtils::formatNumber($ship_costs) . " Teile wurden dafür benötigt.");
                    } else {
                        $msg['error']="Du hast nicht genügend Teile übrig!";
                    }
                } else {
                    $msg['error'] = "Keine Schiffe ausgewählt!";
                }
            } else {
                $msg['error'] = "Die Maximalanzahl der Schiffe würde mit der eingegebenen Menge überschritten werden!";
            }
        }

        return $this->render('game/alliance/base/alliance_base_shipyard.html.twig', [
            'stopped' => $alliance->getResMetal() < 0 || $alliance->getResCrystal() < 0 || $alliance->getResPlastic() < 0 || $alliance->getResFuel() < 0 || $alliance->getResFood() < 0,
            'production' => ceil($this->config->getInt('alliance_shippoints_per_hour') * pow($this->config->getFloat('alliance_shippoints_base'), ($allianceShipyardLevel - 1))),
            'points' => $this->getUser()->getData()->getAllianceShipPoints(),
            'ships' =>$ships,
            'form' => $form,
            'msg' => $msg??null
        ]);
    }
}