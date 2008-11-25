	<script type="text/javascript">
	function toggleText(elemId,switchId)
	{
		if (document.getElementById(switchId).innerHTML=="Anzeigen")
		{
			document.getElementById(elemId).style.display='';	
			document.getElementById(switchId).innerHTML="Verbergen";
		}
		else
		{
			document.getElementById(elemId).style.display='none';					
			document.getElementById(switchId).innerHTML="Anzeigen";
		}		
	}	
	</script>
	

<?PHP
	$status = array("Neu","Zugeteilt","Abgeschlossen","Gelöscht");
	$abuse_colors = array("#f90","#ff0","#0f0","#bbb");
	$ext = true;
	if (isset($_GET['ext']) && $_GET['ext']==1)
		$ext = false;
	
	echo "<h1>Ticketsystem</h1>";
	
	if (isset($_POST['abuse_submit']) && checker_verify())
	{
		dbquery("
		INSERT INTO
			tickets
		(
			cat_id,
			user_id,
			c_user_id,
			c_alliance_id,
			timestamp,
			text		
		)
		VALUES
		(
			'".$_POST['abuse_cat']."',
			'".$cu->id."',
			'".$_POST['abuse_c_user_id']."',
			'".$_POST['abuse_c_alliance_id']."',
			UNIX_TIMESTAMP(),
			'".addslashes($_POST['abuse_text'])."'	
		);");
		$tid = mysql_insert_id();
	
		$tres = dbquery("
		SELECT
			name
		FROM
			ticket_cat
		WHERE
			id=".$_POST['abuse_cat']."
		");
		$tarr = mysql_fetch_row($tres);
		
		$res = dbquery("
			SELECT 
				user_id,
				user_nick,
				user_email,
				group_name,
				user_board_url
			FROM 
				admin_users
			INNER JOIN
				admin_groups
				ON user_admin_rank=group_id
				AND group_level<3
		;");
		if (mysql_num_rows($res)>0)
		{
			while ($arr = mysql_fetch_array($res))
			{
				$text = "Ticket #".$tid." ".GAMEROUND_NAME."\n----------------------\n\n";
				$text.= "Nick: ".$cu->nick."\n";
				$text.= "ID: ".$cu->id."\n";
				$text.= "IP/Host: ".$_SERVER['REMOTE_ADDR']." (".resolveIp($_SERVER['REMOTE_ADDR']).")\n";
				$text.= "\n\n".$tarr[0]."\n\n";
				$text.= $_POST['abuse_text'];
				
	      $email_header = "From: Escape to Andromeda Ticketsystem ".GAMEROUND_NAME."<etoa@dev.etoa.ch>\n";
	      $email_header .= "Reply-To: ".$cu->nick."<".$cu->email().">\n";
	      $email_header .= "X-Mailer: PHP/" . phpversion(). "\n";
	      $email_header .= "X-Sender-IP: ".$_SERVER['REMOTE_ADDR']."\n";
	      $email_header .= "Content-Style-Type: text/css\n";					
				mail($arr['user_email'],"Neues Nicket #".$tid." (".GAMEROUND_NAME."): ".$tarr[0],$text,$email_header);
				
			}	
		}
	

	
	
		echo "Vielen Dank, dein Text wurde gespeichert.<br/>Ein Game-Administrator wird sich dem Problem annehmen.<br/><br/>";
		if (!$ext)
			echo "<input type=\"button\" onclick=\"document.location='?page=help'\" value=\"Weiter\" />";
	}
	else
	{
		echo "Über unser Benachrichtigungssystem kanns du einen Game-Administrator informieren, falls
		du ein Problem mit dem Spiel hast oder einen Missbrauch der Spielregeln festgestellt hast. Bitte fülle folgendes Formular aus
		um dein Anliegen zu beschrieben; je mehr Infos du uns gibts, desto besser können wir dir helfen:<br/><br/>";
		echo "<form action=\"?page=$page\" method=\"post\">";
		checker_init();
		echo "<table class=\"tb\">
		<tr>
			<th>Kategorie:</th>
			<td><select name=\"abuse_cat\">";
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
			<td><textarea name=\"abuse_text\" id=\"abuse_text\" rows=\"10\" cols=\"60\"></textarea></td>
		</tr>
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
			echo "</select></td>
		</tr>
		</table><br/>
		 &nbsp; (* z.B. bei Regelverstössen)<br/><br/>
		<input type=\"submit\" name=\"abuse_submit\" value=\"Einsenden\" /><br/><br/>";
		echo "</form>";
		echo "<script type=\"text/javascript\">document.getElementById('abuse_text').focus()</script>";
		
		if ($ext)
		{
		echo "<h2>Gemeldete Tickets</h2>";
		$res = dbquery("
		SELECT		
			a.user_nick as anick,
			a.user_id as aid,
			t.timestamp,
			c.name as cname,
			t.id,
			t.admin_timestamp,
			t.text,
			t.notice,
			t.status		
		FROM
			tickets as t
		INNER JOIN
			ticket_cat as c
			ON t.cat_id=c.id
		LEFT JOIN
			admin_users as a
		ON
			t.admin_id=a.user_id
		WHERE
			t.user_id=".$cu->id."	
		ORDER BY
			t.timestamp DESC
		;");
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\">
			<tr>
				<th>ID</th>
				<th>Kategorie</th>
				<th>Eingesendet</th>
				<th>Status</th>				
				<th>Admin</th>
				<th>Bearbeitet</th>
				<th>Optionen</th>
			</tr>";
			while($arr=mysql_fetch_array($res))
			{
				echo "<tr>
				<td>".$arr['id']."</td>
				<td>".$arr['cname']."</td>
				<td>".df($arr['timestamp'])."</td>
				<td style=\"color:".$abuse_colors[$arr['status']]."\">".$status[$arr['status']]."</td>
				<td><a href=\"?page=contact&rcpt=".$arr['aid']."\">".$arr['anick']."</a></td>
				<td>".($arr['admin_timestamp'] > 0 ? df($arr['admin_timestamp']) : "-")."</td>
				<td>
					[<a href=\"javascript:;\" onclick=\"toggleText('tx_".$arr['id']."','sw_".$arr['id']."')\" id=\"sw_".$arr['id']."\">Anzeigen</a>]
				</td>
				</tr>
				<tr id=\"tx_".$arr['id']."\" ";
				if (!isset($_GET['id']) || (isset($_GET['id']) && $_GET['id']!=$arr['id']))
				{
					echo "style=\"display:none;\"";
				}
				echo ">
					<td colspan=\"7\">
					<b>Meldung:</b><br/>
					".text2html($arr['text'])."<br/><br/>
					<b>Antwort:</b><br/>";
					if ($arr['notice']!="")
						echo text2html($arr['notice']);
					else
						echo "<i>Noch keine vorhanden</i>";
					echo "</td>
				</tr>";			
			}
			echo "</table>";
		}
		else
		{
			echo "<i>Keine vorhanden!</i>";
		}
		}		
	}
	
	
	
?>