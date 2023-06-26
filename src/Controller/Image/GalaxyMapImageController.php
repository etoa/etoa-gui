<?php

namespace EtoA\Controller\Image;

use EtoA\Image\GalaxyMapImageGenerator;
use EtoA\Universe\GalaxyMap;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GalaxyMapImageController extends AbstractImageController
{
    const MAX_SIZE = 3000;

    public function __construct(
        private readonly GalaxyMapImageGenerator $generator,
    ) {
    }

    #[Route('/images/map', name: 'images.map')]
    public function mapImage(Request $request): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return self::createImageResponse(fn () => $this->generator->createEmptyMessage(
                size: $this->getSize($request),
                message: "Nicht eingeloggt!"
            ));
        }

        return self::createImageResponse(fn () => $this->generator->createMap(
            type: $request->query->getString('type', 'default'),
            size: $this->getSize($request),
            showLegend: $request->query->has('legend'),
            userId: 0, // TODO  $this->getUser()->getUserIdentifier() ...
        ));
    }

    #[Route('/admin/images/map', name: 'admin.images.map')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function adminMapImage(Request $request): Response
    {
        return self::createImageResponse(fn () => $this->generator->createMap(
            type: $request->query->getString('type', 'default'),
            size: $this->getSize($request),
            showLegend: $request->query->has('legend'),
            showAll: true,
            userId: $request->query->getInt('user'),
        ));
    }

    private function getSize(Request $request): int
    {
        return min(self::MAX_SIZE, $request->query->getInt('size', GalaxyMap::WIDTH));
    }
}
