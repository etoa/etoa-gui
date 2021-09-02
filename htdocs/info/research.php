<?PHP

use EtoA\Ship\ShipRequirementRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\Universe\Resources\ResIcons;
use EtoA\Universe\Resources\ResourceNames;

$techSpeedCategory = 1;
echo "<h2>Technologien</h2>";

/** @var \EtoA\Technology\TechnologyDataRepository $technologyDataRepository */
$technologyDataRepository = $app[\EtoA\Technology\TechnologyDataRepository::class];

//Detail

if (isset($_GET['id'])) {
    $tid = intval($_GET['id']);
    $technology = $technologyDataRepository->getTechnology($tid);

    if ($technology !== null) {
        HelpUtil::breadCrumbs(array("Technologien", "research"), array($technology->name, $technology->id), 1);
        echo "<select onchange=\"document.location='?$link&site=research&id='+this.options[this.selectedIndex].value\">";

        $technologyNames = $technologyDataRepository->getTechnologyNames();
        foreach ($technologyNames as $technologyId => $technologyName) {
            echo "<option value=\"" . $technologyId . "\"";
            if ($technologyId === $tid) echo " selected=\"selected\"";
            echo ">" . $technologyName . "</option>";
        }
        echo "</select><br/><br/>";

        tableStart($technology->name);
        echo "<tr><th class=\"tbltitle\" style=\"width:220px;\" rowspan=\"2\"><img src=\"" . IMAGE_PATH . "/" . IMAGE_TECHNOLOGY_DIR . "/technology" . $technology->id . "." . IMAGE_EXT . "\" style=\"width:220px;height:220px;\" alt=\"Bild " . $technology->name . "\" /></td>";
        echo "<td class=\"tbldata\" colspan=\"2\"><div align=\"justify\">" . BBCodeUtils::toHTML($technology->longComment) . "</div></td></tr>";
        echo "<tr>
                <td class=\"tbltitle\" style=\"height:20px;width:120px;\">Maximale Stufe:</td>
                <td class=\"tbldata\" style=\"height:20px;\">" . $technology->lastLevel . "</td>
            </tr>";
        tableEnd();

        if ($technology->typeId === $techSpeedCategory) {
            /** @var ShipRequirementRepository $shipRequirementRepository */
            $shipRequirementRepository = $app[ShipRequirementRepository::class];
            $requirements = $shipRequirementRepository->getShipsWithRequiredTechnology($technology->id);
            if (count($requirements) > 0) {
                tableStart("Folgende Schiffe verwenden diesen Antrieb");
                foreach ($requirements as $requirement) {
                    echo "<tr><td class=\"tbldata\"><a href=\"?$link&amp;site=shipyard&amp;id=" . $requirement->id . "\">" . $requirement->name . "</a></td><td class=\"tbldata\">ben&ouml;tigt Stufe " . $requirement->requiredLevel . "</td></tr>";
                }
                tableEnd();
            }
        }

        // Kostenentwicklung
        tableStart("Kostenentwicklung (Faktor: " . $technology->buildCostsFactor . ")");
        echo "<tr><th class=\"tbltitle\" style=\"text-align:center;\">Level</th>
                  <th class=\"tbltitle\">" . ResIcons::METAL . "" . ResourceNames::METAL . "</th>
                  <th class=\"tbltitle\">" . ResIcons::CRYSTAL . "" . ResourceNames::CRYSTAL . "</th>
                  <th class=\"tbltitle\">" . ResIcons::PLASTIC . "" . ResourceNames::PLASTIC . "</th>
                  <th class=\"tbltitle\">" . ResIcons::FUEL . "" . ResourceNames::FUEL . "</th>
                  <th class=\"tbltitle\">" . ResIcons::FOOD . "" . ResourceNames::FOOD . "</th></tr>";
        for ($x = 0; $x < min(30, $technology->lastLevel); $x++) {
            $bc = calcTechCosts($technology, $x);
            echo '<tr><td class="tbldata">' . ($x + 1) . '</td>
                      <td class="tbldata" style="text-align:right;">' . StringUtils::formatNumber($bc['metal']) . '</td>
                      <td class="tbldata" style="text-align:right;">' . StringUtils::formatNumber($bc['crystal']) . '</td>
                      <td class="tbldata" style="text-align:right;">' . StringUtils::formatNumber($bc['plastic']) . '</td>
                      <td class="tbldata" style="text-align:right;">' . StringUtils::formatNumber($bc['fuel']) . '</td>
                      <td class="tbldata" style="text-align:right;">' . StringUtils::formatNumber($bc['food']) . '</td></tr>';
        }
        tableEnd();

        iBoxStart("Technikbaum");
        showTechTree("t", $technology->id);
        iBoxEnd();
    } else
        echo "Technologiedaten nicht gefunden!";
    echo "<input type=\"button\" value=\"Technologie&uuml;bersicht\" onclick=\"document.location='?$link&amp;site=$site'\" /> &nbsp; ";
    if (!$popup)
        echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=tech'\" /> &nbsp; ";
}

//�bersicht

else {
    /** @var \EtoA\Technology\TechnologyTypeRepository $technologyTypeRepository */
    $technologyTypeRepository = $app[\EtoA\Technology\TechnologyTypeRepository::class];

    HelpUtil::breadCrumbs(array("Technologien", "research"));
    $technologyTypes = $technologyTypeRepository->getTypes();
    if (count($technologyTypes) > 0) {
        foreach ($technologyTypes as $technologyType) {
            $technologies = $technologyDataRepository->getTechnologiesByType($technologyType->id);
            if (count($technologies) > 0) {
                tableStart($technologyType->name);
                foreach ($technologies as $technology) {
                    echo "<tr>
                            <td style=\"width:40px;padding:0px;background:#000\">
                                <a href=\"?$link&amp;site=$site&amp;id=" . $technology->id . "\">
                                    <img src=\"" . IMAGE_PATH . "/" . IMAGE_TECHNOLOGY_DIR . "/technology" . $technology->id . "_small." . IMAGE_EXT . "\" width=\"40\" height=\"40\" alt=\"Bild " . $technology->name . "\" border=\"0\"/>
                                </a>
                            </td>";
                    echo "<td style=\"width:160px;\">
                            <a href=\"?$link&amp;site=$site&amp;id=" . $technology->id . "\">" . $technology->name . "</a></td>";
                    echo "<td>" . $technology->shortComment . "</td></tr>";
                }
                tableEnd();
            }
        }
    } else
        echo "<i>Keine Daten vorhanden!</i>";
}
