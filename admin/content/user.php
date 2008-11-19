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
	// Erstellen
	//
	elseif ($sub=="create")
	{
		echo "<h1>Spieler erstellen</h1>";
		
		if (isset($_POST['create']))
		{
			$errorCode = "";
			if (User::register(array(
				"name" => $_POST['user_name'],
				"nick" => $_POST['user_nick'],
				"password" => $_POST['user_password'],
				"email" => $_POST['user_email'],
				"race" => $_POST['user_race'],
				"ghost" => $_POST['user_ghost']
				),$errorCode))
			{
				ok_msg("Benutzer wurde erstellt! [[url ?page=user&sub=edit&id=".$errorCode."]Details[/url]]");
			}
			else
			{
				error_msg("Benutzer konnte nicht erstellt werden!\n\n".$errorCode."");
			}
		}
		
		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		tableStart("","400");
		echo "<tr><th>Name:</th><td>
		<input type=\"text\" name=\"user_name\" value=\"\" />
		</td></td>";
		echo "<tr><th>Nick:</th><td>
		<input type=\"text\" name=\"user_nick\" value=\"\" />
		</td></td>";
		echo "<tr><th>Passwort:</th><td>
		<input type=\"text\" name=\"user_password\" value=\"\" />
		</td></td>";
		echo "<tr><th>E-Mail:</th><td>
		<input type=\"text\" name=\"user_email\" value=\"\" />
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
		echo "<div style=\"text-align:center;\"><input type=\"submit\" name=\"create\" value=\"Erstellen\" /></div>
		</form>";
	}


	//
	// Fehlerhafte Logins
	//
	elseif ($sub=="specialists")
	{
			advanced_form("specialists");
	}

	//
	// Statistics-Graph
	//
	elseif ($sub=="userstats")
	{
		echo "<h1>Userstatistiken</h1>";
		echo "<h2>Online / Registrierte User</h2>";
		if (file_exists(CACHE_ROOT."/out/userstats.png"))
		{
			echo "<img src=\"../cache/out/userstats.png\" alt=\"Userstats\" />";
		}
		else
		{
			error_msg("Run scripts/userstats.php periodically to update the image!",1);			
		}	
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
		advanced_form("races");
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
		echo "<h1>Spieler</h1>";

		if ((isset($_GET['special']) || isset($_POST['user_search']) || isset($_SESSION['admin']['user_query'])) && isset($_GET['action']) && $_GET['action']=="search")
		{
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
				if (isset($_POST['user_admin']) && $_POST['user_admin']<2)
				{
					if ($_POST['user_admin']==1)
						$sql.= " AND user_admin=1 ";
					else
						$sql.= " AND user_admin=0 ";
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
				
 				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<td class=\"tbltitle\" valign=\"top\">ID</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Nick</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Name</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">E-Mail</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Punkte</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Allianz</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Rasse</td>";
				echo "</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					if ($arr['user_blocked_from']<$time && $arr['user_blocked_to']>$time)
						$uCol=USER_COLOR_BANNED;
					elseif($arr['user_hmode_from']<$time && $arr['user_hmode_to']>$time)
						$uCol=USER_COLOR_HOLIDAY;
					elseif ($arr['user_deleted']!=0)
						$uCol=USER_COLOR_DELETED;
					else
						$uCol=USER_COLOR_DEFAULT;
					echo "<tr>";
					echo "<td class=\"tbldata\" style=\"color:".$uCol.";\" title=\"".$arr['user_name']."\">".$arr['user_id']."</td>";
					echo "<td class=\"tbldata\" style=\"color:".$uCol.";\" title=\"".$arr['user_nick']."\">".$arr['user_nick']."</td>";
					echo "<td class=\"tbldata\" style=\"color:".$uCol.";\" title=\"".$arr['user_name']."\">".cut_string($arr['user_name'],15)."</td>";
					echo "<td class=\"tbldata\" style=\"color:".$uCol.";\" title=\"".$arr['user_email']."\">".cut_string($arr['user_email'],15)."</td>";
					echo "<td class=\"tbldata\" style=\"color:".$uCol.";\">".nf($arr['user_points'])."</td>";
					echo "<td class=\"tbldata\" style=\"color:".$uCol.";\">".($arr['user_alliance_id']>0 ? $allys[$arr['user_alliance_id']]['tag']:'-')."</td>";
					echo "<td class=\"tbldata\" style=\"color:".$uCol.";\">".($arr['user_race_id']>0 ? $race[$arr['user_race_id']]['race_name'] : '-')."</td>";
					echo "<td class=\"tbldata\">
					".edit_button("?page=$page&amp;sub=edit&amp;id=".$arr['user_id'])."
					".cb_button("add_user=".$arr['user_id']."")."
					</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br/><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=search'\" value=\"Aktualisieren\" /><br/><br/>
				<b>Legende:</b> 
				<span style=\"color:".USER_COLOR_DEFAULT."\">Normal</span>, 
				<span style=\"color:".USER_COLOR_BANNED."\">Gesperrt</span>, 
				<span style=\"color:".USER_COLOR_HOLIDAY."\">Urlaub</span>, 
				<span style=\"color:".USER_COLOR_DELETED."\">Löschauftrag</span>
				";
			}
			else
			{
				echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
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
			$_SESSION['admin']['user_query']="";
			echo "Suchmaske (wenn nichts eingegeben wird werden alle Datens&auml;tze angezeigt):<br/><br/>";
			echo "<form action=\"?page=$page&amp;action=search\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\">ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Nickname</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\"/> <br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td></tr>";
			echo "<tr><td class=\"tbltitle\">Name</td><td class=\"tbldata\"><input type=\"text\" name=\"user_name\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
			echo "<tr><td class=\"tbltitle\">E-Mail</td><td class=\"tbldata\"><input type=\"text\" name=\"user_email\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
			echo "<tr><td class=\"tbltitle\">Fixe E-Mail</td><td class=\"tbldata\"><input type=\"text\" name=\"user_email_fix\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
			echo "<tr><td class=\"tbltitle\">Passwort</td><td class=\"tbldata\"><input type=\"text\" name=\"user_password\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">IP-Adresse</td><td class=\"tbldata\"><input type=\"text\" name=\"user_ip\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
			echo "<tr><td class=\"tbltitle\">Allianz</td><td class=\"tbldata\"><input type=\"text\" name=\"user_alliance\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchAlliance(this.value,'user_alliance','citybox2');\"/> <br><div class=\"citybox\" id=\"citybox2\">&nbsp;</div></td></tr>";
			$race = get_races_array();
			echo "<tr><td class=\"tbltitle\">Rasse</td><td class=\"tbldata\"><select name=\"user_race_id\">";
			echo "<option value=\"\">(egal)</option>";
			foreach ($race as $id=>$racedata)
			{
				echo "<option value=\"$id\">".$racedata['race_name']."</option>";
			}
			echo "</select></td></tr>";
			echo "<tr><td class=\"tbltitle\">Profil-Text</td><td class=\"tbldata\"><input type=\"text\" name=\"user_profile_text\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
			echo "<tr><td class=\"tbltitle\">Urlaubsmodus</td><td class=\"tbldata\"><input type=\"radio\" name=\"user_hmode\" value=\"2\" checked=\"checked\" /> Egal &nbsp; <input type=\"radio\" name=\"user_hmode\" value=\"0\" /> Nein &nbsp; <input type=\"radio\" name=\"user_hmode\" value=\"1\" /> Ja</td></tr>";
			echo "<tr><td class=\"tbltitle\">Gesperrt</td><td class=\"tbldata\"><input type=\"radio\" name=\"user_blocked\" value=\"2\" checked=\"checked\" /> Egal &nbsp; <input type=\"radio\" name=\"user_blocked\" value=\"0\" /> Nein &nbsp; <input type=\"radio\" name=\"user_blocked\" value=\"1\"  /> Ja</td></tr>";
			echo "<tr><td class=\"tbltitle\">Admin</td><td class=\"tbldata\"><input type=\"radio\" name=\"user_admin\" value=\"2\" checked=\"checked\" /> Egal &nbsp; <input type=\"radio\" name=\"user_admin\" value=\"0\" /> Nein &nbsp; <input type=\"radio\" name=\"user_admin\" value=\"1\"  /> Ja</td></tr>";
			echo "<tr><td class=\"tbltitle\">Bemerkungen</td><td class=\"tbldata\"><input type=\"radio\" name=\"user_comment\" value=\"2\" checked=\"checked\" /> Egal &nbsp; <input type=\"radio\" name=\"user_comment\" value=\"0\" /> Keine &nbsp; <input type=\"radio\" name=\"user_comment\" value=\"1\"  /> Vorhanden</td></tr>";
			echo "</table>";
			echo "<br/><input type=\"submit\" name=\"user_search\" value=\"Suche starten\" /></form>";

			$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM users;"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.";

		}
	}
?>
