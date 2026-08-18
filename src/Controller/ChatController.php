<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\Chat\ChatBanRepository;
use EtoA\Chat\ChatLogRepository;
use EtoA\Chat\ChatManager;
use EtoA\Chat\ChatRepository;
use EtoA\Chat\ChatUserRepository;
use EtoA\Core\TokenContext;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{

    public function __construct(
        private readonly ChatManager        $chatManager,
        private readonly ChatBanRepository  $chatBanRepository,
        private readonly ChatUserRepository $chatUserRepository,
        private readonly ChatRepository     $chatRepository
    )
    {
    }


    #[Route("/api/chat/users", name: "api.chat.users", methods: "GET")]
    public function users(TokenContext $context): JsonResponse
    {
        $user = $context->getCurrentUser();

        $users = [];
        if ($this->chatManager->isUserOnline($user->getId())) {
            $users = $this->chatManager->getUserOnlineList();
        }

        return new JsonResponse($users);
    }


    #[Route("/api/chat/poll", name: "api.chat.poll", methods: "GET")]
    public function poll(TokenContext $context, Request $request): JsonResponse
    {
        $data = array();

        // Check user is logged in
        $user = $context->getCurrentUser();
        $ban = $this->chatBanRepository->getUserBan($user->getId());

        if ($ban !== null) {
            return new JsonResponse([
                'cmd' => 'bn',
                'msg' => StringUtils::replaceAsciiControlCharsUnicode($ban->getReason()),
            ]);
        }

        $chatUser = $this->chatUserRepository->getChatUser($user->getId());
        if ($chatUser) {
            if ($chatUser->getKick()) {
                $this->chatUserRepository->deleteUser($user);

                return new JsonResponse([
                    'cmd' => 'ki',
                    'msg' => StringUtils::replaceAsciiControlCharsUnicode($chatUser->getKick()),
                ]);
            }
        } else {
            // User does not exist yet
            $this->chatManager->sendSystemMessage($user->getNick() . ' betritt den Chat.');
            $data['cmd'] = 'li';
            $data['msg'] = $this->chatManager->getWelcomeMessage($user->getNick());
        }

        // User exists, not kicked, not banned.
        $this->chatManager->updateUserEntry($user);

        $messages = $this->chatRepository->getMessagesAfter($request->query->getInt('minId'), $request->query->getInt('chanId'));

        $lastId = $request->query->getInt('minId');
        // check whether 'login' has been set
        if (!isset($data['cmd'])) {
            $data['cmd'] = 'up';
        }

        $data['out'] = [];
        foreach ($messages as $message) {
            $data['out'][] = [
                'id' => $message->getId(),
                'text' => StringUtils::replaceAsciiControlChars(htmlspecialchars($message->getText())),
                'time' => date("H:i", $message->getTimestamp()),
                'color' => $message->getColor(),
                'userId' => $message->getUser()?->getId(),
                'nick' => $message->getNick(),
                'admin' => $message->getAdmin(),
            ];
            $lastId = $message->getId();
        }

        $data['lastId'] = $lastId;

        return new JsonResponse($data);
    }

    #[Route("/api/chat/open", name: "api.chat.open", methods: "POST")]
    public function open(Request $request): JsonResponse
    {
        $request->getSession()->set('chat',true);
        return new JsonResponse();
    }
}
