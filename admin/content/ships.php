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
	// 	Dateiname: ships.php
	// 	Topic: Schiffverwaltung
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
			cms_ok_msg(calcShipPoints());
		}
		echo "Nach jeder direkter &Auml;nderung an den Schiffen via Datenbank m&uuml;ssen die Punkte neu berechnet werden: ";
		echo "<br/><br/><input type=\"submit\" name=\"recalc\" value=\"Neu berechnen\" /></form>";
		echo "<h2>Battlepoints</h2>";
		$res=dbquery("
		SELECT
			ship_id,
			ship_name,
			ship_points
		FROM
			ships
		ORDER BY
			ship_points DESC,
			ship_name DESC;");
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\">";
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr><th>".$arr['ship_name']."</th><td style=\"width:70%\">".$arr['ship_points']."</td></tr>";
			}
			echo "</table>";
		}
	}


	//
	// XP-Rechner
	//
	elseif ($sub=="xpcalc")
	{
		echo "<h1>XP-Rechner</h1>";

		echo "Schiff wählen: <select onchange=\"document.location='?page=".$page."&sub=".$sub."&id='+this.options[this.selectedIndex].value\">";
		$res = dbquery("
		SELECT
			ship_name,
			ship_id,
			special_ship_need_exp,
			special_ship_exp_factor
		FROM
			ships
		WHERE
			special_ship=1
		ORDER BY
			ship_name		
		");
		while ($arr=mysql_fetch_array($res))
		{
			if (!isset($ship_xp))
				$ship_xp = $arr['special_ship_need_exp'];
			if (!isset($ship_xp_multiplier))
				$ship_xp_multiplier = $arr['special_ship_exp_factor'];
			echo "<option value=\"".$arr['ship_id']."\"";
			if ($_GET['id']==$arr['ship_id'])
			{
				echo " selected=\"selected\"";
				$ship_xp = $arr['special_ship_need_exp'];
				$ship_xp_multiplier = $arr['special_ship_exp_factor'];
			}
			echo ">".$arr['ship_name']."</option>";
		}
		echo "</select><br/><br/>";

		echo "<table class=\"tb\"><tr><th>Level</th><th>Experience</th></tr>";	
		for ($level=1;$level<=30;$level++)
		{
			echo "<tr><td>$level</td><td>".nf(Ship::xpByLevel($ship_xp,$ship_xp_multiplier,$level))."</td></tr>";
			
		}
		echo "</table>";

		
	}


	//
	// Kategorien
	//
	elseif ($sub=="cat")
	{
		simple_form("ship_cat");
	}

	//
	// Daten
	//
	elseif ($sub=="data")
	{
		advanced_form("ships");
	}
	
	//
	// Schiffsanforderungen
	//
	elseif ($sub=="req")
	{
		//Definistion für die normalen Schiffe
		define("TITLE","Schiffanforderungen");
		define("REQ_TBL","ship_requirements");
		define("ITEMS_TBL","ships");
		define("ITEM_ID_FLD","ship_id");
		define("ITEM_NAME_FLD","ship_name");
		define("ITEM_ENABLE_FLD","ship_buildable");
		define("ITEM_ORDER_FLD","ship_cat_id,ship_order");

		define("ITEM_IMAGE_PATH",IMAGE_PATH."/ships/ship<DB_TABLE_ID>_small.".IMAGE_EXT);

		include("inc/requirements.inc.php");

	}

	//
	// Bauliste
	//
	elseif ($sub=="queue")
	{
		echo "<h2>Bauliste</h2>";

		if ($_POST['shipqueue_search']!="" || $_GET['action']=="searchresults")
		{
			$sqlstart = "
			SELECT
				queue_id,
				queue_starttime,
				queue_endtime,
				queue_objtime,
				queue_cnt,
				ship_name,
				ship_id,
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
					ship_queue
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
				ships
				ON
					queue_ship_id=ship_id
			";
			$sqlend = "
			GROUP BY
					queue_id
			ORDER BY
					queue_entity_id,
					queue_endtime
					;";

			// Suchquery generieren
			if ($_SESSION['shipqueue']['query']=="")
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
				if ($_POST['ship_id']!="")
				{
					if ($sql!="") $sql.=" AND ";
					$sql.= "queue_ship_id=".$_POST['ship_id'];
				}

				if ($sql!="")
				{
					$sql = $sqlstart." WHERE ".$sql.$sqlend;
				}
				else
				{
					$sql = $sqlstart.$sql.$sqlend;
				}
				$_SESSION['shipqueue']['query']=$sql;
			}
			else
			{
				$sql = $_SESSION['shipqueue']['query'];
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
					echo "<td class=\"tbldata\"$style ".mTT($arr['ship_name'],"<b>Schiff-ID:</b> ".$arr['ship_id']).">".$arr['ship_name']."</td>";
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
		elseif ($_GET['action']=="edit" && $_GET['id']>0)
		{
			// Änderungen speichern
			if ($_POST['save']!="")
			{
				dbquery("
				UPDATE
					ship_queue
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
					ship_queue
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
					queue_ship_id,
					queue_cnt
				FROM
	      	ship_queue
	      WHERE
	      	queue_id='".$_GET['id']."'
	      ;");
	      if (mysql_num_rows($res)>0)
	      {
	      	$arr=mysql_fetch_array($res);
					shiplistAdd($arr['queue_entity_id'],$arr['queue_user_id'],$arr['queue_ship_id'],$arr['queue_cnt']);
					dbquery("
					DELETE FROM
						ship_queue
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
				ship_name,
				planet_name,
				user_nick
			FROM
      	ship_queue
      INNER JOIN
      	ships
      	ON queue_ship_id=ship_id
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
				echo "<tr><td class=\"tbltitle\">Schiff</td><td class=\"tbldata\">".$arr['ship_name']."</td></tr>";
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
			$_SESSION['shipqueue']['query']="";

			// Schiffe laden
			$bres = dbquery("SELECT ship_id,ship_name FROM ships ORDER BY ship_name;");
			$slist=array();
			while ($barr=mysql_fetch_array($bres))
			{
				$slist[$barr['ship_id']]=$barr['ship_name'];
			}

			// Suchmaske
			echo "Suchmaske:<br/><br/>";
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\">Planet ID</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td>";
			echo "<tr><td class=\"tbltitle\">Planetname</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('planet_name');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Spieler ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Spieler Nick</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('user_nick');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Schiff</td><td class=\"tbldata\"><select name=\"ship_id\"><option value=\"\"><i>---</i></option>";
			foreach ($slist as $k=>$v)
			{
				echo "<option value=\"".$k."\">".$v."</option>";
			}
			echo "</select></td>";
			echo "</table>";
			echo "<br/><input type=\"submit\" class=\"button\" name=\"shipqueue_search\" value=\"Suche starten\" /></form>";
			$tblcnt = mysql_fetch_row(dbquery("SELECT COUNT(queue_id) FROM ship_queue;"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br/>";
		}
	}

	/**************
	* Schiffliste *
	**************/
	else
	{
		echo "<h2>Schiffliste</h2>";

/*
		if ($_POST['shiplist_search']!="" || $_GET['action']=="searchresults" || $_POST['new']!="")
		{
			$sqlstart = "
			SELECT
				planet_name,
				id,
				planet_user_id,
		      planet_solsys_pos,
		      cell_sx,cell_sy,
		      cell_cx,cell_cy,
		      user_nick,
		      user_id,
		      user_points,
		      ship_id,
		      ship_name,
		      shiplist_id,
		      shiplist_count
			FROM
					shiplist
			INNER JOIN
				planets
				ON planets.id=shiplist_entity_id
			INNER JOIN
				space_cells
				ON planet_solsys_id=cell_id
			INNER JOIN
				users
				ON user_id=shiplist_user_id
			INNER JOIN
				ships
		    ON shiplist_ship_id=ship_id
			";
			$sqlend = "
			GROUP BY
					shiplist_id
			ORDER BY
					shiplist_entity_id,special_ship DESC,
					ship_name;";

			// Fehlerkorrektur
			if ($_GET['repair']>0)
			{
				$res=dbquery($sqlstart." AND shiplist_id=".$_GET['repair'].$sqlend);
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					if ($arr['user_id']!=$arr['planet_user_id'])
					{
						$pres=dbquery("SELECT id FROM planets WHERE planet_user_main=1 AND planet_user_id=".$arr['user_id']." LIMIT 1;");
						if (mysql_num_rows($pres))
						{
							$parr=mysql_fetch_row($pres);
							shiplistAdd($parr[0],$arr['user_id'],$arr['ship_id'],$arr['shiplist_count']);
							dbquery("DELETE FROM shiplist WHERE shiplist_id=".$arr['shiplist_id'].";");
							cms_ok_msg("Schiffe wurden auf den Hauptplaneten verschoben!");
						}
						else
							cms_err_msg("Reparatur nicht m&ouml;glich, Hauptplaneten des Besitzers nicht gefunden!");
					}
					elseif  ($arr['shiplist_count']<0)
					{
						dbquery("UPDATE shiplist SET shiplist_count=0 WHERE shiplist_id=".$arr['shiplist_id'].";");
						cms_ok_msg("Fehlerhafte Anzahl wurde behoben!");
					}
					else
					{
						// Doppelschiffe reparieren
						$lres=dbquery($sqlstart." AND shiplist_entity_id=".$arr['id']." AND shiplist_user_id=".$arr['user_id']." AND shiplist_ship_id=".$arr['ship_id']." AND shiplist_id!=".$arr['shiplist_id'].$sqlend);
						if (mysql_num_rows($lres))
						{
							dbquery("DELETE FROM shiplist WHERE shiplist_id=".$arr['shiplist_id'].";");
							shiplistAdd($arr['id'],$arr['user_id'],$arr['ship_id'],$arr['shiplist_count']);
							cms_ok_msg("Doppelschiffe zusammengef&uuml;hrt!!");
						}
						else
							cms_err_msg("Reparatur nicht m&ouml;glich, kein passendes Reparaturverfahren gefunden!");
					}
				}
				else
					cms_err_msg("Reparatur nicht m&ouml;glich, Datensatz nicht vorhanden!");
			}

			// Suchquery generieren
			elseif ($_SESSION['shipedit']['query']=="")
			{
				if ($_POST['planet_id']!="")
				{
					if ($sql!="") $sql.=" AND ";
					$sql.= "id=".$_POST['planet_id'];
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
					$sql.="user_id=".$_POST['user_id'];
				}
				if ($_POST['user_nick']!="")
				{
					if ($sql!="") $sql.=" AND ";
					if (stristr($_POST['qmode']['user_nick'],"%")) $addchars = "%";else $addchars = "";
					$sql.= "user_nick ".stripslashes($_POST['qmode']['user_nick']).$_POST['user_nick']."$addchars'";
				}
				if ($_POST['ship_id']!="")
				{
					if ($sql!="") $sql.=" AND ";
					$sql.= "ship_id=".$_POST['ship_id'];
				}

				if ($sql!="")
				{
					$sql = $sqlstart." WHERE ".$sql.$sqlend;
				}
				else
				{
					$sql = $sqlstart.$sql.$sqlend;
				}
				$_SESSION['shipedit']['query']=$sql;
			}
			else
				$sql = $_SESSION['shipedit']['query'];

			$res = dbquery($sql);
			if (mysql_num_rows($res)>0)
			{
				echo mysql_num_rows($res)." Datens&auml;tze vorhanden<br/><br/>";
				if (mysql_num_rows($res)>20)
				{
					echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /> ";
					echo "<input type=\"button\" value=\"Aktualisieren\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=searchresults'\" /><br/><br/>";
				}

				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<td class=\"tbltitle\">ID</td>";
				echo "<td class=\"tbltitle\">Planet</td>";
				echo "<td class=\"tbltitle\">Spieler</td>";
				echo "<td class=\"tbltitle\">Schiff</td>";
				echo "<td class=\"tbltitle\">Anzahl</td>";
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
					// Doppeleinträge prüfen
					if ($check[$arr['id']][$arr['user_id']][$arr['ship_id']])
					{
						$error=true;
						$errorMsg="Doppelter Eintrag! Wird zusammengef&uuml;hrt!";
					}
					else
						$check[$arr['id']][$arr['user_id']][$arr['ship_id']]=true;
					// Planet gehört nicht dem Besitzer
					if ($arr['user_id']!=$arr['planet_user_id'])
					{
						$error=true;
						$errorMsg="Planet geh&ouml;rt nicht dem Schiffbesitzer! Wird auf den Heimatplaneten verschoben";
					}

					if ($error)
						$style=" style=\"color:#f30\"";
					else
						$style="";
					echo "<tr>";
					echo "<td class=\"tbldata\" $style>".$arr['shiplist_id']."</a></td>";
					echo "<td class=\"tbldata\"$style ".mTT($arr['planet_name'],"<b>Planet-ID:</b> ".$arr['id']."<br/><b>Koordinaten:</b> ".$arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy']." : ".$arr['planet_solsys_pos']).">".cut_string($arr['planet_name'],11)."</td>";
					echo "<td class=\"tbldata\"$style ".mTT($arr['user_nick'],"<b>User-ID:</b> ".$arr['user_id']."<br/><b>Punkte:</b> ".nf($arr['user_points'])).">".cut_string($arr['user_nick'],11)."</td>";
					echo "<td class=\"tbldata\"$style ".mTT($arr['ship_name'],"<b>Schiff-ID:</b> ".$arr['ship_id']).">".$arr['ship_name']."</td>";
					echo "<td class=\"tbldata\"$style>".nf($arr['shiplist_count'])."</td>";
					echo "<td class=\"tbldata\"$style>".edit_button("?page=$page&sub=$sub&action=edit&shiplist_id=".$arr['shiplist_id']);
					if ($error)
						echo " ".repair_button("?page=$page&sub=$sub&action=searchresults&amp;repair=".$arr['shiplist_id'],"Fehler!",$errorMsg);
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
		// Bearbeiten
		//
		elseif ($_GET['action']=="edit")
		{
			// Daten speichern
			if ($_POST['save']!="")
			{
				if($_POST['shiplist_count']==0)
				{
					$sql = "
					,shiplist_special_ship_level=0,
					shiplist_special_ship_exp=0,
					shiplist_special_ship_bonus_weapon=0,
					shiplist_special_ship_bonus_structure=0,
					shiplist_special_ship_bonus_shield=0,
					shiplist_special_ship_bonus_heal=0,
					shiplist_special_ship_bonus_capacity=0,
					shiplist_special_ship_bonus_speed=0,
					shiplist_special_ship_bonus_pilots=0,
					shiplist_special_ship_bonus_tarn=0,
					shiplist_special_ship_bonus_antrax=0,
					shiplist_special_ship_bonus_forsteal=0,
					shiplist_special_ship_bonus_build_destroy=0,
					shiplist_special_ship_bonus_antrax_food=0,
					shiplist_special_ship_bonus_deactivade=0
					";
				}
				else
				{
					$sql = "
					,shiplist_special_ship_level='".$_POST['shiplist_special_ship_level']."',
					shiplist_special_ship_exp='".$_POST['shiplist_special_ship_exp']."',
					shiplist_special_ship_bonus_weapon='".$_POST['shiplist_special_ship_bonus_weapon']."',
					shiplist_special_ship_bonus_structure='".$_POST['shiplist_special_ship_bonus_structure']."',
					shiplist_special_ship_bonus_shield='".$_POST['shiplist_special_ship_bonus_shield']."',
					shiplist_special_ship_bonus_heal='".$_POST['shiplist_special_ship_bonus_heal']."',
					shiplist_special_ship_bonus_capacity='".$_POST['shiplist_special_ship_bonus_capacity']."',
					shiplist_special_ship_bonus_speed='".$_POST['shiplist_special_ship_bonus_speed']."',
					shiplist_special_ship_bonus_pilots='".$_POST['shiplist_special_ship_bonus_pilots']."',
					shiplist_special_ship_bonus_tarn='".$_POST['shiplist_special_ship_bonus_tarn']."',
					shiplist_special_ship_bonus_antrax='".$_POST['shiplist_special_ship_bonus_antrax']."',
					shiplist_special_ship_bonus_forsteal='".$_POST['shiplist_special_ship_bonus_forsteal']."',
					shiplist_special_ship_bonus_build_destroy='".$_POST['shiplist_special_ship_bonus_build_destroy']."',
					shiplist_special_ship_bonus_antrax_food='".$_POST['shiplist_special_ship_bonus_antrax_food']."',
					shiplist_special_ship_bonus_deactivade='".$_POST['shiplist_special_ship_bonus_deactivade']."'
					";
				}

				dbquery("
				UPDATE
					shiplist
				SET
       		shiplist_count='".$_POST['shiplist_count']."'
       		".$sql."
				WHERE
					shiplist_id='".$_GET['shiplist_id']."';");
			}
			// Datensatz löschen
			elseif ($_POST['del']!="")
			{
				dbquery("
				DELETE FROM
					shiplist
				WHERE
					shiplist_id='".$_GET['shiplist_id']."'
				;");
			}

			$res = dbquery("
			SELECT
				shiplist_id,
				planet_name,
				user_nick,
				ship_name,
				special_ship,
				shiplist_count,
				shiplist_special_ship_level,
				shiplist_special_ship_exp,
				shiplist_special_ship_bonus_weapon,
				shiplist_special_ship_bonus_structure,
				shiplist_special_ship_bonus_shield,
				shiplist_special_ship_bonus_heal,
				shiplist_special_ship_bonus_capacity,
				shiplist_special_ship_bonus_speed,
				shiplist_special_ship_bonus_pilots,
				shiplist_special_ship_bonus_tarn,
				shiplist_special_ship_bonus_antrax,
				shiplist_special_ship_bonus_forsteal,
				shiplist_special_ship_bonus_build_destroy,
				shiplist_special_ship_bonus_antrax_food,
				shiplist_special_ship_bonus_deactivade,
				special_ship_need_exp,
				special_ship_exp_factor
			FROM
      	shiplist
      INNER JOIN
      	planets
      	ON shiplist_entity_id=planets.id
      INNER JOIN
      	users
      	ON shiplist_user_id=user_id
      INNER JOIN
      	ships
      	ON shiplist_ship_id=ship_id
			WHERE
				shiplist_id=".$_GET['shiplist_id'].";");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				echo "<form action=\"?page=$page&sub=$sub&action=edit&shiplist_id=".$_GET['shiplist_id']."\" method=\"post\">";
				echo "<table class=\"tbl\">";
				echo "<tr><td class=\"tbltitle\">ID</td><td class=\"tbldata\">".$arr['shiplist_id']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Planet</td><td class=\"tbldata\">".$arr['planet_name']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Spieler</td><td class=\"tbldata\">".$arr['user_nick']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Schiff</td><td class=\"tbldata\">".$arr['ship_name']."</td></tr>";
				echo "<tr><td class=\"tbltitle\">Anzahl</td><td class=\"tbldata\"><input type=\"text\" name=\"shiplist_count\" value=\"".$arr['shiplist_count']."\" size=\"5\" maxlength=\"20\" /></td></tr>";

				if($arr['special_ship']==1)
				{
					//echo "<tr><td class=\"tbltitle\">Level</td><td class=\"tbldata\"><input type=\"text\" name=\"shiplist_special_ship_level\" value=\"".$arr['shiplist_special_ship_level']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Erfahrung/Level</td>
						<td class=\"tbldata\"><input type=\"text\" name=\"shiplist_special_ship_exp\" value=\"".$arr['shiplist_special_ship_exp']."\" size=\"5\" maxlength=\"20\" />";
						echo " &nbsp; <b>Level:</b> ".Ship::levelByXp($arr['special_ship_need_exp'], $arr['special_ship_exp_factor'],$arr['shiplist_special_ship_exp'])."</td>
					</tr>";
					echo "<tr><td class=\"tbltitle\">Waffenlevel</td><td class=\"tbldata\"><input type=\"text\" name=\"shiplist_special_ship_bonus_weapon\" value=\"".$arr['shiplist_special_ship_bonus_weapon']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
					echo "<tr><td class=\"tbltitle\">Strukturlevel</td><td class=\"tbldata\"><input type=\"text\" name=\"shiplist_special_ship_bonus_structure\" value=\"".$arr['shiplist_special_ship_bonus_structure']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
					echo "<tr><td class=\"tbltitle\">Schildlevel</td><td class=\"tbldata\"><input type=\"text\" name=\"shiplist_special_ship_bonus_shield\" value=\"".$arr['shiplist_special_ship_bonus_shield']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
					echo "<tr><td class=\"tbltitle\">Heallevel</td><td class=\"tbldata\"><input type=\"text\" name=\"shiplist_special_ship_bonus_heal\" value=\"".$arr['shiplist_special_ship_bonus_heal']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
					echo "<tr><td class=\"tbltitle\">Kapazit&auml;tlevel</td><td class=\"tbldata\"><input type=\"text\" name=\"shiplist_special_ship_bonus_capacity\" value=\"".$arr['shiplist_special_ship_bonus_capacity']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
					echo "<tr><td class=\"tbltitle\">Speedlevel</td><td class=\"tbldata\"><input type=\"text\" name=\"shiplist_special_ship_bonus_speed\" value=\"".$arr['shiplist_special_ship_bonus_speed']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
					echo "<tr><td class=\"tbltitle\">Besatzungslevel</td><td class=\"tbldata\"><input type=\"text\" name=\"shiplist_special_ship_bonus_pilots\" value=\"".$arr['shiplist_special_ship_bonus_pilots']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
					echo "<tr><td class=\"tbltitle\">Tarnungslevel</td><td class=\"tbldata\"><input type=\"text\" name=\"shiplist_special_ship_bonus_tarn\" value=\"".$arr['shiplist_special_ship_bonus_tarn']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
					echo "<tr><td class=\"tbltitle\">Giftgaslevel</td><td class=\"tbldata\"><input type=\"text\" name=\"shiplist_special_ship_bonus_antrax\" value=\"".$arr['shiplist_special_ship_bonus_antrax']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
					echo "<tr><td class=\"tbltitle\">Techklaulevel</td><td class=\"tbldata\"><input type=\"text\" name=\"shiplist_special_ship_bonus_forsteal\" value=\"".$arr['shiplist_special_ship_bonus_forsteal']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
					echo "<tr><td class=\"tbltitle\">Bombardierlevel</td><td class=\"tbldata\"><input type=\"text\" name=\"shiplist_special_ship_bonus_build_destroy\" value=\"".$arr['shiplist_special_ship_bonus_build_destroy']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
					echo "<tr><td class=\"tbltitle\">Antraxlevel</td><td class=\"tbldata\"><input type=\"text\" name=\"shiplist_special_ship_bonus_antrax_food\" value=\"".$arr['shiplist_special_ship_bonus_antrax_food']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
					echo "<tr><td class=\"tbltitle\">Deaktivierlevel</td><td class=\"tbldata\"><input type=\"text\" name=\"shiplist_special_ship_bonus_deactivade\" value=\"".$arr['shiplist_special_ship_bonus_deactivade']."\" size=\"5\" maxlength=\"20\" /></td></tr>";
				}

				echo "</table><br/>";
				echo "<input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
				echo "<input type=\"submit\" name=\"del\" value=\"L&ouml;schen\" class=\"button\" onclick=\"return confirm('Schiffe wirklich l&ouml;schen?')\" />&nbsp;";
				echo "<hr/>";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" />&nbsp;";
				echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&sub=$sub';\" />";
				echo "</form>";
			}
			else
			{
				echo "Dieser Datensatz wurde gel&ouml;scht!<br/><br/>";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" />&nbsp;";
			}
		}

		//
		// Suchformular Schiffsliste
		//
		else
		{
			$_SESSION['shipedit']['query']="";



			// Suchmaske
			echo "Suchmaske (Es sind <b>".nf($tblcnt[0])."</b> Eintr&auml;ge in der Datenbank vorhanden.):<br/><br/>";
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\">Planet ID</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td>";
			echo "<tr><td class=\"tbltitle\">Planetname</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('planet_name');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Spieler ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Spieler Nick</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\" />&nbsp;";
			fieldqueryselbox('user_nick');
			echo "<br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td></tr>";
			echo "<tr><td class=\"tbltitle\">Schiff</td><td class=\"tbldata\"><select name=\"ship_id\"><option value=\"\"><i>---</i></option>";
			foreach ($slist as $k=>$v)
				echo "<option value=\"".$k."\">".$v."</option>";
			echo "</select></td>";
			echo "</table>";
			echo "<br/><input type=\"submit\" class=\"button\" name=\"shiplist_search\" value=\"Suche starten\" /></form><br/>";
			*/

			// Schiffe laden
			$bres = dbquery("
			SELECT 
				ship_id,
				ship_name 
			FROM 
				ships 
			ORDER BY 
				ship_name;");
			$slist=array();
			while ($barr=mysql_fetch_array($bres))
				$slist[$barr['ship_id']]=$barr['ship_name'];

			$tblcnt = mysql_fetch_row(dbquery("SELECT count(shiplist_id) FROM shiplist;"));
			
			// Hinzufügen
			echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\" id=\"selector\" name=\"selector\">";
			tableStart();
			
			//Sonnensystem
			echo "<tr><th class=\"tbltitle\">Sonnensystem</th><td class=\"tbldata\">
			<select name=\"cell_sx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showShipsOnPlanet',1);\">";
			echo "<option value=\"0\">Sektor X</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cell_sy\"  onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showShipsOnPlanet',1);\">";
			echo "<option value=\"0\">Sektor Y</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select> : <select name=\"cell_cx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showShipsOnPlanet',1);\">";
			echo "<option value=\"0\">Zelle X</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cell_cy\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showShipsOnPlanet',1);\">";
			echo "<option value=\"0\">Zelle Y</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select></td></tr>";
		
			
			//User
			echo "<tr><th class=\"tbltitle\"><i>oder</i> User</th><td class=\"tbldata\">";
			echo "<input type=\"text\" name=\"userlist_nick\" id=\"userlist_nick\" value=\"\" autocomplete=\"off\" size=\"30\" maxlength=\"30\" onkeyup=\"xajax_searchUserList(this.value,'showShipsOnPlanet');\"><br>
			<div id=\"userlist\">&nbsp;</div>";
			echo "</td></tr>";
			
			//Planeten
			echo "<tr><th class=\"tbltitle\">Planeten</th><td class=\"tbldata\" id=\"planetSelector\">Sonnensystem oder User w&auml;hlen...</td></tr>";
			
			//Schiffe Hinzufügen
			echo "<tr><th class=\"tbltitle\">Hinzuf&uuml;gen:</th><td class=\"tbldata\">
			<input type=\"text\" name=\"shiplist_count\" value=\"1\" size=\"7\" maxlength=\"10\" />
			<select name=\"ship_id\">";
			foreach ($slist as $k=>$v)
			{
				echo "<option value=\"".$k."\">".$v."</option>";
			}
			echo "</select> &nbsp; 
			<input type=\"button\" onclick=\"showLoaderPrepend('shipsOnPlanet');xajax_addShipToPlanet(xajax.getFormValues('selector'));\" value=\"Hinzuf&uuml;gen\" /></td></tr>";
			
			//Vorhandene Schiffe
			echo "<tr><td class=\"tbldata\" id=\"shipsOnPlanet\" colspan=\"2\">Planet w&auml;hlen...</td></tr>";
			tableEnd();
			echo "</form>";

			//Focus
			echo "<script type=\"text/javascript\">document.getElementById('userlist_nick').focus();</script>";

			echo "<br/>Es sind <b>".nf($tblcnt[0])."</b> Eintr&auml;ge in der Datenbank vorhanden.";

	//	}
	}





?>
