<?PHP
	echo "<h2>Spionagesystem</h2>";
	HelpUtil::breadCrumbs(array("Spionagesystem","spy_info"));
	
	iBoxStart("Spionage");
	echo "Du hast die Möglichkeit Planeten anderer Spieler mit Hilfe von Spionagesonden auszuspionieren.<br>
	Je höher die \"Spionagetechik\" ist, desto mehr Informationen kannst du &uuml;ber den gegnerischen Planeten rausfinden.<br>
	Ebenfalls kannst du mit Hilfe dieser Technik Informationen zu fremden Flotten, die zu einem deiner Planeten fliegen, herausfinden. 
	Die Spionage klappt aber nicht immer, sie kann auch abgewehrt werden. Der Abwehrwert setzt sich aus verschiedenen Faktoren, unter anderem
	der Tarntechnik, der Spionagetechnik, der Anzahl Sonden sowohl des spionierenden als auch des auszuspionierenden Spielers zusammen. Ebenfalls wird
	überall ein Zufallswert mitberechnet.
	Im Folgenden eine Liste, die zeigt, welche Informationen eine Technologiestufe der Technik \"Spionagetechnik\" &uuml;ber fremde Flotten und Planeten liefert:";
	iBoxEnd();

	tableStart("Infos &uuml;ber gegnerische Planeten");
	echo "<tr><td class=\"tbltitle\" >Stufe</td><td class=\"tbltitle\" width=\"90%\">Infos &uuml;ber...</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_ATTACK_SHOW_BUILDINGS."</b></td><td class=\"tbldata\" width=\"90%\">... die Geb&auml;ude</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_ATTACK_SHOW_RESEARCH."</b></td><td class=\"tbldata\" width=\"90%\">... die Forschung</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_ATTACK_SHOW_DEFENSE."</b></td><td class=\"tbldata\" width=\"90%\">... die Verteidigung</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_ATTACK_SHOW_SHIPS."</b></td><td class=\"tbldata\" width=\"90%\">... die Schiffe</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_ATTACK_SHOW_RESSOURCEN."</b></td><td class=\"tbldata\" width=\"90%\">... die Ressourcen</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_ATTACK_SHOW_SUPPORT."</b></td><td class=\"tbldata\" width=\"90%\">... die unterstüzenden Schiffe</td></tr>";
	tableEnd();

	tableStart("Infos &uuml;ber gegnerische Flotten");
	echo "<tr><td class=\"tbltitle\" >Stufe</td><td class=\"tbltitle\" width=\"90%\">M&ouml;glichkeiten</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>0</b></td><td class=\"tbldata\" width=\"90%\">Es wird gar nichts angezeigt.</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_TECH_SHOW_ATTITUDE."</b></td><td class=\"tbldata\" width=\"90%\">Die Gesinnung (friedlich, feindlich) der Fotte wird angezeigt.</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_TECH_SHOW_NUM."</b></td><td class=\"tbldata\" width=\"90%\">Du siehst wieviele Schiffe in der Flotte sind.</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_TECH_SHOW_SHIPS."</b></td><td class=\"tbldata\" width=\"90%\">Du siehst welche Schiffstypen in der Flotte sind.</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_TECH_SHOW_NUMSHIPS."</b></td><td class=\"tbldata\" width=\"90%\">Du siehst, wieviele Schiffe von jedem Typ in der Flotte sind.</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_TECH_SHOW_ACTION."</b></td><td class=\"tbldata\" width=\"90%\">Mit dieser Stufe kannst du auch die geplante Aktion (Angreifen, Spionieren etc) der Flotte sehen.</td></tr>";
	tableEnd();

	tableStart("Spionagesonden");
	echo "<tr><td class=\"tbltitle\" >Name</td><td class=\"tbltitle\" colspan=\"2\" width=\"90%\">Beschreibung</td></tr>";
	$res = dbquery("
	SELECT
		ship_name,
		ship_id,
		ship_shortcomment
	FROM
		ships
	WHERE
		ship_actions LIKE '%spy%'
	ORDER BY
		ship_name;");
	while ($arr=mysql_fetch_array($res))
	{
		echo "<tr><td class=\"tbldata\">".$arr['ship_name']."</td>
		<td class=\"tbldata\">".$arr['ship_shortcomment']."</td>
		<td class=\"tbldata\"><a href=\"?".$link."&amp;site=shipyard&amp;id=".$arr['ship_id']."\">Mehr Infos</a></td>
		</tr>";
	}
	tableEnd();

?>
