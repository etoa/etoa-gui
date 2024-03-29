<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EException;
use EtoA\Form\Type\Admin\LogAttackBanType;
use EtoA\Form\Type\Admin\LogDebrisType;
use EtoA\Form\Type\Admin\LogFleetType;
use EtoA\Form\Type\Admin\LogGameType;
use EtoA\Form\Type\Admin\LogGeneralType;
use EtoA\Log\DebrisLogRepository;
use EtoA\Log\FleetLogRepository;
use EtoA\Log\GameLogRepository;
use EtoA\Log\LogRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogController extends AbstractAdminController
{
    public function __construct(
        private LogRepository $logRepository,
        private DebrisLogRepository $debrisLogRepository,
        private FleetLogRepository $fleetLogRepository,
        private GameLogRepository $gameLogRepository
    ) {
    }

    #[Route("/admin/logs/", name: "admin.logs.general")]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function general(Request $request): Response
    {
        return $this->render('admin/logs/general.html.twig', [
            'form' => $this->createForm(LogGeneralType::class, $request->query->all()),
            'total' => $this->logRepository->count(),
        ]);
    }

    #[Route("/admin/logs/debris", name: "admin.logs.debris")]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function debris(Request $request): Response
    {
        $data = array_merge($request->query->all(), ['date' => (new \DateTime())->getTimestamp()]);

        return $this->render('admin/logs/debris.html.twig', [
            'form' => $this->createForm(LogDebrisType::class, $data),
            'total' => $this->debrisLogRepository->count(),
        ]);
    }

    #[Route("/admin/logs/attack-ban", name: "admin.logs.attack-ban")]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function attackBan(Request $request): Response
    {
        $data = array_merge($request->query->all(), ['date' => (new \DateTime())->getTimestamp()]);

        return $this->render('admin/logs/attack_ban.html.twig', [
            'form' => $this->createForm(LogAttackBanType::class, $data),
        ]);
    }

    #[Route("/admin/logs/fleets", name: "admin.logs.fleets")]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function fleets(Request $request): Response
    {
        return $this->render('admin/logs/fleets.html.twig', [
            'form' => $this->createForm(LogFleetType::class, $request->query->all()),
            'total' => $this->fleetLogRepository->count(),
        ]);
    }

    #[Route("/admin/logs/game", name: "admin.logs.game")]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function game(Request $request): Response
    {
        return $this->render('admin/logs/game.html.twig', [
            'form' => $this->createForm(LogGameType::class, $request->query->all()),
            'total' => $this->gameLogRepository->count(),
        ]);
    }

    #[Route("/admin/logs/error", name: "admin.logs.error")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
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
