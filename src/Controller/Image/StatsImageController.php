<?php

namespace EtoA\Controller\Image;

use EtoA\Image\StatsImageGenerator;
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
    public function mapImage(int $user): Response
    {
        return self::createImageResponse(fn() => $this->generator->create($user));
    }

    #[Route('/admin/images/stats/{user}', name: 'admin.images.stats')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function adminMapImage(int $user): Response
    {
        return self::createImageResponse(fn() => $this->generator->create($user));
    }
}