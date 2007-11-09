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
	$ext = true;
	if ($_GET['ext']==1)
		$ext = false;
	
	echo "<h1>Missbrauch melden</h1>";
	
	if (isset($_POST['abuse_submit']) && checker_verify())
	{
		dbquery("
		INSERT INTO
			abuses
		(
			abuse_cat,
			abuse_user_id,
			abuse_c_user_id,
			abuse_c_alliance_id,
			abuse_timestamp,
			abuse_text		
		)
		VALUES
		(
			'".$_POST['abuse_cat']."',
			'".$s['user']['id']."',
			'".$_POST['abuse_c_user_id']."',
			'".$_POST['abuse_c_alliance_id']."',
			UNIX_TIMESTAMP(),
			'".addslashes($_POST['abuse_text'])."'	
		);");
		echo "Vielen Dank, dein Text wurde gespeichert.<br/>Ein Game-Administrator wird sich dem Problem annehmen.<br/><br/>";
	}
	else
	{
		echo "Über unser Benachrichtigungssystem kanns du einen Game-Administrator informieren, falls
		du einen Missbrauch der Spielregeln festgestellt hast. Bitte fülle folgendes Formular aus
		um dein Anliegen zu beschrieben; je mehr Infos du uns gibts, desto besser können wir dir helfen:<br/><br/>";
		echo "<form action=\"?page=$page\" method=\"post\">";
		checker_init();
		echo "<table class=\"tb\">
		<tr>
			<th>Kategorie:</th>
			<td><select name=\"abuse_cat\">";
			foreach ($abuse_cats as $k=>$v)
			{
				echo "<option value=\"".$k."\"";
				if ($_GET['cat']==$k) echo " selected=\"selected\"";
				echo ">".$v."</option>";			
			}
			echo "</select></td>
		</tr>
		<tr>
			<th>Betreffenden Spieler</th>
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
				if ($_GET['uid']==$arr[1]) echo " selected=\"selected\"";
				echo ">".$arr[0]."</option>";			
			}
			echo "</select></td>
		</tr>
		<tr>
			<th>Betreffende Allianz</th>
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
				if ($_GET['aid']==$arr[2]) echo " selected=\"selected\"";
				echo ">[".$arr[1]."] ".$arr[0]."</option>";			
			}
			echo "</select></td>
		</tr>
		<tr>
			<th>Beschreibung:</th>
			<td><textarea name=\"abuse_text\" rows=\"10\" cols=\"60\"></textarea></td>
		</tr>
		</table><br/>
		<input type=\"submit\" name=\"abuse_submit\" value=\"Einsenden\" /><br/><br/>";
		echo "</form>";
		
		if ($ext)
		{
		echo "<h2>Gemeldete Tickets</h2>";
		$res = dbquery("
		SELECT		
			a.user_nick as anick,
			abuse_timestamp,
			abuse_cat,
			abuse_id,
			abuse_admin_timestamp,
			abuse_text,
			abuse_notice,
			abuse_status		
		FROM
			abuses
		LEFT JOIN
			admin_users as a
		ON
			abuse_admin_id=a.user_id
		WHERE
			abuse_user_id=".$s['user']['id']."	
		ORDER BY
			abuse_timestamp DESC
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
				<td>#".$arr['abuse_id']."</td>
				<td>".$abuse_cats[$arr['abuse_cat']]."</td>
				<td>".df($arr['abuse_timestamp'])."</td>
				<td>".$status[$arr['abuse_status']]."</td>
				<td><a href=\"?page=contact\">".$arr['anick']."</a></td>
				<td>".($arr['abuse_admin_timestamp'] > 0 ? df($arr['abuse_admin_timestamp']) : "-")."</td>
				<td>
					[<a href=\"javascript:;\" onclick=\"toggleText('tx_".$arr['abuse_id']."','sw_".$arr['abuse_id']."')\" id=\"sw_".$arr['abuse_id']."\">Anzeigen</a>]
				</td>
				</tr>
				<tr id=\"tx_".$arr['abuse_id']."\" ";
				if ($_GET['id']!=$arr['abuse_id'])
				{
					echo "style=\"display:none;\"";
				}
				echo ">
					<td colspan=\"7\">
					<b>Meldung:</b><br/>
					".text2html($arr['abuse_text'])."<br/><br/>
					<b>Antwort:</b><br/>";
					if ($arr['abuse_notice']!="")
						echo text2html($arr['abuse_notice']);
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