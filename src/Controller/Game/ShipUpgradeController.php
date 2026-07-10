<?php

namespace EtoA\Controller\Game;

use EtoA\Entity\ShipListItem;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipXpCalculator;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShipUpgradeController extends AbstractGameController
{


    public function __construct(
        private readonly ShipListRepository $shipListRepository,
    )
    {
    }

    #[Route('/game/ships/upgrade', name: 'game.ships.upgrade')]
    public function list(): Response
    {
        //Listet alle spezial Schiffe auf die der user besitzt
        $shipList = $this->shipListRepository->getSpecialShipsForUser($this->getUser()->getData());
        $data = [];

        if (count($shipList) > 0) {
            return $this->render('game/shipupgrade/list.html.twig', [
                'shipsData' => $this->buildData($shipList)
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Du bist noch nicht im Besitz eines Spezialschiffes!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Spezialschiffe'
        ]);
    }

    #[Route('/game/ships/upgrade/{id}', name: 'game.ships.upgrade.detail')]
    public function detail(Request $request, ?ShipListItem $shipListItem): Response
    {
        if($shipListItem && $shipListItem->getUser() === $this->getUser()->getData())
        {
            $specialShip = $shipListItem->getShip();

            $form = $this->createFormBuilder($specialShip)
                ->add('specialBonusWeapon', ChoiceType::class, [
                    'expanded' => true,
                    'choices' => [
                       '' => ''
                    ],
                    'label' => false
                ])
                ->add('specialBonusStructure', ChoiceType::class, [
                    'expanded' => true,
                    'choices' => [
                        '' => '',
                    ],
                    'label' => false
                ])
                ->getForm()
                ->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) { }

/*
            echo "<form action=\"?page=$page&amp;id=" . $specialShip->id . "\" method=\"post\">";

            //Zeigt alle Bonis die das Schiff upgraden kann
            tableStart("Bonis");
            echo "
                 <tr>
                     <th width=\"25%\">Skill</th>
                     <th width=\"10%\">Bonus</th>
                     <th width=\"63%\">Info</th>
                     <th width=\"2%\">LvL</th>
                 </tr>
                 ";


            // Waffentechnik Bonus
            if ($specialShip->specialBonusWeapon > 0) {
                echo "<tr>
                         <th>Waffen (" . $item->specialShipBonusWeapon . ")</th>
                         <td>" . (round($item->specialShipBonusWeapon * $specialShip->specialBonusWeapon * 100, 1)) . "%</td>
                         <td>Waffenbonus im Kampf (" . ($specialShip->specialBonusWeapon * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"weapon\" border=\"0\"></td>
                     </tr>";
            }
            // Struktur Bonus
            if ($specialShip->specialBonusStructure > 0) {
                echo "<tr>
                         <th>Panzerung (" . $item->specialShipBonusStructure . ")</th>
                         <td>" . (round($item->specialShipBonusStructure * $specialShip->specialBonusStructure * 100, 1)) . "%</td>
                         <td>Struktur im Kampf (" . ($specialShip->specialBonusStructure * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"structure\" border=\"0\"></td>
                     </tr>";
            }
            // Schild Bonus
            if ($specialShip->specialBonusShield > 0) {
                echo "<tr>
                         <th>Schild (" . $item->specialShipBonusShield . ")</th>
                         <td>" . (round($item->specialShipBonusShield * $specialShip->specialBonusShield * 100, 1)) . "%</td>
                         <td>Schildbonus im Kampf (" . ($specialShip->specialBonusShield * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"shield\" border=\"0\"></td>
                     </tr>";
            }
            // kapazitäts Bonus
            if ($specialShip->specialBonusCapacity > 0) {
                echo "<tr>
                         <th>Kapazität (" . $item->specialShipBonusCapacity . ")</th>
                         <td>" . (round($item->specialShipBonusCapacity * $specialShip->specialBonusCapacity * 100, 1)) . "%</td>
                         <td>Erhöht die Kapazität der ganzen Flotte (" . ($specialShip->specialBonusCapacity * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"capacity\" border=\"0\"></td>
                     </tr>";
            }
            // Speed Bonus
            if ($specialShip->specialBonusSpeed > 0) {
                echo "<tr>
                         <th>Speed (" . $item->specialShipBonusSpeed . ")</th>
                         <td>" . (round($item->specialShipBonusSpeed * $specialShip->specialBonusSpeed * 100, 1)) . "%</td>
                         <td>Erhöht den Speed der ganzen Flotte (" . ($specialShip->specialBonusSpeed * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"speed\" border=\"0\"></td>
                     </tr>";
            }
            // Tarn Bonus
            if ($specialShip->specialBonusTarn > 0) {
                echo "<tr>
                         <th>Tarnung (" . $item->specialShipBonusTarn . ")</th>
                         <td>" . (round($item->specialShipBonusTarn * $specialShip->specialBonusTarn * 100, 1)) . "%</td>
                         <td>Ermöglicht eine absolute Tarnung der Flotte (" . ($specialShip->specialBonusTarn * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"tarn\" border=\"0\"></td>
                     </tr>";
            }
            // Piloten Bonus
            if ($specialShip->specialBonusPilots > 0) {
                echo "<tr>
                         <th>Besatzung (" . $item->specialShipBonusPilots . ")</th>
                         <td>" . (round($item->specialShipBonusPilots * $specialShip->specialBonusPilots * 100, 1)) . "%</td>
                         <td>Verringert die benötigten Piloten der Flotte (" . ($specialShip->specialBonusPilots * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"pilots\" border=\"0\"></td>
                     </tr>";
            }
            // Heal Bonus
            if ($specialShip->specialBonusHeal > 0) {
                echo "<tr>
                         <th>Heilung (" . $item->specialShipBonusHeal . ")</th>
                         <td>" . (round($item->specialShipBonusHeal * $specialShip->specialBonusHeal * 100, 1)) . "%</td>
                         <td>Heilbonus im Kampf (" . ($specialShip->specialBonusHeal * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"heal\" border=\"0\"></td>
                     </tr>";
            }
            // Giftgas Bonus
            if ($specialShip->specialBonusAntrax > 0) {
                echo "<tr>
                         <th>Giftgas (" . $item->specialShipBonusAnthrax . ")</th>
                         <td>" . (round($item->specialShipBonusAnthrax * $specialShip->specialBonusAntrax * 100, 1)) . "%</td>
                         <td>Erhöht Giftgaseffekt (" . ($specialShip->specialBonusAntrax * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"heal\" border=\"0\"></td>
                     </tr>";
            }
            // Techklau Bonus
            if ($specialShip->specialBonusForsteal > 0) {
                echo "<tr>
                         <th>Spionageangriff (" . $item->specialShipBonusForSteal . ")</th>
                         <td>" . (round($item->specialShipBonusForSteal * $specialShip->specialBonusForsteal * 100, 1)) . "%</td>
                         <td>Erhöht die Erfolgschancen beim Spionageangriff (" . ($specialShip->specialBonusForsteal * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"forsteal\" border=\"0\"></td>
                     </tr>";
            }
            // Bombardieren Bonus
            if ($specialShip->specialBonusBuildDestroy > 0) {
                echo "<tr>
                         <th>Bombardieren (" . $item->specialShipBonusBuildDestroy . ")</th>
                         <td>" . (round($item->specialShipBonusBuildDestroy * $specialShip->specialBonusBuildDestroy * 100, 1)) . "%</td>
                         <td>Erhöht Bombardierungschancen (" . ($specialShip->specialBonusBuildDestroy * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"build_destroy\" border=\"0\"></td>
                     </tr>";
            }
            // Antrax Bonus
            if ($specialShip->specialBonusAntraxFood > 0) {
                echo "<tr>
                         <th>Antrax (" . $item->specialShipBonusAnthraxFood . ")</th>
                         <td>" . (round($item->specialShipBonusAnthraxFood * $specialShip->specialBonusAntraxFood * 100, 1)) . "%</td>
                         <td>Erhöht Antraxeffekt (" . ($specialShip->specialBonusAntraxFood * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"antrax_food\" border=\"0\"></td>
                     </tr>";
            }
            // Deaktivieren Bonus
            if ($specialShip->specialBonusDeactivate > 0) {
                echo "<tr>
                         <th>Deaktivieren (" . $item->specialShipBonusDeactivate . ")</th>
                         <td>" . (round($item->specialShipBonusDeactivate * $specialShip->specialBonusDeactivate * 100, 1)) . "%</td>
                         <td>Erhöht Deaktivierungschancen (" . ($specialShip->specialBonusDeactivate * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"deactivade\" border=\"0\"></td>
                     </tr>";
            }
            // Readyness Bonus
            if ($specialShip->specialBonusReadiness > 0) {
                echo "<tr>
                         <th>Bereitschaft (" . $item->specialShipBonusReadiness . ")</th>
                         <td>" . (round($item->specialShipBonusReadiness * $specialShip->specialBonusReadiness * 100, 1)) . "%</td>
                         <td>Verringert die Start- und Landezeit der ganzen Flotte (" . ($specialShip->specialBonusReadiness * 100) . "% pro Level)</td>
                         <td style=\"text-align:center;vertical-align:middle;\"><input type=\"radio\" name=\"upgrade\" value=\"readiness\" border=\"0\"></td>
                     </tr>";
            }



            tableEnd();

            //Level Button anzeigen, wenn genügend EXP vorhaden
            if ($level - $init_level > 0 && ($specialShip->specialMaxLevel > $init_level || $specialShip->specialMaxLevel === 0)) {
                echo "<input type=\"hidden\" name=\"id\" value=\"" . $specialShip->id . "\">";
                echo "<input type=\"submit\" class=\"button\" name=\"submit_upgrade\" value=\"Gewähltes Upgrade duchführen\" /><br><br>";
            }
            echo "</form>";


            echo "<input type=\"button\" value=\"Zurück zur Übersicht\" onclick=\"document.location='?page=ship_upgrade'\" />";

*/


            return $this->render('game/shipupgrade/detail.html.twig', [
                'shipsData' => $this->buildData([$shipListItem]),
                'form' => $form
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Du musst dieses Schiff zuerst bauen, oder auf den Planeten wechseln, auf dem sich das Schiff befindet!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Schiffsupgrade-Menu'
        ]);
    }

    private function buildData(array $shipList): array
    {
        $data = [];

        foreach ($shipList as $item) {
            $ship = $item->getShip();
            $init_level = $item->getSpecialShipLevel();
            $init_exp = $item->getSpecialShipExp();

            //Errechnet das Level aus den momentanen erfahrungen (exp)
            $level = ShipXpCalculator::levelByXp($ship->getSpecialNeedExp(),$ship->getSpecialExpFactor(),$item->getSpecialShipExp());
            $exp_for_next_level = ShipXpCalculator::levelByXp($ship->getSpecialNeedExp(),$ship->getSpecialExpFactor(),$level);

            $data[] = [
                'ship' => $ship,
                'initLevel' => $init_level,
                'initExp' => $init_exp,
                'expForNextLevel' => $exp_for_next_level,
                'item' => $item,
                'level' => $level
            ];
        }

        return $data;
    }
}