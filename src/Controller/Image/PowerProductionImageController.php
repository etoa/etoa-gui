<?php

namespace EtoA\Controller\Image;

use EtoA\Image\PowerProductionImageGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PowerProductionImageController extends AbstractImageController
{
    public function __construct(
        private readonly PowerProductionImageGenerator $generator,
    )
    {
    }

    #[Route('/images/powerProduction', name: 'images.power_production')]
    public function mapImage(): Response
    {
        return self::createImageResponse(fn() => $this->generator->create());
    }
}