<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelpController extends AbstractAdminController
{
    #[Route('/admin/help/techtree', name: 'admin.help.techtree')]
    public function techTree(): Response
    {
        return $this->render('admin/help/techtree.html.twig');
    }
}
