<?PHP

$xajax->register(XAJAX_FUNCTION,"addToTechTree");
$xajax->register(XAJAX_FUNCTION,"removeFromTechTree");
$xajax->register(XAJAX_FUNCTION,"drawObjTechTree");

function addToTechTree($type,$id,$reqid,$reqlvl)
{
	$or = new xajaxResponse();
	ob_start();
	if ($reqid!="")
	{
		$reqlvl = intval($reqlvl);
		if ($reqlvl > 0)
		{
			$reqidexpl = explode(":",$reqid);
			if ($reqidexpl[0]=="t")
			{
				$f = "req_tech_id";
			}
			else
			{
				$f = "req_building_id";
			}
			dbquery("
			INSERT INTO
				".$type."
			(
				obj_id,
				$f,
				req_level
			)
			VALUES
			(
				".$id.",
				".$reqidexpl[1].",
				".$reqlvl."
			)
			ON DUPLICATE KEY UPDATE req_level=".$reqlvl."
			");
			$or->script("xajax_drawObjTechTree('$type',$id)");			
		}
		else
			$or->alert("Ungültige Stufe!");
	}
	else
	{
		$or->alert("Keine Bedingung gewählt!");
	}
	$out = ob_get_contents();
	ob_end_clean();	
	$or->append("item_container_".$id,"innerHTML",$out);
	return $or;	
}

function removeFromTechTree($type,$id,$rid)
{
	$or = new xajaxResponse();	
	ob_start();	
	dbquery("
	DELETE FROM
		".$type."
	WHERE
		id = ".$rid."
	LIMIT 1;
	");
	$or->script("xajax_drawObjTechTree('$type',$id)");			
	$out = ob_get_contents();
	ob_end_clean();	
	$or->append("item_container_".$id,"innerHTML",$out);
	return $or;		
}

function drawObjTechTree($type,$id)
{
	$or = new xajaxResponse();	
	ob_start();		
	drawTechTreeForSingleItem($type,$id);
	$out = ob_get_contents();
	ob_end_clean();	
	$or->assign("item_container_".$id,"innerHTML",$out);
	return $or;					
}

?>