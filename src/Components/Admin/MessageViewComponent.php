<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Entity\Message;
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
    public string $userFrom;
    #[LiveProp]
    public string $userTo;
    #[LiveProp]
    public string $category;
    #[LiveProp]
    public ?Message $message = null;

    public function __construct(
        private readonly MessageRepository $messageRepository
    ) {}

    #[LiveAction]
    public function delete(): void
    {
        $this->message->setRead(true);
        $this->message->setDeleted(true);
        $this->messageRepository->save();
    }

    #[LiveAction]
    public function undelete(): void
    {
        $this->message->setDeleted(false);
        $this->messageRepository->save();
    }
}
