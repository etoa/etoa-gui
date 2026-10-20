<?php

namespace EtoA\Controller\Game;

use EtoA\Form\Type\Core\SingleSubmitType;
use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Form\Type\Core\BunkerShipCountType;
use EtoA\Form\Type\Core\CountType;
use EtoA\Ship\ShipListRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use \Symfony\Component\HttpFoundation\Response;

class BunkerController extends AbstractGameController
{
    public function __construct(
        private readonly PlanetRepository $planetRepository,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly ShipListRepository $shipListRepository
    )
    {
    }

    #[Route('/game/bunker/res', name: 'game.bunker.res')]
    public function res(Request $request):Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $resBunker = $this->buildingListItemRepository->findOneBy(['user'=>$this->getUser()->getData(),'entity'=>$cp,'building'=>BuildingId::RES_BUNKER]);

        if ($resBunker) {
            $form = $this->createFormBuilder([
                    'bunkerMetal' => StringUtils::formatNumber($cp->getBunkerMetal()),
                    'bunkerCrystal' => StringUtils::formatNumber($cp->getBunkerCrystal()),
                    'bunkerPlastic' => StringUtils::formatNumber($cp->getBunkerPlastic()),
                    'bunkerFuel' => StringUtils::formatNumber($cp->getBunkerFuel()),
                    'bunkerFood' => StringUtils::formatNumber($cp->getBunkerFood()),
                ])
                ->add('bunkerMetal', TextType::class, [
                    'attr' => [
                        'size' => 8,
                        'maxlength' => 20,
                        'onKeyUp' => "FormatNumber(this.id,this.value, '', '', '');"
                    ],
                ])
                ->add('bunkerCrystal', TextType::class, [
                    'attr' => [
                        'size' => 8,
                        'maxlength' => 20,
                        'onKeyUp' => "FormatNumber(this.id,this.value, '', '', '');"
                    ],
                ])
                ->add('bunkerPlastic', TextType::class, [
                    'attr' => [
                        'size' => 8,
                        'maxlength' => 20,
                        'onKeyUp' => "FormatNumber(this.id,this.value, '', '', '');"
                    ],
                ])
                ->add('bunkerFuel', TextType::class, [
                    'attr' => [
                        'size' => 8,
                        'maxlength' => 20,
                        'onKeyUp' => "FormatNumber(this.id,this.value, '', '', '');"
                    ],
                ])
                ->add('bunkerFood', TextType::class, [
                    'attr' => [
                        'size' => 8,
                        'maxlength' => 20,
                        'onKeyUp' => "FormatNumber(this.id,this.value, '', '', '');"
                    ],
                ])
                ->add('send', SubmitType::class, [
                    'label' => 'Speichern'
                ])
                ->add('checker', SingleSubmitType::class)
                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $amounts = [];
                foreach (['bunkerMetal', 'bunkerCrystal', 'bunkerPlastic', 'bunkerFuel', 'bunkerFood'] as $field) {
                    $amounts[$field] = max(0, StringUtils::parseFormattedNumber((string) $data[$field]));
                }

                // scale all amounts down proportionally if together they exceed the bunker capacity
                $capacity = $resBunker->getBuilding()->calculateBunkerResources($resBunker->getCurrentLevel());
                $percent = $capacity > 0 ? max(array_sum($amounts) / $capacity, 1) : 1;
                if ($capacity <= 0) {
                    $amounts = array_map(fn () => 0, $amounts);
                }

                $this->planetRepository->updateBunker(
                    $cp,
                    (int) floor($amounts['bunkerMetal'] / $percent),
                    (int) floor($amounts['bunkerCrystal'] / $percent),
                    (int) floor($amounts['bunkerPlastic'] / $percent),
                    (int) floor($amounts['bunkerFuel'] / $percent),
                    (int) floor($amounts['bunkerFood'] / $percent)
                );

                $this->addFlash('success', $percent > 1 ? "Änderungen wurden übernommen! Die Mengen wurden auf die Kapazität des Bunkers gekürzt." : "Änderungen wurden übernommen!");

                return $this->redirectToRoute('game.bunker.res');
            }

            return $this->render('game/bunker/bunker_res.html.twig',[
                'resBunker' => $resBunker,
                'bunkered' => $cp->getBunkerMetal() + $cp->getBunkerCrystal() + $cp->getBunkerPlastic() + $cp->getBunkerFuel() + $cp->getBunkerFood(),
                'form' => $form,
                'msg' => $msg??null,
                'planet' => $cp
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Der Rohstoffbunker wurde noch nicht gebaut!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Bunker'
        ]);
    }

    #[Route('/game/bunker/bunker', name: 'game.bunker.bunker')]
    public function bunker(Request $request):Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $fleetBunker = $this->buildingListItemRepository->findOneBy(['user'=>$this->getUser()->getData(),'entity'=>$cp,'building'=>BuildingId::FLEET_BUNKER]);

        if ($fleetBunker) {
            $bunkered = $this->shipListRepository->getBunkered($this->getUser()->getData(), $cp);

            $form = $this->createFormBuilder(['bunkered'=>$bunkered])
                ->add('bunkered', CollectionType::class, [
                    'entry_type' => BunkerShipCountType::class,
                    'label' => false
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Ausbunkern'
                ])
                ->add('checker', SingleSubmitType::class)
                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $count = 0;
                foreach ($bunkered as $bunker) {
                    if ($bunker->getBunkered() > 0) {
                        $count = $this->shipListRepository->leaveBunker($bunker);
                    }
                }

                if ($count > 0) {
                    $msg['success'] = "Schiffe wurden ausgebunkert!";
                }
            }

            $structure = 0;
            $count = 0;

            foreach ($bunkered as $shipList) {
                $structure += $shipList->getBunkered() * $shipList->getShip()->getStructure();
                $count += $shipList->getBunkered();
            }

            return $this->render('game/bunker/bunker_bunker.html.twig',[
                'bunkered' => $bunkered,
                'msg' => $msg??null,
                'planet' => $cp,
                'form' => $form,
                'totalStructure' => $fleetBunker->getBuilding()->calculateBunkerFleetSpace($fleetBunker->getCurrentLevel()),
                'structure' => $structure,
                'totalCount' => $fleetBunker->getBuilding()->calculateBunkerFleetCount($fleetBunker->getCurrentLevel()),
                'count' => $count,
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Der Flottenbunker wurde noch nicht gebaut!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Bunker'
        ]);
    }

    #[Route('/game/bunker/fleet', name: 'game.bunker.fleet')]
    public function fleet(Request $request):Response
    {

        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $fleetBunker = $this->buildingListItemRepository->findOneBy(['user'=>$this->getUser()->getData(),'entity'=>$cp,'building'=>BuildingId::FLEET_BUNKER]);

        if ($fleetBunker) {
            $ships = $this->shipListRepository->getEntityShipCounts($this->getUser()->getData(), $cp);

            $form = $this->createFormBuilder(['ships'=>$ships])
                ->add('ships', CollectionType::class, [
                    'entry_type' => CountType::class,
                    'label' => false
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Einbunkern'
                ])
                ->add('checker', SingleSubmitType::class)
                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $count = $fleetBunker->getBuilding()->calculateBunkerFleetCount($fleetBunker->getCurrentLevel());
                $structure = $fleetBunker->getBuilding()->calculateBunkerFleetSpace($fleetBunker->getCurrentLevel());
                $counter = 0;

                $bunkeredShips = $this->shipListRepository->getBunkered($this->getUser()->getData(), $cp);

                foreach ($bunkeredShips as $ship) {
                    $count -= $ship->getCount();
                    $structure -= $ship->getCount() * $ship->getShip()->getStructure();
                }

                foreach ($form->get('ships')->all() as $shipListItem) {
                    $cnt = $shipListItem->get('count')->getData();
                    if ($cnt) {
                        $countBunker = min($count, $cnt);
                        $spaceBunker = $shipListItem->getData()->getShip()->getStructure() > 0 ? min($cnt, $structure / $shipListItem->getData()->getShip()->getStructure()) : $cnt;
                        $cnt = (int) floor(min($countBunker, $spaceBunker));
                        $cnt = $this->shipListRepository->bunker($shipListItem->getData(), $cnt);
                        $count -= $cnt;
                        $structure -= $cnt * $shipListItem->getData()->getShip()->getStructure();
                        $counter += $cnt;
                    }
                }

                if ($counter > 0) {
                    $msg['success'] = "Schiffe wurden eingebunkert!";
                }
                else {
                    $msg['error'] = "Schiffe konnten nicht eingebunkert werden, da kein Platz mehr vorhanden war!";
                }
            }

            return $this->render('game/bunker/bunker_fleet.html.twig',[
                'ships' => $ships,
                'msg' => $msg??null,
                'planet' => $cp,
                'form' => $form,
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Der Flottenbunker wurde noch nicht gebaut!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Bunker'
        ]);
    }
}