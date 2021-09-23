<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogController extends AbstractController
{
    /**
     * @Route("/admin/logs/error", name="admin.logs.error")
     */
    public function error(Request $request): Response
    {
        if ($request->request->has('purgelog_submit')) {
            file_put_contents(EException::LOG_FILE, '');

            return $this->redirectToRoute('admin.logs.error');
        }

        $logFile = null;
        if (is_file(EException::LOG_FILE)) {
            $logFile = file_get_contents(EException::LOG_FILE);
        }

        return $this->render('admin/logs/errorlog.html.twig', [
            'logFile' => $logFile,
        ]);
    }
}
