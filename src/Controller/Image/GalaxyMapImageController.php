<?php

namespace EtoA\Controller\Image;

use EtoA\Image\GalaxyMapImageGenerator;
use EtoA\Universe\GalaxyMap;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GalaxyMapImageController extends AbstractImageController
{
    const MAX_SIZE = 3000;

    public function __construct(
        private readonly GalaxyMapImageGenerator $generator,
        private readonly UserRepository $userRepository
    ) {
    }

    #[Route('/game/images/map', name: 'game.images.map')]
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
            user: $this->getUser()->getData()
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
            user: $this->userRepository->find($request->query->getInt('user')),
        ));
    }

    private function getSize(Request $request): int
    {
        return min(self::MAX_SIZE, $request->query->getInt('size', GalaxyMap::WIDTH));
    }
}
