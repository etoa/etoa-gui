<?PHP
	echo "<h2>Spionagesystem</h2>";
	helpNavi(array("Spionagesystem","spy_info"));
	
	infobox_start("Spionage");
	echo "Du hast die M&ouml;glichkeit Planeten anderer Spieler auszuspionieren.<br>
	Je h&ouml;her die \"Spionagetechik\" ist, desto mehr Informationen kannst du &uuml;ber den Gegnerischen Planeter rausfinden.<br>
	Ebenfalls kannst du mit Hilfe dieser Technik Informationen zu fremden Flotten, die zu einem deiner Planeten fliegen, herausfinden. Hier eine Liste, die zeigt, welche Informationen eine Technologiestufe der Technik \"Spionagetechnik\" &uuml;ber fremde Flotten und Planeten liefert:<br/><br/>";
	infobox_end();

	infobox_start("Infos &uuml;ber gegnerische Planeten",1);
	echo "<tr><td class=\"tbltitle\" >Stufe</td><td class=\"tbltitle\" width=\"90%\">Infos &uuml;ber...</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_ATTACK_SHOW_BUILDINGS."</b></td><td class=\"tbldata\" width=\"90%\">... die Geb&auml;ude</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_ATTACK_SHOW_RESEARCH."</b></td><td class=\"tbldata\" width=\"90%\">... die Forschung</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_ATTACK_SHOW_DEFENSE."</b></td><td class=\"tbldata\" width=\"90%\">... die Verteidigung</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_ATTACK_SHOW_SHIPS."</b></td><td class=\"tbldata\" width=\"90%\">... die Schiffe</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_ATTACK_SHOW_RESSOURCEN."</b></td><td class=\"tbldata\" width=\"90%\">... die Ressourcen</td></tr>";
	infobox_end(1);

	infobox_start("Infos &uuml;ber gegnerische Flotten",1);
	echo "<tr><td class=\"tbltitle\" >Stufe</td><td class=\"tbltitle\" width=\"90%\">M&ouml;glichkeiten</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_TECH_SHOW_ATTITUDE."</b></td><td class=\"tbldata\" width=\"90%\">Die Gesinnung (friedlich, feindlich) der Fotte wird angezeigt.</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_TECH_SHOW_NUM."</b></td><td class=\"tbldata\" width=\"90%\">Du siehst wieviele Schiffe in der Flotte sind.</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_TECH_SHOW_SHIPS."</b></td><td class=\"tbldata\" width=\"90%\">Du siehst welche Schiffstypen in der Flotte sind.</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_TECH_SHOW_NUMSHIPS."</b></td><td class=\"tbldata\" width=\"90%\">Du siehst, wieviele Schiffe von jedem Typ in der Flotte sind.</td></tr>";
	echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>".SPY_TECH_SHOW_ACTION."</b></td><td class=\"tbldata\" width=\"90%\">Mit dieser Stufe kannst du auch die geplante Aktion (Angreifen, Spionieren etc) der Flotte sehen.</td></tr>";
	infobox_end(1);

?>
