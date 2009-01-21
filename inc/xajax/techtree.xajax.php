<?PHP
$xajax->register(XAJAX_FUNCTION,"reqInfo");


function reqInfo($id,$cat='b')
{
	$or = new xajaxResponse();
	ob_start();
	
	// Load items
	$bures = dbquery("SELECT building_id,building_name FROM buildings WHERE building_show=1;");
	while ($buarr = mysql_fetch_array($bures))
	{
		$bu_name[$buarr['building_id']]=$buarr['building_name'];
	}		
	$teres = dbquery("SELECT tech_id,tech_name FROM technologies WHERE tech_show=1;");
	while ($tearr = mysql_fetch_array($teres))
	{
		$te_name[$tearr['tech_id']]=$tearr['tech_name'];
	}	

	$teres = dbquery("SELECT ship_id,ship_name FROM ships WHERE ship_show=1 AND special_ship=0;");
	while ($tearr = mysql_fetch_array($teres))
	{
		$sh_name[$tearr['ship_id']]=$tearr['ship_name'];
	}	
	
	$teres = dbquery("SELECT def_id,def_name FROM defense WHERE def_show=1;");
	while ($tearr = mysql_fetch_array($teres))
	{
		$de_name[$tearr['def_id']]=$tearr['def_name'];
	}		
	
	//
	// Required objects
	//
	
	if ($cat=='b')
	{	
		$req_tbl = "building_requirements";
		$req_field = "obj_id";
	}
	elseif($cat=='t')
	{
		$req_tbl = "tech_requirements";
		$req_field = "obj_id";
	}
	elseif($cat=='s')
	{
		$req_tbl = "ship_requirements";
		$req_field = "obj_id";
	}
	elseif($cat=='d')
	{
		$req_tbl = "def_requirements";
		$req_field = "obj_id";
	}		
	
	$items = array();
	$res = dbquery("SELECT * FROM $req_tbl WHERE obj_id=".$id." AND req_building_id>0 AND req_level>0 ORDER BY req_level;");
	$nr = mysql_num_rows($res);
	if ($nr>0)
	{
		while($arr=mysql_fetch_assoc($res))
		{
			$items[] = array($arr['req_building_id'],$bu_name[$arr['req_building_id']],$arr['req_level'],IMAGE_PATH."/buildings/building".$arr['req_building_id']."_middle.".IMAGE_EXT,"xajax_reqInfo(".$arr['req_building_id'].",'b')");
		}
	}
	$res = dbquery("SELECT * FROM $req_tbl WHERE $req_field=".$id." AND req_tech_id>0 AND req_level>0 ORDER BY req_level;");
	$nr2 = mysql_num_rows($res);
	if ($nr2>0)
	{
		while($arr=mysql_fetch_assoc($res))
		{
			$items[] = array($arr['req_tech_id'],$te_name[$arr['req_tech_id']],$arr['req_level'],IMAGE_PATH."/technologies/technology".$arr['req_tech_id']."_middle.".IMAGE_EXT,"xajax_reqInfo(".$arr['req_tech_id'].",'t')");
		}
	}
	
	if (count($items)>0)
	{
		echo "<div class=\"techtreeItemContainer\">";
		foreach ($items as $i)
		{
			echo "<div class=\"techtreeItem\" style=\"background:url('".$i[3]."');\">
			<div class=\"techtreeItemLevel\">Lvl <b>".$i[2]."</b></div>	
			<a href=\"javascript:;\" onclick=\"".$i[4]."\" style=\"height:100%;display:block;\"></a>			
			<div class=\"techtreeItemName\">".$i[1]."</div>				
			</div>";
		}
		echo "<br style=\"clear:both;\"";
		echo "</div>";		
		
		echo "<div style=\"margin:0px auto;\">wird benötigt für</div>";		
	}
	
	//
	// Current object
	//
		
	if ($cat=='b')
	{	
		$img = IMAGE_PATH."/buildings/building".$id."_middle.".IMAGE_EXT;
		$name = $bu_name[$id];
	}
	elseif($cat=='t')
	{
		$img = IMAGE_PATH."/technologies/technology".$id."_middle.".IMAGE_EXT;
		$name = $te_name[$id];
	}
	elseif($cat=='s')
	{
		$img = IMAGE_PATH."/ships/ship".$id."_middle.".IMAGE_EXT;
		$name = $sh_name[$id];
	}
	elseif($cat=='d')
	{
		$img = IMAGE_PATH."/defense/def".$id."_middle.".IMAGE_EXT;
		$name = $de_name[$id];
	}	
	echo "<div class=\"techtreeMainItem\" style=\"background:url('".$img."');\">";
	echo "<div class=\"techtreeItemName\">".$name."</div>";
	echo "</div>";	
	
	//
	// Allowed objects
	// 
	
	if ($cat == 'b' || $cat == 't')
	{
		if ($cat=='b')
		{
			$req_field = "req_building_id";
			$req_level_field = "req_level";
		}
		elseif($cat=='t')
		{
			$req_field = "req_tech_id";
			$req_level_field = "req_level";
		}


		$items = array();
		$res = dbquery("SELECT * FROM building_requirements WHERE ".$req_field."=".$id." ORDER BY ".$req_level_field.";");
		$nr = mysql_num_rows($res);
		if ($nr>0)
		{
			while($arr=mysql_fetch_assoc($res))
			{
				if (isset($bu_name[$arr['obj_id']]))
				{
					$items[] = array($arr['obj_id'],$bu_name[$arr['obj_id']],$arr[$req_level_field],IMAGE_PATH."/buildings/building".$arr['obj_id']."_middle.".IMAGE_EXT,"xajax_reqInfo(".$arr['obj_id'].",'b')");
				}
			}
		}
		$res = dbquery("SELECT * FROM tech_requirements WHERE ".$req_field."=".$id." ORDER BY ".$req_level_field.";");
		$nr = mysql_num_rows($res);
		if ($nr>0)
		{
			while($arr=mysql_fetch_assoc($res))
			{
				if (isset($te_name[$arr['obj_id']]))
				{
					$items[] = array($arr['obj_id'],$te_name[$arr['obj_id']],$arr[$req_level_field],IMAGE_PATH."/technologies/technology".$arr['obj_id']."_middle.".IMAGE_EXT,"xajax_reqInfo(".$arr['obj_id'].",'t')");
				}
			}
		}
		$res = dbquery("SELECT * FROM ship_requirements WHERE ".$req_field."=".$id." ORDER BY ".$req_level_field.";");
		$nr = mysql_num_rows($res);
		if ($nr>0)
		{
			while($arr=mysql_fetch_assoc($res))
			{
				if (isset($sh_name[$arr['obj_id']]))
				{
					$items[] = array($arr['obj_id'],$sh_name[$arr['obj_id']],$arr[$req_level_field],IMAGE_PATH."/ships/ship".$arr['obj_id']."_middle.".IMAGE_EXT,"xajax_reqInfo(".$arr['obj_id'].",'s')");
				}
			}
		}
		$res = dbquery("SELECT * FROM def_requirements WHERE ".$req_field."=".$id." ORDER BY ".$req_level_field.";");
		$nr = mysql_num_rows($res);
		if ($nr>0)
		{
			while($arr=mysql_fetch_assoc($res))
			{
				if (isset($de_name[$arr['obj_id']]))
				{
					$items[] = array($arr['obj_id'],$de_name[$arr['obj_id']],$arr[$req_level_field],IMAGE_PATH."/defense/def".$arr['obj_id']."_middle.".IMAGE_EXT,"xajax_reqInfo(".$arr['obj_id'].",'d')");
				}
			}
		}	
		
		if (count($items)>0)
		{	
			echo "<div style=\"margin:10px auto;\">ermöglicht</div>";

			echo "<div class=\"techtreeItemContainer\">";
			$cnt = 0;
			foreach ($items as $i)
			{
				echo "<div class=\"techtreeItem\" style=\"background:url('".$i[3]."');\">
				<div class=\"techtreeItemLevel\">Ab Lvl <b>".$i[2]."</b></div>	
				<a href=\"javascript:;\" onclick=\"".$i[4]."\" style=\"height:100%;display:block;\"></a>			
				<div class=\"techtreeItemName\">".$i[1]."</div>				
				</div>";
				$cnt++;
				
			}
			echo "<br style=\"clear:both;\"";
			echo "</div>";
		}
	}


	$out=ob_get_clean();
	$or->assign('reqInfo','innerHTML',$out);	
	return $or;	
}

?>