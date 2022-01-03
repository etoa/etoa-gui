<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Form\Type\Admin\AddStarsType;
use EtoA\Universe\Asteroid\AsteroidRepository;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\EmptySpace\EmptySpaceRepository;
use EtoA\Universe\GalaxyChecker;
use EtoA\Universe\Nebula\NebulaRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Star\StarRepository;
use EtoA\Universe\UniverseGenerator;
use EtoA\Universe\UniverseResetService;
use EtoA\Universe\Wormhole\WormholeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UniverseController extends AbstractAdminController
{
    public function __construct(
        private UniverseGenerator $universeGenerator,
        private UniverseResetService $universeResetService,
        private GalaxyChecker $galaxyChecker,
        private CellRepository $cellRepository,
        private StarRepository $starRepository,
        private PlanetRepository $planetRepository,
        private AsteroidRepository $asteroidRepository,
        private NebulaRepository $nebulaRepository,
        private WormholeRepository $wormholeRepository,
        private EmptySpaceRepository $emptySpaceRepository,
    ) {
    }

    #[Route("/admin/universe/edit", name: "admin.universe.edit")]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function universe(Request $request): Response
    {
        if ($this->cellRepository->count() === 0) {
            return $this->redirectToRoute($route);
        }

        $addStarsForm = $this->createForm(AddStarsType::class);
        $addStarsForm->handleRequest($request);
        if ($addStarsForm->isSubmitted() && $addStarsForm->isValid()) {
            $n = max(0, $request->request->getInt('number_of_stars'));
            $this->universeGenerator->addStarSystems($n);
            $this->addFlash('success', $n . ' Sternensysteme wurden hinzugefügt!');
        }

        return $this->render('admin/universe/universe.html.twig', [
            'sectorDimensions' => $this->cellRepository->getSectorDimensions(),
            'cellDimensions' => $this->cellRepository->getCellDimensions(),
            'starCount' => $this->starRepository->count(),
            'planetCount' => $this->planetRepository->count(),
            'asteroidCount' => $this->asteroidRepository->count(),
            'nebulaCount' => $this->nebulaRepository->count(),
            'wormholeCount' => $this->wormholeRepository->count(),
            'emptySpaceCount' => $this->emptySpaceRepository->count(),
            'addStarsForm' => $addStarsForm->createView(),
        ]);
    }

    #[Route("/admin/universe/reset/full", name: "admin.universe.reset.full")]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function resetRound(Request $request): Response
    {
        if ($request->isMethod('post')) {
            $this->universeResetService->reset();
            $this->addFlash('success', 'Die Runde wurde zurückgesetzt!');

            return $this->redirectToRoute('admin.universe.edit');
        }

        return $this->render('admin/universe/reset-full.html.twig');
    }

    #[Route("/admin/universe/reset/universe", name: "admin.universe.reset.universe")]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function resetUniverse(Request $request): Response
    {
        if ($request->isMethod('post')) {
            $this->universeResetService->reset(false);
            $this->addFlash('success', 'Das Universum wurde zurückgesetzt!');

            return $this->redirectToRoute('admin.universe.edit');
        }

        return $this->render('admin/universe/reset.html.twig', [
            'planetWithUserCount' => $this->planetRepository->countWithUser(),
        ]);
    }

    #[Route("/admin/universe/check", name: "admin.universe.check")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function check(): Response
    {
        return $this->render('admin/universe/check.html.twig', [
            'planetsWithInvalidUserId' => $this->galaxyChecker->planetsWithInvalidUserId(),
            'mainPlanetsWithoutUsers' => $this->galaxyChecker->mainPlanetsWithoutUsers(),
            'usersWithInvalidNumberOfMainPlanets' => $this->galaxyChecker->usersWithInvalidNumberOfMainPlanets(),
            'invalidEntities' => $this->galaxyChecker->invalidEntities(),
        ]);
    }
}
