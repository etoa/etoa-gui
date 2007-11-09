<?PHP
						echo "<h2>Allianzgeschichte</h2>";
						infobox_start("Geschichtsdaten",1);
						echo "<tr><th class=\"tbltitle\" style=\"width:120px;\">Datum / Zeit</th><th class=\"tbltitle\">Ereignis</th></tr>";
						$hres=dbquery("SELECT * FROM alliance_history WHERE history_alliance_id=".$s['user']['alliance_id']." ORDER BY history_timestamp DESC;");
						while ($harr=mysql_fetch_array($hres))
						{
							echo "<tr><td class=\"tbldata\">".date("d.m.Y H:i",$harr['history_timestamp'])."</td><td class=\"tbldata\">".text2html($harr['history_text'])."</td></tr>";
						}
						infobox_end(1);
						echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
?>