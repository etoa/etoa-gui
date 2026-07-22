<?php

declare(strict_types=1);

namespace EtoA\Chat;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\User;
use EtoA\Support\StringUtils;
use EtoA\Text\TextRepository;
use EtoA\User\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class ChatManager
{
    public function __construct(
        private readonly ChatRepository       $chatRepository,
        private readonly ChatUserRepository   $chatUserRepository,
        private readonly TextRepository       $textRepo,
        private readonly Security             $security,
        private readonly UserRepository       $userRepository,
        private readonly ConfigurationService $config,
        private readonly ChatBanRepository    $chatBanRepository,
        private readonly ChatLogRepository    $chatLogRepository,
        private readonly RequestStack         $requestStack
    )
    {
    }

    /**
     * Inserts a system message into the chat table
     */
    public function sendSystemMessage(string $msg): void
    {
        $this->chatRepository->addSystemMessage($msg);
    }

    /**
     * Remove a user from the chat user list by
     * inserting a kick reason into the chat user table
     */
    public function kickUser(int|User $uid, string $msg = ''): bool
    {
        $msg = filled($msg) ? $msg : 'Kicked by Admin';

        return $this->chatUserRepository->kickUser($uid, $msg);
    }

    /**
     * Inserts or updates a user in the chat user table
     */
    public function updateUserEntry(User $user): void
    {
        $this->chatUserRepository->updateChatUser($user);
    }

    /**
     * Performs an ordinary logout of an user
     */
    public function logoutUser(int $userId): void
    {
        $this->chatUserRepository->deleteUser($userId);
    }

    /**
     * Gets the configured welcome message
     */
    public function getWelcomeMessage(string $nick): string
    {
        $text = $this->textRepo->find('chat_welcome_message');
        if ($text->isEnabled()) {
            return str_replace(
                array('%nick%'),
                array($nick),
                $text->getContent()
            );
        }

        return '';
    }

    /**
     * Returns true if the specified user is online in the chat
     */
    public function isUserOnline(int $userId): bool
    {
        return (bool)$this->chatUserRepository->getChatUser($userId);
    }

    /**
     * Gets the number of online users in the chat
     */
    public function getUserOnlineNumber(): int
    {
        return count($this->chatUserRepository->getChatUsers());
    }

    /**
     * Gets a list of users currently being online in the chat
     *
     * @return array<int, array{id: int, nick: string}>
     */
    public function getUserOnlineList(): array
    {
        $data = [];
        $chatUsers = $this->chatUserRepository->getChatUsers();
        foreach ($chatUsers as $chatUser) {
            $data[] = [
                'id' => $chatUser->getUser()->getId(),
                'nick' => $chatUser->getUser()->getNick(),
            ];
        }

        return $data;
    }

    /**
     * Cleans users from the chat user table if timeout exceeded
     */
    public function cleanUpUsers(): int
    {
        $chatUsers = $this->chatUserRepository->getTimedOutChatUsers($this->config->getInt('chat_user_timeout'));
        foreach ($chatUsers as $chatUser) {
            $this->sendSystemMessage($chatUser->getNick() . ' verlässt den Chat (Timeout).');
            $this->chatUserRepository->deleteUser($chatUser->getUser());
        }

        return count($chatUsers);
    }

    /**
     * Removes old messages from the chat table
     * Keeps only the last X messages
     */
    public function cleanUpMessages(): int
    {
        return $this->chatRepository->cleanupMessage($this->config->getInt('chat_recent_messages'));
    }

    public function push(string $ct): JsonResponse
    {
        $admin = 0;
        $user = $this->security->getUser()->getData();
        // chatadmins = 2, admins = 1, noadmin-entwickler = 3,
        // leiter team community = 4, admin-entwickler = 5
        if ($user->getAdmin() === 1) {
            if ($user->getChatAdmin() === 3) {
                $admin = 5; // Entwickler mit Adminrechten
            } else {
                $admin = 1; // Admin
            }
        } elseif ($user->getChatAdmin() === 1) {
            $admin = 2;
        } // Chatadmin
        elseif ($user->getChatAdmin() === 2) {
            $admin = 4;
        } // Leiter Team Community
        elseif ($user->getAdmin() === 2) {
            $admin = 3;
        } // Entwickler ohne Adminrechte

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
                        'msg' => 'No user specified!',
                    ]);
                }

                $uid = $this->userRepository->findOneBy(['user' => $words[0]]);
                if ($uid === null) {
                    return new JsonResponse([
                        'cmd' => 'aa',
                        'msg' => 'A user with this nick does not exist!',
                    ]);
                }

                $msg = (count($words) > 1) ? implode(' ', array_slice($words, 1)) : '';
                if ($this->kickUser($uid, $msg)) {
                    $this->sendSystemMessage($words[0] . ' wurde gekickt!' . ($msg != '' ? ' Grund: ' . $msg : ''));

                    return new JsonResponse();
                }

                return new JsonResponse([
                    'cmd' => 'aa',
                    'msg' => 'User is not online in chat!',
                ]);
            }

            // Ban user
            if ($command === "ban" && $admin > 0 && $admin != 3) {
                if (!isset($words[0])) {
                    return new JsonResponse([
                        'cmd' => 'aa',
                        'msg' => 'No user specified!',
                    ]);
                }

                $uid = $this->userRepository->getUserIdByNick($words[0]);
                if ($uid === null) {
                    return new JsonResponse([
                        'cmd' => 'aa',
                        'msg' => 'A user with this nick does not exist!',
                    ]);
                }

                $text = (count($words) > 1) ? implode(' ', array_slice($words, 1)) : '';
                $this->chatBanRepository->banUser($uid, $text, true);
                $this->kickUser($uid, $text);
                $this->sendSystemMessage($words[0] . ' wurde gebannt! Grund: ' . $text);

                return new JsonResponse();
            }

            if ($command === "unban" && $admin > 0 && $admin != 3) {
                if (!isset($words[0])) {
                    return new JsonResponse([
                        'cmd' => 'aa',
                        'msg' => 'No user specified!',
                    ]);
                }

                $uid = $this->userRepository->getUserIdByNick($words[0]);
                if ($uid === null) {
                    return new JsonResponse([
                        'cmd' => 'aa',
                        'msg' => 'A user with this nick does not exist!',
                    ]);
                }

                $deleted = $this->chatBanRepository->deleteBan($uid);
                if ($deleted) {
                    return new JsonResponse([
                        'cmd' => 'aa',
                        'msg' => 'Unbanned ' . $words[0] . '!',
                    ]);
                }

                return new JsonResponse([
                    'cmd' => 'aa',
                    'msg' => 'A user with that nick is not banned!',
                ]);
            }

            if ($command === "banlist" && $admin > 0 && $admin !== 3) {
                $bans = $this->chatBanRepository->getBans();
                if (count($bans) === 0) {
                    return new JsonResponse([
                        'cmd' => 'aa',
                        'msg' => 'Bannliste leer!',
                    ]);
                }

                $list = [];
                foreach ($bans as $ban) {
                    $list[] = [
                        'nick' => $ban->getUser()->getNick(),
                        'reason' => $ban->getReason(),
                        'date' => StringUtils::formatDate($ban->getTimestamp()),
                    ];
                }

                return new JsonResponse([
                    'cmd' => 'bl',
                    'list' => $list,
                ]);
            }

            // Unknown command
            return new JsonResponse([
                'cmd' => 'aa',
                'msg' => 'Unknown command \'' . $command . '\'!',
            ]);
        }

        // Handle normal message
        $hash = md5($ct);
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        // Woo Hoo, Md5 hashtable
        if ($ct != '' && (!$session->has('lastchatmsg') || $session->get('lastchatmsg') != $hash)) {
            $this->chatRepository->addMessage($user, $ct, $session->has('ccolor') ? '#' . $session->get('ccolor') : '', $admin);
            $this->chatLogRepository->addLog($user, $ct, $session->has('ccolor') ? '#' . $session->get('ccolor') : '', $admin);
            $session->set('lastchatmsg',$hash);

            return new JsonResponse();
        }

        // zweimal gleiche Nachricht nacheinander
        return new JsonResponse(['cmd' => 'de']);
    }

    public function logout(): JsonResponse
    {
        $user = $this->security->getUser()->getData();

        $this->logoutUser($user->getId());
        $this->sendSystemMessage($user->getNick() . ' verlässt den Chat.');
        $session = $this->requestStack->getSession();
        $session->set('chat',false);

        return new JsonResponse();
    }
}
