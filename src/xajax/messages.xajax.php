<?php

use EtoA\Message\MessageRepository;

$xajax->register(XAJAX_FUNCTION, 'messagesSelectAllInCategory');
$xajax->register(XAJAX_FUNCTION, 'messagesSetRead');

//Selektiert alle Nachrichten in einer Kategorie
function messagesSelectAllInCategory($cid, $cnt, $bv)
{
    $objResponse = new xajaxResponse();

    if ($bv == "-") {
        for ($x = 0; $x < $cnt; $x++) {
            $objResponse->assign("delcb_" . $cid . "_" . $x, "checked", "");
        }
        $objResponse->assign("selectBtn[$cid]", "value", "X");
    } else {
        for ($x = 0; $x < $cnt; $x++) {
            $objResponse->assign("delcb_" . $cid . "_" . $x, "checked", "true");
        }
        $objResponse->assign("selectBtn[$cid]", "value", "-");
    }
    return $objResponse;
}

function messagesSetRead($mid)
{
    $or = new xajaxResponse();

    // TODO
    global $app;

    /** @var MessageRepository $messageRepository */
    $messageRepository = $app[MessageRepository::class];

    $messageRepository->setRead($mid);

    $or->assign("msgimg" . $mid, "src", "images/pm_normal.gif");

    return $or;
}
