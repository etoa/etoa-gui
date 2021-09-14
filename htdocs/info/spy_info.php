<?PHP

use EtoA\Ship\ShipDataRepository;
use EtoA\Technology\SpyActionLevel;
use EtoA\Technology\SpyTechFleetLevel;

echo "<h2>Spionagesystem</h2>";
HelpUtil::breadCrumbs(array("Spionagesystem", "spy_info"));

iBoxStart("Spionage");
echo "Du hast die Möglichkeit Planeten anderer Spieler mit Hilfe von Spionagesonden auszuspionieren.<br>
    Je höher die \"Spionagetechik\" ist, desto mehr Informationen kannst du &uuml;ber den gegnerischen Planeten rausfinden.<br>
    Ebenfalls kannst du mit Hilfe dieser Technik Informationen zu fremden Flotten, die zu einem deiner Planeten fliegen, herausfinden.
    Die Spionage klappt aber nicht immer, sie kann auch abgewehrt werden. Der Abwehrwert setzt sich aus verschiedenen Faktoren, unter anderem
    der Tarntechnik, der Spionagetechnik, der Anzahl Sonden sowohl des spionierenden als auch des auszuspionierenden Spielers zusammen. Ebenfalls wird
    überall ein Zufallswert mitberechnet.
    Im Folgenden eine Liste, die zeigt, welche Informationen eine Technologiestufe der Technik \"Spionagetechnik\" &uuml;ber fremde Flotten und Planeten liefert:";
iBoxEnd();

tableStart("Infos &uuml;ber gegnerische Planeten");
echo "<tr><td class=\"tbltitle\" >Stufe</td><td class=\"tbltitle\" width=\"90%\">Infos &uuml;ber...</td></tr>";
echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>" . SpyActionLevel::SHOW_BUILDINGS . "</b></td><td class=\"tbldata\" width=\"90%\">... die Geb&auml;ude</td></tr>";
echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>" . SpyActionLevel::SHOW_RESEARCH . "</b></td><td class=\"tbldata\" width=\"90%\">... die Forschung</td></tr>";
echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>" . SpyActionLevel::SHOW_DEFENSE . "</b></td><td class=\"tbldata\" width=\"90%\">... die Verteidigung</td></tr>";
echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>" . SpyActionLevel::SHOW_SHIPS . "</b></td><td class=\"tbldata\" width=\"90%\">... die Schiffe</td></tr>";
echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>" . SpyActionLevel::SHOW_RESSOURCES . "</b></td><td class=\"tbldata\" width=\"90%\">... die Ressourcen</td></tr>";
echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>" . SpyActionLevel::SHOW_SUPPORT . "</b></td><td class=\"tbldata\" width=\"90%\">... die unterstüzenden Schiffe</td></tr>";
tableEnd();

tableStart("Infos &uuml;ber gegnerische Flotten");
echo "<tr><td class=\"tbltitle\" >Stufe</td><td class=\"tbltitle\" width=\"90%\">M&ouml;glichkeiten</td></tr>";
echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>0</b></td><td class=\"tbldata\" width=\"90%\">Es wird gar nichts angezeigt.</td></tr>";
echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>" . SpyTechFleetLevel::SHOW_ATTITUDE . "</b></td><td class=\"tbldata\" width=\"90%\">Die Gesinnung (friedlich, feindlich) der Fotte wird angezeigt.</td></tr>";
echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>" . SpyTechFleetLevel::SHOW_NUMBER . "</b></td><td class=\"tbldata\" width=\"90%\">Du siehst wieviele Schiffe in der Flotte sind.</td></tr>";
echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>" . SpyTechFleetLevel::SHOW_SHIPS . "</b></td><td class=\"tbldata\" width=\"90%\">Du siehst welche Schiffstypen in der Flotte sind.</td></tr>";
echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>" . SpyTechFleetLevel::SHOW_NUMBER_OF_SHIPS . "</b></td><td class=\"tbldata\" width=\"90%\">Du siehst, wieviele Schiffe von jedem Typ in der Flotte sind.</td></tr>";
echo "<tr><td class=\"tbldata\" valign=\"top\" ><b>" . SpyTechFleetLevel::SHOW_ACTION . "</b></td><td class=\"tbldata\" width=\"90%\">Mit dieser Stufe kannst du auch die geplante Aktion (Angreifen, Spionieren etc) der Flotte sehen.</td></tr>";
tableEnd();

tableStart("Spionagesonden");
echo "<tr><td class=\"tbltitle\" >Name</td><td class=\"tbltitle\" colspan=\"2\" width=\"90%\">Beschreibung</td></tr>";

/** @var ShipDataRepository $shipReposistory */
$shipReposistory = $app[ShipDataRepository::class];

$ships = $shipReposistory->getShipsWithAction('spy');
foreach ($ships as $ship) {
    echo "<tr><td class=\"tbldata\">" . $ship->name . "</td>
        <td class=\"tbldata\">" . $ship->shortComment . "</td>
        <td class=\"tbldata\"><a href=\"?" . $link . "&amp;site=shipyard&amp;id=" . $ship->id . "\">Mehr Infos</a></td>
        </tr>";
}
tableEnd();
