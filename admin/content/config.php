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
	// 	Dateiname: config.php
	// 	Topic: Generelle Konfigurationseinstellungen
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	echo "<h1>Konfiguration</h1>";

	//
	// Start-Items
	//
	if ($sub=="defaultitems")
	{
		include("config/defaultitems.inc.php");
	}

	//
	// RSS
	//
	elseif ($sub=="rss")
	{
		echo "<h1>RSS-Feeds</h1>";
	
		if (isset($_GET['action']) && $_GET['action']=="gen_townhall")
		{
			Townhall::genRss();
			success_msg("RSS erstellt!");
		}

		echo "<h2>Feeds (neu) generieren</h2>";
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;action=gen_townhall\">Rathaus-Feed generieren</a>";

	
		echo "<br/><br/><h2>Feedliste</h2>";
		tableStart("Vorhandene Feeds");
		Rss::showOverview();
		tableEnd();
		
	}

	//
	// Htaccess-Schutz
	//
	elseif ($sub=="htaccess")
	{
		echo "<h2>Passwort-Schutz für das Admin-Verzeichnis</h2>";
		
		if (isset($_POST['auth_submit']))
		{
			if ($_POST['auth_name']!="" && $_POST['auth_user']!="" && $_POST['auth_pw']!="")
			{
				$cfg->set('admin_htaccess_auth_name',$_POST['auth_name']);
				$cfg->set('admin_htaccess_auth_user',$_POST['auth_user']);
				$cfg->set('admin_htaccess_auth_pw',$_POST['auth_pw']);
				ok_msg("Das Passwort wurde gesetzt!");
			}
			else
			{
				err_msg("Es wurden nicht alle Felder ausgefüllt!");
			}
		}
	
		if (isset($_POST['auth_submit_clear']))
		{
			$cfg->set('admin_htaccess_auth_name','');
			$cfg->set('admin_htaccess_auth_user','');
			$cfg->set('admin_htaccess_auth_pw','');
			ok_msg("Das Passwort wurde entfernt!");
		}		
		
		
		if ($cfg->get("admin_htaccess_auth_user") != "" && $cfg->get("admin_htaccess_auth_pw")!="")
		{
			echo "<div style=\"color:#0f0\">Der Passwort-Schutz ist zurzeit aktiv!</div><br/>";
		}
		else
		{
			echo "<div style=\"color:#f90\">Der Passwort-Schutz ist nicht aktiv!</div><br/>";
		}
		echo "Diese Daten schützen das Admin-Tool zusätzlich und müssen vor dem eigentlichen Login eingegeben werden. Bei 
		einer Änderung dieser Daten müssen alle anderen Admins informiert werden, da sie sonst nicht mehr in diese Tool einloggen können!<br/><br/>";
		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\" autocomplete=\"off\">";
		echo "<table class=\"tb\">";
		echo "<tr>
			<th style=\"width:100px;\">Titel:</th>
			<td><input type=\"text\" name=\"auth_name\" value=\"".$cfg->get("admin_htaccess_auth_name")."\" /></td>
		</tr>";
		echo "<tr>
			<th style=\"width:100px;\">User:</th>
			<td><input type=\"text\" name=\"auth_user\" value=\"".$cfg->get("admin_htaccess_auth_user")."\" /></td>
		</tr>";
		echo "<tr>
			<th style=\"width:100px;\">Passwort:</th>
			<td><input type=\"text\" name=\"auth_pw\" value=\"".$cfg->get("admin_htaccess_auth_pw")."\" /></td>
		</tr>";
		echo "</table><br/><input type=\"submit\" name=\"auth_submit\" value=\"Speichern\" /> &nbsp; ";
		if ($cfg->get("admin_htaccess_auth_user") != "" && $cfg->get("admin_htaccess_auth_pw")!="")
		{		
			echo "<input type=\"submit\" name=\"auth_submit_clear\" value=\"Schutz deaktivieren (nicht empfohlen)\" />";
		}
		echo "</form>";
		
	}

	//
	// Bildpakete
	//
	elseif ($sub=="imagepacks")
	{
		echo "<h2>Downloadbare Bildpakete erzeugen</h2>";

		$pkg = new ImagePacker("../images/imagepacks","../cache/imagepacks");

		if (isset($_GET['gen']))
		{
			echo "Erstelle Pakate...<br/><div style=\"border:1px solid #fff;\">";
			$pkg->pack();
			echo "</div><br/>";
		}

		if ($pkg->check())
		{
		 echo "<div style=\"color:#0f0\">Bildpakete sind vorhanden!</div>";
		}
		else
		{
		 echo "<br/><div style=\"color:#f00\">Bildpakete sind NICHT vollständig vorhanden!</div>";
		}
		echo "<br/><br/>";

		if (UNIX)
		{
			echo "<a href=\"?page=$page&amp;sub=$sub&amp;gen=1\">Neu erstellen</a>";
		}
		else
		{
			error_msg("Bildpakete können nur auf einem Unix System erstellt werden!");
		}			
	}

	//
	// Universe Maintenance
	//
	elseif ($sub=="uni")
	{

		//
		// Universum erstellen
		//
		if ($_POST['submit_create_universe'])
		{
  		echo "<h2>Urknall</h2>";
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "Neues Universum erstellen? (Alle Einstellungen werden von der <a href=\"?page=config&cid=3\">Konfiguration</a> &uuml;bernommen!)<br/><br/>";
			
			echo "<table class=\"tb\" style=\"width:400px;\">";
			echo "<tr><th>Anzahl Sektoren X:</th><td>".$cfg->param1('num_of_sectors')."</td></tr>";
			echo "<tr><th>Anzahl Sektoren Y:</th><td>".$cfg->param2('num_of_sectors')."</td></tr>";
			echo "<tr><th>Anzahl Zellen X:</th><td>".$cfg->param1('num_of_cells')."</td></tr>";
			echo "<tr><th>Anzahl Zellen Y:</th><td>".$cfg->param2('num_of_cells')."</td></tr>";
			echo "<tr><th>Minimale Felder pro Planet:</th><td>".$cfg->param1('planet_fields')."</td></tr>";
			echo "<tr><th>Maximale Felder pro Planet:</th><td>".$cfg->param2('planet_fields')."</td></tr>";
			echo "<tr><th>Minimale Planetentemparatur:</th><td>".$cfg->param1('planet_temp')."</td></tr>";
			echo "<tr><th>Maximale Planetentemparatur:</th><td>".$cfg->param2('planet_temp')."</td></tr>";
			echo "<tr><th>Planetentemperaturdifferent:</th><td>".$cfg->value('planet_temp')."</td></tr>";
			echo "<tr><th>Anzahl Sternensysteme %:</th><td>".$cfg->value('space_percent_solsys')."</td></tr>";
			echo "<tr><th>Anzahl Asteroidenfelder %:</th><td>".$cfg->value('space_percent_asteroids')."</td></tr>";
			echo "<tr><th>Anzahl Nebelwolken %:</th><td>".$cfg->value('space_percent_nebulas')."</td></tr>";
			echo "<tr><th>Anzahl Wurmlöcher %:</th><td>".$cfg->value('space_percent_wormholes')."</td></tr>";
			echo "<tr><th>Maximale Anzahl Planeten/Sternensystem:</th><td>".$cfg->param1('num_planets')."</td></tr>";
			echo "<tr><th>Minimale Anzahl Planeten/Sternensystem:</th><td>".$cfg->param2('num_planets')."</td></tr>";
			echo "<tr><th>Anzahl verschiedener Planetenbilder / Typ:</th><td>".$cfg->value('num_planet_images')."</td></tr>";
			echo "</table><br/>";

			$imgpath = "../images/galaxylayout_".($cfg->param1('num_of_sectors')*$cfg->param1('num_of_cells'))."_".($cfg->param2('num_of_sectors')*$cfg->param2('num_of_cells')).".png";
			if (is_file($imgpath))	
			{
				echo "Bildvorlage gefunden, verwende diese: <img src=\"".$imgpath."\" /><br/><br/>";
			}
			
			echo "<input onclick=\"return confirm('Universum wirklich erstellen?')\" type=\"submit\" name=\"submit_create_universe2\" value=\"Ja, ein neues Universum erstellen\" >";
			echo "</form>";
		}
		// Erweitern
		elseif($_POST['submit_expansion_universe'])
		{
			echo "<h2>Universum erweitern</h2>";
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "<b>Universum (".$conf['num_of_sectors']['p1']."x".$conf['num_of_sectors']['p2'].") erweitern</b><br><br>";
			echo "Erweitere das Universum. Es werden dabei die bereits gespeicherten Daten &uuml;bernommen bez&uuml;glich der der Aufteilung von Planeten, Sonnensystemen, Gasplaneten, Wurml&ouml;chern etc. &Auml;ndere allenfals die Daten unter dem Link \"Universum\".<br><br>";

			echo "Gr&ouml;sse nach dem Ausbau: ";
			//erstellt 2 auswahllisten für die ausbaugrösse
  	      echo "<select name=\"expansion_sector_x\">";
  	      for ($x=($conf['num_of_sectors']['p1']+1);10>=$x;$x++)
  	      {
  	              echo "<option value=\"$x\">$x</option>";
  	      }
  	      echo "</select>";
  	      echo " x ";
  	      echo "<select name=\"expansion_sector_y\">";
  	      for ($x=($conf['num_of_sectors']['p2']+1);10>=$x;$x++)
  	      {
  	              echo "<option value=\"$x\">$x</option>";
  	      }
  	      echo "</select>";
  	      echo "<br>";

			echo "<input onclick=\"return confirm('Universum wirklich erweitern?')\" type=\"submit\" name=\"submit_expansion_universe2\" value=\"Erweitern\" >";
			echo "</form>";
		}
		// Reset
		elseif ($_POST['submit_reset'])
		{
			echo "<h2>Runde zur&uuml;cksetzen</h2>";
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "Runde wirklich zur&uuml;cksetzen?<br/><br/>";
			echo "<input onclick=\"return confirm('Reset wirklich durchf&uuml;hren?')\" type=\"submit\" name=\"submit_reset2\" value=\"Ja, die gesamte Runde zur&uuml;cksetzen\" >";
			echo "</form>";
		}

		// Uni-Optionen
		else
		{
			if($_POST['submit_create_universe2'])
			{
				Universe::create();
				echo "<br/><br/>
				<img src=\"../misc/map.image.php\" alt=\"Galaxiekarte\" id=\"img\" usemap=\"#Galaxy\" style=\"border:none;\"/><br/><br/>
				<input type=\"button\" value=\"Weiter\" onclick=\"document.location='?page=config&sub=uni'\" />";
			}
			else
			{
	
				// Check if universe exists
				$res = dbquery("SELECT COUNT(id) FROM cells;");
				$arr = mysql_fetch_row($res);
				if ($arr[0]==0)
				{
	        echo "<h2>Urknall</h2>";
	        echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
	        echo "Neues Universum erstellen<br/><br/>";
	        echo "<input type=\"submit\" name=\"submit_create_universe\" value=\"Start\" >";
	        echo "</form><br/>";
		  	}
				else
				{
					/*
	        echo "<h2>Universum erweitern</h2>";
	        if($_POST['submit_expansion_universe2'])
	        {
	            $sector_x = $_POST['expansion_sector_x'];
	            $sector_y = $_POST['expansion_sector_y'];
	            expansion_universe($sector_x,$sector_y);
	        }
	        else
	        {
	             echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
	             echo "<b>Universum (".$conf['num_of_sectors']['p1']."x".$conf['num_of_sectors']['p2'].") erweitern</b>?<br/><br/>";
	             echo "<input type=\"submit\" name=\"submit_expansion_universe\" value=\"Universum erweitern\" >";
	             echo "</form>";
	        }*/
	
	        // Reset
	        echo "<h2>Runde zur&uuml;cksetzen</h2>";
	        if($_POST['submit_reset2'])
	        {
	            Universe::reset();
							echo "<br/><input type=\"button\" value=\"Weiter\" onclick=\"document.location='?page=config&sub=uni'\" />";
	        }
	        else
	        {
	            echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
	            echo "Willst du wirklich  die Runde zur&uuml;cksetzen? (alle User, Allianzen und Objekte l&ouml;schen)<br/><br/>";
	            echo "<input type=\"submit\" name=\"submit_reset\" value=\"Ja, die gesamte Runde zur&uuml;cksetzen\" ><br><br>";
	            echo "</form>";
	        }
	    	}
	    }
		}
	}
	
	//
	// Config-Editor
	//
	else
	{
			$conf_type['text']="Textfeld";
			$conf_type['textarea']="Textblock";
			$conf_type['timedate']="Zeit/Datum-Feld";
			$conf_type['onoff']="Ein/Aus-Schalter";

			if ($_GET['configcat']=="manual")
			{
				echo "<h2>Konfigurationstabelle manuell bearbeiten</h2>";
				if ($_POST['new']!="")
				{
					dbquery("INSERT INTO config () VALUES ();");
				}
				if ($_POST['save']!="")
				{
					if (count($_POST['config_name'])>0)
					{
						foreach ($_POST['config_name'] as $id=>$name)
						{
							if ($_POST['config_del'][$id]==1)
								dbquery("DELETE FROM config WHERE config_id=$id;");
							else
								dbquery("UPDATE config SET config_name='$name',config_value='".$_POST['config_value'][$id]."',config_param1='".$_POST['config_param1'][$id]."',config_param2='".$_POST['config_param2'][$id]."' WHERE config_id=$id;");
						}
					}
				}
				echo "<form action=\"?page=config&amp;configcat=manual\" method=\"post\">";
				$res = dbquery("SELECT * FROM config ORDER BY config_name;");
				if (mysql_num_rows($res)>0)
				{
					echo mysql_num_rows($res)." Datens&auml;tze vorhanden<br/><br/>";
					echo "<table width=\"100%\" class=\"tbl\">";
					echo "<tr><td class=\"tbltitle\">Name</td><td class=\"tbltitle\">Wert</td><td class=\"tbltitle\">Param 1</td><td class=\"tbltitle\">Param 2</td><td class=\"tbltitle\">Del</td></tr>";
					while ($arr = mysql_fetch_array($res))
					{
						echo "<tr>";
						echo "<td class=\"tbldata\"><input type=\"text\" name=\"config_name[".$arr['config_id']."]\" value=\"".$arr['config_name']."\" size=\"15\" maxlength=\"250\" /></td>";
						echo "<td class=\"tbldata\"><textarea name=\"config_value[".$arr['config_id']."]\" cols=\"20\" rows=\"2\">".$arr['config_value']."</textarea></td>";
						echo "<td class=\"tbldata\"><textarea name=\"config_param1[".$arr['config_id']."]\" cols=\"10\" rows=\"2\">".$arr['config_param1']."</textarea></td>";
						echo "<td class=\"tbldata\"><textarea name=\"config_param2[".$arr['config_id']."]\" cols=\"10\" rows=\"2\">".$arr['config_param2']."</textarea></td>";
						echo "<td class=\"tbldata\"><input type=\"checkbox\" name=\"config_del[".$arr['config_id']."]\" value=\"1\"></td>\n";
						echo "</tr>";
					}
					echo "</table><br/>";
					echo "<input type=\"submit\" name=\"save\" value=\"&uuml;bernehmen\" />&nbsp;<input type=\"submit\" name=\"new\" value=\"Neuer Datensatz\" />";
				}
				else
					echo "Es sind keine Datens&auml;tze vorhanden!<br/><br/><input type=\"submit\" name=\"new\" value=\"Neuer Datensatz\" />";
				echo " <input type=\"button\" value=\"Zur&uuml;ck zur &Uuml;bersicht\" onclick=\"document.location='?page=config'\" />";
				echo "</form>";
			}
			elseif ($_GET['cid']>0)
			{
				$cres=dbquery("SELECT * FROM config_cat WHERE cat_id=".$_GET['cid'].";");
				if (mysql_num_rows($cres)>0)
				{
					$carr=mysql_fetch_array($cres);
					echo "<h2>".$carr['cat_name']."</h2>";


					if ($_POST['submit']!="")
					{
						$res=dbquery("SELECT * FROM config WHERE config_cat_id=".$_GET['cid']." ORDER BY config_name;");
						while ($arr=mysql_fetch_array($res))
						{
							dbquery("UPDATE config SET config_value='".create_sql_value($arr['config_type_v'],$arr['config_name'],"v",$_POST)."' WHERE config_id='".$arr['config_id']."'");
							dbquery("UPDATE config SET config_param1='".create_sql_value($arr['config_type_p1'],$arr['config_name'],"p1",$_POST)."' WHERE config_id='".$arr['config_id']."'");
							dbquery("UPDATE config SET config_param2='".create_sql_value($arr['config_type_p2'],$arr['config_name'],"p2",$_POST)."' WHERE config_id='".$arr['config_id']."'");
						}
						echo "&Auml;nderungen wurden &uuml;bernommen!<br/><br/>";
						$cfg->reload();
						$conf = get_all_config();
					}

					echo "<form action=\"?page=config&amp;cid=".$_GET['cid']."\" method=\"post\">";
					echo "<table class=\"tb\">";
					$res=dbquery("SELECT * FROM config WHERE config_cat_id=".$_GET['cid']." ORDER BY config_name;");
					while ($arr=mysql_fetch_array($res))
					{
						if ($arr['config_type_v']!="")
						{
							echo "<tr><th width=\"300\">".$arr['config_comment_v']."</th><td class=\"tbldata\">";
							display_field($arr['config_type_v'],$arr['config_name'],"v");
							echo " (".$arr['config_name'].", Wert)</td></tr>";
						}
						if ($arr['config_type_p1']!="")
						{
							echo "<tr><th>".$arr['config_comment_p1']."</th><td class=\"tbldata\">";
							display_field($arr['config_type_p1'],$arr['config_name'],"p1");
							echo " (".$arr['config_name'].", Parameter 1)</td></tr>";
						}
						if ($arr['config_type_p2']!="")
						{
							echo "<tr><th>".$arr['config_comment_p2']."</th><td class=\"tbldata\">";
							display_field($arr['config_type_p2'],$arr['config_name'],"p2");
							echo " (".$arr['config_name'].", Parameter 2)</td></tr>";
						}
						echo "<tr><td colspan=\"2\" style=\"height:1px;\"></td></tr>";
					}
					echo "</table><br/></br/>";
				}
				echo "<input type=\"submit\" name=\"submit\" value=\"&Uuml;bernehmen\" />";
				echo " <input type=\"button\" value=\"Zur&uuml;ck zur &Uuml;bersicht\" onclick=\"document.location='?page=config'\" /></form>";
			}
			else
			{
				echo "<h2>&Uuml;bersicht ".$scr['round_key']."</h2>";
				echo "W&auml;hle eine Kategorie:";
				$res=dbquery("SELECT cat_name,cat_id,COUNT(*) as cnt FROM config_cat,config WHERE cat_id=config_cat_id GROUP BY cat_id ORDER BY cat_order,cat_name;");
				if (mysql_num_rows($res)>0)
				{
					echo "<ul>";
					while ($arr=mysql_fetch_array($res))
					{
						echo "<li><a href=\"?page=config&amp;cid=".$arr['cat_id']."\">".$arr['cat_name']."</a></li>";
					}
					echo "</ul>";
				}
				else
					echo "<br><br/><i>Keine Konfigurationsdaten vorhanden!</i>";

				if ($_POST['submit_new']!="" && $_POST['config_name']!="")
				{
					dbquery("INSERT INTO config (
					config_name,
					config_cat_id,
					config_comment_v,
					config_type_v,
					config_comment_p1,
					config_type_p1,
					config_comment_p2,
					config_type_p2
					) VALUES (
					'".$_POST['config_name']."',
					'".$_POST['config_cat_id']."',
					'".addslashes($_POST['config_comment_v'])."',
					'".$_POST['config_type_v']."',
					'".addslashes($_POST['config_comment_p1'])."',
					'".$_POST['config_type_p1']."',
					'".addslashes($_POST['config_comment_p2'])."',
					'".$_POST['config_type_p2']."'
					)");
					echo "Variable erstellt!<br/><br/>";
				}

				echo "<h2>Neue Konfigurationsvariable anlegen</h2>";
				echo "<form action=\"?page=$page\" method=\"post\"><table class=\"tb\">";
				echo "<tr><th>Schl&uuml;sselwort, Kategorie:</th><td><input type=\"text\" name=\"config_name\" value=\"\" size=\"20\" /> <select name=\"config_cat_id\">";
				$res=dbquery("SELECT cat_name,cat_id FROM config_cat ORDER BY cat_order,cat_name;");
				while ($arr=mysql_fetch_array($res))
					echo "<option value=\"".$arr['cat_id']."\">".$arr['cat_name']."</option>";
				echo "</select></td></tr>";
				echo "<tr><th>Wert-Beschreibung:</th><td><textarea name=\"config_comment_v\" rows=\"2\" cols=\"40\"></textarea></td></tr>";
				echo "<tr><th>Wert-Typ:</th><td><select name=\"config_type_v\">";
				echo "<option value=\"\" style=\"font-style:italic;\">Nichts</option>";
				foreach ($conf_type as $k=>$v)
					echo "<option value=\"$k\">$v</option>";
				echo "</select></td></tr>";
				echo "<tr><th>Parameter 1-Beschreibung:</th><td><textarea name=\"config_comment_p1\" rows=\"2\" cols=\"40\"></textarea></td></tr>";
				echo "<tr><th>Parameter 1-Typ:</th><td><select name=\"config_type_p1\">";
				echo "<option value=\"\" style=\"font-style:italic;\">Nichts</option>";
				foreach ($conf_type as $k=>$v)
					echo "<option value=\"$k\">$v</option>";
				echo "</select></td></tr>";
				echo "<tr><th>Parameter 2-Beschreibung:</th><td><textarea name=\"config_comment_p2\" rows=\"2\" cols=\"40\"></textarea></td></tr>";
				echo "<tr><th>Parameter 2-Typ:</th><td><select name=\"config_type_p2\">";
				echo "<option value=\"\" style=\"font-style:italic;\">Nichts</option>";
				foreach ($conf_type as $k=>$v)
					echo "<option value=\"$k\">$v</option>";
				echo "</select></td></tr>";
				echo "</table><br/><input type=\"submit\" name=\"submit_new\" value=\"Erstellen\" /></form>";
    }

	}
?>

