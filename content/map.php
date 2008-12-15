<?PHP

	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	File: space.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Space sector map
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	


	// Wenn Planet aktiv, Koordinaten aus der DB lesen
	if ($cp)
	{
		$sx_def = $cp->sx;
		$sy_def = $cp->sy;
	}
	// Sonst Standardkoordinaten (Zentrum der Galaxie)
	else
	{
		$sx_def = $cfg->param1('map_init_sector');
		$sy_def = $cfg->param2('map_init_sector');
	}

	echo "<h1>Sektor ".$sx_def."/".$sy_def."</h1>";

	$sector_pic = "".IMAGE_PATH."/map";

	$sx_num=$cfg->param1('num_of_sectors');
	$sy_num=$cfg->param2('num_of_sectors');
	$cx_num=$cfg->param1('num_of_cells');
	$cy_num=$cfg->param2('num_of_cells');
	$cell_width=$cfg->param1('space_cell_size');
	$cell_height=$cfg->param2('space_cell_size');


	$table_width = $cx_num * $cell_width;
	$table_height = $cx_num * $cell_height;
	$img_width = $cell_width;
	$img_height = $cell_height;

	if (isset($_POST['sx']) && intval($_POST['sx'])>0)
		$sx	= $_POST['sx'];
	elseif (isset($_GET['sx']) && intval($_GET['sx'])>0)
		$sx	= $_GET['sx'];
	else
		$sx = $sx_def;
	if (isset($_POST['sy']) && intval($_POST['sy'])>0)
		$sy	= $_POST['sy'];
	elseif (isset($_GET['sy']) && intval($_GET['sy'])>0)
		$sy	= $_GET['sy'];
	else
		$sy = $sy_def;

	if ($sx>$sx_num) $sx = $sx_num;
	if ($sy>$sy_num) $sy = $sy_num;

	if ($sx<1) $sx = 1;
	if ($sy<1) $sy = 1;

	$sx_tl = $sx-1;
	$sx_tc = $sx;
	$sx_tr = $sx+1;
	$sx_ml = $sx-1;
	$sx_mr = $sx+1;
	$sx_bl = $sx-1;
	$sx_bc = $sx;
	$sx_br = $sx+1;

	$sy_tl = $sy+1;
	$sy_tc = $sy+1;
	$sy_tr = $sy+1;
	$sy_ml = $sy;
	$sy_mr = $sy;
	$sy_bl = $sy-1;
	$sy_bc = $sy-1;
	$sy_br = $sy-1;


	// Lade Sonnensysteme des Users
  $res = dbquery("
  SELECT 
  	cells.id as id
  FROM 
  	planets
  INNER JOIN
  (
  	entities
  	INNER JOIN
  		cells 
  		ON cells.id=entities.cell_id
  )
 	ON entities.id=planets.id
  	AND planet_user_id='".$cu->id."';");

  while ($arr = mysql_fetch_row($res))
  {
  	$user_solsys_ids[]=$arr[0];
  }

	echo "<form action=\"?page=$page\" method=\"post\">";
	iBoxStart("Sektor wählen","450px;");
	echo "<b>Sektor:</b>&nbsp;";
	echo "<select name=\"sx\">";
	for ($x=1;$x<=$sx_num;$x++)
	{
		echo "<option value=\"$x\"";
		if ($x==$sx)echo " selected=\"selected\"";
		echo ">$x</option>";
	}
	echo "</select> / <select name=\"sy\">";
	for ($y=1;$y<=$sy_num;$y++)
	{
		echo "<option value=\"$y\"";
		if ($y==$sy)echo " selected=\"selected\"";
		echo ">$y</option>";
	}
	echo "</select>";
	echo "&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit_sector\" value=\"Anzeigen\" /> &nbsp; 
	<input type=\"button\" onclick=\"document.location='?page=galaxy'\" value=\"Galaxiegrafik\" />";
	iBoxEnd();

	iBoxStart("Sektorkarte","600px;");
	echo "<div style=\"background:#000;text-align:center;\">";
	echo "<table id=\"outerspacetbl\">";

	echo "<tr>";

	if ($sx_tl && $sy_tl!=0 && $sx_tl!=$sx_num+1 && $sy_tl!=$sy_num+1)
	{
		echo "<td width=\"42\" align=\"center\" height=\"42\"><a href=\"?page=$page&amp;sx=$sx_tl&amp;sy=$sy_tl\" title=\"Sektor $sx_tl/$sy_tl\" onmouseover=\"sector_topleft.src='$sector_pic/sector_topleft_On.gif';\" onmouseout=\"sector_topleft.src='$sector_pic/sector_topleft.gif';\">
		<img name=\"sector_topleft\" src=\"$sector_pic/sector_topleft.gif\" height=\"42\" width=\"42\" border=\"0\">
		</a></td>";
	}
	else
	{
		echo "<td>&nbsp;</td>";
	}
	if ($sx_tc && $sy_tc!=0 && $sx_tc!=$sx_num+1 && $sy_tc!=$sy_num+1)
	{
		echo "<td style=\"width:".$table_width."px;text-align:center;height:42px;\">
			<a href=\"?page=$page&amp;sx=$sx_tc&amp;sy=$sy_tc\" alt=\"Sektor $sx_tc/$sy_tc\" title=\"Sektor $sx_tc/$sy_tc\" onmouseover=\"sector_topcenter.src='$sector_pic/sector_topcenter_On.gif';\" onmouseout=\"sector_topcenter.src='$sector_pic/sector_topcenter.gif';\"/>
			<img name=\"sector_topcenter\" src=\"$sector_pic/sector_topcenter.gif\" height=\"42\" width=\"42\" border=\"0\">
		</a></td>";
	}
	else
	{
		echo "<td style=\"width:".$table_width."px;text-align:center;height:20px;\">&nbsp;</td>";
	}
	if ($sx_tr && $sy_tr!=0 && $sx_tr!=$sx_num+1 && $sy_tr!=$sy_num+1)
	{
		echo "<td width=\"42\" align=\"center\" height=\"42\"><a href=\"?page=$page&amp;sx=$sx_tr&amp;sy=$sy_tr\" width=\"20\" height=\"20\" border=\"0\" alt=\"Sektor $sx_tr/$sy_tr\" title=\"Sektor $sx_tr/$sy_tr\" onmouseover=\"sector_topright.src='$sector_pic/sector_topright_On.gif';\" onmouseout=\"sector_topright.src='$sector_pic/sector_topright.gif';\"><img name=\"sector_topright\" src=\"$sector_pic/sector_topright.gif\" height=\"42\" width=\"42\" border=\"0\"></a></td>";
	}
	else
	{
		echo "<td>&nbsp;</td>";
	}
	echo "</tr>";

	echo "<tr>";
	if ($sx_ml && $sy_ml!=0 && $sx_ml!=$sx_num+1 && $sy_ml!=$sy_num+1)
	{
		echo "<td width=\"20\" align=\"center\" height=\"$table_height\"><a href=\"?page=$page&amp;sx=$sx_ml&amp;sy=$sy_ml\" width=\"20\" height=\"20\" border=\"0\" alt=\"Sektor $sx_ml/$sy_ml\" title=\"Sektor $sx_ml/$sy_ml\"  onmouseover=\"sector_middleleft.src='$sector_pic/sector_middleleft_On.gif';\" onmouseout=\"sector_middleleft.src='$sector_pic/sector_middleleft.gif';\"><img name=\"sector_middleleft\" src=\"$sector_pic/sector_middleleft.gif\" height=\"42\" width=\"42\" border=\"0\"></a></td>";
	}
	else
	{
		echo "<td width=\"20\" align=\"center\" height=\"$table_height\">&nbsp;</td>";
	}
	
		echo "<td  style=\"width:".$table_width."px;height:".$table_height."px;\">";
		echo "\n<table style=\"width:".$table_width."px\" id=\"innerspacetbl\">\n";
		echo "<colgroup width=\"$cell_width\" span=\"$cx_num\" align=\"center\" valign=\"middle\"></colgroup>\n";
		$res = dbquery("
		SELECT 
			cx,
			cy,
			cells.id as cid,
			entities.id as eid,
			code
		FROM 
			cells 
		INNER JOIN
			entities
			ON entities.cell_id=cells.id
			AND entities.pos=0
			AND sx='$sx' 
			AND sy='$sy';");
		$cells = array();
		while ($arr = mysql_fetch_assoc($res))
		{
			$cells[$arr['cx']][$arr['cy']]['cid']=$arr['cid'];
			$cells[$arr['cx']][$arr['cy']]['eid']=$arr['eid'];
			$cells[$arr['cx']][$arr['cy']]['code']=$arr['code'];
		}
		for ($y=0;$y<$cx_num;$y++)
		{
			$ycoords = $cy_num-$y;
			
			$counter_left="".IMAGE_PATH."/map/GalaxyFrameCounterLeft";
			$counter_left_high="".IMAGE_PATH."/map/GalaxyFrameCounterLeftHighlight";

			$counter_bottom="".IMAGE_PATH."/map/GalaxyFrameCounterBottom";
			$counter_bottom_high="".IMAGE_PATH."/map/GalaxyFrameCounterBottomHighlight";

			echo "<td class=\"coordstbl\"> <img name=\"counter_left_$ycoords\" src=\"$counter_left$ycoords.gif\" style=\"height:40px;\"/> </td>";

			for ($x=0;$x<$cy_num;$x++)
			{
				$xcoords = $x+1;				
				
				/** Creating the cell object
				$cell = new Cell($cells[$xcoords][$ycoords]['cid']);
				*/

				if ($cp->id()==$cells[$xcoords][$ycoords]['eid'])
				{
					echo "<td class=\"spaceCellSelected\" onmouseover=\"counter_left_$ycoords.src='$counter_left_high$ycoords.gif';counter_bottom_$xcoords.src='$counter_bottom_high$xcoords.gif';\" onmouseout=\"counter_left_$ycoords.src='$counter_left$ycoords.gif';counter_bottom_$xcoords.src='$counter_bottom$xcoords.gif';\">";
				}
				elseif (in_array($cells[$xcoords][$ycoords]['cid'],$user_solsys_ids) && $cells[$xcoords][$ycoords]['eid']!=$cp->id())
				{
					if ($cu->discovered((($sx - 1) * $cx_num) + $xcoords,(($sy - 1) * $cy_num) + $ycoords)==0)
					{
						$cu->setDiscovered((($sx - 1) * $cx_num) + $xcoords,(($sy - 1) * $cy_num) + $ycoords);
					}
					/* With the cell object
					if ($cu->discovered($cell->absX(),$cell->absY())==0)
					{
						$cu->setDiscovered($cell->absX(),$cell->absY());
					}*/
					echo "<td class=\"spaceCellUser\" onmouseover=\"counter_left_$ycoords.src='$counter_left_high$ycoords.gif';counter_bottom_$xcoords.src='$counter_bottom_high$xcoords.gif';\" onmouseout=\"counter_left_$ycoords.src='$counter_left$ycoords.gif';counter_bottom_$xcoords.src='$counter_bottom$xcoords.gif';\">";
				}
				else
				{
					echo "<td class=\"spaceCell\" onmouseover=\"counter_left_$ycoords.src='$counter_left_high$ycoords.gif';counter_bottom_$xcoords.src='$counter_bottom_high$xcoords.gif';\" onmouseout=\"counter_left_$ycoords.src='$counter_left$ycoords.gif';counter_bottom_$xcoords.src='$counter_bottom$xcoords.gif';\">";
				}
				
				// Symbole anzeigen
				if ($cu->discovered((($sx - 1) * $cx_num) + $xcoords,(($sy - 1) * $cy_num) + $ycoords))
				{
				/*With the cell object
				if ($cu->discovered($cell->absX(),$cell->absY()))
				{		*/
					$ent = Entity::createFactory($cells[$xcoords][$ycoords]['code'],$cells[$xcoords][$ycoords]['eid']);
					
					$tt = new Tooltip();
					$tt->addTitle($ent->entityCodeString());
					$tt->addText("Position: $sx/$sy : $xcoords/$ycoords");
					$tt->addComment($ent->name());
					echo "<a href=\"?page=cell&amp;id=".$cells[$xcoords][$ycoords]['cid']."\" ".$tt.">
						<img src=\"".$ent->imagePath()."\" style=\"border:none;background:#000;width:".$img_width."px;height:".$img_height."px\" />
					</a>";
					unset($ent);
				}
				else
				{
					$tt = new Tooltip();
					$tt->addTitle("Unerforschte Raumzelle!");
					$tt->addText("Position: $sx/$sy : $xcoords/$ycoords");
					$tt->addComment("Expedition senden um Zelle sichtbar zu machen.");

					echo "<a href=\"?page=haven&cellTarget=".$cells[$xcoords][$ycoords]['cid']."\" ".$tt.">
						<img src=\"".IMAGE_PATH."/unexplored/ue1.png\" style=\"border:none;background:#000;width:".$img_width."px;height:".$img_height."px\" />
					</a>";
				}
				echo "</td>\n";
			}
			echo "</tr>\n";
		}
		echo "<tr><td class=\"coordstbl\">&nbsp;</td>"; // Linke untere ecke
		for ($x=0;$x<$cy_num;$x++)
		{
			$xcoords = $x+1;
		  echo "<td class=\"coordstbl\"><img name=\"counter_bottom_$xcoords\" src=\"$counter_bottom$xcoords.gif\"/></td>";
		}
		echo "</tr>";
		echo "</table></td>";
		if ($sx_mr && $sy_mr!=0 && $sx_mr!=$sx_num+1 && $sy_mr!=$sy_num+1)
			echo "<td width=\"20\" align=\"center\" height=\"$table_height\"><a href=\"?page=$page&amp;sx=$sx_mr&amp;sy=$sy_mr\"  width=\"20\" height=\"20\" border=\"0\" alt=\"Sektor $sx_mr/$sy_mr\" title=\"Sektor $sx_mr/$sy_mr\" onmouseover=\"sector_middleright.src='$sector_pic/sector_middleright_On.gif';\" onmouseout=\"sector_middleright.src='$sector_pic/sector_middleright.gif';\"><img name=\"sector_middleright\" src=\"$sector_pic/sector_middleright.gif\" height=\"42\" width=\"42\" border=\"0\"></a></td>";
		else
			echo "<td width=\"20\" align=\"center\" height=\"$table_height\">&nbsp;</td>";
		echo "</tr><tr>";

		if ($sx_bl && $sy_bl!=0 && $sx_bl!=$sx_num+1 && $sy_bl!=$sy_num+1)
			echo "<td><a href=\"?page=$page&amp;sx=$sx_bl&amp;sy=$sy_bl\" width=\"20\" height=\"20\" border=\"0\" alt=\"Sektor $sx_bl/$sy_bl\" title=\"Sektor $sx_bl/$sy_bl\" onmouseover=\"sector_bottomleft.src='$sector_pic/sector_bottomleft_On.gif';\" onmouseout=\"sector_bottomleft.src='$sector_pic/sector_bottomleft.gif';\"/><img name=\"sector_bottomleft\" src=\"$sector_pic/sector_bottomleft.gif\" height=\"42\" width=\"42\" border=\"0\"></a></td>";
		else
			echo "<td>&nbsp;</td>";
		if ($sx_bc && $sy_bc!=0 && $sx_bc!=$sx_num+1 && $sy_bc!=$sy_num+1)
			echo "<td width=\"$table_width\" align=\"center\" height=\"20\"><a href=\"?page=$page&amp;sx=$sx_bc&amp;sy=$sy_bc\" width=\"20\" height=\"20\" border=\"0\" alt=\"Sektor $sx_bc/$sy_bc\" title=\"Sektor $sx_bc/$sy_bc\" onmouseover=\"sector_bottomcenter.src='$sector_pic/sector_bottomcenter_On.gif';\" onmouseout=\"sector_bottomcenter.src='$sector_pic/sector_bottomcenter.gif';\"/><img name=\"sector_bottomcenter\" src=\"$sector_pic/sector_bottomcenter.gif\" height=\"42\" width=\"42\" border=\"0\"></a></td>";
		else
			echo "<td width=\"$table_width\" align=\"center\" height=\"20\">&nbsp;</td>";
		if ($sx_br && $sy_br!=0 && $sx_br!=$sx_num+1 && $sy_br!=$sy_num+1)
			echo "<td><a href=\"?page=$page&amp;sx=$sx_br&amp;sy=$sy_br\" width=\"20\" height=\"20\" border=\"0\" alt=\"Sektor $sx_br/$sy_br\" title=\"Sektor $sx_br/$sy_br\" onmouseover=\"sector_bottomright.src='$sector_pic/sector_bottomright_On.gif';\" onmouseout=\"sector_bottomright.src='$sector_pic/sector_bottomright.gif';\"/><img name=\"sector_bottomright\" src=\"$sector_pic/sector_bottomright.gif\" height=\"42\" width=\"42\" border=\"0\"></a></td>";
		else
			echo "<td>&nbsp;</td>";
		echo "</tr></table></form>";
		echo "<br/>Die Galaxie besteht aus $sx_num x $sy_num Sektoren.<br/><br/>";
		echo "</div>";
		iBoxEnd();

?>
