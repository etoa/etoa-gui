<?php

namespace EtoA\Controller\Image;

use EtoA\Image\ProgressImageGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgressImageController extends AbstractImageController
{
    public function __construct(
        private readonly ProgressImageGenerator $generator,
    )
    {
    }

    #[Route('/images/progress/{value}', name: 'images.progress')]
    public function mapImage(Request $request, int $value): Response
    {
        return self::createImageResponse(fn() => $this->generator->create(
            value: $value,
            width: $request->query->getInt('w', 400),
            height: $request->query->getInt('h', 20),
            reverse: $request->query->has('r'),
        ));
    }
}