<?php
    echo "<h2>Bewohner</h2>";
		Help::navi(array("Bewohner","population"));
    infobox_start("Bev&ouml;lkerung");
    echo "<div align=\"justify\">";
    echo "Jeder Planet hat Bewohner, mit welchen man die Bauzeit von Geb&auml;uden, Forschungen, Schiffen und Verteidigungsanlagen senken kann. Pro Arbeiter die unter \"<a href=\"?page=population\">Bev&ouml;lkerung</a>\" zugeteilt sind vermindert sich die Zeit um 3 Sekunden. Jedoch brauchen die Arbeiter auch Nahrung. Pro Arbeit die sie erledigen m&uuml;ssen, verlangen sie 12t Nahrung, welche direkt nach dem Baustart vom Planetkonto abgezogen werden. Wenn ihr den Bau abbrecht bekommt ihr gerechterweise den prozentualen Anteil an Nahrung wieder zur&uuml;ck.<br>
    Es ist ausserdem zu beachten, dass nicht unendlich schnell gebaut oder geforscht werden kann. Die minimale Bau- und Forschzeit betr&auml;gt 10% der normalen Zeit, was eine Zeiteinsparung von 90% bedeutet!";
    echo "</div>";
    infobox_end();
?>
