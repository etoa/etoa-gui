<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use EtoA\Ship\ShipDataRepository;
use EtoA\Support\StringUtils;

/**
 * Description of marketreport
 *
 * @author Nicolas
 */
class MarketReport extends Report
{
    static $subTypes = array(
        'resadd' => 'Rohstoffangebot eingestellt',
        'rescancel' => 'Rohstoffangebot zurückgezogen',
        'ressold' => 'Rohstoffe verkauft',
        'resbought' => 'Rohstoffe gekauft',
        'shipadd' => 'Schiffangebot eingestellt',
        'shipcancel' => 'Schiffangebot zurückgezogen',
        'shipsold' => 'Schiffe verkauft',
        'shipbought' => 'Schiffe gekauft',
        'auctionadd' => 'Auktion hinzugefügt',
        'auctioncancel' => 'Auktion abgebrochen',
        'auctionbid' => 'Gebot abgegeben',
        'auctionoverbid' => 'Überboten',
        'auctionwon' => 'Auktion gewonnen',
        'auctionfinished' => 'Auktion beendet',
    );

    protected $subType = 'other';
    protected $recordId = 0;
    protected $factor = 1.0;
    /** @var int[] */
    protected $resSell;
    /** @var int[] */
    protected $resBuy;
    protected $fleet1Id;
    protected $fleet2Id;
    protected $shipId;
    protected $shipCount;
    protected $timestamp2;

    public function __construct($args)
    {
        global $resNames;
        parent::__construct($args);
        if ($this->valid) {
            $res = dbquery("SELECT * FROM reports_market WHERE id=" . $this->id . ";");
            if (mysql_num_rows($res) > 0) {
                $arr = mysql_fetch_assoc($res);
                $this->subType = $arr['subtype'];
                $this->recordId = $arr['record_id'];
                $this->factor = $arr['factor'];
                foreach ($resNames as $rk => $rn) {
                    $this->resSell[$rk] = $arr['sell_' . $rk];
                    $this->resBuy[$rk] = $arr['buy_' . $rk];
                }
                $this->fleet1Id = $arr['fleet1_id'];
                $this->fleet2Id = $arr['fleet2_id'];
                $this->shipId = $arr['ship_id'];
                $this->shipCount = $arr['ship_count'];
                $this->timestamp2 = $arr['timestamp2'];
            } else {
                $this->valid = false;
                return;
            }
        }
    }

    function createSubject()
    {
        return self::$subTypes[$this->subType];
    }

    function __toString()
    {
        global $resNames, $app;

        ob_start();
        $ent = Entity::createFactoryById($this->entity1Id);
        switch ($this->subType) {
            case "resadd":
                echo "Du hast folgendes Angebot (#" . $this->recordId . ") im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=" . $this->entity1Id . "\">Marktplatz</a>
				auf " . $ent->detailLink() . " eingestellt:<br/><br/>";
                if ($this->content != "")
                    echo $this->content . "<br/><br/>";
                echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
                echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Angebot:</th>
				<th>Preis:</th>
				</tr>";
                foreach ($resNames as $k => $v) {
                    if ($this->resSell[$k] + $this->resBuy[$k] > 0)
                        echo "<tr>
						<td>" . $v . "</td>
						<td>" . StringUtils::formatNumber($this->resSell[$k]) . "</td>
						<td>" . StringUtils::formatNumber($this->resBuy[$k]) . "</td>
						</tr>";
                }
                echo "</table><br/>";
                echo "Die Marktgebühr beträgt: " . round(($this->factor - 1) * 100, 2) . "%.";
                break;

            case "rescancel":
                echo "Du hast das Angebot #" . $this->recordId . " im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=" . $this->entity1Id . "\">Marktplatz</a>
				auf " . $ent->detailLink() . " abgebrochen!<br/><br/>";
                echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
                echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Angebot:</th>
				<th>Preis:</th>
				<th>Retour:</th>
				</tr>";
                foreach ($resNames as $k => $v) {
                    if ($this->resSell[$k] + $this->resBuy[$k] > 0)
                        echo "<tr>
						<td>" . $v . "</td>
						<td>" . StringUtils::formatNumber($this->resSell[$k]) . "</td>
						<td>" . StringUtils::formatNumber($this->resBuy[$k]) . "</td>
						<td>" . StringUtils::formatNumber($this->resSell[$k] * $this->factor) . "</td>
						</tr>";
                }
                echo "</table><br/>";
                echo "Es wurden " . round($this->factor * 100) . "% der Rohstoffe zurückerstattet.";
                break;

            case "ressold":
                $op = new User($this->opponent1Id);
                $ent2 = Entity::createFactoryById($this->entity2Id);

                //				echo "Du hast folgendes Angebot (#".$this->recordId.") im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=".$this->entity1Id."\">Marktplatz</a>
                //				auf ".$ent->detailLink()." an ".$op->detailLink()." auf ".$ent2->detailLink()." verkauft:<br/><br/>";
                echo "Du hast folgendes Angebot (#" . $this->recordId . ") im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=" . $this->entity1Id . "\">Marktplatz</a>
				auf " . $ent->detailLink() . " an " . $op->detailLink() . " verkauft:<br/><br/>";
                if ($this->content != "")
                    echo $this->content . "<br/><br/>";
                echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
                echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Angebot:</th>
				<th>Preis:</th></tr>";
                foreach ($resNames as $k => $v) {
                    if ($this->resSell[$k] + $this->resBuy[$k] > 0)
                        echo "<tr>
						<td>" . $v . "</td>
						<td>" . StringUtils::formatNumber($this->resSell[$k]) . "</td>
						<td>" . StringUtils::formatNumber($this->resBuy[$k]) . "</td>
						</tr>";
                }
                echo "</table><br/>";
                $buyerFleet = new Fleet($this->fleet2Id);
                if ($buyerFleet->valid())
                    echo " Landung: " . df($buyerFleet->landTime()) . "";
                break;

            case "resbought":
                $op = new User($this->opponent1Id);
                $ent2 = Entity::createFactoryById($this->entity2Id);
                $sellerFleet = new Fleet($this->fleet2Id);
                echo "Du hast folgendes Angebot (#" . $this->recordId . ") von " . $op->detailLink() . " gekauft:<br/><br/>";
                if ($this->content != "")
                    echo $this->content . "<br/><br/>";
                echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
                echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Angebot:</th>
				<th>Preis:</th>
				</tr>";
                foreach ($resNames as $k => $v) {
                    if ($this->resSell[$k] + $this->resBuy[$k] > 0)
                        echo "<tr>
						<td>" . $v . "</td>
						<td>" . StringUtils::formatNumber($this->resSell[$k]) . "</td>
						<td>" . StringUtils::formatNumber($this->resBuy[$k]) . "</td>
						</tr>";
                }
                echo "</table><br/>";
                echo "Die Waren werden vom Marktplatz nach " . $ent->detailLink() . " geliefert.";
                if ($sellerFleet->valid())
                    echo " Landung: " . df($sellerFleet->landTime()) . "";
                break;

            case "shipadd":
                /** @var ShipDataRepository $shipRepository */
                $shipRepository = $app[ShipDataRepository::class];
                $shipNames = $shipRepository->getShipNames(true);

                echo "Du hast folgendes Angebot (#" . $this->recordId . ") im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=" . $this->entity1Id . "\">Marktplatz</a>
				auf " . $ent->detailLink() . " eingestellt:<br/><br/>";
                if ($this->content != "")
                    echo $this->content . "<br/><br/>";
                echo "" . StringUtils::formatNumber($this->shipCount) . " <b>" . $shipNames[$this->shipId] . "</b> <br/><br/> ";
                echo "zu einem Preis von: <br/><br/>";
                echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
                echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Preis:</th>
				</tr>";
                foreach ($resNames as $k => $v) {
                    if ($this->resSell[$k] + $this->resBuy[$k] > 0)
                        echo "<tr>
						<td>" . $v . "</td>
						<td>" . StringUtils::formatNumber($this->resBuy[$k]) . "</td>
						</tr>";
                }
                echo "</table><br/>";
                break;

            case "shipcancel":
                /** @var ShipDataRepository $shipRepository */
                $shipRepository = $app[ShipDataRepository::class];
                $shipNames = $shipRepository->getShipNames(true);

                echo "Du hast das Angebot #" . $this->recordId . " im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=" . $this->entity1Id . "\">Marktplatz</a>
				auf " . $ent->detailLink() . " abgebrochen!<br/><br/>";
                echo "" . StringUtils::formatNumber($this->shipCount) . " <b>" . $shipNames[$this->shipId] . "</b> <br/><br/> ";
                echo "zu einem Preis von: <br/><br/>";
                echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
                echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Preis:</th>
				</tr>";
                foreach ($resNames as $k => $v) {
                    if ($this->resSell[$k] + $this->resBuy[$k] > 0)
                        echo "<tr>
						<td>" . $v . "</td>
						<td>" . StringUtils::formatNumber($this->resBuy[$k]) . "</td>
						</tr>";
                }
                echo "</table><br/>";
                echo "" . floor($this->shipCount * $this->factor) . " Schiffe (" . round($this->factor * 100) . "%) wurden zurückerstattet.";
                break;
            case "shipbought":
                /** @var ShipDataRepository $shipRepository */
                $shipRepository = $app[ShipDataRepository::class];
                $shipNames = $shipRepository->getShipNames(true);

                $op = new User($this->opponent1Id);
                $ent2 = Entity::createFactoryById($this->entity2Id);
                $sellerFleet = new Fleet($this->fleet2Id);
                echo "Du hast folgendes Angebot (#" . $this->recordId . ") von " . $op->detailLink() . " gekauft:<br/><br/>";
                echo "" . StringUtils::formatNumber($this->shipCount) . " <b>" . $shipNames[$this->shipId] . "</b> <br/><br/> ";
                echo "zu einem Preis von: <br/><br/>";
                echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
                echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Preis:</th>
				</tr>";
                foreach ($resNames as $k => $v) {
                    if ($this->resSell[$k] + $this->resBuy[$k] > 0)
                        echo "<tr>
						<td>" . $v . "</td>
						<td>" . StringUtils::formatNumber($this->resBuy[$k]) . "</td>
						</tr>";
                }
                echo "</table><br/>";

                echo "Die Waren werden vom Marktplatz nach " . $ent->detailLink() . " geliefert.";
                if ($sellerFleet->valid())
                    echo " Landung: " . df($sellerFleet->landTime()) . "";
                break;
            case "shipsold":
                /** @var ShipDataRepository $shipRepository */
                $shipRepository = $app[ShipDataRepository::class];
                $shipNames = $shipRepository->getShipNames(true);

                $op = new User($this->opponent1Id);
                $ent2 = Entity::createFactoryById($this->entity2Id);

                echo "Du hast folgendes Angebot (#" . $this->recordId . ") im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=" . $this->entity1Id . "\">Marktplatz</a>
				auf " . $ent->detailLink() . " an " . $op->detailLink() . " verkauft:<br/><br/>";
                echo "" . StringUtils::formatNumber($this->shipCount) . " <b>" . $shipNames[$this->shipId] . "</b> <br/><br/> ";
                echo "zu einem Preis von: <br/><br/>";
                echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
                echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Preis:</th>
				</tr>";
                foreach ($resNames as $k => $v) {
                    if ($this->resSell[$k] + $this->resBuy[$k] > 0)
                        echo "<tr>
						<td>" . $v . "</td>
						<td>" . StringUtils::formatNumber($this->resBuy[$k]) . "</td>
						</tr>";
                }
                echo "</table><br/>";
                echo "Die Waren werden vom Marktplatz nach " . $ent->detailLink() . " geliefert.";
                $buyerFleet = new Fleet($this->fleet2Id);
                if ($buyerFleet->valid())
                    echo " Landung: " . df($buyerFleet->landTime()) . "";
                break;
            case 'auctionadd':
                echo "Du hast folgendes Angebot (#" . $this->recordId . ") im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=" . $this->entity1Id . "\">Marktplatz</a>
				auf " . $ent->detailLink() . " eingestellt:<br/><br/>";
                if ($this->content != "")
                    echo $this->content . "<br/><br/>";
                echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
                echo "<tr>
					<th style=\"width:100px;\">Rohstoff:</th>
					<th>Angebot:</th>
					</tr>";
                foreach ($resNames as $k => $v) {
                    if ($this->resSell[$k] > 0)
                        echo "<tr>
						<td>" . $v . "</td>
						<td>" . StringUtils::formatNumber($this->resSell[$k]) . "</td>
						</tr>";
                }
                echo "</table><br/>";
                echo 'Die Auktion endet am ' . df($this->timestamp2) . '.';
                break;
            case 'auctioncancel':
                echo "Du hast das Angebot #" . $this->recordId . " im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=" . $this->entity1Id . "\">Marktplatz</a>
				auf " . $ent->detailLink() . " abgebrochen!<br/><br/>";
                echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
                echo "<tr>
					<th style=\"width:100px;\">Rohstoff:</th>
					<th>Angebot:</th>
					<th>Retour:</th>
					</tr>";
                foreach ($resNames as $k => $v) {
                    if ($this->resSell[$k] > 0)
                        echo "<tr>
							<td>" . $v . "</td>
							<td>" . StringUtils::formatNumber($this->resSell[$k]) . "</td>
							<td>" . StringUtils::formatNumber($this->resSell[$k] * $this->factor) . "</td>
						</tr>";
                }
                echo "</table><br/>";
                echo "Es wurden " . round($this->factor * 100) . "% der Rohstoffe zurückerstattet.";
                break;
            case 'auctionoverbid':
                $op = new User($this->opponent1Id);
                echo 'Du wurdest bei folgendem Angebot (#' . $this->recordId . ') von ' . $op->detailLink() . ' &uuml;berboten:<br/><br/>';
                if ($this->timestamp2)
                    echo 'Die Auktion dauert noch bis am ' . date("d.m.Y H:i", $this->timestamp2) . '<br />';
                else
                    echo "Die Auktion ist nun zu Ende und wird nach " . AUCTION_DELAY_TIME . " Stunden gel&ouml;scht.<br />";
                echo '<a href="?page=market&mode=auction&id=' . $this->id . '">Hier</a> gehts zu der Auktion.';
                break;
            case 'auctionfinished':
                $op = new User($this->opponent1Id);
                echo "Du hast folgendes Angebot (#" . $this->recordId . ") im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=" . $this->entity1Id . "\">Marktplatz</a>
				auf " . $ent->detailLink() . " an " . $op->detailLink() . " versteigert:<br/><br/>";
                echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
                echo "<tr>
					<th style=\"width:100px;\">Rohstoff:</th>
					<th>Angebot:</th>
					<th>Preis:</th>
					</tr>";
                foreach ($resNames as $k => $v) {
                    if ($this->resSell[$k] + $this->resBuy[$k] > 0)
                        echo "<tr>
							<td>" . $v . "</td>
							<td>" . StringUtils::formatNumber($this->resSell[$k]) . "</td>
							<td>" . StringUtils::formatNumber($this->resBuy[$k]) . "</td>
						</tr>";
                }
                echo "</table><br/>";
                echo 'Die Auktion wird nach ' . AUCTION_DELAY_TIME . ' Stunden gel&ouml;scht und die Waren werden in wenigen Minuten versendet.<br /><br />';
                break;
            case 'auctionwon':
                $op = new User($this->opponent1Id);
                echo 'Du hast folgendes Angebot (#' . $this->recordId . ') von ' . $op->detailLink() . ' ersteigert:<br/><br/>';
                echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
                echo "<tr>
					<th style=\"width:100px;\">Rohstoff:</th>
					<th>Angebot:</th>
					<th>Preis:</th>
					</tr>";
                foreach ($resNames as $k => $v) {
                    if ($this->resSell[$k] + $this->resBuy[$k] > 0)
                        echo "<tr>
							<td>" . $v . "</td>
							<td>" . StringUtils::formatNumber($this->resSell[$k]) . "</td>
							<td>" . StringUtils::formatNumber($this->resBuy[$k]) . "</td>
						</tr>";
                }
                echo "</table><br/>";
                echo 'Die Auktion wird nach ' . AUCTION_DELAY_TIME . ' Stunden gel&ouml;scht und die Waren werden in wenigen Minuten versendet.<br /><br />';
                break;
            default:
                etoa_dump($this);
        }

        return ob_get_clean();
    }
}
