<?PHP

$xajax->register(XAJAX_FUNCTION,'messagesNewMessagePreview');
$xajax->register(XAJAX_FUNCTION,'messagesSelectAllInCategory');

//Nachriten Vorschau
function messagesNewMessagePreview($val)
{
  $objResponse = new xajaxResponse();
 	$objResponse->assign('msgPreview', 'innerHTML', text2html($val));
 	return $objResponse;
}

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





?>