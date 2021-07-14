<?PHP
echo "<h2>Statistik</h2>";
HelpUtil::breadCrumbs(array("Statistik", "stats"));
iBoxStart("Spielerstatistiken");
echo "Die Tabellen auf der Statistikseite zeigen eine Rangliste der Spieler und Allianzen. Sie ist geordnet nach Punkten (absteigend) und Registrierdatum (absteigend). Die Punkte werden nach verbauten Rohstoffen berechnet. Dies gilt f&uuml;r gebaute Geb&auml;ude, Schiffe und Verteidigungsanlagen und f&uuml;r erforschte Technologien. Jedoch wird Nahrung, die zur Bauzeitreduktion verwendet wird, nicht mit einberechnet. In den Detailstatistiken wird aufgef&uuml;hrt wie gut die Spieler in den einzelnen Teilbereichen sind. Die Allianzen bekommen ebenfalls Punkte, die nach den Punkten der Allianzmitglieder berechnet werden.
    <br/><br/>
    Punkteberechnung: Spieler: Pro 1'000 verbaute Rohstoffe gibt es 1 Punkt. Allianz: Pro 100 Punkte eines Allianzmitgliedes bekommt die Allianz 1 Punkt. (Spielerpunkte geteilt durch 100)";
iBoxEnd();
