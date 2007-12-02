<?PHP


function havenTargetInfo($f)
{

	$objResponse = new xajaxResponse();
	ob_start();
	

	$objResponse->addAppend("targetinfo","innerHTML",ob_get_contents());				
	ob_end_clean();
  return $objResponse->getXML();	
}



$objAjax->registerFunction('havenCheckShipCount');


?>