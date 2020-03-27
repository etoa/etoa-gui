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
	// 	Dateiname: user.php
	// 	Topic: Benutzerverwaltung
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//


	//
	// Fehlerhafte Logins
	//
	if ($sub=="xml")
	{
		require("user/xml.inc.php");
	}

	//
	// Ip-Search
	//
	elseif ($sub=="ipsearch")
	{
		require("user/ipsearch.inc.php");
	}

	//
	// Sessions
	//
	elseif ($sub=="sessions")
	{
		require("user/sessions.inc.php");
	}

	//
	// Erstellen
	//
	elseif ($sub=="create")
	{
		echo "<h1>Spieler erstellen</h1>";

		if (isset($_POST['create']))
		{
			try
			{
				$newUser = User::register(
					$_POST['user_name'],
					$_POST['user_email'],
					$_POST['user_nick'],
					$_POST['user_password'],
					$_POST['user_race'],
					$_POST['user_ghost']==1
				);
				$newUser->setVerified(true);
				add_log(3,"Der Benutzer ".$newUser->nick." (".$newUser->realName.", ".$newUser->email.") wurde registriert!");
				success_msg("Benutzer wurde erstellt! [[page user sub=edit id=".$newUser->id."]Details[/page]]");
			}
			catch (Exception $e)
			{
				error_msg("Benutzer konnte nicht erstellt werden!\n\n".$e->getMessage());
			}
		}

		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		tableStart("","400");
		echo "<tr><th>Name:</th><td>
		<input type=\"text\" name=\"user_name\" value=\"\" />
		</td></td>";
		echo "<tr><th>E-Mail:</th><td>
		<input type=\"text\" name=\"user_email\" value=\"\" />
		</td></td>";
		echo "<tr><th>Nick:</th><td>
		<input type=\"text\" name=\"user_nick\" value=\"\" />
		</td></td>";
		echo "<tr><th>Passwort:</th><td>
		<input type=\"password\" name=\"user_password\" value=\"\" />
		</td></td>";
		echo "<tr><th>Rasse:</th><td>
		<select name=\"user_race\" />
		<option value=\"0\">Keine</option>";
		$res = dbquery("SELECT * FROM races ORDER BY race_name");
		while ($arr = mysql_fetch_assoc($res))
		{
			echo "<option value=\"".$arr['race_id']."\">".$arr['race_name']."</option>";
		}
		echo "</select>
		</td></td>";
		echo "<tr><th>Geist:</th><td>
		<input type=\"radio\" name=\"user_ghost\" value=\"1\" /> Ja &nbsp;
		<input type=\"radio\" name=\"user_ghost\" value=\"0\" checked=\"checked\" /> Nein
		</td></td>";

		tableEnd();
		echo "<p><input type=\"submit\" name=\"create\" value=\"Erstellen\" /></p>
		</form>";
	}


	//
	// Fehlerhafte Logins
	//
	elseif ($sub=="specialists")
	{
			advanced_form("specialists", $twig);
	}

	//
	// Fehlerhafte Logins
	//
	elseif ($sub=="loginfailures")
	{
		require("user/loginfailures.inc.php");
	}

	//
	// Beobachter
	//
	elseif ($sub=="observed")
	{
		require("user/observed.inc.php");
	}

	//
	// Tickets
	//
	elseif ($sub=="tickets")
	{
		require("user/tickets.inc.php");
	}


	//
	// Verwarnungen
	//
	elseif ($sub=="warnings")
	{
		require("user/warnings.inc.php");
	}

	//
	// Bilder prüfen
	//
	elseif ($sub=="imagecheck")
	{
		require("user/imagecheck.inc.php");
	}

	//
	// User banner
	//
	elseif ($sub=="userbanner")
	{
		require("user/userbanner.inc.php");
	}

	//
	// Session-Log
	//
	elseif ($sub=="userlog")
	{
		require("user/userlog.inc.php");
	}

	//
	// Rassen
	//
	elseif ($sub=="race")
	{
		advanced_form("races", $twig);
	}

	//
	// History
	//
	elseif ($sub=="history")
	{
		require("user/history.inc.php");
	}

	//
	// Änderungsanträge
	//
	elseif ($sub=="requests")
	{
		require("user/requests.inc.php");
	}

	//
	// Punkteverlauf
	//
	elseif ($sub=="point")
	{
		require("user/point.inc.php");
	}

	//
	// Multisuche
	//
	elseif ($sub=="multi")
	{
		require("user/multi.inc.php");
	}

	//
	// Sittings
	//
	elseif ($sub=="sitting")
	{
		require("user/sitting.inc.php");
	}


	//
	// User-Suchergebnisse anzeigen
	//

	else
	{
		$twig->addGlobal("title", 'Spieler');

		if ((isset($_GET['special']) || isset($_POST['user_search']) || isset($_SESSION['admin']['user_query'])) && isset($_GET['action']) && $_GET['action']=="search")
		{
			$twig->addGlobal("subtitle", 'Suchergebnisse');

			$tables = 'users';

			if (isset($_GET['special']))
			{
				switch ($_GET['special'])
				{
					case "ip":
						$sql= " user_ip='".base64_decode($_GET['val'])."'";
						break;
					case "host":
						$sql= " user_hostname='".base64_decode($_GET['val'])."'";
						break;
					case "blocked":
						$sql= " (user_blocked_from<".time()." AND user_blocked_to>".time().")";
						break;
					default:
						$sql= " user_nick='%".base64_decode($_GET['val'])."%'";
				}
				$sqlstart="SELECT * FROM $tables WHERE ";
				$sqlend=" ORDER BY user_nick;";
				$sql = $sqlstart.$sql.$sqlend;
				$_SESSION['admin']['user_query']=$sql;
			}
			elseif ($_SESSION['admin']['user_query']=="")
			{
				$sql='';
				if ($_POST['user_id']!="")
				{
					$sql.= " AND user_id='".$_POST['user_id']."'";
				}
				if (isset($_POST['user_nick_search'])!="")
				{
					$sql.= " AND user_nick LIKE '%".$_POST['user_nick_search']."%'";
				}
				if ($_POST['user_nick']!="")
				{
					$sql.= " AND user_nick  LIKE '%".$_POST['user_nick']."%'";
				}
				if ($_POST['user_name']!="")
				{
					$sql.= " AND user_name  LIKE '%".$_POST['user_name']."%'";
				}
				if ($_POST['user_email']!="")
				{
					$sql.= " AND user_email  LIKE '%".$_POST['user_email']."%'";
				}
				if ($_POST['user_email_fix']!="")
				{
					$sql.= " AND user_email_fix LIKE '%".$_POST['user_email_fix']."%'";
				}
				if ($_POST['user_password']!="")
				{
					$sql.= " AND user_password LIKE '".md5($_POST['user_password'])."'";
				}
				if ($_POST['user_ip']!="")
				{
					$sql.= " AND user_ip LIKE '%".$_POST['user_ip']."%'";
				}
				if ($_POST['user_alliance']!="")
				{
					$sql.= " AND user_alliance_id=alliance_id AND alliance_name LIKE '%".$_POST['user_alliance']."%'";
					$tables.=",".'alliances';
				}
				if ($_POST['user_race_id']!="")
				{
					$sql.= " AND user_race_id='".$_POST['user_race_id']."'";
				}
				if ($_POST['user_profile_text']!="")
				{
					$sql.= " AND user_profile_text LIKE '%".$_POST['user_profile_text']."%'";
				}
				if (isset($_POST['user_hmode']) && $_POST['user_hmode']<2)
				{
					if ($_POST['user_hmode']==1)
						$sql.= " AND (user_hmode_from<".time()." AND user_hmode_to>".time().")";
					else
						$sql.= " AND (user_hmode_to<".time().")";
				}
				if (isset($_POST['user_blocked']) && $_POST['user_blocked']<2)
				{
					if ($_POST['user_blocked']==1)
						$sql.= " AND (user_blocked_from<".time()." AND user_blocked_to>".time().")";
					else
						$sql.= " AND (user_blocked_to<".time().")";
				}
				if (isset($_POST['user_chatadmin']) && $_POST['user_chatadmin']<2)
				{
					if ($_POST['user_chatadmin']==1)
						$sql.= " AND user_chatadmin=1 ";
					else
						$sql.= " AND user_chatadmin=0 ";
				}
				if (isset($_POST['user_ghost']) && $_POST['user_ghost']<2)
				{
					if ($_POST['user_ghost']==1)
						$sql.= " AND user_ghost=1 ";
					else
						$sql.= " AND user_ghost=0 ";
				}

				$sqlstart="SELECT * FROM $tables WHERE 1 ";
				$sqlend=" ORDER BY user_nick;";
				$sql = $sqlstart.$sql.$sqlend;
				$_SESSION['admin']['user_query']=$sql;
			}
			else
  			$sql=$_SESSION['admin']['user_query'];

			$res = dbquery($sql);
			$nr = mysql_num_rows($res);
			if ($nr==1)
			{
				$arr = mysql_fetch_array($res);
				echo "<script>document.location='?page=$page&sub=edit&id=".$arr['user_id']."';</script>
				Klicke <a href=\"?page=$page&sub=edit&id=".$arr['user_id']."\">hier</a> falls du nicht automatisch weitergeleitet wirst...";
			}
			elseif ($nr>0)
			{
				echo $nr." Datens&auml;tze vorhanden<br/><br/>";
				if ($nr>20)
				{
					echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /><br/><br/>";
				}

				$race = get_races_array();
				$allys=get_alliance_names();
				$time = time();

 				tableStart();
				echo "<tr>";
				echo "<th>ID</th>";
				echo "<th>Nick</th>";
				echo "<th>Status</th>";
				echo "<th>Name</th>";
				echo "<th>E-Mail</th>";
        echo "<th>Dual Name</th>";
        echo "<th>Dual E-Mail</th>";
				echo "<th>Punkte</th>";
				echo "<th>Allianz</th>";
				echo "<th>Rasse</th>
				<th></th>";
				echo "</tr>";
				while ($arr = mysql_fetch_assoc($res))
				{
					if ($arr['user_blocked_from']<$time && $arr['user_blocked_to']>$time) {
						$status = "Gesperrt";
						$uCol=' class="userLockedColor"';
					} elseif($arr['user_hmode_from']<$time && $arr['user_hmode_to']>$time) {
						$status = "Urlaub";
						$uCol=' class="userHolidayColor"';
					} elseif ($arr['user_deleted']!=0) {
						$status = "Löschauftrag";
						$uCol=' class="userDeletedColor"';
					} elseif ($arr['admin']!=0) {
						$status = "Admin";
						$uCol=' class="adminColor"';
					} elseif ($arr['user_ghost']!=0) {
						$status = "Geist";
						$uCol=' class="userGhostColor"';
					} else {
						$status = 'Spieler';
						$uCol="";
					}
					echo "<tr>";
					echo "<td>".$arr['user_id']."</td>";
					echo "<td><a href=\"?page=$page&amp;sub=edit&amp;id=".$arr['user_id']."\">".$arr['user_nick']."</a></td>";
					echo "<td ".$uCol.">".$status."</td>";
					echo "<td title=\"".$arr['user_name']."\">".cut_string($arr['user_name'],15)."</td>";
					echo "<td title=\"".$arr['user_email']."\">".cut_string($arr['user_email'],15)."</td>";
          echo "<td title=\"".$arr['dual_name']."\">".cut_string($arr['dual_name'],15)."</td>";
					echo "<td title=\"".$arr['dual_email']."\">".cut_string($arr['dual_email'],15)."</td>";
					echo "<td>".nf($arr['user_points'])."</td>";
					echo "<td>".($arr['user_alliance_id']>0 ? $allys[$arr['user_alliance_id']]['tag']:'-')."</td>";
					echo "<td>".($arr['user_race_id']>0 ? $race[$arr['user_race_id']]['race_name'] : '-')."</td>";
					echo "<td>
					".edit_button("?page=$page&amp;sub=edit&amp;id=".$arr['user_id'])."
					".cb_button("add_user=".$arr['user_id']."")."
					</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<p><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> &nbsp; ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=search'\" value=\"Aktualisieren\" /></p>";
			}
			else
			{
				$twig->addGlobal('infoMessage', "Die Suche lieferte keine Resultate!");
				echo "<p><input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" /></p>";
			}
		}

		//
		// User-Daten bearbeiten
		//

		elseif ($sub=="edit")
		{
			require("user/edit.inc.php");
		}

		//
		// Suchmaske
		//

		else
		{
			$twig->addGlobal("subtitle", 'Suchmaske');

			$_SESSION['admin']['user_query']="";
			echo "<form action=\"?page=$page&amp;action=search\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><th>ID</th><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><th>Nickname</th><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" id=\"user_nick\"  value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\"/> <br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td></tr>";
			echo "<tr><th>Name</th><td class=\"tbldata\"><input type=\"text\" name=\"user_name\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
			echo "<tr><th>E-Mail</th><td class=\"tbldata\"><input type=\"text\" name=\"user_email\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
			echo "<tr><th>Fixe E-Mail</th><td class=\"tbldata\"><input type=\"text\" name=\"user_email_fix\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
			echo "<tr><th>Passwort</th><td class=\"tbldata\"><input type=\"text\" name=\"user_password\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><th>IP-Adresse</th><td class=\"tbldata\"><input type=\"text\" name=\"user_ip\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
			echo "<tr><th>Allianz</th><td class=\"tbldata\"><input type=\"text\" name=\"user_alliance\" id=\"user_alliance\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchAlliance(this.value,'user_alliance','citybox2');\"/> <br><div class=\"citybox\" id=\"citybox2\">&nbsp;</div></td></tr>";
			$race = get_races_array();
			echo "<tr><th>Rasse</th><td class=\"tbldata\"><select name=\"user_race_id\">";
			echo "<option value=\"\">(egal)</option>";
			foreach ($race as $id=>$racedata)
			{
				echo "<option value=\"$id\">".$racedata['race_name']."</option>";
			}
			echo "</select></td></tr>";
			echo "<tr><th>Profil-Text</th><td class=\"tbldata\"><input type=\"text\" name=\"user_profile_text\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
			echo "<tr><th>Urlaubsmodus</th><td class=\"tbldata\">
				<input type=\"radio\" name=\"user_hmode\" value=\"2\" checked=\"checked\" /> Egal &nbsp; 
				<input type=\"radio\" name=\"user_hmode\" value=\"0\" /> Nein &nbsp; 
				<input type=\"radio\" name=\"user_hmode\" value=\"1\" /> Ja</td></tr>";
			echo "<tr><th>Gesperrt</th><td class=\"tbldata\">
				<input type=\"radio\" name=\"user_blocked\" value=\"2\" checked=\"checked\" /> Egal &nbsp; 
				<input type=\"radio\" name=\"user_blocked\" value=\"0\" /> Nein &nbsp; 
				<input type=\"radio\" name=\"user_blocked\" value=\"1\"  /> Ja</td></tr>";
			echo "<tr><th>Geist</th><td class=\"tbldata\"><input type=\"radio\" name=\"user_ghost\" value=\"2\" checked=\"checked\" /> Egal &nbsp; <input type=\"radio\" name=\"user_ghost\" value=\"0\" /> Nein &nbsp; <input type=\"radio\" name=\"user_ghost\" value=\"1\"  /> Ja</td></tr>";
			echo "<tr><th>Chat-Admin</th><td class=\"tbldata\"><input type=\"radio\" name=\"user_chatadmin\" value=\"2\" checked=\"checked\" /> Egal &nbsp; <input type=\"radio\" name=\"user_chatadmin\" value=\"0\" /> Nein &nbsp; <input type=\"radio\" name=\"user_chatadmin\" value=\"1\"  /> Ja</td></tr>";
			echo "</table>";
			echo "<br/><input type=\"submit\" name=\"user_search\" value=\"Suche starten\" /> (wenn nichts eingegeben wird werden alle Datens&auml;tze angezeigt)</form>";

			$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM users;"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.";

		}
	}
?>
