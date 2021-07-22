<?PHP

use EtoA\Market\MarketAuctionRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;

$xajax->register(XAJAX_FUNCTION, 'calcMarketRessPrice');
$xajax->register(XAJAX_FUNCTION, 'calcMarketRessBuy');
$xajax->register(XAJAX_FUNCTION, 'calcMarketShipPrice');
$xajax->register(XAJAX_FUNCTION, 'calcMarketShipBuy');
$xajax->register(XAJAX_FUNCTION, 'checkMarketAuctionFormular');
$xajax->register(XAJAX_FUNCTION, 'calcMarketAuctionTime');
$xajax->register(XAJAX_FUNCTION, 'calcMarketAuctionPrice');

$xajax->register(XAJAX_FUNCTION, 'marketSearch');
$xajax->register(XAJAX_FUNCTION, 'showAuctionDetail');

function marketSearch($form, $order = "distance", $orderDirection = 0)
{
    global $resNames, $resIcons, $app;
    ob_start();
    $ajax = new xajaxResponse();

    /** @var EntityRepository $entityRepository */
    $entityRepository = $app[EntityRepository::class];
    /** @var EntityService $entityService */
    $entityService = $app[EntityService::class];

    //
    // Resources
    // <editor-fold>
    if ($form['search_cat'] == "resources") {
        // Build resource type filter query
        $sfilter = "";
        $dfilter = "";
        foreach ($resNames as $rk => $rv) {
            if (isset($form['market_search_filter_supply_' . $rk]) && $form['market_search_filter_supply_' . $rk] == 1) {
                if ($sfilter != "")
                    $sfilter .= " OR ";
                $sfilter .= " sell_" . $rk . ">0 ";
            }
            if (isset($form['market_search_filter_demand_' . $rk]) && $form['market_search_filter_demand_' . $rk] == 1) {
                if ($dfilter != "")
                    $dfilter .= " OR ";
                $dfilter .= " buy_" . $rk . ">0 ";
            }
        }

        // Load current entity if payable check active
        if (isset($form['market_search_filter_payable']) && $form['market_search_filter_payable'] == 1) {
            $te = Entity::createFactoryById($_SESSION['cpid']);
        }

        $sql = "
        SELECT
            *
        FROM
            market_ressource
        WHERE
            buyable='1'
            AND (
            " . ($sfilter != "" ? $sfilter : 0) . "
            OR user_id = 0
            )
            AND (
            " . ($dfilter != "" ? $dfilter : 0) . "
            OR user_id = 0
            )
            AND user_id!='" . $_SESSION['user_id'] . "'
            AND (for_user='" . $_SESSION['user_id'] . "' OR for_user='0')
            AND (for_alliance='" . $_SESSION['alliance_id'] . "' OR for_alliance='0')
            ;";

        $res = dbquery($sql);

        $nr = mysql_num_rows($res);
        if ($nr > 0) {
            $currentEntity = $entityRepository->getEntity($_SESSION['cpid']);
            $tradeShip = new Ship(MARKET_SHIP_ID);
            $specialist = new Specialist(0, 0, $_SESSION['user_id']);

            $data = array();
            $i = 0;
            while ($arr = mysql_fetch_assoc($res)) {
                $show = true;
                if (isset($te)) {
                    foreach ($resNames as $rk => $rn) {
                        if ($te->resources[$rk] < $arr['buy_' . $rk]) {
                            $show = false;
                            break;
                        }
                    }
                }
                if ($show) {
                    $data[$i] = array();
                    $data[$i] = $arr;
                    $data[$i]['sell_total'] = 0;
                    $data[$i]['buy_total'] = 0;
                    $data[$i]['used_res'] = 0;
                    foreach ($resNames as $rk => $rn) {
                        $data[$i]['sell_total'] += $arr['sell_' . $rk];
                        $data[$i]['buy_total'] += $arr['buy_' . $rk];
                        if ((int) $arr['sell_' . $rk] + (int) $arr['buy_' . $rk] > 0)
                            $data[$i]['used_res']++;
                    }
                    $sellerEntity = $entityRepository->getEntity((int) $arr['entity_id']);
                    $dist = $entityService->distance($sellerEntity, $currentEntity);
                    $data[$i]['distance'] = $dist;
                    $data[$i]['duration'] = ceil($dist / ($tradeShip->speed * $specialist->tradeTime) * 3600 + $tradeShip->time2start + $tradeShip->time2land);
                    $i++;
                }
            }
            $offerCount = count($data);
            if ($offerCount > 0) {
                $sortOrder = $orderDirection > 0 ? SORT_DESC : SORT_ASC;
                $sort = [];
                foreach ($data as $key => $row) {
                    if ($order == "sell")
                        $sort[$key]  = $row['sell_total'];
                    elseif ($order == "buy")
                        $sort[$key]  = $row['buy_total'];
                    else
                        $sort[$key]  = $row['distance'];
                }
                array_multisort($sort, $sortOrder, $data);
            }


            $cres = dbquery("
            SELECT
                COUNT(id)
            FROM
                market_ressource
            WHERE
                buyable=1
                AND user_id!='" . $_SESSION['user_id'] . "'
                AND (for_user='" . $_SESSION['user_id'] . "' OR for_user='0')
                AND (for_alliance='" . $_SESSION['alliance_id'] . "' OR for_alliance='0')
            ;");
            $carr = mysql_fetch_row($cres);
            echo "<form action=\"?page=market&amp;mode=ressource\" method=\"post\" id=\"ress_buy_selector\">\n";
            checker_init();
            tableStart();
            echo "<thead><tr>
                <th class=\"infoboxtitle\" colspan=\"20\">Rohstoffangebote ($offerCount von " . $carr[0] . ")
                    <span id=\"market_search_loading\"><img src=\"images/loading.gif\" alt=\"loading\" /></span>
                </th></tr>";
            echo "<tr>
                            <th>Rohstoffe:</th>
                            <th><a href=\"javascript:sortSearch('sell'," . ($order == "sell" ? ($orderDirection + 1) % 2 : 0) . ")\">Angebot:</a></th>
                            <th><a href=\"javascript:sortSearch('buy'," . ($order == "buy" ? ($orderDirection + 1) % 2 : 0) . ")\">Preis:</a></th>
                            <th>Anbieter:</th>
                            <th><a href=\"javascript:sortSearch('distance'," . ($order == "distance" ? ($orderDirection + 1) % 2 : 0) . ")\">Entfernung:</a></th>
                            <th>Beschreibung:</th>
                            <th style=\"width:50px;\">Kaufen:</th>
                        </tr></thead>";
            $cnt = 0;
            foreach ($data as $arr) {
                $i = 0;

                // Reservation
                $reservation = "";
                $class = "";
                if ($arr['for_user'] != 0) {
                    $class = "top";
                    $reservation = "<span class=\"userAllianceMemberColor\">F&uuml;r dich reserviert</span>";
                } elseif ($arr['for_alliance'] != 0) {
                    $class = "top";
                    $reservation = "<span class=\"userAllianceMemberColor\">F&uuml;r Allianzmitglied reserviert</span>";
                }

                $cres = $arr['used_res'];

                echo '<tbody class="offer">';
                foreach ($resNames as $rk => $rn) {
                    if ((int) $arr['sell_' . $rk] + (int) $arr['buy_' . $rk] > 0) {
                        echo "<tr>
                                        <td class=\"rescolor" . $rk . " rname\">" . $resIcons[$rk] . "<b>" . $rn . "</b>:</td>
                                        <td class=\"rescolor" . $rk . " rsupp\">" . ($arr['sell_' . $rk] > 0 ? nf($arr['sell_' . $rk]) : '-') . "</td>
                                        <td class=\"rescolor" . $rk . " rdema\">" . ($arr['buy_' . $rk] > 0 ? nf($arr['buy_' . $rk]) : '-') . "</td>";
                        if ($i == 0) {

                            echo "<td rowspan=\"" . $cres . "\" class=\"usrinfo\">
                                                <a href=\"?page=userinfo&amp;id=" . $arr['user_id'] . "\">" . get_user_nick($arr['user_id']) . "</a></td>";
                            echo "<td rowspan=\"" . $cres . "\" class=\"duration\">
                                                " . tf($arr['duration']) . "
                                                </td>
                                                <td rowspan=\"" . $cres . "\">
                                                    " . $reservation . "<br />
                                                    <span class='rtext " . $class . "'  >" . stripslashes($arr['text']) . "</span>
                                                </td>
                                                <td rowspan=\"" . $cres . "\">
                                                    <input type=\"checkbox\" name=\"ressource_market_id[]\" id=\"ressource_market_id\" value=\"" . $arr['id'] . "\" /><br/><br/>
                                                </td>";
                        }
                        echo "</tr>";
                        $i++;
                    }
                }
                echo '</tbody>';
                $cnt++;
                // Setzt Lücke zwischen den Angeboten
                if ($cnt < $offerCount) {
                    echo "<tr class=\"spacer\">
                        <td colspan=\"7\" style=\"height:10px;background:#000\">&nbsp;</td>
                    </tr>";
                }
            }
            if ($i == 0) {
                echo "<tr>
                    <td colspan=\"7\"><i>Keine bezahlbaren Angebote vorhanden!</td>
                </tr>";
            }
            tableEnd();
            tableStart();
            echo "
                    <tr>
                        <td colspan=\"7\" id=\"ress_buy_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan=\"7\" style=\"text-align:center;vertical-align:middle;\">
                            <input type=\"submit\" name=\"ressource_submit\" id=\"ressource_submit\" value=\"Angebot annehmen\"/>
                        </td>
                    </tr>";
            echo "</form>";
            tableEnd();
        } else {
            error_msg("Keine Angebote vorhanden!", 1);
        }
    }
    // </editor-fold>

    //
    // Ships
    // <editor-fold>
    elseif ($form['search_cat'] == "ships") {

        echo "<form action=\"?page=market&amp;mode=ships\" method=\"post\" id=\"ship_buy_selector\">\n";
        checker_init();

        // Load current entity if payable check active

        if (isset($form['market_ship_search_filter_payable']) && $form['market_ship_search_filter_payable'] == 1) {
            $te = Entity::createFactoryById($_SESSION['cpid']);
        }

        $res = dbquery("
            SELECT
                *
            FROM
                market_ship
            WHERE
                buyable='1'
            AND user_id!='" . $_SESSION['user_id'] . "'
            AND (for_user='" . $_SESSION['user_id'] . "' OR for_user='0')
            AND (for_alliance='" . $_SESSION['alliance_id'] . "' OR for_alliance='0')
            ;");
        $cnt = 0;
        if (mysql_num_rows($res) > 0) {
            while ($arr = mysql_fetch_array($res)) {
                $show = true;
                if (isset($te)) {
                    foreach ($resNames as $rk => $rn) {
                        if ($te->resources[$rk] < $arr['costs_' . $rk]) {
                            $show = false;
                            break;
                        }
                    }
                }
                if ($show) {
                    if ($cnt == 0) {
                        tableStart();
                        echo "<thead>
                                    <tr><th class=\"infoboxtitle\" colspan=\"20\">Angebots&uuml;bersicht</th></tr>";
                        echo "	<tr>
                                        <th width=\"25%\">Angebot:</th>
                                        <th colspan=\"2\" width=\"25%\">Preis:</th>
                                        <th width=\"15%\">Anbieter:</th>
                                        <th width=\"25%\">Beschreibung:</th>
                                        <th width=\"10%\">Kaufen:</th>
                                    </tr></thead>";
                    }

                    $reservation = "";
                    $class = "";
                    if ($arr['for_user'] != 0) {
                        $class = "top";
                        $reservation = "<span class=\"userAllianceMemberColor\">F&uuml;r dich reserviert</span>";
                    } elseif ($arr['for_alliance'] != 0) {
                        $class = "top";
                        $reservation = "<span class=\"userAllianceMemberColor\">F&uuml;r Allianzmitglied reserviert</span>";
                    }

                    $i = 0;
                    $resCnt = count($resNames);

                    /** @var ShipDataRepository $shipRepository */
                    $shipRepository = $app[ShipDataRepository::class];
                    $shipNames = $shipRepository->getShipNames(true);

                    echo '<tbody class="offer">';
                    foreach ($resNames as $rk => $rn) {
                        echo "<tr>";
                        if ($i == 0) {
                            echo "<td rowspan=\"$resCnt\">" . $arr['count'] . " <a href=\"?page=help&site=shipyard&id=" . $arr['ship_id'] . "\">" . $shipNames[$arr['ship_id']] . "</a></td>";
                        }
                        echo "<td class=\"rescolor" . $rk . " rname\">" . $resIcons[$rk] . "<b>" . $rn . "</b>:</td>
                            <td class=\"rescolor" . $rk . " rdema\">" . nf($arr['costs_' . $rk]) . "</td>";
                        if ($i++ == 0) {
                            $tu = new User($arr['user_id']);
                            echo "<td rowspan=\"$resCnt\" class=\"usrinfo\">" . $tu->detailLink() . "</td>";
                            echo "<td rowspan=\"$resCnt\">" . $reservation . "<br /><span class='rtext " . $class . "'  >" . stripslashes($arr['text']) . "</span></td>";
                            echo "<td rowspan=\"$resCnt\">
                                    <input type=\"checkbox\" name=\"ship_market_id[]\" id=\"ship_market_id_" . $arr['id'] . "\" value=\"" . $arr['id'] . "\" onclick=\"xajax_calcMarketShipBuy(xajax.getFormValues('ship_buy_selector'));\" />
                                </td>";
                        }
                        echo "</tr>";
                    }
                    echo '</tbody>';

                    $cnt++;
                    if ($cnt < mysql_num_rows($res))
                        echo "<tr><td colspan=\"6\" style=\"height:10px;background:#000\"></td></tr>";
                }
            }
            if ($cnt > 0) {
                tableEnd();

                tableStart();
                echo "<tr>
                                    <td colspan=\"7\" id=\"ship_buy_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan=\"7\" style=\"text-align:center;vertical-align:middle;\">
                                        <input type=\"submit\" name=\"ship_submit\" id=\"ship_submit\" value=\"Angebot annehmen\" disabled=\"disabled\"/>
                                    </td>
                                </tr>";
                tableEnd();
            } else
                error_msg("Keine bezahlbaren Angebote vorhanden!", 1);
        } else {
            error_msg("Keine Angebote vorhanden!", 1);
        }
        echo "</form>";
    }
    // </editor-fold>



    // Auctions
    // <editor-fold>
    elseif ($form['search_cat'] == "auctions") {
        if (isset($_SESSION['auctionid']) && $_SESSION['auctionid'] > 0) {
            ob_end_clean();
            $ajax->script("xajax_showAuctionDetail(" . $_SESSION['auctionid'] . ");");
            $_SESSION['auctionid'] = 0;
            unset($_SESSION['auctionid']);
            return $ajax;
        }

        /** @var MarketAuctionRepository $marketAuctionRepository */
        $marketAuctionRepository = $app[MarketAuctionRepository::class];
        $auctions = $marketAuctionRepository->getBuyableAuctions((int) $_SESSION['user_id']);

        if (count($auctions) > 0) {
            tableStart("Offene Auktionen");
            // Header
            echo "<tr>
                <th>Angebot</th>
                <th>Beschreibung</th>
                <th style=\"width:100px;\">Angebotsende</th>
                <th style=\"width:50px;\">Gebote</th>
                <th colspan=\"2\">Aktuelles Gebot</th>
                </tr>";

            $cnt = 0;
            $acnts = array();
            $acnt = 0;
            foreach ($auctions as $auction) {
                //restliche zeit bis zum ende
                $rest_time = $auction->dateEnd - time();

                // Gibt Nachricht aus, wenn die Auktion beendet ist, aber noch kein Löschtermin festgelegt ist
                if ($rest_time <= 0) {
                    $rest_time = "Auktion beendet!";
                }
                // und sonst Zeit bis zum Ende anzeigen
                else {
                    $rest_time = tf($rest_time);
                }

                echo "<tr>
                        <td>";
                $sellResources = $auction->getSellResources();
                foreach ($resNames as $rk => $rn) {
                    if ($sellResources->get($rk) > 0) {
                        echo "<span class=\"rescolor" . $rk . "\">";
                        echo $resIcons[$rk] . $rn . ": " . nf($sellResources->get($rk)) . "</span><br style=\"clear:both;\" />";
                    }
                }
                echo "</td>
                        <td>" . $auction->text . "</td>
                        <td>$rest_time</td>
                        <td>" . $auction->bidCount . "</td>
                        <td>";
                $currencyResources = $auction->getCurrencyResources();
                $buyResources = $auction->getBuyResources();
                foreach ($resNames as $rk => $rn) {
                    if ($currencyResources->get($rk) > 0) {
                        echo "<span class=\"rescolor" . $rk . "\">";
                        echo $resIcons[$rk] . $rn . ": " . nf($buyResources->get($rk));
                        echo "</span><br style=\"clear:both;\" />";
                    }
                }
                echo "</td>";
                echo "<td style=\"width:100px;\">
                    <input type=\"button\" onclick=\"xajax_showAuctionDetail(" . $auction->id . ")\" value=\"Infos &amp; Bieten\" /></td>";
                echo "</td></tr>";
            }
            tableEnd();
        } else {
            error_msg("Keine Auktionen vorhanden!", 1);
        }
    }
    // </editor-fold>

    $ajax->assign("market_search_results", "innerHTML", ob_get_clean());
    $ajax->assign("market_search_loading", "style.display", "none");

    //jquery start
    $ajax->script('uname="' . $_SESSION['user_nick'] . '";jqinit();');

    return $ajax;
}

function showAuctionDetail($id)
{
    global $resNames, $app;
    ob_start();
    $ajax = new xajaxResponse();

    /** @var EntityRepository $entityRepository */
    $entityRepository = $app[EntityRepository::class];
    /** @var PlanetRepository $planetRepository */
    $planetRepository = $app[PlanetRepository::class];
    /** @var EntityService $entityService */
    $entityService = $app[EntityService::class];
    /** @var MarketAuctionRepository $marketAuctionRepository */
    $marketAuctionRepository = $app[MarketAuctionRepository::class];
    $auction = $marketAuctionRepository->getNonUserAuction((int) $id, (int) $_SESSION['user_id']);

    if ($auction !== null) {
        //restliche zeit bis zum ende
        $rest_time = $auction->dateEnd - time();

        // Gibt Nachricht aus, wenn die Auktion beendet ist, aber noch kein Löschtermin festgelegt ist
        if ($rest_time <= 0) {
            $rest_time_str = "Auktion beendet!";
        }
        // und sonst Zeit bis zum Ende anzeigen
        else {
            $rest_time_str = tf($rest_time);
        }

        $seller = new User($auction->userId);
        $bidder = new User($auction->currentBuyerId);
        $sellerEntity = $entityRepository->getEntity($auction->entityId);
        $ownEntity = $entityRepository->getEntity((int) $_SESSION['cpid']);
        $planet = $planetRepository->find((int) $_SESSION['cpid']);


        echo "<form action=\"?page=market&amp;mode=auction\" method=\"post\" name=\"auctionShowFormular\" id=\"auction_show_selector\">";
        checker_init();
        echo "<input type=\"hidden\" value=\"" . $auction->id . "\" name=\"auction_market_id\" id=\"auction_market_id\"/>";
        echo "<input type=\"hidden\" value=\"0\" name=\"auction_show_last_update\" id=\"auction_show_last_update\"/>";
        echo "<input type=\"hidden\" value=\"" . $rest_time . "\" name=\"auction_rest_time\" id=\"auction_rest_time\"/>";

        // Übergibt Daten an XAJAX
        $sellResources = $auction->getSellResources();
        $buyResources = $auction->getBuyResources();
        $currencyResources = $auction->getCurrencyResources();
        $ownResources = [
            0 => $planet->resMetal,
            1 => $planet->resCrystal,
            2 => $planet->resPlastic,
            3 => $planet->resFuel,
            4 => $planet->resFood,
        ];
        foreach ($resNames as $rk => $rn) {
            // Rohstoffe
            echo "<input type=\"hidden\" value=\"" . $ownResources[$rk] . "\" name=\"res_$rk\" id=\"res_$rk\"/>";
            // Angebot
            echo "<input type=\"hidden\" value=\"" . $sellResources->get($rk) . "\" name=\"sell_$rk\" id=\"sell_$rk\"/>";
            // Höchstgebot
            echo "<input type=\"hidden\" value=\"" . $buyResources->get($rk) . "\" name=\"buy_$rk\" id=\"buy_$rk\"/>";
            // Gewünschte Währung
            echo "<input type=\"hidden\" value=\"" . $currencyResources->get($rk) . "\" name=\"currency_$rk\" id=\"currency_$rk\"/>";
        }

        tableStart("Auktionsdetails");
        echo "<tr>";



        echo "
        <th style=\"width:150px;\">Verkäufer:</th>
        <td>" . $seller . "</td>
        <th style=\"width:200px;\">Aktueller Höchstbietender:</th>
        <td>" . $bidder . "</td>

        <td id=\"rest_time_str\" rowspan=\"2\" style=\"text-align:center;vertical-align:middle\">
        <span style=\"font-size:16pt;\" >" . $rest_time_str . "</span></td>
        </tr>

        <tr>
        <th>Entfernung:</th>
        <td>" . $entityService->distance($sellerEntity, $ownEntity) . " AE</td>
        <th>Anzahl Gebote:</th>
        <td>" . $auction->bidCount . "</td>
        </tr>";

        if ($auction->text != "")
            echo "<tr><td colspan=\"5\">" . (isset($auction->text) ? stripslashes($auction->text) : 'Keine Beschreibung vorhanden') . "</td></tr>";
        tableEnd();

        // Angebots/Preis Maske
        if ($rest_time > 0) {
            tableStart();
            echo "<tr>
                <th style=\"width:15%;vertical-align:middle;\" colspan=\"2\">Rohstoff</th>
                <th style=\"width:5%;text-align:center;vertical-align:middle;\">Kurs</th>
                <th style=\"width:15%;vertical-align:middle;\">Höchstgebot</th>
                <th style=\"width:15%;vertical-align:middle;\">Bieten</th>
                <th style=\"width:35%;vertical-align:middle;\">Min./Max.</th>
            </tr>";

            $factor = array(MARKET_METAL_FACTOR, MARKET_CRYSTAL_FACTOR, MARKET_PLASTIC_FACTOR, MARKET_FUEL_FACTOR, MARKET_FOOD_FACTOR);
            foreach ($resNames as $rk => $rn) {
                echo "<tr>
                <th style=\"vertical-align:middle;\" class=\"rescolor" . $rk . "\">" . $rn . ":</th>
                <td id=\"auction_sell_metal_field\" style=\"vertical-align:middle;\"  class=\"rescolor" . $rk . "\">
                    " . nf($sellResources->get($rk)) . "
                </td>
                <th style=\"text-align:center;vertical-align:middle;\">" . $factor[$rk] . "</th>
                <td id=\"auction_buy_" . $rk . "_field\" style=\"vertical-align:middle;\">
                    " . nf($buyResources->get($rk)) . "
                </td>
                <td style=\"vertical-align:middle;\">";
                if ($currencyResources->get($rk) == 1) {

                    //calcMarketAuctionPrice(0);
                    echo "<input type=\"text\" value=\"" . nf($buyResources->get($rk)) . "\" name=\"new_buy_" . $rk . "\" id=\"new_buy_" . $rk . "\" size=\"9\" maxlength=\"15\" onkeyup=\"xajax_calcMarketAuctionPrice(xajax.getFormValues('auction_show_selector'));FormatNumber(this.id,this.value," . $ownResources[$rk] . ",'','');\"/>";
                } else {
                    echo " - ";
                }
                echo "</td>
                    <th id=\"auction_min_max_" . $rk . "\" style=\"vertical-align:middle;\"> - </th>
                </tr>";
            }
            // Status Nachricht (Ajax Überprüfungstext)
            echo "<tr>
                            <td colspan=\"6\" id=\"auction_check_message\" style=\"text-align:center;vertical-align:middle;height:30px;\">&nbsp;</td>
                        </tr>";

            tableEnd();
            echo "<p><input type=\"submit\" name=\"submit_auction_bid\" id=\"submit_auction_bid\" style=\"color: rgb(255, 0, 0);\" disabled=\"disabled\" value=\"Gebot abgeben\" /> ";
            echo "<input type=\"button\" onclick=\"applySearchFilter();\" value=\"Zurück\" /></p>";
        } else
            echo "<p><input type=\"button\" onclick=\"applySearchFilter();\" value=\"Zurück\" /></p>";
        echo "</form>";
    } else {
        error_msg("Angebot nicht mehr vorhanden!");
        echo "<p><input type=\"button\" onclick=\"applySearchFilter();\" value=\"Zurück\" /></p>";
    }


    $ajax->assign("market_search_results", "innerHTML", ob_get_clean());
    $ajax->assign("market_search_loading", "style.display", "none");
    return $ajax;
}


function calcMarketRessPrice($val, $last_update = 0)
{
    ob_start();
    $objResponse = new xajaxResponse();

    // Eingaben wurden noch nicht geprüft
    $objResponse->assign("ress_check_submit", "value", 0);

    // Stellt "Value-Variable" auf 0 wenn diese noch nicht vorhanden ist
    if ($val['ress_buy_metal'] == "") {
        $val['ress_buy_metal'] = 0;
    }
    if ($val['ress_buy_crystal'] == "") {
        $val['ress_buy_crystal'] = 0;
    }
    if ($val['ress_buy_plastic'] == "") {
        $val['ress_buy_plastic'] = 0;
    }
    if ($val['ress_buy_fuel'] == "") {
        $val['ress_buy_fuel'] = 0;
    }
    if ($val['ress_buy_food'] == "") {
        $val['ress_buy_food'] = 0;
    }

    $val['ress_sell_metal'] = min(nf_back($val['ress_sell_metal']), floor($val['res_metal'] / MARKET_SELL_TAX));
    $val['ress_sell_crystal'] = min(nf_back($val['ress_sell_crystal']), floor($val['res_crystal'] / MARKET_SELL_TAX));
    $val['ress_sell_plastic'] = min(nf_back($val['ress_sell_plastic']), floor($val['res_plastic'] / MARKET_SELL_TAX));
    $val['ress_sell_fuel'] = min(nf_back($val['ress_sell_fuel']), floor($val['res_fuel'] / MARKET_SELL_TAX));
    $val['ress_sell_food'] = min(nf_back($val['ress_sell_food']), floor($val['res_food'] / MARKET_SELL_TAX));

    $val['ress_buy_metal'] = nf_back($val['ress_buy_metal']);
    $val['ress_buy_crystal'] = nf_back($val['ress_buy_crystal']);
    $val['ress_buy_plastic'] = nf_back($val['ress_buy_plastic']);
    $val['ress_buy_fuel'] = nf_back($val['ress_buy_fuel']);
    $val['ress_buy_food'] = nf_back($val['ress_buy_food']);


    //
    // Errechnet und formatiert Preise
    //

    //
    // Titan
    //
    if ($val['ress_sell_metal'] == 0) {
        // MaxBetrag
        // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
        $ress_buy_metal_max =    $val['ress_sell_metal'] / MARKET_METAL_FACTOR * MARKET_METAL_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_crystal'] / MARKET_METAL_FACTOR * MARKET_CRYSTAL_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_plastic'] / MARKET_METAL_FACTOR * MARKET_PLASTIC_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_fuel'] / MARKET_METAL_FACTOR * MARKET_FUEL_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_food'] / MARKET_METAL_FACTOR * MARKET_FOOD_FACTOR * RESS_PRICE_FACTOR_MAX;
        // Errechnet Grundbetrag abzüglich bereits eingebener Preise
        $ress_buy_metal_max =  $ress_buy_metal_max
            -    $val['ress_buy_metal'] * MARKET_METAL_FACTOR / MARKET_METAL_FACTOR
            - $val['ress_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_METAL_FACTOR
            - $val['ress_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_METAL_FACTOR
            - $val['ress_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_METAL_FACTOR
            - $val['ress_buy_food'] * MARKET_FOOD_FACTOR / MARKET_METAL_FACTOR;
        $log_ress_buy_metal_max = ceil($ress_buy_metal_max);        //Der Effektivwert, dieser wird nicht angepasst
        $ress_buy_metal_max = floor($ress_buy_metal_max);    //Rundet Betrag auf die nächst kleinere Ganzzahl

        // MinBetrag
        // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
        $ress_buy_metal_min =    $val['ress_sell_metal'] / MARKET_METAL_FACTOR * MARKET_METAL_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_crystal'] / MARKET_METAL_FACTOR * MARKET_CRYSTAL_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_plastic'] / MARKET_METAL_FACTOR * MARKET_PLASTIC_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_fuel'] / MARKET_METAL_FACTOR * MARKET_FUEL_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_food'] / MARKET_METAL_FACTOR * MARKET_FOOD_FACTOR * RESS_PRICE_FACTOR_MIN;
        // Errechnet Grundbetrag abzüglich bereits eingebener Preise
        $ress_buy_metal_min =  $ress_buy_metal_min
            -    $val['ress_buy_metal'] * MARKET_METAL_FACTOR / MARKET_METAL_FACTOR
            - $val['ress_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_METAL_FACTOR
            - $val['ress_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_METAL_FACTOR
            - $val['ress_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_METAL_FACTOR
            - $val['ress_buy_food'] * MARKET_FOOD_FACTOR / MARKET_METAL_FACTOR;
        $ress_buy_metal_min = ceil($ress_buy_metal_min);    //Rundet Betrag auf die nächste höhere Ganzzahl
        $log_ress_buy_metal_min = $ress_buy_metal_min;        //Der Effektivwert, dieser wird nicht angepasst

        if ($ress_buy_metal_max <= 0) {
            $ress_buy_metal_max = 0;
        }

        if ($ress_buy_metal_min <= 0) {
            $ress_buy_metal_min = 0;
        }

        // Generiert Link mit dem Min./Max. Betrag. Bei draufklcikt wird der Wert sofort ins Feld geschrieben und dannach Formatiert
        $out_ress_min_max_metal = "<a href=\"javascript:;\" onclick=\"document.getElementById('ress_buy_metal').value=" . ($val['ress_buy_metal'] + $ress_buy_metal_min) . ";xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));xajax_formatNumbers('ress_buy_metal','" . ($val['ress_buy_metal'] + $ress_buy_metal_min) . "',1,'');\">+" . nf($ress_buy_metal_min) . "</a> / <a href=\"javascript:;\" onclick=\"document.getElementById('ress_buy_metal').value=" . ($val['ress_buy_metal'] + $ress_buy_metal_max) . ";xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));xajax_formatNumbers('ress_buy_metal','" . ($val['ress_buy_metal'] + $ress_buy_metal_max) . "',1,'');\">+" . nf($ress_buy_metal_max) . "</a>";

        // Gibt das Preisfeld frei
        $objResponse->assign("ress_buy_metal", "disabled", false);
    } else {
        $out_ress_min_max_metal = '';
        // Sperrt das Preisfeld
        $objResponse->assign("ress_buy_metal", "disabled", true);
        $objResponse->assign("ress_buy_metal", "value", 0);
    }


    //
    // Silizium
    //
    if ($val['ress_sell_crystal'] == 0) {
        // MaxBetrag
        // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
        $ress_buy_crystal_max =    $val['ress_sell_metal'] / MARKET_CRYSTAL_FACTOR * MARKET_METAL_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_crystal'] / MARKET_CRYSTAL_FACTOR * MARKET_CRYSTAL_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_plastic'] / MARKET_CRYSTAL_FACTOR * MARKET_PLASTIC_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_fuel'] / MARKET_CRYSTAL_FACTOR * MARKET_FUEL_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_food'] / MARKET_CRYSTAL_FACTOR * MARKET_FOOD_FACTOR * RESS_PRICE_FACTOR_MAX;
        // Errechnet Grundbetrag abzüglich bereits eingebener Preise
        $ress_buy_crystal_max =  $ress_buy_crystal_max
            -    $val['ress_buy_metal'] * MARKET_METAL_FACTOR / MARKET_CRYSTAL_FACTOR
            - $val['ress_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_CRYSTAL_FACTOR
            - $val['ress_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_CRYSTAL_FACTOR
            - $val['ress_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_CRYSTAL_FACTOR
            - $val['ress_buy_food'] * MARKET_FOOD_FACTOR / MARKET_CRYSTAL_FACTOR;
        $log_ress_buy_crystal_max = ceil($ress_buy_crystal_max);        //Der Effektivwert, dieser wird nicht angepasst
        $ress_buy_crystal_max = floor($ress_buy_crystal_max);    //Rundet Betrag auf die nächst kleiner Ganzzahl

        // MinBetrag
        // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
        $ress_buy_crystal_min =    $val['ress_sell_metal'] / MARKET_CRYSTAL_FACTOR * MARKET_METAL_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_crystal'] / MARKET_CRYSTAL_FACTOR * MARKET_CRYSTAL_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_plastic'] / MARKET_CRYSTAL_FACTOR * MARKET_PLASTIC_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_fuel'] / MARKET_CRYSTAL_FACTOR * MARKET_FUEL_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_food'] / MARKET_CRYSTAL_FACTOR * MARKET_FOOD_FACTOR * RESS_PRICE_FACTOR_MIN;
        // Errechnet Grundbetrag abzüglich bereits eingebener Preise
        $ress_buy_crystal_min =  $ress_buy_crystal_min
            -    $val['ress_buy_metal'] * MARKET_METAL_FACTOR / MARKET_CRYSTAL_FACTOR
            - $val['ress_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_CRYSTAL_FACTOR
            - $val['ress_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_CRYSTAL_FACTOR
            - $val['ress_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_CRYSTAL_FACTOR
            - $val['ress_buy_food'] * MARKET_FOOD_FACTOR / MARKET_CRYSTAL_FACTOR;
        $ress_buy_crystal_min = ceil($ress_buy_crystal_min);  //Rundet Betrag auf die nächste höhere Ganzzahl
        $log_ress_buy_crystal_min = $ress_buy_crystal_min;        //Der Effektivwert, dieser wird nicht angepasst

        if ($ress_buy_crystal_max <= 0) {
            $ress_buy_crystal_max = 0;
        }

        if ($ress_buy_crystal_min <= 0) {
            $ress_buy_crystal_min = 0;
        }

        // Generiert Link mit dem Min./Max. Betrag. Bei draufklcikt wird der Wert sofort ins Feld geschrieben und dannach Formatiert
        $out_ress_min_max_crystal = "<a href=\"javascript:;\" onclick=\"document.getElementById('ress_buy_crystal').value=" . ($val['ress_buy_crystal'] + $ress_buy_crystal_min) . ";xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));xajax_formatNumbers('ress_buy_crystal','" . ($val['ress_buy_crystal'] + $ress_buy_crystal_min) . "',1,'');\">+" . nf($ress_buy_crystal_min) . "</a> / <a href=\"javascript:;\" onclick=\"document.getElementById('ress_buy_crystal').value=" . ($val['ress_buy_crystal'] + $ress_buy_crystal_max) . ";xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));xajax_formatNumbers('ress_buy_crystal','" . ($val['ress_buy_crystal'] + $ress_buy_crystal_max) . "',1,'');\">+" . nf($ress_buy_crystal_max) . "</a>";

        // Gibt das Preisfeld frei
        $objResponse->assign("ress_buy_crystal", "disabled", false);
    } else {
        $out_ress_min_max_crystal = '';
        // Sperrt das Preisfeld
        $objResponse->assign("ress_buy_crystal", "disabled", true);
        $objResponse->assign("ress_buy_crystal", "value", 0);
    }



    //
    // PVC
    //
    if ($val['ress_sell_plastic'] == 0) {
        // MaxBetrag
        // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
        $ress_buy_plastic_max =    $val['ress_sell_metal'] / MARKET_PLASTIC_FACTOR * MARKET_METAL_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_crystal'] / MARKET_PLASTIC_FACTOR * MARKET_CRYSTAL_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_plastic'] / MARKET_PLASTIC_FACTOR * MARKET_PLASTIC_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_fuel'] / MARKET_PLASTIC_FACTOR * MARKET_FUEL_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_food'] / MARKET_PLASTIC_FACTOR * MARKET_FOOD_FACTOR * RESS_PRICE_FACTOR_MAX;
        // Errechnet Grundbetrag abzüglich bereits eingebener Preise
        $ress_buy_plastic_max =  $ress_buy_plastic_max
            -    $val['ress_buy_metal'] * MARKET_METAL_FACTOR / MARKET_PLASTIC_FACTOR
            - $val['ress_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_PLASTIC_FACTOR
            - $val['ress_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_PLASTIC_FACTOR
            - $val['ress_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_PLASTIC_FACTOR
            - $val['ress_buy_food'] * MARKET_FOOD_FACTOR / MARKET_PLASTIC_FACTOR;
        $log_ress_buy_plastic_max = ceil($ress_buy_plastic_max);        //Der Effektivwert, dieser wird nicht angepasst
        $ress_buy_plastic_max = floor($ress_buy_plastic_max);    //Rundet Betrag auf die nächst kleiner Ganzzahl

        // MinBetrag
        // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
        $ress_buy_plastic_min =    $val['ress_sell_metal'] / MARKET_PLASTIC_FACTOR * MARKET_METAL_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_crystal'] / MARKET_PLASTIC_FACTOR * MARKET_CRYSTAL_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_plastic'] / MARKET_PLASTIC_FACTOR * MARKET_PLASTIC_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_fuel'] / MARKET_PLASTIC_FACTOR * MARKET_FUEL_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_food'] / MARKET_PLASTIC_FACTOR * MARKET_FOOD_FACTOR * RESS_PRICE_FACTOR_MIN;
        // Errechnet Grundbetrag abzüglich bereits eingebener Preise
        $ress_buy_plastic_min =  $ress_buy_plastic_min
            -    $val['ress_buy_metal'] * MARKET_METAL_FACTOR / MARKET_PLASTIC_FACTOR
            - $val['ress_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_PLASTIC_FACTOR
            - $val['ress_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_PLASTIC_FACTOR
            - $val['ress_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_PLASTIC_FACTOR
            - $val['ress_buy_food'] * MARKET_FOOD_FACTOR / MARKET_PLASTIC_FACTOR;
        $ress_buy_plastic_min = ceil($ress_buy_plastic_min);  //Rundet Betrag auf die nächste höhere Ganzzahl
        $log_ress_buy_plastic_min = $ress_buy_plastic_min;        //Der Effektivwert, dieser wird nicht angepasst

        if ($ress_buy_plastic_max <= 0) {
            $ress_buy_plastic_max = 0;
        }

        if ($ress_buy_plastic_min <= 0) {
            $ress_buy_plastic_min = 0;
        }

        // Generiert Link mit dem Min./Max. Betrag. Bei draufklcikt wird der Wert sofort ins Feld geschrieben und dannach Formatiert
        $out_ress_min_max_plastic = "<a href=\"javascript:;\" onclick=\"document.getElementById('ress_buy_plastic').value=" . ($val['ress_buy_plastic'] + $ress_buy_plastic_min) . ";xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));xajax_formatNumbers('ress_buy_plastic','" . ($val['ress_buy_plastic'] + $ress_buy_plastic_min) . "',1,'');\">+" . nf($ress_buy_plastic_min) . "</a> / <a href=\"javascript:;\" onclick=\"document.getElementById('ress_buy_plastic').value=" . ($val['ress_buy_plastic'] + $ress_buy_plastic_max) . ";xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));xajax_formatNumbers('ress_buy_plastic','" . ($val['ress_buy_plastic'] + $ress_buy_plastic_max) . "',1,'');\">+" . nf($ress_buy_plastic_max) . "</a>";

        // Gibt das Preisfeld frei
        $objResponse->assign("ress_buy_plastic", "disabled", false);
    } else {
        $out_ress_min_max_plastic = '';
        // Sperrt das Preisfeld
        $objResponse->assign("ress_buy_plastic", "disabled", true);
        $objResponse->assign("ress_buy_plastic", "value", 0);
    }



    //
    // Tritium
    //
    if ($val['ress_sell_fuel'] == 0) {
        // MaxBetrag
        // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
        $ress_buy_fuel_max =    $val['ress_sell_metal'] / MARKET_FUEL_FACTOR * MARKET_METAL_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_crystal'] / MARKET_FUEL_FACTOR * MARKET_CRYSTAL_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_plastic'] / MARKET_FUEL_FACTOR * MARKET_PLASTIC_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_fuel'] / MARKET_FUEL_FACTOR * MARKET_FUEL_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_food'] / MARKET_FUEL_FACTOR * MARKET_FOOD_FACTOR * RESS_PRICE_FACTOR_MAX;
        // Errechnet Grundbetrag abzüglich bereits eingebener Preise
        $ress_buy_fuel_max =  $ress_buy_fuel_max
            -    $val['ress_buy_metal'] * MARKET_METAL_FACTOR / MARKET_FUEL_FACTOR
            - $val['ress_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_FUEL_FACTOR
            - $val['ress_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_FUEL_FACTOR
            - $val['ress_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_FUEL_FACTOR
            - $val['ress_buy_food'] * MARKET_FOOD_FACTOR / MARKET_FUEL_FACTOR;
        $log_ress_buy_fuel_max = ceil($ress_buy_fuel_max);        //Der Effektivwert, dieser wird nicht angepasst
        $ress_buy_fuel_max = floor($ress_buy_fuel_max);    //Rundet Betrag auf die nächst kleiner Ganzzahl

        // MinBetrag
        // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
        $ress_buy_fuel_min =    $val['ress_sell_metal'] / MARKET_FUEL_FACTOR * MARKET_METAL_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_crystal'] / MARKET_FUEL_FACTOR * MARKET_CRYSTAL_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_plastic'] / MARKET_FUEL_FACTOR * MARKET_PLASTIC_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_fuel'] / MARKET_FUEL_FACTOR * MARKET_FUEL_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_food'] / MARKET_FUEL_FACTOR * MARKET_FOOD_FACTOR * RESS_PRICE_FACTOR_MIN;
        // Errechnet Grundbetrag abzüglich bereits eingebener Preise
        $ress_buy_fuel_min =  $ress_buy_fuel_min
            -    $val['ress_buy_metal'] * MARKET_METAL_FACTOR / MARKET_FUEL_FACTOR
            - $val['ress_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_FUEL_FACTOR
            - $val['ress_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_FUEL_FACTOR
            - $val['ress_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_FUEL_FACTOR
            - $val['ress_buy_food'] * MARKET_FOOD_FACTOR / MARKET_FUEL_FACTOR;
        $ress_buy_fuel_min = ceil($ress_buy_fuel_min);  //Rundet Betrag auf die nächste höhere Ganzzahl
        $log_ress_buy_fuel_min = $ress_buy_fuel_min;        //Der Effektivwert, dieser wird nicht angepasst

        if ($ress_buy_fuel_max <= 0) {
            $ress_buy_fuel_max = 0;
        }

        if ($ress_buy_fuel_min <= 0) {
            $ress_buy_fuel_min = 0;
        }

        // Generiert Link mit dem Min./Max. Betrag. Bei draufklcikt wird der Wert sofort ins Feld geschrieben und dannach Formatiert
        $out_ress_min_max_fuel = "<a href=\"javascript:;\" onclick=\"document.getElementById('ress_buy_fuel').value=" . ($val['ress_buy_fuel'] + $ress_buy_fuel_min) . ";xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));xajax_formatNumbers('ress_buy_fuel','" . ($val['ress_buy_fuel'] + $ress_buy_fuel_min) . "',1,'');\">+" . nf($ress_buy_fuel_min) . "</a> / <a href=\"javascript:;\" onclick=\"document.getElementById('ress_buy_fuel').value=" . ($val['ress_buy_fuel'] + $ress_buy_fuel_max) . ";xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));xajax_formatNumbers('ress_buy_fuel','" . ($val['ress_buy_fuel'] + $ress_buy_fuel_max) . "',1,'');\">+" . nf($ress_buy_fuel_max) . "</a>";

        // Gibt das Preisfeld frei
        $objResponse->assign("ress_buy_fuel", "disabled", false);
    } else {
        $out_ress_min_max_fuel = '';
        // Sperrt das Preisfeld
        $objResponse->assign("ress_buy_fuel", "disabled", true);
        $objResponse->assign("ress_buy_fuel", "value", 0);
    }



    //
    // Nahrung
    //
    if ($val['ress_sell_food'] == 0) {
        // MaxBetrag
        // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
        $ress_buy_food_max =    $val['ress_sell_metal'] / MARKET_FOOD_FACTOR * MARKET_METAL_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_crystal'] / MARKET_FOOD_FACTOR * MARKET_CRYSTAL_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_plastic'] / MARKET_FOOD_FACTOR * MARKET_PLASTIC_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_fuel'] / MARKET_FOOD_FACTOR * MARKET_FUEL_FACTOR * RESS_PRICE_FACTOR_MAX
            + $val['ress_sell_food'] / MARKET_FOOD_FACTOR * MARKET_FOOD_FACTOR * RESS_PRICE_FACTOR_MAX;
        // Errechnet Grundbetrag abzüglich bereits eingebener Preise
        $ress_buy_food_max =  $ress_buy_food_max
            -    $val['ress_buy_metal'] * MARKET_METAL_FACTOR / MARKET_FOOD_FACTOR
            - $val['ress_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_FOOD_FACTOR
            - $val['ress_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_FOOD_FACTOR
            - $val['ress_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_FOOD_FACTOR
            - $val['ress_buy_food'] * MARKET_FOOD_FACTOR / MARKET_FOOD_FACTOR;
        $log_ress_buy_food_max = ceil($ress_buy_food_max);        //Der Effektivwert, dieser wird nicht angepasst
        $ress_buy_food_max = floor($ress_buy_food_max);    //Rundet Betrag auf die nächst kleiner Ganzzahl


        // MinBetrag
        // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
        $ress_buy_food_min =    $val['ress_sell_metal'] / MARKET_FOOD_FACTOR * MARKET_METAL_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_crystal'] / MARKET_FOOD_FACTOR * MARKET_CRYSTAL_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_plastic'] / MARKET_FOOD_FACTOR * MARKET_PLASTIC_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_fuel'] / MARKET_FOOD_FACTOR * MARKET_FUEL_FACTOR * RESS_PRICE_FACTOR_MIN
            + $val['ress_sell_food'] / MARKET_FOOD_FACTOR * MARKET_FOOD_FACTOR * RESS_PRICE_FACTOR_MIN;
        // Errechnet Grundbetrag abzüglich bereits eingebener Preise
        $ress_buy_food_min =  $ress_buy_food_min
            -    $val['ress_buy_metal'] * MARKET_METAL_FACTOR / MARKET_FOOD_FACTOR
            - $val['ress_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_FOOD_FACTOR
            - $val['ress_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_FOOD_FACTOR
            - $val['ress_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_FOOD_FACTOR
            - $val['ress_buy_food'] * MARKET_FOOD_FACTOR / MARKET_FOOD_FACTOR;
        $ress_buy_food_min = ceil($ress_buy_food_min);  //Rundet Betrag auf die nächste höhere Ganzzahl
        $log_ress_buy_food_min = $ress_buy_food_min;        //Der Effektivwert, dieser wird nicht angepasst

        if ($ress_buy_food_max <= 0) {
            $ress_buy_food_max = 0;
        }

        if ($ress_buy_food_min <= 0) {
            $ress_buy_food_min = 0;
        }

        // Generiert Link mit dem Min./Max. Betrag. Bei draufklcikt wird der Wert sofort ins Feld geschrieben und dannach Formatiert
        $out_ress_min_max_food = "<a href=\"javascript:;\" onclick=\"document.getElementById('ress_buy_food').value=" . ($val['ress_buy_food'] + $ress_buy_food_min) . ";xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));xajax_formatNumbers('ress_buy_food','" . ($val['ress_buy_food'] + $ress_buy_food_min) . "',1,'');\">+" . nf($ress_buy_food_min) . "</a> / <a href=\"javascript:;\" onclick=\"document.getElementById('ress_buy_food').value=" . ($val['ress_buy_food'] + $ress_buy_food_max) . ";xajax_calcMarketRessPrice(xajax.getFormValues('ress_selector'));xajax_formatNumbers('ress_buy_food','" . ($val['ress_buy_food'] + $ress_buy_food_max) . "',1,'');\">+" . nf($ress_buy_food_max) . "</a>";

        // Gibt das Preisfeld frei
        $objResponse->assign("ress_buy_food", "disabled", false);
    } else {
        // Sperrt das Preisfeld
        $out_ress_min_max_food = '';
        $objResponse->assign("ress_buy_food", "disabled", true);
        $objResponse->assign("ress_buy_food", "value", 0);
    }


    //
    // End Prüfung ob Angebot OK ist
    //

    // 0 Rohstoffe angegeben
    if (
        $val['ress_sell_metal'] <= 0
        && $val['ress_sell_crystal'] <= 0
        && $val['ress_sell_plastic'] <= 0
        && $val['ress_sell_fuel'] <= 0
        && $val['ress_sell_food'] <= 0
    ) {
        $out_check_message = "<div style=\"color:red;font-weight:bold;\">Gib ein Angebot ein!</div>";

        $objResponse->assign("ressource_sell_submit", "disabled", true);
        $objResponse->assign("ressource_sell_submit", "style.color", '#f00');
    }
    // Alle Rohstoffe angegeben (und somit kein Preis festgelegt)
    elseif (
        $val['ress_sell_metal'] > 0
        && $val['ress_sell_crystal'] > 0
        && $val['ress_sell_plastic'] > 0
        && $val['ress_sell_fuel'] > 0
        && $val['ress_sell_food'] > 0
    ) {
        $out_check_message = "<div style=\"color:red;font-weight:bold;\">Das Angebot muss einen Preis haben!</div>";

        $objResponse->assign("ressource_sell_submit", "disabled", true);
        $objResponse->assign("ressource_sell_submit", "style.color", '#f00');
    }
    // Zu hohe Preise
    elseif (($log_ress_buy_metal_max ?? 0) < 0
        || ($log_ress_buy_crystal_max ?? 0) < 0
        || ($log_ress_buy_plastic_max ?? 0) < 0
        || ($log_ress_buy_fuel_max ?? 0) < 0
        || ($log_ress_buy_food_max ?? 0) < 0
    ) {
        $out_check_message = "<div style=\"color:red;font-weight:bold;\">Die Preise sind zu hoch!</div>";

        $objResponse->assign("ressource_sell_submit", "disabled", true);
        $objResponse->assign("ressource_sell_submit", "style.color", '#f00');
    }
    // Zu niedrige Preise
    elseif (($log_ress_buy_metal_min ?? 0) > 0
        || ($log_ress_buy_crystal_min ?? 0) > 0
        || ($log_ress_buy_plastic_min ?? 0) > 0
        || ($log_ress_buy_fuel_min ?? 0) > 0
        || ($log_ress_buy_food_min ?? 0) > 0
    ) {
        $out_check_message = "<div style=\"color:red;font-weight:bold;\">Die Preise sind zu niedrig!</div>";

        $objResponse->assign("ressource_sell_submit", "disabled", true);
        $objResponse->assign("ressource_sell_submit", "style.color", '#f00');
    }
    // Zu wenig Rohstoffe auf dem Planeten
    elseif (
        $val['ress_sell_metal'] * MARKET_SELL_TAX > $val['res_metal']
        || $val['ress_sell_crystal'] * MARKET_SELL_TAX > $val['res_crystal']
        || $val['ress_sell_plastic'] * MARKET_SELL_TAX > $val['res_plastic']
        || $val['ress_sell_fuel'] * MARKET_SELL_TAX > $val['res_fuel']
        || $val['ress_sell_food'] * MARKET_SELL_TAX > $val['res_food']
    ) {
        $out_check_message = "<div style=\"color:red;font-weight:bold;\">Es sind zu wenig Rohstoffe vorhanden! (Beachte Verkaufsgebühr)</div>";

        $objResponse->assign("ressource_sell_submit", "disabled", true);
        $objResponse->assign("ressource_sell_submit", "style.color", '#f00');
    }
    // Unerlaubte Zeichen im Werbetext
    elseif (check_illegal_signs($val['ressource_text']) != "") {
        $out_check_message = "<div style=\"color:red;font-weight:bold;\">Unerlaubte Zeichen im Werbetext (" . check_illegal_signs("><$") . ")!</div>";

        $objResponse->assign("ressource_sell_submit", "disabled", true);
        $objResponse->assign("ressource_sell_submit", "style.color", '#f00');
    }
    // Angebot ist OK
    else {
        // Rechnet gesamt Verkaufsgebühren
        $sell_tax = $val['ress_sell_metal'] * (MARKET_SELL_TAX - 1)
            + $val['ress_sell_crystal'] * (MARKET_SELL_TAX - 1)
            + $val['ress_sell_plastic'] * (MARKET_SELL_TAX - 1)
            + $val['ress_sell_fuel'] * (MARKET_SELL_TAX - 1)
            + $val['ress_sell_food'] * (MARKET_SELL_TAX - 1);

        $out_check_message = "<div style=\"color:#0f0;font-weight:bold;\">OK!<br>Verkaufsgebühren: " . nf($sell_tax) . " t</div>";
        $objResponse->assign("ressource_sell_submit", "disabled", false);
        $objResponse->assign("ressource_sell_submit", "style.color", '#0f0');

        // XAJAX bestätigt die Korrektheit/Legalität der Eingaben
        $objResponse->assign("ress_check_submit", "value", 1);
    }

    // Bestätigt, dass XAJAX das Formular vor dem Absenden nochmal kontrolliert hat
    $objResponse->assign("ress_last_update", "value", $last_update);


    // XAJAX ändert Daten
    $objResponse->assign("ress_min_max_metal", "innerHTML", $out_ress_min_max_metal);
    $objResponse->assign("ress_min_max_crystal", "innerHTML", $out_ress_min_max_crystal);
    $objResponse->assign("ress_min_max_plastic", "innerHTML", $out_ress_min_max_plastic);
    $objResponse->assign("ress_min_max_fuel", "innerHTML", $out_ress_min_max_fuel);
    $objResponse->assign("ress_min_max_food", "innerHTML", $out_ress_min_max_food);

    $objResponse->assign("ress_sell_metal", "value", nf($val['ress_sell_metal']));
    $objResponse->assign("ress_sell_crystal", "value", nf($val['ress_sell_crystal']));
    $objResponse->assign("ress_sell_plastic", "value", nf($val['ress_sell_plastic']));
    $objResponse->assign("ress_sell_fuel", "value", nf($val['ress_sell_fuel']));
    $objResponse->assign("ress_sell_food", "value", nf($val['ress_sell_food']));

    $objResponse->assign("ress_buy_metal", "value", nf($val['ress_buy_metal']));
    $objResponse->assign("ress_buy_crystal", "value", nf($val['ress_buy_crystal']));
    $objResponse->assign("ress_buy_plastic", "value", nf($val['ress_buy_plastic']));
    $objResponse->assign("ress_buy_fuel", "value", nf($val['ress_buy_fuel']));
    $objResponse->assign("ress_buy_food", "value", nf($val['ress_buy_food']));


    $objResponse->assign("check_message", "innerHTML", $out_check_message);


    $objResponse->assign("marketinfo", "innerHTML", ob_get_contents());
    ob_end_clean();

    return $objResponse;
}





/************************************************/
/* Markt: Rohstoff Kauf Check/Kalkulator        */
/* Berechnet die Kosten der Angebote beim Kauf  */
/************************************************/

function calcMarketRessBuy($val)
{
    ob_start();
    $objResponse = new xajaxResponse();

    $ress_metal_total_costs = 0;
    $ress_crystal_total_costs = 0;
    $ress_plastic_total_costs = 0;
    $ress_fuel_total_costs = 0;
    $ress_food_total_costs = 0;
    $cnt = 0;

    if (isset($val['ressource_market_id'])) {
        foreach ($val['ressource_market_id'] as $num => $id) {
            $cnt++;

            // Summiert Rohstoffe
            $ress_metal_total_costs += $val['ress_buy_metal'][$id];
            $ress_crystal_total_costs += $val['ress_buy_crystal'][$id];
            $ress_plastic_total_costs += $val['ress_buy_plastic'][$id];
            $ress_fuel_total_costs += $val['ress_buy_fuel'][$id];
            $ress_food_total_costs += $val['ress_buy_food'][$id];
        }
    }


    //
    // Endprüfung ob alles OK ist
    //

    // Prüft, ob min. 1 Angebot selektiert wurde
    if ($cnt <= 0) {
        $out_ress_buy_check_message = "<div style=\"color:red;font-weight:bold;\">Es ist kein Angebot ausgewählt!</div>";

        $objResponse->assign("ressource_submit", "disabled", true);
        $objResponse->assign("ressource_submit", "style.color", '#f00');
    }
    // Prüft, ob genug Rohstoffe vorhanden sind
    elseif (
        $val['res_metal'] < $ress_metal_total_costs
        || $val['res_crystal'] < $ress_crystal_total_costs
        || $val['res_plastic'] < $ress_plastic_total_costs
        || $val['res_fuel'] < $ress_fuel_total_costs
        || $val['res_food'] < $ress_food_total_costs
    ) {
        $out_ress_buy_check_message = "<div style=\"color:red;font-weight:bold;\">Es sind zu wenig Rohstoffe vorhanden!</div>";

        $objResponse->assign("ressource_submit", "disabled", true);
        $objResponse->assign("ressource_submit", "style.color", '#f00');
    }
    // Angebot ist OK
    else {
        $out_ress_buy_check_message = "<div style=\"color:#0f0;font-weight:bold;\">OK!<br>";
        if ($cnt == 1) {
            $out_ress_buy_check_message .= "1 Angebot ausgewählt</div>";
            $objResponse->assign("ressource_submit", "value", "Angebot annehmen");
        } else {
            $out_ress_buy_check_message .= "" . $cnt . " Angebote ausgewählt</div>";
            $objResponse->assign("ressource_submit", "value", "Angebote annehmen");
        }
        $objResponse->assign("ressource_submit", "disabled", false);
        $objResponse->assign("ressource_submit", "style.color", '#0f0');
    }



    $objResponse->assign("ressource_check_message", "innerHTML", $out_ress_buy_check_message);


    $objResponse->assign("marketinfo", "innerHTML", ob_get_contents());
    ob_end_clean();

    return $objResponse;
}




/********************************************/
/* Markt: Schiff/Preis Kalkulator           */
/* Berechnet div. Preise und Prüft Angebot  */
/********************************************/
function calcMarketShipPrice($val, $new_ship = 0, $last_update = 0)
{
    ob_start();
    $objResponse = new xajaxResponse();

    // Eingaben wurden noch nicht geprüft
    $objResponse->assign("ship_check_submit", "value", 0);

    $ship = $val['ship_list'];
    $ship_count = min(nf_back($val['ship_count']), $_SESSION['market']['ship_data'][$ship]['shiplist_count']);
    $ship_max_count = $_SESSION['market']['ship_data'][$ship]['shiplist_count'];
    $ship_costs_metal = $_SESSION['market']['ship_data'][$ship]['ship_costs_metal'];
    $ship_costs_crystal = $_SESSION['market']['ship_data'][$ship]['ship_costs_crystal'];
    $ship_costs_plastic = $_SESSION['market']['ship_data'][$ship]['ship_costs_plastic'];
    $ship_costs_fuel = $_SESSION['market']['ship_data'][$ship]['ship_costs_fuel'];
    $ship_costs_food = $_SESSION['market']['ship_data'][$ship]['ship_costs_food'];

    $val['ship_buy_metal'] = nf_back($val['ship_buy_metal']);
    $val['ship_buy_crystal'] = nf_back($val['ship_buy_crystal']);
    $val['ship_buy_plastic'] = nf_back($val['ship_buy_plastic']);
    $val['ship_buy_fuel'] = nf_back($val['ship_buy_fuel']);
    $val['ship_buy_food'] = nf_back($val['ship_buy_food']);


    // Rechnet gesamt Kosten pro Rohstoff (Kosten * Anzahl) (Dient als Basis für Min/Max rechnung)
    $ship_costs_metal_total = $ship_costs_metal * $ship_count;
    $ship_costs_crystal_total = $ship_costs_crystal * $ship_count;
    $ship_costs_plastic_total = $ship_costs_plastic * $ship_count;
    $ship_costs_fuel_total = $ship_costs_fuel * $ship_count;
    $ship_costs_food_total = $ship_costs_food * $ship_count;

    // Schreibt Originalpreise in "Preis-Felder" und berechnet Min/Max wenn eine neue Eingabe gemacht wurde
    if ($new_ship == 1) {
        $val['ship_buy_metal'] = $ship_costs_metal_total;
        $val['ship_buy_crystal'] = $ship_costs_crystal_total;
        $val['ship_buy_plastic'] = $ship_costs_plastic_total;
        $val['ship_buy_fuel'] = $ship_costs_fuel_total;
        $val['ship_buy_food'] = $ship_costs_food_total;

        //Ändert Daten beim "Angebot Feld" welches gesperrt ist für Änderungen
        $objResponse->assign("ship_sell_metal", "value", nf($ship_costs_metal_total));
        $objResponse->assign("ship_sell_crystal", "value", nf($ship_costs_crystal_total));
        $objResponse->assign("ship_sell_plastic", "value", nf($ship_costs_plastic_total));
        $objResponse->assign("ship_sell_fuel", "value", nf($ship_costs_fuel_total));
        $objResponse->assign("ship_sell_food", "value", nf($ship_costs_food_total));
    }



    //
    // Errechnet und formatiert Preise
    //

    //
    // Titan
    //

    // MaxBetrag
    // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
    $ship_buy_metal_max =    $ship_costs_metal_total / MARKET_METAL_FACTOR * MARKET_METAL_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_crystal_total / MARKET_METAL_FACTOR * MARKET_CRYSTAL_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_plastic_total / MARKET_METAL_FACTOR * MARKET_PLASTIC_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_fuel_total / MARKET_METAL_FACTOR * MARKET_FUEL_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_food_total / MARKET_METAL_FACTOR * MARKET_FOOD_FACTOR * SHIP_PRICE_FACTOR_MAX;
    // Errechnet Grundbetrag abzüglich bereits eingebener Preise
    $ship_buy_metal_max = $ship_buy_metal_max
        -    $val['ship_buy_metal'] * MARKET_METAL_FACTOR / MARKET_METAL_FACTOR
        - $val['ship_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_METAL_FACTOR
        - $val['ship_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_METAL_FACTOR
        - $val['ship_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_METAL_FACTOR
        - $val['ship_buy_food'] * MARKET_FOOD_FACTOR / MARKET_METAL_FACTOR;
    $log_ship_buy_metal_max = ceil($ship_buy_metal_max);        //Der Effektivwert, dieser wird nicht angepasst
    $ship_buy_metal_max = floor($ship_buy_metal_max);    //Rundet Betrag auf die nächst kleinere Ganzzahl


    // MinBetrag
    // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
    $ship_buy_metal_ship_min =    $ship_costs_metal_total / MARKET_METAL_FACTOR * MARKET_METAL_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_crystal_total / MARKET_METAL_FACTOR * MARKET_CRYSTAL_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_plastic_total / MARKET_METAL_FACTOR * MARKET_PLASTIC_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_fuel_total / MARKET_METAL_FACTOR * MARKET_FUEL_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_food_total / MARKET_METAL_FACTOR * MARKET_FOOD_FACTOR * SHIP_PRICE_FACTOR_MIN;
    // Errechnet Grundbetrag abzüglich bereits eingebener Preise
    $ship_buy_metal_ship_min = $ship_buy_metal_ship_min
        -    $val['ship_buy_metal'] * MARKET_METAL_FACTOR / MARKET_METAL_FACTOR
        - $val['ship_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_METAL_FACTOR
        - $val['ship_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_METAL_FACTOR
        - $val['ship_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_METAL_FACTOR
        - $val['ship_buy_food'] * MARKET_FOOD_FACTOR / MARKET_METAL_FACTOR;
    $ship_buy_metal_ship_min = ceil($ship_buy_metal_ship_min);    //Rundet Betrag auf die nächste höhere Ganzzahl
    $log_ship_buy_metal_ship_min = $ship_buy_metal_ship_min;        //Der Effektivwert, dieser wird nicht angepasst

    if ($ship_buy_metal_max <= 0) {
        $ship_buy_metal_max = 0;
    }

    if ($ship_buy_metal_ship_min <= 0) {
        $ship_buy_metal_ship_min = 0;
    }

    // Generiert Link mit dem Min./Max. Betrag. Bei draufklick wird der Wert sofort ins Feld geschrieben
    $out_ship_min_max_metal = "<a href=\"javascript:;\" onclick=\"document.getElementById('ship_buy_metal').value=" . ($val['ship_buy_metal'] + $ship_buy_metal_ship_min) . ";xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));xajax_formatNumbers('ship_buy_metal','" . ($val['ship_buy_metal'] + $ship_buy_metal_ship_min) . "',1,'');\">+" . nf($ship_buy_metal_ship_min) . "</a> / <a href=\"javascript:;\" onclick=\"document.getElementById('ship_buy_metal').value=" . ($val['ship_buy_metal'] + $ship_buy_metal_max) . ";xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));xajax_formatNumbers('ship_buy_metal','" . ($val['ship_buy_metal'] + $ship_buy_metal_max) . "',1,'');\">+" . nf($ship_buy_metal_max) . "</a>";





    //
    // Silizium
    //

    // MaxBetrag
    // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
    $ship_buy_crystal_max =    $ship_costs_metal_total / MARKET_CRYSTAL_FACTOR * MARKET_METAL_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_crystal_total / MARKET_CRYSTAL_FACTOR * MARKET_CRYSTAL_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_plastic_total / MARKET_CRYSTAL_FACTOR * MARKET_PLASTIC_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_fuel_total / MARKET_CRYSTAL_FACTOR * MARKET_FUEL_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_food_total / MARKET_CRYSTAL_FACTOR * MARKET_FOOD_FACTOR * SHIP_PRICE_FACTOR_MAX;
    // Errechnet Grundbetrag abzüglich bereits eingebener Preise
    $ship_buy_crystal_max = $ship_buy_crystal_max
        -    $val['ship_buy_metal'] * MARKET_METAL_FACTOR / MARKET_CRYSTAL_FACTOR
        - $val['ship_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_CRYSTAL_FACTOR
        - $val['ship_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_CRYSTAL_FACTOR
        - $val['ship_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_CRYSTAL_FACTOR
        - $val['ship_buy_food'] * MARKET_FOOD_FACTOR / MARKET_CRYSTAL_FACTOR;
    $log_ship_buy_crystal_max = ceil($ship_buy_crystal_max);        //Der Effektivwert, dieser wird nicht angepasst
    $ship_buy_crystal_max = floor($ship_buy_crystal_max);    //Rundet Betrag auf die nächst kleinere Ganzzahl


    // MinBetrag
    // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
    $ship_buy_crystal_min =    $ship_costs_metal_total / MARKET_CRYSTAL_FACTOR * MARKET_METAL_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_crystal_total / MARKET_CRYSTAL_FACTOR * MARKET_CRYSTAL_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_plastic_total / MARKET_CRYSTAL_FACTOR * MARKET_PLASTIC_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_fuel_total / MARKET_CRYSTAL_FACTOR * MARKET_FUEL_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_food_total / MARKET_CRYSTAL_FACTOR * MARKET_FOOD_FACTOR * SHIP_PRICE_FACTOR_MIN;
    // Errechnet Grundbetrag abzüglich bereits eingebener Preise
    $ship_buy_crystal_min = $ship_buy_crystal_min
        -    $val['ship_buy_metal'] * MARKET_METAL_FACTOR / MARKET_CRYSTAL_FACTOR
        - $val['ship_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_CRYSTAL_FACTOR
        - $val['ship_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_CRYSTAL_FACTOR
        - $val['ship_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_CRYSTAL_FACTOR
        - $val['ship_buy_food'] * MARKET_FOOD_FACTOR / MARKET_CRYSTAL_FACTOR;
    $ship_buy_crystal_min = ceil($ship_buy_crystal_min);    //Rundet Betrag auf die nächste höhere Ganzzahl
    $log_ship_buy_crystal_ship_min = $ship_buy_crystal_min;        //Der Effektivwert, dieser wird nicht angepasst

    if ($ship_buy_crystal_max <= 0) {
        $ship_buy_crystal_max = 0;
    }

    if ($ship_buy_crystal_min <= 0) {
        $ship_buy_crystal_min = 0;
    }

    // Generiert Link mit dem Min./Max. Betrag. Bei draufklick wird der Wert sofort ins Feld geschrieben
    $out_ship_min_max_crystal = "<a href=\"javascript:;\" onclick=\"document.getElementById('ship_buy_crystal').value=" . ($val['ship_buy_crystal'] + $ship_buy_crystal_min) . ";xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));xajax_formatNumbers('ship_buy_crystal','" . ($val['ship_buy_crystal'] + $ship_buy_crystal_min) . "',1,'');\">+" . nf($ship_buy_crystal_min) . "</a> / <a href=\"javascript:;\" onclick=\"document.getElementById('ship_buy_crystal').value=" . ($val['ship_buy_crystal'] + $ship_buy_crystal_max) . ";xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));xajax_formatNumbers('ship_buy_crystal','" . ($val['ship_buy_crystal'] + $ship_buy_crystal_max) . "',1,'');\">+" . nf($ship_buy_crystal_max) . "</a>";



    //
    // PVC
    //

    // MaxBetrag
    // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
    $ship_buy_plastic_max =    $ship_costs_metal_total / MARKET_PLASTIC_FACTOR * MARKET_METAL_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_crystal_total / MARKET_PLASTIC_FACTOR * MARKET_CRYSTAL_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_plastic_total / MARKET_PLASTIC_FACTOR * MARKET_PLASTIC_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_fuel_total / MARKET_PLASTIC_FACTOR * MARKET_FUEL_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_food_total / MARKET_PLASTIC_FACTOR * MARKET_FOOD_FACTOR * SHIP_PRICE_FACTOR_MAX;
    // Errechnet Grundbetrag abzüglich bereits eingebener Preise
    $ship_buy_plastic_max = $ship_buy_plastic_max
        -    $val['ship_buy_metal'] * MARKET_METAL_FACTOR / MARKET_PLASTIC_FACTOR
        - $val['ship_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_PLASTIC_FACTOR
        - $val['ship_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_PLASTIC_FACTOR
        - $val['ship_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_PLASTIC_FACTOR
        - $val['ship_buy_food'] * MARKET_FOOD_FACTOR / MARKET_PLASTIC_FACTOR;
    $log_ship_buy_plastic_max = ceil($ship_buy_plastic_max);        //Der Effektivwert, dieser wird nicht angepasst
    $ship_buy_plastic_max = floor($ship_buy_plastic_max);    //Rundet Betrag auf die nächst kleinere Ganzzahl


    // MinBetrag
    // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
    $ship_buy_plastic_min =    $ship_costs_metal_total / MARKET_PLASTIC_FACTOR * MARKET_METAL_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_crystal_total / MARKET_PLASTIC_FACTOR * MARKET_CRYSTAL_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_plastic_total / MARKET_PLASTIC_FACTOR * MARKET_PLASTIC_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_fuel_total / MARKET_PLASTIC_FACTOR * MARKET_FUEL_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_food_total / MARKET_PLASTIC_FACTOR * MARKET_FOOD_FACTOR * SHIP_PRICE_FACTOR_MIN;
    // Errechnet Grundbetrag abzüglich bereits eingebener Preise
    $ship_buy_plastic_min = $ship_buy_plastic_min
        -    $val['ship_buy_metal'] * MARKET_METAL_FACTOR / MARKET_PLASTIC_FACTOR
        - $val['ship_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_PLASTIC_FACTOR
        - $val['ship_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_PLASTIC_FACTOR
        - $val['ship_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_PLASTIC_FACTOR
        - $val['ship_buy_food'] * MARKET_FOOD_FACTOR / MARKET_PLASTIC_FACTOR;
    $ship_buy_plastic_min = ceil($ship_buy_plastic_min);    //Rundet Betrag auf die nächste höhere Ganzzahl
    $log_ship_buy_plastic_ship_min = $ship_buy_plastic_min;        //Der Effektivwert, dieser wird nicht angepasst

    if ($ship_buy_plastic_max <= 0) {
        $ship_buy_plastic_max = 0;
    }

    if ($ship_buy_plastic_min <= 0) {
        $ship_buy_plastic_min = 0;
    }

    // Generiert Link mit dem Min./Max. Betrag. Bei draufklick wird der Wert sofort ins Feld geschrieben
    $out_ship_min_max_plastic = "<a href=\"javascript:;\" onclick=\"document.getElementById('ship_buy_plastic').value=" . ($val['ship_buy_plastic'] + $ship_buy_plastic_min) . ";xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));xajax_formatNumbers('ship_buy_plastic','" . ($val['ship_buy_plastic'] + $ship_buy_plastic_min) . "',1,'');\">+" . nf($ship_buy_plastic_min) . "</a> / <a href=\"javascript:;\" onclick=\"document.getElementById('ship_buy_plastic').value=" . ($val['ship_buy_plastic'] + $ship_buy_plastic_max) . ";xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));xajax_formatNumbers('ship_buy_plastic','" . ($val['ship_buy_plastic'] + $ship_buy_plastic_max) . "',1,'');\">+" . nf($ship_buy_plastic_max) . "</a>";





    //
    // Tritium
    //

    // MaxBetrag
    // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
    $ship_buy_fuel_max =    $ship_costs_metal_total / MARKET_FUEL_FACTOR * MARKET_METAL_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_crystal_total / MARKET_FUEL_FACTOR * MARKET_CRYSTAL_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_plastic_total / MARKET_FUEL_FACTOR * MARKET_PLASTIC_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_fuel_total / MARKET_FUEL_FACTOR * MARKET_FUEL_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_food_total / MARKET_FUEL_FACTOR * MARKET_FOOD_FACTOR * SHIP_PRICE_FACTOR_MAX;
    // Errechnet Grundbetrag abzüglich bereits eingebener Preise
    $ship_buy_fuel_max = $ship_buy_fuel_max
        -    $val['ship_buy_metal'] * MARKET_METAL_FACTOR / MARKET_FUEL_FACTOR
        - $val['ship_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_FUEL_FACTOR
        - $val['ship_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_FUEL_FACTOR
        - $val['ship_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_FUEL_FACTOR
        - $val['ship_buy_food'] * MARKET_FOOD_FACTOR / MARKET_FUEL_FACTOR;
    $log_ship_buy_fuel_max = ceil($ship_buy_fuel_max);        //Der Effektivwert, dieser wird nicht angepasst
    $ship_buy_fuel_max = floor($ship_buy_fuel_max);    //Rundet Betrag auf die nächst kleinere Ganzzahl


    // MinBetrag
    // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
    $ship_buy_fuel_min =    $ship_costs_metal_total / MARKET_FUEL_FACTOR * MARKET_METAL_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_crystal_total / MARKET_FUEL_FACTOR * MARKET_CRYSTAL_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_plastic_total / MARKET_FUEL_FACTOR * MARKET_PLASTIC_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_fuel_total / MARKET_FUEL_FACTOR * MARKET_FUEL_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_food_total / MARKET_FUEL_FACTOR * MARKET_FOOD_FACTOR * SHIP_PRICE_FACTOR_MIN;
    // Errechnet Grundbetrag abzüglich bereits eingebener Preise
    $ship_buy_fuel_min = $ship_buy_fuel_min
        -    $val['ship_buy_metal'] * MARKET_METAL_FACTOR / MARKET_FUEL_FACTOR
        - $val['ship_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_FUEL_FACTOR
        - $val['ship_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_FUEL_FACTOR
        - $val['ship_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_FUEL_FACTOR
        - $val['ship_buy_food'] * MARKET_FOOD_FACTOR / MARKET_FUEL_FACTOR;
    $ship_buy_fuel_min = ceil($ship_buy_fuel_min);    //Rundet Betrag auf die nächste höhere Ganzzahl
    $log_ship_buy_fuel_ship_min = $ship_buy_fuel_min;        //Der Effektivwert, dieser wird nicht angepasst

    if ($ship_buy_fuel_max <= 0) {
        $ship_buy_fuel_max = 0;
    }

    if ($ship_buy_fuel_min <= 0) {
        $ship_buy_fuel_min = 0;
    }

    // Generiert Link mit dem Min./Max. Betrag. Bei draufklick wird der Wert sofort ins Feld geschrieben
    $out_ship_min_max_fuel = "<a href=\"javascript:;\" onclick=\"document.getElementById('ship_buy_fuel').value=" . ($val['ship_buy_fuel'] + $ship_buy_fuel_min) . ";xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));xajax_formatNumbers('ship_buy_fuel','" . ($val['ship_buy_fuel'] + $ship_buy_fuel_min) . "',1,'');\">+" . nf($ship_buy_fuel_min) . "</a> / <a href=\"javascript:;\" onclick=\"document.getElementById('ship_buy_fuel').value=" . ($val['ship_buy_fuel'] + $ship_buy_fuel_max) . ";xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));xajax_formatNumbers('ship_buy_fuel','" . ($val['ship_buy_fuel'] + $ship_buy_fuel_max) . "',1,'');\">+" . nf($ship_buy_fuel_max) . "</a>";



    //
    // Nahrung
    //

    // MaxBetrag
    // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
    $ship_buy_food_max =    $ship_costs_metal_total / MARKET_FOOD_FACTOR * MARKET_METAL_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_crystal_total / MARKET_FOOD_FACTOR * MARKET_CRYSTAL_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_plastic_total / MARKET_FOOD_FACTOR * MARKET_PLASTIC_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_fuel_total / MARKET_FOOD_FACTOR * MARKET_FUEL_FACTOR * SHIP_PRICE_FACTOR_MAX
        + $ship_costs_food_total / MARKET_FOOD_FACTOR * MARKET_FOOD_FACTOR * SHIP_PRICE_FACTOR_MAX;
    // Errechnet Grundbetrag abzüglich bereits eingebener Preise
    $ship_buy_food_max = $ship_buy_food_max
        -    $val['ship_buy_metal'] * MARKET_METAL_FACTOR / MARKET_FOOD_FACTOR
        - $val['ship_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_FOOD_FACTOR
        - $val['ship_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_FOOD_FACTOR
        - $val['ship_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_FOOD_FACTOR
        - $val['ship_buy_food'] * MARKET_FOOD_FACTOR / MARKET_FOOD_FACTOR;
    $log_ship_buy_food_max = ceil($ship_buy_food_max);        //Der Effektivwert, dieser wird nicht angepasst
    $ship_buy_food_max = floor($ship_buy_food_max);    //Rundet Betrag auf die nächst kleinere Ganzzahl


    // MinBetrag
    // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
    $ship_buy_food_min =    $ship_costs_metal_total / MARKET_FOOD_FACTOR * MARKET_METAL_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_crystal_total / MARKET_FOOD_FACTOR * MARKET_CRYSTAL_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_plastic_total / MARKET_FOOD_FACTOR * MARKET_PLASTIC_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_fuel_total / MARKET_FOOD_FACTOR * MARKET_FUEL_FACTOR * SHIP_PRICE_FACTOR_MIN
        + $ship_costs_food_total / MARKET_FOOD_FACTOR * MARKET_FOOD_FACTOR * SHIP_PRICE_FACTOR_MIN;
    // Errechnet Grundbetrag abzüglich bereits eingebener Preise
    $ship_buy_food_min = $ship_buy_food_min
        -    $val['ship_buy_metal'] * MARKET_METAL_FACTOR / MARKET_FOOD_FACTOR
        - $val['ship_buy_crystal'] * MARKET_CRYSTAL_FACTOR / MARKET_FOOD_FACTOR
        - $val['ship_buy_plastic'] * MARKET_PLASTIC_FACTOR / MARKET_FOOD_FACTOR
        - $val['ship_buy_fuel'] * MARKET_FUEL_FACTOR / MARKET_FOOD_FACTOR
        - $val['ship_buy_food'] * MARKET_FOOD_FACTOR / MARKET_FOOD_FACTOR;
    $ship_buy_food_min = ceil($ship_buy_food_min);    //Rundet Betrag auf die nächste höhere Ganzzahl
    $log_ship_buy_food_ship_min = $ship_buy_food_min;        //Der Effektivwert, dieser wird nicht angepasst

    if ($ship_buy_food_max <= 0) {
        $ship_buy_food_max = 0;
    }

    if ($ship_buy_food_min <= 0) {
        $ship_buy_food_min = 0;
    }

    // Generiert Link mit dem Min./Max. Betrag. Bei draufklick wird der Wert sofort ins Feld geschrieben
    $out_ship_min_max_food = "<a href=\"javascript:;\" onclick=\"document.getElementById('ship_buy_food').value=" . ($val['ship_buy_food'] + $ship_buy_food_min) . ";xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));xajax_formatNumbers('ship_buy_food','" . ($val['ship_buy_food'] + $ship_buy_food_min) . "',1,'');\">+" . nf($ship_buy_food_min) . "</a> / <a href=\"javascript:;\" onclick=\"document.getElementById('ship_buy_food').value=" . ($val['ship_buy_food'] + $ship_buy_food_max) . ";xajax_calcMarketShipPrice(xajax.getFormValues('ship_selector'));xajax_formatNumbers('ship_buy_food','" . ($val['ship_buy_food'] + $ship_buy_food_max) . "',1,'');\">+" . nf($ship_buy_food_max) . "</a>";


    //
    // End Prüfung ob Angebot OK ist
    //

    // 0 Schiffe angegeben
    if ($ship_count <= 0) {
        $out_ship_check_message = "<div style=\"color:red;font-weight:bold;\">Gib ein Angebot ein!</div>";

        $objResponse->assign("ship_sell_submit", "disabled", true);
        $objResponse->assign("ship_sell_submit", "style.color", '#f00');
    }
    // Zu hohe Preise
    elseif (
        $log_ship_buy_metal_max < 0
        || $log_ship_buy_crystal_max < 0
        || $log_ship_buy_plastic_max < 0
        || $log_ship_buy_fuel_max < 0
        || $log_ship_buy_food_max < 0
    ) {
        $out_ship_check_message = "<div style=\"color:red;font-weight:bold;\">Die Preise sind zu hoch!</div>";

        $objResponse->assign("ship_sell_submit", "disabled", true);
        $objResponse->assign("ship_sell_submit", "style.color", '#f00');
    }
    // Zu niedrige Preise
    elseif (
        $log_ship_buy_metal_ship_min > 0
        || $log_ship_buy_crystal_ship_min > 0
        || $log_ship_buy_plastic_ship_min > 0
        || $log_ship_buy_fuel_ship_min > 0
        || $log_ship_buy_food_ship_min > 0
    ) {
        $out_ship_check_message = "<div style=\"color:red;font-weight:bold;\">Die Preise sind zu niedrig!</div>";

        $objResponse->assign("ship_sell_submit", "disabled", true);
        $objResponse->assign("ship_sell_submit", "style.color", '#f00');
    }
    // Unerlaubte Zeichen im Werbetext
    elseif (check_illegal_signs($val['ship_text']) != "") {
        $out_ship_check_message = "<div style=\"color:red;font-weight:bold;\">Unerlaubte Zeichen im Werbetext (" . check_illegal_signs("><$") . ")!</div>";

        $objResponse->assign("ship_sell_submit", "disabled", true);
        $objResponse->assign("ship_sell_submit", "style.color", '#f00');
    }
    // Angebot ist OK
    else {
        $out_ship_check_message = "<div style=\"color:#0f0;font-weight:bold;\">OK!</div>";
        $objResponse->assign("ship_sell_submit", "disabled", false);
        $objResponse->assign("ship_sell_submit", "style.color", '#0f0');

        // XAJAX bestätigt die Korrektheit/Legalität der Eingaben
        $objResponse->assign("ship_check_submit", "value", 1);
    }

    // Bestätigt, dass XAJAX das Formular vor dem Absenden nochmal kontrolliert hat
    $objResponse->assign("ship_last_update", "value", $last_update);



    // XAJAX ändert Daten
    $objResponse->assign("ship_min_max_metal", "innerHTML", $out_ship_min_max_metal);
    $objResponse->assign("ship_min_max_crystal", "innerHTML", $out_ship_min_max_crystal);
    $objResponse->assign("ship_min_max_plastic", "innerHTML", $out_ship_min_max_plastic);
    $objResponse->assign("ship_min_max_fuel", "innerHTML", $out_ship_min_max_fuel);
    $objResponse->assign("ship_min_max_food", "innerHTML", $out_ship_min_max_food);

    $objResponse->assign("ship_buy_metal", "value", nf($val['ship_buy_metal']));
    $objResponse->assign("ship_buy_crystal", "value", nf($val['ship_buy_crystal']));
    $objResponse->assign("ship_buy_plastic", "value", nf($val['ship_buy_plastic']));
    $objResponse->assign("ship_buy_fuel", "value", nf($val['ship_buy_fuel']));
    $objResponse->assign("ship_buy_food", "value", nf($val['ship_buy_food']));

    $objResponse->assign("ship_count", "value", nf($ship_count));

    $objResponse->assign("ship_check_message", "innerHTML", $out_ship_check_message);


    $objResponse->assign("marketinfo", "innerHTML", ob_get_contents());
    ob_end_clean();

    return $objResponse;
}



/************************************************/
/* Markt: Schiffs Kauf Check/Kalkulator         */
/* Berechnet die Kosten der Angebote beim Kauf  */
/************************************************/

function calcMarketShipBuy($val)
{
    ob_start();
    $objResponse = new xajaxResponse();

    $ship_metal_total_costs = 0;
    $ship_crystal_total_costs = 0;
    $ship_plastic_total_costs = 0;
    $ship_fuel_total_costs = 0;
    $ship_food_total_costs = 0;
    $cnt = 0;

    if (isset($val['ship_market_id'])) {
        foreach ($val['ship_market_id'] as $num => $id) {
            $cnt++;

            // Summiert Rohstoffe
            $ship_metal_total_costs += $val['ship_buy_metal'][$id];
            $ship_crystal_total_costs += $val['ship_buy_crystal'][$id];
            $ship_plastic_total_costs += $val['ship_buy_plastic'][$id];
            $ship_fuel_total_costs += $val['ship_buy_fuel'][$id];
            $ship_food_total_costs += $val['ship_buy_food'][$id];
        }
    }



    //
    // Endprüfung ob alles OK ist
    //

    // Prüft, ob min. 1 Angebot selektiert wurde
    if ($cnt <= 0) {
        $out_ship_buy_check_message = "<div style=\"color:red;font-weight:bold;\">Es ist kein Angebot ausgewählt!</div>";

        $objResponse->assign("ship_submit", "disabled", true);
        $objResponse->assign("ship_submit", "style.color", '#f00');
    }
    // Prüft, ob genug Rohstoffe vorhanden sind
    elseif (
        $val['res_metal'] < $ship_metal_total_costs
        || $val['res_crystal'] < $ship_crystal_total_costs
        || $val['res_plastic'] < $ship_plastic_total_costs
        || $val['res_fuel'] < $ship_fuel_total_costs
        || $val['res_food'] < $ship_food_total_costs
    ) {
        $out_ship_buy_check_message = "<div style=\"color:red;font-weight:bold;\">Es sind zu wenig Rohstoffe vorhanden!</div>";

        $objResponse->assign("ship_submit", "disabled", true);
        $objResponse->assign("ship_submit", "style.color", '#f00');
    }
    // Angebot ist OK
    else {
        $out_ship_buy_check_message = "<div style=\"color:#0f0;font-weight:bold;\">OK!<br>";
        if ($cnt == 1) {
            $out_ship_buy_check_message .= "1 Angebot ausgewählt</div>";
            $objResponse->assign("ship_submit", "value", "Angebot annehmen");
        } else {
            $out_ship_buy_check_message .= "" . $cnt . " Angebote ausgewählt</div>";
            $objResponse->assign("ship_submit", "value", "Angebote annehmen");
        }
        $objResponse->assign("ship_submit", "disabled", false);
        $objResponse->assign("ship_submit", "style.color", '#0f0');
    }


    $objResponse->assign("ship_buy_check_message", "innerHTML", $out_ship_buy_check_message);


    $objResponse->assign("marketinfo", "innerHTML", ob_get_contents());
    ob_end_clean();

    return $objResponse;
}




/***************************************/
/* Markt: Auktion Endzeit Kalkulator   */
/* Berechnet die Dauer der Auktion     */
/***************************************/

function calcMarketAuctionTime($val)
{
    ob_start();
    $objResponse = new xajaxResponse();

    // Berechnet End Datum
    $auction_end_time = $val['auction_time_min'] + $val['auction_time_days'] * 24 * 3600 + $val['auction_time_hours'] * 3600;


    $objResponse->assign("auction_end_time", "innerHTML", date("d.m.Y H:i", $auction_end_time));

    $objResponse->assign("marketinfo", "innerHTML", ob_get_contents());
    ob_end_clean();

    return $objResponse;
}





/***************************************/
/* Markt: Auktionen/Eingabe Check      */
/* Prüft Angebot                       */
/***************************************/

function checkMarketAuctionFormular($val, $last_update = 0)
{
    ob_start();
    $objResponse = new xajaxResponse();

    // Eingaben wurden noch nicht geprüft
    $objResponse->assign("auction_check_submit", "value", 0);

    // Setzt Kästchen value wieder auf 1
    $objResponse->assign("auction_buy_metal", "value", 1);
    $objResponse->assign("auction_buy_crystal", "value", 1);
    $objResponse->assign("auction_buy_plastic", "value", 1);
    $objResponse->assign("auction_buy_fuel", "value", 1);
    $objResponse->assign("auction_buy_food", "value", 1);


    $val['auction_sell_metal'] = min(nf_back($val['auction_sell_metal']), floor($val['res_metal'] / MARKET_SELL_TAX));
    $val['auction_sell_crystal'] = min(nf_back($val['auction_sell_crystal']), floor($val['res_crystal'] / MARKET_SELL_TAX));
    $val['auction_sell_plastic'] = min(nf_back($val['auction_sell_plastic']), floor($val['res_plastic'] / MARKET_SELL_TAX));
    $val['auction_sell_fuel'] = min(nf_back($val['auction_sell_fuel']), floor($val['res_fuel'] / MARKET_SELL_TAX));
    $val['auction_sell_food'] = min(nf_back($val['auction_sell_food']), floor($val['res_food'] / MARKET_SELL_TAX));


    // Deselektiert Preiskästchen wenn vom gleichen Rohstoff verkauft wird
    // Titan
    if ($val['auction_sell_metal'] != 0) {
        $objResponse->assign("auction_buy_metal", "checked", false);
        $objResponse->assign("auction_buy_metal", "value", 0);
        $val['auction_buy_metal'] = 0;
    }
    // Silizium
    if ($val['auction_sell_crystal'] != 0) {
        $objResponse->assign("auction_buy_crystal", "checked", false);
        $objResponse->assign("auction_buy_crystal", "value", 0);
        $val['auction_buy_crytsal'] = 0;
    }
    // PVC
    if ($val['auction_sell_plastic'] != 0) {
        $objResponse->assign("auction_buy_plastic", "checked", false);
        $objResponse->assign("auction_buy_plastic", "value", 0);
        $val['auction_buy_plastic'] = 0;
    }
    // Tritium
    if ($val['auction_sell_fuel'] != 0) {
        $objResponse->assign("auction_buy_fuel", "checked", false);
        $objResponse->assign("auction_buy_fuel", "value", 0);
        $val['auction_buy_fuel'] = 0;
    }
    // Nahrung
    if ($val['auction_sell_food'] != 0) {
        $objResponse->assign("auction_buy_food", "checked", false);
        $objResponse->assign("auction_buy_food", "value", 0);
        $val['auction_buy_food'] = 0;
    }


    //
    // End Prüfung ob Angebot OK ist
    //

    // Keine Rohstoffe angegeben
    if (
        $val['auction_sell_metal'] <= 0
        && $val['auction_sell_crystal'] <= 0
        && $val['auction_sell_plastic'] <= 0
        && $val['auction_sell_fuel'] <= 0
        && $val['auction_sell_food'] <= 0
    ) {
        $out_auction_check_message = "<div style=\"color:red;font-weight:bold;\">Gib ein Angebot ein!</div>";

        $objResponse->assign("auction_sell_submit", "disabled", true);
        $objResponse->assign("auction_sell_submit", "style.color", '#f00');
    }
    // Keinen Preis angegeben
    elseif (
        $val['auction_buy_metal'] == 0
        && $val['auction_buy_crystal'] == 0
        && $val['auction_buy_plastic'] == 0
        && $val['auction_buy_fuel'] == 0
        && $val['auction_buy_food'] == 0
    ) {
        $out_auction_check_message = "<div style=\"color:red;font-weight:bold;\">Das Angebot muss eine Zahlungsmöglichkeit aufweisen!</div>";

        $objResponse->assign("auction_sell_submit", "disabled", true);
        $objResponse->assign("auction_sell_submit", "style.color", '#f00');
    }
    // Zu wenig Rohstoffe auf dem Planeten
    elseif (
        floor($val['auction_sell_metal'] * MARKET_SELL_TAX) > $val['res_metal']
        || floor($val['auction_sell_crystal'] * MARKET_SELL_TAX) > $val['res_crystal']
        || floor($val['auction_sell_plastic'] * MARKET_SELL_TAX) > $val['res_plastic']
        || floor($val['auction_sell_fuel'] * MARKET_SELL_TAX) > $val['res_fuel']
        || floor($val['auction_sell_food'] * MARKET_SELL_TAX) > $val['res_food']
    ) {
        $out_auction_check_message = "<div style=\"color:red;font-weight:bold;\">Es sind zu wenig Rohstoffe vorhanden! (Beachte Verkaufsgebühr)</div>";

        $objResponse->assign("auction_sell_submit", "disabled", true);
        $objResponse->assign("auction_sell_submit", "style.color", '#f00');
    }
    // Unerlaubte Zeichen im Werbetext
    elseif (check_illegal_signs($val['auction_text']) != "") {
        $out_auction_check_message = "<div style=\"color:red;font-weight:bold;\">Unerlaubte Zeichen im Werbetext (" . check_illegal_signs("><$") . ")!</div>";

        $objResponse->assign("auction_sell_submit", "disabled", true);
        $objResponse->assign("auction_sell_submit", "style.color", '#f00');
    }
    // Angebot ist OK
    else {
        // Rechnet gesamt Verkaufsgebühren
        $sell_tax = $val['auction_sell_metal'] * (MARKET_SELL_TAX - 1)
            + $val['auction_sell_crystal'] * (MARKET_SELL_TAX - 1)
            + $val['auction_sell_plastic'] * (MARKET_SELL_TAX - 1)
            + $val['auction_sell_fuel'] * (MARKET_SELL_TAX - 1)
            + $val['auction_sell_food'] * (MARKET_SELL_TAX - 1);

        $out_auction_check_message = "<div style=\"color:#0f0;font-weight:bold;\">OK!<br>Verkaufsgebühren: " . nf($sell_tax) . " t</div>";
        $objResponse->assign("auction_sell_submit", "disabled", false);
        $objResponse->assign("auction_sell_submit", "style.color", '#0f0');

        $objResponse->assign("auction_check_submit", "value", 1);
    }

    // Bestätigt, dass XAJAX das Formular vor dem Absenden nochmal kontrolliert hat
    $objResponse->assign("auction_last_update", "value", $last_update);


    // XAJAX ändert Daten
    $objResponse->assign("auction_check_message", "innerHTML", $out_auction_check_message);

    $objResponse->assign("auction_sell_metal", "value", nf($val['auction_sell_metal']));
    $objResponse->assign("auction_sell_crystal", "value", nf($val['auction_sell_crystal']));
    $objResponse->assign("auction_sell_plastic", "value", nf($val['auction_sell_plastic']));
    $objResponse->assign("auction_sell_fuel", "value", nf($val['auction_sell_fuel']));
    $objResponse->assign("auction_sell_food", "value", nf($val['auction_sell_food']));


    $objResponse->assign("marketinfo", "innerHTML", ob_get_contents());
    ob_end_clean();

    return $objResponse;
}




/************************************************************/
/* Markt: Auktionen Dauer Kalkulator                        */
/* Berechnet div. Preise und Prüft Angebot beim bieten      */
/************************************************************/

function calcMarketAuctionPrice($val, $last_update = 0)
{
    ob_start();
    $objResponse = new xajaxResponse();

    // Eingaben wurden noch nicht geprüft
    $objResponse->assign("auction_show_check_submit", "value", 0);

    $val['new_buy_0'] = min(nf_back($val['new_buy_0'] ?? 0), floor($val['res_0']));
    $val['new_buy_1'] = min(nf_back($val['new_buy_1'] ?? 0), floor($val['res_1']));
    $val['new_buy_2'] = min(nf_back($val['new_buy_2'] ?? 0), floor($val['res_2']));
    $val['new_buy_3'] = min(nf_back($val['new_buy_3'] ?? 0), floor($val['res_3']));
    $val['new_buy_4'] = min(nf_back($val['new_buy_4'] ?? 0), floor($val['res_4']));

    etoa_dump($val);
    // Errechnet Rohstoffwert vom Höchstbietenden
    $buy_price =     $val['buy_0'] * MARKET_METAL_FACTOR
        + $val['buy_1'] * MARKET_CRYSTAL_FACTOR
        + $val['buy_2'] * MARKET_PLASTIC_FACTOR
        + $val['buy_3'] * MARKET_FUEL_FACTOR
        + $val['buy_4'] * MARKET_FOOD_FACTOR;
    // Errechnet Roshtoffwert vom eingegebenen Gebot
    $new_buy_price =     $val['new_buy_0'] * MARKET_METAL_FACTOR
        + $val['new_buy_1'] * MARKET_CRYSTAL_FACTOR
        + $val['new_buy_2'] * MARKET_PLASTIC_FACTOR
        + $val['new_buy_3'] * MARKET_FUEL_FACTOR
        + $val['new_buy_4'] * MARKET_FOOD_FACTOR;

    //
    // Errechnet und formatiert Preise
    //
    $buyMax = array();
    $buyMin = array();
    $logBuyMax = array();
    $logBuyMin = array();
    $outMinMax = array();
    $factor = array(
        MARKET_METAL_FACTOR,
        MARKET_CRYSTAL_FACTOR,
        MARKET_PLASTIC_FACTOR,
        MARKET_FUEL_FACTOR,
        MARKET_FOOD_FACTOR
    );

    global $resNames;
    foreach ($resNames as $rid => $r) {
        // MaxBetrag
        // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
        $buyMax[$rid] = $val['sell_0'] / $factor[$rid] * $factor[0] * AUCTION_PRICE_FACTOR_MAX
            + $val['sell_1'] / $factor[$rid] * $factor[1] * AUCTION_PRICE_FACTOR_MAX
            + $val['sell_2'] / $factor[$rid] * $factor[2] * AUCTION_PRICE_FACTOR_MAX
            + $val['sell_3'] / $factor[$rid] * $factor[3] * AUCTION_PRICE_FACTOR_MAX
            + $val['sell_4'] / $factor[$rid] * $factor[4] * AUCTION_PRICE_FACTOR_MAX;
        // Errechnet Grundbetrag abzüglich bereits eingebener Preise
        $buyMax[$rid] = $buyMax[$rid]
            - $val['new_buy_0'] * $factor[0] / $factor[$rid]
            - $val['new_buy_1'] * $factor[1] / $factor[$rid]
            - $val['new_buy_2'] * $factor[2] / $factor[$rid]
            - $val['new_buy_3'] * $factor[3] / $factor[$rid]
            - $val['new_buy_4'] * $factor[4] / $factor[$rid];
        $logBuyMax[$rid] = ceil($buyMax[$rid]);        //Der Effektivwert, dieser wird nicht angepasst
        $buyMax[$rid] = floor($buyMax[$rid]);            //Rundet Betrag auf die nächst kleinere Ganzzahl


        // MinBetrag
        // Errechnet Grundbetrag (Noch ohne Abzüge eingegebenen Preisen)
        $buyMin[$rid] =
            $val['sell_0'] / $factor[$rid] * $factor[0] * AUCTION_PRICE_FACTOR_MIN
            + $val['sell_1'] / $factor[$rid] * $factor[1] * AUCTION_PRICE_FACTOR_MIN
            + $val['sell_2'] / $factor[$rid] * $factor[2] * AUCTION_PRICE_FACTOR_MIN
            + $val['sell_3'] / $factor[$rid] * $factor[3] * AUCTION_PRICE_FACTOR_MIN
            + $val['sell_4'] / $factor[$rid] * $factor[4] * AUCTION_PRICE_FACTOR_MIN;
        // Errechnet Grundbetrag abzüglich bereits eingebener Preise
        $buyMin[$rid] =    $buyMin[$rid]
            - $val['new_buy_0'] * $factor[0] / $factor[$rid]
            - $val['new_buy_1'] * $factor[1] / $factor[$rid]
            - $val['new_buy_2'] * $factor[2] / $factor[$rid]
            - $val['new_buy_3'] * $factor[3] / $factor[$rid]
            - $val['new_buy_4'] * $factor[4] / $factor[$rid];
        $buyMin[$rid] = ceil($buyMin[$rid]);    //Rundet Betrag auf die nächste höhere Ganzzahl
        $logBuyMin[$rid] = $buyMin[$rid];        //Der Effektivwert, dieser wird nicht angepasst

        if ($buyMax[$rid] <= 0) {
            $buyMax[$rid] = 0;
        }

        if ($buyMin[$rid] <= 0) {
            $buyMin[$rid] = 0;
        }

        // Generiert Link mit dem Min./Max. Betrag. Bei draufklick wird der Wert sofort ins Feld geschrieben
        if ($val['currency_' . $rid] == 1) {
            $outMinMax[$rid] = "<a href=\"javascript:;\" onclick=\"document.getElementById('new_buy_" . $rid . "').value=" . ($val['new_buy_' . $rid] + $buyMin[$rid]) . ";xajax_calcMarketAuctionPrice(xajax.getFormValues('auction_show_selector'));\">+" . $buyMin[$rid] . "</a> / <a href=\"javascript:;\" onclick=\"document.getElementById('new_buy_" . $rid . "').value=" . ($val['new_buy_' . $rid] + $buyMax[$rid]) . ";xajax_calcMarketAuctionPrice(xajax.getFormValues('auction_show_selector'));\">+" . $buyMax[$rid] . "</a>";
        } else {
            $outMinMax[$rid] = "-";
        }
    }
    etoa_dump($buyMin);
    etoa_dump($buyMax);


    //
    // End Prüfung ob Angebot OK ist
    //

    // Keine Rohstoffe angegeben
    if (
        $val['new_buy_0'] <= 0
        && $val['new_buy_1'] <= 0
        && $val['new_buy_2'] <= 0
        && $val['new_buy_3'] <= 0
        && $val['new_buy_4'] <= 0
    ) {
        $out_auction_check_message = "<div style=\"color:red;font-weight:bold;\">Gib ein Gebot ein!</div>";

        $objResponse->assign("submit_auction_bid", "disabled", true);
        $objResponse->assign("submit_auction_bid", "style.color", '#f00');
    }
    // Zu hohe Preise
    elseif (
        $logBuyMax[0] < 0
        || $logBuyMax[1] < 0
        || $logBuyMax[2] < 0
        || $logBuyMax[3] < 0
        || $logBuyMax[4] < 0
    ) {
        $out_auction_check_message = "<div style=\"color:red;font-weight:bold;\">Das Gebot ist zu hoch!</div>";

        $objResponse->assign("submit_auction_bid", "disabled", true);
        $objResponse->assign("submit_auction_bid", "style.color", '#f00');
    }
    // Zu niedrige Preise
    elseif (
        $logBuyMin[0] > 0
        || $logBuyMin[1] > 0
        || $logBuyMin[2] > 0
        || $logBuyMin[3] > 0
        || $logBuyMin[4] > 0
    ) {
        $out_auction_check_message = "<div style=\"color:red;font-weight:bold;\">Das Gebot ist zu niedrig!</div>";

        $objResponse->assign("submit_auction_bid", "disabled", true);
        $objResponse->assign("submit_auction_bid", "style.color", '#f00');
    }
    // Zu wenig Rohstoffe auf dem Planeten
    elseif (
        $val['new_buy_0'] > $val['res_0']
        || $val['new_buy_1'] > $val['res_1']
        || $val['new_buy_2'] > $val['res_2']
        || $val['new_buy_3'] > $val['res_3']
        || $val['new_buy_4'] > $val['res_4']
    ) {
        $out_auction_check_message = "<div style=\"color:red;font-weight:bold;\">Es sind zu wenig Rohstoffe vorhanden!</div>";

        $objResponse->assign("submit_auction_bid", "disabled", true);
        $objResponse->assign("submit_auction_bid", "style.color", '#f00');
    }
    // Gebot ist tiefer als das vom Höchstbietenden
    elseif ($buy_price * (1 + AUCTION_OVERBID) >= $new_buy_price) {
        $out_auction_check_message = "<div style=\"color:red;font-weight:bold;\">Das Gebot muss mindestens " . AUCTION_OVERBID . "% höher sein als das Gebot des Höchstbietenden!</div>";

        $objResponse->assign("submit_auction_bid", "disabled", true);
        $objResponse->assign("submit_auction_bid", "style.color", '#f00');
    }
    // Zeit ist abgelaufen
    elseif ($val['auction_rest_time'] <= 0) {
        $out_auction_check_message = "<div style=\"color:red;font-weight:bold;\">Auktion ist beendet!</div>";

        $objResponse->assign("submit_auction_bid", "disabled", true);
        $objResponse->assign("submit_auction_bid", "style.color", '#f00');
    }
    // Angebot ist OK
    else {
        $out_auction_check_message = "<div style=\"color:#0f0;font-weight:bold;\">OK!</div>";
        $objResponse->assign("submit_auction_bid", "disabled", false);
        $objResponse->assign("submit_auction_bid", "style.color", '#0f0');

        $objResponse->assign("auction_show_check_submit", "value", 1);
    }


    // Bestätigt, dass XAJAX das Formular vor dem Absenden nochmal kontrolliert hat
    $objResponse->assign("auction_show_last_update", "value", $last_update);

    // XAJAX ändert Daten
    foreach ($resNames as $rid => $r) {
        $objResponse->assign("auction_min_max_" . $rid, "innerHTML", $outMinMax[$rid]);
        $objResponse->assign("new_buy_" . $rid, "value", nf($val['new_buy_' . $rid]));
    }


    $objResponse->assign("auction_check_message", "innerHTML", $out_auction_check_message);

    $objResponse->assign("marketinfo", "innerHTML", ob_get_contents());
    ob_end_clean();

    return $objResponse;
}
