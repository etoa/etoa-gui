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
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	


	$c = $planets->getCurrentData();

	echo "<h1>Raumkarte</h1>";

	// Wenn Planet aktiv, Koordinaten aus der DB lesen
	if ($planets->current->solsys_id>0)
	{
		$sx_def = $planets->current->sx;
		$sy_def = $planets->current->sy;
	}
	// Sonst Standardkoordinaten (Zentrum der Galaxie)
	else
	{
		$sx_def = $conf['map_init_sector']['p1'];
		$sy_def = $conf['map_init_sector']['p2'];
	}

	$sector_pic = "".IMAGE_PATH."/galaxy";

	$sx_num=$conf['num_of_sectors']['p1'];
	$sy_num=$conf['num_of_sectors']['p2'];
	$cx_num=$conf['num_of_cells']['p1'];
	$cy_num=$conf['num_of_cells']['p2'];
	$cell_width=$conf['space_cell_size']['p1'];
	$cell_height=$conf['space_cell_size']['p2'];

	$table_width = $cx_num * $cell_width;
	$table_height = $cx_num * $cell_height;
	$img_width = $cell_width;
	$img_height = $cell_height;

	if (intval($_POST['sx'])>0)
		$sx	= $_POST['sx'];
	elseif (intval($_GET['sx'])>0)
		$sx	= $_GET['sx'];
	else
		$sx = $sx_def;
	if (intval($_POST['sy'])>0)
		$sy	= $_POST['sy'];
	elseif (intval($_GET['sy'])>0)
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
  	planet_solsys_id
  FROM 
  	".$db_table['planets']."
  WHERE
  	planet_user_id='".$s['user']['id']."';");

  while ($arr = mysql_fetch_array($res))
  {
      $user_solsys_id[]=$arr['planet_solsys_id'];
  }

	echo "<form action=\"?page=$page\" method=\"post\">";
	echo "<div style=\"text-align:center;\">Die Galaxie besteht aus $sx_num x $sy_num Sektoren.<br/><br/><b>Sektor:</b>&nbsp;";

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
	//echo "<input type=\"text\" name=\"sx\" value=\"$sx\" maxlength=\"3\" size=\"2\">&nbsp;/&nbsp;<input type=\"text\" name=\"sy\" value=\"$sy\" maxlength=\"3\" size=\"2\">";
	echo "&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit_sector\" value=\"Anzeigen\" /> &nbsp; <input type=\"button\" onclick=\"document.location='?page=galaxygraph'\" value=\"Galaxiegrafik\" /></div><br/>";

	echo "<table id=\"outerspacetbl\" cellspacing=\"0\" cellpadding=\"0\">";

	echo "<tr>";
	//echo "<td width=\"42\" align=\"center\" height=\"42\"><a href=\"?page=$page&amp;sx=$sx_tl&amp;sy=$sy_tl\" class=\"galaxyTopLeft\" title=\"Sektor $sx_tl/$sy_tl\">

	//echo "<td width=\"$table_width\" align=\"center\" height=\"42\"><a href=\"?page=$page&amp;sx=$sx_tc&amp;sy=$sy_tc\" class=\"galaxyTopCenter\" alt=\"Sektor $sx_tc/$sy_tc\" title=\"Sektor $sx_tc/$sy_tc\" /></a></td>";
	if ($sx_tl && $sy_tl!=0 && $sx_tl!=$sx_num+1 && $sy_tl!=$sy_num+1)
	{
		echo "<td width=\"42\" align=\"center\" height=\"42\"><a href=\"?page=$page&amp;sx=$sx_tl&amp;sy=$sy_tl\" title=\"Sektor $sx_tl/$sy_tl\" onmouseover=\"sector_topleft.src='$sector_pic/sector_topleft_On.gif';\" onmouseout=\"sector_topleft.src='$sector_pic/sector_topleft.gif';\">
		<img name=\"sector_topleft\" src=\"$sector_pic/sector_topleft.gif\" height=\"42\" width=\"42\" border=\"0\">
		</a></td>";
	}
	else
	{
		echo "<td width=\"20\" align=\"center\" height=\"20\">&nbsp;</td>";
	}
	if ($sx_tc && $sy_tc!=0 && $sx_tc!=$sx_num+1 && $sy_tc!=$sy_num+1)
	{
		echo "<td width=\"$table_width\" align=\"center\" height=\"42\"><a href=\"?page=$page&amp;sx=$sx_tc&amp;sy=$sy_tc\" alt=\"Sektor $sx_tc/$sy_tc\" title=\"Sektor $sx_tc/$sy_tc\" onmouseover=\"sector_topcenter.src='$sector_pic/sector_topcenter_On.gif';\" onmouseout=\"sector_topcenter.src='$sector_pic/sector_topcenter.gif';\"/>
		<img name=\"sector_topcenter\" src=\"$sector_pic/sector_topcenter.gif\" height=\"42\" width=\"42\" border=\"0\">
		</a></td>";
	}
	else
	{
		echo "<td width=\"$table_width\" align=\"center\" height=\"20\">&nbsp;</td>";
	}
	if ($sx_tr && $sy_tr!=0 && $sx_tr!=$sx_num+1 && $sy_tr!=$sy_num+1)
	{
		echo "<td width=\"42\" align=\"center\" height=\"42\"><a href=\"?page=$page&amp;sx=$sx_tr&amp;sy=$sy_tr\" width=\"20\" height=\"20\" border=\"0\" alt=\"Sektor $sx_tr/$sy_tr\" title=\"Sektor $sx_tr/$sy_tr\" onmouseover=\"sector_topright.src='$sector_pic/sector_topright_On.gif';\" onmouseout=\"sector_topright.src='$sector_pic/sector_topright.gif';\"><img name=\"sector_topright\" src=\"$sector_pic/sector_topright.gif\" height=\"42\" width=\"42\" border=\"0\"></a></td>";
	}
	else
	{
		echo "<td width=\"20\" align=\"center\" height=\"20\">&nbsp;</td>";
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
	
		echo "<td width=\"$table_width\" height=\"$table_height\">";
		echo "\n<table width=\"$table_width\" align=\"center\" cellspacing=\"0\" cellpadding=\"0\" id=\"innerspacetbl\">\n";
		echo "<colgroup width=\"$cell_width\" span=\"$cx_num\" align=\"center\" valign=\"middle\"></colgroup>\n";
		$res = dbquery("
		SELECT 
			* 
		FROM 
			space_cells 
		WHERE 
			cell_sx='$sx' 
			AND cell_sy='$sy';");
		$cells = array();
		while ($arr = mysql_fetch_array($res))
		{
			$cells[$arr['cell_cx']][$arr['cell_cy']]['id']=$arr['cell_id'];
			$cells[$arr['cell_cx']][$arr['cell_cy']]['solsys_num_planets']=$arr['cell_solsys_num_planets'];
			$cells[$arr['cell_cx']][$arr['cell_cy']]['solsys_sol_type']=$arr['cell_solsys_solsys_sol_type'];
			$cells[$arr['cell_cx']][$arr['cell_cy']]['solsys_name']=$arr['cell_solsys_name'];
			$cells[$arr['cell_cx']][$arr['cell_cy']]['asteroid']=$arr['cell_asteroid'];
			$cells[$arr['cell_cx']][$arr['cell_cy']]['nebula']=$arr['cell_nebula'];
			$cells[$arr['cell_cx']][$arr['cell_cy']]['wormhole_id']=$arr['cell_wormhole_id'];
		}
		for ($y=0;$y<$cx_num;$y++)
		{
			$ycoords = $cy_num-$y;


			$counter_left="".IMAGE_PATH."/galaxy/GalaxyFrameCounterLeft";
			$counter_left_high="".IMAGE_PATH."/galaxy/GalaxyFrameCounterLeftHighlight";

			$counter_bottom="".IMAGE_PATH."/galaxy/GalaxyFrameCounterBottom";
			$counter_bottom_high="".IMAGE_PATH."/galaxy/GalaxyFrameCounterBottomHighlight";

			//MvI: Grafik-Counter eingepflegt.
			echo "<td class=\"coordstbl\"> <img name=\"counter_left_$ycoords\" src=\"$counter_left$ycoords.gif\" style=\"height:40px;\"/> </td>";
			//echo "<tr><td class=\"coordstbl\" id=\"ycoords_$ycoords\">$ycoords</td>";


			for ($x=0;$x<$cy_num;$x++)
			{

				$xcoords = $x+1;
				if ($c->solsys_id!="" && $cells[$xcoords][$ycoords]['id']==$c->solsys_id)
				{
					echo "<td class=\"spaceCellSelected\" onmouseover=\"counter_left_$ycoords.src='$counter_left_high$ycoords.gif';counter_bottom_$xcoords.src='$counter_bottom_high$xcoords.gif';\" onmouseout=\"counter_left_$ycoords.src='$counter_left$ycoords.gif';counter_bottom_$xcoords.src='$counter_bottom$xcoords.gif';\">";
				}
				elseif (in_array($cells[$xcoords][$ycoords]['id'],$user_solsys_id) && $cells[$xcoords][$ycoords]['id']!=$c->solsys_id)
				{
					echo "<td class=\"spaceCellUser\" onmouseover=\"counter_left_$ycoords.src='$counter_left_high$ycoords.gif';counter_bottom_$xcoords.src='$counter_bottom_high$xcoords.gif';\" onmouseout=\"counter_left_$ycoords.src='$counter_left$ycoords.gif';counter_bottom_$xcoords.src='$counter_bottom$xcoords.gif';\">";
				}
				else
				{
					echo "<td class=\"spaceCell\" onmouseover=\"counter_left_$ycoords.src='$counter_left_high$ycoords.gif';counter_bottom_$xcoords.src='$counter_bottom_high$xcoords.gif';\" onmouseout=\"counter_left_$ycoords.src='$counter_left$ycoords.gif';counter_bottom_$xcoords.src='$counter_bottom$xcoords.gif';\">";
				}

				//
				// Symbole anzeigen
				//

				// Sonnensystem
				if ($cells[$xcoords][$ycoords]['solsys_num_planets']!=0)
				{
					echo "<a href=\"?page=solsys&amp;id=".$cells[$xcoords][$ycoords]['id']."\" ".tm("Sonnensystem ".$cells[$xcoords][$ycoords]['solsys_name']."","<b>Position:</b> $sx/$sy : $xcoords/$ycoords<br/><b>Planeten:</b> ".$cells[$xcoords][$ycoords]['solsys_num_planets']."")."><img src=\"".IMAGE_PATH."/galaxy/sol".$cells[$xcoords][$ycoords]['solsys_sol_type'].".gif\"  border=\"0\" width=\"$img_width\" height=\"$img_height\" /></a>";
				}
				// Nebel
				elseif ($cells[$xcoords][$ycoords]['nebula']!=0)
				{
					$test = pseudo_randomize(8,$sx_tl,$sy_tl,$xcoords,$ycoords);
					//MvI: Randomize Nebulas eingepflegt.
					//$es_c = mt_rand(1,8);
					echo "<a href=\"?page=haven&amp;planet_to=0&amp;cell_to_id=".$cells[$xcoords][$ycoords]['id']."\"><img src=\"".IMAGE_PATH."/galaxy/nebula".$test.".gif\" ".tm("Intergalaktischer Nebel","<b>Position:</b> $sx/$sy : $xcoords/$ycoords")." border=\"0\" width=\"$img_width\" height=\"$img_height\" /></a>";
				}
				// Asteroiden
				elseif ($cells[$xcoords][$ycoords]['asteroid']!=0)
				{
					//MvI: Randomizer eingebaut
					$test = pseudo_randomize(4,$sx_tl,$sy_tl,$xcoords,$ycoords);
					echo "<a href=\"?page=haven&amp;planet_to=0&amp;cell_to_id=".$cells[$xcoords][$ycoords]['id']."\"><img src=\"".IMAGE_PATH."/galaxy/asteroid_field".$test.".gif\" ".tm("Asteroidenfeld","<b>Position:</b> $sx/$sy : $xcoords/$ycoords")." border=\"0\" width=\"$img_width\" height=\"$img_height\" /></a>";
				}
				elseif ($cells[$xcoords][$ycoords]['wormhole_id']!=0)
				{
					$wres=dbquery("
					SELECT 
                        cell_sx,
                        cell_sy,
                        cell_cx,
                        cell_cy 
					FROM 
						space_cells 
					WHERE 
						cell_id='".$cells[$xcoords][$ycoords]['wormhole_id']."';");
					if (mysql_num_rows($wres)>0)
					{
						$warr=mysql_fetch_array($wres);
						echo "<a href=\"?page=haven&amp;planet_to=0&amp;cell_to_id=".$cells[$xcoords][$ycoords]['id']."\" ".tm("Wurmloch","<b>Position:</b> $sx/$sy : $xcoords/$ycoords<br/><b>Ziel:</b> ".$warr['cell_sx']."/".$warr['cell_sy']." : ".$warr['cell_cx']."/".$warr['cell_cy']).">";
					}
					echo "<img src=\"".IMAGE_PATH."/galaxy/wormhole.gif\" border=\"0\" width=\"$img_width\" height=\"$img_height\" /></a>";
				}
				else
				{
					$es_c = mt_rand(1,4);
					echo "<img src=\"".IMAGE_PATH."/galaxy/empty_space".$es_c.".gif\" alt=\"Leerer Raum\" title=\"Leerer Raum ($xcoords/$ycoords)\" border=\"0\" width=\"$img_width\" height=\"$img_height\" />";
				}
				echo "</td>\n";
			}
			echo "</tr>\n";
		}
		echo "<tr><td class=\"coordstbl\">&nbsp;</td>"; // Linke untere ecke
		for ($x=0;$x<$cy_num;$x++)
		{
			$xcoords = $x+1;

			//Editiert durch MvI: Imagecounter-Grafik statt Text
			  echo "<td class=\"coordstbl\"><img name=\"counter_bottom_$xcoords\" src=\"$counter_bottom$xcoords.gif\"/></td>";
			//echo "<td> <img src=\"".IMAGE_PATH."/galaxy/GalaxyFrameCounterLeft".$ycoords.".gif\" style=\"height:40px;\"/> </td>";
			//echo "<td class=\"coordstbl\" id=\"xcoords_$xcoords\">$xcoords</td>";

		}
		echo "</tr>";
		echo "</table></td>";
		if ($sx_mr && $sy_mr!=0 && $sx_mr!=$sx_num+1 && $sy_mr!=$sy_num+1)
			echo "<td width=\"20\" align=\"center\" height=\"$table_height\"><a href=\"?page=$page&amp;sx=$sx_mr&amp;sy=$sy_mr\"  width=\"20\" height=\"20\" border=\"0\" alt=\"Sektor $sx_mr/$sy_mr\" title=\"Sektor $sx_mr/$sy_mr\" onmouseover=\"sector_middleright.src='$sector_pic/sector_middleright_On.gif';\" onmouseout=\"sector_middleright.src='$sector_pic/sector_middleright.gif';\"><img name=\"sector_middleright\" src=\"$sector_pic/sector_middleright.gif\" height=\"42\" width=\"42\" border=\"0\"></a></td>";
		else
			echo "<td width=\"20\" align=\"center\" height=\"$table_height\">&nbsp;</td>";

		echo "</tr><tr>";

		if ($sx_bl && $sy_bl!=0 && $sx_bl!=$sx_num+1 && $sy_bl!=$sy_num+1)
			echo "<td width=\"20\" align=\"center\" height=\"20\"><a href=\"?page=$page&amp;sx=$sx_bl&amp;sy=$sy_bl\" width=\"20\" height=\"20\" border=\"0\" alt=\"Sektor $sx_bl/$sy_bl\" title=\"Sektor $sx_bl/$sy_bl\" onmouseover=\"sector_bottomleft.src='$sector_pic/sector_bottomleft_On.gif';\" onmouseout=\"sector_bottomleft.src='$sector_pic/sector_bottomleft.gif';\"/><img name=\"sector_bottomleft\" src=\"$sector_pic/sector_bottomleft.gif\" height=\"42\" width=\"42\" border=\"0\"></a></td>";
		else
			echo "<td width=\"20\" align=\"center\" height=\"20\">&nbsp;</td>";
		if ($sx_bc && $sy_bc!=0 && $sx_bc!=$sx_num+1 && $sy_bc!=$sy_num+1)
			echo "<td width=\"$table_width\" align=\"center\" height=\"20\"><a href=\"?page=$page&amp;sx=$sx_bc&amp;sy=$sy_bc\" width=\"20\" height=\"20\" border=\"0\" alt=\"Sektor $sx_bc/$sy_bc\" title=\"Sektor $sx_bc/$sy_bc\" onmouseover=\"sector_bottomcenter.src='$sector_pic/sector_bottomcenter_On.gif';\" onmouseout=\"sector_bottomcenter.src='$sector_pic/sector_bottomcenter.gif';\"/><img name=\"sector_bottomcenter\" src=\"$sector_pic/sector_bottomcenter.gif\" height=\"42\" width=\"42\" border=\"0\"></a></td>";
		else
			echo "<td width=\"$table_width\" align=\"center\" height=\"20\">&nbsp;</td>";
		if ($sx_br && $sy_br!=0 && $sx_br!=$sx_num+1 && $sy_br!=$sy_num+1)
			echo "<td width=\"20\" align=\"center\" height=\"20\"><a href=\"?page=$page&amp;sx=$sx_br&amp;sy=$sy_br\" width=\"20\" height=\"20\" border=\"0\" alt=\"Sektor $sx_br/$sy_br\" title=\"Sektor $sx_br/$sy_br\" onmouseover=\"sector_bottomright.src='$sector_pic/sector_bottomright_On.gif';\" onmouseout=\"sector_bottomright.src='$sector_pic/sector_bottomright.gif';\"/><img name=\"sector_bottomright\" src=\"$sector_pic/sector_bottomright.gif\" height=\"42\" width=\"42\" border=\"0\"></a></td>";
		else
			echo "<td width=\"20\" align=\"center\" height=\"20\">&nbsp;</td>";
		echo "</tr></table></form>";

?>
