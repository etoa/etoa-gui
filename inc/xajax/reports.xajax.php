<?PHP

$xajax->register(XAJAX_FUNCTION,'reportSetRead');

function reportSetRead($id)
{
  $or = new xajaxResponse();	
	$r = Report::createFactory($id);
	$r->read = true;
  $or->assign("repimg".$id,"src","images/pm_normal.gif");
	return $or;
}


?>