<?php

namespace EtoA\Controller\Game;

use EtoA\Form\Type\Core\SingleSubmitType;
use EtoA\Entity\ShipListItem;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipXpCalculator;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        if ($shipListItem && $shipListItem->getUser() === $this->getUser()->getData()) {
            $specialShip = $shipListItem->getShip();
            $data = $this->buildData([$shipListItem]);
            $texts = [
                'weapon' => [
                    'label' => 'Waffen',
                    'text' => 'Waffenbonus im Kampf'
                ],
                'structure' => [
                    'label' => 'Panzerung',
                    'text' => 'Struktur im Kampf'
                ],
                'shield' => [
                    'label' => 'Schild',
                    'text' => 'Schildbonus im Kampf'
                ],
                'speed' => [
                    'label' => 'Speed',
                    'text' => 'Erhöht den Speed der ganzen Flotte'
                ],
                'pilots' => [
                    'label' => 'Besatzung',
                    'text' => 'Verringert die benötigten Piloten der Flotte'
                ],
                'capacity' => [
                    'label' => 'Kapazität',
                    'text' => 'Erhöht die Kapazität der ganzen Flotte'
                ],
                'tarn' => [
                    'label' => 'Tarnung',
                    'text' => 'Ermöglicht eine absolute Tarnung der Flotte'
                ],
                'heal' => [
                    'label' => 'Heilung',
                    'text' => 'Heilbonus im Kampf'
                ],
                'anthrax' => [
                    'label' => 'Giftgas',
                    'text' => 'Erhöht Giftgaseffekt'
                ],
                'forsteal' => [
                    'label' => 'Spionageangriff',
                    'text' => 'Erhöht die Erfolgschancen beim Spionageangriff'
                ],
                'buildDestroy' => [
                    'label' => 'Bombardieren',
                    'text' => 'Erhöht Bombardierungschancen'
                ],
                'anthraxFood' => [
                    'label' => 'Antrax',
                    'text' => 'Erhöht Antraxeffekt'
                ],
                'deactivate' => [
                    'label' => 'Deaktivieren',
                    'text' => 'Erhöht Deaktivierungschancen'
                ],
                'readiness' => [
                    'label' => 'Bereitschaft',
                    'text' => 'Verringert die Start- und Landezeit der ganzen Flotte'
                ]
            ];

            $form = $this->createFormBuilder()
                ->add('bonus', ChoiceType::class, [
                    'expanded' => true,
                    'choice_loader' => new CallbackChoiceLoader(static function () use ($specialShip): array {
                        $ref = new \ReflectionClass($specialShip);
                        $allMethods = $ref->getMethods();
                        $getSpecialSkills = [];

                        foreach ($allMethods as $method) {
                            $name = $method->getName();

                            if (str_starts_with($name, 'getSpecialBonus')) {
                                if ($method->invoke($specialShip) > 0)
                                    // would be so much easier if ship and shiplist had the property name for special skills
                                    $getSpecialSkills[] = lcfirst(str_replace('getSpecialBonus', '', $name));
                            }
                        }

                        return $getSpecialSkills;
                    }),
                    'label' => false,
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Gewähltes Upgrade durchführen',
                ])
                ->add('checker', SingleSubmitType::class)
                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                switch ($form->getData()['bonus']) {
                    case 'weapon':
                        $shipListItem->setSpecialShipBonusWeapon($shipListItem->getSpecialShipBonusWeapon() + 1);
                        break;
                    case 'structure':
                        $shipListItem->setSpecialShipBonusStructure($shipListItem->getSpecialShipBonusStructure() + 1);
                        break;
                    case 'shield':
                        $shipListItem->setSpecialShipBonusShield($shipListItem->getSpecialShipBonusShield() + 1);
                        break;
                    case 'heal':
                        $shipListItem->setSpecialShipBonusHeal($shipListItem->getSpecialShipBonusHeal() + 1);
                        break;
                    case 'capacity':
                        $shipListItem->setSpecialShipBonusCapacity($shipListItem->getSpecialShipBonusCapacity() + 1);
                        break;
                    case 'speed':
                        $shipListItem->setSpecialShipBonusSpeed($shipListItem->getSpecialShipBonusSpeed() + 1);
                        break;
                    case 'pilots':
                        $shipListItem->setSpecialShipBonusPilots($shipListItem->getSpecialShipBonusPilots() + 1);
                        break;
                    case 'tarn':
                        $shipListItem->setSpecialShipBonusTarn($shipListItem->getSpecialShipBonusTarn() + 1);
                        break;
                    case 'anthrax':
                        $shipListItem->setSpecialShipBonusAnthrax($shipListItem->getSpecialShipBonusAnthrax() + 1);
                        break;
                    case 'forsteal':
                        $shipListItem->setSpecialShipBonusForSteal($shipListItem->getSpecialShipBonusForSteal() + 1);
                        break;
                    case 'buildDestroy':
                        $shipListItem->setSpecialShipBonusBuildDestroy($shipListItem->getSpecialShipBonusBuildDestroy() + 1);
                        break;
                    case 'anthraxFood':
                        $shipListItem->setSpecialShipBonusAnthraxFood($shipListItem->getSpecialShipBonusAnthraxFood() + 1);
                        break;
                    case 'deactivate':
                        $shipListItem->setSpecialShipBonusDeactivate($shipListItem->getSpecialShipBonusDeactivate() + 1);
                        break;
                    case 'readiness':
                        $shipListItem->setSpecialShipBonusReadiness($shipListItem->getSpecialShipBonusReadiness() + 1);
                        break;
                    default:
                        throw new \RuntimeException('Invalid special ability: ' . $form->getData()['bonus']);
                }
                $shipListItem->setSpecialShipLevel($shipListItem->getSpecialShipLevel() + 1);

                if ($data[0]['level'] - $data[0]['initLevel'] > 0 &&
                    (
                        $data[0]['ship']->getSpecialMaxLevel() > $data[0]['initLevel']
                        || $data[0]['ship']->getSpecialMaxLevel() === 0
                    )
                ) {
                    $this->shipListRepository->save();

                    $this->addFlash('success', "Upgrade erfolgreich durchgeführt!");
                }

                return $this->redirectToRoute('game.ships.upgrade.detail', ['id' => $shipListItem->getId()]);
            }

            return $this->render('game/shipupgrade/detail.html.twig', [
                'shipsData' => $data,
                'form' => $form,
                'texts' => $texts
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
            $init_exp = $item->getSpecialShipExp();;
            //Errechnet das Level aus den momentanen erfahrungen (exp)
            $level = ShipXpCalculator::levelByXp($ship->getSpecialNeedExp(), $ship->getSpecialExpFactor(), $item->getSpecialShipExp());
            $exp_for_next_level = ShipXpCalculator::xpByLevel($ship->getSpecialNeedExp(), $ship->getSpecialExpFactor(), $level);

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