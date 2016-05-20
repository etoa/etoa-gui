<?PHP

	$UID = $cu->id;
	if ($UID >0)
	{
			
		if (isset($_GET['chk']) && $_GET['chk'] == 'new')
		{
			echo "<h1>Notizen</h1><br>";
			echo "<form action=\"?page=notepad&amp;func=new\" method=\"post\">";
			echo "<input type=\"Text\" name=\"Titel\" size=\"50\" /><br><br>";
			echo "<textarea name=\"Text\" cols=\"80\" rows=\"20\"></textarea><br><br>";
			echo "<input type=\"Submit\" name=\"Einf&#252gen\" value=\"Einf&#252gen\"></input>";
			echo "</form>";
		}
		elseif (isset($_GET['chk']) && $_GET['chk'] == 'edit')
		{
			$res = dbquery("select * from admin_notes where notes_id=".$_GET['pid']." AND admin_id ='".$UID."'");
			$row = mysql_fetch_array($res);

			echo "<h1>Notizen</h1><br>";
			echo "<form action=\"?page=notepad&amp;func=editieren\" method=\"post\">";
			echo "<input type=\"hidden\" name=\"pid\" value=\"".$row['notes_id']."\">
			<input type=\"Text\" name=\"Titel\" value=\"".$row['titel']."\" size=\"50\"/><br><br>";
			echo "<textarea name=\"Text\" cols=\"80\" rows=\"20\">".$row['text']."</textarea><br><br>";
			echo "<input type=\"Submit\" name=\"&#196ndern\" value=\"&#196ndern\"></input>";
			echo "</form>";
		}							
		else
		{
			echo "<h1>Notizen</h1>";

			// New action
			if(isset($_GET['func']) && $_GET['func'] == 'new')
			{
				$time = time();
				$Titel = $_POST['Titel'];
				$Text = $_POST['Text'];
				dbquery("
				insert into 
					admin_notes 
				(
					admin_id, titel, text, date
				)
				 values 
				(
					'$UID', '$Titel', '$Text', '$time'
				);
				");
			}				
			
			// Edit action								
			if (isset($_GET['func']) && $_GET['func'] == 'editieren')
			{
				$Titel = $_POST['Titel'];
				$Text = $_POST['Text'];
				$PID = $_POST['pid'];
				dbquery("update admin_notes set text = '".$Text."', titel = '".$Titel."' where notes_id = ".$PID."");
			}								
			
			// Delete action
			if (isset($_GET['chk']) && $_GET['chk'] == 'del')
			{
				$PID = $_GET['pid'];
				dbquery("delete from admin_notes where notes_id = ".$PID."");
			}												

			// Overview
			$res = dbquery("select * from admin_notes where admin_id ='".$UID."'");
			if (mysql_num_rows($res) == 0)
			{
				echo "Keine Notiz vorhanden";
			
				echo "<form action=\"?page=notepad&amp;chk=new\" method=\"post\">";
				echo "<br><input type=\"Submit\" name=\"Neue Notiz\" value=\"Neue Notiz\"></input>";
				echo "</form>";
			}
			else
			{
				echo "<br>";
				tableStart("Notizübersicht","95%");
				while ($row = mysql_fetch_array($res))
				{
					$Time = $row['date'];
					$datum = date("d.m.Y",$Time);
					$uhrzeit = date("H:i",$Time);

					$PID = $row['notes_id'];

					echo "<tr>
					<td width=\"120\"><b>".text2html($row['titel'])."</b><br/>".$datum." ".$uhrzeit."</td>";
					echo "<td width=\"350\">".text2html($row['text'])."</td>";
					echo "<td>
					<a href=\"?page=$page&amp;chk=edit&pid=".$PID."\">Bearbeiten</a><br>
					<a href=\"?page=$page&amp;chk=del&pid=".$PID."\" onclick=\"return confirm('Eintrag löschen?')\">L&#246schen</a>
					</td></tr>";
				}
				echo "</table>";
				echo "<form action=\"?page=notepad&amp;chk=new\" method=\"post\">";
				echo "<br><input type=\"Submit\" name=\"Neue Notiz\" value=\"Neue Notiz\"></input>";
				echo "</form>";
			}
		}
						
	}
	else
	{
		echo "Ungültige ID";
	}

?>