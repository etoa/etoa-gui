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
    private ChatManager $chatManager;
    private ChatBanRepository $chatBanRepository;
    private ChatUserRepository $chatUserRepository;
    private ChatRepository $chatRepository;
    private UserRepository $userRepository;
    private ChatLogRepository $chatLogRepository;

    public function __construct(ChatManager $chatManager, ChatBanRepository $chatBanRepository, ChatUserRepository $chatUserRepository, ChatRepository $chatRepository, UserRepository $userRepository, ChatLogRepository $chatLogRepository)
    {
        $this->chatManager = $chatManager;
        $this->chatBanRepository = $chatBanRepository;
        $this->chatUserRepository = $chatUserRepository;
        $this->chatRepository = $chatRepository;
        $this->userRepository = $userRepository;
        $this->chatLogRepository = $chatLogRepository;
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
     * @Route("/api/chat/push", methods={"GET"}, name="api.chat.push")
     */
    public function push(TokenContext $context, Request $request): JsonResponse
    {
        $admin = 0;
        $user = $this->userRepository->getUser($context->getCurrentUser()->getId());
        // chatadmins = 2, admins = 1, noadmin-entwickler = 3,
        // leiter team community = 4, admin-entwickler = 5
        if ($user->admin === 1) {
            if ($user->chatAdmin === 3) {
                $admin = 5; // Entwickler mit Adminrechten
            } else {
                $admin = 1; // Admin
            }
        } elseif ($user->chatAdmin === 1)
            $admin = 2; // Chatadmin
        elseif ($user->chatAdmin === 2)
            $admin = 4; // Leiter Team Community
        elseif ($user->admin === 2)
            $admin = 3; // Entwickler ohne Adminrechte

        $ct = $request->query->get('ctext');

        // Detect command
        $words = StringUtils::splitBySpaces($ct);
        $commandMatch = [];
        // Handle command
        if (count($words) > 0 && preg_match('#^/([a-z]+)$#i', array_shift($words), $commandMatch)) {
            $command = strtolower($commandMatch[1]);

            // Kick user
            if ($command === "kick" && $admin > 0 && $admin !== 3) {
                if (!isset($words[0])) {
                    return new JsonResponse([
                        'cmd' => 'aa',
                        'msg' => 'No user specified!'
                    ]);
                }

                $uid = $this->userRepository->getUserIdByNick($words[0]);
                if ($uid === null) {
                    return new JsonResponse([
                        'cmd' => 'aa',
                        'msg' => 'A user with this nick does not exist!'
                    ]);
                }

                $msg = (count($words) > 1) ? implode(' ', array_slice($words, 1)) : '';
                if ($this->chatManager->kickUser($uid, $msg)) {
                    $this->chatManager->sendSystemMessage($words[0] . ' wurde gekickt!' . ($msg != '' ? ' Grund: ' . $msg : ''));

                    return new JsonResponse();
                }

                return new JsonResponse([
                    'cmd' => 'aa',
                    'msg' => 'User is not online in chat!'
                ]);
            }

            // Ban user
            if ($command === "ban" && $admin > 0 && $admin != 3) {
                if (!isset($words[0])) {
                    return new JsonResponse([
                        'cmd' => 'aa',
                        'msg' => 'No user specified!'
                    ]);
                }

                $uid = $this->userRepository->getUserIdByNick($words[0]);
                if ($uid === null) {
                    return new JsonResponse([
                        'cmd' => 'aa',
                        'msg' => 'A user with this nick does not exist!'
                    ]);
                }

                $text = (count($words) > 1) ? implode(' ', array_slice($words, 1)) : '';
                $this->chatBanRepository->banUser($uid, $text, true);
                $this->chatManager->kickUser($uid, $text);
                $this->chatManager->sendSystemMessage($words[0] . ' wurde gebannt! Grund: ' . $text);

                return new JsonResponse();
            }

            if ($command === "unban" && $admin > 0 && $admin != 3) {
                if (!isset($words[0])) {
                    return new JsonResponse([
                        'cmd' => 'aa',
                        'msg' => 'No user specified!'
                    ]);
                }

                $uid = $this->userRepository->getUserIdByNick($words[0]);
                if ($uid === null) {
                    return new JsonResponse([
                        'cmd' => 'aa',
                        'msg' => 'A user with this nick does not exist!'
                    ]);
                }

                $deleted = $this->chatBanRepository->deleteBan($uid);
                if ($deleted > 0) {
                    return new JsonResponse([
                        'cmd' => 'aa',
                        'msg' => 'Unbanned ' . $words[0] . '!'
                    ]);
                }

                return new JsonResponse([
                    'cmd' => 'aa',
                    'msg' => 'A user with that nick is not banned!'
                ]);
            }

            if ($command === "banlist" && $admin > 0 && $admin !== 3) {
                $bans = $this->chatBanRepository->getBans();
                if (count($bans) === 0) {
                    return new JsonResponse([
                        'cmd' => 'aa',
                        'msg' => 'Bannliste leer!'
                    ]);
                }

                $list = [];
                foreach ($bans as $ban) {
                    $list[] = [
                        'nick' => $ban->userNick,
                        'reason' => $ban->reason,
                        'date' => StringUtils::formatDate($ban->timestamp)
                    ];
                }

                return new JsonResponse([
                    'cmd' => 'bl',
                    'list' => $list
                ]);
            }

            // Unknown command
            return new JsonResponse([
                'cmd' => 'aa',
                'msg' => 'Unknown command \'' . $command . '\'!'
            ]);
        }

        // Handle normal message
        $hash = md5($ct);
        // Woo Hoo, Md5 hashtable
        if ($ct != '' && (!isset($_SESSION['lastchatmsg']) || $_SESSION['lastchatmsg'] != $hash)) {
            $this->chatRepository->addMessage($context->getCurrentUser()->getId(), $context->getCurrentUser()->getNick(), $ct, isset($_SESSION['ccolor']) ? '#' . $_SESSION['ccolor'] : '', $admin);
            $this->chatLogRepository->addLog($context->getCurrentUser()->getId(), $context->getCurrentUser()->getNick(), $ct, isset($_SESSION['ccolor']) ? '#' . $_SESSION['ccolor'] : '', $admin);
            $_SESSION['lastchatmsg'] = $hash;

            return new JsonResponse();
        }

        // zweimal gleiche Nachricht nacheinander
        return new JsonResponse(['cmd' => 'de']);
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
