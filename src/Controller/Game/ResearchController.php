<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Entity\BuildingListItem;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResearchController extends AbstractGameController
{
    public function __construct(
        private readonly PlanetRepository $planetRepository,
        private readonly  BuildingListItemRepository $buildingListItemRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository
    )
    {
    }

    #[Route('/game/research', name: 'game.research')]
    public function list(Request $request): Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $researchBuilding = $this->buildingListItemRepository->findOneBy(['entity'=>$cp, 'building'=>BuildingId::TECHNOLOGY]);

        if($researchBuilding) {
            return $this->render('game/research/research.html.twig',[
                'planet' => $cp,
                'researchBuilding' => $researchBuilding,
                'genBuilding' => $this->buildingListItemRepository->findOneBy(['entity'=>$cp, 'building'=>BuildingId::PEOPLE])??new BuildingListItem(),
                'genTechLevel' => $this->technologyListItemRepository->findOneBy(['user'=>$this->getUser()->getData(),'technology'=>TechnologyId::GEN])?->getCurrentLevel()
             ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Das Forschungslabor wurde noch nicht gebaut!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Forschungslabor des Planeten '. $cp->getName()
        ]);
    }
}