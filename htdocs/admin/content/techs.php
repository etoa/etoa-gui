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
		if (isset($_POST['recalc']) && $_POST['recalc']!="")
		{
			echo MessageBox::ok("", Ranking::calcTechPoints());
		}
		echo "Nach jeder &Auml;nderung an den Forschungen m&uuml;ssen die Forschungspunkte neu berechnet werden.<br/><br/> ";
		echo "<input type=\"submit\" name=\"recalc\" value=\"Neu berechnen\" /></form>";

		echo "<h2>Forschungspunkte</h2>";
		$res=dbquery("SELECT
			tech_id,
			tech_name
		FROM technologies
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
				FROM tech_points
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
	// Kategorien
	//
	elseif ($sub=="type")
	{
		simple_form("tech_types", $twig);
	}

	//
	// Technologien
	//
	elseif ($sub=="data")
	{
		advanced_form("technologies", $twig);
	}
	//
	// Anforderungen
	//
	elseif ($sub=="req")
	{

		define("TITLE","Forschungsanforderungen");
		define("REQ_TBL","tech_requirements");
		define("ITEMS_TBL","technologies");
		define("ITEM_ID_FLD","tech_id");
		define("ITEM_NAME_FLD","tech_name");
		define("ITEM_ENABLE_FLD","tech_show");
		define("ITEM_ORDER_FLD","tech_type_id,tech_order,tech_name");

		define("ITEM_IMAGE_PATH",IMAGE_PATH."/technologies/technology<DB_TABLE_ID>_small.".IMAGE_EXT);

		include("inc/requirements.inc.php");

	}

	//
	// Liste
	//
	else
	{
        $twig->addGlobal('title', 'Forschungsliste');

		$build_type[0]="Unt&auml;tig";
		$build_type[3]="Forschen";

		if (isset($_POST['techlist_search']) || (isset($_GET['action']) && ($_GET['action']=="search" || $_GET['action']=="searchresults")) || isset($_POST['new']))
		{
			if (isset($_GET['query']) && $_GET['query']!="")
			{
				$qs = searchQueryDecode($_GET['query']);
				foreach($qs as $k=>$v)
				{
					$_POST[$k]=$v;
				}
				$_SESSION['search']['tech']['query']=null;
			}
			$sql = "";
			$query = "";
			$sqlstart = "
			SELECT 
					planet_name,
					planets.id as id,
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
				techlist_entity_id=planets.id
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
				techlist_entity_id,
				tech_type_id,
				tech_order,
				tech_name;";

			// Forschung hinzufügen
			if (isset($_POST['new']))
			{
     			$updata=explode(":",$_POST['planet_id']);

     			if(isset($_POST['all_techs'])) {
     				$res=dbquery("SELECT
						tech_id,
						tech_name
					FROM technologies
					ORDER BY tech_order,tech_name;");
					if (mysql_num_rows($res)>0)
					{
						while ($arr=mysql_fetch_array($res))
						{
							if (mysql_num_rows(dbquery("SELECT techlist_id FROM techlist WHERE techlist_user_id=".$updata[1]." AND techlist_tech_id=".$arr['tech_id'].";"))==0)
							{
								dbquery("INSERT INTO techlist (techlist_entity_id,techlist_user_id,techlist_tech_id,techlist_current_level) VALUES (".$updata[0].",".$updata[1].",".$arr['tech_id'].",".$_POST['techlist_current_level'].");");
							}
							else
							{
								dbquery("UPDATE techlist 
										 SET techlist_current_level = ".$_POST['techlist_current_level']."
										 WHERE techlist_user_id = ".$updata[1]."
										 AND techlist_tech_id = ".$arr['tech_id']);
							}

						}
					}
					echo "Technologien wurden aktualisiert!<br/>";
     			}
     			else {
	     			if (mysql_num_rows(dbquery("SELECT techlist_id FROM techlist WHERE techlist_user_id=".$updata[1]." AND techlist_tech_id=".$_POST['tech_id'].";"))==0)
					{
						dbquery("INSERT INTO techlist (techlist_entity_id,techlist_user_id,techlist_tech_id,techlist_current_level) VALUES (".$updata[0].",".$updata[1].",".$_POST['tech_id'].",".$_POST['techlist_current_level'].");");
						echo "Technologie wurde hinzugef&uuml;gt!<br/>";
					}
					else
					{
						dbquery("UPDATE techlist 
								 SET techlist_current_level = ".$_POST['techlist_current_level']."
								 WHERE techlist_user_id = ".$updata[1]."
								 AND techlist_tech_id = ".$_POST['tech_id']);
						echo "Technologie wurde aktualisiert!<br/>";
					}
     			}

				$sql= " AND user_id=".$updata[1];
				$_SESSION['search']['tech']['query']=null;

				// Technologien laden
				$bres = dbquery("SELECT tech_id,tech_name FROM technologies ORDER BY tech_type_id,tech_order,tech_name;");
				$tlist=array();
				while ($barr=mysql_fetch_array($bres))
					$tlist[$barr['tech_id']]=$barr['tech_name'];

				// Hinzufügen
				echo "<h2>Neue Technologien hinzuf&uuml;gen</h2>";
				echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
				tableStart();
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
				$pres=dbquery("SELECT user_id,user_nick,planets.id FROM users,planets WHERE planet_user_id=user_id AND planet_user_main=1 ORDER BY user_nick;");
				while ($parr=mysql_fetch_array($pres))
				{
					echo "<option value=\"".$parr['id'].":".$parr['user_id']."\"";
					if ($updata[1]==$parr['user_id']) echo " selected=\"selected\"";
					echo ">".$parr['user_nick']."</option>";
				}
				echo "</select></td></tr>";
				tableEnd();
				echo "<input type=\"submit\" name=\"new\" value=\"Hinzuf&uuml;gen\" /></form><br/>";
				$sql = $sqlstart.$sql.$sqlend;
				$_SESSION['search']['tech']['query']=$sql;
			}

			// Suchquery generieren
			elseif ($_SESSION['search']['tech']['query']==null)
			{
				if ($_POST['planet_id']!='')
					$sql.= " AND planets.id='".$_POST['planet_id']."'";
				if ($_POST['planet_name']!='')
				{
					if (stristr($_POST['qmode']['planet_name'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND planet_name ".stripslashes($_POST['qmode']['planet_name']).$_POST['planet_name']."$addchars'";
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
				$_SESSION['search']['tech']['query']=$sql;
			}
			else
				$sql = $_SESSION['search']['tech']['query'];

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
					if ($arr['techlist_build_type']==3)
						$style=" style=\"color:#0f0\"";
					else
						$style="";
					echo "<tr>";
					echo "<td class=\"tbldata\"$style ".mTT($arr['planet_name'],$arr['sx']."/".$arr['sy']." : ".$arr['cx']."/".$arr['cy']." : ".$arr['pos']).">".cut_string($arr['planet_name'] != '' ? $arr['planet_name'] : 'Unbenannt',11)."</a> [".$arr['id']."]</a></td>";
					echo "<td class=\"tbldata\"$style ".mTT($arr['user_nick'],nf($arr['user_points'])." Punkte").">".cut_string($arr['user_nick'],11)."</a></td>";
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
		elseif (isset($_GET['action']) && $_GET['action']=="edit")
		{
			if (isset($_POST['save']))
			{
				dbquery("UPDATE techlist SET techlist_current_level='".$_POST['techlist_current_level']."',techlist_build_type='".$_POST['techlist_build_type']."',techlist_build_start_time=UNIX_TIMESTAMP('".$_POST['techlist_build_start_time']."'),techlist_build_end_time=UNIX_TIMESTAMP('".$_POST['techlist_build_end_time']."') WHERE techlist_id='".$_GET['techlist_id']."';");
			}
			elseif (isset($_POST['del']))
			{
				dbquery("DELETE FROM techlist WHERE techlist_id='".$_GET['techlist_id']."';");
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
								techlist.techlist_entity_id=planets.id
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
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Baustart</td><td class=\"tbldata\"><input type=\"text\" name=\"techlist_build_start_time\" id=\"techlist_build_start_time\" value=\"$bst\" size=\"20\" maxlength=\"30\" /> <input type=\"button\" value=\"Jetzt\" onclick=\"document.getElementById('techlist_build_start_time').value='".date("Y-m-d H:i:s")."'\" /></td></tr>";
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
			echo '<div class="tabs">
			<ul>
				<li><a href="#tabs-1">Suchmaske</a></li>
				<li><a href="#tabs-2">Direkt hinzufügen</a></li>
			</ul>
			<div id="tabs-1">';

			$_SESSION['search']['tech']['query']=null;

			// Technologien laden
			$bres = dbquery("SELECT tech_id,tech_name FROM technologies ORDER BY tech_type_id,tech_order,tech_name;");
			$tlist=array();
			while ($barr=mysql_fetch_array($bres)) {
				$tlist[$barr['tech_id']]=$barr['tech_name'];
			}

			// Suchmaske
			echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
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

			echo '</div><div id="tabs-2">';

			// Hinzufügen
			echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><th class=\"tbltitle\">Technologie</th><td class=\"tbldata\"><select name=\"tech_id\">";
			foreach ($tlist as $k=>$v)
				echo "<option value=\"".$k."\">".$v."</option>";
			echo "</select><br>Alle Techs <input type='checkbox' name='all_techs'></td></tr>";
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
			tableEnd();
			echo "<p><input type=\"submit\" name=\"new\" value=\"Hinzuf&uuml;gen\" /></p></form>";

			echo '
				</div>
			</div>';

			$tblcnt = mysql_fetch_row(dbquery("SELECT 
													count(*) 
												FROM 
													techlist;"));
			echo "<p>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.</p>";

		}
	}


?>
