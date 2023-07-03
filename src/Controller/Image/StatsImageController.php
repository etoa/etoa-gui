<?php

namespace EtoA\Controller\Image;

use EtoA\Image\StatsImageGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StatsImageController extends AbstractImageController
{
    public function __construct(
        private readonly StatsImageGenerator $generator,
    )
    {
    }

    #[Route('/images/stats/{user}', name: 'images.stats')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function mapImage(int $user, Request $request): Response
    {
        return $this->create($request, $user);
    }

    #[Route('/admin/images/stats/{user}', name: 'admin.images.stats')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function adminMapImage(int $user, Request $request): Response
    {
        return $this->create($request, $user);
    }

    private function create(Request $request, int $user): Response
    {
        $width = $request->query->getInt('width', 600);
        
        $startVal = $request->query->get('start');
        $start = $startVal !== null ? (is_numeric($startVal) ? intval($startVal) : strtotime($startVal)) : null;
        $endVal = $request->query->get('end');
        $end = $endVal !== null ? (is_numeric($endVal) ? intval($endVal) : strtotime($endVal)) : null;

        return self::createImageResponse(fn() => $this->generator->create($user, width: $width, start: $start, end: $end));
    }
}