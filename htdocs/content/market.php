<?PHP

use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Planet\PlanetRepository;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository */
$planetRepo = $app[PlanetRepository::class];

/** @var ResourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];
/** @var BuildingRepository $buildingRepository */
$buildingRepository = $app[BuildingRepository::class];

/********
 *	Datei-Struktur
 *	Die Datei market.php ist wie folgt aufgebaut
 *
 *	if(Markt gebaut)
 *	{
 *		-> Navigation
 *
 *		-> Rohstoffverkauf speichern
 *		-> Schiffskauf speichern
 *		-> Auktionsgebot speichern
 *
 *		-> Rohstoffverkauf speichern
 *		-> Schiffverkauf speichern
 *		-> Auktion Speichern
 *
 *		-> Rohstoff Angebote anzeigen
 *		-> Schiffs Angebote anzeigen
 *		-> Auktionen anzeigen
 *		-> Einzelne Auktion anzeigen
 *
 *		-> Suchmaske
 *		-> Eigene Angebote anzeigen
 *		-> Angebote aufgeben
 *	}
 *
 ********/

$planet = $planetRepo->find($cp->id);

if ($config->getBoolean('market_enabled')) {
    $mode = isset($_GET['mode']) ? $_GET['mode'] : "";

    $market = $buildingRepository->getEntityBuilding($cu->getId(), $planet->id, MARKTPLATZ_ID);

    //Überprüfung ob der Marktplatz schon gebaut wurde
    if ($market !== null && $market->currentLevel > 0) {
        // Header
        //<editor-fold>

        define("MARKET_TAX", max(1, MARKET_SELL_TAX * $cu->specialist->tradeBonus));

        // Show title
        echo '<h1>Marktplatz (Stufe ' . $market->currentLevel . ') des Planeten ' . $planet->name . '</h1>';
        echo $resourceBoxDrawer->getHTML($planet);

        if ($market->isDeactivated()) {
            // Load javascript
            require("content/market/js.php");

            // Läd die Anzahl aller eingestellter Angebote auf dem aktuellen Planeten
            $cnt_res = dbquery("
                SELECT
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            market_ressource
                        WHERE
                            user_id='" . $cu->id . "'
                            AND entity_id='" . $planet->id . "'
                            AND buyer_entity_id=0
                    ) AS ress_cnt,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            market_ship
                        WHERE
                            user_id='" . $cu->id . "'
                            AND entity_id='" . $planet->id . "'
                            AND buyer_entity_id=0
                    ) AS ship_cnt,
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            market_auction
                        WHERE
                            user_id='" . $cu->id . "'
                            AND entity_id='" . $planet->id . "'
                    ) AS auction_cnt
                    ;");

            $cnt_arr = mysql_fetch_assoc($cnt_res);

            // Summiert die eingestellten Angebote und berechnet die Anzahl der noch einstellbaren Angebote
            $anzahl = $cnt_arr['ress_cnt'] + $cnt_arr['ship_cnt'] + $cnt_arr['auction_cnt'];
            $possible = $market->currentLevel - $anzahl;

            // Lädt Stufe des Allianzmarktplatzes
            if ($cu->allianceId() > 0)
                $alliance_market_level = $cu->alliance->buildlist->getLevel(ALLIANCE_MARKET_ID);
            else
                $alliance_market_level = 0;

            // Calculate cooldown
            if ($alliance_market_level < 5) {
                $factor = 0.2 * $alliance_market_level;
            } else {
                $factor = $alliance_market_level - 4;
            }
            $cooldown = ($factor == 0) ? 0 : 3600 / $factor;
            if ($alliance_market_level > 0) {
                if ($cu->alliance->buildlist->getCooldown(ALLIANCE_MARKET_ID) > time()) {
                    $status_text = "Bereit in <span id=\"cdcd\">" . tf($cu->alliance->buildlist->getCooldown(ALLIANCE_MARKET_ID) - time() . "</span>");
                    $cd_enabled = true;
                } else {
                    $status_text = "Bereit";
                    $cd_enabled = false;
                }
            } else {
                $status_text = "Es wurde noch kein Handelszentrum gebaut!";
                $cd_enabled = false;
            }

            // Definiert den Rückgabefaktor beim zurückziehen eines Angebots
            $return_factor = floor((1 - 1 / ($market->currentLevel + 1)) * 100) / 100;


            //Marktinof Bof
            tableStart("Marktplatz-Infos");
            echo "<tr><th>Angebote:</th>
                <td>Im Moment hast du " . $anzahl . " Angebote von diesem Planet auf dem Markt</td></tr>";
            echo "<tr><th>Mögliche Angebote:</th>
                <td>Du kannst noch " . $possible . " Angebote einstellen</td></tr>";
            echo "<tr><th>Rückzugsgebühren:</th>
                <td>Wenn du ein Angebot von diesem Planet zur&uuml;ckziehst erh&auml;lst du " . ($return_factor * 100) . "% des Angebotes zur&uuml;ck (abgerundet).</td></tr>";
            echo "<tr><th>Verkaufsgebühren:</th>
                <td>Die Verkaufsgeb&uuml;hr des Marktplatzes betr&auml;gt " . get_percent_string(MARKET_TAX, 1, 1) . "";
            if ($cu->specialist->tradeBonus != 1) {
                echo " (inkl " . get_percent_string($cu->specialist->tradeBonus, 1, 1) . " Kostenverringerung durch " . $cu->specialist->name . "!";
            }
            echo "	</td></tr>";
            if ($cu->specialist->tradeTime != 1) {
                echo "<tr><th>Handelsflottengeschwindigkeit:</th>
                    <td>Die Handelsflotten fliegen durch " . $cu->specialist->name . " mit " . get_percent_string($cu->specialist->tradeTime, 1) . " Geschwindigkeit!
                    </td></tr>";
            }
            if ($cu->allianceId() > 0) {
                echo "<tr><th>Allianzmarktstatus:</th>
                    <td>" . $status_text . "</td></tr>";
            }


            echo "<tr><th>Rohstoffkurse:</th><td>" . popUp("Details anzeigen", "page=help&amp;site=rates") . "</td></tr>";
            tableEnd();

            // Navigation
            $tabitems = array(
                "user_home" => "Angebote aufgeben",
                "user_sell" => "Eigene Angebote",
                "search" => "Angebotssuche",
            );
            show_tab_menu("mode", $tabitems);

            echo "<br/>";

            //</editor-fold>

            //
            // Rohstoffkauf speichern
            //
            if (isset($_POST['ressource_submit']) && checker_verify()) {
                require("content/market/res_sell.php");
            }

            //
            // Schiffskauf speichern
            //
            elseif (isset($_POST['ship_submit']) && checker_verify()) {
                require("content/market/ship_sell.php");
            }

            //
            // Auktionsgebot speichern
            //
            elseif (isset($_POST['submit_auction_bid'])  && checker_verify()) {
                require("content/market/auction_bid.php");
            }

            //
            // Rohstoffverkauf speichern
            //
            elseif (isset($_POST['ress_last_update']) && intval($_POST['ress_last_update']) == 1 && checker_verify()) {
                require("content/market/res_offer.php");
            }

            //
            // Schiffverkauf speichern
            //
            elseif (isset($_POST['ship_last_update']) && intval($_POST['ship_last_update']) == 1 && checker_verify()) {
                require("content/market/ship_offer.php");
            }

            //
            // Auktion Speichern
            //
            elseif (isset($_POST['auction_last_update']) && intval($_POST['auction_last_update']) == 1 && checker_verify()) {
                require("content/market/auction_new.php");
            }


            //
            // Einzelne Auktion anzeigen (Bei einer Auktion bieten)
            //<editor-fold>
            elseif (isset($_POST['auction_market_id']) && intval($_POST['auction_market_id']) != 0 && !isset($_POST['auction_cancel']) && checker_verify()) {


                $cnt = 0;
                $acnts = array();
                $acnt = 0;

                $res = dbquery("
                SELECT
                    *
                FROM
                    market_auction
                WHERE
                    auction_market_id='" . intval($_POST['auction_market_id']) . "'
                    AND auction_user_id!='" . $cu->id . "' ");
                if (mysql_num_rows($res) > 0) {
                    $arr = mysql_fetch_array($res);

                    echo "<form action=\"?page=" . $page . "&amp;mode=auction\" method=\"post\" name=\"auctionShowFormular\" id=\"auction_show_selector\">";
                    $cstr = checker_init();

                    // Übergibt Daten an XAJAX
                    // Rohstoffe
                    echo "<input type=\"hidden\" value=\"" . $planet->resMetal . "\" name=\"res_metal\" id=\"res_metal\"/>";
                    echo "<input type=\"hidden\" value=\"" . $planet->resCrystal . "\" name=\"res_crystal\" id=\"res_crystal\"/>";
                    echo "<input type=\"hidden\" value=\"" . $planet->resPlastic . "\" name=\"res_plastic\" id=\"res_plastic\"/>";
                    echo "<input type=\"hidden\" value=\"" . $planet->resFuel . "\" name=\"res_fuel\" id=\"res_fuel\"/>";
                    echo "<input type=\"hidden\" value=\"" . $planet->resFood . "\" name=\"res_food\" id=\"res_food\"/>";

                    // Angebot
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_sell_metal'] . "\" name=\"auction_sell_metal\" id=\"auction_sell_metal\"/>";
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_sell_crystal'] . "\" name=\"auction_sell_crystal\" id=\"auction_sell_crystal\"/>";
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_sell_plastic'] . "\" name=\"auction_sell_plastic\" id=\"auction_sell_plastic\"/>";
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_sell_fuel'] . "\" name=\"auction_sell_fuel\" id=\"auction_sell_fuel\"/>";
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_sell_food'] . "\" name=\"auction_sell_food\" id=\"auction_sell_food\"/>";

                    // Höchstgebot
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_buy_metal'] . "\" name=\"auction_buy_metal\" id=\"auction_buy_metal\"/>";
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_buy_crystal'] . "\" name=\"auction_buy_crystal\" id=\"auction_buy_crystal\"/>";
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_buy_plastic'] . "\" name=\"auction_buy_plastic\" id=\"auction_buy_plastic\"/>";
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_buy_fuel'] . "\" name=\"auction_buy_fuel\" id=\"auction_buy_fuel\"/>";
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_buy_food'] . "\" name=\"auction_buy_food\" id=\"auction_buy_food\"/>";

                    // Gewünschte Währung
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_currency_metal'] . "\" name=\"auction_currency_metal\" id=\"auction_currency_metal\"/>";
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_currency_crystal'] . "\" name=\"auction_currency_crystal\" id=\"auction_currency_crystal\"/>";
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_currency_plastic'] . "\" name=\"auction_currency_plastic\" id=\"auction_currency_plastic\"/>";
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_currency_fuel'] . "\" name=\"auction_currency_fuel\" id=\"auction_currency_fuel\"/>";
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_currency_food'] . "\" name=\"auction_currency_food\" id=\"auction_currency_food\"/>";

                    // Zeit
                    echo "<input type=\"hidden\" value=\"0\" name=\"auction_rest_time\" id=\"auction_rest_time\"/>";

                    // Angebot ID
                    echo "<input type=\"hidden\" value=\"" . $arr['auction_market_id'] . "\" name=\"auction_market_id\" id=\"auction_market_id\"/>";

                    // SQL
                    echo "<input type=\"hidden\" value=\"" . stripslashes($_POST['auction_sql_add']) . "\" id=\"auction_sql_add\" name=\"auction_sql_add\"/>";

                    //Check Feld (wird beim Klicken auf den Submit-Button noch einmal aktualisiert)
                    echo "<input type=\"hidden\" value=\"0\" name=\"auction_show_last_update\" id=\"auction_show_last_update\"/>";

                    // Wird gewechselt wenn man den "Zurückbutton" benutzt
                    echo "<input type=\"hidden\" value=\"0\" name=\"auction_back\" id=\"auction_back\"/>";

                    //restliche zeit bis zum ende
                    $rest_time = $arr['auction_end'] - time();

                    $t = floor($rest_time / 3600 / 24);
                    $h = floor(($rest_time - ($t * 24 * 3600)) / 3600);
                    $m = floor(($rest_time - ($t * 24 * 3600) - ($h * 3600)) / 60);
                    $sec = floor(($rest_time - ($t * 24 * 3600) - ($h * 3600) - ($m * 60)));

                    if ($rest_time <= 3600) {
                        $class = "class=\"tbldata2\"";
                    } else {
                        $class = "class=\"tbldata\"";
                    }

                    // Gibt Nachricht aus, wenn die Auktion beendet ist
                    if ($rest_time <= 0) {
                        $rest_time = "Auktion beendet!";
                    }
                    // und sonst wird die Zeit bis zum Ende angezeigt
                    else {
                        $rest_time = "Noch " . $t . "t " . $h . "h " . $m . "m " . $sec . "s";
                    }

                    // Übergibt die Endzeit an de Javascript Countdownfunktion
                    $acnts['countdown' . $acnt] = $arr['auction_end'] - time();

                    // Höchstbietender anzeigen wenn vorhanden
                    if ($arr['auction_current_buyer_id'] != 0) {
                        $buyer = "<a href=\"?page=userinfo&amp;id=" . $arr['auction_current_buyer_id'] . "\">" . get_user_nick($arr['auction_current_buyer_id']) . "</a>";
                    } else {
                        $buyer = "&nbsp;";
                    }



                    // Allgemeine Angebotsinfo
                    tableStart("Angebotsinfo");
                    echo "<tr>
                    <th>Anbieter</th>
                                    <td>
                                        <a href=\"?page=userinfo&amp;id=" . $arr['auction_user_id'] . "\">" . get_user_nick($arr['auction_user_id']) . "</a>
                                    </td>
                                </tr>
                                <tr>
                    <th>Start</th>
                                    <td>
                                        " . date("d.m.Y  G:i:s", $arr['auction_start']) . "
                                    </td>
                                </tr>
                                <tr>
                    <th>Ende</th>
                                    <td>
                                        " . date("d.m.Y  G:i:s", $arr['auction_end']) . "
                                    </td>
                                </tr>
                                <tr>
                    <th>Dauer</th>";
                    // Löschdatum anzeigen wenn dieses schon festgelegt ist und "Auktion beendet"
                    if ($arr['auction_delete_date'] != 0) {
                        $delete_rest_time = $arr['auction_delete_date'] - time();

                        $t = floor($delete_rest_time / 3600 / 24);
                        $h = floor(($delete_rest_time) / 3600);
                        $m = floor(($delete_rest_time - ($h * 3600)) / 60);
                        $sec = floor(($delete_rest_time - ($h * 3600) - ($m * 60)));

                        // Gibt Nachricht aus, wenn Löschzeit erreicht oder überschritten
                        if ($delete_rest_time <= 0) {
                            $delete_time = "Wird gelöscht...";
                        }
                        // und sonst wird die Zeit bis zur Löschung angezeigt
                        else {
                            $delete_time = "In " . $h . "h und " . $m . "m";
                        }


                        echo "<td>AUKTION BEENDET</td>
                                    </tr>
                                    <tr>
                                        <th>Löschung</th>
                                        <td>" . $delete_time . "</td>
                                    </tr>";
                    } else {
                        echo "<td " . $class . " id=\"countdown" . $acnt . "\">" . $rest_time . "</td>";
                    }


                    echo "</tr>";

                    // Höchstbietender anzeigen wenn vorhanden
                    if ($arr['auction_current_buyer_id'] != 0) {
                        echo "<tr>
                                    <th>Höchstbietender</th>
                                                    <td>
                                                        " . $buyer . "
                                                    </td>
                                                </tr>
                                                <tr>
                                    <th>Geboten am</th>
                                                    <td>
                                                        " . date("d.m.Y  G:i:s", $arr['auction_current_buyer_date']) . "
                                                    </td>
                                                </tr>";
                    }
                    tableEnd();

                    echo "<script type=\"text/javascript\">";
                    foreach ($acnts as $cfield => $ctime) {
                        echo "setCountdown('" . $ctime . "','" . $cfield . "');";
                    }
                    echo "</script>";


                    // Angebots/Preis Maske
                    //Header
                    tableStart();
                    echo "<tr>
                                    <th style=\"width:15%;vertical-align:middle;\">Rohstoff</th>
                                    <th style=\"width:15%;vertical-align:middle;\">Angebot</th>
                                    <th style=\"width:5%;text-align:center;vertical-align:middle;\">Kurs</th>
                                    <th style=\"width:15%;vertical-align:middle;\">Höchstgebot</th>
                                    <th style=\"width:15%;vertical-align:middle;\">Bieten</th>
                                    <th style=\"width:35%;vertical-align:middle;\">Min./Max.</th>
                                </tr>";
                    // Titan
                    echo "<tr>
                                    <th style=\"vertical-align:middle;\">" . RES_METAL . ":</th>
                                    <td id=\"auction_sell_metal_field\" style=\"vertical-align:middle;\">
                                        " . nf($arr['auction_sell_metal']) . "
                                    </td>
                                    <th style=\"text-align:center;vertical-align:middle;\">" . MARKET_METAL_FACTOR . "</th>
                                    <td id=\"auction_buy_metal_field\" style=\"vertical-align:middle;\">
                                        " . nf($arr['auction_buy_metal']) . "
                                    </td>
                                    <td style=\"vertical-align:middle;\">";
                    if ($arr['auction_currency_metal'] == 1 && $arr['auction_buyable'] == 1) {
                        echo "<input type=\"text\" value=\"" . nf($arr['auction_buy_metal']) . "\" name=\"auction_new_buy_metal\" id=\"auction_new_buy_metal\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value," . $planet->resMetal . ",'','');calcMarketAuctionPrice(0);\"/>";
                    } else {
                        echo "<input type=\"hidden\" value=\"0\" name=\"auction_new_buy_metal\" id=\"auction_new_buy_metal\"/>";
                        echo " - ";
                    }
                    echo "</td>
                                    <th id=\"auction_min_max_metal\" style=\"vertical-align:middle;\"> - </th>
                                </tr>";
                    // Silizium
                    echo "<tr>
                                    <th style=\"vertical-align:middle;\">" . RES_CRYSTAL . ":</th>
                                    <td id=\"auction_sell_crystal_field\" style=\"vertical-align:middle;\">
                                        " . nf($arr['auction_sell_crystal']) . "
                                    </td>
                                    <th style=\"text-align:center;vertical-align:middle;\">" . MARKET_CRYSTAL_FACTOR . "</th>
                                    <td id=\"auction_buy_crystal_field\" style=\"vertical-align:middle;\">
                                        " . nf($arr['auction_buy_crystal']) . "
                                    </td>
                                    <td style=\"vertical-align:middle;\">";
                    if ($arr['auction_currency_crystal'] == 1 && $arr['auction_buyable'] == 1) {
                        echo "<input type=\"text\" value=\"" . nf($arr['auction_buy_crystal']) . "\" name=\"auction_new_buy_crystal\" id=\"auction_new_buy_crystal\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value," . $planet->resCrystal . ",'','');calcMarketAuctionPrice(0);\"/>";
                    } else {
                        echo "<input type=\"hidden\" value=\"0\" name=\"auction_new_buy_crystal\" id=\"auction_new_buy_crystal\"/>";
                        echo " - ";
                    }
                    echo "</td>
                                    <th id=\"auction_min_max_crystal\" style=\"vertical-align:middle;\"> - </th>
                                </tr>";
                    // PVC
                    echo "<tr>
                                    <th style=\"vertical-align:middle;\">" . RES_PLASTIC . ":</th>
                                    <td id=\"auction_sell_plastic_field\" style=\"vertical-align:middle;\">
                                        " . nf($arr['auction_sell_plastic']) . "
                                    </td>
                                    <th style=\"text-align:center;vertical-align:middle;\">" . MARKET_PLASTIC_FACTOR . "</th>
                                    <td id=\"auction_buy_plastic_field\" style=\"vertical-align:middle;\">
                                        " . nf($arr['auction_buy_plastic']) . "
                                    </td>
                                    <td style=\"vertical-align:middle;\">";
                    if ($arr['auction_currency_plastic'] == 1 && $arr['auction_buyable'] == 1) {
                        echo "<input type=\"text\" value=\"" . nf($arr['auction_buy_plastic']) . "\" name=\"auction_new_buy_plastic\" id=\"auction_new_buy_plastic\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value," . $planet->resPlastic . ",'','');calcMarketAuctionPrice(0);\"/>";
                    } else {
                        echo "<input type=\"hidden\" value=\"0\" name=\"auction_new_buy_plastic\" id=\"auction_new_buy_plastic\"/>";
                        echo " - ";
                    }
                    echo "</td>
                                    <th id=\"auction_min_max_plastic\" style=\"vertical-align:middle;\"> - </th>
                                </tr>";
                    // Tritium
                    echo "<tr>
                                    <th style=\"vertical-align:middle;\">" . RES_FUEL . ":</th>
                                    <td id=\"auction_sell_fuel_field\" style=\"vertical-align:middle;\">
                                        " . nf($arr['auction_sell_fuel']) . "
                                    </td>
                                    <th style=\"text-align:center;vertical-align:middle;\">" . MARKET_FUEL_FACTOR . "</th>
                                    <td id=\"auction_buy_fuel_field\" style=\"vertical-align:middle;\">
                                        " . nf($arr['auction_buy_fuel']) . "
                                    </td>
                                    <td style=\"vertical-align:middle;\">";
                    if ($arr['auction_currency_fuel'] == 1 && $arr['auction_buyable'] == 1) {
                        echo "<input type=\"text\" value=\"" . nf($arr['auction_buy_fuel']) . "\" name=\"auction_new_buy_fuel\" id=\"auction_new_buy_fuel\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value," . $planet->resFuel . ",'','');calcMarketAuctionPrice(0);\"/>";
                    } else {
                        echo "<input type=\"hidden\" value=\"0\" name=\"auction_new_buy_fuel\" id=\"auction_new_buy_fuel\"/>";
                        echo " - ";
                    }
                    echo "</td>
                                    <th id=\"auction_min_max_fuel\" style=\"vertical-align:middle;\"> - </th>
                                </tr>";
                    // Nahrung
                    echo "<tr>
                                    <th style=\"vertical-align:middle;\">" . RES_FOOD . ":</th>
                                    <td id=\"auction_sell_food_field\" style=\"vertical-align:middle;\">
                                        " . nf($arr['auction_sell_food']) . "
                                    </td>
                                    <th style=\"text-align:center;vertical-align:middle;\">" . MARKET_FOOD_FACTOR . "</th>
                                    <td id=\"auction_buy_food_field\" style=\"vertical-align:middle;\">
                                        " . nf($arr['auction_buy_food']) . "
                                    </td>
                                    <td style=\"vertical-align:middle;\">";
                    if ($arr['auction_currency_food'] == 1 && $arr['auction_buyable'] == 1) {
                        echo "<input type=\"text\" value=\"" . nf($arr['auction_buy_food']) . "\" name=\"auction_new_buy_food\" id=\"auction_new_buy_food\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value," . $planet->resFood . ",'','');calcMarketAuctionPrice(0);\"/>";
                    } else {
                        echo "<input type=\"hidden\" value=\"0\" name=\"auction_new_buy_food\" id=\"auction_new_buy_food\"/>";
                        echo " - ";
                    }
                    echo "</td>
                                    <th id=\"auction_min_max_food\" style=\"vertical-align:middle;\"> - </th>
                                </tr>";

                    // Status Nachricht (Ajax Überprüfungstext)
                    echo "<tr>
                                    <td colspan=\"6\" id=\"auction_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
                                </tr>";
                    tableEnd();

                    echo "<br/><br/><input type=\"button\" class=\"button\" name=\"auction_submit\" id=\"auction_submit\" value=\"Bieten\" disabled=\"disabled\" onclick=\"calcMarketAuctionPrice(1);checkUpdate('auctionShowFormular', 'auction_show_last_update');\"/><br/><br/><input type=\"button\" class=\"button\" name=\"auction_back_submit\" id=\"auction_back_submit\" value=\"Zurück\" onclick=\"auctionBack();\" />";
                    echo "</form>";
                } else {
                    error_msg("Angebot nicht mehr vorhanden!");
                }
            }
            //</editor-fold>

            //
            // Suchmaske
            //
            elseif ($mode == "search") {
                require("content/market/search.php");
            }

            //
            // Eigene Angebote anzeigen
            //
            elseif ($mode == "user_sell") {
                require("content/market/own.php");
            }

            //
            // Angebote aufgeben
            //
            else {
                require("content/market/new.php");
            }

            if ($cd_enabled) {
                countDown("cdcd", $cu->alliance->buildlist->getCooldown(ALLIANCE_MARKET_ID));
            }
        } else {
            info_msg("Dieses Gebäude ist noch bis " . df($market->deactivated) . " deaktiviert!");
        }
    }

    //
    // Meldung dass noch kein Marktplatz gebaut wurde
    //
    else {
        // Header
        echo '<h1>Marktplatz</h1>';
        echo $resourceBoxDrawer->getHTML($planet);
        info_msg("Der Marktplatz wurde noch nicht gebaut.");
    }
} else {
    echo '<h1>Marktplatz</h1>';
    echo $resourceBoxDrawer->getHTML($planet);
    info_msg("Der Marktplatz ist momentan im Spiel deaktiviert.");
}
