<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Chat\ChatBanRepository;
use EtoA\Chat\ChatMessage;
use EtoA\Chat\ChatRepository;
use EtoA\Chat\ChatUserRepository;
use EtoA\Entity\ChatBan;
use EtoA\Entity\ChatUser;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('admin_chat_view')]
class ChatViewComponent
{
    use DefaultActionTrait;

    public function __construct(
        private readonly ChatBanRepository  $chatBanRepository,
        private readonly ChatUserRepository $chatUserRepository,
        private readonly ChatRepository     $chatRepository,
    ) {
    }

    #[LiveAction]
    public function default(): void
    {
    }

    /**
     * @return ChatMessage[]
     */
    public function getMessages(): array
    {
        return $this->chatRepository->getMessagesAfter(0);
    }

    /**
     * @return ChatUser[]
     */
    public function getUsers(): array
    {
        return $this->chatUserRepository->getChatUsers();
    }

    /**
     * @return ChatBan[]
     */
    public function getBans(): array
    {
        return $this->chatBanRepository->findAll();
    }
}
