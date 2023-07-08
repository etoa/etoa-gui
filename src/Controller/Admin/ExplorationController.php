<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Universe\Cell\CellRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ExplorationController extends AbstractAdminController
{
    public function __construct(
        private readonly UserRepository               $userRepository,
        private readonly CellRepository               $cellRepository,
        private readonly UserUniverseDiscoveryService $userUniverseDiscoveryService
    )
    {
    }

    #[Route("/admin/universe/exploration/", name: "admin.universe.exploration")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function overview(): Response
    {
        $users = $this->userRepository->searchUserNicknames();
        if (count($users) === 0) {
            $this->addFlash('error', 'Keine Benutzer vorhanden!');
        }

        return $this->render('admin/universe/exploration.html.twig', [
            'users' => $users,
            'user' => null,
        ]);
    }

    #[Route("/admin/universe/exploration/user", name: "admin.universe.exploration.user")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function user(Request $request): Response
    {
        $userId = $request->query->getInt('userId');
        if ($userId === 0) {
            return $this->redirectToRoute('admin.universe.exploration');
        }

        $user = $this->userRepository->getUser($userId);

        $sx = 1;
        $sy = 1;
        $cx = 1;
        $cy = 1;
        $radius = 1;

        // Discover selected cell
        if ($request->request->has('discover_selected')) {
            $sx = $request->request->getInt('sx');
            $sy = $request->request->getInt('sy');
            $cx = $request->request->getInt('cx');
            $cy = $request->request->getInt('cy');
            $radius = abs($request->request->getInt('radius'));

            $cell = $this->cellRepository->getCellIdByCoordinates($sx, $sy, $cx, $cy);
            if ($cell !== null) {
                $this->userUniverseDiscoveryService->setDiscovered($user, $cell, $radius);
                $this->addFlash('success', 'Koordinaten erkundet!');
            } else {
                $this->addFlash('error', 'Ungültige Koordinate!');
            }
        } // Reset discovered coordinates
        elseif ($request->request->has('discover_reset')) {
            $this->userUniverseDiscoveryService->setDiscoveredAll($user, false);
            $this->addFlash('success', 'Erkundung zurückgesetzt!');
        } // Discover all coordinates
        elseif ($request->request->has('discover_all')) {
            $this->userUniverseDiscoveryService->setDiscoveredAll($user, true);
            $this->addFlash('success', 'Alles erkundet!');
        }

        return $this->render('admin/universe/exploration.html.twig', [
            'users' => $this->userRepository->searchUserNicknames(),
            'user' => $user,
            'discoveredPercent' => $this->userUniverseDiscoveryService->getDiscoveredPercent($user),
            'sx' => $sx,
            'sy' => $sy,
            'cx' => $cx,
            'cy' => $cy,
            'radius' => $radius,
        ]);
    }
}
