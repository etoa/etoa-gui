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
	// Bilder prüfen
	//
	if ($sub=="imagecheck")
	{
		echo "<h1>Allianz-Bilder pr&uuml;fen</h1";

		if ($_GET['edit_profile']>0)
		{
			$res = dbquery("SELECT alliance_img FROM alliances WHERE alliance_id=".$_GET['edit_profile'].";");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array($res);
				echo "<h2>Profilbild &auml;ndern</h2><form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
				echo "Bildpfad: <input type=\"text\" name=\"alliance_img\" value=\"".$arr['alliance_img']."\" size=\"70\" /><br/><br/>";
				echo "<input type=\"hidden\" name=\"alliance_id\" value=\"".$_GET['edit_profile']."\" />";
				echo "<input type=\"submit\" name=\"edit_profile_submit\" value=\"Speichern\" /></form>";
			}
		}
		else
		{
			if ($_GET['remove_profile']>0)
			{
				dbquery("UPDATE alliances SET alliance_img='' WHERE alliance_id=".$_GET['remove_profile'].";");
				if (mysql_affected_rows()>0)
					echo "Bild-Verkn&uuml;pfung entfernt!";
			}
			if ($_POST['edit_profile_submit']!="")
			{
				dbquery("UPDATE alliances SET alliance_img='".$_POST['alliance_img']."' WHERE alliance_id=".$_POST['alliance_id'].";");
				if (mysql_affected_rows()>0)
					echo "Bild-Verkn&uuml;pfung ge&auml;ndert!";
			}
	
	
	
			echo "<h2>Fehlerhafte Bilder</h2";
			$res=dbquery("SELECT alliance_id,alliance_name,alliance_img FROM alliances WHERE alliance_img!='' ORDER BY alliance_name");
			if (mysql_num_rows($res)>0)
			{
				$cnt=0;
				echo "<table class=\"tb\"><tr><th>Allianz</th><th>Bild</th><th>Aktionen</th></tr>";
				while($arr=mysql_fetch_array($res))
				{
					if (substr($arr['alliance_img'],0,7)!="http://")
					{
						echo "<tr><td>".$arr['alliance_name']."</td>";
						echo "<td ".tm("Profilbild von ".$arr['alliance_name'],"<img src=\'".$arr['alliance_img']."\' />")."><a href=\"".$arr['alliance_img']."\">".cut_string($arr['alliance_img'],70)."</a></td>";
						echo "<td><a href=\"?page=$page&amp;sub=$sub&amp;remove_profile=".$arr['alliance_id']."\">Entfernen</a> 
						<a href=\"?page=$page&amp;sub=$sub&amp;edit_profile=".$arr['alliance_id']."\">&Auml;ndern</a> 
						<a href=\"?page=$page&amp;sub=edit&amp;alliance_id=".$arr['alliance_id']."\">Profil</a></td></tr>";
						$cnt++;
					}
				}
				if ($cnt==0)
					echo "<tr><td colspan=\"3\"><i>Keine fehlerhaften Bilder vorhanden!</i></td></tr>";
				echo "</table>";
				
			}
		}		
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
		echo '</select> mit folgendem Text: <input type="text" id="ban_text" value="'.$ban_text.'" /> ';
		echo '<input type="button" onclick="xajax_allianceNewsSetBanTime(document.getElementById(\'ban_timespan\').options[document.getElementById(\'ban_timespan\').selectedIndex].value,document.getElementById(\'ban_text\').value)" value="Speichern" /><br/><br/>';
		
		echo '<form id="newsForm" action="?page='.$page.'&amp;sub='.$sub.'" method="post">';
		echo '<div id="newsBox">Lade...</div></form>';
		echo '<script type="text/javascript">xajax_allianceNewsLoad()</script>';
	}
	
	//
	// Allianz-History
	//
	elseif ($sub=="history")
	{
		echo "<h1>Allianzgeschichte</h1>";
		$alliances=get_alliance_names();		

		echo "<h2>Auswahl</h2>";
		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		echo "Allianz ausw&auml;hlen: <select name=\"alliance_id\">";
		foreach ($alliances as $id=>$data)
		{
			echo "<option value=\"$id\"";
			if (isset($_POST['alliance_id']) && $_POST['alliance_id']==$id)
			{
				echo ' selected="selected"';
			}
			echo ">[".$data['tag']."] ".$data['name']."</option>";
		}
		echo "</select> <input type=\"submit\" name=\"submit\" value=\"Anzeigen\" />";

		
		if ($_POST['submit']!="" && $_POST['alliance_id']>0)
		{
			echo "<h2>Geschichte der Allianz [".$alliances[$_POST['alliance_id']]['tag']."] ".$alliances[$_POST['alliance_id']]['name']."</h2>";
			echo "<table>";
			echo "<tr><th class=\"tbltitle\" style=\"width:120px;\">Datum / Zeit</th><th class=\"tbltitle\">Ereignis</th></tr>";
			$hres=dbquery("
			SELECT 
				* 
			FROM 
				alliance_history 
			WHERE 
				history_alliance_id=".$_POST['alliance_id']." 
			ORDER BY 
				history_timestamp DESC;");
			if (mysql_num_rows($hres)>0)
			{
				while ($harr=mysql_fetch_array($hres))
				{
					echo "<tr><td class=\"tbldata\">".date("d.m.Y H:i",$harr['history_timestamp'])."</td><td class=\"tbldata\">".text2html($harr['history_text'])."</td></tr>";
				}				
			}
			else
			{
				echo "<tr><td colspan=\"3\" class=\"tbldata\"><i>Keine Daten vorhanden!</i></td></tr>";
			}
			echo "</table><br/><br/>";
		}
	}
	
	elseif ($sub=="crab")
	{
		echo "<h1>&Uuml;berfl&uuml;ssige Daten</h1>";
		
		// Daten laden
		$alliances=get_alliance_names();
		
		$ally_ids=array_keys($alliances);			
		$users = get_user_names(); 	
		$user_ids=array_keys($users);			

		if ($_GET['action']=="cleanranks")
		{
			$res=dbquery("SELECT rank_alliance_id,rank_id FROM ".$db_table['alliance_ranks'].";");
			if (mysql_num_rows($res)>0)
			{
				while($arr=mysql_fetch_array($res))
					if (!in_array($arr['rank_alliance_id'],$ally_ids))
						dbquery("DELETE FROM ".$db_table['alliance_ranks']." WHERE rank_id=".$arr['rank_id'].";");
			}			
			echo "Fehlerhafte Daten gel&ouml;scht<br/>";
		}
		elseif ($_GET['action']=="clearbnd")
		{
			$res=dbquery("SELECT alliance_bnd_alliance_id1,alliance_bnd_alliance_id2,alliance_bnd_id FROM ".$db_table['alliance_bnd'].";");
			if (mysql_num_rows($res)>0)
			{
				while($arr=mysql_fetch_array($res))
					if (!in_array($arr['alliance_bnd_alliance_id1'],$ally_ids) || !in_array($arr['alliance_bnd_alliance_id2'],$ally_ids))
						dbquery("DELETE FROM ".$db_table['alliance_bnd']." WHERE alliance_bnd_id=".$arr['alliance_bnd_id'].";");
			}		
			echo "Fehlerhafte Daten gel&ouml;scht<br/>";
		}
		elseif ($_GET['action']=="dropinactive")
		{
			$res = dbquery("SELECT * FROM ".$db_table['alliances']." ORDER BY alliance_tag;");
			if (mysql_num_rows($res)>0)
			{
				$cnt=0;
				while ($arr = mysql_fetch_array($res))
				{
					$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM ".$db_table['users']." WHERE user_alliance_id=".$arr['alliance_id'].";"));
					if ($tblcnt[0]==0)
					{
						dbquery("DELETE FROM ".$db_table['alliances']." WHERE alliance_id=".$arr['alliance_id'].";");
						dbquery("DELETE FROM ".$db_table['alliance_ranks']." WHERE rank_alliance_id='".$arr['alliance_id']."';");
						dbquery("DELETE FROM ".$db_table['alliance_bnd']." WHERE alliance_bnd_alliance_id1='".$arr['alliance_id']."' OR alliance_bnd_alliance_id2='".$arr['alliance_id']."';");
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
			$res=dbquery("SELECT rank_alliance_id FROM ".$db_table['alliance_ranks'].";");
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
			$res=dbquery("SELECT alliance_bnd_alliance_id1,alliance_bnd_alliance_id2 FROM ".$db_table['alliance_bnd'].";");
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
			echo "Keine Allianten vorhanden!";
	}
	else
	{
		echo "<h1>Allianzen</h1>";
			
		//
		// Suchergebnisse
		//
		
		if (($_POST['alliance_search']!="" || $_SESSION['admin']['queries']['alliances']!="") && $_GET['action']=="search")
		{
			$tables = $db_table['alliances'];
  	
  		if ($_SESSION['admin']['queries']['alliances']=="")
  		{
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
					alliance_founder_id			
				FROM $tables WHERE 1 ";
				$sqlend = " ORDER BY alliance_tag;";
				$sql = $sqlstart.$sql.$sqlend;
				$_SESSION['admin']['queries']['alliances']=$sql;
			}
			else
				$sql = $_SESSION['admin']['queries']['alliances'];
			
			$res = dbquery($sql);
			if (mysql_num_rows($res)>0)
			{
				echo mysql_num_rows($res)." Datens&auml;tze vorhanden<br/><br/>";
				if (mysql_num_rows($res)>20)
					echo "<a href=\"?page=$page\">Neue Suche</a><br/><br/>";
 
 				$users = get_user_names(); 	
				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<td class=\"tbltitle\" valign=\"top\">ID</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Name</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Tag</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Gr&uuml;nder</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Gr&uuml;ndung</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">User</td>";
				echo "<td valign=\"top\">&nbsp;</td>";
				echo "<td valign=\"top\">&nbsp;</td>";
				echo "</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM ".$db_table['users']." WHERE user_alliance_id=".$arr['alliance_id'].";"));
					if ($tblcnt[0]==0)
						$allyCol=USER_COLOR_INACTIVE;
					else
						$allyCol=USER_COLOR_DEFAULT;
					echo "<tr>";
					echo "<td class=\"tbldata\" style=\"color:$allyCol;\">".$arr['alliance_id']."</td>";
					echo "<td class=\"tbldata\" style=\"color:$allyCol;\">".$arr['alliance_name']."</td>";
					echo "<td class=\"tbldata\" style=\"color:$allyCol;\">".$arr['alliance_tag']."</td>";
					echo "<td class=\"tbldata\" style=\"color:$allyCol;\">".$users[$arr['alliance_founder_id']]['nick']."</td>";
					echo "<td class=\"tbldata\" style=\"color:$allyCol;\">".date("Y-m-d",$arr['alliance_foundation_date'])."</td>";
					echo "<td class=\"tbldata\" style=\"color:$allyCol;\">".$tblcnt[0]."</td>";
					echo "<td class=\"tbldata\" style=\"width:50px;\">".edit_button("?page=$page&sub=edit&alliance_id=".$arr['alliance_id'])." ";
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
		
		elseif ($_GET['sub']=="dropinactive")
		{
			echo "Sollen folgende leeren Allianzen gel&ouml;scht werden?<br/><br/>";
			$res = dbquery("SELECT * FROM ".$db_table['alliances']." ORDER BY alliance_tag;");
			
			if (mysql_num_rows($res)>0)
			{
 				$users = get_user_names(); 	
				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<td class=\"tbltitle\" valign=\"top\">ID</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Name</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Tag</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Gr&uuml;nder</td>";
				echo "<td class=\"tbltitle\" valign=\"top\">Gr&uuml;ndung</td>";
				echo "<td valign=\"top\">&nbsp;</td>";
				echo "</tr>";
				$cnt=0;
				while ($arr = mysql_fetch_array($res))
				{
					$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM ".$db_table['users']." WHERE user_alliance_id=".$arr['alliance_id'].";"));
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
		
		elseif ($_GET['sub']=="edit")
		{
			if ($_POST['save']!="")
			{
				//  Bild löschen wenn nötig
				if ($_POST['alliance_img_del']==1)
        {
					$res = dbquery("SELECT alliance_img FROM alliances WHERE alliance_id=".$_GET['alliance_id'].";");
					if (mysql_num_rows($res)>0)
					{
						$arr=mysql_fetch_array($res);
	          if (file_exists('../'.ALLIANCE_IMG_DIR."/".$arr['alliance_img']))
	          {
	              unlink('../'.ALLIANCE_IMG_DIR."/".$arr['alliance_img']);
	          }
	          $img_sql=",alliance_img=''";
	        }
        }
		
				
				// Daten speichern
				dbquery("
				UPDATE 
					".$db_table['alliances']." 
				SET 
					alliance_name='".$_POST['alliance_name']."',
					alliance_tag='".$_POST['alliance_tag']."',
					alliance_text='".addslashes($_POST['alliance_text'])."',
					alliance_application_template='".addslashes($_POST['alliance_application_template'])."',
					alliance_url='".$_POST['alliance_url']."',
					alliance_founder_id='".$_POST['alliance_founder_id']."' 
					".$img_sql."
				WHERE 
					alliance_id='".$_GET['alliance_id']."'
				;");
				// Ränge speichern
				if (count($_POST['rank_del'])>0)
					foreach($_POST['rank_del'] as $k=>$v)
						dbquery("DELETE FROM ".$db_table['alliance_ranks']." WHERE rank_id='$k';");
				if (count($_POST['rank_name'])>0)
					foreach($_POST['rank_name'] as $k=>$v)
						dbquery("UPDATE ".$db_table['alliance_ranks']." SET rank_name='".addslashes($v)."',rank_level='".$_POST['rank_level'][$k]."',rank_points='".$_POST['rank_points'][$k]."' WHERE rank_id='$k';");
				// Bündnisse / Kriege speichern
				if (count($_POST['alliance_bnd_del'])>0)
					foreach($_POST['alliance_bnd_del'] as $k=>$v)
						dbquery("DELETE FROM ".$db_table['alliance_bnd']." WHERE alliance_bnd_id='$k';");
				if (count($_POST['alliance_bnd_alliance_id2'])>0)
					foreach($_POST['alliance_bnd_alliance_id2'] as $k=>$v)
						dbquery("UPDATE ".$db_table['alliance_bnd']." SET alliance_bnd_alliance_id2='".$v."',alliance_bnd_level='".$_POST['alliance_bnd_level'][$k]."',alliance_bnd_text='".$_POST['alliance_bnd_text'][$k]."' WHERE alliance_bnd_id='$k';");
			}
			
			$res = dbquery("SELECT * FROM ".$db_table['alliances']." WHERE alliance_id=".$_GET['alliance_id'].";");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				echo "<form action=\"?page=$page&sub=edit&alliance_id=".$_GET['alliance_id']."\" method=\"post\">";
				echo "<table class=\"tbl\">";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">ID</td><td class=\"tbldata\">".$arr['alliance_id']."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Name</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_name\" value=\"".$arr['alliance_name']."\" size=\"20\" maxlength=\"250\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Tag</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_tag\" value=\"".$arr['alliance_tag']."\" size=\"20\" maxlength=\"250\" /></td></tr>";
				$users = get_user_names();
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Gr&uuml;nder</td><td class=\"tbldata\"><select name=\"alliance_founder_id\">";
				echo "<option value=\"0\">(niemand)</option>";
				foreach ($users as $uid=>$udata)
				{
					echo "<option value=\"$uid\"";
					if ($arr['alliance_founder_id']==$uid) echo " selected=\"selected\"";
					echo ">".$udata['nick']."</option>";
				}			
				echo "</select></td></tr>";				
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Text</td><td class=\"tbldata\"><textarea cols=\"45\" rows=\"10\" name=\"alliance_text\">".stripslashes($arr['alliance_text'])."</textarea></td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Gr&uuml;ndung</td><td class=\"tbldata\">".date("Y-m-d H:i:s",$arr['alliance_foundation_date'])."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Website</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_url\" value=\"".$arr['alliance_url']."\" size=\"40\" maxlength=\"250\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Bewerbungsvorlage</td><td class=\"tbldata\"><textarea cols=\"45\" rows=\"10\" name=\"alliance_application_template\">".stripslashes($arr['alliance_application_template'])."</textarea></td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Bild</td><td class=\"tbldata\">";
	      if ($arr['alliance_img']!="")
	      {
	        echo '<img src="../'.ALLIANCE_IMG_DIR.'/'.$arr['alliance_img'].'" alt="Profil" /><br/>';
	        echo "<input type=\"checkbox\" value=\"1\" name=\"alliance_img_del\"> Bild l&ouml;schen<br/>";
	      }				
				echo "</td></tr>";				
				
				
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Mitglieder</td><td class=\"tbldata\">";
				$ures = dbquery("SELECT user_id,user_nick,user_points FROM ".$db_table['users']." WHERE user_alliance_id=".$arr['alliance_id']." ORDER BY user_points DESC,user_nick;");
				if (mysql_num_rows($ures)>0)
				{
					echo "<table style=\"width:100%\">";
					while($uarr=mysql_fetch_array($ures))
						echo "<tr><td>".$uarr['user_nick']."</td><td>".nf($uarr['user_points'])." Punkte</td><td>[<a href=\"?page=user&amp;sub=edit&amp;user_id=".$uarr['user_id']."\">details</a>] [<a href=\"?page=messages&amp;sub=sendmsg&amp;user_id=".$uarr['user_id']."\">msg</a>]</td></tr>";
					echo "</table>";
				}
				else
					echo "<b>KEINE MITGLIEDER!</b>";
				echo "</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">R&auml;nge</td><td class=\"tbldata\">";
				$rres = dbquery("SELECT rank_id,rank_level,rank_name,rank_points FROM ".$db_table['alliance_ranks']." WHERE rank_alliance_id=".$arr['alliance_id']." ORDER BY rank_level DESC;");
				if (mysql_num_rows($rres)>0)
				{
					echo "<table style=\"width:100%\">";
					echo "<tr><th>Name</th><th>Punkte</th><th>Rang</th><th>L&ouml;schen</th></tr>";
					while($rarr=mysql_fetch_array($rres))
					{
						echo "<tr><td><input type=\"text\" size=\"10\" name=\"rank_name[".$rarr['rank_id']."]\" value=\"".$rarr['rank_name']."\" /></td>";
						echo "<td><input type=\"text\" size=\"10\" name=\"rank_points[".$rarr['rank_id']."]\" value=\"".$rarr['rank_points']."\" /></td>";
						echo "<td><select name=\"rank_level[".$rarr['rank_id']."]\">";
						for($x=0;$x<=5;$x++)
						{
							echo "<option value=\"$x\"";
							if ($rarr['rank_level']==$x) echo " selected=\"selected\"";
							echo ">$x</option>";
						}
						echo "</select></td>";
						echo "<td><input type=\"checkbox\" name=\"rank_del[".$rarr['rank_id']."]\" value=\"1\" /></td></tr>";
					}
					echo "</table>";
				}
				else
					echo "<b>Keine R&auml;nge vorhanden!</b>";
				echo "</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">B&uuml;ndnisse/Kriege</td><td class=\"tbldata\">";
				$bres = dbquery("SELECT * FROM ".$db_table['alliance_bnd']." WHERE alliance_bnd_alliance_id1=".$arr['alliance_id']." ORDER BY alliance_bnd_level DESC,alliance_bnd_date DESC;");
				if (mysql_num_rows($bres)>0)
				{
					echo "<table style=\"width:100%\">";
					echo "<tr><th valign=\"top\">Allianz / Text</th><th valign=\"top\">Status / Datum</th><th valign=\"top\">L&ouml;schen</th></tr>";
					while($barr=mysql_fetch_array($bres))
					{
						echo "<tr><td><select name=\"alliance_bnd_alliance_id2[".$barr['alliance_bnd_id']."]\">";
						$ally_arr=get_alliance_names();
						echo "<option value=\"0\">(Keine)</option>";
						foreach ($ally_arr as $aid=>$ak)
						{
							echo "<option value=\"$aid\"";
							if ($aid==$barr['alliance_bnd_alliance_id2']) echo " selected=\"selected\"";
							echo ">[".$ak['tag']."]  ".$ak['name']."</option>";
						}
						echo "</select><br/>";		
						echo "<textarea cols=\"30\" rows=\"3\" name=\"alliance_bnd_text[".$barr['alliance_bnd_id']."]\">".stripslashes($barr['alliance_bnd_text'])."</textarea></td>";
						echo "<td valign=\"top\"><select name=\"alliance_bnd_level[".$barr['alliance_bnd_id']."]\">";
						echo "<option value=\"0\">Anfrage</option>";
						echo "<option value=\"2\"";
						if ($barr['alliance_bnd_level']==2) echo " selected=\"selected\"";
						echo ">B&uuml;ndnis</option>";
						echo "<option value=\"2\"";
						if ($barr['alliance_bnd_level']==3) echo " selected=\"selected\"";
						echo ">Krieg</option>";
						echo "</select>";
						echo "".date("d.m.Y H:i",$barr['alliance_bnd_date'])."</td>";
						echo "<td valign=\"top\"><input type=\"checkbox\" name=\"alliance_bnd_del[".$barr['alliance_bnd_id']."]\" value=\"1\" /></td></tr>";
					}
					echo "</table>";
				}
				else
					echo "<b>Keine B&uuml;ndnisse/Kriege vorhanden!</b>";
				echo "</td></tr>";
				echo "</table>";
				echo "<br/><input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" />&nbsp;";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=search'\" /> ";
				echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" />";
				echo "</form>";
			}
			else
				echo "<b>Fehler:</b>Datensatz nicht gefunden!<br/><br/><a href=\"javascript:history.back();\">Zur&uuml;ck</a>";

		}
		
		//
		// Daten löschen
		//
		
		elseif ($_GET['sub']=="drop")
		{
			$res = dbquery("SELECT * FROM ".$db_table['alliances']." WHERE alliance_id=".$_GET['alliance_id'].";");
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
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Gr&uuml;nder</td><td class=\"tbldata\">".$users[$arr['alliance_founder_id']]."</td></tr>";				
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Text</td><td class=\"tbldata\">".text2html($arr['alliance_text'])."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Gr&uuml;ndung</td><td class=\"tbldata\">".date("Y-m-d H:i:s",$arr['alliance_foundation_date'])."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Website</td><td class=\"tbldata\">".$arr['alliance_url']."</td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Bild</td><td class=\"tbldata\">".$arr['alliance_img']."<br/><img src=\"".$arr['alliance_img']."\" width=\"100%\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\" valign=\"top\">Mitglieder</td><td class=\"tbldata\">";
				$ures = dbquery("SELECT user_id,user_nick,user_points FROM ".$db_table['users']." WHERE user_alliance_id=".$arr['alliance_id']." ORDER BY user_nick;");
				if (mysql_num_rows($ures)>0)
				{
					echo "<table style=\"width:100%\">";
					while($uarr=mysql_fetch_array($ures))
						echo "<tr><td>".$uarr['user_nick']."</td><td>".$uarr['user_points']." Punkte</td><td>[<a href=\"?page=user&amp;sub=edit&amp;user_id=".$uarr['user_id']."\">details</a>] [<a href=\"?page=messages&amp;sub=sendmsg&amp;user_id=".$uarr['user_id']."\">msg</a>]</td></tr>";
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
			if ($_POST['drop']!="")
			{
				delete_alliance($_POST['alliance_id']);
				echo "Die Allianz wurde gel&ouml;scht!<br/><br/>";
			}

			// Leere Allianzen löschen
			if ($_GET['action']=="dropinactive")
			{
				$res = dbquery("SELECT * FROM ".$db_table['alliances']." ORDER BY alliance_tag;");
				if (mysql_num_rows($res)>0)
				{
					$cnt=0;
					while ($arr = mysql_fetch_array($res))
					{
						$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM ".$db_table['users']." WHERE user_alliance_id=".$arr['alliance_id'].";"));
						if ($tblcnt[0]==0)
						{
							dbquery("DELETE FROM ".$db_table['alliances']." WHERE alliance_id=".$arr['alliance_id'].";");
							dbquery("DELETE FROM ".$db_table['alliance_ranks']." WHERE rank_alliance_id='".$arr['alliance_id']."';");
							dbquery("DELETE FROM ".$db_table['alliance_bnd']." WHERE alliance_bnd_alliance_id1='".$arr['alliance_id']."' OR alliance_bnd_alliance_id2='".$arr['alliance_id']."';");
							$cnt++;
						}
					}
				}
				echo "$cnt leere Allianzen wurden gel&ouml;scht!<br/><br/>";
			}

			// Suchmaske
			echo "Suchmaske (wenn nichts eingegeben wird werden alle Datens&auml;tze angezeigt):<br/><br/>";
			echo "<form action=\"?page=$page&amp;action=search\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\">ID</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_id\" value=\"\" size=\"5\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Tag</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_tag\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('alliance_tag');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Name</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_name\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchAlliance(this.value,'alliance_name','citybox2');\"/> ";fieldqueryselbox('alliance_name');echo "<br><div class=\"citybox\" id=\"citybox2\">&nbsp;</div></td></tr>";
			echo "<tr><td class=\"tbltitle\">Text</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_text\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('alliance_text');echo "</td></tr>";
			echo "</table>";
			echo "<br/><input type=\"submit\" name=\"alliance_search\" value=\"Suche starten\" /></form>";
			$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM ".$db_table['alliances'].";"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.";	
			
		}
	}
?>

