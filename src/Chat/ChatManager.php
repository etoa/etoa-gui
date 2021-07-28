<?php

declare(strict_types=1);

namespace EtoA\Chat;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Text\TextRepository;

class ChatManager
{
    private ChatRepository $chatRepository;
    private ChatUserRepository $chatUserRepository;
    private TextRepository $textRepo;
    private ConfigurationService $config;

    public function __construct(
        ChatRepository $chatRepository,
        ChatUserRepository $chatUserRepository,
        TextRepository $textRepo,
        ConfigurationService $config
    ) {
        $this->chatRepository = $chatRepository;
        $this->chatUserRepository = $chatUserRepository;
        $this->textRepo = $textRepo;
        $this->config = $config;
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
    public function kickUser(int $uid, string $msg = ''): bool
    {
        $msg = filled($msg) ? $msg : 'Kicked by Admin';

        return (bool) $this->chatUserRepository->kickUser($uid, $msg);
    }

    /**
     * Inserts or updates a user in the chat user table
     */
    public function updateUserEntry(int $id, string $nick): void
    {
        $this->chatUserRepository->updateChatUser($id, $nick);
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
                $text->content
            );
        }

        return '';
    }

    /**
     * Returns true if the specified user is online in the chat
     */
    public function isUserOnline(int $userId): bool
    {
        return (bool) $this->chatUserRepository->getChatUser($userId);
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
                'id' => $chatUser->id,
                'nick' => $chatUser->nick,
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
            $this->sendSystemMessage($chatUser->nick . ' verlässt den Chat (Timeout).');
            $this->chatUserRepository->deleteUser($chatUser->id);
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
}
