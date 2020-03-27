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
	// 	Dateiname: buildings.php
	// 	Topic: Gebäudeverwaltung
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	//
	// Kategorien bearbeiten
	//
	if ($sub=="prices")
	{
		echo "<h1>Preisrechner</h1>";
		echo "<script type=\"text/javascript\">
		function showPrices()
		{
			xajax_buildingPrices(
			document.getElementById('c1_id').options[document.getElementById('c1_id').selectedIndex].value,
			document.getElementById('c1_level').options[document.getElementById('c1_level').selectedIndex].value
			)
		}           
		
		function showTotalPrices()
		{
			xajax_totalBuildingPrices(xajax.getFormValues('totalCosts'));
		}		
		</script>";

		$res = dbquery("
		SELECT
			building_id,
			building_name
		FROM
			buildings
		ORDER BY
			building_name
		");
		$bs = array();
		while ($arr=mysql_Fetch_row($res))
		{
			$bs[$arr[0]]=$arr[1];
		}

		echo "<h2>(Aus)baukosten (von Stufe x-1 auf Stufe x)</h2>";
		echo "<table class=\"tb\">
			<tr>
				<th>Gebäude</th>
				<th>Stufe</th>
				<th>Zeit</th>
				<th>".RES_METAL."</th>
				<th>".RES_CRYSTAL."</th>
				<th>".RES_PLASTIC."</th>
				<th>".RES_FUEL."</th>
				<th>".RES_FOOD."</th>
				<th>Energie</th>
			</tr>";
		echo "<tr>
		<td><select id=\"c1_id\" onchange=\"showPrices()\">";
		foreach ($bs as $k => $v)
		{
			echo "<option value=\"".$k."\">".$v."</option>";
		}
		echo "</select></td>
		<td><select id=\"c1_level\" onchange=\"showPrices()\">";
		for ($x=1;$x<=40;$x++)
		{
			echo "<option value=\"".($x-1)."\">".$x."</option>";
		}
		echo "</select></td>
		<td id=\"c1_time\">-</td>
		<td id=\"c1_metal\">-</td>
		<td id=\"c1_crystal\">-</td>
		<td id=\"c1_plastic\">-</td>
		<td id=\"c1_fuel\">-</td>
		<td id=\"c1_food\">-</td>
		<td id=\"c1_power\">-</td>
		";
		echo "</tr></table>";

		echo "<h2>Totale Kosten</h2>
		<form action=\"?page=$page&amp;sub=$sub\" method=\"post\" id=\"totalCosts\">";
		ecHo "<table class=\"tb\">
		<tr>
			<th>Gebäude</th>
			<th>Level</th>
			<th>".RES_METAL."</th>
			<th>".RES_CRYSTAL."</th>
			<th>".RES_PLASTIC."</th>
			<th>".RES_FUEL."</th>
			<th>".RES_FOOD."</th>
		</tr>";
		foreach ($bs as $k => $v)
		{
			echo "<tr>
			<td>".$v."</td>
			<td>Level 0 bis <select name=\"b_lvl[".$k."]\" onchange=\"showTotalPrices()\">";
			for ($x=0;$x<=40;$x++)
			{
				echo "<option value=\"".$x."\">".$x."</option>";
			}
			echo "</select></td>
			<td id=\"b_metal_".$k."\">-</td>
			<td id=\"b_crystal_".$k."\">-</td>
			<td id=\"b_plastic_".$k."\">-</td>
			<td id=\"b_fuel_".$k."\">-</td>
			<td id=\"b_food_".$k."\">-</td>
			</tr>";
		}
		echo "<tr><td style=\"height:2px;\" colspan=\"7\"></tr>";
		echo "<tr>
			<td colspan=\"2\">Total</td>
			<td id=\"t_metal\">-</td>
			<td id=\"t_crystal\">-</td>
			<td id=\"t_plastic\">-</td>
			<td id=\"t_fuel\">-</td>
			<td id=\"t_food\">-</td>
		</tr>";
		echo "</table></form>";
	}

	//
	// Gebäudepunkte
	//
	elseif ($sub=="points")
	{
		echo "<h1>Geb&auml;udepunkte</h1>";
		echo "<h2>Geb&auml;udepunkte neu berechnen</h2><form action=\"?page=$page&amp;sub=$sub\" method=\"POST\">";
		if (isset($_POST['recalc']) && $_POST['recalc']!="")
		{
			echo MessageBox::ok("", Ranking::calcBuildingPoints());
		}
		echo "Nach jeder &Auml;nderung an den Geb&auml;uden m&uuml;ssen die Geb&auml;udepunkte neu berechnet werden.<br/><br/>
		Diese Aktion kann eine Weile dauern! ";
		echo "<input type=\"submit\" name=\"recalc\" value=\"Neu berechnen\" /></form>";

		echo "<h2>Geb&auml;udepunkte</h2>";
		$res=dbquery("
		SELECT
			building_id,
			building_name
		FROM 
			buildings
		ORDER BY 
			building_order,
			building_name;");
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\">";
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr><th>".$arr['building_name']."</th><td style=\"width:70%\"><table class=\"tb\">";
				$pres=dbquery("
				SELECT
					bp_level,
					bp_points
				FROM 
					building_points
				WHERE
					bp_building_id=".$arr['building_id']."
				ORDER BY 
					bp_level ASC;");
				if (mysql_num_rows($pres)>0)
				{
					$cnt=0;
					while ($parr=mysql_fetch_array($pres))
					{
						if ($cnt==0)
							echo "<tr>";
						echo "<th>".$parr['bp_level']."</th><td>".$parr['bp_points']."</td>";
						if ($cnt=="3")
						{
							echo "</tr>";
							$cnt=0;
						}
						else
							$cnt++;
					}
					if ($cnt!=0)
					{
						for ($x=$cnt;$x<4;$x++)
						{
							echo "<td colspan=\"2\"></td>";
						}
						echo "</tr>";
					}
				}
				echo "</table></td></tr>";
			}
			echo "</table>";
		}

	}

	//
	// Kategorien bearbeiten
	//
	elseif ($sub=="type")
	{
		simple_form("building_types", $twig);
	}

	//
	// Gebäude bearbeiten
	//
	elseif ($sub=="data")
	{
		advanced_form("buildings", $twig);
	}

	//
	// Voraussetzungen
	//
	elseif ($sub=="reqmap")
	{

		echo "<h1>Gebäude Techtree</h1>";
		$starItem = 6;

		// Lade Gebäude
		$bures = dbquery("SELECT building_id,building_name FROM buildings;");
		while ($buarr = mysql_fetch_array($bures))
		{
			$bu_name[$buarr['building_id']]=$buarr['building_name'];
		}

		function reqTree($cItemId,$cItemLevel,$level=0,$endnode=false,$empty=null)
		{
			global $bu_name;

			$res = dbquery("SELECT * FROM building_requirements WHERE req_building_id=".$cItemId.";");
			$nr = mysql_num_rows($res);

			echo "<tr><td style=\"padding:0px;margin:0px;color:#000;background:#fff;\">";
			for ($x=0;$x<$level;$x++)
			{
				if ($x==$level-1)
				{
					$divtext = $cItemLevel;
					if ($endnode)
						$img = "treelastnode.gif";
					else
						$img = "treenode.gif";
				}
				else
				{
					if ($empty[$x])
						$img = "blank.gif";
					else
						$img = "treeline.gif";
					$divtext = "";
				}
				echo "<div style=\"margin:0px;padding:0px;background:#fff url('../images/".$img."') repeat-y;width:20px;height:20px;float:left;text-align:left;color:#666;\" >".$divtext."</div>";
			}
			echo "<img src=\"".IMAGE_PATH."/buildings/building".$cItemId."_small.".IMAGE_EXT."\" align=\"top\" style=\"border:none;height:20px;width:20px;margin:0px;padding:0px;\">
				<a href=\"javascript:;\" style=\"color:#00f\" onclick=\"xajax_reqInfo(".$cItemId.")\">".$bu_name[$cItemId]."</a> 
			<br style=\"clear:both;\"/>
			</td></tr>";

			if ($endnode)
				$empty[$level-1]=true;

			if ($nr>0)
			{
				$cnt=0;
				while($arr=mysql_fetch_assoc($res))
				{
					$cnt++;
					reqTree($arr['obj_id'],$arr['req_level'],$level+1, $cnt==$nr ? true : false,$empty);
				}
			}
			return $nr;
		}

		echo "<table style=\"border-collapse:collapse;border:3px solid #fff;background:#fff;float:left;\">";
		$num = reqTree($starItem,1);
		echo "</table>";
		echo "<div id=\"reqInfo\" style=\"width:500px;text-align:center;;margin-left:10px;padding:10px;background:#fff;color:#000;float:left;\">
		Gebäude auswählen...
		</div>";

	}

	//
	// Voraussetzungen
	//
	elseif ($sub=="req")
	{
		define('TITLE',"Geb&auml;udeanforderungen");
		define('ITEMS_TBL',"buildings");
		define('TYPES_TBL',"building_types");
		define('REQ_TBL',"building_requirements");
		define('ITEM_ID_FLD',"building_id");
		define('ITEM_NAME_FLD',"building_name");
		define('ITEM_ENABLE_FLD',"1");
		define('ITEM_ORDER_FLD',"building_type_id,building_order,building_name");

		define("ITEM_IMAGE_PATH",IMAGE_PATH."/buildings/building<DB_TABLE_ID>_small.".IMAGE_EXT);

		include("inc/requirements.inc.php");
	}

	//
	// Liste
	//
	else
	{
		$twig->addGlobal('title', 'Gebäudeliste');

		$buildTypes = Building::getBuildTypes();

		$build_colors[0]="inherit";
		$build_colors[1]="red";
		$build_colors[2]="orange";
		$build_colors[3]="#0f0";
		$build_colors[4]="orange";

		if (isset($_POST['save']))
		{
			dbquery("
			UPDATE 
				buildlist 
			SET 
                buildlist_current_level='".$_POST['buildlist_current_level']."',
                buildlist_build_type='".$_POST['buildlist_build_type']."',
                buildlist_build_start_time=UNIX_TIMESTAMP('".$_POST['buildlist_build_start_time']."'),
                buildlist_build_end_time=UNIX_TIMESTAMP('".$_POST['buildlist_build_end_time']."') 
			WHERE 
				buildlist_id='".$_POST['buildlist_id']."';");
		}
		elseif (isset($_POST['del']))
		{
			dbquery("DELETE FROM buildlist WHERE buildlist_id='".$_POST['buildlist_id']."';");
		}


		//
		// Datensatz bearbeiten
		//
		if (isset($_GET['action']) && $_GET['action']=="edit")
		{
			echo "<h2>Datensatz bearbeiten</h2>";
			$res = dbquery("
			SELECT 
				buildlist.buildlist_id,
				buildlist.buildlist_current_level,
				buildlist.buildlist_build_start_time,
				buildlist.buildlist_build_end_time,
				buildlist.buildlist_build_type,
				planets.planet_name,
				users.user_nick,
				buildings.building_name
			FROM 
                buildlist
			INNER JOIN
                planets
			ON 
				buildlist.buildlist_entity_id=planets.id
			INNER JOIN
                users
			ON 
				buildlist.buildlist_user_id=users.user_id
			INNER JOIN
                buildings
			ON
				buildlist.buildlist_building_id=buildings.building_id
				AND buildlist.buildlist_id=".$_GET['buildlist_id'].";");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				echo "<form action=\"?page=$page&sub=$sub&amp;action=search\" method=\"post\">";
				echo "<input type=\"hidden\" name=\"buildlist_id\" value=\"".$arr['buildlist_id']."\" />";
				echo "<table class=\"tb\">";
				echo "<tr><th>ID</th><td>".$arr['buildlist_id']."</td></tr>";
				echo "<tr><th>Planet</th><td>".$arr['planet_name']."</td></tr>";
				echo "<tr><th>Spieler</th><td>".$arr['user_nick']."</td></tr>";
				echo "<tr><th>Geb&auml;ude</th><td>".$arr['building_name']."</td></tr>";
				echo "<tr><th>Level</th><td><input type=\"text\" name=\"buildlist_current_level\" value=\"".$arr['buildlist_current_level']."\" size=\"2\" maxlength=\"3\" /></td></tr>";
				echo "<tr><th>Baustatus</th><td><select name=\"buildlist_build_type\">";
				foreach ($buildTypes as $id=>$val)
				{
					echo "<option value=\"$id\"";
					if ($arr['buildlist_build_type']==$id) echo " selected=\"selected\"";
					echo ">$val</option>";
				}
				echo "</select></td></tr>";
				if ($arr['buildlist_build_start_time']>0) $bst = date(DATE_FORMAT,$arr['buildlist_build_start_time']); else $bst = "";
				if ($arr['buildlist_build_end_time']>0) $bet = date(DATE_FORMAT,$arr['buildlist_build_end_time']); else $bet = "";
				echo "<tr><th>Baustart</th><td><input type=\"text\" name=\"buildlist_build_start_time\" id=\"buildlist_build_start_time\" value=\"$bst\" size=\"20\" maxlength=\"30\" /> <input type=\"button\" value=\"Jetzt\" onclick=\"document.getElementById('buildlist_build_start_time').value='".date("Y-d-m h:i")."'\" /></td></tr>";
				echo "<tr><th>Bauende</th><td><input type=\"text\" name=\"buildlist_build_end_time\" value=\"$bet\" size=\"20\" maxlength=\"30\" /></td></tr>";
				echo "</table>";
				echo "<br/><input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" />&nbsp;";
				echo "<input type=\"submit\" name=\"del\" value=\"L&ouml;schen\" />&nbsp;";
				echo "<input type=\"submit\" value=\"Zur&uuml;ck zu den Suchergebnissen\" />&nbsp;";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Neue Suche\" />";
				echo "</form>";
			}
			else
				echo "Dieser Datensatz wurde gel&ouml;scht!<br/><br/><a href=\"?page=$page&amp;sub=$sub\">Neue Suche</a>";
		}

		//
		// Suchergebnisse anzeigen
		//
		elseif ((isset($_POST['buildlist_search']) || isset($_POST['new']) || isset($_SESSION['search']['buildings']['query'])) && (isset($_GET['action']) &&$_GET['action']=="search"))
		{
			if (isset($_GET['query']) && $_GET['query']!="")
			{
				$qs = searchQueryDecode($_GET['query']);
				foreach($qs as $k=>$v)
				{
					$_POST[$k]=$v;
				}
				$_SESSION['search']['buildings']['query']=null;
			}
			$tables = "buildlist,planets,users,buildings";
			$sql = "";
			if (isset($_POST['new']))
			{
				$updata=explode(":",$_POST['entity_id']);
				if (mysql_num_rows(dbquery("SELECT buildlist_id FROM buildlist WHERE buildlist_entity_id=".$updata[0]." AND buildlist_building_id=".$_POST['building_id'].";"))==0)
				{
					dbquery("
					INSERT INTO 
					buildlist 
                        (buildlist_entity_id,
                        buildlist_user_id,
                        buildlist_building_id,
                        buildlist_current_level) 
					VALUES 
                        (".$updata[0].",
                        ".$updata[1].",
                        ".$_POST['building_id'].",
                        ".$_POST['building_level'].");");
					echo "Geb&auml;ude wurde hinzugef&uuml;gt!<br/>";
				}
				else
					echo "Geb&auml;ude kann nicht hinzugef&uuml;gt werden, es ist bereits vorhanden!<br/>";
				$sql= "planets.id=".$updata[0]." AND ";
				$_SESSION['search']['buildings']['query']=null;


				echo "<h2>Neues Geb&auml;ude hinzuf&uuml;gen</h2>";
				echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
				tableStart();
				echo "<tr><th class=\"tbltitle\">Geb&auml;ude</th><td class=\"tbldata\"><select name=\"building_id\">";
				$bres = dbquery("SELECT building_id,building_name FROM buildings ORDER BY building_type_id,building_order,building_name;");
				while ($barr=mysql_fetch_array($bres))
				{
					echo "<option value=\"".$barr['building_id']."\">".$barr['building_name']."</option>";
				}
				echo "</select></td></tr>";
				echo "<tr><th class=\"tbltitle\">mit Stufe</th><td class=\"tbldata\"><input type=\"text\" name=\"building_level\" value=\"1\" size=\"1\" maxlength=\"3\" /></td></tr>";
				echo "<tr><th class=\"tbltitle\">auf dem Planeten</th><td class=\"tbldata\"> <select name=\"entity_id\"><";
				$pres=dbquery("SELECT 
								users.user_id,
								planets.id,
								planets.planet_name,
								users.user_nick,
								entities.pos,
								cells.sx,
								cells.sy,
								cells.cx,
								cells.cy 
							FROM 
								planets
							INNER JOIN
								entities
							ON 
								planets.id=entities.id
							INNER JOIN
								cells
							ON 
								entities.cell_id=cells.id
							INNER JOIN 
								users
							ON
								planets.planet_user_id=users.user_id
							ORDER BY 
								planets.id;");
				while ($parr=mysql_fetch_array($pres))
				{
					echo "<option value=\"".$parr['id'].":".$parr['user_id']."\"";
					if ($updata[0]==$parr['id']) echo " selected=\"selected\"";
					echo ">".$parr['sx']."/".$parr['sy']." : ".$parr['cx']."/".$parr['cy']." : ".$parr['pos']." &nbsp; ".$parr['planet_name']." (".$parr['user_nick'].")</option>";
				}
				echo "</select></td></tr>";
				tableEnd();
				echo "<input type=\"submit\" name=\"new\" value=\"Hinzuf&uuml;gen\" /></form><br/>";


			}
			else
			{
				if ($_POST['entity_id']!="")
				{
					$sql.= "id=".$_POST['entity_id']." AND ";
				}
				if ($_POST['planet_name']!="")
				{
					if (stristr($_POST['qmode']['planet_name'],"%")) $addchars = "%";else $addchars = "";
					$sql.= "planet_name ".stripslashes($_POST['qmode']['planet_name']).$_POST['planet_name']."$addchars' AND ";
				}
				if ($_POST['user_id']!="")
				{
					$sql.= "user_id=".$_POST['user_id']." AND ";
				}
				if ($_POST['user_nick']!="")
				{
					if (stristr($_POST['qmode']['user_nick'],"%")) $addchars = "%";else $addchars = "";
					$sql.= "user_nick ".stripslashes($_POST['qmode']['user_nick']).$_POST['user_nick']."$addchars' AND ";
				}
				if ($_POST['building_id']!="")
				{
					$sql.= "building_id=".$_POST['building_id']." AND ";
				}
			}

			echo "<h2>Suchergebnisse</h2>";
			$sqlstart = "SELECT * FROM $tables WHERE ";
			$sqlend = "buildlist_building_id=building_id AND user_id=buildlist_user_id AND planets.id=buildlist_entity_id
			GROUP BY buildlist_id
			ORDER BY buildlist_user_id,buildlist_entity_id,building_type_id,building_order,building_name;";

  		if ($_SESSION['search']['buildings']['query']==null)
  			$_SESSION['search']['buildings']['query']=$sqlstart.$sql.$sqlend;

			$res = dbquery($_SESSION['search']['buildings']['query']);
			if (mysql_num_rows($res)>0)
			{
				echo mysql_num_rows($res)." Datens&auml;tze vorhanden<br/><br/>";
				if (mysql_num_rows($res)>20)
					echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Neue Suche\" /><br/><br/>";

				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<th class=\"tbltitle\">Planet</th>";
				echo "<th class=\"tbltitle\">Spieler</th>";
				echo "<th class=\"tbltitle\">Geb&auml;ude</th>";
				echo "<th class=\"tbltitle\">Stufe</th>";
				echo "<th class=\"tbltitle\">Status</th>";
				echo "<th></th>";
				echo "</tr>";
				for ($x=0;$x<mysql_num_rows($res);$x++)
				{
					if (sizeof($narr)>1)
						$arr=$narr;
					else
						$arr = mysql_fetch_array($res);
					$narr=mysql_fetch_array($res);

					echo "<tr>";
					echo "<td class=\"tbldata\"><a href=\"?page=galaxy&amp;sub=edit&amp;id=".$arr['buildlist_entity_id']."\" title=\"".$arr['planet_name']."\">".cut_string($arr['planet_name'] != '' ? $arr['planet_name'] : 'Unbenannt',11)."</a> [".$arr['id']."]</td>";

					echo "<td class=\"tbldata\"><a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr['buildlist_user_id']."\" title=\"".$arr['user_nick']."\">".cut_string($arr['user_nick'],11)."</a></td>";
					$style=" ";

					echo "<td class=\"tbldata\">".$arr['building_name']."</td>";
					echo "<td class=\"tbldata\">".nf($arr['buildlist_current_level'])."</td>";
					echo "<td class=\"tbldata\" style=\"background:".$build_colors[$arr['buildlist_build_type']]."\">".$buildTypes[$arr['buildlist_build_type']]."</td>";
					echo "<td class=\"tbldata\">".edit_button("?page=$page&amp;sub=$sub&amp;action=edit&amp;buildlist_id=".$arr['buildlist_id'])."</td>";
					echo "</tr>";

				}
				echo "</table>";
				echo "<br/><input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Neue Suche\" />";
			}
			else
			{
				echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Zur&uuml;ck\" />";
			}
		}

		//
		// Suchformular
		//
		else
		{
			echo '<div class="tabs">
			<ul>
				<li><a href="#tabs-1">Schnellsuche</a></li>
				<li><a href="#tabs-2">Erweiterte Suche</a></li>
			</ul>
			<div id="tabs-1">';

			$bres = dbquery("SELECT building_id,building_name FROM buildings ORDER BY building_type_id,building_order,building_name;");
			while ($barr=mysql_fetch_array($bres))
			{
				$bdata[$barr['building_id']]=$barr;
			}

			// Hinzufügen
			echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\" id=\"selector\" name=\"selector\">";

			//Sonnensystem
			echo '<table class="tb">';
			echo "<tr><th class=\"tbltitle\">Sonnensystem</th><td class=\"tbldata\">
			<select name=\"cell_sx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showBuildingsOnPlanet',1);\">";
			echo "<option value=\"0\">Sektor X</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cell_sy\"  onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showBuildingsOnPlanet',1);\">";
			echo "<option value=\"0\">Sektor Y</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select> : <select name=\"cell_cx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showBuildingsOnPlanet',1);\">";
			echo "<option value=\"0\">Zelle X</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cell_cy\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showBuildingsOnPlanet',1);\">";
			echo "<option value=\"0\">Zelle Y</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select></td></tr>";


			//User
			echo "<tr><th class=\"tbltitle\"><i>oder</i> User</th><td class=\"tbldata\">";
			echo "<input type=\"text\" name=\"userlist_nick\" id=\"userlist_nick\" value=\"\" autocomplete=\"off\" size=\"30\" maxlength=\"30\" onchange=\"xajax_searchUserList(this.value,'showBuildingsOnPlanet');\" onkeyup=\"xajax_searchUserList(this.value,'showBuildingsOnPlanet');\"><br>
			<div id=\"userlist\">&nbsp;</div>";
			echo "</td></tr>";

			//Planeten
			echo "<tr><th class=\"tbltitle\">Planeten</th><td class=\"tbldata\" id=\"planetSelector\">Sonnensystem oder User w&auml;hlen...</td></tr>";

			//Gebäude Hinzufügen
			echo "<tr><th class=\"tbltitle\">Hinzuf&uuml;gen:</th><td class=\"tbldata\">
			<input type=\"text\" name=\"buildlist_current_level\" value=\"1\" size=\"7\" maxlength=\"10\" />
			<select name=\"building_id\">";
			foreach ($bdata as $barr)
			{
				echo "<option value=\"".$barr['building_id']."\">".$barr['building_name']."</option>";
			}
			echo "</select> &nbsp; 
			<input type=\"button\" onclick=\"showLoaderPrepend('shipsOnPlanet');xajax_addBuildingToPlanet(xajax.getFormValues('selector'));\" value=\"Hinzuf&uuml;gen\" />
			<input type=\"button\" onclick=\"showLoaderPrepend('shipsOnPlanet');xajax_addAllBuildingToPlanet(xajax.getFormValues('selector'),".count($bdata).");\" value=\"Alle hinzuf&uuml;gen\" /></td></tr>";

			//Gebäude wählen
			echo "<tr><td class=\"tbldata\" id=\"shipsOnPlanet\" colspan=\"2\">Planet w&auml;hlen...</td></tr>";
			tableEnd();
			echo "</form>";

			//Focus
			echo "<script type=\"text/javascript\">document.getElementById('userlist_nick').focus();</script>";

			//Add User
			if (searchQueryArray($sa,$so))
			{
				if (isset($sa['user_nick']))
				{
					echo "<script type=\"text/javascript\">document.getElementById('userlist_nick').value=\"".$sa['user_nick'][1]."\";xajax_searchUserList('".$sa['user_nick'][1]."','showBuildingsOnPlanet');</script>";
				}
			}

			echo '</div><div id="tabs-2">';

			$_SESSION['search']['buildings']['query']=null;
			echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
			echo '<table class="tb">';
			echo "<tr><th class=\"tbltitle\">Planet ID</th><td class=\"tbldata\"><input type=\"text\" name=\"entity_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><th class=\"tbltitle\">Planetname</th><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" />&nbsp;";
			fieldqueryselbox('planet_name');
			echo "</td></tr>";
			echo "<tr><th class=\"tbltitle\">Spieler ID</th><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><th class=\"tbltitle\">Spieler Nick</th><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\" />&nbsp;";
			fieldqueryselbox('user_nick');
			echo "<br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td></tr>";
			echo "<tr><th class=\"tbltitle\">Geb&auml;ude</th><td class=\"tbldata\"><select name=\"building_id\"><option value=\"\"><i>---</i></option>";
			foreach ($bdata as $barr)
			{
				echo "<option value=\"".$barr['building_id']."\">".$barr['building_name']."</option>";
			}
			echo "</select></td></tr>";
			tableEnd();
			echo "<br/><input type=\"submit\" name=\"buildlist_search\" value=\"Suche starten\" />";
			echo "</form>";

			echo '
				</div>
			</div>';

			$tblcnt = mysql_fetch_row(dbquery("SELECT count(buildlist_id) FROM buildlist;"));
			echo "<p>Es sind <b>".nf($tblcnt[0])."</b> Eintr&auml;ge in der Datenbank vorhanden.</p>";

		}

	}


?>
