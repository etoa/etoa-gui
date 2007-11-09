<?PHP

//Nachriten Vorschau
function messagesNewMessagePreview($val)
{
  	$objResponse = new xajaxResponse();
 	$objResponse->addAssign('msgPreview', 'innerHTML', text2html($val));

  	return $objResponse->getXML();
}

//Selektiert alle Nachrichten in einer Kategorie
function messagesSelectAllInCategory($cid,$cnt,$bv)
{
    $objResponse = new xajaxResponse();

    if ($bv=="-")
    {
	    for ($x=0;$x<$cnt;$x++)
		    $objResponse->addAssign("delcb_".$cid."_".$x, "checked","");
			$objResponse->addAssign("selectBtn[$cid]", "value","X");
    }
    else
    {
	    for ($x=0;$x<$cnt;$x++)
		    $objResponse->addAssign("delcb_".$cid."_".$x, "checked","true");

		$objResponse->addAssign("selectBtn[$cid]", "value","-");
	}

    return $objResponse->getXML();
}


$objAjax->registerFunction('messagesNewMessagePreview');
$objAjax->registerFunction('messagesSearchUser');
$objAjax->registerFunction('messagesSelectAllInCategory');




?>