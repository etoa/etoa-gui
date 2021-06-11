<?PHP

$xajax->register(XAJAX_FUNCTION,'imagePackInfo');
$xajax->register(XAJAX_FUNCTION,'designInfo');

function designInfo($did)
{
	$ajax = new xajaxResponse();
	$designs = get_designs();
	if (!empty($did) && isset($designs[$did])) {
		$cd = $designs[$did];
		$out = "
		<b>Version:</b> ".$cd['version']."<br/>
		<b>Geändert:</b> ".$cd['changed']."<br/>
		<b>Autor:</b> <a href=\"mailto:".$cd['email']."\">".$cd['author']."</a><br/>
		<b>Beschreibung:</b> ".$cd['description']."";
	}
	else
	{
		$out = '';
	}
	$ajax->assign("designInfo","innerHTML",$out);
  return $ajax;	
}

function imagePackInfo($pid,$ext="",$path="")
{
	$ajax = new xajaxResponse();
	if ($pid!="")
	{
		$packs = get_imagepacks();
		$cd = $packs[$pid];
		
		$out = "<b>Geändert:</b> ".$cd['changed']."<br/>
		<b>Autor:</b> <a href=\"mailto:".$cd['email']."\">".$cd['author']."</a><br/>";
		$ajax->assign("imagePackInfo","innerHTML",$out);
		$out = " Dateiendung: <select name=\"image_ext\">";
		foreach ($cd['extensions'] as $e)
		{
			$out.= "<option value=\"".$e."\"";
			if ($ext==$e) $out.=" selected=\"selected\"";
			$out.= ">".$e."</option>";
		}
		$out.="</select>";	
		$ajax->assign("imagePackExtension","innerHTML",$out);
	}
	else
	{
		$exts=array("jpg","jpeg","png","gif");
		$out = " Dateiendung: ";
		$out.= "<select name=\"image_ext\">";
		foreach ($exts as $e)
		{
			$out.= "<option value=\"".$e."\"";
			if ($ext==$e) $out.=" selected=\"selected\"";
			$out.= ">".$e."</option>";
		}
		$out.="</select>";	
		$ajax->assign("imagePackExtension","innerHTML",$out);
		$out = "Pfad: <input type=\"text\" name=\"image_url\" id=\"user_image_url\" maxlength=\"255\" size=\"45\" value=\"".$path."\">";
		$ajax->assign("imagePackInfo","innerHTML",$out);
	}
  return $ajax;	
}


?>
