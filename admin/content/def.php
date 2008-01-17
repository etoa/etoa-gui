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
	// 	Dateiname: def.php	
	// 	Topic: Verwaltung der Verteidigungsanlagen 
	// 	Autor: Nicolas Perrenoud alias MrCage							
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar: 	
	//


	//
	// Battlepoints
	//
	if ($sub=="battlepoints")
	{
		echo "<h1>Battlepoints</h1>";
		echo "<h2>Battlepoints neu berechnen</h2><form action=\"?page=$page&amp;sub=$sub\" method=\"POST\">";
		if ($_POST['recalc']!="")
		{
			$res = dbquery("
			SELECT
				def_id,
        def_costs_metal,
        def_costs_crystal,
        def_costs_fuel,
        def_costs_plastic,
        def_costs_food
			FROM
				".$db_table['defense'].";");
			$mnr = mysql_num_rows($res);
			if ($mnr>0)
			{
				while ($arr = mysql_fetch_array($res))
				{
					$p = ($arr['def_costs_metal']+$arr['def_costs_crystal']+$arr['def_costs_fuel']+$arr['def_costs_plastic']+$arr['def_costs_food'])/$conf['points_update']['p1'];
					dbquery("UPDATE ".$db_table['defense']." SET 
						def_battlepoints=$p
					WHERE 
						def_id=".$arr['def_id'].";");
				}
			}
			if ($mnr>0)
				echo "Die Battlepoints von <b>$mnr</b> Verteidigungsanlagen wurden aktualisiert!<br/><br/>";			
		}		
		echo "Nach jeder &Auml;nderung an den Verteidigungsanlagen m&uuml;ssen die Battlepoints neu berechnet werden: ";
		echo "<input type=\"submit\" name=\"recalc\" value=\"Neu berechnen\" /></form>";		
		echo "<h2>Battlepoints</h2>";
		$res=dbquery("SELECT
			def_id,
			def_name,
			def_battlepoints
		FROM ".$db_table['defense']."
		ORDER BY def_battlepoints DESC, def_name DESC;");
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\">";
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr><th>".$arr['def_name']."</th><td style=\"width:70%\">".$arr['def_battlepoints']."</td></tr>";
			}			
			echo "</table>";
		}	
	}
	
	//
	// Bearbeiten
	//	
	elseif ($sub=="data")
	{
		advanced_form("defense");
	}

	//
	// Kategorien
	//	
	elseif ($sub=="cat")
	{
		advanced_form("def_cat");
	}

	
	//
	// Voraussetzungen
	//
	elseif ($sub=="req")
	{
		define("TITLE","Verteidigungsanforderungen");
		define("ITEMS_TBL","defense");
		define("TYPES_TBL","def_types");
		define("REQ_TBL","def_requirements");
		define("REQ_ITEM_FLD","req_def_id");
		define("ITEM_ID_FLD","def_id");
		define("ITEM_NAME_FLD","def_name");
		define("ITEM_SHOW_FLD","def_show");
		define("ITEM_ORDER_FLD","def_order,def_name");
		define("NO_ITEMS_MSG","In dieser Kategorie gibt es keine Verteidigunsanlagen!");
	
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
				echo "<table style=\"width:400px\" class=\"tb\">";
				echo "<tr><th colspan=\"3\" >".$arr[ITEM_NAME_FLD]."</th></tr>";
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
							echo "<td class=\"tbldata\"><input type=\"submit\" class=\"button\" name=\"del_building[".$arr[ITEM_ID_FLD]."][$b]\" value=\"L&ouml;schen\" /></td></tr>";
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
					echo "<td class=\"tbldata\"><input type=\"submit\" class=\"button\" name=\"add_building\" value=\"&Uuml;bernehmen\" /></td></tr>";
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
							echo "<td class=\"tbldata\"><input type=\"submit\" class=\"button\" name=\"del_tech[".$arr[ITEM_ID_FLD]."][$b]\" value=\"L&ouml;schen\"$form_addition /></td></tr>";
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
					echo "<td class=\"tbldata\"><input type=\"submit\" class=\"button\" name=\"add_tech\" value=\"&Uuml;bernehmen\" /></td></tr>";
				}
				if ($using_something==0)
					echo "<tr><td width=\"200\" class=\"tbldata\">&nbsp;</td><td colspan=\"2\" class=\"techtreeBuildingNoReq\">Keine Voraussetzungen</td></tr>";
				if ($_GET['action']!="new_building" && $_GET['action']!="new_tech")
				{
					echo "<tr><td class=\"tbldata\">Neue Voraussetzung?</td>";
					echo "<td class=\"tbldata\" colspan=\"2\"><input type=\"button\" class=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=new_building&amp;id=".$arr[ITEM_ID_FLD]."';\" value=\"Geb&auml;ude\" />&nbsp;";
					echo "<input type=\"button\" class=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=new_tech&amp;id=".$arr[ITEM_ID_FLD]."';\" value=\"Forschung\" /></tr>";
				}
				echo "</table><br/>"; 	
			}
			if ($form_addition=="")
				echo "<p align=\"center\"><input type=\"submit\" class=\"button\" name=\"submit_changes\" value=\"&Auml;nderungen &uuml;bernehmen\" /></p>";
		}
		else
			echo "<p class=\"infomsg\">".NO_ITEMS_MSG."</p>";
	}

	//
	// Liste
	//
	else
	{
		echo "<h1>Verteidigungsliste</h1>";
	
		if ($_POST['deflist_search']!="" || $_GET['action']=="searchresults" || $_POST['new']!="")
		{
	
			$sqlstart = "SELECT 
				planet_id,
				planet_name,
		  	planet_solsys_pos,
		  	cell_sx,cell_sy,
		  	cell_cx,cell_cy,
		  	user_id,
		  	user_nick,
		  	user_points,
		  	def_id,
		  	def_name,
		  	deflist_id,
		  	deflist_count,
		  	deflist_build_count			
			FROM 
				".$db_table['deflist'].",
				".$db_table['planets'].",
				".$db_table['space_cells'].",
				".$db_table['users'].",
				".$db_table['defense']." 
			WHERE 
		    	planet_solsys_id=cell_id 
					AND deflist_def_id=def_id 
					AND user_id=deflist_user_id 
					AND planet_id=deflist_planet_id ";
			$sqlend = " 
			GROUP BY 
					deflist_id 
			ORDER BY 
					deflist_planet_id,
					def_order,def_name;";
  
			// Verteidigung hinzufügen
			if ($_POST['new']!="")
			{
				$updata=explode(":",$_POST['planet_id']);
				if (mysql_num_rows(dbquery("SELECT deflist_id FROM ".$db_table['deflist']." WHERE deflist_planet_id=".$updata[0]." AND deflist_def_id=".$_POST['def_id'].";"))==0)
				{
					dbquery("INSERT INTO ".$db_table['deflist']." (deflist_planet_id,deflist_user_id,deflist_def_id,deflist_count) VALUES (".$updata[0].",".$updata[1].",".$_POST['def_id'].",".$_POST['deflist_count'].");");					
					echo "Verteidigung wurde hinzugef&uuml;gt!<br/>";
				}
				else
				{
					dbquery("UPDATE ".$db_table['deflist']." SET deflist_count=deflist_count+".$_POST['deflist_count']." WHERE deflist_planet_id=".$updata[0]." AND deflist_def_id=".$_POST['def_id'].";");
					echo "Verteidigung wurde hinzugef&uuml;gt!<br/>";
				}
				$sql= " AND planet_id=".$updata[0];
				$_SESSION['defedit']['query']="";
				
				// Verteidigung laden
				$bres = dbquery("SELECT def_id,def_name FROM ".$db_table['defense']." ORDER BY def_name;");
				$slist=array();
				while ($barr=mysql_fetch_array($bres))
					$slist[$barr['def_id']]=$barr['def_name'];					

				// Hinzufügen
				echo "<h2>Neue Verteidigungsanlagen hinzuf&uuml;gen</h2>";
				echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
				infobox_start("",1);
				echo "<tr><th class=\"tbltitle\">Verteidigung:</th><td class=\"tbldata\"><select name=\"def_id\">";
				foreach ($slist as $k=>$v)
				{
					echo "<option value=\"".$k."\"";
					if ($k==$_POST['def_id']) echo " selected=\"selected\"";
					echo ">".$v."</option>";
				}
				echo "</select></td></tr>";
				if ($_POST['deflist_count']) 
					$v=$_POST['deflist_count'];
				else	
					$v=1;
				echo "<tr><th class=\"tbltitle\">Anzahl</th><td class=\"tbldata\"><input type=\"text\" name=\"deflist_count\" value=\"$v\" size=\"1\" maxlength=\"3\" /></td></tr>";
				echo "<tr><th class=\"tbltitle\">auf dem Planeten</th><td class=\"tbldata\"> <select name=\"planet_id\"><";
				$pres=dbquery("SELECT user_id,planet_id,planet_name,user_nick,planet_solsys_pos,cell_sx,cell_sy,cell_cx,cell_cy FROM ".$db_table['planets'].",".$db_table['space_cells'].",".$db_table['users']." WHERE planet_user_id=user_id AND planet_solsys_id=cell_id ORDER BY planet_id;");
				while ($parr=mysql_fetch_array($pres))
				{
					echo "<option value=\"".$parr['planet_id'].":".$parr['user_id']."\"";
					if ($updata[0]==$parr['planet_id']) echo " selected=\"selected\"";
					echo ">".$parr['cell_sx']."/".$parr['cell_sy']." : ".$parr['cell_cx']."/".$parr['cell_cy']." : ".$parr['planet_solsys_pos']." &nbsp; ".$parr['planet_name']." (".$parr['user_nick'].")</option>";
				}
				echo "</select></td></tr>";
				infobox_end(1);
				echo "<input type=\"submit\" name=\"new\" value=\"Hinzuf&uuml;gen\" /></form><br/>";				
				$sql = $sqlstart.$sql.$sqlend;
				$_SESSION['defedit']['query']=$sql;
			}			
			
			// Suchquery generieren
			elseif ($_SESSION['defedit']['query']=="")
			{
				if ($_POST['planet_id']!="")
					$sql.= " AND planet_id='".$_POST['planet_id']."'";
				if ($_POST['planet_name']!="")
				{
					if (stristr($_POST['qmode']['planet_name'],"%")) 
						$addchars = "%";else $addchars = "";
					$sql.= " AND planet_name ".stripslashes($_POST['qmode']['planet_name']).$_POST['planet_name']."$addchars'";
				}
				if ($_POST['user_id']!="")
					$sql.=" AND user_id='".$_POST['user_id']."'";
				if ($_POST['user_nick']!="")
				{
					if (stristr($_POST['qmode']['user_nick'],"%")) 
						$addchars = "%";else $addchars = "";
					$sql.= " AND user_nick ".stripslashes($_POST['qmode']['user_nick']).$_POST['user_nick']."$addchars'";
				}
				if ($_POST['def_id']!="")
					$sql.= " AND def_id='".$_POST['def_id']."'";
				if ($_POST['building']==1)
					$sql.= " AND deflist_build_count!=0";
				if ($_POST['building']==0)
					$sql.= " AND deflist_build_count=0";					

				$sql = $sqlstart.$sql.$sqlend;
				$_SESSION['defedit']['query']=$sql;
			}
			else
				$sql = $_SESSION['defedit']['query'];								
  	
  	
			$res = dbquery($sql);
			if (mysql_num_rows($res)>0)
			{
				echo mysql_num_rows($res)." Datens&auml;tze vorhanden<br/><br/>";
				if (mysql_num_rows($res)>20)
				{
					echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&sub=$sub'\" /> ";
  				echo "<input type=\"button\" value=\"Aktualisieren\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=searchresults'\" /><br/><br/>";					
  			}
				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<td class=\"tbltitle\">ID</td>";
				echo "<td class=\"tbltitle\">Planet</td>";
				echo "<td class=\"tbltitle\">Spieler</td>";
				echo "<td class=\"tbltitle\">Verteidigung</td>";
				echo "<td class=\"tbltitle\">Anzahl</td>";
				echo "<td class=\"tbltitle\">Bau</td>";
				echo "</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					if ($arr['deflist_build_count']>0)
						$style=" style=\"color:#0f0\"";
					else
						$style="";					
					
					echo "<tr>";
					echo "<td class=\"tbldata\" $style>".$arr['deflist_id']."</a></td>";
					echo "<td class=\"tbldata\" $style".tm($arr['planet_name'],"<b>Planet-ID:</b> ".$arr['planet_id']."<br/><b>Koordinaten:</b> ".$arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy']." : ".$arr['planet_solsys_pos']).">".cut_string($arr['planet_name'],11)."</a></td>";
					echo "<td class=\"tbldata\" $style".tm($arr['user_nick'],"<b>User-ID:</b> ".$arr['user_id']."<br/><b>Punkte:</b> ".nf($arr['user_points'])).">".cut_string($arr['user_nick'],11)."</a></td>";
					echo "<td class=\"tbldata\" $style".tm($arr['def_name'],"<b>Verteidigungs-ID:</b> ".$arr['def_id']).">".$arr['def_name']."</a></td>";
					echo "<td class=\"tbldata\" $style>".nf($arr['deflist_count'])."</a></td>";
					echo "<td class=\"tbldata\" $style>".nf($arr['deflist_build_count'])."</a></td>";
					echo "<td class=\"tbldata\">".edit_button("?page=$page&sub=$sub&action=edit&deflist_id=".$arr['deflist_id'])."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br/><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&sub=$sub'\" /> ";
				echo "<input type=\"button\" value=\"Aktualisieren\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=searchresults'\" />";				
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
			if ($_POST['save']!="")
			{
				dbquery("UPDATE ".$db_table['deflist']." SET deflist_count='".$_POST['deflist_count']."',deflist_build_count='".$_POST['deflist_build_count']."',deflist_build_start_time=UNIX_TIMESTAMP('".$_POST['deflist_build_start_time']."'),deflist_build_end_time=UNIX_TIMESTAMP('".$_POST['deflist_build_end_time']."') WHERE deflist_id='".$_GET['deflist_id']."';");
			}
			elseif ($_POST['del']!="")
			{
				dbquery("DELETE FROM ".$db_table['deflist']." WHERE deflist_id='".$_GET['deflist_id']."';");
			}
			elseif ($_POST['build_cancel']!="")
			{
				dbquery("UPDATE ".$db_table['deflist']." SET
					deflist_build_count=0,
					deflist_build_start_time=0,
					deflist_build_end_time=0,
					deflist_build_object_time=0
				WHERE 
					deflist_id='".$_GET['deflist_id']."';");
			}
			elseif ($_POST['build_finish']!="")
			{
				dbquery("UPDATE ".$db_table['deflist']." SET
					deflist_count=deflist_count+deflist_build_count,
					deflist_build_count=0,
					deflist_build_start_time=0,
					deflist_build_end_time=0,
					deflist_build_object_time=0
				WHERE 
					deflist_id='".$_GET['deflist_id']."';");
			}				
			
			
			$res = dbquery("SELECT * FROM ".$db_table['deflist'].",".$db_table['planets'].",".$db_table['users'].",".$db_table['defense']." WHERE deflist_def_id=def_id AND user_id=deflist_user_id AND planet_id=deflist_planet_id AND deflist_id=".$_GET['deflist_id'].";");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				echo "<form action=\"?page=$page&sub=$sub&action=edit&deflist_id=".$_GET['deflist_id']."\" method=\"post\">";
				echo "<table class=\"tbl\">";
				echo "<tr><td class=\"tbltitle\">ID</td><td class=\"tbldata\">".$arr['deflist_id']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Planet</td><td class=\"tbldata\">".$arr['planet_name']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Spieler</td><td class=\"tbldata\">".$arr['user_nick']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Verteidigung</td><td class=\"tbldata\">".$arr['def_name']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Anzahl</td><td class=\"tbldata\"><input type=\"text\" name=\"deflist_count\" value=\"".$arr['deflist_count']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\">Im Bau</td><td class=\"tbldata\"><input type=\"text\" name=\"deflist_build_count\" value=\"".$arr['deflist_build_count']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
				
				if ($arr['deflist_build_start_time']>0) $bst = date(DATE_FORMAT,$arr['deflist_build_start_time']); else $bst = "";
				if ($arr['deflist_build_end_time']>0) $bet = date(DATE_FORMAT,$arr['deflist_build_end_time']); else $bet = "";
				echo "<tr><td class=\"tbltitle\">Baustart</td><td class=\"tbldata\"><input type=\"text\" name=\"deflist_build_start_time\" id=\"deflist_build_start_time\" value=\"$bst\" size=\"20\" maxlength=\"30\" /> <input type=\"button\" value=\"Jetzt\" onclick=\"document.getElementById('deflist_build_start_time').value='".date("Y-d-m h:i")."'\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\">Bauende</td><td class=\"tbldata\"><input type=\"text\" name=\"deflist_build_end_time\" value=\"$bet\" size=\"20\" maxlength=\"30\" /></td></tr>";
				echo "</table><br/>";
				echo "<input type=\"submit\" name=\"build_cancel\" value=\"Bau abbrechen\"  onclick=\"return confirm('Bau wirklich abbrechen?')\" />&nbsp;";
				echo "<input type=\"submit\" name=\"build_finish\" value=\"Bau fertigstellen\" />&nbsp;";
				echo "<input type=\"submit\" name=\"del\" value=\"L&ouml;schen\" class=\"button\" />&nbsp;";
				echo "<hr/>";				
				echo "<input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" /> ";
				echo "<input type=\"button\" value=\"Neue Suche\" class=\"button\" onclick=\"document.location='?page=$page&sub=$sub'\" /></form>";
			}
			else
				echo "Dieser Datensatz wurde gel&ouml;scht!<br/><br/><input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" />&nbsp;";
		}
		
		//
		// Suchformular
		//
		else
		{		
			
			
			$_SESSION['defedit']['query']="";

			// Verteidigung laden
			$bres = dbquery("SELECT def_id,def_name FROM ".$db_table['defense']." ORDER BY def_name;");
			$dlist=array();
			while ($barr=mysql_fetch_array($bres))
				$dlist[$barr['def_id']]=$barr['def_name'];	
				
			// Suchmaske
			echo "Suchmaske:<br/><br/>";
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\">Planet ID</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Planetname</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('planet_name');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Spieler ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Spieler Nick</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\"/> ";fieldqueryselbox('user_nick');echo "<br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></tr>";
			echo "<tr><td class=\"tbltitle\">Verteidigung</td><td class=\"tbldata\"><select name=\"def_id\"><option value=\"\"><i>---</i></option>";
			foreach ($dlist as $k=>$v)
				echo "<option value=\"".$k."\">".$v."</option>";
			echo "</select></td></tr>";
			echo "<tr><td class=\"tbltitle\">Im Bau</td><td class=\"tbldata\"><input type=\"radio\" name=\"building\" value=\"2\" checked=\"checked\" /> Egal  <input type=\"radio\" name=\"building\" value=\"1\" /> Ja  <input type=\"radio\" name=\"building\" value=\"0\" /> Nein</td></tr>";			
			echo "</table>";
			echo "<br/><input type=\"submit\" class=\"button\" name=\"deflist_search\" value=\"Suche starten\" /></form>";

/*
			// Hinzufügen
			echo "<h2>Neue Verteidigung hinzuf&uuml;gen</h2>";
			echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
			infobox_start("",1);
			echo "<tr><th class=\"tbltitle\">Verteidigung:</th><td class=\"tbldata\"><select name=\"def_id\">";
			foreach ($dlist as $k=>$v)
				echo "<option value=\"".$k."\">".$v."</option>";
			echo "</select></td></tr>";
			echo "<tr><th class=\"tbltitle\">Anzahl</th><td class=\"tbldata\"><input type=\"text\" name=\"deflist_count\" value=\"1\" size=\"1\" maxlength=\"3\" /></td></tr>";
			echo "<tr><th class=\"tbltitle\">auf dem Planeten</th><td class=\"tbldata\"> <select name=\"planet_id\"><";
			$pres=dbquery("SELECT user_id,planet_id,planet_name,user_nick,planet_solsys_pos,cell_sx,cell_sy,cell_cx,cell_cy FROM ".$db_table['planets'].",".$db_table['space_cells'].",".$db_table['users']." WHERE planet_user_id=user_id AND planet_solsys_id=cell_id ORDER BY planet_id;");
			while ($parr=mysql_fetch_array($pres))
			{
				echo "<option value=\"".$parr['planet_id'].":".$parr['user_id']."\">".$parr['cell_sx']."/".$parr['cell_sy']." : ".$parr['cell_cx']."/".$parr['cell_cy']." : ".$parr['planet_solsys_pos']." &nbsp; ".$parr['planet_name']." (".$parr['user_nick'].")</option>";
			}
			echo "</select></td></tr>";
			infobox_end(1);
			echo "<input type=\"submit\" name=\"new\" value=\"Hinzuf&uuml;gen\" /></form>";	
*/		
			

			// Objekte laden
			$bres = dbquery("SELECT def_id,def_name FROM defense ORDER BY def_name;");
			$slist=array();
			while ($barr=mysql_fetch_array($bres))
			{
				$slist[$barr['def_id']]=$barr['def_name'];
			}
		
			// Hinzufügen
			echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\" id=\"selector\">";
			infobox_start("",1);
			
			//Sonnensystem
			echo "<tr><th class=\"tbltitle\">Sonnensystem</th><td class=\"tbldata\">
			<select name=\"cell_sx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showDefenseOnPlanet',1);\">";
			echo "<option value=\"0\">Sektor X</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cell_sy\"  onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showDefenseOnPlanet',1);\">";
			echo "<option value=\"0\">Sektor Y</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select> : <select name=\"cell_cx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showDefenseOnPlanet',1);\">";
			echo "<option value=\"0\">Zelle X</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cell_cy\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showDefensesOnPlanet',1);\">";
			echo "<option value=\"0\">Zelle Y</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select></td></tr>";
			
			//User
			echo "<tr><th class=\"tbltitle\"><i>oder</i> User</th><td class=\"tbldata\">";
			echo "<input type=\"text\" name=\"userlist_nick\" id=\"userlist_nick\" value=\"\" autocomplete=\"off\" size=\"30\" maxlength=\"30\" onkeyup=\"xajax_searchUserList(this.value,'showDefenseOnPlanet');\"><br>
			<div id=\"userlist\">&nbsp;</div>";
			echo "</td></tr>";
			
			//Planeten
			echo "<tr><th class=\"tbltitle\">Planeten</th><td class=\"tbldata\" id=\"planetSelector\">Sonnensystem oder User w&auml;hlen...</td></tr>";
			
			//Schiffe Hinzufügen
			echo "<tr><th class=\"tbltitle\">Hinzuf&uuml;gen:</th><td class=\"tbldata\">
			<input type=\"text\" name=\"shiplist_count\" value=\"1\" size=\"1\" maxlength=\"3\" />
			<select name=\"ship_id\">";
			foreach ($slist as $k=>$v)
			{
				echo "<option value=\"".$k."\">".$v."</option>";
			}
			echo "</select> &nbsp; <input type=\"button\" onclick=\"xajax_addDefenseToPlanet(xajax.getFormValues('selector'));\" value=\"Hinzuf&uuml;gen\" /></td></tr>";
			
			//Vorhandene Schiffe
			echo "<tr><th class=\"tbltitle\">Vorhandene Verteidigung:</th><td class=\"tbldata\" id=\"shipsOnPlanet\">Planet w&auml;hlen...</td></tr>";
			infobox_end(1);
			echo "</form>";
			echo '<script type="text/javascript">document.forms[0].user_nick.focus();</script>';
	
			$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM ".$db_table['deflist'].";"));
			echo "Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br/>";	
		
			
		}		
	}


?>