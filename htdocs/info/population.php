<?php

use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

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
    <b>".nf($config->param1Int('user_start_people'))."</b> Menschen auf jedem Planeten. Dieser Wert kann durch folgende Gebäude gesteigert werden:<br/>
		<ul>";

    /** @var \EtoA\Building\BuildingDataRepository $buildingDataRepository */
    $buildingDataRepository = $app['etoa.building.datarepository'];
    $buildingNames = $buildingDataRepository->getBuildingNamesHavingPlaceForPeople();

    foreach ($buildingNames as $buildingId => $buildingName) {
        echo "<li><a href=\"?$link&amp;site=buildings&amp;id=".$buildingId."\">".$buildingName."</a></li>";
    }

    echo "</ul>";
    iBoxEnd();
