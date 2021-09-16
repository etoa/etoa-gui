<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\Chat\ChatBanRepository;
use EtoA\Chat\ChatManager;
use EtoA\Chat\ChatRepository;
use EtoA\Chat\ChatUserRepository;
use EtoA\Core\TokenContext;
use EtoA\Support\StringUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    private ChatManager $chatManager;
    private ChatBanRepository $chatBanRepository;
    private ChatUserRepository $chatUserRepository;
    private ChatRepository $chatRepository;

    public function __construct(ChatManager $chatManager, ChatBanRepository $chatBanRepository, ChatUserRepository $chatUserRepository, ChatRepository $chatRepository)
    {
        $this->chatManager = $chatManager;
        $this->chatBanRepository = $chatBanRepository;
        $this->chatUserRepository = $chatUserRepository;
        $this->chatRepository = $chatRepository;
    }

    /**
     * @Route("/api/chat/users", methods={"GET"}, name="api.chat.users")
     */
    public function users(TokenContext $context): JsonResponse
    {
        $user = $context->getCurrentUser();

        $users = [];
        if ($this->chatManager->isUserOnline($user->getId())) {
            $users = $this->chatManager->getUserOnlineList();
        }

        return new JsonResponse($users);
    }

    /**
     * @Route("/api/chat/poll", methods={"GET"}, name="api.chat.poll")
     */
    public function poll(TokenContext $context, Request $request): JsonResponse
    {
        $data = array();

        // Check user is logged in
        $user = $context->getCurrentUser();
        $ban = $this->chatBanRepository->getUserBan($user->getId());

        if ($ban !== null) {
            return new JsonResponse([
                'cmd' => 'bn',
                'msg' => StringUtils::replaceAsciiControlCharsUnicode($ban->reason),
            ]);
        }

        $chatUser = $this->chatUserRepository->getChatUser($user->getId());
        if ($chatUser !== null) {
            if ($chatUser->kick !== null) {
                $this->chatUserRepository->deleteUser($user->getId());
                return new JsonResponse([
                    'cmd' => 'ki',
                    'msg' => StringUtils::replaceAsciiControlCharsUnicode($chatUser->kick)
                ]);
            }
        } else {
            // User does not exist yet
            $this->chatManager->sendSystemMessage($user->getNick() . ' betritt den Chat.');
            $data['cmd'] = 'li';
            $data['msg'] = $this->chatManager->getWelcomeMessage($user->getNick());
        }

        // User exists, not kicked, not banned.
        $this->chatManager->updateUserEntry($user->getId(), $user->getNick());

        $messages = $this->chatRepository->getMessagesAfter($request->query->getInt('minId'), $request->query->getInt('chanId'));

        $lastId = $request->query->getInt('minId');
        // check whether 'login' has been set
        if (!isset($data['cmd'])) {
            $data['cmd'] = 'up';
        }

        $data['out'] = [];
        foreach ($messages as $message) {
            $data['out'][] = [
                'id' => $message->id,
                'text' => StringUtils::replaceAsciiControlChars(htmlspecialchars($message->text)),
                'time' => date("H:i", $message->timestamp),
                'color' => $message->color,
                'userId' => $message->userId,
                'nick' => $message->nick,
                'admin' => $message->admin
            ];
            $lastId = $message->id;
        }

        $data['lastId'] = $lastId;

        return new JsonResponse($data);
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
