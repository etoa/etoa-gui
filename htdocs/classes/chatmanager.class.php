<?PHP

use EtoA\Chat\ChatRepository;
use EtoA\Chat\ChatUserRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Text\TextRepository;

class ChatManager {

    /**
     * Inserts a system message into the chat table
    */
    static function sendSystemMessage($msg)
    {
        global $app;

        /** @var ChatRepository $chatRepository */
        $chatRepository = $app[ChatRepository::class];
        $chatRepository->addSystemMessage($msg);
    }

    /**
    * Remove a user from the chat user list by
    * insterting a kick reason into the chat user table
    */
    static function kickUser($uid, $msg = '')
    {
        global $app;
        /** @var ChatUserRepository $chatUserRepository */
        $chatUserRepository = $app[ChatUserRepository::class];

        $msg = $msg ? $msg :'Kicked by Admin';

        return (bool) $chatUserRepository->kickUser($uid, $msg);
    }

    /**
    * Inserts or updates a user in the chat user table
    */
    static function updateUserEntry($id, $nick) {
        global $app;

        /** @var ChatUserRepository $chatUserRepository */
        $chatUserRepository = $app[ChatUserRepository::class];
        $chatUserRepository->updateChatUser((int) $id, $nick);
    }

        /**
         * Performs an ordinary logout of an user
        */
        static function logoutUser($userId) {
            global $app;

            /** @var ChatUserRepository $chatUserRepository */
            $chatUserRepository = $app[ChatUserRepository::class];
            $chatUserRepository->deleteUser((int) $userId);
    }

    /**
    * Gets the configured welcome message
    */
    static function getWelcomeMessage($nick) {
        // TODO
        global $app;
        /** @var TextRepository $textRepo */
        $textRepo = $app[TextRepository::class];

        $text = $textRepo->find('chat_welcome_message');
        if ($text->isEnabled())
        {
            return str_replace(
                array('%nick%'),
                array($nick),
                $text->content
            );
        }
        return '';
    }

    /**
    * Returns true if the specified user is online in the chat
    */
    static function isUserOnline($userId) {
        global $app;

        /** @var ChatUserRepository $chatUserRepository */
        $chatUserRepository = $app[ChatUserRepository::class];

        return (bool) $chatUserRepository->getChatUser($userId);
    }

    /**
     * Gets the number of online users in the chat
     */
    static function getUserOnlineNumber()
    {
        global $app;

        /** @var ChatUserRepository $chatUserRepository */
        $chatUserRepository = $app[ChatUserRepository::class];

        return count($chatUserRepository->getChatUsers());
    }

    /**
     * Gets a list of users currently being online in the chat
    */
    static function getUserOnlineList() {
        global $app;

        /** @var ChatUserRepository $chatUserRepository */
        $chatUserRepository = $app[ChatUserRepository::class];

        $data = [];
        $chatUsers = $chatUserRepository->getChatUsers();
        foreach ($chatUsers as $chatUser) {
            $data[] = [
                'id' => $chatUser->id,
                'nick' => $chatUser->nick,
            ];
        }

        return $data;
    }

    /**
    * Cleans users from the chat user table if timeout exceeded
    */
    static function cleanUpUsers()
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];
        /** @var ChatUserRepository $chatUserRepository */
        $chatUserRepository = $app[ChatUserRepository::class];

        $chatUsers = $chatUserRepository->getTimedOutChatUsers($config->getInt('chat_user_timeout'));
        foreach ($chatUsers as $chatUser) {
            self::sendSystemMessage($chatUser->nick.' verlässt den Chat (Timeout).');
            $chatUserRepository->deleteUser($chatUser->id);
        }

        return count($chatUsers);
    }

    /**
    * Removes old messages from the chat table
    * Keeps only the last X messages
    */
    static function cleanUpMessages()
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];

        /** @var ChatRepository $chatRepository */
        $chatRepository = $app[ChatRepository::class];

        return $chatRepository->cleanupMessage($config->getInt('chat_recent_messages'));
    }
}
