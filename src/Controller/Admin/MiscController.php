<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MiscController extends AbstractAdminController
{
    #[Route("/admin/misc/", name: "admin.misc.index")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function index(): Response
    {
        return $this->render('admin/default.html.twig', [
            'title' => 'Diverses',
            'content' => 'Wähle eine Unterseite aus dem Menü!',
        ]);
    }
}
