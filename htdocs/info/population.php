<?php
    echo "<h2>Bewohner</h2>";
		HelpUtil::breadCrumbs(array("Allgemeines"));
    iBoxStart("Bev&ouml;lkerung");
    echo "<div align=\"justify\">";
    echo "Jeder Planet hat Bewohner, mit welchen man die Bauzeit von Geb&auml;uden, Forschungen, Schiffen und Verteidigungsanlagen senken kann. Pro Arbeiter, die unter \"<a href=\"?page=population\">Bev&ouml;lkerung</a>\" zugeteilt werden, vermindert sich die Zeit um 3 Sekunden. Jedoch brauchen die Arbeiter auch Nahrung. Pro Arbeit die sie erledigen m&uuml;ssen, verlangen sie 12t Nahrung, welche direkt nach dem Baustart vom Planetenkonto abgezogen werden. Wenn ihr den Bau abbrecht, bekommt ihr gerechterweise den prozentualen Anteil an Nahrung wieder zur&uuml;ck.<br>
    Es ist ausserdem zu beachten, dass nicht unendlich schnell gebaut oder geforscht werden kann. Die minimale Bau- und Forschzeit betr&auml;gt 10% der normalen Zeit, was eine Zeiteinsparung von 90% bedeutet!";
    echo "</div>";
    iBoxEnd();
    
    iBoxStart("Wohnraum");    
		echo "Auf einem Planeten ist die Grösse der Bevölkerung begrenzt. Es gibt einen Grundwohnraum für
		<b>".nf($conf['user_start_people']['p1'])."</b> Menschen auf jedem Planeten. Dieser Wert kann durch folgende Gebäude gesteigert werden:<br/>
		<ul>";
		$res = dbquery("
		SELECT
			building_id,
      buildings.building_name
		FROM
    	buildings
    WHERE
    	buildings.building_people_place>0
     ;");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				echo "<li><a href=\"?$link&amp;site=buildings&amp;id=".$arr['building_id']."\">".$arr['building_name']."</a></li>";
			}
		}
		echo "</ul>";
    iBoxEnd();

    
    
    
?>
