<?php

namespace EtoA\Controller\Image;

use EtoA\Entity\User;
use EtoA\Image\StatsImageGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StatsImageController extends AbstractImageController
{
    const MAX_SIZE = 3000;
    const DEFAULT_SIZE = 600;

    public function __construct(
        private readonly StatsImageGenerator $generator,
    )
    {
    }

    #[Route('game/images/stats/{id}', name: 'images.stats')]
    public function mapImage(User $user, Request $request): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return self::createImageResponse(fn () => $this->generator->createEmptyMessage(
                size: $this->getSize($request),
                message: "Nicht eingeloggt!"
            ));
        }

        return $this->create($request, $user);
    }

    #[Route('/admin/images/stats/{user}', name: 'admin.images.stats')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function adminMapImage(User $user, Request $request): Response
    {
        return $this->create($request, $user);
    }

    private function create(Request $request, User $user): Response
    {
        $width = $this->getSize($request);
        
        $startVal = $request->query->get('start');
        $start = $startVal !== null ? (is_numeric($startVal) ? intval($startVal) : strtotime($startVal)) : null;
        $endVal = $request->query->get('end');
        $end = $endVal !== null ? (is_numeric($endVal) ? intval($endVal) : strtotime($endVal)) : null;

        return self::createImageResponse(fn() => $this->generator->create($user, width: $width, start: $start, end: $end));
    }

    private function getSize(Request $request): int
    {
        return min(self::MAX_SIZE, $request->query->getInt('width', self::DEFAULT_SIZE));
    }
}