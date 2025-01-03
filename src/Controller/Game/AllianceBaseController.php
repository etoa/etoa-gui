<?php

namespace EtoA\Controller\Game;

use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceBuildListRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceSpendRepository;
use EtoA\Alliance\Base\AllianceBase;
use EtoA\Alliance\Base\AllianceItemBuildStatus;
use EtoA\Alliance\Base\AllianceItemRequirementStatus;
use EtoA\Entity\AllianceSpend;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        private readonly AllianceRepository $allianceRepository
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
            ->getForm();

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
            ->getForm();

        $form_storage->handleRequest($request);
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

        $form_filter->handleRequest($request);
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
        $allianceShipyardLevel = $this->allianceBuildListRepository->getLevel($cu->allianceId(), AllianceBuildingId::SHIPYARD);

        if ($allianceShipyardLevel) {
            echo "<h1>Schiffswerft</h1>";

            echo "<form action=\"?page=" . $page . "&amp;action=" . $_GET['action'] . "&amp;action2=shipyard\" method=\"post\" id=\"alliance_shipyard\">\n";
            echo $cstr;

            tableStart("Guthaben Übersicht");

            echo "<tr>";
            if ($alliance->resMetal < 0 || $alliance->resCrystal < 0 || $alliance->resPlastic < 0 || $alliance->resFuel < 0 || $alliance->resFood < 0) {
                echo "<td style=\"text-align:center;\"><span " . tm("Produktionsstop", "Die Produktion wurde unterbrochen, da negative Rohstoffe vorhanden sind.") . ">Schiffsteile pro Stunde: 0</span></td>";
            } else {
                // if changed, also change classes/alliance.class.php
                echo "<td style=\"text-align:center;\">Schiffsteile pro Stunde: " . ceil($config->getInt('alliance_shippoints_per_hour') * pow($config->getFloat('alliance_shippoints_base'), ($allianceShipyardLevel - 1))) . "</td>";
            }
            echo "</tr>
    <tr>
        <td style=\"text-align:center;\">Vorhandene Teile: " . ($cu->allianceShippoints - $ship_costed) . "</td>
    </tr>";

            tableEnd();


            // Listet Schiffe auf
            if (count($ships) > 0) {
                foreach ($ships as $ship) {
                    // Zählt die Anzahl Schiffe dieses Typs im ganzen Account...
                    $ship_count = 0;
                    // ... auf den Planeten
                    if (isset($shiplist[$ship->id])) {
                        $ship_count += $shiplist[$ship->id];
                    }
                    // ... in der Bauliste
                    if (isset($queue_total[$ship->id])) {
                        $ship_count += $queue_total[$ship->id];
                    }
                    // ... in der Luft
                    if (isset($fleet[$ship->id])) {
                        $ship_count += $fleet[$ship->id];
                    }


                    //Kostenfaktor Schiffe
                    $cost_factor = pow($config->getFloat("alliance_shipcosts_factor"), $ship_count);

                    $path = $ship->getImagePath('medium');
                    tableStart($ship->name);
                    echo "<tr>
                <td style=\"width:120px;background:#000;vertical-align:middle;padding:0px;\">
                <img src=\"" . $path . "\" style=\"width:120px;height:120px;border:none;margin:0px;\" alt=\"" . $ship->name . "\"/>
                    <input type=\"hidden\" value=\"" . $ship->name . "\" id=\"ship_name_" . $ship->id . "\" name=\"ship_name_" . $ship->id . "\" />
                </td>
                <td style=\"vertical-align:top;height:100px;\" colspan=\"7\">
                    " . $ship->longComment . "
                </td>
                    </tr>
                    <tr>
                            <th style=\"width:13%\">Waffen</th>
                            <th style=\"width:13%\">Struktur</th>
                            <th style=\"width:13%\">Schild</th>
                            <th style=\"width:13%\">Speed</th>
                            <th style=\"width:13%\">Startzeit</th>
                            <th style=\"width:13%\">Landezeit</th>
                            <th style=\"width:12%\">Kosten</th>
                            <th style=\"width:10%\">Anzahl</th>
                        </tr>
                        <tr>
                            <td>" . StringUtils::formatNumber($ship->weapon) . "</td>
                            <td>" . StringUtils::formatNumber($ship->structure) . "</td>
                            <td>" . StringUtils::formatNumber($ship->shield) . "</td>
                            <td>" . StringUtils::formatNumber($ship->speed) . " AE/h</td>
                            <td>" . StringUtils::formatTimespan($ship->timeToStart / FLEET_FACTOR_S) . "</td>
                            <td>" . StringUtils::formatTimespan($ship->timeToLand / FLEET_FACTOR_S) . "</td>";
                    if ($ship->maxCount !== 0 && $ship->maxCount <= $ship_count) {
                        echo "<td colspan=\"2\"><i>Maximalanzahl erreicht</i></td>";
                    } else {
                        echo "<td>" . StringUtils::formatNumber($ship->allianceCosts * $cost_factor) . " <input type=\"hidden\" value=\"" . $ship->allianceCosts * $cost_factor . "\" id=\"ship_costs_" . $ship->id . "\" name=\"ship_costs_" . $ship->id . "\" /></td>
                            <td>
                                <input type=\"text\" value=\"0\" name=\"buy_ship[" . $ship->id . "]\" id=\"buy_ship_" . $ship->id . "\" size=\"4\" maxlength=\"6\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/>";
                    }
                    echo "<input type=\"hidden\" value=\"" . $ship->maxCount . "\" id=\"ship_max_count_" . $ship->id . "\" name=\"ship_max_count_" . $ship->id . "\" />
                                </td>
                        </tr>";


                    tableEnd();
                }
            } else {
                iBoxStart("Schiffe");
                echo "Es sind keine Allianzschiffe vorhanden!";
                iBoxEnd();
            }



            tableStart("Fertigung");

            echo "<tr>
                    <td style=\"text-align:center;\">
                        <select id=\"user_buy_ship\" name=\"user_buy_ship\">
                            <option value=\"" . $cu->id . "\">" . $cu . " (" . StringUtils::formatNumber($cu->allianceShippoints - $ship_costed) . ")</option>
                        </select><br/><br/>
                    <input type=\"submit\" class=\"button\" name=\"ship_submit\" id=\"ship_submit\" value=\"Schiffe herstellen\" " . tm("Schiffe herstellen", "Stellt aus den vorhandenen Teilen die gewünschten Schiffe für den ausgewählten User her.") . ">
                    </td>
                </tr>";

            tableEnd();

            echo "</form>";
        }

        return $this->render('game/alliance/base/alliance_base.html.twig');
    }
}