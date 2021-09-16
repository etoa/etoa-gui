<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\Chat\ChatManager;
use EtoA\Core\TokenContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    private ChatManager $chatManager;

    public function __construct(ChatManager $chatManager)
    {
        $this->chatManager = $chatManager;
    }

    /**
     * @Route("/api/chat/logout", methods={"GET"}, name="api.chat.logout")
     */
    public function logout(TokenContext $context): JsonResponse
    {
        $user = $context->getCurrentUser();

        $this->chatManager->logoutUser($user->getId());
        $this->chatManager->sendSystemMessage($user->getNick() . ' verlässt den Chat.');

        return new JsonResponse();
    }
}
