<?PHP

use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var MessageRepository $messageRepository */
$messageRepository = $app[MessageRepository::class];

/** @var MessageCategoryRepository $messageCategoryRepository */
$messageCategoryRepository = $app[MessageCategoryRepository::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

if ($request->query->getInt('msg_id') > 0) {
    viewDeletedMessage($cu, $messageRepository, $userRepository, $request->query->getInt('msg_id'));
} else {
    listDeletedMessages($request, $cu, $messageRepository, $messageCategoryRepository, $userRepository);
}

function viewDeletedMessage(
    User $cu,
    MessageRepository $messageRepository,
    UserRepository $userRepository,
    int $id
): void {
    $messages = $messageRepository->findBy([
        'id' => $id,
        'user_to_id' => $cu->id,
        'deleted' => true,
    ]);
    if (count($messages) > 0) {
        $message = $messages[0];

        $subject = filled($message->subject)
            ? htmlentities($message->subject, ENT_QUOTES, 'UTF-8')
            : "<i>Kein Titel</i>";

        tableStart();
        echo "<tr><th colspan=\"2\">" . $subject . "</th></tr>";
        echo "<tr><th style=\"width:100px;\">Datum:</td><td>" . date("d.m.Y H:i", $message->timestamp) . "</td></tr>";
        echo "<tr><th>Sender:</th><td>" . userPopUp($message->userFrom, $userRepository->getNick($message->userFrom), 0) . "</td></tr>";
        echo "<tr><th>Text:</td><td>" . BBCodeUtils::toHTML(addslashes($message->text)) . "</td></tr>";
        tableEnd();

        echo "<input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=messages&mode=deleted'\" /> &nbsp; ";
        echo "<input type=\"button\" value=\"Wiederherstellen\" onclick=\"document.location='?page=messages&mode=deleted&restore=" . $message->id . "'\" />";
    } else {
        error_msg("Diese Nachricht existiert nicht!");
        echo "<input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=messages&mode=deleted'\" />";
    }
}

function listDeletedMessages(
    Request $request,
    User $cu,
    MessageRepository $messageRepository,
    MessageCategoryRepository $messageCategoryRepository,
    UserRepository $userRepository
): void {
    global $page;
    global $mode;

    if ($request->query->getInt('restore') > 0) {
        if ($messageRepository->setDeleted($request->query->getInt('restore'), false, $cu->id)) {
            echo "Nachricht wurde wiederhergestellt!<br/><br/>";
        }
    }

    tableStart();
    echo "<tr><th colspan=\"5\">Papierkorb</th></tr>";

    $messages = $messageRepository->findBy([
        'user_to_id' => $cu->id,
        'deleted' => true,
    ], 30);
    if (count($messages) > 0) {
        foreach ($messages as $message) {

            $subject = filled($message->subject)
                ? htmlentities($message->subject, ENT_QUOTES, 'UTF-8')
                : "<i>Kein Titel</i>";

            echo "<tr><td style=\"width:16px;\">
            <a href=\"?page=$page&msg_id=" . $message->id . "&mode=" . $mode . "\">
            <img src=\"images/pm_normal.gif\" style=\"border:none;width:16px;height:18px;\"></a></td>";
            echo "<td><a href=\"?page=$page&msg_id=" . $message->id . "&mode=" . $mode . "\">" . $subject . "</a></td>";
            echo "<td style=\"width:120px;\">" . $messageCategoryRepository->getName($message->catId) . "</td>";
            echo "<td style=\"width:120px;\">" . userPopUp($message->userFrom, $userRepository->getNick($message->userFrom), 0) . "</td>";
            echo "<td style=\"width:120px;\">" . date("d.m.Y H:i", $message->timestamp) . "</td>";
        }
    } else {
        echo "<tr><td width=\"400\" colspan=\"4\"><i>Keine Nachrichten vorhanden</i></td>";
    }
    tableEnd();

    echo "<br/>Es werden nur die 30 neusten Nachrichten angezeigt.";
}
