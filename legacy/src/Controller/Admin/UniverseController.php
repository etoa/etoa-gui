<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Form\Type\Admin\AddStarsType;
use EtoA\Form\Type\Admin\UniverseBigBangType;
use EtoA\Universe\Asteroid\AsteroidRepository;
use EtoA\Universe\BigBangConfiguration;
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
        private ConfigurationService $config,
    ) {
    }

    #[Route("/admin/universe/edit", name: "admin.universe.edit")]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function universe(Request $request): Response
    {
        if ($this->cellRepository->count() === 0) {
            return $this->redirectToRoute('admin.universe.big-bang.configure');
        }

        $addStarsForm = $this->createForm(AddStarsType::class);
        $addStarsForm->handleRequest($request);
        if ($addStarsForm->isSubmitted() && $addStarsForm->isValid()) {
            $n = max(0, $addStarsForm->getData()['count']);
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

    #[Route("/admin/universe/big-bang/configure", name: "admin.universe.big-bang.configure")]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function bigBangConfigure(Request $request): Response
    {
        $bigBangConfig = BigBangConfiguration::createFromConfig($this->config);
        $form = $this->createForm(UniverseBigBangType::class, $bigBangConfig);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bigBangConfig->updateConfig($this->config);

            $this->addFlash('success', 'Konfiguration aktualisiert!');

            return $this->redirectToRoute('admin.universe.big-bang');
        }

        return $this->render('admin/universe/big-bang-configure.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/admin/universe/big-bang", name: "admin.universe.big-bang")]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function bigBang(Request $request): Response
    {
        $xDimensions = $this->config->param1Int('num_of_sectors') * $this->config->param1Int('num_of_cells');
        $yDimensions = $this->config->param2Int('num_of_sectors') * $this->config->param2Int('num_of_cells');
        $layouts = [];
        $dir = realpath(__DIR__ ."/../../../htdocs/images/galaxylayouts/");
        $d = opendir($dir);
        while ($file = readdir($d)) {
            if (is_file($dir . DIRECTORY_SEPARATOR . $file) && substr($file, strrpos($file, ".png")) == ".png" && $ims = getimagesize($dir . DIRECTORY_SEPARATOR . $file)) {
                if ($ims[0] == $xDimensions && $ims[1] == $yDimensions) {
                    $layouts[] = basename($file);
                }
            }
        }

        if ($request->isMethod('post')) {
            $output = $this->universeGenerator->create(
                $request->request->get('map_image'),
                $request->request->getInt('map_precision')
            );

            return $this->render('admin/universe/big-bang-result.html.twig', [
                'output' => $output,
            ]);
        }

        return $this->render('admin/universe/big-bang.html.twig', [
            'xDimensions' => $xDimensions,
            'yDimensions' => $yDimensions,
            'layouts' => $layouts,
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
