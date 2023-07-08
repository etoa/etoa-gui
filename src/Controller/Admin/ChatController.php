<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Chat\ChatBanRepository;
use EtoA\Chat\ChatLogRepository;
use EtoA\Chat\ChatManager;
use EtoA\Chat\ChatUserRepository;
use EtoA\Form\Type\Admin\ChatLogSearchType;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ChatController extends AbstractAdminController
{
    public function __construct(
        private readonly ChatLogRepository  $chatLogRepository,
        private readonly ChatUserRepository $chatUserRepository,
        private readonly ChatBanRepository  $chatBanRepository,
        private readonly ChatManager        $chatManager,
        private readonly UserRepository     $userRepository,
    )
    {
    }

    #[Route('/admin/chat/', name: 'admin.chat')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function view(): Response
    {
        return $this->render('admin/chat/chat.html.twig');
    }

    #[Route('/admin/chat/users/{id}/ban', name: 'admin.chat.ban')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function ban(int $id): RedirectResponse
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Spieler nicht gefunden');
        } else {
            $this->chatBanRepository->banUser($user->id, 'Banned by Admin');
            $this->chatUserRepository->kickUser($user->id, 'Bannend by Admin');
            $this->chatManager->sendSystemMessage($user->nick . " wurde gebannt!");

            $this->addFlash('success', $user->nick . " wurde gebannt!");
        }

        return $this->redirectToRoute('admin.chat');
    }

    #[Route('/admin/chat/users/{id}/kick', name: 'admin.chat.kick')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function kick(int $id): RedirectResponse
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Spieler nicht gefunden');
        } else {
            $this->chatUserRepository->kickUser($user->id, 'Bannend by Admin');
            $this->chatManager->sendSystemMessage($user->nick . " wurde gekickt!");

            $this->addFlash('success', $user->nick . " wurde gekickt!");
        }

        return $this->redirectToRoute('admin.chat');
    }

    #[Route('/admin/chat/users/{id}/delete', name: 'admin.chat.delete')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function delete(int $id): RedirectResponse
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Spieler nicht gefunden');
        } else {
            $this->chatUserRepository->deleteUser($user->id);

            $this->addFlash('success', $user->nick . " wurde aus dem Chat gelöscht!");
        }

        return $this->redirectToRoute('admin.chat');
    }

    #[Route('/admin/chat/users/{id}/unban', name: 'admin.chat.unban')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function unban(int $id): RedirectResponse
    {
        $user = $this->userRepository->getUser($id);
        if ($user === null) {
            $this->addFlash('error', 'Spieler nicht gefunden');
        } else {
            $this->chatBanRepository->deleteBan($user->id);

            $this->addFlash('success', "Ban für " . $user->nick . " wurde gelöscht!");
        }

        return $this->redirectToRoute('admin.chat');
    }

    #[Route('/admin/chat/log', name: 'admin.chat.log')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function log(Request $request): Response
    {
        return $this->render('admin/chat/log.html.twig', [
            'form' => $this->createForm(ChatLogSearchType::class, $request->query->all()),
            'total' => $this->chatLogRepository->count(),
        ]);
    }
}
