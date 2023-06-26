<?php

namespace EtoA\Controller\Image;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class AbstractImageController extends AbstractController
{
    public static function createImageResponse(callable $callback): Response
    {
        return new StreamedResponse($callback, Response::HTTP_OK, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-cache, must-revalidate', // HTTP/1.1
            'Expires' => 'Mon, 26 Jul 1997 05:00:00 GMT', // Datum in der Vergangenheit
        ]);
    }
}