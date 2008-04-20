<?PHP
	$abuse_status = array("Neu","Zugeteilt","Abgeschlossen","Gelöscht");
	$abuse_colors = array("#f90","#ff0","#0f0","#bbb");

	echo "<h1>Tickets</h1>";
	
	
	if (isset($_GET['view'])>0)
	{
		$res = dbquery("
		SELECT
			u.user_nick as unick,
			u.user_id as uid,
			t.timestamp,
			c.name as cname,
			t.id,
			t.text,
			t.status,
			t.admin_id,
			cu.user_nick as cunick,
			cu.user_id as cuid,
			alliance_tag,
			alliance_name,
			alliance_id,
			t.solution,
			t.notice								
		FROM
			tickets as t
		INNER JOIN
			ticket_cat as c
			ON c.id=t.cat_id			
		LEFT JOIN
			users as u
		ON
			t.user_id=u.user_id
		LEFT JOIN
			users as cu
		ON
			c_user_id=cu.user_id
		LEFT JOIN
			alliances
		ON
			c_alliance_id=alliance_id
		WHERE
			t.id=".$_GET['view']."
		;");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			echo "<h2>Details Ticket #".$arr['id']."</h2>";
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
				<td>".$arr['cname']."</td>
			</tr>
			<tr>
				<th>Zeit</th>
				<td>".df($arr['timestamp'])."</td>
			</tr>
			<tr>
				<th>Text</th>
				<td>".text2html($arr['text'])."</td>
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
					if ($arr['status']==$k) echo " selected=\"selected\"";
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
					if ($arr['admin_id']==$aarr[1]) echo " selected=\"selected\"";
					echo ">".$aarr[0]."</option>";
				}
				echo "</select></td>
			</tr>
			<tr>
				<th>Lösung:<br/><span style=\"font-size:8pt\">Admin-Interne Notiz</span></th>
				<td><textarea name=\"abuse_solution\" rows=\"5\" cols=\"70\">".stripslashes($arr['solution'])."</textarea></td>
			</tr>";
			if ($arr['status']==1 || $arr['status']==2)
			echo "<tr>
				<th>Nachricht an Spieler:<br/><span style=\"font-size:8pt\">Dies wird beim Spieler angezeigt</span></th>
				<td><textarea name=\"abuse_notice\" rows=\"5\" cols=\"70\">".stripslashes($arr['notice'])."</textarea></td>
			</tr>";			
			echo "</table><br/>";
			echo "<input type=\"hidden\" name=\"abuse_id\" value=\"".$arr['id']."\" />";
			if ($arr['status']==0)
			{
				echo "<input type=\"submit\" name=\"submit_chown\" value=\"Ticket mir zuweisen und User informieren\" /> &nbsp; ";
				echo "<input type=\"submit\" name=\"submit_delete\" value=\"Ticket löschen\" /> &nbsp; ";
			}
			if ($arr['status']==1)
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
				tickets
			SET
				admin_id=".$_POST['abuse_admin_id'].",
				status='".$_POST['abuse_status']."',
				admin_timestamp=UNIX_TIMESTAMP(),
				solution='".addslashes($_POST['abuse_solution'])."',
				notice='".addslashes($_POST['abuse_notice'])."'
			WHERE
				id=".$_POST['abuse_id']."
			;");	
			echo "Ticket geändert!<br/>";
		}
		if (isset($_POST['submit_chown']))
		{
			dbquery("
			UPDATE
				tickets
			SET
				admin_id=".$s['user_id'].",
				status=1,
				admin_timestamp=UNIX_TIMESTAMP(),
				solution='".addslashes($_POST['abuse_solution'])."'
			WHERE
				id=".$_POST['abuse_id']."
			;");	
			echo "Ticket geändert!<br/>";
			$text = "Hallo!\n\nEin Administrator hat dein Ticket erhalten und wird sich um das Problem kümmern ! Klicke [url ?page=ticket&id=".$_POST['abuse_id']."]hier[/url] um Informationen dazu anzuzeigen.";
			send_msg($_POST['user_id'],USER_MSG_CAT_ID,"Dein Ticket ".$_POST['abuse_id']."",$text);
		}	
		if (isset($_POST['submit_finished']))
		{
			dbquery("
			UPDATE
				tickets
			SET
				status=2,
				admin_timestamp=UNIX_TIMESTAMP(),
				solution='".addslashes($_POST['abuse_solution'])."',
				notice='".addslashes($_POST['abuse_notice'])."'
			WHERE
				id=".$_POST['abuse_id']."
			;");	
			$text = "Hallo!\n\nDein Ticket wurde bearbeitet! Klicke [url ?page=ticket&id=".$_POST['abuse_id']."]hier[/url] um Informationen dazu anzuzeigen.";
			send_msg($_POST['user_id'],USER_MSG_CAT_ID,"Dein Ticket #".$_POST['abuse_id']."",$text);
			
			echo "Ticket geändert!<br/>";
		}				
		if (isset($_POST['submit_delete']))
		{
			dbquery("
			UPDATE
				tickets
			SET
				admin_id=".$s['user_id'].",
				status=3,
				admin_timestamp=UNIX_TIMESTAMP(),
				solution='".addslashes($_POST['abuse_solution'])."',
				notice='".addslashes($_POST['abuse_notice'])."'
			WHERE
				id=".$_POST['abuse_id']."
			;");	
			echo "Ticket geändert!<br/>";
		}	
		if (isset($_GET['action']) && $_GET['action']=="delall")
		{
			dbquery("
			DELETE FROM
				tickets
			WHERE
				status=3
			;");	
			echo "Tickets gelöscht!<br/>";
		}	
		
		echo "<h2>Unbearbeitete Tickets</h2>";
		$res = dbquery("
		SELECT		
			u.user_nick,
			u.user_id,  
			c.name as cname,
			t.timestamp,
			t.id		
		FROM
			tickets as t
		INNER JOIN
			ticket_cat as c
			ON c.id=t.cat_id
		LEFT JOIN
			users as u
		ON
			t.user_id=u.user_id
		WHERE
			t.status=0	
		ORDER BY
			t.timestamp
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
				<td>".$arr['id']."</td>
				<td>".$arr['user_nick']."</td>
				<td>".$arr['cname']."</td>
				<td>".df($arr['timestamp'])."</td>
				<td>
					<a href=\"?page=$page&amp;sub=$sub&amp;view=".$arr['id']."\">Anzeigen</a>
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
			t.timestamp,
			c.name as cname,
			t.id,
			t.admin_timestamp		
		FROM
			tickets t
		INNER JOIN
			ticket_cat as c
			ON c.id=t.cat_id			
		LEFT JOIN
			users as u
		ON
			t.user_id=u.user_id
		LEFT JOIN
			admin_users as a
		ON
			t.admin_id=a.user_id
		WHERE
			t.status=1	
		ORDER BY
			t.timestamp DESC
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
				<td>#".$arr['id']."</td>
				<td>".$arr['unick']."</td>
				<td>".$arr['cname']."</td>
				<td>".df($arr['timestamp'])."</td>
				<td>".$arr['anick']."</td>
				<td>".df($arr['admin_timestamp'])."</td>
				<td>
					<a href=\"?page=$page&amp;sub=$sub&amp;view=".$arr['id']."\">Anzeigen</a>
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
			t.timestamp,
			c.name as cname,
			t.id,
			t.admin_timestamp		
		FROM
			tickets as t
		INNER JOIN
			ticket_cat as c
			ON c.id=t.cat_id						
		LEFT JOIN
			users as u
		ON
			t.user_id=u.user_id
		LEFT JOIN
			admin_users as a
		ON
			t.admin_id=a.user_id
		WHERE
			t.status=2	
		ORDER BY
			t.timestamp DESC
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
				<td>#".$arr['id']."</td>
				<td>".$arr['unick']."</td>
				<td>".$arr['cname']."</td>
				<td>".df($arr['timestamp'])."</td>
				<td>".$arr['anick']."</td>
				<td>".df($arr['admin_timestamp'])."</td>
				<td>
					<a href=\"?page=$page&amp;sub=$sub&amp;view=".$arr['id']."\">Anzeigen</a>
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
			t.timestamp,
			c.name as cname,
			t.id,
			t.admin_timestamp		
		FROM
			tickets as t
		INNER JOIN
			ticket_cat as c
			ON c.id=t.cat_id				
		LEFT JOIN
			users as u
		ON
			t.user_id=u.user_id
		LEFT JOIN
			admin_users as a
		ON
			t.admin_id=a.user_id
		WHERE
			t.status=3	
		ORDER BY
			t.timestamp DESC
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
				<td>#".$arr['id']."</td>
				<td>".$arr['unick']."</td>
				<td>".$arr['cname']."</td>
				<td>".df($arr['timestamp'])."</td>
				<td>".$arr['anick']."</td>
				<td>".df($arr['admin_timestamp'])."</td>
				<td>
					<a href=\"?page=$page&amp;sub=$sub&amp;view=".$arr['id']."\">Anzeigen</a>
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