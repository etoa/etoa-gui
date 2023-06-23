<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Message\Message;
use EtoA\Message\MessageRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('admin_message_view')]
class MessageViewComponent
{
    use DefaultActionTrait;

    #[LiveProp]
    public int $messageId;
    #[LiveProp]
    public string $userFrom;
    #[LiveProp]
    public string $userTo;
    #[LiveProp]
    public string $category;
    private ?Message $message = null;

    public function mount(Message $message = null): void
    {
        $this->message = $message;
        if ($message !== null) {
            $this->messageId = $message->id;
        }
    }

    public function __construct(
        private MessageRepository $messageRepository
    ) {
    }

    #[LiveAction]
    public function delete(): void
    {
        $this->messageRepository->setRead($this->messageId);
        $this->messageRepository->setDeleted($this->messageId);
    }

    #[LiveAction]
    public function undelete(): void
    {
        $this->messageRepository->setDeleted($this->messageId, false);
    }

    public function getMessage(): Message
    {
        if ($this->message === null) {
            $this->message = $this->messageRepository->find($this->messageId);
        }

        return $this->message;
    }
}
