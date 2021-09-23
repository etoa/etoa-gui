<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Cell;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;
use SectorMapRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalaxyMapController extends AbstractController
{
    private UserRepository $userRepository;
    private ConfigurationService $config;
    private UserUniverseDiscoveryService $userUniverseDiscoveryService;
    private EntityRepository $entityRepository;

    public function __construct(UserRepository $userRepository, ConfigurationService $config, UserUniverseDiscoveryService $userUniverseDiscoveryService, EntityRepository $entityRepository)
    {
        $this->userRepository = $userRepository;
        $this->config = $config;
        $this->userUniverseDiscoveryService = $userUniverseDiscoveryService;
        $this->entityRepository = $entityRepository;
    }

    /**
     * @Route("/admin/galaxy/map", name="admin.galaxy.map")
     */
    public function view(Request $request): Response
    {
        $sx_num = $this->config->param1Int('num_of_sectors');
        $sy_num = $this->config->param2Int('num_of_sectors');
        $cx_num = $this->config->param1Int('num_of_cells');
        $cy_num = $this->config->param2Int('num_of_cells');

        $sectorMap = new SectorMapRenderer($cx_num, $cy_num);

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
        for ($sy = $sy_num; $sy > 0; $sy--) {
            for ($sx = 1; $sx <= $sx_num; $sx++) {
                $mapsectors[$sy][$sx] = $sectorMap->render($sx, $sy, $this->userUniverseDiscoveryService, $this->entityRepository);
            }
        }

        return $this->render('admin/galaxy/map.html.twig', [
            'mapSectors' => $mapsectors,
        ]);
    }
}