<?php

namespace EtoA\Controller\Image;

use EtoA\Image\AllianceStatsImageGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AllianceStatsImageController extends AbstractImageController
{
    public function __construct(
        private readonly AllianceStatsImageGenerator $generator,
    )
    {
    }

    #[Route('/images/alliance/stats/{alliance}', name: 'images.alliance.stats')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function mapImage(int $alliance): Response
    {
        return self::createImageResponse(fn() => $this->generator->create($alliance));
    }

    #[Route('/admin/images/alliance/stats/{alliance}', name: 'admin.images.alliance.stats')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function adminMapImage(int $alliance): Response
    {
        return self::createImageResponse(fn() => $this->generator->create($alliance));
    }
}