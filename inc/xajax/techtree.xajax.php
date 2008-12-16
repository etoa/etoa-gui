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
	
	
	if ($cat=='b')
	{	
		$req_tbl = "building_requirements";
		$req_field = "req_building_id";
	}
	elseif($cat=='t')
	{
		$req_tbl = "tech_requirements";
		$req_field = "req_tech_id";
	}
	elseif($cat=='s')
	{
		$req_tbl = "ship_requirements";
		$req_field = "req_ship_id";
	}
	elseif($cat=='d')
	{
		$req_tbl = "def_requirements";
		$req_field = "req_def_id";
	}		
	
	$items = array();
	$res = dbquery("SELECT * FROM $req_tbl WHERE $req_field=".$id." AND req_req_building_level>0 ORDER BY req_req_building_level;");
	$nr = mysql_num_rows($res);
	if ($nr>0)
	{
		while($arr=mysql_fetch_assoc($res))
		{
			$items[] = "<td style=\"padding:4px;border:1px solid #bbb;background:#eef;width:150px;\">
			<a href=\"javascript:;\" style=\"color:#00f\" onclick=\"xajax_reqInfo(".$arr['req_req_building_id'].",'b')\">
			<img src=\"".IMAGE_PATH."/buildings/building".$arr['req_req_building_id']."_small.".IMAGE_EXT."\" align=\"middle\"/>
			</a><br/>
	 		<b>".$bu_name[$arr['req_req_building_id']]."</b><br/>
	 		Stufe ".$arr['req_req_building_level']."			
			</td>";
		}
	}
	$res = dbquery("SELECT * FROM $req_tbl WHERE $req_field=".$id." AND req_req_tech_level>0 ORDER BY req_req_tech_level;");
	$nr2 = mysql_num_rows($res);
	if ($nr2>0)
	{
		while($arr=mysql_fetch_assoc($res))
		{
			$items[] = "<td style=\"padding:4px;border:1px solid #bbb;background:#efe;width:150px;\">
			<a href=\"javascript:;\" style=\"color:#00f\" onclick=\"xajax_reqInfo(".$arr['req_req_tech_id'].",'t')\">
			<img src=\"".IMAGE_PATH."/technologies/technology".$arr['req_req_tech_id']."_small.".IMAGE_EXT."\" align=\"middle\"/>
			</a><br/>
	 		<b>".$te_name[$arr['req_req_tech_id']]."</b><br/>
	 		Stufe ".$arr['req_req_tech_level']."			
			</td>";
		}
	}
	
	if (count($items)>0)
	{
		echo "<table style=\"margin:0px auto;color:#000\"><tr>";
		$cnt=0;
		foreach ($items as $i)
		{
			echo $i;
			$cnt++;
			if ($cnt==4)
			{
				echo "</tr><tr>";
				$cnt=0;
			}
		}
		if ($cnt<4)
		{
			for ($x=$cnt;$x<=4;$x++)
			{
				echo "<td></td>";
			}
		}
		echo "</tr></table>";
		echo "<br/>wird benötigt für<br/><br/>";		
	}
	
		
	if ($cat=='b')
	{	
		echo "<div style=\"position:relative;border:1px solid black;padding:0px;background:url('".IMAGE_PATH."/buildings/building".$id."_middle.".IMAGE_EXT."');color:#000;width:110px;height:110px;margin:10px auto;\">";
		echo "<div style=\"position:absolute;top:0px;background:#000;height:15px;width:100%;color:#fff;\">".$bu_name[$id]."</div>";
		//echo "<img src=\"".IMAGE_PATH."/buildings/building".$id."_small.".IMAGE_EXT."\" align=\"middle\"/><br/><b>".$bu_name[$id]."</b>";
		echo "</div><br/>";
	}
	elseif($cat=='t')
	{
		echo "<img src=\"".IMAGE_PATH."/technologies/technology".$id."_small.".IMAGE_EXT."\" align=\"middle\"/><br/>
		 	<b>".$te_name[$id]."</b>";
		echo "</div><br/>";
	}
	elseif($cat=='s')
	{
		echo "<img src=\"".IMAGE_PATH."/ships/ship".$id."_small.".IMAGE_EXT."\" align=\"middle\"/><br/>
		 	<b>".$sh_name[$id]."</b>";
		echo "</div><br/>";
	}
	elseif($cat=='d')
	{
		echo "<img src=\"".IMAGE_PATH."/defense/def".$id."_small.".IMAGE_EXT."\" align=\"middle\"/><br/>
		 	<b>".$de_name[$id]."</b>";
		echo "</div><br/>";
	}	
	
	if ($cat == 'b' || $cat == 't')
	{
		if ($cat=='b')
		{
			$req_field = "req_req_building_id";
			$req_level_field = "req_req_building_level";
		}
		elseif($cat=='t')
		{
			$req_field = "req_req_tech_id";
			$req_level_field = "req_req_tech_level";
		}


		$items = array();
		$res = dbquery("SELECT * FROM building_requirements WHERE ".$req_field."=".$id." ORDER BY ".$req_level_field.";");
		$nr = mysql_num_rows($res);
		if ($nr>0)
		{
			while($arr=mysql_fetch_assoc($res))
			{
				if (isset($bu_name[$arr['req_building_id']]))
				{
					$items[] =  "<td style=\"padding:4px;border:1px solid #bbb;background:#eef;width:150px;\">
					mit Stufe ".$arr[$req_level_field]."<br/><br/>
					<a href=\"javascript:;\" style=\"color:#00f\" onclick=\"xajax_reqInfo(".$arr['req_building_id'].",'b')\">
					<img src=\"".IMAGE_PATH."/buildings/building".$arr['req_building_id']."_small.".IMAGE_EXT."\" align=\"middle\"/>
					</a><br/>
			 		<b>".$bu_name[$arr['req_building_id']]."</b>			
					</td>";
				}
			}
		}
		$res = dbquery("SELECT * FROM tech_requirements WHERE ".$req_field."=".$id." ORDER BY ".$req_level_field.";");
		$nr = mysql_num_rows($res);
		if ($nr>0)
		{
			while($arr=mysql_fetch_assoc($res))
			{
				if (isset($te_name[$arr['req_tech_id']]))
				{
					$items[] =  "<td style=\"padding:4px;border:1px solid #bbb;background:#efe;width:150px;\">
					mit Stufe ".$arr[$req_level_field]."<br/><br/>
					<a href=\"javascript:;\" style=\"color:#00f\" onclick=\"xajax_reqInfo(".$arr['req_tech_id'].",'t')\">
					<img src=\"".IMAGE_PATH."/technologies/technology".$arr['req_tech_id']."_small.".IMAGE_EXT."\" align=\"middle\"/>
					</a><br/>
			 		<b>".$te_name[$arr['req_tech_id']]."</b>			
					</td>";
				}
			}
		}
		$res = dbquery("SELECT * FROM ship_requirements WHERE ".$req_field."=".$id." ORDER BY ".$req_level_field.";");
		$nr = mysql_num_rows($res);
		if ($nr>0)
		{
			while($arr=mysql_fetch_assoc($res))
			{
				if (isset($sh_name[$arr['req_ship_id']]))
				{
					$items[] =  "<td style=\"padding:4px;border:1px solid #bbb;background:#fee;width:150px;\">
					mit Stufe ".$arr[$req_level_field]."<br/><br/>
					<a href=\"javascript:;\" style=\"color:#00f\" onclick=\"xajax_reqInfo(".$arr['req_ship_id'].",'s')\">
					<img src=\"".IMAGE_PATH."/ships/ship".$arr['req_ship_id']."_small.".IMAGE_EXT."\" align=\"middle\"/>
					</a><br/>
			 		<b>".$sh_name[$arr['req_ship_id']]."</b>			
					</td>";
				}
			}
		}
		$res = dbquery("SELECT * FROM def_requirements WHERE ".$req_field."=".$id." ORDER BY ".$req_level_field.";");
		$nr = mysql_num_rows($res);
		if ($nr>0)
		{
			while($arr=mysql_fetch_assoc($res))
			{
				if (isset($de_name[$arr['req_def_id']]))
				{
					$items[] =  "<td style=\"padding:4px;border:1px solid #bbb;background:#ffe;width:150px;\">
					mit Stufe ".$arr[$req_level_field]."<br/><br/>
					<a href=\"javascript:;\" style=\"color:#00f\" onclick=\"xajax_reqInfo(".$arr['req_def_id'].",'d')\">
					<img src=\"".IMAGE_PATH."/defense/def".$arr['req_def_id']."_small.".IMAGE_EXT."\" align=\"middle\"/>
					</a><br/>
			 		<b>".$de_name[$arr['req_def_id']]."</b>			
					</td>";
				}
			}
		}	
		
		if (count($items)>0)
		{	
			echo "ermöglicht<br/><br/>
			<table style=\"margin:0px auto;color:#000\"><tr>";
			$cnt=0;
			foreach ($items as $i)
			{
				echo $i;
				$cnt++;
				if ($cnt==4)
				{
					echo "</tr><tr>";
					$cnt=0;
				}
			}
			if ($cnt<4)
			{
				for ($x=$cnt;$x<=4;$x++)
				{
					echo "<td></td>";
				}
			}
			echo "</tr></table>";
		}
	}


	echo "<br/><br/><table style=\"margin:0px auto;\"><tr>";
	echo "<td style=\"color:#000;padding:4px;border:1px solid #bbb;background:#eef;width:150px;\">Gebäude</td>";
	echo "<td style=\"color:#000;padding:4px;border:1px solid #bbb;background:#efe;width:150px;\">Technologie</td>";
	echo "<td style=\"color:#000;padding:4px;border:1px solid #bbb;background:#fee;width:150px;\">Schiff</td>";
	echo "<td style=\"color:#000;padding:4px;border:1px solid #bbb;background:#ffe;width:150px;\">Verteidigung</td>";
	echo "</tr></table>";
	
	$out=ob_get_clean();
	$or->assign('reqInfo','innerHTML',$out);	
	return $or;	
}

?>