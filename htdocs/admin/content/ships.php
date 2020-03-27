<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

	//
	// Battlepoints
	//
	if ($sub=="battlepoints")
	{
		$twig->addGlobal("title", "Schiff-Punkte");

		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"POST\">";
		if (isset($_POST['recalc']))
		{
			echo MessageBox::ok("", Ranking::calcShipPoints());
		}
		echo "<p>Nach jeder direkter &Auml;nderung an den Schiffen via Datenbank m&uuml;ssen die Punkte neu berechnet werden!</p>
		<p><input type=\"submit\" name=\"recalc\" value=\"Neu berechnen\" /></p>
		</form>";

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
		$twig->addGlobal("title", "XP-Rechner");

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
			if (isset($_GET['id']) && $_GET['id']==$arr['ship_id'])
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
		simple_form("ship_cat", $twig);
	}

	//
	// Daten
	//
	elseif ($sub=="data")
	{
		advanced_form("ships", $twig);
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
		define("ITEM_ENABLE_FLD","1");
		define("ITEM_ORDER_FLD","ship_cat_id,ship_order");

		define("ITEM_IMAGE_PATH",IMAGE_PATH."/ships/ship<DB_TABLE_ID>_small.".IMAGE_EXT);

		include("inc/requirements.inc.php");

	}

	//
	// Bauliste
	//
	elseif ($sub=="queue")
	{
		$twig->addGlobal("title", "Schiff-Bauliste");

		if (isset($_POST['shipqueue_search']) || isset($_GET['action']) && $_GET['action']=="searchresults")
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
			$sql ='';
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
		elseif (isset($_GET['action']) && $_GET['action']=="edit" && $_GET['id']>0)
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
			$twig->addGlobal("subtitle", "Suchmaske");
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
			echo "<p><input type=\"submit\" class=\"button\" name=\"shipqueue_search\" value=\"Suche starten\" /></p></form>";
			$tblcnt = mysql_fetch_row(dbquery("SELECT COUNT(queue_id) FROM ship_queue;"));
			echo "<p>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.</p>";
		}
	}

	/**************
	* Schiffliste *
	**************/
	else
	{
		$twig->addGlobal("title", "Schiffliste");

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
			echo "<table>";

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
			echo "<tr><th class=\"tbltitle\">User:</th><td class=\"tbldata\">";
			echo "<input type=\"text\" name=\"userlist_nick\" id=\"userlist_nick\" value=\"\" autocomplete=\"off\" size=\"30\" maxlength=\"30\" onkeyup=\"xajax_searchUserList(this.value,'showShipsOnPlanet');\"><br>
			<div id=\"userlist\">&nbsp;</div>";
			echo "</td></tr>";

			//Planeten
			echo "<tr><th class=\"tbltitle\">Kolonien:</th><td class=\"tbldata\" id=\"planetSelector\">Sonnensystem oder User w&auml;hlen...</td></tr>";

			//Schiffe Hinzufügen
			echo "<tr name=\"addObject\" id=\"addObject\" style=\"display:none;\"><th class=\"tbltitle\">Hinzuf&uuml;gen:</th><td class=\"tbldata\">
			<input type=\"text\" name=\"shiplist_count\" value=\"1\" size=\"7\" maxlength=\"10\" />
			<select name=\"ship_id\">";
			foreach ($slist as $k=>$v)
			{
				echo "<option value=\"".$k."\">".$v."</option>";
			}
			echo "</select> &nbsp; 
			<input type=\"button\" onclick=\"showLoaderPrepend('shipsOnPlanet');xajax_addShipToPlanet(xajax.getFormValues('selector'));\" value=\"Hinzuf&uuml;gen\" /></td></tr>";

			//Vorhandene Schiffe
			tableEnd();
			echo "<br/>";

			echo "<div id=\"shipsOnPlanet\" style=\"width:700px\"></div>";

			echo "</form>";



			//Focus
			echo "<script type=\"text/javascript\">document.getElementById('userlist_nick').focus();</script>";

			//Add User
			if (searchQueryArray($sa,$so))
			{
				if (isset($sa['user_nick']))
				{
					echo "<script type=\"text/javascript\">document.getElementById('userlist_nick').value=\"".$sa['user_nick'][1]."\";xajax_searchUserList('".$sa['user_nick'][1]."','showShipsOnPlanet');</script>";
				}
			}

			echo "<br/>Es sind <b>".nf($tblcnt[0])."</b> Eintr&auml;ge in der Datenbank vorhanden.";

	}

?>
