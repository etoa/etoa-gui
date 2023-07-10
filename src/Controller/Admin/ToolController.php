<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ToolController extends AbstractAdminController
{
    #[Route("/admin/tools/", name: "admin.tools.index")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function index(): Response
    {
        return $this->render('admin/tools/index.html.twig');
    }
}
