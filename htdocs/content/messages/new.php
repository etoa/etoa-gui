<?php

use Symfony\Component\HttpFoundation\Request;

/** @var \EtoA\Message\MessageRepository $messageRepository */
$messageRepository = $app[\EtoA\Message\MessageRepository::class];

/** @var \EtoA\User\UserRepository $userRepository */
$userRepository = $app['etoa.user.repository'];

/** @var Request */
$request = Request::createFromGlobals();

if (!$cu->isVerified)
{
    iBoxStart("Funktion gesperrt");
    echo "Solange deine E-Mail Adresse nicht bestätigt ist, kannst du keine Nachrichten versenden!";
    iBoxEnd();
}
else
{
    $previewNewMessage = $cu->properties->msgCreationPreview==1 ? true : false;

    if (!isset($_SESSION['messagesSent']))
    {
        $_SESSION['messagesSent'] = array();
    }

    if ($request->request->has('submit') && checker_verify())
    {
        $time = time();
        $rcpts = rawurldecode($request->request->get('message_user_to'));
        $rcptarr = explode(";", $rcpts);

        iBoxStart("Nachrichtenversand");
        foreach ($rcptarr as $rcpt)
        {
            $uid = $userRepository->getUserIdByNick($rcpt);
            if ($uid !== null) {
                // Prüfe Flooding
                $flood_interval = time() - FLOOD_CONTROL;
                if (!isset($_SESSION['messagesSent'][$uid]) || $_SESSION['messagesSent'][$uid] < $flood_interval)
                {
                    if (!$messageRepository->isRecipientIgnoringSender($cu->id, $uid))
                    {
                        // Prüfe Titel
                        $check_subject = check_illegal_signs($request->request->get('message_subject'));
                        if($check_subject=="")
                        {
                            $_SESSION['messagesSent'][$uid] = $time;

                            $messageRepository->sendFromUserToUser(
                                $cu->id,
                                $uid,
                                $request->request->get('message_subject'),
                                $request->request->get('message_text')
                            );

                            echo "Nachricht wurde an <b>".$rcpt."</b> gesendet! ";
                            $_POST['message_user_to']=null;
                            $app['dispatcher']->dispatch(new \EtoA\Message\Event\MessageSend(), \EtoA\Message\Event\MessageSend::SEND_SUCCESS);
                        }
                        else
                        {
                            echo "Du hast ein unerlaubtes Zeichen ( ".$check_subject." ) im Betreff!<br/>";
                        }
                    }
                    else
                    {
                        echo "<b>Fehler:</b> Dieser Benutzer hat dich ignoriert, die Nachricht wurde nicht gesendet!<br/>";
                    }
                }
                else
                {
                    echo "<b>Flood-Kontrolle!</b> Du kannst erst nach ".FLOOD_CONTROL." Sekunden eine neue Nachricht an ".$rcpt." schreiben!<br/>";
                }
            }
            else
            {
                echo "<b>Fehler:</b> Der Benutzer <b>".$rcpt."</b> existiert nicht!<br/>";
            }
        }
        iBoxEnd();
    }

    // User zuweisen
    // Wenn Username durch Link weitergegeben wird (z.b. Stats -> mail)
    if ($request->query->has('message_user_to'))
    {
        $user = $userRepository->getNick($request->query->getInt('message_user_to'));
    }
    // Username löschen falls auf "Weiterleiten" geklcikt wurde
    elseif ($request->request->has('remit'))
    {
        $user = '';
    }
    //Der Username wird übernommen wenn dieser angegeben ist
    elseif ($request->request->has('message_user_to'))
    {
        $user = rawurldecode($request->request->get('message_user_to'));
    }
    else
    {
        $user = '';
    }

    // Betreff zuweisen
    if ($request->query->has('message_subject'))
    {
        $subj = base64_decode($_GET['message_subject'], true);
    }
    elseif ($request->request->has('message_subject'))
    {
        // Weiterleiten
        if ($request->request->has('remit'))
        {
            $subj = 'Fw: '.htmlentities($request->request->get('message_subject'), ENT_QUOTES, 'UTF-8');
        }
        // Antworten und "Re: " voran fügen, wenn dies nicht schon steht
        elseif ($request->request->has('answer'))
        {
            $subj = 'Re: '.htmlentities($request->request->get('message_subject'), ENT_QUOTES, 'UTF-8');
        }
        else
        {
            $subj = htmlentities($request->request->get('message_subject'), ENT_QUOTES, 'UTF-8');
        }
    }
    else
    {
        $subj = '';
    }

    $text = '';
    if ($request->request->has('message_text'))
    {
        $text = $request->request->has('message_sender')
            ? "\n\n[b]Nachricht von ".$request->request->get('message_sender').":[/b]\n\n".htmlentities($request->request->get('message_text'), ENT_QUOTES,'UTF-8')
            : htmlentities($request->request->get('message_text'), ENT_QUOTES,'UTF-8');
    }
    elseif ($request->query->has('message_text'))
    {
        echo "--------------------";
        $sql = "SELECT text
            FROM message_data
            INNER JOIN messages ON id=message_id
                AND message_user_to='".$cu->id."'
                AND id='".intval(base64_decode(stripslashes($_GET['message_text']), true))."'
            LIMIT 1;";
        $mres = dbquery($sql);

        if ($request->query->has('message_sender'))
        {
            if (mysql_num_rows($mres))
            {
                $marr = mysql_fetch_array($mres);
                $text = "\n\n[b]Nachricht von ".base64_decode($_GET['message_sender'], true).":[/b]\n\n".htmlentities($marr['text'],ENT_QUOTES,'UTF-8');
            }
        }
        else
        {
            $text = "\n\n".htmlentities($marr['text'],ENT_QUOTES,'UTF-8')."";
        }
    }
    else
    {
        $text = '';
    }

    if ($cu->properties->msgSignature)
    {
        $text = "\n\n".$cu->properties->msgSignature.$text;
    }
    echo "<form action=\"?page=".$page."&mode=".$mode."\" method=\"POST\" name=\"msgform\">";
    checker_init();
    tableStart("Nachricht verfassen");
    echo "<tr>
            <th width=\"50\" valign=\"top\">Empf&auml;nger:</th>
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
                <input type=\"text\" name=\"message_subject\" value=\"".$subj."\"  style=\"width:97%\" maxlength=\"255\">
            </td>
            </tr>";
        echo "<tr>
            <th width=\"50\" valign=\"top\">Text:</th>
            <td width=\"250\"><textarea name=\"message_text\" id=\"message\" rows=\"12\" cols=\"60\" ";
            if ($previewNewMessage)
            {
                echo "onkeyup=\"text2html(this.value,'msgPreview');\"";
            }
            echo ">".$text.'</textarea><br/>'.helpLink('textformat', 'Hilfe zur Formatierung').'</td>';

            if ($previewNewMessage)
            {
                $prevstr="text2html(document.getElementById('message').value,'msgPreview');";
            }
            else
            {
                $prevstr="";
            }
            echo "<td>
            <input type=\"button\" onclick=\"bbcode(this.form,'b','');".$prevstr."\" value=\"B\" style=\"font-weight:bold;\">
            <input type=\"button\" onclick=\"bbcode(this.form,'i','');".$prevstr."\" value=\"I\" style=\"font-style:italic;\">
            <input type=\"button\" onclick=\"bbcode(this.form,'u','');".$prevstr."\" value=\"U\" style=\"text-decoration:underline\">
            <input type=\"button\" onclick=\"bbcode(this.form,'c','');".$prevstr."\" value=\"Center\" style=\"text-align:center\"> <br/><br/>
            <input type=\"button\" onclick=\"namedlink(this.form,'url');".$prevstr."\" value=\"Link\">
            <input type=\"button\" onclick=\"namedlink(this.form,'email');".$prevstr."\" value=\"E-Mail\">
            <input type=\"button\" onclick=\"bbcode(this.form,'img','http://');".$prevstr."\" value=\"Bild\"> <br/><br/>";
            ?>
            <select id="sizeselect" onchange="fontformat(this.form,this.options[this.selectedIndex].value,'size');" onclick="<?PHP echo $prevstr;?>">
                <option value="0">Gr&ouml;sse</option>
                <option value="7">winzig</option>
                <option value="10">klein</option>
                <option value="12">mittel</option>
                <option value="16">groß</option>
                <option value="20">riesig</option>
            </select>
            <select id="colorselect" onchange="fontformat(this.form,this.options[this.selectedIndex].value,'color');" onclick="<?PHP echo $prevstr;?>">
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
            echo "<a href=\"javascript:;\" onclick=\"addText(':-)', '', false, document.msgform);".$prevstr."\"><img src=\"".SMILIE_DIR."/smile.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(';-)', '', false, document.msgform);".$prevstr."\"><img src=\"".SMILIE_DIR."/wink.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':-P', '', false, document.msgform);".$prevstr."\"><img src=\"".SMILIE_DIR."/tongue.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':0', '', false, document.msgform);".$prevstr."\"><img src=\"".SMILIE_DIR."/laugh.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':-D', '', false, document.msgform);".$prevstr."\"><img src=\"".SMILIE_DIR."/biggrin.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;<br>
            <a href=\"javascript:;\" onclick=\"addText(':-(', '', false, document.msgform);".$prevstr."\"><img src=\"".SMILIE_DIR."/frown.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText('8-)', '', false, document.msgform);".$prevstr."\"><img src=\"".SMILIE_DIR."/cool.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':angry:', '', false, document.msgform);".$prevstr."\"><img src=\"".SMILIE_DIR."/angry.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':sad:', '', false, document.msgform);".$prevstr."\"><img src=\"".SMILIE_DIR."/sad.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':pst:', '', false, document.msgform);".$prevstr."\"><img src=\"".SMILIE_DIR."/pst.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;<br>
            <a href=\"javascript:;\" onclick=\"addText(':holy:', '', false, document.msgform);".$prevstr."\"><img src=\"".SMILIE_DIR."/holy.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':rolleyes:', '', false, document.msgform);".$prevstr."\"><img src=\"".SMILIE_DIR."/rolleyes.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;
            <a href=\"javascript:;\" onclick=\"addText(':anger:', '', false, document.msgform);".$prevstr."\"><img src=\"".SMILIE_DIR."/anger.gif\" style=\"border:none;\" alt=\"Smilie\" title=\"Smilie\"  /></a>&nbsp;";
            echo "</td>";
    echo "</tr>";
    if ($previewNewMessage)
    {
    echo "<tr>
                <th>Vorschau:</th>
                <td colspan=\"2\" id=\"msgPreview\">Vorschau wird geladen...</td>
            </tr>";
    }
    tableEnd();
    echo "<script type=\"text/javascript\">";
    if ($previewNewMessage)
    {
        echo "text2html(document.getElementById('message').value,'msgPreview');";
    }
    echo "document.getElementById('user_nick').focus()";
    echo "</script>";
    echo "<input type=\"submit\" name=\"submit\" value=\"Senden\" onclick=\"if (document.getElementById('message_user_to').value=='') {window.alert('Empf&auml;nger fehlt!');document.getElementById('message_user_to').focus();return false;}\">";
    echo "</form>";
}
