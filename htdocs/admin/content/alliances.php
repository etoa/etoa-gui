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
	// 	Dateiname: alliances.php
	// 	Topic: Allianz-Verwaltung
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	//
	// Bilder prüfen test by lambo test by nicu
	//
	if ($sub=="imagecheck")
	{
		$dir = ALLIANCE_IMG_DIR."/";
		echo "<h1>Allianz-Bilder pr&uuml;fen</h1>";

		//
		// Check submit
		//
		if (isset($_POST['validate_submit']))
		{
			foreach ($_POST['validate'] as $id=>$v)
			{
				if ($v==0)
				{
					$res = dbquery("SELECT alliance_img FROM alliances WHERE alliance_id=".$id.";");
					if (mysql_num_rows($res)>0)
					{
						$arr=mysql_fetch_array($res);
			      if (file_exists(ALLIANCE_IMG_DIR."/".$arr['alliance_img']))
			      {
			 	    	unlink(ALLIANCE_IMG_DIR."/".$arr['alliance_img']);
			  	  }
						dbquery("UPDATE alliances SET alliance_img='',alliance_img_check=0 WHERE alliance_id=".$id.";");
						if (mysql_affected_rows()>0)
						{
							echo "Bild entfernt!<br/><br/>";
						}
					}
				}
				else
				{
					dbquery("UPDATE alliances SET alliance_img_check=0 WHERE alliance_id=".$id.";");
				}
			}
		}

		//
		// Check new images
		//
		echo "<h2>Noch nicht verifizierte Bilder</h2>";
		echo "Diese Bilder gehören zu aktiven Allianzen. Bitte prüfe regelmässig, ob sie nicht gegen unsere Regeln verstossen!<br/>";
		$res = dbquery("SELECT
			alliance_id,
			alliance_tag,
			alliance_name,
			alliance_img 
		FROM
			alliances
		WHERE
			alliance_img_check=1
			AND alliance_img!='';");
		if (mysql_num_rows($res)>0)
		{
			echo "Es sind ".mysql_num_rows($res)." Bilder gespeichert!<br/><br/>";
			echo "<form action=\"\" method=\"post\">
			<table class=\"tb\"><tr><th>User</th><th>Fehler</th><th>Aktionen</th></tr>";
			while($arr = mysql_fetch_assoc($res))
			{
				echo "<tr><td>[".$arr['alliance_tag']."] ".$arr['alliance_name']."</td><td>";
				if (file_exists($dir.$arr['alliance_img']))
				{
					echo '<img src="'.$dir.$arr['alliance_img'].'" alt="Profil" />';
				}
				else
				{
					echo '<span style=\"color:red\">Bild existiert nicht!</span>';
				}
				echo "</td><td>
				<input type=\"radio\" name=\"validate[".$arr['alliance_id']."]\" value=\"1\" checked=\"checked\"> Bild ist in Ordnung<br/>
				<input type=\"radio\" name=\"validate[".$arr['alliance_id']."]\" value=\"0\" > Bild verstösst gegen die Regeln. Lösche es!<br/>
				</td></tr>";
			}
			echo "</table><br/>
			<input type=\"submit\" name=\"validate_submit\" value=\"Speichern\" /></form>";
		}
		else
		{
			echo "<br/><i>Keine Bilder vorhanden!</i>";
		}

		//
		// Orphans
		//
		$res=dbquery("
		SELECT 
			alliance_id,
			alliance_name,
			alliance_img 
		FROM 
			alliances 
		WHERE 
			alliance_img!='' 
		");
		$nr = mysql_num_rows($res);
		$paths = array();
		$nicks = array();
		if ($nr>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				$paths[$arr['alliance_id']] = $arr['alliance_img'];
				$nicks[$arr['alliance_id']] = $arr['alliance_name'];
			}
		}
		$files = array();
		if (is_dir($dir)) {
			$d = opendir($dir);
			while ($f = readdir($d))
			{
				if (is_file($dir.$f))
				{
					array_push($files,$f);
				}
			}
			closedir($d);
		}

		$overhead = array();
		while(count($files)>0)
		{
			$k = array_pop($files);
			if (!in_array($k,$paths))
				array_push($overhead,$k);
		}

		if (isset($_GET['action']) && $_GET['action']=="clearoverhead")
		{
			while(count($overhead)>0)
			{
				unlink($dir.array_pop($overhead));
			}
			echo "Verwaiste Bilder gelöscht!<br/><bt/>";
		}
		$co = count($overhead);

		echo "<h2>Verwaiste Bilder</h2>";
		if ($co>0)
		{
				echo "Diese Bilder gehören zu Allianzen, die nicht mehr in unserer Datenbank vorhanden sind.<br/>
				Es sind $co Bilder vorhanden. <a href=\"?page=$page&amp;sub=$sub&amp;action=clearoverhead\">L&ouml;sche alle verwaisten Bilder</a><br/><br/>";
				echo "<table class=\"tb\">
				<tr><th>Datei</th><th>Bild</th></tr>";
				foreach($overhead as $v)
				{
					echo "<tr><td>".$v."</td>";
					echo '<td><img src="'.$dir.$v.'" alt="Profil" /></td></tr>';
				}
				echo "</table><br/>";
		}
		else
		{
			echo "<i>Keine vorhanden!</i>";
		}
	}


	//
	// Gebäude bearbeiten
	//
	elseif ($sub=="buildingsdata")
	{
		advanced_form("alliancebuildings", $twig);
	}

	//
	// Tech bearbeiten
	//
	elseif ($sub=="techdata")
	{
		advanced_form("alliancetechnologies", $twig);
	}

	//
	// Erstellen
	//
	elseif ($sub=="create")
	{
		echo "<h1>Allianz erstellen</h1>";

		if (isset($_POST['create']))
		{
			$errorCode = "";
			if (Alliance::create(array(
				"name" => $_POST['alliance_name'],
				"tag" => $_POST['alliance_tag'],
				"founder" => new User($_POST['alliance_founder_id'])
				),$errorCode))
			{
				success_msg("Allianz wurde erstellt! [[page alliances sub=edit id=".$errorCode->id."]Details[/page]]");
			}
			else
			{
				error_msg("Allianz konnte nicht erstellt werden!\n\n".$errorCode."");
			}
		}

		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		echo '<table class="tbl">';
		echo "<tr><th>Tag:</th><td>
		<input type=\"text\" name=\"alliance_tag\" value=\"\" />
		</td></td>";
		echo "<tr><th>Name:</th><td>
		<input type=\"text\" name=\"alliance_name\" value=\"\" />
		</td></td>";
		echo "<tr><th>Gründer:</th><td>
		<select name=\"alliance_founder_id\" />";
		$res = dbquery("SELECT user_id,user_nick FROM users where user_alliance_id=0 ORDER BY user_nick");
		while ($arr = mysql_fetch_assoc($res))
		{
			echo "<option value=\"".$arr['user_id']."\">".$arr['user_nick']."</option>";
		}
		echo "</select>
		</td></td>";
		tableEnd();
		echo "<p><input type=\"submit\" name=\"create\" value=\"Erstellen\" /></p>
		</form>";
	}

	//
	// Allianznews (Rathaus)
	//
	elseif ($sub=="news")
	{
		echo '<h1>Allianz-News</h1>';

		echo 'News entfernen die älter als <select id="timespan">
		<option value="604800">1 Woche</option>
		<option value="1209600">2 Wochen</option>
		<option value="2592000" selected="selected">1 Monat</option>
		<option value="5184000">2 Monate</option>
		<option value="7776000">3 Monate</option>
		<option value="15552000">6 Monate</option>
		</select> sind: 
		<input type="button" onclick="xajax_allianceNewsRemoveOld(document.getElementById(\'timespan\').options[document.getElementById(\'timespan\').selectedIndex].value)" value="Ausführen" /><br/><br/>';

		$ban_timespan = array(
		21600=>'6 Stunden',
		43200=>'12 Stunden',
		64800=>'18 Stunden',
		86400=>'1 Tag',
		172800=>'2 Tage',
		259200=>'3 Tage',
		432000=>'5 Tage',
		604800=>'1 Woche'
		);
		$ban_text = $conf['townhall_ban']['p1']!='' ? stripslashes($conf['townhall_ban']['p1']) : 'Rathaus-Missbrauch';

		echo 'Standardeinstellung für Sperre: <select id="ban_timespan">';
		foreach ($ban_timespan as $k => $v)
		{
			echo '<option value="'.$k.'"';
			echo  $conf['townhall_ban']['v']==$k ? ' selected="selected"' : '';
			echo '>'.$v.'</option>';
		}
		echo '</select> mit folgendem Text: <input type="text" id="ban_text" value="'.$ban_text.'" size="35" /> ';
		echo '<input type="button" onclick="xajax_allianceNewsSetBanTime(document.getElementById(\'ban_timespan\').options[document.getElementById(\'ban_timespan\').selectedIndex].value,document.getElementById(\'ban_text\').value)" value="Speichern" /><br/><br/>';

		echo '<form id="newsForm" action="?page='.$page.'&amp;sub='.$sub.'" method="post">';
		echo '<div id="newsBox">Lade...</div></form>';
		echo '<script type="text/javascript">xajax_allianceNewsLoad()</script>';
	}

	elseif ($sub=="crap")
	{
		echo "<h1>&Uuml;berfl&uuml;ssige Daten</h1>";

		// Daten laden
		$alliances=get_alliance_names();

		$ally_ids=array_keys($alliances);
		$users = get_user_names();
		$user_ids=array_keys($users);

		if (isset($_GET['action']) && $_GET['action']=="cleanranks")
		{
			$res=dbquery("SELECT rank_alliance_id,rank_id FROM alliance_ranks;");
			if (mysql_num_rows($res)>0)
			{
				while($arr=mysql_fetch_array($res))
					if (!in_array($arr['rank_alliance_id'],$ally_ids))
						dbquery("DELETE FROM alliance_ranks WHERE rank_id=".$arr['rank_id'].";");
			}
			echo "Fehlerhafte Daten gel&ouml;scht<br/>";
		}
		elseif (isset($_GET['action']) && $_GET['action']=="clearbnd")
		{
			$res=dbquery("SELECT alliance_bnd_alliance_id1,alliance_bnd_alliance_id2,alliance_bnd_id FROM alliance_bnd;");
			if (mysql_num_rows($res)>0)
			{
				while($arr=mysql_fetch_array($res))
					if (!in_array($arr['alliance_bnd_alliance_id1'],$ally_ids) || !in_array($arr['alliance_bnd_alliance_id2'],$ally_ids))
						dbquery("DELETE FROM alliance_bnd WHERE alliance_bnd_id=".$arr['alliance_bnd_id'].";");
			}
			echo "Fehlerhafte Daten gel&ouml;scht<br/>";
		}
		elseif (isset($_GET['action']) && $_GET['action']=="dropinactive")
		{
			$res = dbquery("SELECT * FROM alliances ORDER BY alliance_tag;");
			if (mysql_num_rows($res)>0)
			{
				$cnt=0;
				while ($arr = mysql_fetch_array($res))
				{
					$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM users WHERE user_alliance_id=".$arr['alliance_id'].";"));
					if ($tblcnt[0]==0)
					{
						dbquery("DELETE FROM alliances WHERE alliance_id=".$arr['alliance_id'].";");
						dbquery("DELETE FROM alliance_ranks WHERE rank_alliance_id='".$arr['alliance_id']."';");
						dbquery("DELETE FROM alliance_bnd WHERE alliance_bnd_alliance_id1='".$arr['alliance_id']."' OR alliance_bnd_alliance_id2='".$arr['alliance_id']."';");
						$cnt++;
					}
				}
			}
			echo "$cnt leere Allianzen wurden gel&ouml;scht!<br/>";
		}


		if (count($alliances)>0)
		{
			// Ränge ohne Allianz
			echo "<h2>R&auml;nge ohne Allianz:</h2>";
			$res=dbquery("SELECT rank_alliance_id FROM alliance_ranks;");
			if (mysql_num_rows($res)>0)
			{
				$cnt=0;
				while($arr=mysql_fetch_array($res))
					if (!in_array($arr['rank_alliance_id'],$ally_ids))
						$cnt++;
				if ($cnt>0)
					echo "$cnt von ".mysql_num_rows($res)." R&auml;nge ohne Allianz! <a href=\"?page=$page&amp;sub=$sub&amp;action=cleanranks\">L&ouml;schen?</a>";
				else
					echo "Keine fehlerhaften Daten gefunden";
			}

			// Bündnisse/Kriege ohne Allianz
			echo "<h2>B&uuml;ndnisse/Kriege ohne Allianz:</h2>";
			$res=dbquery("SELECT alliance_bnd_alliance_id1,alliance_bnd_alliance_id2 FROM alliance_bnd;");
			if (mysql_num_rows($res)>0)
			{
				$cnt=0;
				while($arr=mysql_fetch_array($res))
					if (!in_array($arr['alliance_bnd_alliance_id1'],$ally_ids) || !in_array($arr['alliance_bnd_alliance_id2'],$ally_ids))
						$cnt++;
				if ($cnt>0)
					echo "$cnt von ".mysql_num_rows($res)." B&uuml;ndnisse/Kriege ohne Allianz! <a href=\"?page=$page&amp;sub=$sub&amp;action=clearbnd\">L&ouml;schen?</a>";
				else
					echo "Keine fehlerhaften Daten gefunden!";
			}

			// Allianzen ohne Gründer
			echo "<h2>Allianzen ohne Gr&uuml;nder:</h2>";
			echo "<table class=\"tbl\">";
			echo "<tr><th class=\"tbltitle\">Tag</th><th class=\"tbltitle\">Name</th><th>&nbsp;</th></tr>";
			$cnt=0;
			foreach($alliances as $k=>$v)
			{
				if (!in_array($v['founder_id'],$user_ids))
				{
					echo "<tr><td class=\"tbldata\">".$v['name']."</td><td class=\"tbldata\">".$v['tag']."</td><td class=\"tbldata\"><a href=\"?page=$page&amp;sub=edit&amp;alliance_id=$k\">detail</a></td></tr>";
					$cnt++;
				}
			}
			if ($cnt==0)
				echo "<tr><td class=\"tbldata\" colspan=\"2\">Keine fehlerhaften Daten gefunden!</td></tr>";
			echo "</table><br/>";
			if ($cnt>0)
				echo "$cnt Allianzen von ".count($alliances)." ohne Gr&uuml;nder!";

			// User mit fehlerhafter Allianz-Verknüpfung
			echo "<h2>User mit fehlerhafter Allianz-Verkn&uuml;pfung:</h2>";
			echo "<table class=\"tbl\">";
			echo "<tr><th class=\"tbltitle\">Nick</th><th class=\"tbltitle\">E-Mail</th><th>&nbsp;</th></tr>";
			$cnt=0;
			foreach($users as $k=>$v)
			{
				if ($v['alliance_id']!=0)
				{
					if (!in_array($v['alliance_id'],$ally_ids))
					{
						echo "<tr><td class=\"tbldata\">".$v['nick']."</td><td class=\"tbldata\">".$v['email']."</td><td class=\"tbldata\"><a href=\"?page=user&amp;sub=edit&amp;user_id=$k\">detail</a></td></tr>";
						$cnt++;
					}
				}
			}
			if ($cnt==0)
				echo "<tr><td class=\"tbldata\" colspan=\"2\">Keine fehlerhaften Daten gefunden!</td></tr>";
			echo "</table><br/>";
			if ($cnt>0)
				echo "$cnt User von ".count($users)." mit fehlerhafter Verkn&uuml;pfung!";

			// Leere Allianzen
			echo "<h2>Leere Allianzen (Allianzen ohne User)</h2>";
			echo "<table class=\"tbl\">";
			echo "<tr><th class=\"tbltitle\">Name</th><th class=\"tbltitle\">Tag</th><th>&nbsp;</th><th>&nbsp;</th></tr>";
			$cnt=0;
			foreach($alliances as $k=>$v)
			{
				$ucnt=0;
				foreach($users as $uk=>$uv)
				{
					if ($uv['alliance_id']==$k)
						$ucnt++;
				}
				if ($ucnt==0)
				{
					echo "<tr><td class=\"tbldata\">".$v['name']."</td><td class=\"tbldata\">".$v['tag']."</td><td class=\"tbldata\"><a href=\"?page=$page&amp;sub=edit&amp;alliance_id=$k\">detail</a></td><td class=\"tbldata\"><a href=\"?page=$page&amp;sub=drop&amp;alliance_id=$k\">l&ouml;schen</a></td></tr>";
					$cnt++;
				}
			}
			if ($cnt==0)
				echo "<tr><td class=\"tbldata\" colspan=\"2\">Keine fehlerhaften Daten gefunden!</td></tr>";
			echo "</table><br/>";
			if ($cnt>0)
				echo "$cnt Allianzen von ".count($alliances)." sind leer! <a href=\"?page=$page&amp;sub=$sub&amp;action=dropinactive\">L&ouml;schen?</a>";
		}
		else
			echo "Keine Allianzen vorhanden!";
	}
	else
	{
		$twig->addGlobal('title', 'Allianzen');

		//
		// Suchergebnisse
		//

		if ((isset($_POST['alliance_search']) && $_POST['alliance_search']!="" || $_SESSION['admin']['queries']['alliances']!="") && isset($_GET['action']) && $_GET['action']=="search")
		{
			$twig->addGlobal('subtitle', 'Suchergebnisse');

  		if ($_SESSION['admin']['queries']['alliances']=="")
  		{
			$sql = '';
				if ($_POST['alliance_id']!="")
				{
					$sql.= " AND alliance_id ".stripslashes($_POST['qmode']['alliance_id']).$_POST['alliance_id']."$addchars'";
				}
				if ($_POST['alliance_tag']!="")
				{
					if (stristr($_POST['qmode']['alliance_tag'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND alliance_tag ".stripslashes($_POST['qmode']['alliance_tag']).$_POST['alliance_tag']."$addchars'";
				}
				if ($_POST['alliance_name']!="")
				{
					if (stristr($_POST['qmode']['alliance_name'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND alliance_name ".stripslashes($_POST['qmode']['alliance_name']).$_POST['alliance_name']."$addchars'";
				}
				if ($_POST['alliance_text']!="")
				{
					if (stristr($_POST['qmode']['alliance_text'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND alliance_text ".stripslashes($_POST['qmode']['alliance_text']).$_POST['alliance_text']."$addchars'";
				}

				$sqlstart =	"SELECT 
					alliance_id,
					alliance_name,
					alliance_tag,
					alliance_foundation_date,
					alliance_founder_id,
					COUNT(user_id) AS cnt			
				FROM 
					alliances 
				LEFT JOIN
					users ON user_alliance_id=alliance_id
				WHERE 1 ";
				$sqlend = "
				GROUP BY alliance_id
				ORDER BY alliance_tag;";
				$sql = $sqlstart.$sql.$sqlend;
				$_SESSION['admin']['queries']['alliances'] = $sql;
			}
			else
				$sql = $_SESSION['admin']['queries']['alliances'];

			$res = dbquery($sql);
			$nr = mysql_num_rows($res);
			if ($nr==1)
			{
				$arr = mysql_fetch_array($res);
				echo "<script>document.location='?page=$page&sub=edit&id=".$arr['alliance_id']."';</script>
				Klicke <a href=\"?page=$page&sub=edit&id=".$arr['alliance_id']."\">hier</a> falls du nicht automatisch weitergeleitet wirst...";
			}
			elseif ($nr>0)
			{
				echo $nr." Datens&auml;tze vorhanden<br/><br/>";
				if ($nr>20)
				{
					echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /><br/><br/>";
				}

 				$users = get_user_names();
				echo "<table class=\"tb\">";
				echo "<tr>";
				echo "<th>ID</th>";
				echo "<th>Name</th>";
				echo "<th>Gr&uuml;nder</th>";
				echo "<th>Gr&uuml;ndung</th>";
				echo "<th>User</th>";
				echo "<th>&nbsp;</th>";
				echo "</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					echo "<tr>";
					echo "<td>".$arr['alliance_id']."</td>";
					echo "<td>[".$arr['alliance_tag']."] <a href=\"?page=$page&sub=edit&alliance_id=".$arr['alliance_id']."\">".$arr['alliance_name']."</a></td>";
					echo "<td>".$users[$arr['alliance_founder_id']]['nick']."</td>";
					echo "<td>".df($arr['alliance_foundation_date'])."</td>";
					echo "<td>".$arr['cnt']."</td>";
					echo "<td style=\"width:50px;\">";
					echo del_button("?page=$page&sub=drop&alliance_id=".$arr['alliance_id'])."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br/><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=search'\" value=\"Aktualisieren\" /> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=dropinactive'\" value=\"Leere Allianzen l&ouml;schen\" />";
			}
			else
			{
				echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Zur&uuml;ck\" />";
			}
		}

		//
		// Leere Allianzen löschen
		//

		elseif (isset($_GET['sub']) && $_GET['sub']=="dropinactive")
		{
			echo "Sollen folgende leeren Allianzen gel&ouml;scht werden?<br/><br/>";
			$res = dbquery("SELECT * FROM alliances ORDER BY alliance_tag;");

			if (mysql_num_rows($res)>0)
			{
 				$users = get_user_names();
				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<td class=\"tbltitle\" valign=\"top\">ID</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Name</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Tag</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Gr&uuml;nder</td>";
				echo "<th>Gr&uuml;ndung</th>";
				echo "<td valign=\"top\">&nbsp;</td>";
				echo "</tr>";
				$cnt=0;
				while ($arr = mysql_fetch_array($res))
				{
					$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM users WHERE user_alliance_id=".$arr['alliance_id'].";"));
					if ($tblcnt[0]==0)
					{
						echo "<tr>";
						echo "<td class=\"tbldata\">".$arr['alliance_id']."</td>";
						echo "<td class=\"tbldata\">".$arr['alliance_name']."</td>";
						echo "<td class=\"tbldata\">".$arr['alliance_tag']."</td>";
						echo "<td class=\"tbldata\">".$users[$arr['alliance_founder_id']]['nick']."</td>";
						echo "<td class=\"tbldata\">".date("Y-m-d",$arr['alliance_foundation_date'])."</td>";
						echo "<td class=\"tbldata\"><a href=\"?page=$page&sub=edit&alliance_id=".$arr['alliance_id']."\">details</a></td>";
						echo "</tr>";
						$cnt++;
					}
				}
				echo "</table>";
				echo "<br/>$cnt von ".mysql_num_rows($res)." Allianzen sind zur L&ouml;schung vorgesehen!<br/>";
				echo "<br/><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Nein, zur&uuml;ck zur &Uuml;bersicht\" /> ";
				if ($cnt>0)
				echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=dropinactive'\" value=\"Ja, l&ouml;schen!\" />";
			}
			else
			{
				echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Zur&uuml;ck\" />";
			}
		}

		//
		// Daten bearbeiten
		//

		elseif (isset($_GET['sub']) && $_GET['sub']=="edit")
		{
			include("alliance/edit.inc.php");
		}

		//
		// Daten löschen
		//

		elseif (isset($_GET['sub']) && $_GET['sub']=="drop")
		{
			$res = dbquery("SELECT * FROM alliances WHERE alliance_id=".$_GET['alliance_id'].";");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				echo "Soll folgende Allianz gel&ouml;scht werden?<br/><br/>";
				echo "<form action=\"?page=$page\" method=\"post\">";
				echo "<table class=\"tbl\">";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">ID</td><td class=\"tbldata\">".$arr['alliance_id']."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Name</td><td class=\"tbldata\">".$arr['alliance_name']."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Tag</td><td class=\"tbldata\">".$arr['alliance_tag']."</td></tr>";
				$users = get_user_names();
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Gr&uuml;nder</td><td class=\"tbldata\">".$users[$arr['alliance_founder_id']]['nick']."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Text</td><td class=\"tbldata\">".text2html($arr['alliance_text'])."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Gr&uuml;ndung</td><td class=\"tbldata\">".date("Y-m-d H:i:s",$arr['alliance_foundation_date'])."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Website</td><td class=\"tbldata\">".$arr['alliance_url']."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Bild</td><td class=\"tbldata\">".$arr['alliance_img']."<br/><img src=\"".$arr['alliance_img']."\" width=\"100%\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Mitglieder</td><td class=\"tbldata\">";
				$ures = dbquery("SELECT user_id,user_nick,user_points FROM users WHERE user_alliance_id=".$arr['alliance_id']." ORDER BY user_nick;");
				if (mysql_num_rows($ures)>0)
				{
					echo "<table style=\"width:100%\">";
					while($uarr=mysql_fetch_array($ures))
						echo "<tr><td>".$uarr['user_nick']."</td>
						<td>".$uarr['user_points']." Punkte</td>
						<td>[<a href=\"?page=user&amp;sub=edit&amp;user_id=".$uarr['user_id']."\">details</a>] [<a href=\"?page=messages&amp;sub=sendmsg&amp;user_id=".$uarr['user_id']."\">msg</a>]</td></tr>";
					echo "</table>";
				}
				else
					echo "<b>KEINE MITGLIEDER!</b>";
				echo "</td></tr>";
				echo "</table>";
				echo "<input type=\"hidden\" name=\"alliance_id\" value=\"".$arr['alliance_id']."\" />";
				echo "<br/><input type=\"submit\" name=\"drop\" value=\"L&ouml;schen\" />&nbsp;";
				echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"history.back();\" /> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" />";
				echo "</form>";
			}
			else
				echo "<b>Fehler:</b>Datensatz nicht gefunden!<br/><br/><a href=\"javascript:history.back();\">Zur&uuml;ck</a>";
		}

		//
		// Suchmaske
		//

		else
		{
			$_SESSION['admin']['queries']['alliances']="";

			// Allianz löschen
			if (isset($_POST['drop']))
			{
				$ally = new Alliance($_POST['alliance_id']);
				if ($ally->delete()) {
					echo "Die Allianz wurde gel&ouml;scht!<br/><br/>";
				} else {
					echo MessageBox::error("", "Allianz konnte nicht gelöscht werden (ist sie in einem aktiven Krieg?)");
				}
			}

			// Leere Allianzen löschen
			if (isset($_GET['action']) && $_GET['action']=="dropinactive")
			{
				$res = dbquery("SELECT * FROM alliances ORDER BY alliance_tag;");
				if (mysql_num_rows($res)>0)
				{
					$cnt=0;
					while ($arr = mysql_fetch_array($res))
					{
						$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM users WHERE user_alliance_id=".$arr['alliance_id'].";"));
						if ($tblcnt[0]==0)
						{
							dbquery("DELETE FROM alliances WHERE alliance_id=".$arr['alliance_id'].";");
							dbquery("DELETE FROM alliance_ranks WHERE rank_alliance_id='".$arr['alliance_id']."';");
							dbquery("DELETE FROM alliance_bnd WHERE alliance_bnd_alliance_id1='".$arr['alliance_id']."' OR alliance_bnd_alliance_id2='".$arr['alliance_id']."';");
							$cnt++;
						}
					}
				}
				echo "$cnt leere Allianzen wurden gel&ouml;scht!<br/><br/>";
			}

			// Suchmaske
			$twig->addGlobal("subtitle", 'Suchmaske');

			echo "<form action=\"?page=$page&amp;action=search\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\">ID</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_id\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('alliance_id');echo"</td></tr>";
			echo "<tr><td class=\"tbltitle\">Tag</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_tag\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('alliance_tag');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Name</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_name\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchAlliance(this.value,'alliance_name','citybox2');\"/> ";fieldqueryselbox('alliance_name');echo "<br><div class=\"citybox\" id=\"citybox2\">&nbsp;</div></td></tr>";
			echo "<tr><td class=\"tbltitle\">Text</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_text\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('alliance_text');echo "</td></tr>";
			echo "</table>";
			echo "<br/><input type=\"submit\" name=\"alliance_search\" value=\"Suche starten\" /> (wenn nichts eingegeben wird werden alle Datens&auml;tze angezeigt)</form>";
			$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM alliances;"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.";

		}
	}
?>

