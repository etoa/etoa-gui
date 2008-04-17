<?PHP
	echo "<h1>Tickets</h1>";
	
	
	if ($_GET['view']>0)
	{
		$res = dbquery("
		SELECT
			u.user_nick as unick,
			u.user_id as uid,
			abuse_timestamp,
			abuse_cat,
			abuse_id,
			abuse_text,
			abuse_status,
			abuse_admin_id,
			cu.user_nick as cunick,
			cu.user_id as cuid,
			alliance_tag,
			alliance_name,
			alliance_id,
			abuse_solution,
			abuse_notice								
		FROM
			abuses
		LEFT JOIN
			users as u
		ON
			abuse_user_id=u.user_id
		LEFT JOIN
			users as cu
		ON
			abuse_c_user_id=cu.user_id
		LEFT JOIN
			alliances
		ON
			abuse_c_alliance_id=alliance_id
		WHERE
			abuse_id=".$_GET['view']."
		;");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			echo "<h2>Details Ticket #".$arr['abuse_id']."</h2>";
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "<input type=\"hidden\" name=\"user_id\" value=\"".$arr['uid']."\" />";
			echo "<table class=\"tb\">
			<tr>
				<th>Spieler</th>
				<td>".$arr['unick']." 
					[<a href=\"?page=user&sub=edit&amp;user_id=".$arr['uid']."\">Daten</a>]
					[<a href=\"?page=messages&sub=sendmsg&user_id=".$arr['uid']."\">Nachricht</a>]
				</td>
			</tr>
			<tr>
				<th>Kategorie</th>
				<td>".$abuse_cats[$arr['abuse_cat']]."</td>
			</tr>
			<tr>
				<th>Zeit</th>
				<td>".df($arr['abuse_timestamp'])."</td>
			</tr>
			<tr>
				<th>Text</th>
				<td>".text2html($arr['abuse_text'])."</td>
			</tr>";
			if ($arr['cunick']!="")
			{
				echo "<tr>
				<th>Betreffenden Spieler</th>
				<td>".$arr['cunick']." 
					[<a href=\"?page=user&sub=edit&amp;user_id=".$arr['cuid']."\">Daten</a>]
					[<a href=\"?page=messages&sub=sendmsg&user_id=".$arr['cuid']."\">Nachricht</a>]
				</td>
				</tr>";				
			}
			if ($arr['alliance_id']>0)
			{
				echo "<tr>
				<th>Betreffende Allianz</th>
				<td>[".$arr['alliance_tag']."] ".$arr['alliance_name']." 
					[<a href=\"?page=alliances&sub=edit&amp;alliance_id=".$arr['alliance_id']."\">Daten</a>]
				</td>
				</tr>";				
			}			
			echo "<tr>
				<th>Status</th>
				<td><select name=\"abuse_status\">";
				foreach ($abuse_status as $k => $v)
				{
					echo "<option value=\"".$k."\"";
					if ($arr['abuse_status']==$k) echo " selected=\"selected\"";
					echo ">".$v."</option>";
				}
				echo "</select></td>
			</tr>		
			<tr>
				<th>Zugewiesener Admin</th>
				<td><select name=\"abuse_admin_id\">
				<option value=\"0\">(keiner)</option>";
				$ares = dbquery("
				SELECT
					user_nick,
					user_id
				FROM
					admin_users
				ORDER BY
					user_nick				
				;");
				while ($aarr=mysql_fetch_row($ares))
				{
					echo "<option value=\"".$aarr[1]."\"";
					if ($arr['abuse_admin_id']==$aarr[1]) echo " selected=\"selected\"";
					echo ">".$aarr[0]."</option>";
				}
				echo "</select></td>
			</tr>
			<tr>
				<th>Lösung:</th>
				<td><textarea name=\"abuse_solution\" rows=\"5\" cols=\"70\">".stripslashes($arr['abuse_solution'])."</textarea></td>
			</tr>";
			if ($arr['abuse_status']==1 || $arr['abuse_status']==2)
			echo "<tr>
				<th>Nachricht an Spieler:</th>
				<td><textarea name=\"abuse_notice\" rows=\"5\" cols=\"70\">".stripslashes($arr['abuse_notice'])."</textarea></td>
			</tr>";			
			echo "</table><br/>";
			echo "<input type=\"hidden\" name=\"abuse_id\" value=\"".$arr['abuse_id']."\" />";
			if ($arr['abuse_status']==0)
			{
				echo "<input type=\"submit\" name=\"submit_chown\" value=\"Ticket mir zuweisen und User informieren\" /> &nbsp; ";
				echo "<input type=\"submit\" name=\"submit_delete\" value=\"Ticket löschen\" /> &nbsp; ";
			}
			if ($arr['abuse_status']==1)
			{
				echo "<input type=\"submit\" name=\"submit_finished\" value=\"Ticket als Bearbeitet kennzeichnen und User informieren\" /> &nbsp; ";
			}

			echo "<input type=\"submit\" name=\"submit_changes\" value=\"Übernehmen\" /> &nbsp; ";
			echo "<input type=\"button\" value=\"Übersicht\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" />";
			echo "</form>";
		}	
		else
		{
			echo "Fehler! Ticket nicht vorhanden!";
		}	
	}
	else
	{	
		if (isset($_POST['submit_changes']))
		{
			dbquery("
			UPDATE
				abuses
			SET
				abuse_admin_id=".$_POST['abuse_admin_id'].",
				abuse_status='".$_POST['abuse_status']."',
				abuse_admin_timestamp=UNIX_TIMESTAMP(),
				abuse_solution='".addslashes($_POST['abuse_solution'])."',
				abuse_notice='".addslashes($_POST['abuse_notice'])."'
			WHERE
				abuse_id=".$_POST['abuse_id']."
			;");	
			echo "Ticket geändert!<br/>";
		}
		if (isset($_POST['submit_chown']))
		{
			dbquery("
			UPDATE
				abuses
			SET
				abuse_admin_id=".$s['user_id'].",
				abuse_status=1,
				abuse_admin_timestamp=UNIX_TIMESTAMP(),
				abuse_solution='".addslashes($_POST['abuse_solution'])."'
			WHERE
				abuse_id=".$_POST['abuse_id']."
			;");	
			echo "Ticket geändert!<br/>";
			$text = "Hallo!\n\nEin Administrator hat dein Ticket erhalten und wird sich um das Problem kümmern ! Klicke [url ?page=abuse&id=".$_POST['abuse_id']."]hier[/url] um Informationen dazu anzuzeigen.";
			send_msg($_POST['user_id'],USER_MSG_CAT_ID,"Dein Ticket #".$_POST['abuse_id']."",$text);
		}	
		if (isset($_POST['submit_finished']))
		{
			dbquery("
			UPDATE
				abuses
			SET
				abuse_status=2,
				abuse_admin_timestamp=UNIX_TIMESTAMP(),
				abuse_solution='".addslashes($_POST['abuse_solution'])."',
				abuse_notice='".addslashes($_POST['abuse_notice'])."'
			WHERE
				abuse_id=".$_POST['abuse_id']."
			;");	
			$text = "Hallo!\n\nDein Ticket wurde bearbeitet! Klicke [url ?page=abuse&id=".$_POST['abuse_id']."]hier[/url] um Informationen dazu anzuzeigen.";
			send_msg($_POST['user_id'],USER_MSG_CAT_ID,"Dein Ticket #".$_POST['abuse_id']."",$text);
			
			echo "Ticket geändert!<br/>";
		}				
		if (isset($_POST['submit_delete']))
		{
			dbquery("
			UPDATE
				abuses
			SET
				abuse_admin_id=".$s['user_id'].",
				abuse_status=3,
				abuse_admin_timestamp=UNIX_TIMESTAMP(),
				abuse_solution='".addslashes($_POST['abuse_solution'])."',
				abuse_notice='".addslashes($_POST['abuse_notice'])."'
			WHERE
				abuse_id=".$_POST['abuse_id']."
			;");	
			echo "Ticket geändert!<br/>";
		}	
		if ($_GET['action']=="delall")
		{
			dbquery("
			DELETE FROM
				abuses
			WHERE
				abuse_status=3
			;");	
			echo "Tickets gelöscht!<br/>";
		}	
		
		echo "<h2>Unbearbeitete Tickets</h2>";
		$res = dbquery("
		SELECT		
			user_nick,
			user_id,
			abuse_timestamp,
			abuse_cat,
			abuse_id		
		FROM
			abuses
		LEFT JOIN
			users
		ON
			abuse_user_id=user_id
		WHERE
			abuse_status=0	
		ORDER BY
			abuse_timestamp
		;");
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\">
			<tr>
				<th>ID</th>
				<th>Spieler</th>
				<th>Kategorie</th>
				<th>Zeit</th>
				<th>Optionen</th>
			</tr>";
			while($arr=mysql_fetch_array($res))
			{
				echo "<tr>
				<td>#".$arr['abuse_id']."</td>
				<td>".$arr['user_nick']."</td>
				<td>".$abuse_cats[$arr['abuse_cat']]."</td>
				<td>".df($arr['abuse_timestamp'])."</td>
				<td>
					<a href=\"?page=$page&amp;sub=$sub&amp;view=".$arr['abuse_id']."\">Anzeigen</a>
				</td>
				</tr>";			
			}
			echo "</table>";
		}
		else
		{
			echo "<i>Keine vorhanden!</i>";
		}
	
		echo "<h2>Zugewiesene Tickets</h2>";
		$res = dbquery("
		SELECT		
			u.user_nick as unick,
			u.user_id as uid,
			a.user_nick as anick,
			abuse_timestamp,
			abuse_cat,
			abuse_id,
			abuse_admin_timestamp		
		FROM
			abuses
		LEFT JOIN
			users as u
		ON
			abuse_user_id=u.user_id
		LEFT JOIN
			admin_users as a
		ON
			abuse_admin_id=a.user_id
		WHERE
			abuse_status=1	
		ORDER BY
			abuse_timestamp DESC
		;");
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\">
			<tr>
				<th>ID</th>
				<th>Spieler</th>
				<th>Kategorie</th>
				<th>Eingesendet</th>
				<th>Admin</th>
				<th>Zugewiesen</th>
				<th>Optionen</th>
			</tr>";
			while($arr=mysql_fetch_array($res))
			{
				echo "<tr>
				<td>#".$arr['abuse_id']."</td>
				<td>".$arr['unick']."</td>
				<td>".$abuse_cats[$arr['abuse_cat']]."</td>
				<td>".df($arr['abuse_timestamp'])."</td>
				<td>".$arr['anick']."</td>
				<td>".df($arr['abuse_admin_timestamp'])."</td>
				<td>
					<a href=\"?page=$page&amp;sub=$sub&amp;view=".$arr['abuse_id']."\">Anzeigen</a>
				</td>
				</tr>";			
			}
			echo "</table>";
		}
		else
		{
			echo "<i>Keine vorhanden!</i>";
		}

	
		echo "<h2>Bearbeitete Tickets</h2>";
		$res = dbquery("
		SELECT		
			u.user_nick as unick,
			u.user_id as uid,
			a.user_nick as anick,
			abuse_timestamp,
			abuse_cat,
			abuse_id,
			abuse_admin_timestamp		
		FROM
			abuses
		LEFT JOIN
			users as u
		ON
			abuse_user_id=u.user_id
		LEFT JOIN
			admin_users as a
		ON
			abuse_admin_id=a.user_id
		WHERE
			abuse_status=2	
		ORDER BY
			abuse_timestamp
		;");
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\">
			<tr>
				<th>ID</th>
				<th>Spieler</th>
				<th>Kategorie</th>
				<th>Eingesendet</th>
				<th>Admin</th>
				<th>Zugewiesen</th>
				<th>Optionen</th>
			</tr>";
			while($arr=mysql_fetch_array($res))
			{
				echo "<tr>
				<td>#".$arr['abuse_id']."</td>
				<td>".$arr['unick']."</td>
				<td>".$abuse_cats[$arr['abuse_cat']]."</td>
				<td>".df($arr['abuse_timestamp'])."</td>
				<td>".$arr['anick']."</td>
				<td>".df($arr['abuse_admin_timestamp'])."</td>
				<td>
					<a href=\"?page=$page&amp;sub=$sub&amp;view=".$arr['abuse_id']."\">Anzeigen</a>
				</td>
				</tr>";			
			}
			echo "</table>";
		}
		else
		{
			echo "<i>Keine vorhanden!</i>";
		}
	
	
		echo "<h2>Gelöschte Tickets</h2>";
		$res = dbquery("
		SELECT		
			u.user_nick as unick,
			u.user_id as uid,
			a.user_nick as anick,
			abuse_timestamp,
			abuse_cat,
			abuse_id,
			abuse_admin_timestamp		
		FROM
			abuses
		LEFT JOIN
			users as u
		ON
			abuse_user_id=u.user_id
		LEFT JOIN
			admin_users as a
		ON
			abuse_admin_id=a.user_id
		WHERE
			abuse_status=3	
		ORDER BY
			abuse_timestamp
		;");
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\">
			<tr>
				<th>ID</th>
				<th>Spieler</th>
				<th>Kategorie</th>
				<th>Eingesendet</th>
				<th>Admin</th>
				<th>Zugewiesen</th>
				<th>Optionen</th>
			</tr>";
			while($arr=mysql_fetch_array($res))
			{
				echo "<tr>
				<td>#".$arr['abuse_id']."</td>
				<td>".$arr['unick']."</td>
				<td>".$abuse_cats[$arr['abuse_cat']]."</td>
				<td>".df($arr['abuse_timestamp'])."</td>
				<td>".$arr['anick']."</td>
				<td>".df($arr['abuse_admin_timestamp'])."</td>
				<td>
					<a href=\"?page=$page&amp;sub=$sub&amp;view=".$arr['abuse_id']."\">Anzeigen</a>
				</td>
				</tr>";			
			}
			echo "</table>";
			echo "<br/><a href=\"?page=$page&amp;sub=$sub&amp;action=delall\" onclick=\"return confirm('Wirklich löschen?')\">Gelöschte endgültig löschen</a>";
		}
		else
		{
			echo "<i>Keine vorhanden!</i>";
		}


	}

?>