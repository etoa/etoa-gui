<?PHP
	echo "<h2>Statistik</h2>";
	HelpUtil::breadCrumbs(array("Statistik","stats"));
	iBoxStart("Spielerstatistiken");
	echo "Die Tabellen auf der Statistikseite zeigen eine Rangliste der Spieler und Allianzen. Sie ist geordnet nach Punkten (absteigend) und Registrierdatum (absteigend). Punkte gibt es f&uuml;r verbaute Rohstoffe, z.B. wenn du Geb&auml;ude oder Schiffe baust. Pro 1'000 verbaute Rohstoffe gibt es 1 Punkt. Die Allianzen bekommen ebenfalls Punkte: pro 100 Punkte, die die Mitglieder der Allianz zusammenbringen, gibt es 1 Allianzpunkt.";
	iBoxEnd();
?>