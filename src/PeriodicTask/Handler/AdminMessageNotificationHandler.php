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
    private AdminUserRepository $adminUserRepository;
    private MessageRepository $messageRepository;
    private UserRepository $userRepository;
    private MailSenderService $mailSenderService;

    public function __construct(AdminUserRepository $adminUserRepository, MessageRepository $messageRepository, UserRepository $userRepository, MailSenderService $mailSenderService)
    {
        $this->adminUserRepository = $adminUserRepository;
        $this->messageRepository = $messageRepository;
        $this->userRepository = $userRepository;
        $this->mailSenderService = $mailSenderService;
    }

    public function __invoke(AdminMessageNotificationTask $task): SuccessResult
    {
        $count = 0;

        $adminUsers = $this->adminUserRepository->findAll();
        foreach ($adminUsers as $adminUser) {
            if ($adminUser->playerId > 0) {
                $messages = $this->messageRepository->findBy([
                    'user_to_id' => $adminUser->playerId,
                    'mailed' => false,
                    'read' => false,
                ]);
                if (count($messages) > 0) {
                    $email_text = "Hallo " . $adminUser->nick . ",\n\nDu hast " . count($messages) . " neue Nachricht(en) erhalten.\n\n";
                    foreach ($messages as $message) {
                        $email_text .= $message->userFrom == 0
                            ? "#" . ($count + 1) . " Von System mit dem Betreff '" . $message->subject . "'\n\n\n"
                            : "#" . ($count + 1) . " Von " . $this->userRepository->getNick($message->userFrom) . " mit dem Betreff '" . $message->subject . "'\n\n" . substr($message->text, 0, 500) . "\n\n\n";
                    }

                    $this->mailSenderService->send("Neue private Nachricht in EtoA - Admin", $email_text, $adminUser->email);

                    $this->messageRepository->setMailed($adminUser->playerId);

                    $count++;
                }
            }
        }

        return SuccessResult::create("$count Admin-Mailbenachrichtigungen versendet");
    }
}
