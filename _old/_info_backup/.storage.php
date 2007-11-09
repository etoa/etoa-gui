<?PHP
	infobox_start("Lagerkapazit&auml;t");
	echo "<div align=\"justify\">";
	echo "Du kannst auf einem Planeten nicht unentlich viele Rohstoffe lagern. Jeder Planet hat eine Lagerkapazit&auml;t von ".intval($conf['def_store_capacity']['v']).". Um die Lagerkapazit&auml;t zu erh&ouml;hen, kannst du eine Planetenbasis und danach verschiedene Speicher, Lagerhallen und Silos bauen, welche die Kapazit&auml;t erh&ouml;hen. Wenn eine Zahl in der Rohstoffanzeige rot gef&auml;rbt ist, bedeutet das, dass dieser Rohstoff die Lagerkapazit&auml;t &uuml;berschreitet. Baue in diesem Fall den Speicher aus. Eine &uuml;berschrittene Lagerkapazit&auml;t bedeutet, dass nichts mehr produziert wird, jedoch werden Rohstoffe, die z.B. mit einer Flotte ankommen, trotzdem auf dem Planeten gespeichert.<br>";
	echo "</div>";
	infobox_end();
?>
