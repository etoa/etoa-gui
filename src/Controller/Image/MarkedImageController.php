<?php

namespace EtoA\Controller\Image;

use EtoA\Image\MarketImageGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MarkedImageController extends AbstractImageController
{
    public function __construct(
        private readonly MarketImageGenerator $generator,
    )
    {
    }

    #[Route('/images/market', name: 'images.market')]
    public function mapImage(): Response
    {
        return self::createImageResponse(fn() => $this->generator->create());
    }
}