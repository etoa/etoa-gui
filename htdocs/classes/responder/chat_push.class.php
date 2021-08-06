<?PHP

use EtoA\Chat\ChatBanRepository;
use EtoA\Chat\ChatLogRepository;
use EtoA\Chat\ChatManager;
use EtoA\Chat\ChatRepository;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;

class ChatPushJsonResponder extends JsonResponder
{
    function getRequiredParams()
    {
        return array('ctext');
    }

    function validateSession()
    {
        global $s;
        return $s->chatValidate();
    }

    function getResponse($params)
    {

        $data = array();

        if (isset($_SESSION['user_id'])) {
            /** @var UserRepository $userRepository */
            $userRepository = $this->app[UserRepository::class];

            $admin = 0;
            $user = $userRepository->getUser((int) $_SESSION['user_id']);
            if ($user !== null) { // Should always be true, otherwise the user does not exist
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
            } else {
                return array('cmd' => 'nu'); // no user
            }

            $ct = $params['ctext'];

            // Detect command
            $m = array();
            $words = StringUtils::splitBySpaces($ct);
            $commandMatch = array();

            /** @var ChatBanRepository $chatBanRepository */
            $chatBanRepository = $this->app[ChatBanRepository::class];

            /** @var UserRepository $userRepository */
            $userRepository = $this->app[UserRepository::class];

            /** @var ChatManager */
            $chatManager = $this->app[ChatManager::class];

            // Handle command
            if (count($words) > 0 && preg_match('#^/([a-z]+)$#i', array_shift($words), $commandMatch)) {
                $command = strtolower($commandMatch[1]);

                // Kick user
                if ($command == "kick" && $admin > 0 && $admin != 3) {
                    if (isset($words[0])) {
                        $uid = $userRepository->getUserIdByNick($words[0]);
                        if ($uid > 0) {
                            $msg = (count($words) > 1) ? implode(' ', array_slice($words, 1)) : '';
                            if ($chatManager->kickUser($uid, $msg)) {
                                $chatManager->sendSystemMessage($words[0] . ' wurde gekickt!' . ($msg != '' ? ' Grund: ' . $msg : ''));
                            } else {
                                return array(
                                    'cmd' => 'aa',
                                    'msg' => 'User is not online in chat!'
                                );
                            }
                        } else {
                            return array(
                                'cmd' => 'aa',
                                'msg' => 'A user with this nick does not exist!'
                            );
                        }
                    } else {
                        return array(
                            'cmd' => 'aa',
                            'msg' => 'No user specified!'
                        );
                    }
                }

                // Ban user
                elseif ($command == "ban" && $admin > 0 && $admin != 3) {
                    if (isset($words[0])) {
                        $uid = $userRepository->getUserIdByNick($words[0]);
                        if ($uid > 0) {
                            $text = (count($words) > 1) ? implode(' ', array_slice($words, 1)) : '';
                            $chatBanRepository->banUser($uid, $text, true);
                            $chatManager->kickUser($uid, $text);
                            $chatManager->sendSystemMessage($words[0] . ' wurde gebannt! Grund: ' . $text);
                        } else {
                            return array(
                                'cmd' => 'aa',
                                'msg' => 'A user with this nick does not exist!'
                            );
                        }
                    } else {
                        return array(
                            'cmd' => 'aa',
                            'msg' => 'No user specified!'
                        );
                    }
                } elseif ($command == "unban" && $admin > 0 && $admin != 3) {
                    if (isset($words[0])) {
                        $uid = $userRepository->getUserIdByNick($words[0]);
                        if ($uid > 0) {
                            $deleted = $chatBanRepository->deleteBan($uid);
                            if ($deleted > 0) {
                                return [
                                    'cmd' => 'aa',
                                    'msg' => 'Unbanned ' . $words[0] . '!'
                                ];
                            }

                            return array(
                                'cmd' => 'aa',
                                'msg' => 'A user with that nick is not banned!'
                            );
                        }
                        return array(
                            'cmd' => 'aa',
                            'msg' => 'A user with this nick does not exist!'
                        );
                    } else {
                        return array(
                            'cmd' => 'aa',
                            'msg' => 'No user specified!'
                        );
                    }
                } elseif ($command == "banlist" && $admin > 0 && $admin != 3) {
                    $bans = $chatBanRepository->getBans();
                    if (count($bans) > 0) {
                        $list = [];
                        foreach ($bans as $ban) {
                            $list[] = array(
                                'nick' => $ban->userNick,
                                'reason' => $ban->reason,
                                'date' => df($ban->timestamp)
                            );
                        }

                        return array(
                            'cmd' => 'bl',
                            'list' => $list
                        );
                    }

                    return array(
                        'cmd' => 'aa',
                        'msg' => 'Bannliste leer!'
                    );
                }

                // Unknown command
                else {
                    return array(
                        'cmd' => 'aa',
                        'msg' => 'Unknown command \'' . $command . '\'!'
                    );
                }
            }

            // Handle normal message
            else {
                $hash = md5($ct);
                // Woo Hoo, Md5 hashtable
                if ($ct != '' && (!isset($_SESSION['lastchatmsg']) || $_SESSION['lastchatmsg'] != $hash)) {
                    /** @var ChatRepository $chatRepository */
                    $chatRepository = $this->app[ChatRepository::class];
                    $chatRepository->addMessage((int) $_SESSION['user_id'], $_SESSION['user_nick'], $ct, isset($_SESSION['ccolor']) ? '#' . $_SESSION['ccolor'] : '', $admin);

                    /** @var ChatLogRepository $chatLogRepository */
                    $chatLogRepository = $this->app[ChatLogRepository::class];
                    $chatLogRepository->addLog((int) $_SESSION['user_id'], $_SESSION['user_nick'], $ct, isset($_SESSION['ccolor']) ? '#' . $_SESSION['ccolor'] : '', $admin);
                    $_SESSION['lastchatmsg'] = $hash;
                } else {
                    // zweimal gleiche Nachricht nacheinander
                    return array('cmd' => 'de');
                }
            }
        } else {
            // !isset $s[userid] => not logged in
            return array('cmd' => 'nl');
        }

        return $data;
    }
}
