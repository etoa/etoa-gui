<?php

use EtoA\Chat\ChatBanRepository;
use EtoA\Chat\ChatRepository;
use EtoA\Chat\ChatUserRepository;

$xajax->register(XAJAX_FUNCTION,'loadChat');
$xajax->register(XAJAX_FUNCTION,'showChatUsers');
$xajax->register(XAJAX_FUNCTION,'showBannedChatUsers');


function loadChat($minId)
{
    global $app;
    /** @var ChatRepository $chatRepository */
    $chatRepository = $app[ChatRepository::class];

	$minId = intval($minId);
	$ajax = new xajaxResponse();

	$messages = $chatRepository->getMessagesAfter($minId);
    $out='';
    $lastid = null;
    foreach ($messages as $message) {
        if ($message->admin == 1)
            $adminstr = "<img src=\"../images/star_y.gif\" />";
        else
            $adminstr = "";

        if ($message->userId === 0) {
            $out .= "<span style=\"color:#aaa\">";
            $out .= "&lt;" . date("H:i", $message->timestamp) . "&gt; " . stripslashes($message->text);
            $out .= "</span><br/>";
        } elseif ($message->color != "") {
            $out .= "<span style=\"color:" . $message->color . "\">";
            $out .= "$adminstr&lt;<a style=\"color:" . $message->color . "\" href=\"?page=user&amp;sub=edit&amp;id=" . $message->userId . "\">" . $message->nick . "</a> | " . date("H:i", $message->timestamp) . "&gt; " . stripslashes($message->text);
            $out .= "</span><br/>";
        } else {
            $out .= "$adminstr&lt;<a style=\"color:#fff\" href=\"?page=user&amp;sub=edit&amp;id=" . $message->userId . "\">" . $message->nick . "</a> | " . date("H:i", $message->timestamp) . "&gt; " . stripslashes($message->text) . "<br/>";
        }
        $lastid = $message->id;
    }

    if ($lastid !== null) {
        $ajax->append("chatitems","innerHTML",$out);
        $ajax->assign("lastid","innerHTML",$lastid);
        $ajax->script('document.getElementById("chatitems").scrollTop = document.getElementById("chatitems").scrollHeight;');
    }

    $ajax->script("setTimeout(\"xajax_loadChat(document.getElementById('lastid').innerHTML)\",1000);");

    return $ajax;
}

function showChatUsers()
{
    global $app;

    /** @var ChatUserRepository $chatUserRepository */
    $chatUserRepository = $app[ChatUserRepository::class];
    $chatUsers = $chatUserRepository->getChatUsers();

	$ajax = new xajaxResponse();
	$out="";
	if (count($chatUsers) > 0) {
		foreach ($chatUsers as $chatUser) {
			$out.= "<a href=\"?page=user&amp;sub=edit&amp;id=".$chatUser->id."\">
			".$chatUser->nick."</a> ".date("H:i:s",$chatUser->timestamp)."
			<a href=\"?page=chat&amp;kick=".$chatUser->id."\">Kick</a>
			<a href=\"?page=chat&amp;ban=".$chatUser->id."\">Ban</a>
			<a href=\"?page=chat&amp;del=".$chatUser->id."\">Del</a>
<br/>";
		}
	}
	else
		$out.="Keine User online!<br/>";
	$ajax->assign("chatuserlist","innerHTML",$out);

	$ajax->script("setTimeout(\"xajax_showChatUsers();\",1000);");
  return $ajax;
}

function showBannedChatUsers()
{
    global $app;
    /** @var ChatBanRepository $chatBanRepository */
    $chatBanRepository = $app[ChatBanRepository::class];

	$ajax = new xajaxResponse();
	$bans = $chatBanRepository->getBans();
	$out="";
	if (count($bans) > 0) {
		$out.= "<ul>";
		foreach ($bans as $ban) {
			$out.= "<li><a href=\"?page=user&amp;sub=edit&amp;id=".$ban->userId."\">".$ban->userNick."</a>
			 ".$ban->reason." (".date("H:i:s",$ban->timestamp).")
			<a href=\"?page=chat&amp;unban=".$ban->userId."\">Unbannen</a></li>";
		}
		$out.="</ul>";
	} else {
        $out.="Keine User gebannt!<br/>";
    }

	$ajax->assign("bannedchatuserlist","innerHTML",$out);

	$ajax->script("setTimeout(\"xajax_showBannedChatUsers();\",10000);");

    return $ajax;
}
