<?PHP

	echo "<h1>Favoriten f&uuml;r Raumschiffhafen</h1>";

	// Bearbeiten

	if ($_GET['edit']>0)
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
            ".$db_table['target_bookmarks'].",
            ".$db_table['space_cells']."
		WHERE
            target_bookmarks.bookmark_cell_id=space_cells.cell_id
            AND target_bookmarks.bookmark_user_id=".$_SESSION[ROUNDID]['user']['id']."
            AND target_bookmarks.bookmark_id='".$_GET['edit']."';");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
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
				$parr=mysql_fetch_array($pres);
			}
			infobox_start("Favorit bearbeiten",1);
			echo "<tr><th class=\"tbltitle\">Koordinaten</th><td class=\"tbldata\">".$arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy'];
			if ($arr['bookmark_planet_id']>0)
				echo " : ".$parr['planet_solsys_pos'];
			echo "</td></tr>";
			echo "<tr><th class=\"tbltitle\">Kommentar</th><td class=\"tbldata\"><input type=\"text\" name=\"bookmark_comment\" size=\"20\" maxlen=\"30\" value=\"".stripslashes($arr['bookmark_comment'])."\"  /></td></tr>";
			infobox_end(1);
			echo "<input type=\"hidden\" name=\"bookmark_id\" value=\"".$_GET['edit']."\" />";
			echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_edit_target\" /> &nbsp; ";
		}
		else
			echo "<b>Fehler:</b> Datensatz nicht gefunden!<br/><br/>";
		echo " <input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
		echo "</form>";
	}
	else
	{
		// Bearbeiteter Favorit speichern
		if ($_POST['submit_edit_target'] && checker_verify())
		{
			dbquery("UPDATE ".$db_table['target_bookmarks']." SET bookmark_comment='".addslashes($_POST['bookmark_comment'])."' WHERE bookmark_id='".$_POST['bookmark_id']."' AND bookmark_user_id=".$_SESSION[ROUNDID]['user']['id'].";");
		}

		// Favorit löschen
		if ($_GET['del']>0)
		{
			dbquery("DELETE FROM ".$db_table['target_bookmarks']." WHERE bookmark_id='".$_GET['del']."' AND bookmark_user_id=".$_SESSION[ROUNDID]['user']['id'].";");
		}

		// Neuer Favorit aus Planeten-ID speichern
		if ($_GET['add_planet_id']>0)
		{
			$pres=dbquery("
			SELECT
				planet_id,
				planet_solsys_id
			FROM
				".$db_table['planets']."
			WHERE
				planet_id='".$_GET['add_planet_id']."'
				AND planet_user_id!='".$_SESSION[ROUNDID]['user']['id']."';");
			if (mysql_num_rows($pres)>0)
			{
				$parr=mysql_fetch_array($pres);
				if (mysql_num_rows(dbquery("SELECT bookmark_id FROM ".$db_table['target_bookmarks']." WHERE bookmark_planet_id='".$parr['planet_id']."' AND bookmark_cell_id='".$parr['planet_solsys_id']."' AND bookmark_user_id='".$_SESSION[ROUNDID]['user']['id']."';"))==0)
				{
					dbquery("INSERT INTO ".$db_table['target_bookmarks']." (bookmark_user_id,bookmark_planet_id,bookmark_cell_id,bookmark_comment) VALUES ('".$_SESSION[ROUNDID]['user']['id']."','".$parr['planet_id']."','".$parr['planet_solsys_id']."',''); ");
					echo "Der Favorit wurde hinzugef&uuml;gt!<br/><br/>";
				}
				else
					echo "<b>Fehler:</b> Dieser Favorit existiert schon!<br/><br/>";
			}
		}

		// Neuer Favorit speichern
		if ($_POST['submit_target']!="" && checker_verify())
		{
			$res=dbquery("
			SELECT
				*
			FROM
				".$db_table['space_cells']."
			WHERE
                cell_sx='".$_POST['sx']."'
                AND cell_sy='".$_POST['sy']."'
                AND cell_cx='".$_POST['cx']."'
                AND cell_cy='".$_POST['cy']."';");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array($res);
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
								AND planet_solsys_pos='".$_POST['p']."'
								AND planet_user_id!='".$_SESSION[ROUNDID]['user']['id']."';");
							if (mysql_num_rows($pres)>0)
							{
								$parr=mysql_fetch_array($pres);
								if (mysql_num_rows(dbquery("SELECT bookmark_id FROM ".$db_table['target_bookmarks']." WHERE bookmark_planet_id='".$parr['planet_id']."' AND bookmark_cell_id='".$arr['cell_id']."' AND bookmark_user_id='".$_SESSION[ROUNDID]['user']['id']."';"))==0)
								{
									dbquery("INSERT INTO ".$db_table['target_bookmarks']." (bookmark_user_id,bookmark_planet_id,bookmark_cell_id,bookmark_comment) VALUES ('".$_SESSION[ROUNDID]['user']['id']."','".$parr['planet_id']."','".$arr['cell_id']."','".addslashes($_POST['bookmark_comment'])."'); ");
									echo "Der Favorit wurde hinzugef&uuml;gt!<br/><br/>";
								}
								else
									echo "<b>Fehler:</b> Dieser Favorit existiert schon!<br/><br/>";
							}
							else
								echo "<b>Fehler:</b> Der gew&auml;hlte Planet existiert nicht oder es ist ein eigener Planet. Eigene Planeten m&uuml;ssen nicht in die Bookmarks aufgenommen werden, sie sind im Hafen automatisch anw&auml;hlbar!<br/><br/>";
						}
						else
							echo "<b>Fehler:</b> Die gew&auml;hlte Zelle ist ein Sonnensystem, du musst eine Planetennummer angeben!<br/><br/>";
					}
					else
					{
						if (mysql_num_rows(dbquery("SELECT bookmark_id FROM ".$db_table['target_bookmarks']." WHERE bookmark_cell_id='".$arr['cell_id']."' AND bookmark_user_id='".$_SESSION[ROUNDID]['user']['id']."';"))==0)
						{
							dbquery("INSERT INTO ".$db_table['target_bookmarks']." (bookmark_user_id,bookmark_cell_id,bookmark_comment) VALUES ('".$_SESSION[ROUNDID]['user']['id']."','".$arr['cell_id']."','".addslashes($_POST['bookmark_comment'])."'); ");
							echo "Der Favorit wurde hinzugef&uuml;gt!<br/><br/>";
						}
						else
							echo "<b>Fehler:</b> Dieser Favorit existiert schon!<br/><br/>";
					}
				}
				else
					echo "<b>Fehler:</b> Leerer Raum kann nicht als Ziel gespeichert werden!<br/><br/>";
			}
			else
				echo "<b>Fehler:</b> Die entsprechende Zelle wurde nicht gefunden!<br/><br/>";
		}

		echo "<h2>Favorit hinzuf&uuml;gen</h2>";
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
		echo "<option value=\"0\">Zelle</option>";
		for ($y=1;$y<=$conf['num_planets']['p2'];$y++)
		{
			echo "<option value=\"$y\">$y</option>";
		}
		echo "</select> &nbsp; ";
		echo "<input type=\"text\" name=\"bookmark_comment\" size=\"20\" maxlen=\"30\" value=\"Kommentar\" onfocus=\"if (this.value=='Kommentar') this.value=''\" /> &nbsp;";
		echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_target\" />";
		echo "</form>";

		echo "<h2>Gespeicherte Favoriten</h2>";

		$res = dbquery("
		SELECT
			*
		FROM
			".$db_table['target_bookmarks'].",
			".$db_table['space_cells']."
		WHERE
		 	target_bookmarks.bookmark_cell_id=space_cells.cell_id
		 	AND target_bookmarks.bookmark_user_id=".$_SESSION[ROUNDID]['user']['id']."
		 GROUP BY
		 	target_bookmarks.bookmark_id
		 ORDER BY
		 	target_bookmarks.bookmark_comment,
            target_bookmarks.bookmark_cell_id,
            target_bookmarks.bookmark_planet_id;");
		if (mysql_num_rows($res)>0)
		{
			infobox_start("",1);
			echo "<tr><th class=\"tbltitle\">Typ</th><th class=\"tbltitle\">Koordinaten</th><th class=\"tbltitle\">Kommentar</th><th class=\"tbltitle\">Aktionen</th></tr>";
			while ($arr=mysql_fetch_array($res))
			{
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
					$parr=mysql_fetch_array($pres);
					echo "<tr><td class=\"tbldata\">Planet <b>".$parr['planet_name']."</b></td>";
				}
				elseif ($arr['cell_asteroid']>0)
					echo "<tr><td class=\"tbldata\">Asteroidenfeld</td>";
				elseif ($arr['cell_nebula']>0)
					echo "<tr><td class=\"tbldata\">Interstellarer Nebel</td>";
				elseif ($arr['cell_wormhole_id']>0)
					echo "<tr><td class=\"tbldata\">Wurmloch</td>";
				if ($arr['bookmark_planet_id']>0)
					echo "<td class=\"tbldata\"><a href=\"?page=solsys&amp;id=".$arr['bookmark_cell_id']."\">".$arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy']." : ".$parr['planet_solsys_pos'];
				else
					echo "<td class=\"tbldata\"><a href=\"?page=space&amp;sx=".$arr['cell_sx']."&amp;sy=".$arr['cell_sy']."\">".$arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy'];
				echo "</a></td>";
				echo "<td class=\"tbldata\">".text2html($arr['bookmark_comment'])."</td>";
				echo "<td class=\"tbldata\"><a href=\"?page=haven&amp;planet_to=".$arr['bookmark_planet_id']."&amp;cell_to_id=".$arr['bookmark_cell_id']."\">Flotte hinschicken</a> ";
				echo "<a href=\"?page=$page&amp;edit=".$arr['bookmark_id']."\">Bearbeiten</a> ";
				echo "<a href=\"?page=$page&amp;del=".$arr['bookmark_id']."\" onclick=\"return confirm('Soll dieser Favorit wirklich gel&ouml;scht werden?');\">Entfernen</a></td></tr>";
			}
			infobox_end(1);
		}
		else
			echo "<i>Noch keine Bookmarks vorhanden!</i>";
	}
?>
