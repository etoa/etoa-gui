<?PHP

$xajax->register(XAJAX_FUNCTION,'messagesSelectAllInCategory');
$xajax->register(XAJAX_FUNCTION,'messagesSetRead');

//Selektiert alle Nachrichten in einer Kategorie
function messagesSelectAllInCategory($cid,$cnt,$bv)
{
    $objResponse = new xajaxResponse();

    if ($bv=="-")
    {
	    for ($x=0;$x<$cnt;$x++)
	    {
		    $objResponse->assign("delcb_".$cid."_".$x, "checked","");
		  }
			$objResponse->assign("selectBtn[$cid]", "value","X");
    }
    else
    {
	    for ($x=0;$x<$cnt;$x++)
	    {
		    $objResponse->assign("delcb_".$cid."_".$x, "checked","true");
		  }
			$objResponse->assign("selectBtn[$cid]", "value","-");
		}
    return $objResponse;
}

function messagesSetRead($mid)
{
  $or = new xajaxResponse();
  dbquery("UPDATE
  	messages
  SET
  	message_read=1
  WHERE
  	message_id=".$mid."
  LIMIT 1;");
  $or->assign("msgimg".$mid,"src","images/pm_normal.gif");
	return $or;
}


?>
