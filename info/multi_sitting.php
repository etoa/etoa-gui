<?php
    echo "<h2>Multierkennung / Sittingmodus</h2>";
		Help::navi(array("Multis und Sitting","multi_sitting"));
    iBoxStart("Multierkennung");
    echo "<div align=\"justify\">";
    echo "Die Multierkennung ist ein System welches entwickelt wurde um den Gebrauch von Multiaccounts zu verhindern.<br>Spielen jetzt mehrere User vom gleichen Computer aus oder haben dauerhaft die gleiche IP-Adresse, so m&uuml;ssen sich die betroffenen Spieler in der Multierkennung eintragen! Jeder User muss jeden anderen User eintragen, es reicht nicht, wenn zum Beispiel nur ein Spieler von zwei den anderen eintr&auml;gt!<br>Es ist ebenfalls nicht erlaubt User oft ein- und auszutragen!<br><br>
    Angabe eines Users:<br>
    1. \"User hinzuf&uuml;gen\"<br>
    2. User Nick eingeben<br>
    3. Beziehung eingeben (Bruder, Schwester, Ehemann etc.)<br>
    4. \"&Uuml;bernehmen\"";
    echo "</div>";
    iBoxEnd();

    iBoxStart("Sittingmodus");
    echo "<div align=\"justify\">";
    echo "Mit dem Sittingmodus ist es einem User m&ouml;glich, seinen Account von einem andere User (Sitter) verwalten zu lassen. Der Zeitraum in dem der Sitter aktiv sein soll, ist frei einstellbar!<br>Man kann den Sittingmodus aber nicht beliebig lange verwenden. Jeder Account hat eine Anzahl von \"Sittertagen\" welche er &uuml;ber die ganze Runde verbrauchen kann. Sind diese auf Null so muss man bei Abwesenheiten in den Urlaubsmodus wechseln!<br><br>
    Achtung, die folgenden Einstellungen muss der User machen, der in Abwesenheit ist! Er muss einen Sitter bestimmen und er muss auch ein Passwort festlegen, welches er dann dem Auserw&auml;hlten zusenden kann! (nicht das normale Account Passwort!)<br><br>
    Einstellen des Sittingmodus:<br>
    1. Datum hinzuf&uuml;gen<br>
    2. Erstes Startdatum festlegen und die Anzahl Tage w&auml;hlen<br>
    3. \"&Uuml;bernehmen\"<br>
    Wiederhole Schritt 1-3 solange, bis du mit den Daten zufrieden bist!<br>
    4. Sitter Nick eingeben<br>
    5. Sitter Passwort eingeben (Achtung, dieses Passwort darf nicht dein eigenes sein!)<br>
    6. Sitter Passwort (wiederholen) eingeben<br>
    7. \"&Uuml;bernehmen\"<br>
    Zufrieden mit allen Einstellungen?<br>
    Sitter Passwort gemerkt?<br>
    8. \"Sittingmodus aktivieren\"";
    echo "</div>";
    iBoxEnd();
?>
