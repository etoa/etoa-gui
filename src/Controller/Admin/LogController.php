<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EException;
use EtoA\Form\Type\Admin\LogDebrisType;
use EtoA\Form\Type\Admin\LogGeneralType;
use EtoA\Log\DebrisLogRepository;
use EtoA\Log\LogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogController extends AbstractAdminController
{
    public function __construct(
        private LogRepository $logRepository,
        private DebrisLogRepository $debrisLogRepository
    ) {
    }

    /**
     * @Route("/admin/logs/", name="admin.logs.general")
     */
    public function general(Request $request): Response
    {
        return $this->render('admin/logs/general.html.twig', [
            'form' => $this->createForm(LogGeneralType::class, $request->query->all())->createView(),
            'total' => $this->logRepository->count(),
        ]);
    }

    /**
     * @Route("/admin/logs/debris", name="admin.logs.debris")
     */
    public function debris(Request $request): Response
    {
        $data = array_merge($request->query->all(), ['date' => (new \DateTime())->format('Y-m-d\TH:i:s')]);

        return $this->render('admin/logs/debris.html.twig', [
            'form' => $this->createForm(LogDebrisType::class, $data)->createView(),
            'total' => $this->debrisLogRepository->count(),
        ]);
    }

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
