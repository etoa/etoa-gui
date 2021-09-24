<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\Admin\LegacyTemplateTitleHelper;
use EtoA\Security\Admin\CurrentAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegacyAdminController extends AbstractController
{
    /**
     * @Route("/admin/", name="legacy.admin")
     */
    public function index(Request $request): Response
    {
        ob_start();

        /** @var CurrentAdmin $tokenUser */
        $tokenUser = $this->getUser();
        $adminUser = $tokenUser->getData();

        require_once __DIR__ . '/../../htdocs/admin/index.php';

        foreach (LegacyTemplateTitleHelper::$flashes as $message => $type) {
            $this->addFlash($type, $message);
        }

        return $this->render('admin/default.html.twig', [
            'title' => LegacyTemplateTitleHelper::$title,
            'subtitle' => LegacyTemplateTitleHelper::$subTitle,
            'content' => ob_get_clean(),
            'ajaxJs' => $xajax->getJavascript(),
        ]);
    }

    /**
     * @Route("/admin/dl/", name="legacy.admin.dl")
     */
    public function dl(): Response
    {
        ob_start();

        /** @var CurrentAdmin $tokenUser */
        $tokenUser = $this->getUser();
        $adminUser = $tokenUser->getData();

        require_once __DIR__ . '/../../htdocs/admin/dl.php';

        return new Response(ob_get_clean());
    }
}
