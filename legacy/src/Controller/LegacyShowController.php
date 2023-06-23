<?php declare(strict_types=1);

namespace EtoA\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegacyShowController extends AbstractController
{
    /**
     * @Route("/show/")
     */
    public function index(): Response
    {
        ob_start();

        require_once __DIR__ . '/../../htdocs/show.php';

        return new Response(ob_get_clean());
    }
}
