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
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	echo "<h1>Favoriten f&uuml;r Raumschiffhafen</h1>";

	// Bearbeiten

	if (isset($_GET['edit']) && $_GET['edit']>0)
	{
		echo "<form action=\"?page=$page\" method=\"post\">";
		checker_init();
		$res=dbquery("
		SELECT
      target_bookmarks.bookmark_planet_id,
      target_bookmarks.bookmark_comment,
      space_cells.cell_sx,
      space_cells.cell_sy,
      space_cells.cell_cx,
      space_cells.cell_cy
		FROM
      ".$db_table['target_bookmarks']."
      INNER JOIN
      ".$db_table['space_cells']."
      ON target_bookmarks.bookmark_cell_id=space_cells.cell_id
      AND target_bookmarks.bookmark_user_id=".$s['user']['id']."
      AND target_bookmarks.bookmark_id='".$_GET['edit']."';");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_assoc($res);
			if ($arr['bookmark_planet_id']>0)
			{
				$pres=dbquery("
				SELECT
					planet_name,
					planet_solsys_pos
				FROM
					".$db_table['planets']."
				WHERE
					planet_id=".$arr['bookmark_planet_id'].";");
				$parr=mysql_fetch_assoc($pres);
			}
			
			infobox_start("Favorit bearbeiten",1);
			echo "<tr>
							<th class=\"tbltitle\">Koordinaten</th>
							<td class=\"tbldata\">
								".$arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy'];
								if ($arr['bookmark_planet_id']>0)
								{
									echo " : ".$parr['planet_solsys_pos'];
								}
				echo "</td>
						</tr>
						<tr>
							<th class=\"tbltitle\">Kommentar</th>
							<td class=\"tbldata\">
								<input type=\"text\" name=\"bookmark_comment\" size=\"20\" maxlen=\"30\" value=\"".stripslashes($arr['bookmark_comment'])."\" />
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
				".$db_table['target_bookmarks']." 
			SET 
				bookmark_comment='".addslashes($_POST['bookmark_comment'])."' 
			WHERE 
				bookmark_id='".$_POST['bookmark_id']."' 
				AND bookmark_user_id='".$s['user']['id']."';");
		}

		// Favorit löschen
		if (isset($_GET['del']) && $_GET['del']>0)
		{
			dbquery("
			DELETE FROM 
				".$db_table['target_bookmarks']." 
			WHERE 
				bookmark_id='".$_GET['del']."' 
				AND bookmark_user_id='".$s['user']['id']."';");
		}

		// Neuer Favorit aus Planeten-ID speichern
		if (isset($_GET['add_planet_id']) && $_GET['add_planet_id']>0)
		{
			$pres=dbquery("
			SELECT
				planet_id,
				planet_solsys_id
			FROM
				".$db_table['planets']."
			WHERE
				planet_id='".$_GET['add_planet_id']."'
				AND planet_user_id!='".$s['user']['id']."';");
			if (mysql_num_rows($pres)>0)
			{
				$parr=mysql_fetch_assoc($pres);
				
				$check_res = dbquery("
				SELECT 
					bookmark_id 
				FROM 
					".$db_table['target_bookmarks']." 
				WHERE 
					bookmark_planet_id='".$parr['planet_id']."' 
					AND bookmark_cell_id='".$parr['planet_solsys_id']."' 
					AND bookmark_user_id='".$s['user']['id']."';");
				
				
				if (mysql_num_rows($check_res)==0)
				{
					dbquery("
					INSERT INTO 
					".$db_table['target_bookmarks']." 
						(bookmark_user_id,
						bookmark_planet_id,
						bookmark_cell_id,
						bookmark_comment) 
					VALUES 
						('".$s['user']['id']."',
						'".$parr['planet_id']."',
						'".$parr['planet_solsys_id']."',
						'');");
					
					echo "Der Favorit wurde hinzugef&uuml;gt!<br/><br/>";
				}
				else
				{
					echo "<b>Fehler:</b> Dieser Favorit existiert schon!<br/><br/>";
				}
			}
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
				$arr=mysql_fetch_assoc($res);
				if ($arr['cell_solsys_num_planets']>0 || $arr['cell_asteroid']>0 || $arr['cell_nebula']>0 || $arr['cell_wormhole_id']>0)
				{
					if ($arr['cell_solsys_num_planets']>0)
					{
						if ($_POST['p']>0)
						{
							$pres=dbquery("
							SELECT
								planet_id
							FROM
								".$db_table['planets']."
							WHERE
								planet_solsys_id='".$arr['cell_id']."'
								AND planet_solsys_pos='".$_POST['p']."';");
							if (mysql_num_rows($pres)>0)
							{
								$parr=mysql_fetch_assoc($pres);
								
								$check_res = dbquery("
								SELECT 
									bookmark_id 
								FROM 
									".$db_table['target_bookmarks']." 
								WHERE 
									bookmark_planet_id='".$parr['planet_id']."' 
									AND bookmark_cell_id='".$arr['cell_id']."'
									AND bookmark_user_id='".$s['user']['id']."';");								
								
								
								if (mysql_num_rows($check_res)==0)
								{
									dbquery("
									INSERT INTO 
									".$db_table['target_bookmarks']." 
										(bookmark_user_id,
										bookmark_planet_id,
										bookmark_cell_id,
										bookmark_comment) 
									VALUES 
										('".$s['user']['id']."',
										'".$parr['planet_id']."',
										'".$arr['cell_id']."',
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
								echo "<b>Fehler:</b> Der gew&auml;hlte Planet existiert nicht oder es ist ein eigener Planet. Eigene Planeten m&uuml;ssen nicht in die Bookmarks aufgenommen werden, sie sind im Hafen automatisch anw&auml;hlbar!<br/><br/>";
							}
						}
						else
						{
							echo "<b>Fehler:</b> Die gew&auml;hlte Zelle ist ein Sonnensystem, du musst eine Planetennummer angeben!<br/><br/>";
						}
					}
					else
					{
						$check_res = dbquery("
						SELECT 
							bookmark_id 
						FROM 
							".$db_table['target_bookmarks']." 
						WHERE 
							bookmark_cell_id='".$arr['cell_id']."' 
							AND bookmark_user_id='".$s['user']['id']."';");
						
						if (mysql_num_rows($check_res)==0)
						{
							dbquery("
							INSERT INTO 
							".$db_table['target_bookmarks']." 
								(bookmark_user_id,
								bookmark_cell_id,
								bookmark_comment) 
							VALUES 
								('".$s['user']['id']."',
								'".$arr['cell_id']."',
								'".addslashes($_POST['bookmark_comment'])."');");
							
							echo "Der Favorit wurde hinzugef&uuml;gt!<br/><br/>";
						}
						else
						{
							echo "<b>Fehler:</b> Dieser Favorit existiert schon!<br/><br/>";
						}
					}
				}
				else
				{
					echo "<b>Fehler:</b> Leerer Raum kann nicht als Ziel gespeichert werden!<br/><br/>";
				}
			}
			else
			{
				echo "<b>Fehler:</b> Es existiert kein Objekt an den angegebenen Koordinaten!!<br/><br/>";
			}
		}

		echo "<h2>Favorit hinzuf&uuml;gen</h2><br>";
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
		echo "</select> : <select name=\"p\">";
		for ($y=0;$y<=$conf['num_planets']['p2'];$y++)
		{
			echo "<option value=\"$y\">$y</option>";
		}
		echo "</select> &nbsp; ";
		echo "<input type=\"text\" name=\"bookmark_comment\" size=\"20\" maxlen=\"30\" value=\"Kommentar\" onfocus=\"if (this.value=='Kommentar') this.value=''\" /> &nbsp;";
		echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_target\" />";
		echo "</form><br><br>";


		$res = dbquery("
		SELECT
      bookmarks.id,
      bookmarks.comment,
      bookmarks.entity_id,
      entities.type
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
							<th class=\"tbltitle\">Typ</th>
							<th class=\"tbltitle\">Koordinaten</th>
							<th class=\"tbltitle\">Besitzer</th>
							<th class=\"tbltitle\">Kommentar</th>
							<th class=\"tbltitle\">Aktionen</th>
						</tr>";
			while ($arr=mysql_fetch_assoc($res))
			{
				$ent = Entity::createFactory($arr['type'],$arr['entity_id']);
				
				/*
				if ($arr['bookmark_planet_id']>0)
				{
					$pres=dbquery("
					SELECT
						planet_name,
						planet_solsys_pos,
						planet_user_id
					FROM
						".$db_table['planets']."
					WHERE
						planet_id=".$arr['bookmark_planet_id'].";");
					$parr=mysql_fetch_assoc($pres);
					
					$typ = "Planet <b>".$parr['planet_name']."</b>";
					
					if($parr['planet_user_id']!=0)
					{
						$user_nick = get_user_nick($parr['planet_user_id']);
					}
				}
				elseif ($arr['cell_asteroid']>0)
				{
					$typ = "Asteroidenfeld";
				}
				elseif ($arr['cell_nebula']>0)
				{
					$typ = "Intergalaktischer Nebel";
				}
				elseif ($arr['cell_wormhole_id']>0)
				{
					$typ = "Wurmloch";
				}
				else
				{
					$typ = "Unbekannt!";
				}
				
				if ($arr['bookmark_planet_id']>0)
				{
					$koords = "<a href=\"?page=solsys&amp;id=".$arr['bookmark_cell_id']."\">".$arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy']." : ".$parr['planet_solsys_pos']."</a>";
				}
				else
				{
					$koords = "<a href=\"?page=space&amp;sx=".$arr['cell_sx']."&amp;sy=".$arr['cell_sy']."\">".$arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy']."</a>";
				}				
				*/
				
				
				echo "<tr>
									<td class=\"tbldata\">".$ent->type."</td>
									<td class=\"tbldata\">".$ent."</td>
									<td class=\"tbldata\">".$ent->owner."</td>
									<td class=\"tbldata\">".text2html($arr['comment'])."</td>
									<td class=\"tbldata\">
										<a href=\"?page=haven&amp;planet_to=".$arr['bookmark_planet_id']."&amp;cell_to_id=".$arr['bookmark_cell_id']."\">Flotte hinschicken</a> <a href=\"?page=$page&amp;edit=".$arr['bookmark_id']."\">Bearbeiten</a> <a href=\"?page=$page&amp;del=".$arr['bookmark_id']."\" onclick=\"return confirm('Soll dieser Favorit wirklich gel&ouml;scht werden?');\">Entfernen</a>
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
?>
