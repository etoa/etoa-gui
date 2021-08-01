<?php

use EtoA\Message\MessageRepository;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var MessageRepository */
$messageRepository = $app[MessageRepository::class];

/** @var UserRepository */
$userRepository = $app[UserRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

if ($request->query->getInt('msg_id') > 0) {
    viewSentMessage($cu, $messageRepository, $userRepository, $request->query->getInt('msg_id'));
} else {
    listSentMessages($cu, $messageRepository, $userRepository);
}

function viewSentMessage(
    User $cu,
    MessageRepository $messageRepository,
    UserRepository $userRepository,
    int $id
): void {
    $messages = $messageRepository->findBy([
        'id' => $id,
        'user_from_id' => $cu->id,
    ]);
    if (count($messages) > 0) {
        $message = $messages[0];

        $subject = filled($message->subject)
            ? htmlentities($message->subject, ENT_QUOTES, 'UTF-8')
            : "<i>Kein Titel</i>";

        tableStart();
        echo "<tr><th colspan=\"2\">" . $subject . "</th></tr>";
        echo "<tr><th style=\"width:100px;\">Datum:</td><td>" . date("d.m.Y H:i", $message->timestamp) . "</td></tr>";
        echo "<tr><th>Empfänger:</th><td>" . userPopUp($message->userTo, $userRepository->getNick($message->userTo), 0) . "</td></tr>";
        echo "<tr><th>Text:</td><td>" . text2html(addslashes($message->text)) . "</td></tr>";
        tableEnd();

        echo "<input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=messages&mode=sent'\" /> &nbsp; ";
    } else {
        error_msg("Diese Nachricht existiert nicht!");
        echo "<input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=messages&mode=sent'\" />";
    }
}

function listSentMessages(
    User $cu,
    MessageRepository $messageRepository,
    UserRepository $userRepository
): void {
    global $page;
    global $mode;

    tableStart();
    echo "<tr><th colspan=\"5\">Gesendete Nachrichten</th></tr>";

    $messages = $messageRepository->findBy([
        'user_from_id' => $cu->id
    ]);
    if (count($messages) > 0) {
        foreach ($messages as $message) {

            $im_path = !$message->read
                ? "images/pm_new.gif"
                : "images/pm_normal.gif";

            echo "<tr><td style=\"width:16px;\">
            <a href=\"?page=$page&msg_id=" . $message->id . "&mode=" . $mode . "\">
            <img src=\"" . $im_path . "\" style=\"border:none;width:16px;height:18px;\"></a></td>";
            echo "<td ><a href=\"?page=$page&msg_id=" . $message->id . "&mode=" . $mode . "\">";
            echo filled($message->subject)
                ? htmlentities($message->subject, ENT_QUOTES, 'UTF-8')
                : "<i>Kein Titel</i>";
            echo "</a></td><td style=\"width:120px;\">" . userPopUp($message->userTo, $userRepository->getNick($message->userTo), 0) . "</td>";
            echo "<td style=\"width:120px;\">" . date("d.m.Y H:i", $message->timestamp) . "</td>";
        }
    } else {
        echo "<tr><td width=\"400\" colspan=\"4\"><i>Keine Nachrichten vorhanden</i></td>";
    }
    tableEnd();
    echo "<br/>Es werden nur die 30 neusten Nachrichten angezeigt.";
}
