<?PHP

use EtoA\Admin\AdminUserRepository;
use EtoA\Message\MessageRepository;
use EtoA\User\UserRepository;
use Pimple\Container;

/**
 * Inform admins about incomming messages
 */
class AdminMessageNotificationTask implements IPeriodicTask
{
    private AdminUserRepository $adminUserRepo;
    private UserRepository $userRepo;
    private MessageRepository $messageRepository;

    public function __construct(Container $app)
    {
        $this->adminUserRepo = $app[AdminUserRepository::class];
        $this->userRepo = $app[UserRepository::class];
        $this->messageRepository = $app[MessageRepository::class];
    }

    function run()
    {
        $count = 0;

        $adminUsers = $this->adminUserRepo->findAll();
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
                            : "#" . ($count + 1) . " Von " . $this->userRepo->getNick($message->userFrom) . " mit dem Betreff '" . $message->subject . "'\n\n" . substr($message->text, 0, 500) . "\n\n\n";
                    }

                    $mail = new Mail("Neue private Nachricht in EtoA - Admin", $email_text);
                    $mail->send($adminUser->email);

                    $this->messageRepository->setMailed($adminUser->playerId);

                    $count++;
                }
            }
        }

        return "$count Admin-Mailbenachrichtugungen versendet";
    }

    function getDescription()
    {
        return "Admin-Mailbenachrichtugungen versenden";
    }
}
