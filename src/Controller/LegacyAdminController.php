<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\Security\Admin\CurrentAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegacyAdminController extends AbstractController
{
    /**
     * @Route("/admin/", name="legacy.admin")
     */
    public function index(): Response
    {
        ob_start();

        /** @var CurrentAdmin $tokenUser */
        $tokenUser = $this->getUser();
        $adminUser = $tokenUser->getData();

        require_once __DIR__ . '/../../htdocs/admin/index.php';

        return new Response(ob_get_clean());
    }
}
