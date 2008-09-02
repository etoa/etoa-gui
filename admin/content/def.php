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
		echo "<h1>Punkte</h1>";
		echo "<h2>Punkte neu berechnen</h2><form action=\"?page=$page&amp;sub=$sub\" method=\"POST\">";
		if ($_POST['recalc']!="")
		{
			cms_ok_msg(calcDefensePoints());
		}		
		echo "Nach jeder direkter &Auml;nderung an den Verteidigungsanlagen via Datenbank m&uuml;ssen die Punkte neu berechnet werden: ";
		echo "<br/><br/><input type=\"submit\" name=\"recalc\" value=\"Neu berechnen\" /></form>";		
		echo "<h2>Battlepoints</h2>";
		$res=dbquery("SELECT
			def_id,
			def_name,
			def_points
		FROM ".$db_table['defense']."
		ORDER BY def_points DESC, def_name DESC;");
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\">";
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr><th>".$arr['def_name']."</th><td style=\"width:70%\">".$arr['def_points']."</td></tr>";
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

		include("inc/requirements.inc.php");

	}

	//
	// Liste
	//
	else
	{
		echo "<h1>Verteidigungsliste</h1>";
	
		if (isset($_POST['deflist_search']) || $_GET['action']=="searchresults" || isset($_POST['new']))
		{
	
			$sqlstart = "SELECT 
				planets.id,
				planet_name,
		  		entities.pos,
		  	cells.sx,cells.sy,
		  	cells.cx,cells.cy,
		  	user_id,
		  	user_nick,
		  	user_points,
		  	def_id,
		  	def_name,
		  	deflist_id,
		  	deflist_count,
		  	deflist_build_count			
			FROM 
				deflist,
				entities,
				".$db_table['planets'].",
				cells,
				".$db_table['users'].",
				".$db_table['defense']." 
			WHERE 
				planets.id=entities.id
		    AND	entities.cell_id=cells.id 
					AND deflist_def_id=def_id 
					AND user_id=deflist_user_id 
					AND planets.id=deflist_entity_id ";
			$sqlend = " 
			GROUP BY 
					deflist_id 
			ORDER BY 
					deflist_entity_id,
					def_order,def_name;";
  
			// Verteidigung hinzufügen
			if (isset($_POST['new']))
			{
				$updata=explode(":",$_POST['planet_id']);
				if (mysql_num_rows(dbquery("SELECT deflist_id FROM deflist WHERE deflist_entity_id=".$updata[0]." AND deflist_def_id=".$_POST['def_id'].";"))==0)
				{
					dbquery("INSERT INTO deflist (deflist_entity_id,deflist_user_id,deflist_def_id,deflist_count) VALUES (".$updata[0].",".$updata[1].",".$_POST['def_id'].",".$_POST['deflist_count'].");");					
					echo "Verteidigung wurde hinzugef&uuml;gt!<br/>";
				}
				else
				{
					dbquery("UPDATE deflist SET deflist_count=deflist_count+".$_POST['deflist_count']." WHERE deflist_entity_id=".$updata[0]." AND deflist_def_id=".$_POST['def_id'].";");
					echo "Verteidigung wurde hinzugef&uuml;gt!<br/>";
				}
				$sql= " AND planets.id=".$updata[0];
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
				$pres=dbquery("SELECT 
								users.user_id,
								planets.id,
								planet_name,
								users.user_nick,
								entities.pos,
								cells.sx,
								cells.sy,
								cells.cx,
								cells.cy 
							FROM 
								users
							INNER JOIN
								planets
								ON planets.planet_user_id=users.user_id
							INNER JOIN
								entities
								ON entities.id=planets.id
							INNER JOIN
								cells
								ON cells.id=entities.cell_id
							ORDER BY 
								planets.id;");
				while ($parr=mysql_fetch_array($pres))
				{
					echo "<option value=\"".$parr['id'].":".$parr['user_id']."\"";
					if ($updata[0]==$parr['id']) echo " selected=\"selected\"";
					echo ">".$parr['sx']."/".$parr['sy']." : ".$parr['cx']."/".$parr['cy']." : ".$parr['pos']." &nbsp; ".$parr['planet_name']." (".$parr['user_nick'].")</option>";
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
					$sql.= " AND id='".$_POST['planet_id']."'";
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
  	

			if (isset($_POST['save']))
			{
				dbquery("UPDATE 
					deflist 
				SET 
					deflist_count='".$_POST['deflist_count']."'
				WHERE 
					deflist_id='".$_POST['deflist_id']."';");
				success_msg("Gespeichert");
			}
			elseif (isset($_POST['del']))
			{
				dbquery("DELETE FROM deflist WHERE deflist_id='".$_POST['deflist_id']."';");
				success_msg("Gelöscht");
			}
			elseif ($_GET['cleanup']==1)
			{
				dbquery("DELETE FROM deflist WHERE deflist_count=0;");
				success_msg("Aufgeräumt");
			}
  	
			$res = dbquery($sql);
			if (mysql_num_rows($res)>0)
			{
				echo mysql_num_rows($res)." Datens&auml;tze vorhanden<br/><br/>";
				if (mysql_num_rows($res)>20)
				{
					echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&sub=$sub'\" /> ";
  				echo "<input type=\"button\" value=\"Aktualisieren\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=searchresults'\" /> ";					
  				echo "<input type=\"button\" value=\"Clean-Up\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=searchresults&amp;cleanup=1'\" /><br/><br/>";					
  			}
				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<td class=\"tbltitle\">ID</td>";
				echo "<td class=\"tbltitle\">Planet</td>";
				echo "<td class=\"tbltitle\">Spieler</td>";
				echo "<td class=\"tbltitle\">Verteidigung</td>";
				echo "<td class=\"tbltitle\">Anzahl</td>";
				echo "</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					if ($arr['deflist_count']==0)
						$style=" style=\"color:#999\"";
					else
						$style="";					
					
					echo "<tr>";
					echo "<td class=\"tbldata\" $style>".$arr['deflist_id']."</a></td>";
					echo "<td class=\"tbldata\" $style".tm($arr['planet_name'],"<b>Planet-ID:</b> ".$arr['id']."<br/><b>Koordinaten:</b> ".$arr['sx']."/".$arr['sy']." : ".$arr['cx']."/".$arr['cy']." : ".$arr['pos']).">".cut_string($arr['planet_name'],11)."</a></td>";
					echo "<td class=\"tbldata\" $style".tm($arr['user_nick'],"<b>User-ID:</b> ".$arr['user_id']."<br/><b>Punkte:</b> ".nf($arr['user_points'])).">".cut_string($arr['user_nick'],11)."</a></td>";
					echo "<td class=\"tbldata\" $style".tm($arr['def_name'],"<b>Verteidigungs-ID:</b> ".$arr['def_id']).">".$arr['def_name']."</a></td>";
					echo "<td class=\"tbldata\" $style>".nf($arr['deflist_count'])."</a></td>";
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
			$res = dbquery("SELECT 
								deflist.deflist_id,
								deflist.deflist_count,
								defense.def_name,
								users.user_nick,
								planets.planet_name
							FROM 
								deflist
							INNER JOIN
								defense
							ON
								deflist.deflist_def_id=defense.def_id
							INNER JOIN
								users
							ON
								deflist.deflist_user_id=users.user_id
							INNER JOIN
								planets
							ON
								deflist.deflist_entity_id=planets.id;");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				echo "<form action=\"?page=$page&sub=$sub&action=searchresults\" method=\"post\">";
				echo "<input type=\"hidden\" name=\"deflist_id\" value=\"".$arr['deflist_id']."\" />";
				echo "<table class=\"tbl\">";
				echo "<tr><td class=\"tbltitle\">ID</td><td class=\"tbldata\">".$arr['deflist_id']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Planet</td><td class=\"tbldata\">".$arr['planet_name']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Spieler</td><td class=\"tbldata\">".$arr['user_nick']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Verteidigung</td><td class=\"tbldata\">".$arr['def_name']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Anzahl</td><td class=\"tbldata\">
					<input type=\"text\" name=\"deflist_count\" value=\"".$arr['deflist_count']."\" size=\"5\" maxlength=\"20\" /></td></tr>";

				echo "</table><br/>";
				echo "<input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
				echo "<input type=\"submit\" name=\"del\" value=\"L&ouml;schen\" class=\"button\" onclick=\"return confirm('Wirklich löschen?');\" />&nbsp;";
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
			echo "<h2>Suchmaske</h2>";

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
			echo "</table>";
			echo "<br/><input type=\"submit\" class=\"button\" name=\"deflist_search\" value=\"Suche starten\" /></form><br/>";
	
			

			// Objekte laden
			$bres = dbquery("SELECT def_id,def_name FROM defense ORDER BY def_name;");
			$slist=array();
			while ($barr=mysql_fetch_array($bres))
			{
				$slist[$barr['def_id']]=$barr['def_name'];
			}
			echo "<h2>Schnellsuche</h2>";
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

			$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM deflist;"));
			echo "Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br/>";	
	
			
		}		
	}


?>