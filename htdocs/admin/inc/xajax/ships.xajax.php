<?PHP

$xajax->register(XAJAX_FUNCTION,"calcShipLevelFromXP");


function calcShipLevelFromXP($base_xp,$base_xp_factor,$current_xp,$target)
{
	$objResponse = new xajaxResponse();

	$level=1;
	while(true)
	{
		$xp = ceil($base_xp * pow($base_xp_factor,$level-1));
		if ($xp > $current_xp || $level>100)
			break;
		$level++;
	}

  $objResponse->assign($target,"innerHTML", $level);
	return $objResponse;
}
?>
