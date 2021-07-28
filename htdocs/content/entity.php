<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository */
$planetRepo = $app[PlanetRepository::class];

/** @var EntityRepository $entityRepository */
$entityRepository = $app[EntityRepository::class];

/** @var UserRepository */
$userRepository = $app[UserRepository::class];

/** @var UserUniverseDiscoveryService */
$userUniverseDiscoveryService = $app[UserUniverseDiscoveryService::class];

$user = $userRepository->getUser($cu->id);

$id = 0;
if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $id = intval($_GET['id']);
} elseif (isset($_POST['id']) && intval($_POST['id']) > 0) {
    $id = intval($_POST['id']);
} elseif (isset($_POST['search_submit'])) {
    echo "<h1>Planeten-Datenbank</h1>";
    error_msg("Ungültige Kennung!");
}

if ($id > 0) {

    if ($ent = Entity::createFactoryById($id)) {
        $cell = new Cell($ent->cellId());
        if ($userUniverseDiscoveryService->discovered($user, $cell->absX(), $cell->absY())) {
            if ($ent->isValid()) {
                echo "<h1>Übersicht über " . $ent . " (" . $ent->entityCodeString() . ")</h1>";
                if ($ent->entityCode() == EntityType::PLANET) {
                    $planet = $planetRepo->find($ent->id());

                    $rowSpan = 7;
                    if (filled($planet->name)) {
                        $rowSpan++;
                    }
                    if (filled($planet->description)) {
                        $rowSpan++;
                    }
                    if ($planet->hasDebrisField()) {
                        $rowSpan++;
                    }

                    tableStart("Planetendaten");
                    echo "<tr>
                        <td width=\"320\" style=\"background:#000;;vertical-align:middle\" rowspan=\"" . $rowSpan . "\">
                            <img src=\"" . $ent->imagePath("b") . "\" alt=\"planet\" width=\"310\" height=\"310\"/>
                        </td>";
                    echo "<th width=\"100\">Besitzer:</th>
                    <td>";
                    if ($ent->ownerId() > 0)
                        echo "<a href=\"?page=userinfo&amp;id=" . $ent->ownerId() . "\">" . $ent->owner() . "</a>";
                    else
                        echo $ent->owner();
                    echo "</td>
                    </tr>";

                    if (filled($planet->name)) {
                        echo "<tr>
                            <th width=\"100\">Name:</th>
                            <td>" . $planet->name . "</td></tr>";
                    }

                    echo "<tr>
                        <th width=\"100\">Sonnentyp:</th>
                        <td>" . $ent->starTypeName . "</td></tr>";
                    echo "<tr>
                        <th width=\"100\">Planettyp:</th>
                        <td>" . $ent->typeName . "</td></tr>";
                    echo "<tr>
                        <th width=\"100\">Felder:</th>
                        <td>" . $planet->fields . " total</td></tr>";
                    echo "<tr>
                        <th width=\"100\">Grösse:</th>
                        <td>" . nf($config->getInt('field_squarekm') * $planet->fields) . " km&sup2;</td></tr>";
                    echo "<tr>
                        <th width=\"100\">Temperatur:</th>
                        <td>" . $planet->tempFrom . "&deg;C bis " . $planet->tempTo . "&deg;C <br/><br/>";
                    echo "<img src=\"images/heat_small.png\" alt=\"Heat\" style=\"width:16px;float:left;\" />
                        Wärmebonus: " . helpLink("tempbonus") . "<br/> ";
                    $solarProdBonus = $planet->solarPowerBonus();
                    $color = $solarProdBonus >= 0 ? '#0f0' : '#f00';
                    echo "<span style=\"color:" . $color . "\">" . ($solarProdBonus > 0 ? '+' : '') . $solarProdBonus . "</span>";
                    echo " Energie pro Solarsatellit <br style=\"clear:both;\"/><br/>
                        <img src=\"images/ice_small.png\" alt=\"Cold\" style=\"width:16px;float:left;\" />
                        Kältebonus: " . helpLink("tempbonus") . "<br/> ";
                    $fuelProdBonus = $planet->fuelProductionBonus();
                    $color = $fuelProdBonus >= 0 ? '#0f0' : '#f00';
                    echo "<span style=\"color:" . $color . "\">" . ($fuelProdBonus > 0 ? '+' : '') . $fuelProdBonus . "%</span>";
                    echo " " . RES_FUEL . "-Produktion </td></tr>";

                    if (filled($planet->description)) {
                        echo "<tr>
                            <th width=\"100\">Beschreibung:</th>
                            <td>" . $planet->description . "</td></tr>";
                    }

                    if ($planet->hasDebrisField()) {
                        echo '<tr>
                        <th class="tbltitle">Trümmerfeld:</th><td>
                        ' . RES_ICON_METAL . "" . nf($planet->wfMetal) . '<br style="clear:both;" />
                        ' . RES_ICON_CRYSTAL . "" . nf($planet->wfCrystal) . '<br style="clear:both;" />
                        ' . RES_ICON_PLASTIC . "" . nf($planet->wfPlastic) . '<br style="clear:both;" />
                        </td></tr>';
                    }

                    tableEnd();
                } elseif ($ent->entityCode() == 's') {
                    tableStart("Sterndaten");
                    echo "<tr>
                        <td width=\"220\" style=\"background:#000;vertical-align:middle\" rowspan=\"2\">
                            <img src=\"" . $ent->imagePath("b") . "\" alt=\"star\" width=\"220\" height=\"220\"/>
                        </td>";
                    echo "<th style=\"height:20px;\">Typ:</th>
                    <td>" . $ent->type() . " " . helpLink("stars") . "</td>
                    </tr>";

                    $data = $ent->typeData();

                    echo "<tr><th>Beschreibung:</th><td>" . $data['comment'] . "</td></tr>";


                    tableEnd();
                } else {
                    iBoxStart("Objektdaten");
                    echo "Über dieses Objekt sind keine weiteren Daten verfügbar!";
                    iBoxEnd();
                }

                // Previous and next entity
                $idprev = $id - 1;
                $idnext = $id + 1;
                $pmarr = $entityRepository->getMaxEntityId();
                if ($idprev > 0) {
                    $str_prev =    "<td><input type=\"button\" value=\"&lt;\" onclick=\"document.location='?page=$page&amp;id=" . $idprev . "'\" /></td>";
                }
                if ($idnext <= $pmarr) {
                    $str_next = "<td><input type=\"button\" value=\"&gt;\" onclick=\"document.location='?page=$page&amp;id=" . $idnext . "'\" /></td>";
                }
            } else {
                echo "<h1>Raumobjekt-Datenbank</h1>";
                error_msg("Das Objekt mit der Kennung [b]" . $id . "[/b] existiert nicht!");
            }
        } else {
            echo "<h1>Raumobjekt-Datenbank</h1>";
            error_msg("Das Objekt mit der Kennung [b]" . $id . "[/b] wurde noch nicht entdeckt!");
        }
    } else {
        echo "<h1>Raumobjekt-Datenbank</h1>";
        error_msg("Das Objekt mit der Kennung [b]" . $id . "[/b] existiert nicht!");
    }
} else {
    echo "<h1>Raumobjekt-Datenbank</h1>";
    error_msg("Das Objekt mit der Kennung [b]" . $id . "[/b] existiert nicht!");
}

echo "<form action=\"?page=$page\" method=\"post\" name=\"planetsearch\">";
tableStart("Objektsuche");
echo "<tr>";
if (isset($str_prev)) echo $str_prev;
echo "<th>Kennung:</th>
    <td>
        <input type=\"text\" name=\"id\" size=\"5\" maxlength=\"7\" value=\"" . $id . "\" /> &nbsp;
        <input type=\"submit\" name=\"search_submit\" value=\"Objekt anzeigen\" />
    </td>";
if (isset($str_next)) echo $str_next;
echo "</tr>";
tableEnd();
echo "<input type=\"button\" value=\"Zur Raumkarte\" onclick=\"document.location='?page=sector'\" /> &nbsp; ";
if (isset($ent))
    echo "<input type=\"button\" value=\"Zur Systemkarte\" onclick=\"document.location='?page=cell&amp;id=" . $ent->cellId() . "&hl=" . $id . "'\" />";
echo "</form>
<script type=\"\">document.forms['planetsearch'].elements[0].focus();</script>";
