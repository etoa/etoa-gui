<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Admin\AdminUserRepository;
use EtoA\Message\MessageRepository;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\AdminMessageNotificationTask;
use EtoA\Support\Mail\MailSenderService;
use EtoA\User\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AdminMessageNotificationHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly AdminUserRepository $adminUserRepository,
        private readonly MessageRepository $messageRepository,
        private readonly MailSenderService $mailSenderService
    )
    {}

    public function __invoke(AdminMessageNotificationTask $task): SuccessResult
    {
        $count = 0;

        $adminUsers = $this->adminUserRepository->findAll();
        foreach ($adminUsers as $adminUser) {
            if ($adminUser->getPlayer()) {
                $messages = $this->messageRepository->findBy([
                    'userTo' => $adminUser->getPlayer(),
                    'mailed' => false,
                    'read' => false,
                ]);
                if (count($messages) > 0) {
                    $email_text = "Hallo " . $adminUser->getNick() . ",\n\nDu hast " . count($messages) . " neue Nachricht(en) erhalten.\n\n";
                    foreach ($messages as $message) {
                        $email_text .= !$message->getUserFrom()
                            ? "#" . ($count + 1) . " Von System mit dem Betreff '" . $message->getMessageData()->getSubject() . "'\n\n\n"
                            : "#" . ($count + 1) . " Von " . $message->getUserFrom()->getNick() . " mit dem Betreff '" . $message->getMessageData()->getSubject() . "'\n\n" . substr($message->getMessageData()->getText(), 0, 500) . "\n\n\n";
                    }

                    $this->mailSenderService->send("Neue private Nachricht in EtoA - Admin", $email_text, $adminUser->getEmail());

                    $this->messageRepository->setMailed($adminUser->getPlayer());

                    $count++;
                }
            }
        }

        return SuccessResult::create("$count Admin-Mailbenachrichtigungen versendet");
    }
}
