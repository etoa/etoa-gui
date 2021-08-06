<?php

use EtoA\Message\MessageIgnoreRepository;
use EtoA\Message\MessageRepository;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var MessageRepository */
$messageRepository = $app[MessageRepository::class];

/** @var MessageIgnoreRepository */
$messageIgnoreRepository = $app[MessageIgnoreRepository::class];

/** @var UserRepository */
$userRepository = $app[UserRepository::class];

/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

if (!$cu->isVerified) {
    iBoxStart("Funktion gesperrt");
    echo "Solange deine E-Mail Adresse nicht bestätigt ist, kannst du keine Nachrichten versenden!";
    iBoxEnd();
} else {
    if (!isset($_SESSION['messagesSent'])) {
        $_SESSION['messagesSent'] = [];
    }

    if ($request->request->has('submit') && checker_verify()) {
        submitSendMessage(
            $request,
            $userRepository,
            $messageRepository,
            $messageIgnoreRepository,
            $cu,
            $app['dispatcher']
        );
    }

    sendMessageForm($request, $cu, $messageRepository, $userRepository, $userPropertiesRepository);
}

function sendMessageForm(
    Request $request,
    User $cu,
    MessageRepository $messageRepository,
    UserRepository $userRepository,
    UserPropertiesRepository $userPropertiesRepository
): void {
    global $page;
    global $mode;

    $properties = $userPropertiesRepository->getOrCreateProperties($cu->id);

    $previewNewMessage = $properties->msgCreationPreview;

    $user = getInitialUser($request, $userRepository);
    $subject = getInitialSubject($request);
    $text = getInitialMessageText($request, $messageRepository, $cu);
    if (filled($properties->msgSignature)) {
        $text = "\n\n" . $properties->msgSignature . $text;
    }

    echo "<form action=\"?page=" . $page . "&mode=" . $mode . "\" method=\"POST\" name=\"msgform\">";
    checker_init();
    tableStart("Nachricht verfassen");
    echo "<tr>
        <th width=\"50\" valign=\"top\">Empfänger:</th>
        <td width=\"250\"  colspan=\"2\">
            <input type=\"text\" name=\"message_user_to\" id=\"user_nick\" autocomplete=\"off\" value=\"";
    echo $user;
    echo "\" maxlength=\"255\" style=\"width:330px\" onkeyup=\"xajax_searchUser(this.value);\"> Mehrere Empfänger mit ; trennen<br />
            <div class=\"citybox\" id=\"citybox\" style=\"display:none\">&nbsp;</div>
        </td>
        </tr>";
    echo "<tr>
        <th width=\"50\" valign=\"top\">Betreff:</th>
        <td width=\"250\" colspan=\"2\">
            <input type=\"text\" name=\"message_subject\" value=\"" . $subject . "\"  style=\"width:97%\" maxlength=\"255\">
        </td>
        </tr>";
    echo "<tr>
        <th width=\"50\" valign=\"top\">Text:</th>
        <td width=\"250\"><textarea name=\"message_text\" id=\"message\" rows=\"12\" cols=\"60\" ";
    if ($previewNewMessage) {
        echo "onkeyup=\"text2html(this.value,'msgPreview');\"";
    }
    echo ">" . $text . '</textarea><br/>' . helpLink('textformat', 'Hilfe zur Formatierung') . '</td>';

    $previewFunction = $previewNewMessage ? "text2html(document.getElementById('message').value,'msgPreview');" : '';
    echo "<td>
        <input type=\"button\" onclick=\"bbcode(this.form,'b','');" . $previewFunction . "\" value=\"B\" style=\"font-weight:bold;\">
        <input type=\"button\" onclick=\"bbcode(this.form,'i','');" . $previewFunction . "\" value=\"I\" style=\"font-style:italic;\">
        <input type=\"button\" onclick=\"bbcode(this.form,'u','');" . $previewFunction . "\" value=\"U\" style=\"text-decoration:underline\">
        <input type=\"button\" onclick=\"bbcode(this.form,'c','');" . $previewFunction . "\" value=\"Center\" style=\"text-align:center\"> <br/><br/>
        <input type=\"button\" onclick=\"namedlink(this.form,'url');" . $previewFunction . "\" value=\"Link\">
        <input type=\"button\" onclick=\"namedlink(this.form,'email');" . $previewFunction . "\" value=\"E-Mail\">
        <input type=\"button\" onclick=\"bbcode(this.form,'img','http://');" . $previewFunction . "\" value=\"Bild\"> <br/><br/>";
?>
    <select id="sizeselect" onchange="fontformat(this.form,this.options[this.selectedIndex].value,'size');" onclick="<?= $previewFunction ?>">
        <option value="0">Grösse</option>
        <option value="7">winzig</option>
        <option value="10">klein</option>
        <option value="12">mittel</option>
        <option value="16">groß</option>
        <option value="20">riesig</option>
    </select>
    <select id="colorselect" onchange="fontformat(this.form,this.options[this.selectedIndex].value,'color');" onclick="<?= $previewFunction ?>">
        <option value="0">Farbe</option>
        <option value="skyblue" style="color: skyblue;">sky blue</option>
        <option value="royalblue" style="color: royalblue;">royal blue</option>
        <option value="blue" style="color: blue;">blue</option>
        <option value="darkblue" style="color: darkblue;">dark-blue</option>
        <option value="orange" style="color: orange;">orange</option>
        <option value="orangered" style="color: orangered;">orange-red</option>

        <option value="crimson" style="color: crimson;">crimson</option>
        <option value="red" style="color:red;">red</option>
        <option value="firebrick" style="color: firebrick;">firebrick</option>
        <option value="darkred" style="color: darkred;">dark red</option>
        <option value="green" style="color: green;">green</option>
        <option value="limegreen" style="color: limegreen;">limegreen</option>
        <option value="seagreen" style="color: seagreen;">sea-green</option>
        <option value="deeppink" style="color: deeppink;">deeppink</option>
        <option value="tomato" style="color: tomato;">tomato</option>

        <option value="coral" style="color: coral;">coral</option>
        <option value="purple" style="color: purple;">purple</option>
        <option value="indigo" style="color: indigo;">indigo</option>
        <option value="burlywood" style="color: burlywood;">burlywood</option>
        <option value="sandybrown" style="color: sandybrown;">sandy brown</option>
        <option value="sienna" style="color: sienna;">sienna</option>
        <option value="chocolate" style="color: chocolate;">chocolate</option>
        <option value="teal" style="color: teal;">teal</option>
        <option value="silver" style="color: silver;">silver</option>
    </select>
<?php
    echo "<br><br>";
    // Smilies
    echo "<a href=\"javascript:;\" onclick=\"addText(':-)', '', false, document.msgform);" . $previewFunction . "\"><img src=\"" . SMILIE_DIR . "/smile.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(';-)', '', false, document.msgform);" . $previewFunction . "\"><img src=\"" . SMILIE_DIR . "/wink.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':-P', '', false, document.msgform);" . $previewFunction . "\"><img src=\"" . SMILIE_DIR . "/tongue.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':0', '', false, document.msgform);" . $previewFunction . "\"><img src=\"" . SMILIE_DIR . "/laugh.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':-D', '', false, document.msgform);" . $previewFunction . "\"><img src=\"" . SMILIE_DIR . "/biggrin.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;<br>
            <a href=\"javascript:;\" onclick=\"addText(':-(', '', false, document.msgform);" . $previewFunction . "\"><img src=\"" . SMILIE_DIR . "/frown.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText('8-)', '', false, document.msgform);" . $previewFunction . "\"><img src=\"" . SMILIE_DIR . "/cool.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':angry:', '', false, document.msgform);" . $previewFunction . "\"><img src=\"" . SMILIE_DIR . "/angry.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':sad:', '', false, document.msgform);" . $previewFunction . "\"><img src=\"" . SMILIE_DIR . "/sad.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':pst:', '', false, document.msgform);" . $previewFunction . "\"><img src=\"" . SMILIE_DIR . "/pst.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;<br>
            <a href=\"javascript:;\" onclick=\"addText(':holy:', '', false, document.msgform);" . $previewFunction . "\"><img src=\"" . SMILIE_DIR . "/holy.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':rolleyes:', '', false, document.msgform);" . $previewFunction . "\"><img src=\"" . SMILIE_DIR . "/rolleyes.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':anger:', '', false, document.msgform);" . $previewFunction . "\"><img src=\"" . SMILIE_DIR . "/anger.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;";
    echo "</td>";
    echo "</tr>";
    if ($previewNewMessage) {
        echo "<tr>
            <th>Vorschau:</th>
            <td colspan=\"2\" id=\"msgPreview\">Vorschau wird geladen...</td>
        </tr>";
    }
    tableEnd();
    echo "<script type=\"text/javascript\">";
    if ($previewNewMessage) {
        echo "text2html(document.getElementById('message').value,'msgPreview');";
    }
    echo "document.getElementById('user_nick').focus()";
    echo "</script>";
    echo "<input type=\"submit\" name=\"submit\" value=\"Senden\" onclick=\"if (document.getElementById('message_user_to').value=='') {window.alert('Empfänger fehlt!');document.getElementById('message_user_to').focus();return false;}\">";
    echo "</form>";
}

function getInitialUser(Request $request, UserRepository $userRepository)
{
    // Wenn Username durch Link weitergegeben wird (z.b. Stats -> mail)
    if ($request->query->has('message_user_to')) {
        return $userRepository->getNick($request->query->getInt('message_user_to'));
    }

    // Username löschen falls auf "Weiterleiten" geklcikt wurde
    if ($request->request->has('remit')) {
        return '';
    }

    // Der Username wird übernommen wenn dieser angegeben ist
    if ($request->request->has('message_user_to')) {
        return rawurldecode($request->request->get('message_user_to'));
    }

    return '';
}

function getInitialSubject(Request $request)
{
    if ($request->query->has('message_subject')) {
        return base64_decode($_GET['message_subject'], true);
    }

    if ($request->request->has('message_subject')) {
        // Weiterleiten
        if ($request->request->has('remit')) {
            return 'Fw: ' . htmlentities($request->request->get('message_subject'), ENT_QUOTES, 'UTF-8');
        }

        // Antworten und "Re: " voran fügen, wenn dies nicht schon steht
        if ($request->request->has('answer')) {
            return 'Re: ' . htmlentities($request->request->get('message_subject'), ENT_QUOTES, 'UTF-8');
        }

        return htmlentities($request->request->get('message_subject'), ENT_QUOTES, 'UTF-8');
    }

    return '';
}

function getInitialMessageText(
    Request $request,
    MessageRepository $messageRepository,
    User $cu
): string {
    if ($request->request->has('message_text')) {
        if ($request->request->has('message_sender')) {
            return "\n\n[b]Nachricht von " . $request->request->get('message_sender') . ":[/b]\n\n" . htmlentities($request->request->get('message_text'), ENT_QUOTES, 'UTF-8');
        }
        return htmlentities($request->request->get('message_text'), ENT_QUOTES, 'UTF-8');
    }

    if ($request->query->has('message_text')) {
        $messageId = intval(base64_decode(stripslashes($request->query->get('message_text')), true));
        $messages = $messageRepository->findBy([
            'id' => $messageId,
            'user_to_id' => $cu->id,
        ]);
        if (count($messages) > 0) {
            if ($request->query->has('message_sender')) {
                return "\n\n[b]Nachricht von " . base64_decode($request->query->get('message_sender'), true) . ":[/b]\n\n" . htmlentities($messages[0]->text, ENT_QUOTES, 'UTF-8');
            }
            return "\n\n" . htmlentities($messages[0]->text, ENT_QUOTES, 'UTF-8');
        }
    }

    return '';
}

function submitSendMessage(
    Request $request,
    UserRepository $userRepository,
    MessageRepository $messageRepository,
    MessageIgnoreRepository $messageIgnoreRepository,
    User $cu,
    $dispatcher
): void {

    iBoxStart("Nachrichtenversand");

    $recipientNames = explode(";", rawurldecode($request->request->get('message_user_to')));
    foreach ($recipientNames as $recipientName) {
        echo sendMessage(
            $userRepository,
            $messageRepository,
            $messageIgnoreRepository,
            $cu->id,
            $recipientName,
            $request->request->get('message_subject'),
            $request->request->get('message_text'),
            $dispatcher,
        );
    }

    $request->request->remove('message_user_to');

    iBoxEnd();
}

function sendMessage(
    UserRepository $userRepository,
    MessageRepository $messageRepository,
    MessageIgnoreRepository $messageIgnoreRepository,
    int $senderId,
    string $recipientName,
    string $subject,
    string $text,
    $dispatcher
): string {

    $recipientUserId = $userRepository->getUserIdByNick($recipientName);
    if ($recipientUserId === null) {
        return "<b>Fehler:</b> Der Benutzer <b>" . $recipientName . "</b> existiert nicht!<br/>";
    }

    $flood_interval = time() - FLOOD_CONTROL;
    if (isset($_SESSION['messagesSent'][$recipientUserId]) && $_SESSION['messagesSent'][$recipientUserId] > $flood_interval) {
        return "<b>Flood-Kontrolle!</b> Du kannst erst nach " . FLOOD_CONTROL . " Sekunden eine neue Nachricht an " . $recipientName . " schreiben!<br/>";
    }

    if ($messageIgnoreRepository->isRecipientIgnoringSender($senderId, $recipientUserId)) {
        return "<b>Fehler:</b> Dieser Benutzer hat dich ignoriert, die Nachricht wurde nicht gesendet!<br/>";
    }

    $check_subject = check_illegal_signs($subject);
    if ($check_subject != "") {
        return "Du hast ein unerlaubtes Zeichen ( " . $check_subject . " ) im Betreff!<br/>";
    }

    $_SESSION['messagesSent'][$recipientUserId] = time();

    $messageRepository->sendFromUserToUser(
        $senderId,
        $recipientUserId,
        $subject,
        $text
    );

    $dispatcher->dispatch(new \EtoA\Message\Event\MessageSend(), \EtoA\Message\Event\MessageSend::SEND_SUCCESS);

    return "Nachricht wurde an <b>" . $recipientName . "</b> gesendet!<br>";
}
