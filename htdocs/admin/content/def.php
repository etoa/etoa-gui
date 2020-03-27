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
		if (isset($_POST['recalc']) && $_POST['recalc']!="")
		{
			echo MessageBox::ok("", Ranking::calcDefensePoints());
		}
		echo "Nach jeder direkter &Auml;nderung an den Verteidigungsanlagen via Datenbank m&uuml;ssen die Punkte neu berechnet werden: ";
		echo "<br/><br/><input type=\"submit\" name=\"recalc\" value=\"Neu berechnen\" /></form>";
		echo "<h2>Battlepoints</h2>";
		$res=dbquery("SELECT
			def_id,
			def_name,
			def_points
		FROM defense
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
	//
	//
	elseif ($sub=="transforms")
	{
		advanced_form("obj_transforms", $twig);
	}

	//
	// Bauliste
	//
	elseif ($sub=="queue")
	{
		echo "<h2>Bauliste</h2>";

		if ((isset($_POST['defqueue_search']) && $_POST['defqueue_search']!="") || (isset($_GET['action']) && $_GET['action']=="searchresults"))
		{
			$sqlstart = "
			SELECT
				queue_id,
				queue_starttime,
				queue_endtime,
				queue_objtime,
				queue_cnt,
				def_name,
				def_id,
				planet_name,
				planets.id,
				planet_user_id,
				entities.pos,
				cells.sx,
				cells.sy,
				cells.cx,
				cells.cy,
				user_nick,
				user_id,
				user_points
			FROM
					def_queue
			INNER JOIN
				planets
				ON
					queue_entity_id=planets.id
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
					queue_user_id=user_id
			INNER JOIN
				defense
				ON
					queue_def_id=def_id
			";
			$sqlend = "
			GROUP BY
					queue_id
			ORDER BY
					queue_entity_id,
					queue_endtime
					;";

			// Suchquery generieren
			if ($_SESSION['defqueue']['query']=="")
			{
				if ($_POST['planet_id']!="")
				{
					if ($sql!="") $sql.=" AND ";
					$sql.= "queue_entity_id=".$_POST['planet_id'];
				}
				if ($_POST['planet_name']!="")
				{
					if ($sql!="") $sql.=" AND ";
					if (stristr($_POST['qmode']['planet_name'],"%")) $addchars = "%";else $addchars = "";
					$sql.= "planet_name ".stripslashes($_POST['qmode']['planet_name']).$_POST['planet_name']."$addchars'";
				}
				if ($_POST['user_id']!="")
				{
					if ($sql!="") $sql.=" AND ";
					$sql.="queue_user_id=".$_POST['user_id'];
				}
				if ($_POST['user_nick']!="")
				{
					if ($sql!="") $sql.=" AND ";
					if (stristr($_POST['qmode']['user_nick'],"%")) $addchars = "%";else $addchars = "";
					$sql.= "user_nick ".stripslashes($_POST['qmode']['user_nick']).$_POST['user_nick']."$addchars'";
				}
				if ($_POST['def_id']!="")
				{
					if ($sql!="") $sql.=" AND ";
					$sql.= "queue_def_id=".$_POST['def_id'];
				}

				if ($sql!="")
				{
					$sql = $sqlstart." WHERE ".$sql.$sqlend;
				}
				else
				{
					$sql = $sqlstart.$sql.$sqlend;
				}
				$_SESSION['defqueue']['query']=$sql;
			}
			else
			{
				$sql = $_SESSION['defqueue']['query'];
			}

			$res = dbquery($sql);
			$nr = mysql_num_rows($res);
			if ($nr>0)
			{
				echo "$nr Datens&auml;tze vorhanden<br/><br/>";
				if ($nr>20)
				{
					echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /> ";
					echo "<input type=\"button\" value=\"Aktualisieren\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=searchresults'\" /><br/><br/>";
				}

				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<td class=\"tbltitle\">ID</td>";
				echo "<td class=\"tbltitle\">Schiff</td>";
				echo "<td class=\"tbltitle\">Anzahl</td>";
				echo "<td class=\"tbltitle\">Planet</td>";
				echo "<td class=\"tbltitle\">Spieler</td>";
				echo "<td class=\"tbltitle\">Start</td>";
				echo "<td class=\"tbltitle\">Ende</td>";
				echo "<td></td>";
				echo "</tr>";
				$check = array();
				$pid=0;
				while ($arr = mysql_fetch_array($res))
				{
					if ($pid>0 && $pid!=$arr['id'])
					{
						echo "<tr><td colspan=\"8\" style=\"height:3px;background:#000;\" class=\"tbldata\"></td></tr>";
					}
					$pid=$arr['id'];

					$error=false;

					// Planet gehört nicht dem Besitzer
					if ($arr['user_id']!=$arr['planet_user_id'])
					{
						$error=true;
						$errorMsg="Planet geh&ouml;rt nicht dem Schiffbesitzer! Wird auf den Heimatplaneten verschoben";
					}
					/*
					// Zu viele Schiffe im Bau
					if ($arr['shiplist_build_count']*$arr['shiplist_build_object_time'] > $arr['shiplist_build_end_time']-$arr['shiplist_build_start_time'])
					{
						$error=true;
						$errorMsg="Bauzeit fehlerhaft, zu kurze Gesamtzeit (".tf($arr['shiplist_build_count']*$arr['shiplist_build_object_time'])." n&ouml;tig, ".tf($arr['shiplist_build_end_time']-$arr['shiplist_build_start_time'])." vorhanden)!";
					}
					// Bauzeit pro Objekt ist fehlerhaft
					if ($arr['shiplist_build_object_time']!=0 && ($arr['shiplist_build_end_time']-$arr['shiplist_build_start_time'])%$arr['shiplist_build_object_time']!=0)
					{
						$error=true;
						$errorMsg="Bauanzahl fehlerhaft!";
					}
					*/

					if ($error)
						$style=" style=\"color:#f30\"";
					elseif ($arr['queue_cnt']==0)
						$style=" style=\"color:#999\"";
					else
						$style="";
					echo "<tr>";
					echo "<td class=\"tbldata\" $style>".$arr['queue_id']."</a></td>";
					echo "<td class=\"tbldata\"$style ".mTT($arr['def_name'],"<b>Schiff-ID:</b> ".$arr['def_id']).">".$arr['def_name']."</td>";
					echo "<td class=\"tbldata\"$style>".nf($arr['queue_cnt'])."</td>";
					echo "<td class=\"tbldata\"$style ".mTT($arr['planet_name'],"<b>Planet-ID:</b> ".$arr['id']."<br/><b>Koordinaten:</b> ".$arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy']." : ".$arr['planet_solsys_pos']).">".cut_string($arr['planet_name'],11)."</td>";
					echo "<td class=\"tbldata\"$style ".mTT($arr['user_nick'],"<b>User-ID:</b> ".$arr['user_id']."<br/><b>Punkte:</b> ".nf($arr['user_points'])).">".cut_string($arr['user_nick'],11)."</td>";
					echo "<td class=\"tbldata\"$style>".df($arr['queue_starttime'],1)."</td>";
					echo "<td class=\"tbldata\"$style>".df($arr['queue_endtime'],1)."</td>";
					echo "<td class=\"tbldata\"$style>".edit_button("?page=$page&sub=$sub&action=edit&id=".$arr['queue_id']);
					//if ($error)
					//	echo " ".repair_button("?page=$page&sub=$sub&action=searchresults&amp;repair=".$arr['shiplist_id'],"Fehler!",$errorMsg);
					echo "</td>";
					echo "</tr>";
				}
				$check=NULL;
				echo "</table>";
				echo "<br/><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /> ";
				echo "<input type=\"button\" value=\"Aktualisieren\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=searchresults'\" />";
			}
			else
			{
				echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" />";
			}
		}

		//
		// Auftrag bearbeiten
		//
		elseif (isset($_GET['action']) && $_GET['action']=="edit" && $_GET['id']>0)
		{
			// Änderungen speichern
			if ($_POST['save']!="")
			{
				dbquery("
				UPDATE
					def_queue
				SET
        	queue_cnt='".$_POST['queue_cnt']."',
        	queue_starttime=UNIX_TIMESTAMP('".$_POST['queue_starttime']."'),
        	queue_endtime=UNIX_TIMESTAMP('".$_POST['queue_endtime']."')
				WHERE
					queue_id='".$_GET['id']."';");
			}

			// Auftrag löschen
			elseif ($_POST['del']!="")
			{
				dbquery("
				DELETE FROM
					def_queue
				WHERE
					queue_id='".$_GET['id']."';");
				echo "Datensatz entfernt!<br/><br/>";
			}

			// Auftrag abschliessen
			elseif ($_POST['build_finish']!="")
			{
				$res = dbquery("
				SELECT
					queue_entity_id,
					queue_user_id,
					queue_def_id,
					queue_cnt
				FROM
	      	def_queue
	      WHERE
	      	queue_id='".$_GET['id']."'
	      ;");
	      if (mysql_num_rows($res)>0)
	      {
	      	$arr=mysql_fetch_array($res);
					shiplistAdd($arr['queue_entity_id'],$arr['queue_user_id'],$arr['queue_def_id'],$arr['queue_cnt']);
					dbquery("
					DELETE FROM
						def_queue
					WHERE
						queue_id='".$_GET['id']."'
					;");
				}
				echo "Bau abgeschlossen!<br/><br/>";
			}

			$res = dbquery("
			SELECT
				queue_id,
				queue_objtime,
				queue_starttime,
				queue_endtime,
				queue_cnt,
				def_name,
				planet_name,
				user_nick
			FROM
      	def_queue
      INNER JOIN
      	defense
      	ON queue_def_id=def_id
      INNER JOIN
      	users
      	ON queue_user_id=user_id
      INNER JOIN
      	planets
      	ON queue_entity_id=planets.id
			WHERE
	       queue_id=".intval($_GET['id']).";");

			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				if ($arr['queue_starttime']>0)
					$bst = date(DATE_FORMAT,$arr['queue_starttime']);
				else
					$bst = "";
				if ($arr['queue_endtime']>0)
					$bet = date(DATE_FORMAT,$arr['queue_endtime']);
				else
					$bet = "";

				echo "<form action=\"?page=$page&sub=$sub&action=edit&id=".$arr['queue_id']."\" method=\"post\">";
				echo "<table class=\"tbl\">";
				echo "<tr><td class=\"tbltitle\">ID</td><td class=\"tbldata\">".$arr['queue_id']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Planet</td><td class=\"tbldata\">".$arr['planet_name']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Spieler</td><td class=\"tbldata\">".$arr['user_nick']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Schiff</td><td class=\"tbldata\">".$arr['def_name']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Anzahl</td><td class=\"tbldata\"><input type=\"text\" name=\"queue_cnt\" value=\"".$arr['queue_cnt']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\">Baustart</td><td class=\"tbldata\">
				<input type=\"text\" id=\"shiplist_build_start_time\" name=\"queue_starttime\" value=\"$bst\" size=\"20\" maxlength=\"30\" />
				<input type=\"button\" value=\"Jetzt\" onclick=\"document.getElementById('shiplist_build_start_time').value='".date("Y-d-m h:i")."'\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\">Bauende</td><td class=\"tbldata\">
				<input type=\"text\" id=\"shiplist_build_end_time\" name=\"queue_endtime\" value=\"$bet\" size=\"20\" maxlength=\"30\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\">Bauzeit pro Schiff</td><td class=\"tbldata\">".tf($arr['queue_objtime'])."</td></tr>";
				echo "</table><br/>";
				echo "<input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
				echo "<input type=\"submit\" name=\"build_finish\" value=\"Bau fertigstellen\" />&nbsp;";
				echo "<input type=\"submit\" name=\"del\" value=\"L&ouml;schen\" class=\"button\" onclick=\"return confirm('Schiffe wirklich l&ouml;schen?')\" />&nbsp;";
				echo "<hr/>";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" />&nbsp;";
				echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&sub=$sub';\" />";
				echo "</form>";
			}
			else
			{
				echo "Dieser Datensatz existiert nicht mehr!<br/><br/>";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" />&nbsp;";
			}
		}


		//
		// Suchmaske Schiffaufträge
		//
		else
		{
			$_SESSION['defqueue']['query']="";

			// Schiffe laden
			$bres = dbquery("SELECT def_id,def_name FROM defense ORDER BY def_name;");
			$slist=array();
			while ($barr=mysql_fetch_array($bres))
			{
				$slist[$barr['def_id']]=$barr['def_name'];
			}

			// Suchmaske
			echo "Suchmaske:<br/><br/>";
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\">Planet ID</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td>";
			echo "<tr><td class=\"tbltitle\">Planetname</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('planet_name');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Spieler ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Spieler Nick</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('user_nick');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Verteidigung</td><td class=\"tbldata\"><select name=\"def_id\"><option value=\"\"><i>---</i></option>";
			foreach ($slist as $k=>$v)
			{
				echo "<option value=\"".$k."\">".$v."</option>";
			}
			echo "</select></td>";
			echo "</table>";
			echo "<br/><input type=\"submit\" class=\"button\" name=\"defqueue_search\" value=\"Suche starten\" /></form>";
			$tblcnt = mysql_fetch_row(dbquery("SELECT COUNT(queue_id) FROM def_queue;"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br/>";
		}
	}


	//
	// Bearbeiten
	//
	elseif ($sub=="data")
	{
		advanced_form("defense", $twig);
	}

	//
	// Kategorien
	//
	elseif ($sub=="cat")
	{
		advanced_form("def_cat", $twig);
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
		define("REQ_ITEM_FLD","obj_id");
		define("ITEM_ID_FLD","def_id");
		define("ITEM_NAME_FLD","def_name");
		define("ITEM_ENABLE_FLD","def_buildable");
		define("ITEM_ORDER_FLD","def_cat_id,def_order,def_name");

		define("ITEM_IMAGE_PATH",IMAGE_PATH."/defense/def<DB_TABLE_ID>_small.".IMAGE_EXT);


		include("inc/requirements.inc.php");

	}

	//
	// Liste
	//
	else
	{
		echo "<h1>Verteidigungsliste</h1>";

		if (isset($_POST['deflist_search']) || (isset($_GET['action']) && $_GET['action']=="searchresults") || isset($_POST['new']))
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
		  	deflist_count
			FROM 
				deflist,
				entities,
				planets,
				cells,
				users,
				defense 
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
      $sql = "";

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
				$bres = dbquery("SELECT def_id,def_name FROM defense ORDER BY def_name;");
				$slist=array();
				while ($barr=mysql_fetch_array($bres))
					$slist[$barr['def_id']]=$barr['def_name'];

				// Hinzufügen
				echo "<h2>Neue Verteidigungsanlagen hinzuf&uuml;gen</h2>";
				echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
				tableStart();
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
				tableEnd();
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
			elseif (isset($_GET['cleanup']) && $_GET['cleanup']==1)
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
					echo "<td class=\"tbldata\" $style".mTT($arr['planet_name'],"<b>Planet-ID:</b> ".$arr['id']."<br/><b>Koordinaten:</b> ".$arr['sx']."/".$arr['sy']." : ".$arr['cx']."/".$arr['cy']." : ".$arr['pos']).">".cut_string($arr['planet_name'],11)."</a></td>";
					echo "<td class=\"tbldata\" $style".mTT($arr['user_nick'],"<b>User-ID:</b> ".$arr['user_id']."<br/><b>Punkte:</b> ".nf($arr['user_points'])).">".cut_string($arr['user_nick'],11)."</a></td>";
					echo "<td class=\"tbldata\" $style".mTT($arr['def_name'],"<b>Verteidigungs-ID:</b> ".$arr['def_id']).">".$arr['def_name']."</a></td>";
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
		elseif (isset($_GET['action']) && $_GET['action']=="edit")
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
			$bres = dbquery("SELECT def_id,def_name FROM defense ORDER BY def_name;");
			$dlist=array();
			while ($barr=mysql_fetch_array($bres))
				$dlist[$barr['def_id']]=$barr['def_name'];


			echo "<h2>Schnellsuche</h2>";
			// Hinzufügen
			echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\" id=\"selector\">";
			tableStart();

			//Sonnensystem
			echo "<tr><th class=\"tbltitle\">Sonnensystem</th><td class=\"tbldata\">
			<select name=\"cell_sx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showDefenseOnPlanet');\">";
			echo "<option value=\"0\">Sektor X</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cell_sy\"  onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showDefenseOnPlanet');\">";
			echo "<option value=\"0\">Sektor Y</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select> : <select name=\"cell_cx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showDefenseOnPlanet');\">";
			echo "<option value=\"0\">Zelle X</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cell_cy\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showDefenseOnPlanet');\">";
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

			//Def Hinzufügen
			echo "<tr><th class=\"tbltitle\">Hinzuf&uuml;gen:</th><td class=\"tbldata\">
			<input type=\"text\" name=\"deflist_count\" value=\"1\" size=\"1\" maxlength=\"3\" />
			<select name=\"def_id\">";
			foreach ($dlist as $k=>$v)
			{
				echo "<option value=\"".$k."\">".$v."</option>";
			}
			echo "</select> &nbsp; <input type=\"button\" onclick=\"xajax_addDefenseToPlanet(xajax.getFormValues('selector'));\" value=\"Hinzuf&uuml;gen\" /></td></tr>";

			//Vorhandene Def
			echo "<tr><th class=\"tbltitle\">Vorhandene Verteidigung:</th><td class=\"tbldata\" id=\"shipsOnPlanet\">Planet w&auml;hlen...</td></tr>";
			tableEnd();
			echo "</form>";
			echo '<script type="text/javascript">document.forms[0].user_nick.focus();</script>';

			//Add User
			if (searchQueryArray($sa,$so))
			{
				if (isset($sa['user_nick']))
				{
					echo "<script type=\"text/javascript\">document.getElementById('userlist_nick').value=\"".$sa['user_nick'][1]."\";xajax_searchUserList('".$sa['user_nick'][1]."','showDefenseOnPlanet');</script>";
				}
			}

			$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM deflist;"));
			echo "Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br/><br />";


			// Suchmaske
			echo "<h2>Suchmaske</h2>";

			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			tableStart();
			echo "<tr><th class=\"tbltitle\">Planet ID</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><th class=\"tbltitle\">Planetname</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('planet_name');echo "</td></tr>";
			echo "<tr><th class=\"tbltitle\">Spieler ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><th class=\"tbltitle\">Spieler Nick</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\"/> ";fieldqueryselbox('user_nick');echo "<br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></tr>";
			echo "<tr><th class=\"tbltitle\">Verteidigung</td><td class=\"tbldata\"><select name=\"def_id\"><option value=\"\"><i>---</i></option>";
			foreach ($dlist as $k=>$v)
				echo "<option value=\"".$k."\">".$v."</option>";
			echo "</select></td></tr>";
			tableEnd();
			echo "<br/><input type=\"submit\" class=\"button\" name=\"deflist_search\" value=\"Suche starten\" /></form>";


		}
	}


?>
