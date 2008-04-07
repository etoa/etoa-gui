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
		echo "</select> : <select name=\"pos\">";
		for ($y=0;$y<=$conf['num_planets']['p2'];$y++)
		{
			echo "<option value=\"$y\">$y</option>";
		}
		echo "</select> &nbsp; ";
		echo "<input type=\"text\" name=\"bookmark_comment\" size=\"20\" maxlen=\"200\" value=\"Kommentar\" onfocus=\"if (this.value=='Kommentar') this.value=''\" /> &nbsp;";
		echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_target\" />";
		echo "</form><br><br>";

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
										<a href=\"?page=cell&amp;id=".$ent->cellId()."\">System</a> 
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
?>
