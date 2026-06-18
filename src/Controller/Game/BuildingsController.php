<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingService;
use EtoA\Entity\Building;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\Routing\Annotation\Route;

class BuildingsController extends AbstractGameController
{
    public function __construct(
        private readonly BuildingService $buildingService,
        private readonly PlanetRepository $planetRepository
    )
    {
    }

    #[Route('/game/buildings', name: 'game.buildings')]
    public function list(Request $request): Response
    {
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));

        //only allow own planets
        if ($planet->getUser() !== $this->getUser()->getData()) {
            return $this->redirectToRoute('game.overview');
        }

        $buildingData = $this->buildingService->getBuildingsData();

        return $this->render('game/buildings/list.html.twig', [
            'planet' => $planet,
            'buildingData' => $buildingData,
        ]);
    }

    #[Route('/game/buildings/{id}', name: 'game.buildings.show')]
    public function show(?Building $building, Request $request): Response
    {
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));

        //check building and only allow own planets
        if (!$building || !$building->isShow() || $planet->getUser() !== $this->getUser()->getData()) {
            return $this->redirectToRoute('game.buildings');
        }

        $form = $this->container->get('form.factory')->createNamed('building_form',FormType::class)
            ->add('build', SubmitType::class)
            ->add('demolish', SubmitType::class, [
                'label' => 'Abreisen',
                'attr' => [
                    'onclick' => "if (this.value=='Abreisen'){return confirm('Wirklich abbrechen?');}"
                ]
            ])
            ->add('cancelBuild', SubmitType::class, [
                'label' => 'Bau abbrechen',
                'attr' => [
                    'onclick' => "if (this.value=='Bau abbrechen'){return confirm('Wirklich abbrechen?');}"
                ]
            ])
            ->add('cancelDemolish', SubmitType::class, [
                'label' => 'Abriss abbrechen',
                'attr' => [
                    'onclick' => "if (this.value=='Abriss abbrechen'){return confirm('Wirklich abbrechen?');}"
                ]
            ])
            ->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('build')->isClicked()) {
                if ($this->buildingService->build($building)) {
                    $this->addFlash('success','Bauauftrag wurde erfolgreich gestartet!');
                } else {
                    $this->addFlash('error','Gebäude nicht baubar!');
                }
            }

            if($form->get('cancelBuild')->isClicked()) {
                if ($this->buildingService->cancelBuild($building->bl)) {
                    $this->addFlash('success','Bauauftrag wurde erfolgreich abgebrochen!');
                } else {
                    $this->addFlash('error','Bauauftrag kann nicht mehr abgebrochen werden, die Arbeit ist bereits fertiggestellt!');
                }
            }

            if($form->get('demolish')->isClicked()) {
                if ($this->buildingService->demolish($building->bl)) {
                    $this->addFlash('success','Abbruchauftrag wurde erfolgreich gestartet!');
                } else {
                    $this->addFlash('error','Gebäude nicht abreissbar!');
                }
            }

            if($form->get('cancelDemolish')->isClicked()) {
                if ($this->buildingService->cancelDemolish($building->bl)) {
                    $this->addFlash('success','Abbruchauftrag wurde erfolgreich abgebrochen!');
                } else {
                    $this->addFlash('error','Abbruchauftrag kann nicht mehr abgebrochen werden, die Arbeit ist bereits fertiggestellt!');
                }
            }
        }

        $buildingData = $this->buildingService->getBuildingData($building);

        return $this->render('game/buildings/show.html.twig', [
            'planet' => $planet,
            'building' => $building,
            'buildingData' => $buildingData,
            'form' => $form
        ]);
    }
}