<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Entity\BuildingListItem;
use EtoA\Entity\Technology;
use EtoA\Entity\TechnologyListItem;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Technology\TechnologyService;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResearchController extends AbstractGameController
{
    public function __construct(
        private readonly PlanetRepository $planetRepository,
        private readonly  BuildingListItemRepository $buildingListItemRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly TechnologyService $technologyService
    )
    {
    }

    #[Route('/game/research', name: 'game.research')]
    public function list(Request $request): Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $researchBuilding = $this->buildingListItemRepository->findOneBy(['entity'=>$cp, 'building'=>BuildingId::TECHNOLOGY]);

        if($researchBuilding) {
            return $this->render('game/research/list.html.twig',[
                'planet' => $cp,
                'researchBuilding' => $researchBuilding,
                'genBuilding' => $this->buildingListItemRepository->findOneBy(['entity'=>$cp, 'building'=>BuildingId::PEOPLE])??new BuildingListItem(),
                'genTechLevel' => $this->technologyListItemRepository->findOneBy(['user'=>$this->getUser()->getData(),'technology'=>TechnologyId::GEN])?->getCurrentLevel(),
                'render' => $this->technologyService->renderResearch()
             ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Das Forschungslabor wurde noch nicht gebaut!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Forschungslabor des Planeten '. $cp->getName()
        ]);
    }

    #[Route('/game/research/{id}', name: 'game.research.detail')]
    public function detail(Request $request, ?Technology $technology): Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));

        if($technology && $technology->isShow()) {
            $researchBuilding = $this->buildingListItemRepository->findOneBy(['entity'=>$cp, 'building'=>BuildingId::TECHNOLOGY]);
            $technologyListItem = $this->technologyListItemRepository->findOneBy(['user'=>$this->getUser()->getData(),'technology'=>$technology])??new TechnologyListItem();

            if($researchBuilding) {
                return $this->render('game/research/detail.html.twig',[
                    'planet' => $cp,
                    'researchBuilding' => $researchBuilding,
                    'genBuilding' => $this->buildingListItemRepository->findOneBy(['entity'=>$cp, 'building'=>BuildingId::PEOPLE])??new BuildingListItem(),
                    'genTechLevel' => $this->technologyListItemRepository->findOneBy(['user'=>$this->getUser()->getData(),'technology'=>TechnologyId::GEN])?->getCurrentLevel(),
                    'technology' => $technologyListItem
                ]);
            }
            else {
                return $this->render('game/error.html.twig',[
                    'msg' => 'Das Forschungslabor wurde noch nicht gebaut!',
                    'path' => $this->generateUrl('game.overview'),
                    'headline' => 'Forschungslabor des Planeten '. $cp->getName()
                ]);
            }
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Technik nich vorhanden!',
            'path' => $this->generateUrl('game.research'),
            'headline' => 'Forschungslabor des Planeten '. $cp->getName()
        ]);
    }
}