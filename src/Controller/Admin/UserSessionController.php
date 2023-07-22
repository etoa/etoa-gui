<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Form\Type\Admin\UserSessionLogType;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionManager;
use EtoA\User\UserSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserSessionController extends AbstractController
{
    public function __construct(
        private readonly UserSessionRepository $userSessionRepository,
        private readonly UserRepository        $userRepository,
        private readonly ConfigurationService  $config,
        private readonly UserSessionManager    $userSessionManager,
    )
    {
    }

    #[Route('/admin/users/sessions', name: 'admin.users.sessions', priority: 10)]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function sessions(): Response
    {
        return $this->render('admin/user/sessions.html.twig', [
            'userNicks' => $this->userRepository->searchUserNicknames(),
            'sessions' => $this->userSessionRepository->getSessions(),
            'timeout' => $this->config->getInt('user_timeout'),
            'time' => time(),
        ]);
    }

    #[Route('/admin/users/sessions/{id}/kick', name: 'admin.users.sessions.kick')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function kick(string $id): RedirectResponse
    {
        $this->userSessionManager->kick($id);
        $this->addFlash('success', sprintf("Session %s gelöscht", $id));

        return $this->redirectToRoute('admin.users.sessions');
    }

    #[Route('/admin/users/sessions/kick/all', name: 'admin.users.sessions.kick-all')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function kickAll(): RedirectResponse
    {
        $sessionIds = $this->userSessionRepository->getUserSessionIds();
        foreach ($sessionIds as $sessionId) {
            $this->userSessionManager->kick($sessionId);
        }

        $this->addFlash('success', "Alle Sessions gelöscht!");

        return $this->redirectToRoute('admin.users.sessions');
    }

    #[Route('/admin/users/session-log', name: 'admin.users.session-log', priority: 10)]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function sessionLog(Request $request): Response
    {
        return $this->render('admin/user/session-log.html.twig', [
            'form' => $this->createForm(UserSessionLogType::class, $request->query->all()),
            'total' => $this->userSessionRepository->countLogs(),
        ]);
    }
}
