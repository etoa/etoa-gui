<?php
    echo "<h2>Raketensystem</h2>";
		helpNavi(array("Raketensystem","misslie_system"));
    infobox_start("Raketensystem");
    echo "<div align=\"justify\">";
    echo "Vom Raketensilo aus können Raketen für einen Raketenangriff gestartet werden. Es ist zu beachten, dass nur startbare Raketen abgeschossen werden können.<br>
					In der Raketenverwaltung kannst du jeweils eine begrenzte Anzahl Raketen kaufen oder verschrotten.<br/>
					Wenn du einen Planeten in deiner Umgebung angreiffen möchtest, wähle die gewünschten Raketen aus. Gib dann die Zielkoordinaten ein; es wird angezeigt, ob das Ziel erreichbar ist oder nicht. Wenn alles in Ordnung ist, kannst du die Raketen nun starten. Pro Stufe des Raketensilos ist ein Raketenangriff möglich.<br/>
					Wenn sie das Ziel erreicht haben, bekommst du eine Nachricht, wie der Angriff geendet hat. Es kann sein, dass sie von den Abfangraketen des Gegners abgeschossen wurden, dass Verteidigung des Gegners zerstört wurde, dass ein Gebäude temporär deaktiviert wurde oder dass die Verteidigung deines Gegners den Angfriff geblokt hat.<br/>
					Das hinterhältige an den Raketen ist, dass der Gegner sie nicht wahrnimmt. Er ist nicht vorgewarnt und kann dadurch nicht noch schnell Abfangraketen hinstellen oder die Verteidigung ausbauen. Im Gegenzug zeigt die Erkundungssonde zwar ein eventuell vorhandenes Raketensilo, nicht aber seinen Inhalt. So kannst du nur abschätzen, wie viele Raketen zur Verteidigung da sind.<br/>";
    echo "</div>";
    infobox_end();
?>