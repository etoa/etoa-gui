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
	// Tipps
	//
	if ($sub=="tipps")
	{
		advanced_form("tipps");
	}	

	elseif ($sub=="createvar")
	{
		if (isset($_POST['submit_new']) && $_POST['config_name']!="")
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
	
	//
	// Config-Editor
	//
	else
	{
			$conf_type['text']="Textfeld";
			$conf_type['textarea']="Textblock";
			$conf_type['timedate']="Zeit/Datum-Feld";
			$conf_type['onoff']="Ein/Aus-Schalter";

			if (isset($sub) && intval($sub)>0)
				$_GET['cid'] = $sub;

			if (isset($_GET['configcat']) && $_GET['configcat']=="manual")
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
			elseif (isset($_GET['cid']) && $_GET['cid']>0)
			{
				$cres=dbquery("SELECT * FROM config_cat WHERE cat_id=".$_GET['cid'].";");
				if (mysql_num_rows($cres)>0)
				{
					$carr=mysql_fetch_array($cres);
					echo "<h2>".$carr['cat_name']."</h2>";


					if (isset($_POST['submit']))
					{
						$res=dbquery("SELECT * FROM config WHERE config_cat_id=".$_GET['cid']." ORDER BY config_name;");
						while ($arr=mysql_fetch_array($res))
						{
							dbquery("UPDATE config SET config_value='".create_sql_value($arr['config_type_v'],$arr['config_name'],"v",$_POST)."' WHERE config_id='".$arr['config_id']."'");
							dbquery("UPDATE config SET config_param1='".create_sql_value($arr['config_type_p1'],$arr['config_name'],"p1",$_POST)."' WHERE config_id='".$arr['config_id']."'");
							dbquery("UPDATE config SET config_param2='".create_sql_value($arr['config_type_p2'],$arr['config_name'],"p2",$_POST)."' WHERE config_id='".$arr['config_id']."'");
						}
						echo "&Auml;nderungen wurden &uuml;bernommen!<br/><br/>";
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
				echo "<h2>&Uuml;bersicht</h2>";
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

    }

	}
?>

