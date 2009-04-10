<?PHP
if (Alliance::checkActionRights('history'))
{


						echo "<h2>Allianzgeschichte</h2>";
						tableStart("Geschichtsdaten");
						echo "<tr><th style=\"width:120px;\">Datum / Zeit</th><th>Ereignis</th></tr>";
						$hres=dbquery("
						SELECT 
							* 
						FROM 
							alliance_history 
						WHERE 
							history_alliance_id=".$arr['alliance_id']." 
						ORDER BY history_timestamp DESC;");
						while ($harr=mysql_fetch_array($hres))
						{
							echo "<tr><td>".date("d.m.Y H:i",$harr['history_timestamp'])."</td><td>".text2html($harr['history_text'])."</td></tr>";
						}
						tableEnd();
						echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
}
?>