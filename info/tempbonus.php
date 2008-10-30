<?php
	echo "<h2>Temperaturbonus</h2>";
	Help::navi(array("Temperaturbonus","tempbonus"));

	tableStart("Temperaturbonus");
	echo "<tr>
		<td class=\"tbltitle\" style=\"width:40px;\">Wärmebonus <br/>
		<img src=\"images/heat.png\" alt=\"Heat\"  style=\"width:100px;\" /></td>
		<td class=\"tbldata\">Die Planetentemperatur verstärkt oder schwächt die Produktion von Energie durch Solarsatelliten. 
		Je näher ein Planet bei der Sonne ist, desto besser ist die Temperatur und demzufolge auch die Energieproduktion.
		Der angegebene Wert in der Planetenübersicht zeigt an, wie viel Energie <b>jeder einzelne</b> Solarsatellit zusätlich produziert.
		Gebäude mit grossem Energiebedarf sollten darum auf sonnennahen Planeten gebaut werden.
	</tr>";
	echo "<tr>
		<td class=\"tbltitle\" style=\"width:40px;\">Kältebonus <br/>
		<img src=\"images/ice.png\" alt=\"Cold\" style=\"width:100px;\"/></td>
		<td class=\"tbldata\">Tritium kann auf kälteren Planeten besonders leicht hergestellt werden. mit Der Kältebonus wirkt sich daher prozentual auf die
		Tritiumproduktion aus (zusätzlich zu allen anderen Boni). Kältere Planeten sind weit weg von einem Stern; sie sind für eine grosse Tritiumproduktion sehr zu empfehlen. 
		</td>
	</tr>";	
	tableEnd();
?>



