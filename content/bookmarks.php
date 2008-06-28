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
	// 	File: bookmarks.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Target-Bookmarks-Manager
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	$mode = (isset($_GET['mode']) && $_GET['mode']!="") ? $_GET['mode'] : 'target';
	
	echo "<h1>Favoriten</h1>";
 	show_tab_menu("mode",array("target"=>"Zielfavoriten",
 														"fleet"=>"Flottenfavoriten"));
 	echo '<br/>';

	if ($mode=="fleet")
	{
		$res = dbquery("SELECT ship_id,ship_name FROM ships WHERE ship_show=1 ORDER BY ship_type_id,ship_order;");
		while ($arr = mysql_fetch_row($res))
		{
			$ships[$arr[0]] = $arr[1];
		}
	
			// Favorit speichern
			if (isset($_POST['submit_new']))
			{
				$res=dbquery("
				SELECT
					entities.id
				FROM
					entities
				INNER JOIN
					cells
				ON entities.cell_id=cells.id
					AND sx='".$_POST['sx']."'
	        AND sy='".$_POST['sy']."'
	        AND cx='".$_POST['cx']."'
	        AND cy='".$_POST['cy']."'
	        AND pos='".$_POST['pos']."';");
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_row($res);
					$addships = "";
					foreach ($_POST['sid'] as $k => $sid)
					{
						if ($addships=="")
							$addships.= $sid.":".$_POST['scount'][$k];
						else
							$addships.= ",".$sid.":".$_POST['scount'][$k];
					}
					$freight = max(0,intval($_POST['res1'])).",".
					max(0,intval($_POST['res2'])).",".
					max(0,intval($_POST['res3'])).",".
					max(0,intval($_POST['res4'])).",".
					max(0,intval($_POST['res5'])).",".
					"0,".
					max(0,intval($_POST['resp']))."";
					
					$fetch = max(0,intval($_POST['fetch1'])).",".
					max(0,intval($_POST['fetch2'])).",".
					max(0,intval($_POST['fetch3'])).",".
					max(0,intval($_POST['fetch4'])).",".
					max(0,intval($_POST['fetch5'])).",".
					"0,".
					max(0,intval($_POST['fetchp']))."";
					
					dbquery("
					INSERT INTO 
						fleet_bookmarks
					(
						user_id,
						name,
						target_id,
						ships,
						res,
						resfetch,
						action
					) 
					VALUES 
					(
						'".$cu->id()."',
						'".addslashes($_POST['name'])."',
						'".$arr[0]."',
						'".$addships."',
						'".$freight."',
						'".$fetch."',
						'".$_POST['action']."'
					);");
							
					ok_msg("Der Favorit wurde hinzugef&uuml;gt!");
				}
				else
				{
					err_msg("Es existiert kein Objekt an den angegebenen Koordinaten!");
				}
			}

			// Favorit löschen
			if (isset($_GET['del']) && $_GET['del']>0)
			{
				dbquery("
				DELETE FROM 
					fleet_bookmarks
				WHERE 
					id='".$_GET['del']."' 
					AND user_id='".$cu->id()."';");
				if (mysql_affected_rows()>0)
					ok_msg("Gelöscht");
			}
		
			echo "<div id=\"shipTempl\" style=\"display:none\">Schiffe hinzufügen: <input type=\"text\" name=\"scount[]\" value=\"1\" size=\"4\" />&nbsp;
			<select name=\"sid[]\">";
			foreach ($ships as $k => $v)
			{
				echo "<option value=\"".$k."\">".$v."</option>";
			}
			echo "</select></div>";
		
			echo "<form action=\"?page=$page&amp;mode=$mode\" method=\"post\">";
			infobox_start("Favorit hinzuf&uuml;gen");
			checker_init();
			echo "Name: <input type=\"text\" name=\"name\" size=\"20\" maxlen=\"200\" value=\"Name\" onfocus=\"if (this.value=='Name') this.value=''\" /> &nbsp; ";
			echo "Ziel: <select name=\"sx\">";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
			{
				echo "<option value=\"$x\">$x</option>";
			}
			echo "</select> / <select name=\"sy\">";
			for ($y=1;$y<=$conf['num_of_sectors']['p2'];$y++)
			{
				echo "<option value=\"$y\">$y</option>";
			}
			echo "</select> : <select name=\"cx\">";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
			{
				echo "<option value=\"$x\">$x</option>";
			}
			echo "</select> / <select name=\"cy\">";
			for ($y=1;$y<=$conf['num_of_cells']['p2'];$y++)
			{
				echo "<option value=\"$y\">$y</option>";
			}
			echo "</select> : <select name=\"pos\">";
			for ($y=0;$y<=$conf['num_planets']['p2'];$y++)
			{
				echo "<option value=\"$y\">$y</option>";
			}
			echo "</select> &nbsp; ";
			echo " <select name=\"action\">";
			foreach (FleetAction::getAll() as $ai)
			{
				echo "<option value=\"".$ai->code()."\" style=\"color:".$ai->color()."\">".$ai->name()."</option>";
			}
			echo "</select><br/><br/>";
			echo "<div id=\"shipboxadd\" style=\"float:left;\">
			Schiffe hinzufügen: <input type=\"text\" name=\"scount[]\" value=\"1\" size=\"4\" />&nbsp;
			<select name=\"sid[]\">";
			foreach ($ships as $k => $v)
			{
				echo "<option value=\"".$k."\">".$v."</option>";
			}
			echo "</select></div>
			<input type=\"button\" value=\"Mehr Schiffe hinzufügen\" onclick=\"document.getElementById('shipboxadd').innerHTML += '<br/>'+document.getElementById('shipTempl').innerHTML\" />
			<br style=\"clear:both;\" />";
			
			echo "<table style=\"margin:10px;width:320px;float:left;\" class=\"tb\">
			<tr><th colspan=\"2\">Fracht</th></tr>
			<tr><th>".RES_ICON_METAL."".RES_METAL."</th>
			<td><input type=\"text\" name=\"res1\" id=\"res1\" value=\"0\" size=\"8\" tabindex=\"".($tabindex++)."\" /></td></tr>
			<tr><th>".RES_ICON_CRYSTAL."".RES_CRYSTAL."</th>
			<td><input type=\"text\" name=\"res2\" id=\"res2\" value=\"0\" size=\"8\" tabindex=\"".($tabindex++)."\" /></td></tr>
			<tr><th>".RES_ICON_PLASTIC."".RES_PLASTIC."</th>
			<td><input type=\"text\" name=\"res3\" id=\"res3\" value=\"0\" size=\"8\" tabindex=\"".($tabindex++)."\" /></td></tr>
			<tr><th>".RES_ICON_FUEL."".RES_FUEL."</th>
			<td><input type=\"text\" name=\"res4\" id=\"res4\" value=\"0\" size=\"8\" tabindex=\"".($tabindex++)."\"/></td></tr>
			<tr><th>".RES_ICON_FOOD."".RES_FOOD."</th>
			<td><input type=\"text\" name=\"res5\" id=\"res5\" value=\"0\" size=\"8\" tabindex=\"".($tabindex++)."\"  /></td></tr>
			<tr><th>".RES_ICON_PEOPLE."Passagiere</th>
			<td><input type=\"text\" name=\"resp\" id=\"resp\" value=\"0\" size=\"8\" tabindex=\"".($tabindex++)."\"/></td></tr>					
			</table>

			<table style=\"margin:10px;width:320px;float:left;\" class=\"tb\">
			<tr><th colspan=\"2\">Abholauftrag</th></tr>
			<tr><th>".RES_ICON_METAL."".RES_METAL."</th>
			<td><input type=\"text\" name=\"fetch1\" id=\"res1\" value=\"0\" size=\"8\" tabindex=\"".($tabindex++)."\" /></td></tr>
			<tr><th>".RES_ICON_CRYSTAL."".RES_CRYSTAL."</th>
			<td><input type=\"text\" name=\"fetch2\" id=\"res2\" value=\"0\" size=\"8\" tabindex=\"".($tabindex++)."\" /></td></tr>
			<tr><th>".RES_ICON_PLASTIC."".RES_PLASTIC."</th>
			<td><input type=\"text\" name=\"fetch3\" id=\"res3\" value=\"0\" size=\"8\" tabindex=\"".($tabindex++)."\" /></td></tr>
			<tr><th>".RES_ICON_FUEL."".RES_FUEL."</th>
			<td><input type=\"text\" name=\"fetch4\" id=\"res4\" value=\"0\" size=\"8\" tabindex=\"".($tabindex++)."\"/></td></tr>
			<tr><th>".RES_ICON_FOOD."".RES_FOOD."</th>
			<td><input type=\"text\" name=\"fetch5\" id=\"res5\" value=\"0\" size=\"8\" tabindex=\"".($tabindex++)."\"  /></td></tr>
			<tr><th>".RES_ICON_PEOPLE."Passagiere</th>
			<td><input type=\"text\" name=\"fetchp\" id=\"resp\" value=\"0\" size=\"8\" tabindex=\"".($tabindex++)."\"/></td></tr>					
			</table>
			<br style=\"clear:both\" />";                                                                                   
			echo "<br/>Wichtug: Die Flotte wird nur starten, falls die Schiffe und das Ziel die gewählte Aktion unterstützen.
			<br/><br/><input type=\"submit\" value=\"Speichern\" name=\"submit_new\" />";
			infobox_end();
			echo "</form>";

			$res = dbquery("
			SELECT
	      *
			FROM
				fleet_bookmarks
			ORDER BY
			 name;");
			if (mysql_num_rows($res)>0)
			{
				infobox_start("Gespeicherte Favoriten",1);
				echo "<tr>
								<th class=\"tbltitle\">Name</th>
								<th class=\"tbltitle\" colspan=\"2\">Ziel</th>
								<th class=\"tbltitle\">Aktion</th>
								<th class=\"tbltitle\">Schiffe</th>
								<th class=\"tbltitle\">Aktionen</th>
							</tr>";
				while ($arr=mysql_fetch_assoc($res))
				{
					$ent = Entity::createFactoryById($arr['target_id']);
					$ac = FleetAction::createFactory($arr['action']);
				
					$sidarr = explode(",",$arr['ships']);
					
					echo "<tr>
						<td class=\"tbldata\">".text2html($arr['name'])."</td>
						<td class=\"tbldata\" style=\"width:40px;background:#000\"><img src=\"".$ent->imagePath()."\" /></td>
						<td class=\"tbldata\">".$ent."<br/>".$ent->entityCodeString()."</td>
						<td class=\"tbldata\">".$ac."</td>
						<td class=\"tbldata\">";
						foreach ($sidarr as $sd)
						{
							$sdi = explode(":",$sd);
							echo $sdi[1]." ".$ships[$sdi[0]];
							echo ", ";
						}
						echo "</td>
						<td class=\"tbldata\">
							<a href=\"javascript:;\" onclick=\"\">Starten</a> 
							<a href=\"?page=$page&amp;mode=$mode&amp;edit=".$arr['id']."\">Bearbeiten</a> 
							<a href=\"?page=$page&amp;mode=$mode&amp;del=".$arr['id']."\" onclick=\"return confirm('Soll dieser Favorit wirklich gel&ouml;scht werden?');\">Entfernen</a>
						</td>
					</tr>";
				}
				infobox_end(1);
			}
			else
			{
				echo "<i>Noch keine Favoriten vorhanden!</i>";
			}			
		
		
	}
	else
	{
	
		// Bearbeiten
		if (isset($_GET['edit']) && $_GET['edit']>0)
		{
			echo "<form action=\"?page=$page\" method=\"post\">";
			checker_init();
			$res=dbquery("
			SELECT
	      bookmarks.comment,
	      bookmarks.entity_id,
	      entities.code      
			FROM
	      bookmarks
			INNER JOIN
				entities	
				ON bookmarks.entity_id=entities.id
				AND bookmarks.id='".$_GET['edit']."'
				AND bookmarks.user_id=".$cu->id().";");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_assoc($res);
				$ent = Entity::createFactory($arr['code'],$arr['entity_id']);
				
				infobox_start("Favorit bearbeiten",1);
				echo "<tr>
								<th class=\"tbltitle\">Koordinaten</th>
								<td class=\"tbldata\">".$ent->entityCodeString()." - ".$ent."</td>
							</tr>
							<tr>
								<th class=\"tbltitle\">Kommentar</th>
								<td class=\"tbldata\">
									<textarea name=\"bookmark_comment\" rows=\"3\" cols=\"60\">".stripslashes($arr['comment'])."</textarea>
								</td>
							</tr>";
				infobox_end(1);
				
				echo "<input type=\"hidden\" name=\"bookmark_id\" value=\"".$_GET['edit']."\" />";
				echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_edit_target\" /> &nbsp; ";
			}
			else
			{
				echo "<b>Fehler:</b> Datensatz nicht gefunden!<br/><br/>";
			}
			echo " <input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
			echo "</form>";
		}
		else
		{
			// Bearbeiteter Favorit speichern
			if (isset($_POST['submit_edit_target']) && $_POST['submit_edit_target'] && checker_verify())
			{
				dbquery("
				UPDATE 
					bookmarks
				SET 
					comment='".addslashes($_POST['bookmark_comment'])."' 
				WHERE 
					id='".$_POST['bookmark_id']."' 
					AND user_id='".$cu->id()."';");
				if (mysql_affected_rows()>0)
					ok_msg("Gespeichert");
			}
	
			// Favorit löschen
			if (isset($_GET['del']) && $_GET['del']>0)
			{
				dbquery("
				DELETE FROM 
					bookmarks
				WHERE 
					id='".$_GET['del']."' 
					AND user_id='".$cu->id()."';");
				if (mysql_affected_rows()>0)
					ok_msg("Gelöscht");
			}
	
			// Neuer Favorit speichern
			if (isset($_POST['submit_target']) && $_POST['submit_target']!="" && checker_verify())
			{
				$res=dbquery("
				SELECT
					entities.id
				FROM
					entities
				INNER JOIN
					cells
				ON entities.cell_id=cells.id
					AND sx='".$_POST['sx']."'
	        AND sy='".$_POST['sy']."'
	        AND cx='".$_POST['cx']."'
	        AND cy='".$_POST['cy']."'
	        AND pos='".$_POST['pos']."';");
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_row($res);
					$check_res = dbquery("
					SELECT 
						id 
					FROM 
						bookmarks
					WHERE 
						entity_id='".$arr[0]."' 
						AND user_id='".$cu->id()."';");
					if (mysql_num_rows($check_res)==0)
					{
						dbquery("
						INSERT INTO 
							bookmarks
						(
							user_id,
							entity_id,
							comment) 
						VALUES 
							('".$cu->id()."',
							'".$arr[0]."',
							'".addslashes($_POST['bookmark_comment'])."');");
								
						echo "Der Favorit wurde hinzugef&uuml;gt!<br/><br/>";
					}
					else
					{
						echo "<b>Fehler:</b> Dieser Favorit existiert schon!<br/><br/>";
					}
				}
				else
				{
					echo "<b>Fehler:</b> Es existiert kein Objekt an den angegebenen Koordinaten!!<br/><br/>";
				}
			}
	
			// Neuer Favorit speichern (id gegeben
			if (isset($_GET['add']) && $_GET['add']>0)
			{
				$res=dbquery("
				SELECT
					entities.id
				FROM
					entities
				WHERE
					id=".$_GET['add'].";");
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_row($res);
					$check_res = dbquery("
					SELECT 
						id 
					FROM 
						bookmarks
					WHERE 
						entity_id='".$arr[0]."' 
						AND user_id='".$cu->id()."';");
					if (mysql_num_rows($check_res)==0)
					{
						dbquery("
						INSERT INTO 
							bookmarks
						(
							user_id,
							entity_id,
							comment) 
						VALUES 
							('".$cu->id()."',
							'".$arr[0]."',
							'-');");
								
						echo "Der Favorit wurde hinzugef&uuml;gt!<br/><br/>";
					}
					else
					{
						echo "<b>Fehler:</b> Dieser Favorit existiert schon!<br/><br/>";
					}
				}
				else
				{
					echo "<b>Fehler:</b> Es existiert kein Objekt an den angegebenen Koordinaten!!<br/><br/>";
				}
			}
	
	
			// Add-Bookmakr-Box
			infobox_start("Favorit hinzuf&uuml;gen");
			echo "<form action=\"?page=$page\" method=\"post\">";
			checker_init();
			echo "<select name=\"sx\">";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
			{
				echo "<option value=\"$x\">$x</option>";
			}
			echo "</select> / <select name=\"sy\">";
			for ($y=1;$y<=$conf['num_of_sectors']['p2'];$y++)
			{
				echo "<option value=\"$y\">$y</option>";
			}
			echo "</select> : <select name=\"cx\">";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
			{
				echo "<option value=\"$x\">$x</option>";
			}
			echo "</select> / <select name=\"cy\">";
			for ($y=1;$y<=$conf['num_of_cells']['p2'];$y++)
			{
				echo "<option value=\"$y\">$y</option>";
			}
			echo "</select> : <select name=\"pos\">";
			for ($y=0;$y<=$conf['num_planets']['p2'];$y++)
			{
				echo "<option value=\"$y\">$y</option>";
			}
			echo "</select> &nbsp; ";
			echo "<input type=\"text\" name=\"bookmark_comment\" size=\"20\" maxlen=\"200\" value=\"Kommentar\" onfocus=\"if (this.value=='Kommentar') this.value=''\" /> &nbsp;";
			echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_target\" />";
			infobox_end();
	
			// List bookmarks
			$res = dbquery("
			SELECT
	      bookmarks.id,
	      bookmarks.comment,
	      bookmarks.entity_id,
	      entities.code
			FROM
				bookmarks
			INNER JOIN
				entities	
				ON bookmarks.user_id=".$cu->id()."
				AND bookmarks.entity_id=entities.id
			ORDER BY
			 	bookmarks.comment;");
			if (mysql_num_rows($res)>0)
			{
				infobox_start("Gespeicherte Favoriten",1);
				echo "<tr>
								<th class=\"tbltitle\" colspan=\"2\">Typ</th>
								<th class=\"tbltitle\">Koordinaten</th>
								<th class=\"tbltitle\">Besitzer</th>
								<th class=\"tbltitle\">Kommentar</th>
								<th class=\"tbltitle\">Aktionen</th>
							</tr>";
				while ($arr=mysql_fetch_assoc($res))
				{
					$ent = Entity::createFactory($arr['code'],$arr['entity_id']);
				
					echo "<tr>
										<td class=\"tbldata\" style=\"width:40px;background:#000\"><img src=\"".$ent->imagePath()."\" /></td>
										<td class=\"tbldata\">".$ent->entityCodeString()."</td>
										<td class=\"tbldata\">".$ent."</td>
										<td class=\"tbldata\">".$ent->owner()."</td>
										<td class=\"tbldata\">".text2html($arr['comment'])."</td>
										<td class=\"tbldata\">
											<a href=\"?page=haven&amp;target=".$ent->id()."\">Flotte</a> 
											<a href=\"?page=entity&amp;id=".$ent->id()."&amp;hl=".$ent->id()."\">Infos</a> 
											<a href=\"?page=cell&amp;id=".$ent->cellId()."&amp;hl=".$ent->id()."\">System</a> 
											<a href=\"?page=$page&amp;edit=".$arr['id']."\">Bearbeiten</a> 
											<a href=\"?page=$page&amp;del=".$arr['id']."\" onclick=\"return confirm('Soll dieser Favorit wirklich gel&ouml;scht werden?');\">Entfernen</a>
									</td>
							</tr>";
				}
				infobox_end(1);
			}
			else
			{
				echo "<i>Noch keine Bookmarks vorhanden!</i>";
			}
		}
	}
?>
