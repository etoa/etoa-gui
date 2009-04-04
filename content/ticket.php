<?PHP
	$ext = true;

	echo "<h1>Ticketsystem</h1>";

if (isset($_GET['id']) && $_GET['id']>0)
{
	echo "<h2>Ticket-Details</h2>";
	$tarr = Ticket::find(array("user_id"=>$s['user_id'],"id"=>intval($_GET['id'])));
	if (count($tarr) > 0)
	{
		$ti = array_shift($tarr);
		if (isset($_POST['submit_new_post']))
		{
			if ($ti->addMessage(array("user_id"=>$s['user_id'],"message"=>$_POST['message'])))
			{
				ok_msg("Nachricht hinzugefügt!");
			}
		}
		if (isset($_GET['reopen']))
		{
			$ti->reopen();
		}

		tableStart("Ticket ".$ti->idString);
		echo '<tr><th>Kategorie:</th><td colspan="3">';
		echo $ti->catName;
		echo '</td></tr>';
		echo '<tr><th>User:</th><td>';
		echo ''.$ti->userNick.'';
		echo '</td></tr>';
		if ($ti->adminId > 0)
		{
			echo '<tr><th>Zugeteilter Admin:</th><td>';
			echo $ti->adminNick;
			echo '</td></tr>';
		}
		echo '<tr><th>Status:</th><td colspan="3">';
		echo $ti->statusName;
		echo '</td></tr>';
		tableEnd();

		tableStart("Nachrichten");
		echo "<tr><th style=\"width:120px;\">Datum</th><th style=\"width:150px;\">Autor</th><th>Nachricht</th></tr>";
		foreach ($ti->getMessages() as $mi)
		{
			echo "<tr>
			<td>".df($mi->timestamp)."</td>
			<td>".$mi->authorNick."</td>
			<td>".text2html($mi->message)."</td>
			</tr>";
		}
		tableEnd();

		if ($ti->status!="closed")
		{
			echo '<form action="?page='.$page.'&amp;id='.$_GET['id'].'" method="post">';
			tableStart("Neue Nachricht");
			echo '<tr><th>Absender:</th><td>';
			echo $s['user_nick']."";
			echo '</td></tr>';
			echo '<tr><th>Nachricht:</th><td>';
			echo '<textarea name="message" rows="8" cols="60"></textarea>';
			echo '</td></tr>';
			tableEnd();
			echo '<input type="submit" name="submit_new_post" value="Senden" /> &nbsp;
			'.button("Zur Übersicht","?page=$page").' &nbsp;	';

			echo "</form><br/>";
		}
		else
		{
			echo '<p>'.button("Zur Übersicht","?page=$page").' &nbsp;
			'.button("Ticket wiedereröffnen","?page=$page&amp;id=".$ti->id."&amp;reopen=1").'
			</p>';
		}
	}
	else
	{
		err_msg("Ticket nicht vorhanden!");
	}


}
else
{


	if (isset($_POST['abuse_submit']) && checker_verify())
	{
		Ticket::create(array_merge($_POST,array("user_id"=>$s['user_id'])));
		echo "<br/>Vielen Dank, dein Text wurde gespeichert.<br/>Ein Game-Administrator wird sich dem Problem annehmen.<br/><br/>";
		if ($ext)
			echo "<input type=\"button\" onclick=\"document.location='?page=ticket'\" value=\"Weiter\" />";
	}
	else
	{
		echo "Über unser Benachrichtigungssystem kanns du einen Game-Administrator informieren, falls
		du ein Problem mit dem Spiel hast oder einen Missbrauch der Spielregeln festgestellt hast. Bitte fülle folgendes Formular aus
		um dein Anliegen zu beschrieben; je mehr Infos du uns gibts, desto besser können wir dir helfen:<br/><br/>";
		echo "<form action=\"?page=$page\" method=\"post\">";
		checker_init();
		tableStart("Neues Ticket",700);
		echo "<tr>
			<th>Kategorie:</th>
			<td><select name=\"cat_id\">";
			$cres = dbquery("
			SELECT
				id,
				name
			FROM
				ticket_cat
			ORDER By
				sort			
			");			
			while ($carr = mysql_fetch_row($cres))
			{
				echo "<option value=\"".$carr[0]."\"";
				if (isset($_GET['cat']) && $_GET['cat']==$carr[0]) echo " selected=\"selected\"";
				echo ">".$carr[1]."</option>";			
			}
			echo "</select></td>
		</tr>
		<tr>
			<th>Beschreibung:</th>
			<td><textarea name=\"message\" id=\"abuse_text\" rows=\"10\" cols=\"60\"></textarea></td>
		</tr>";
	/*
		<tr>
			<th>Betreffenden Spieler * </th>
			<td><select name=\"abuse_c_user_id\">
			<option value=\"0\">-</option>";
			$res = dbquery("
			SELECT 
				user_nick,
				user_id
			FROM
				users
			ORDER BY
				user_nick;");
			while($arr=mysql_fetch_row($res))
			{
				echo "<option value=\"".$arr[1]."\"";
				if (isset($_GET['uid']) && $_GET['uid']==$arr[1]) echo " selected=\"selected\"";
				echo ">".$arr[0]."</option>";			
			}
			echo "</select></td>
		</tr>
		<tr>
			<th>Betreffende Allianz * </th>
			<td><select name=\"abuse_c_alliance_id\">
			<option value=\"0\">-</option>";
			$res = dbquery("
			SELECT 
				alliance_name,
				alliance_tag,
				alliance_id
			FROM
				alliances
			ORDER BY
				alliance_tag;");
			while($arr=mysql_fetch_row($res))
			{
				echo "<option value=\"".$arr[2]."\"";
				if (isset($_GET['aid']) && $_GET['aid']==$arr[2]) echo " selected=\"selected\"";
				echo ">[".$arr[1]."] ".$arr[0]."</option>";			
			}
			echo "</select> (* z.B. bei Regelverstössen angeben)</td>
		</tr>*/
		echo "</table><br/>
		
		<input type=\"submit\" name=\"abuse_submit\" value=\"Einsenden\" /><br/><br/>";
		echo "</form>";
		echo "<script type=\"text/javascript\">document.getElementById('abuse_text').focus()</script>";
		
		if ($ext)
		{
		
		$tickets = Ticket::find(array('user_id'=>$s['user_id']));
		
		if (count($tickets)>0)
		{
			tableStart("Vorhandene Tickets",700);
			echo "<tr>
				<th>ID</th>
				<th>Kategorie</th>
				<th>Status</th>				
				<th>Admin</th>
				<th>Aktualisiert</th>
				<th>Optionen</th>
			</tr>";
			foreach($tickets as $tid => &$ti)
			{
				echo "<tr>
				<td>".$ti->idString."</td>
				<td>".$ti->catName."</td>
				<td>".$ti->statusName."</td>
				<td><a href=\"?page=contact&rcpt=".$ti->adminId."\">".$ti->adminNick."</a></td>
				<td>".df($ti->time)."</td>
				<td>
					<a href=\"?page=$page&amp;id=".$tid."\">Anzeigen</a>
				</td>
				</tr>";
			}
			echo "</table>";
		}
		}		
	}
	
}
	
?>