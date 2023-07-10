<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Cell;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\SectorMapRenderer;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GalaxyMapController extends AbstractAdminController
{
    public function __construct(
        private readonly UserRepository               $userRepository,
        private readonly ConfigurationService         $config,
        private readonly UserUniverseDiscoveryService $userUniverseDiscoveryService,
        private readonly EntityRepository             $entityRepository
    )
    {
    }

    #[Route("/admin/universe/map", name: "admin.universe.map")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function view(Request $request): Response
    {
        $sx_num = $this->config->param1Int('num_of_sectors');
        $sy_num = $this->config->param2Int('num_of_sectors');
        $cx_num = $this->config->param1Int('num_of_cells');
        $cy_num = $this->config->param2Int('num_of_cells');

        $sectorMap = new SectorMapRenderer($cx_num, $cy_num);
        $sectorMap->setCellUrl("/admin/?page=galaxy&cell_id=");

        // Selected cell
        if ($request->query->has('cell')) {
            $cell = new Cell($request->query->getInt('cell'));
            if ($cell->isValid()) {
                $sectorMap->setSelectedCell($cell);
            }
        }

        // View map as user
        if ($request->query->has('user')) {
            $impersonatedUser = $this->userRepository->getUser($request->query->getInt('user'));
            $sectorMap->setImpersonatedUser($impersonatedUser);
        }

        // Draw map
        $mapsectors = array();

        try {
            for ($sy = $sy_num; $sy > 0; $sy--) {
                for ($sx = 1; $sx <= $sx_num; $sx++) {
                    $mapsectors[$sy][$sx] = $sectorMap->render($sx, $sy, $this->userUniverseDiscoveryService, $this->entityRepository);
                }
            }
        } catch (\RuntimeException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('admin/universe/map.html.twig', [
            'mapSectors' => $mapsectors,
        ]);
    }
}
