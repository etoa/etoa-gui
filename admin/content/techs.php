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
	// 	Dateiname: techs.php	
	// 	Topic: Verwaltung der Technologien 
	// 	Autor: Nicolas Perrenoud alias MrCage							
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar: 	
	//
	
	//
	// Forschungspunkte
	//
	if ($sub=="points")
	{
		echo "<h1>Forschungspunkte</h1>";
		echo "<h2>Forschungpsunkte neu berechnen</h2><form action=\"?page=$page&amp;sub=$sub\" method=\"POST\">";
		if ($_POST['recalc']!="")
		{
			dbquery("DELETE FROM ".$db_table['tech_points'].";");
			$res = dbquery("
			SELECT
				tech_id,
        tech_costs_metal,
        tech_costs_crystal,
        tech_costs_fuel,
        tech_costs_plastic,
        tech_costs_food,
				tech_build_costs_factor        
			FROM
				".$db_table['technologies'].";");
			$mnr = mysql_num_rows($res);
			if ($mnr>0)
			{
				while ($arr = mysql_fetch_array($res))
				{
					for ($level=1;$level<=intval($_POST['maxlevel']);$level++)
					{
						$r = $arr['tech_costs_metal']+$arr['tech_costs_crystal']+$arr['tech_costs_fuel']+$arr['tech_costs_plastic']+$arr['tech_costs_food'];
						$p = ($r*(1-pow($arr['tech_build_costs_factor'],$level))/(1-$arr['tech_build_costs_factor'])) / $conf['points_update']['p1']; 
						dbquery("INSERT INTO ".$db_table['tech_points']." (bp_tech_id,bp_level,bp_points) VALUES (".$arr['tech_id'].",$level,$p);");
					}
				}
			}
			if ($mnr>0)
				echo "Die Forschungspunkte von <b>$mnr</b> Forschungen wurden aktualisiert!<br/><br/>";			
		}		
		echo "Nach jeder &Auml;nderung an den Forschungen m&uuml;ssen die Forschungspunkte neu berechnet werden.<br/><br/>Punkte bis und mit Level ";
		echo "<input type=\"text\" name=\"maxlevel\" value=\"20\" size=\"2\" maxlength=\"2\" /> <input type=\"submit\" name=\"recalc\" value=\"Neu berechnen\" /></form>";		
		
		echo "<h2>Forschungspunkte</h2>";
		$res=dbquery("SELECT
			tech_id,
			tech_name
		FROM ".$db_table['technologies']."
		ORDER BY tech_order,tech_name;");
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\">";
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr><th>".$arr['tech_name']."</th><td style=\"width:70%\"><table class=\"tb\">";
				$pres=dbquery("SELECT
					bp_level,
					bp_points
				FROM ".$db_table['tech_points']."
				WHERE bp_tech_id=".$arr['tech_id']."
				ORDER BY bp_level ASC;");
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
				}
				echo "</table></td></tr>";
			}			
			echo "</table>";
		}		
	}
	
	//
	// Kategorien
	//
	elseif ($sub=="type")
	{
		simple_form("tech_types");
	}
	
	//
	// Technologien
	//
	elseif ($sub=="data")
	{
		advanced_form("technologies");
	}	
	//
	// Anforderungen
	//
	elseif ($sub=="req")
	{

		define("TITLE","Forschungsanforderungen");
		define("ITEMS_TBL","technologies");
		define("TYPES_TBL","tech_types");
		define("REQ_TBL","tech_requirements");
		define("REQ_ITEM_FLD","req_tech_id");
		define("ITEM_ID_FLD","tech_id");
		define("ITEM_NAME_FLD","tech_name");
		define("ITEM_SHOW_FLD","tech_show");
		define("ITEM_ORDER_FLD","tech_type_id,tech_order,tech_name");
		define("NO_ITEMS_MSG","In dieser Kategorie gibt es keine Forschungen!");
		

	
		echo "<h1>".TITLE."</h1>";

		if ($_POST['submit_changes']!="")
		{
			// Gebäudeänderungen speichern			
			foreach ($_POST['building_id'] as $id=>$val)
			{
				if ($_POST['building_level'][$id]<1)
					dbquery("DELETE FROM ".$db_table[REQ_TBL]." WHERE req_id=$id;");
				else
					dbquery("UPDATE ".$db_table[REQ_TBL]." SET req_req_building_id=$val,req_req_building_level=".$_POST['building_level'][$id]." WHERE req_id=$id;");
			}			
			// Technologieänderungen speichern
			foreach ($_POST['tech_id'] as $id=>$val)
			{
				if ($_POST['tech_level'][$id]<1)
					dbquery("DELETE FROM ".$db_table[REQ_TBL]." WHERE req_id=$id;");
				else
					dbquery("UPDATE ".$db_table[REQ_TBL]." SET req_req_tech_id=$val,req_req_tech_level=".$_POST['tech_level'][$id]." WHERE req_id=$id;");
			}							
		}

		// Gebäudeverknüpfung speichern
		if ($_POST['add_building']!="")
		{
			if ($_POST['new_item_id']!="")
			{			
				if (mysql_num_rows(dbquery("SELECT req_id FROM ".$db_table[REQ_TBL]." WHERE ".REQ_ITEM_FLD."=".$_POST['new_id']." AND req_req_building_id=".$_POST['new_item_id'].";"))==0)
				{
					dbquery("INSERT INTO ".$db_table[REQ_TBL]." (".REQ_ITEM_FLD.",req_req_building_id,req_req_building_level) VALUES ('".$_POST['new_id']."','".$_POST['new_item_id']."','".$_POST['new_item_level']."');");
				}			
				else
					echo "Fehler! Diese Geb&auml;udeverkn&uuml;pfung existiert bereits!<br/><br/>";
			}
			else
				echo "Fehler! Kein verkn&uuml;pfendes Geb&auml;ude ausgew&auml;hlt!<br/><br/>";
		}
		
		// Technologieverknüpfung speicher
		if ($_POST['add_tech']!="")
		{
			if ($_POST['new_item_id']!="")
			{			
				if (mysql_num_rows(dbquery("SELECT req_id FROM ".$db_table[REQ_TBL]." WHERE ".REQ_ITEM_FLD."=".$_POST['new_id']." AND req_req_tech_id=".$_POST['new_item_id'].";"))==0)
				{
					dbquery("INSERT INTO ".$db_table[REQ_TBL]." (".REQ_ITEM_FLD.",req_req_tech_id,req_req_tech_level) VALUES ('".$_POST['new_id']."','".$_POST['new_item_id']."','".$_POST['new_item_level']."');");
				}			
				else
					echo "Fehler! Diese Forschungsverkn&uuml;pfung existiert bereits!<br/><br/>";
			}
			else
				echo "Fehler! Keine verkn&uuml;pfende Forschung ausgew&auml;hlt!<br/><br/>";
		}
		
		// Gebäudeverknüpfungen löschen
		if ($_POST['del_building']!="")
		{
			if (count($_POST['del_building'])>0)
			{			
				foreach ($_POST['del_building'] as $req_building_id=>$req_req_building_id)
				{
					foreach ($req_req_building_id as $key=>$val)
					{
						dbquery("DELETE FROM ".$db_table[REQ_TBL]." WHERE ".REQ_ITEM_FLD."=$req_building_id AND req_req_building_id=$key;");
					}
				}
			}
		}		

		// Technologieknüpfungen löschen
		if ($_POST['del_tech']!="")
		{
			if (count($_POST['del_tech'])>0)
			{			
				foreach ($_POST['del_tech'] as $req_building_id=>$req_req_tech_id)
				{
					foreach ($req_req_tech_id as $key=>$val)
					{
						dbquery("DELETE FROM ".$db_table[REQ_TBL]." WHERE ".REQ_ITEM_FLD."=$req_building_id AND req_req_tech_id=$key;");
					}
				}
			}
		}		


		// Lade Gebäude- & Technologienamen
		$bures = dbquery("SELECT building_id,building_name FROM ".$db_table['buildings']." WHERE building_show=1;");
		while ($buarr = mysql_fetch_array($bures))
		{
			$bu_name[$buarr['building_id']]=$buarr['building_name'];
		}
		$teres = dbquery("SELECT tech_id,tech_name FROM ".$db_table['technologies']." WHERE tech_show=1;");
		while ($tearr = mysql_fetch_array($teres))
		{
			$te_name[$tearr['tech_id']]=$tearr['tech_name'];
		}	
  	
		// Lade Anforderungen
		$rres = dbquery("SELECT * FROM ".$db_table[REQ_TBL].";");
		while ($rarr = mysql_fetch_array($rres))
		{
			$b_req[$rarr[REQ_ITEM_FLD]]['i'][$rarr['req_req_building_id']]=$rarr['req_id'];
			$b_req[$rarr[REQ_ITEM_FLD]]['i'][$rarr['req_req_tech_id']]=$rarr['req_id'];
			if ($rarr['req_req_building_id']>0) $b_req[$rarr[REQ_ITEM_FLD]]['b'][$rarr['req_req_building_id']]=$rarr['req_req_building_level'];
			if ($rarr['req_req_tech_id']>0) $b_req[$rarr[REQ_ITEM_FLD]]['t'][$rarr['req_req_tech_id']]=$rarr['req_req_tech_level'];
		}
  	
		$res = dbquery("SELECT * FROM ".$db_table[ITEMS_TBL]." WHERE ".ITEM_SHOW_FLD."=1 ORDER BY ".ITEM_ORDER_FLD.";");
		if (mysql_num_rows($res)>0)
		{
			if ($_GET['action']=="new_building" || $_GET['action']=="new_tech")
				$form_addition=" disabled=\"disabled\"";

			while ($arr=mysql_fetch_array($res))
			{
				echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
				echo "<table style=\"width:400px;\" class=\"tb\">";
				echo "<tr><th colspan=\"3\" class=\"techtreeBuildingTitle\">".$arr[ITEM_NAME_FLD]."</th></tr>";
				$using_something=0;

				// Gespeicherte Gebäudeanforderungen			
				if (count($b_req[$arr[ITEM_ID_FLD]]['b'])>0)
				{
					foreach ($b_req[$arr[ITEM_ID_FLD]]['b'] as $b=>$l)
					{
						echo "<tr>";
						echo "<td class=\"tbldata\" width=\"200\"><select name=\"building_id[".$b_req[$arr[ITEM_ID_FLD]]['i'][$b]."]\" $form_addition>";
						if ($b==0)
						echo "<option value=\"\"><i>Geb&auml;ude w&auml;hlen</i></option>";
						foreach ($bu_name as $key=>$val)
						{
							echo "<option value=\"$key\"";
							if ($b==$key) echo " selected=\"selected\"";
							echo ">$val</option>";
						}
						echo "</select></td><td class=\"tbldata\" width=\"50\"><input type=\"text\" name=\"building_level[".$b_req[$arr[ITEM_ID_FLD]]['i'][$b]."]\" size=\"1\" maxlength=\"3\" value=\"$l\"$form_addition /></td>";
						if ($_GET['action']!="new_building" && $_GET['action']!="new_tech")
							echo "<td class=\"tbldata\"><input type=\"submit\" name=\"del_building[".$arr[ITEM_ID_FLD]."][$b]\" value=\"L&ouml;schen\" /></td></tr>";
						else
							echo "<td class=\"tbldata\">&nbsp;</td></tr>";
					}
					$using_something=1;
				}
				// Neue Gebäudeanforderung
				if ($_GET['action']=="new_building" && $_GET['id']==$arr[ITEM_ID_FLD])
				{
					echo "<input type=\"hidden\" name=\"new_id\" value=\"".$arr[ITEM_ID_FLD]."\">";
					echo "<tr><td class=\"tbldata\" width=\"200\"><select name=\"new_item_id\">";
					echo "<option value=\"\" style=\"font-style:italic;\">Geb&auml;ude w&auml;hlen</option>";
					foreach ($bu_name as $key=>$val)
					{
						if ($key!=$arr[ITEM_ID_FLD])
							echo "<option value=\"$key\">$val</option>";
					}			
					echo "</select></td><td class=\"tbldata\"><input type=\"text\" name=\"new_item_level\" size=\"1\" maxlength=\"3\" value=\"1\" /></td>";
					echo "<td class=\"tbldata\"><input type=\"submit\" name=\"add_building\" value=\"&Uuml;bernehmen\" /></td></tr>";
				}
				
				// Gespeicherte Forschungsanforderungen
				if (count($b_req[$arr[ITEM_ID_FLD]]['t'])>0)
				{
					foreach ($b_req[$arr[ITEM_ID_FLD]]['t'] as $b=>$l)
					{
						echo "<tr><td class=\"tbldata\" width=\"200\"><select name=\"tech_id[".$b_req[$arr[ITEM_ID_FLD]]['i'][$b]."]\" $form_addition>";
						if ($b==0)
						echo "<option value=\"\"><i>Geb&auml;ude w&auml;hlen</i></option>";
						foreach ($te_name as $key=>$val)
						{
							echo "<option value=\"$key\"";
							if ($b==$key) echo " selected=\"selected\"";
							echo ">$val</option>";
						}
						echo "</select></td><td class=\"tbldata\" width=\"50\"><input type=\"text\" name=\"tech_level[".$b_req[$arr[ITEM_ID_FLD]]['i'][$b]."]\" size=\"1\" maxlength=\"3\" value=\"$l\"$form_addition /></td>";
						if ($_GET['action']!="new_building" && $_GET['action']!="new_tech")
							echo "<td class=\"tbldata\"><input type=\"submit\" name=\"del_tech[".$arr[ITEM_ID_FLD]."][$b]\" value=\"L&ouml;schen\"$form_addition /></td></tr>";
						else
							echo "<td class=\"tbldata\">&nbsp;</td></tr>";
					}		
					$using_something=1;
				}				
				// Neue Forschungsanforderung
				if ($_GET['action']=="new_tech" && $_GET['id']==$arr[ITEM_ID_FLD])
				{
					echo "<input type=\"hidden\" name=\"new_id\" value=\"".$arr[ITEM_ID_FLD]."\">";
					echo "<tr><td class=\"tbldata\" width=\"200\"><select name=\"new_item_id\">";
					echo "<option value=\"\" style=\"font-style:italic;\">Technologie w&auml;hlen</option>";
					foreach ($te_name as $key=>$val)
					{
						echo "<option value=\"$key\">$val</option>";
					}			
					echo "</select></td><td class=\"tbldata\"><input type=\"text\" name=\"new_item_level\" size=\"1\" maxlength=\"3\" value=\"1\" /></td>";
					echo "<td class=\"tbldata\"><input type=\"submit\" name=\"add_tech\" value=\"&Uuml;bernehmen\" /></td></tr>";
				}
				if ($using_something==0)
					echo "<tr><td width=\"200\" class=\"tbldata\">&nbsp;</td><td colspan=\"2\" class=\"techtreeBuildingNoReq\">Keine Voraussetzungen</td></tr>";
				if ($_GET['action']!="new_building" && $_GET['action']!="new_tech")
				{
					echo "<tr><td class=\"tbldata\">Neue Voraussetzung?</td>";
					echo "<td class=\"tbldata\" colspan=\"2\"><input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=new_building&amp;id=".$arr[ITEM_ID_FLD]."';\" value=\"Geb&auml;ude\" />&nbsp;";
					echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=new_tech&amp;id=".$arr[ITEM_ID_FLD]."';\" value=\"Forschung\" /></tr>";
				}
				echo "</table><br/>"; 	
			}
			if ($form_addition=="")
				echo "<p align=\"center\"><input type=\"submit\" name=\"submit_changes\" value=\"&Auml;nderungen &uuml;bernehmen\" /></p>";
		}
		else
			echo "<p class=\"infomsg\">".NO_ITEMS_MSG."</p>";
	}
	
	//
	// Liste
	//
	else
	{
		echo "<h1>Forschungsliste</h1>";

		$build_type[0]="Unt&auml;tig";
		$build_type[1]="Forschen";
	
		if (isset($_POST['techlist_search']) || (isset($_GET['action']) && $_GET['action']=="searchresults") || isset($_POST['new']))
		{
			$sqlstart = "
			SELECT 
					planet_name,
		      entities.pos,
		      cells.sx,cells.sy,
		      cells.cx,cells.cy,
		      user_nick,
		      user_points,
		      tech_name,
		      techlist_id,
		      techlist_build_type,
		      techlist_current_level
			FROM 
				techlist
			INNER JOIN
				technologies
			ON
				techlist.techlist_tech_id=technologies.tech_id
			INNER JOIN 
				planets
			ON
				techlist_planet_id=planets.id
			INNER JOIN
				entities
			ON
				planets.id=entities.id
			INNER Join
				cells
			ON
				entities.cell_id=cells.id
			INNER JOIN
				users
			ON
				techlist.techlist_user_id=users.user_id			
			";
			$sqlend = "
			GROUP BY 
				techlist_id 
			ORDER BY 
				techlist_planet_id,
				tech_type_id,
				tech_order,
				tech_name;";
 	
			// Forschung hinzufügen
			if (isset($_POST['new']))
			{
				$updata=explode(":",$_POST['planet_id']);
				if (mysql_num_rows(dbquery("SELECT techlist_id FROM ".$db_table['techlist']." WHERE techlist_user_id=".$updata[1]." AND techlist_tech_id=".$_POST['tech_id'].";"))==0)
				{
					dbquery("INSERT INTO ".$db_table['techlist']." (techlist_planet_id,techlist_user_id,techlist_tech_id,techlist_current_level) VALUES (".$updata[0].",".$updata[1].",".$_POST['tech_id'].",".$_POST['techlist_current_level'].");");					
					echo "Technologie wurde hinzugef&uuml;gt!<br/>";
				}
				else
				{
					echo "Technologie existiert bereits!<br/>";
				}
				$sql= " AND user_id=".$updata[1];
				$_SESSION['techedit']['query']="";
				
				// Technologien laden
				$bres = dbquery("SELECT tech_id,tech_name FROM ".$db_table['technologies']." ORDER BY tech_type_id,tech_order,tech_name;");
				$tlist=array();
				while ($barr=mysql_fetch_array($bres))
					$tlist[$barr['tech_id']]=$barr['tech_name'];	

				// Hinzufügen
				echo "<h2>Neue Technologien hinzuf&uuml;gen</h2>";
				echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
				infobox_start("",1);
				echo "<tr><th class=\"tbltitle\">Technologie:</th><td class=\"tbldata\"><select name=\"tech_id\">";
				foreach ($tlist as $k=>$v)
				{
					echo "<option value=\"".$k."\"";
					if ($k==$_POST['tech_id']) echo " selected=\"selected\"";
					echo ">".$v."</option>";
				}
				echo "</select></td></tr>";
				if ($_POST['techlist_current_level']) 
					$v=$_POST['techlist_current_level'];
				else	
					$v=1;
				echo "<tr><th class=\"tbltitle\">Stufe</th><td class=\"tbldata\"><input type=\"text\" name=\"techlist_current_level\" value=\"$v\" size=\"1\" maxlength=\"3\" /></td></tr>";
				echo "<tr><th class=\"tbltitle\">f&uuml;r den Spieler</th><td class=\"tbldata\"> <select name=\"planet_id\"><";
				$pres=dbquery("SELECT user_id,user_nick,planets.id FROM ".$db_table['users'].",".$db_table['planets']." WHERE planet_user_id=user_id AND planet_user_main=1 ORDER BY user_nick;");
				while ($parr=mysql_fetch_array($pres))
				{
					echo "<option value=\"".$parr['planet_id'].":".$parr['user_id']."\"";
					if ($updata[1]==$parr['user_id']) echo " selected=\"selected\"";
					echo ">".$parr['user_nick']."</option>";
				}
				echo "</select></td></tr>";
				infobox_end(1);
				echo "<input type=\"submit\" name=\"new\" value=\"Hinzuf&uuml;gen\" /></form><br/>";				
				$sql = $sqlstart.$sql.$sqlend;
				$_SESSION['techedit']['query']=$sql;
			}			
			
			// Suchquery generieren
			elseif ($_SESSION['techedit']['query']=="")
			{ 	
 			
				if ($_POST['planet_id']!='')
					$sql.= " AND planets.id='".$_POST['planet_id']."'";
				if ($_POST['planet_name']!='')
				{
					if (stristr($_POST['qmode']['planet_name'],"%")) 
						$addchars = "%";else $addchars = "";
					$sql.= " AND planet_name ".stripslashes($_POST['qmode']['planet_name'])."'".$_POST['planet_name']."$addchars'";
				}
				if ($_POST['user_id']!='')
					$sql.=" AND user_id='".$_POST['user_id']."'";
				if ($_POST['user_nick']!="")
				{
					if (stristr($_POST['qmode']['user_nick'],"%")) 
						$addchars = "%";else $addchars = "";
					$sql.= " AND user_nick ".stripslashes($_POST['qmode']['user_nick']).$_POST['user_nick']."$addchars'";
				}		
				if ($_POST['tech_id']!='')
					$sql.= " AND tech_id='".$_POST['tech_id']."'";
					
				$sql = $sqlstart.$sql.$sqlend;
				$_SESSION['techedit']['query']=$sql;
			}
			else
				$sql = $_SESSION['techedit']['query'];											
 	
			$res = dbquery($sql);
			if (mysql_num_rows($res)>0)
			{
				echo mysql_num_rows($res)." Datens&auml;tze vorhanden<br/><br/>";
				if (mysql_num_rows($res)>20)
					echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /><br/><br/>";
  	
				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<td class=\"tbltitle\" valign=\"top\">Planet</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Spieler</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Forschung</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Stufe</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Status</td>";
				echo "</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					if ($arr['techlist_build_type']==1)
						$style=" style=\"color:#0f0\"";
					else
						$style="";
					echo "<tr>";
					echo "<td class=\"tbldata\"$style ".tm($arr['planet_name'],$arr['sx']."/".$arr['sy']." : ".$arr['cx']."/".$arr['cy']." : ".$arr['pos']).">".cut_string($arr['planet_name'],11)."</a></td>";
					echo "<td class=\"tbldata\"$style ".tm($arr['user_nick'],nf($arr['user_points'])." Punkte").">".cut_string($arr['user_nick'],11)."</a></td>";
					echo "<td class=\"tbldata\"$style>".$arr['tech_name']."</a></td>";
					echo "<td class=\"tbldata\"$style>".nf($arr['techlist_current_level'])."</a></td>";
					echo "<td class=\"tbldata\"$style>".$build_type[$arr['techlist_build_type']]."</a></td>";
					echo "<td class=\"tbldata\">".edit_button("?page=$page&sub=$sub&action=edit&techlist_id=".$arr['techlist_id'])."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br/><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" />";
			}
			else
			{
				echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page&sub=$sub'\" />";
			}		
		}
		
		//
		// Bearbeiten
		//
		elseif ($_GET['action']=="edit")
		{
			if (isset($_POST['save']))
			{
				dbquery("UPDATE ".$db_table['techlist']." SET techlist_current_level='".$_POST['techlist_current_level']."',techlist_build_type='".$_POST['techlist_build_type']."',techlist_build_start_time=UNIX_TIMESTAMP('".$_POST['techlist_build_start_time']."'),techlist_build_end_time=UNIX_TIMESTAMP('".$_POST['techlist_build_end_time']."') WHERE techlist_id='".$_GET['techlist_id']."';");
			}
			elseif (isset($_POST['del']))
			{
				dbquery("DELETE FROM ".$db_table['techlist']." WHERE techlist_id='".$_GET['techlist_id']."';");
			}
			
			$res = dbquery("SELECT 
								* 
							FROM 
								techlist
							INNER JOIN
								technologies
							ON
								techlist.techlist_tech_id=technologies.tech_id
								AND techlist.techlist_id='".$_GET['techlist_id']."'
							INNER JOIN
								planets
							ON
								techlist.techlist_planet_id=planets.id
							INNER JOIN
								users
							ON
								techlist.techlist_user_id=users.user_id;");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				echo "<form action=\"?page=$page&sub=$sub&action=edit&techlist_id=".$_GET['techlist_id']."\" method=\"post\">";
				echo "<table class=\"tbl\">";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">ID</td><td class=\"tbldata\">".$arr['techlist_id']."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Planet</td><td class=\"tbldata\">".$arr['planet_name']."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Spieler</td><td class=\"tbldata\">".$arr['user_nick']."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Geb&auml;ude</td><td class=\"tbldata\">".$arr['tech_name']."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Level</td><td class=\"tbldata\"><input type=\"text\" name=\"techlist_current_level\" value=\"".$arr['techlist_current_level']."\" size=\"2\" maxlength=\"3\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Baustatus</td><td class=\"tbldata\"><select name=\"techlist_build_type\">";
				foreach ($build_type as $id=>$val)
				{
					echo "<option value=\"$id\"";
					if ($arr['techlist_build_type']==$id) echo " selected=\"selected\"";
					echo ">$val</option>";
				}
				echo "</select></td></tr>";
				
				if ($arr['techlist_build_start_time']>0) $bst = date(DATE_FORMAT,$arr['techlist_build_start_time']); else $bst = "";
				if ($arr['techlist_build_end_time']>0) $bet = date(DATE_FORMAT,$arr['techlist_build_end_time']); else $bet = "";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Baustart</td><td class=\"tbldata\"><input type=\"text\" name=\"techlist_build_start_time\" id=\"techlist_build_start_time\" value=\"$bst\" size=\"20\" maxlength=\"30\" /> <input type=\"button\" value=\"Jetzt\" onclick=\"document.getElementById('techlist_build_start_time').value='".date("Y-d-m h:i")."'\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Bauende</td><td class=\"tbldata\"><input type=\"text\" name=\"techlist_build_end_time\" value=\"$bet\" size=\"20\" maxlength=\"30\" /></td></tr>";
				echo "</table>";
				echo "<br/><input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" />&nbsp;";
				echo "<input type=\"submit\" name=\"del\" value=\"L&ouml;schen\" />&nbsp;";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" />&nbsp;";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&sub=$sub'\" value=\"Neue Suche\" />&nbsp;";
				echo "</form>";
			}
			else
				echo "Dieser Datensatz wurde gel&ouml;scht!<br/><br/><input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" />;";
		}
		
		//
		// Suchformular Technologien
		//
		else
		{		
			$_SESSION['techedit']['query']="";
			
			// Technologien laden
			$bres = dbquery("SELECT tech_id,tech_name FROM ".$db_table['technologies']." ORDER BY tech_type_id,tech_order,tech_name;");
			$tlist=array();
			while ($barr=mysql_fetch_array($bres))
				$tlist[$barr['tech_id']]=$barr['tech_name'];	
			
			// Suchmaske
			echo "Suchmaske:<br/><br/>";
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\">Planet ID</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Planetname</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('planet_name');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Spieler ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Spieler Nick</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\" />&nbsp;";
			fieldqueryselbox('user_nick');
			echo "<br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td></tr>";
			echo "<tr><td class=\"tbltitle\">Forschung</td><td class=\"tbldata\"><select name=\"tech_id\"><option value=\"\"><i>---</i></option>";
			foreach ($tlist as $k=>$v)
				echo "<option value=\"".$k."\">".$v."</option>";
			echo "</select></td></tr>";
			echo "</table>";
			echo "<br/><input type=\"submit\" name=\"techlist_search\" value=\"Suche starten\" /></form>";
			$tblcnt = mysql_fetch_row(dbquery("SELECT 
													count(*) 
												FROM 
													".$db_table['techlist'].";"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br/>";	
			
			// Hinzufügen
			echo "<h2>Neue Forschung hinzuf&uuml;gen</h2>";
			echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
			infobox_start("",1);
			echo "<tr><th class=\"tbltitle\">Technologie</th><td class=\"tbldata\"><select name=\"tech_id\">";
			foreach ($tlist as $k=>$v)
				echo "<option value=\"".$k."\">".$v."</option>";
			echo "</select></td></tr>";
			echo "<tr><th class=\"tbltitle\">Stufe</th><td class=\"tbldata\"><input type=\"text\" name=\"techlist_current_level\" value=\"1\" size=\"1\" maxlength=\"3\" /></td></tr>";
			echo "<tr><th class=\"tbltitle\">f&uuml;r den Spieler</th><td class=\"tbldata\"> <select name=\"planet_id\"><";
			$pres=dbquery("SELECT 
								user_id,
								user_nick,
								planets.id 
							FROM 
								planets
							INNER JOIN
								users
							ON
								users.user_id=planets.planet_user_id
								AND planets.planet_user_main=1 
							ORDER BY 
								user_nick;");
			while ($parr=mysql_fetch_array($pres))
			{
				echo "<option value=\"".$parr['id'].":".$parr['user_id']."\">".$parr['user_nick']."</option>";
			}
			echo "</select></td></tr>";
			infobox_end(1);
			echo "<input type=\"submit\" name=\"new\" value=\"Hinzuf&uuml;gen\" /></form>";				
		}		
	}


?>