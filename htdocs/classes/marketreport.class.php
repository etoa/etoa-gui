<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of marketreport
 *
 * @author Nicolas
 */
class MarketReport extends Report
{
	static $subTypes = array(
		'resadd'=>'Rohstoffangebot eingestellt',
		'rescancel'=>'Rohstoffangebot zurückgezogen',
		'ressold'=>'Rohstoffe verkauft',
		'resbought'=>'Rohstoffe gekauft',
		'shipadd'=>'Schiffangebot eingestellt',
		'shipcancel'=>'Schiffangebot zurückgezogen',
		'shipsold'=>'Schiffe verkauft',
		'shipbought'=>'Schiffe gekauft',
		'auctionadd'=>'Auktion hinzugefügt',
		'auctioncancel'=>'Auktion abgebrochen',
		'auctionbid'=>'Gebot abgegeben',
		'auctionoverbid'=>'Überboten',
		'auctionwon'=>'Auktion gewonnen',
		'auctionfinished'=>'Auktion beendet',
	);

	protected $subType = 'other';
	protected $recordId=0;
	protected $factor=1.0;
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
		if ($this->valid)
		{
			$res = dbquery("SELECT * FROM reports_market WHERE id=".$this->id.";");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_assoc($res);
				$this->subType = $arr['subtype'];
				$this->recordId = $arr['record_id'];
				$this->factor = $arr['factor'];
				foreach ($resNames as $rk => $rn)
				{
					$this->resSell[$rk] = $arr['sell_'.$rk];
					$this->resBuy[$rk] = $arr['buy_'.$rk];
				}
				$this->fleet1Id = $arr['fleet1_id'];
				$this->fleet2Id = $arr['fleet2_id'];
				$this->shipId = $arr['ship_id'];
				$this->shipCount = $arr['ship_count'];
				$this->timestamp2 = $arr['timestamp2'];
			}
			else
			{
				$this->valid = false;
				return;
			}
		}
	}

	static function addMarketReport($data,$subType,$recordId,$marketData)
	{
		global $resNames;

		$id = parent::add(array_merge($data,array("type"=>"market")));
		if ($id!=null)
		{
			$fs = "";
			$vs = "";
			foreach ($resNames as $rk => $rn)
			{
				if (isset($marketData['sell_'.$rk]))
				{
					$fs.= ",sell_".$rk." ";
					$vs.= ",".$marketData['sell_'.$rk]." ";
				}
				if (isset($marketData['buy_'.$rk]))
				{
					$fs.= ",buy_".$rk." ";
					$vs.= ",".$marketData['buy_'.$rk]." ";
				}
			}
			if (isset($marketData['factor']) && $marketData['factor']>0)
			{
				$fs.= ",factor ";
				$vs.= ",".$marketData['factor']." ";
			}
			if (isset($marketData['fleet1_id']) && $marketData['fleet1_id']>0)
			{
				$fs.= ",fleet1_id ";
				$vs.= ",".$marketData['fleet1_id']." ";
			}
			if (isset($marketData['fleet2_id']) && $marketData['fleet2_id']>0)
			{
				$fs.= ",fleet2_id ";
				$vs.= ",".$marketData['fleet2_id']." ";
			}
			if (isset($marketData['ship_id']) && $marketData['ship_id']>0)
			{
				$fs.= ",ship_id ";
				$vs.= ",".$marketData['ship_id']." ";
			}
			if (isset($marketData['ship_count']) && $marketData['ship_count']>0)
			{
				$fs.= ",ship_count ";
				$vs.= ",".$marketData['ship_count']." ";
			}
			if (isset($marketData['timestamp2']) && $marketData['timestamp2']>0)
			{
				$fs.= ",timestamp2 ";
				$vs.= ",".$marketData['timestamp2']." ";
			}
			dbquery("INSERT INTO
				reports_market
			(
				id,
				subtype,
				record_id
				".$fs."
			)
			VALUES
			(
				".$id.",
				'".(isset(self::$subTypes[$subType]) ? $subType : 'other')."',
				".intval($recordId)."
				".$vs."
			)
			");
			return $id;
		}
		return null;
	}

	function createSubject()
	{
		return self::$subTypes[$this->subType];
	}

	function __toString()
	{
		global $resNames;

		ob_start();
		$ent = Entity::createFactoryById($this->entity1Id);
		switch ($this->subType)
		{
			case "resadd":
				echo "Du hast folgendes Angebot (#".$this->recordId.") im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=".$this->entity1Id."\">Marktplatz</a>
				auf ".$ent->detailLink()." eingestellt:<br/><br/>";
				if ($this->content !="")
					echo $this->content."<br/><br/>";
				echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
				echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Angebot:</th>
				<th>Preis:</th>
				</tr>";
				foreach ($resNames as $k=>$v)
				{
					if ($this->resSell[$k] + $this->resBuy[$k]>0)
						echo "<tr>
						<td>".$v."</td>
						<td>".nf($this->resSell[$k])."</td>
						<td>".nf($this->resBuy[$k])."</td>
						</tr>";
				}
				echo "</table><br/>";
				echo "Die Marktgebühr beträgt: ".round(($this->factor-1)*100,2)."%.";
				break;

			case "rescancel":
				echo "Du hast das Angebot #".$this->recordId." im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=".$this->entity1Id."\">Marktplatz</a>
				auf ".$ent->detailLink()." abgebrochen!<br/><br/>";
				echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
				echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Angebot:</th>
				<th>Preis:</th>
				<th>Retour:</th>
				</tr>";
				foreach ($resNames as $k=>$v)
				{
					if ($this->resSell[$k] + $this->resBuy[$k]>0)
						echo "<tr>
						<td>".$v."</td>
						<td>".nf($this->resSell[$k])."</td>
						<td>".nf($this->resBuy[$k])."</td>
						<td>".nf($this->resSell[$k]*$this->factor)."</td>
						</tr>";
				}
				echo "</table><br/>";
				echo "Es wurden ".round($this->factor*100)."% der Rohstoffe zurückerstattet.";
				break;

			case "ressold":
				$op = new User($this->opponent1Id);
				$ent2 = Entity::createFactoryById($this->entity2Id);

//				echo "Du hast folgendes Angebot (#".$this->recordId.") im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=".$this->entity1Id."\">Marktplatz</a>
//				auf ".$ent->detailLink()." an ".$op->detailLink()." auf ".$ent2->detailLink()." verkauft:<br/><br/>";
				echo "Du hast folgendes Angebot (#".$this->recordId.") im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=".$this->entity1Id."\">Marktplatz</a>
				auf ".$ent->detailLink()." an ".$op->detailLink()." verkauft:<br/><br/>";
				if ($this->content !="")
					echo $this->content."<br/><br/>";
				echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
				echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Angebot:</th>
				<th>Preis:</th></tr>";
				foreach ($resNames as $k=>$v)
				{
					if ($this->resSell[$k] + $this->resBuy[$k]>0)
						echo "<tr>
						<td>".$v."</td>
						<td>".nf($this->resSell[$k])."</td>
						<td>".nf($this->resBuy[$k])."</td>
						</tr>";
				}
				echo "</table><br/>";
				$buyerFleet = new Fleet($this->fleet2Id);
				if ($buyerFleet->valid())
					echo " Landung: ".df($buyerFleet->landTime())."";
				break;

			case "resbought":
				$op = new User($this->opponent1Id);
				$ent2 = Entity::createFactoryById($this->entity2Id);
				$sellerFleet = new Fleet($this->fleet2Id);
				echo "Du hast folgendes Angebot (#".$this->recordId.") von ".$op->detailLink()." gekauft:<br/><br/>";
				if ($this->content !="")
					echo $this->content."<br/><br/>";
				echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
				echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Angebot:</th>
				<th>Preis:</th>
				</tr>";
				foreach ($resNames as $k=>$v)
				{
					if ($this->resSell[$k] + $this->resBuy[$k]>0)
						echo "<tr>
						<td>".$v."</td>
						<td>".nf($this->resSell[$k])."</td>
						<td>".nf($this->resBuy[$k])."</td>
						</tr>";
				}
				echo "</table><br/>";
				echo "Die Waren werden vom Marktplatz nach ".$ent->detailLink()." geliefert.";
				if ($sellerFleet->valid())
					echo " Landung: ".df($sellerFleet->landTime())."";
				break;

			case "shipadd":
				echo "Du hast folgendes Angebot (#".$this->recordId.") im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=".$this->entity1Id."\">Marktplatz</a>
				auf ".$ent->detailLink()." eingestellt:<br/><br/>";
				if ($this->content !="")
					echo $this->content."<br/><br/>";
				$ts = new Ship($this->shipId);
				echo "".nf($this->shipCount)." <b>".$ts."</b> <br/><br/> ";
				echo "zu einem Preis von: <br/><br/>";
				echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
				echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Preis:</th>
				</tr>";
				foreach ($resNames as $k=>$v)
				{
					if ($this->resSell[$k] + $this->resBuy[$k]>0)
						echo "<tr>
						<td>".$v."</td>
						<td>".nf($this->resBuy[$k])."</td>
						</tr>";
				}
				echo "</table><br/>";
				break;

			case "shipcancel":
				echo "Du hast das Angebot #".$this->recordId." im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=".$this->entity1Id."\">Marktplatz</a>
				auf ".$ent->detailLink()." abgebrochen!<br/><br/>";
				$ts = new Ship($this->shipId);
				echo "".nf($this->shipCount)." <b>".$ts."</b> <br/><br/> ";
				echo "zu einem Preis von: <br/><br/>";
				echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
				echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Preis:</th>
				</tr>";
				foreach ($resNames as $k=>$v)
				{
					if ($this->resSell[$k] + $this->resBuy[$k]>0)
						echo "<tr>
						<td>".$v."</td>
						<td>".nf($this->resBuy[$k])."</td>
						</tr>";
				}
				echo "</table><br/>";
				echo "".floor($this->shipCount*$this->factor)." Schiffe (".round($this->factor*100)."%) wurden zurückerstattet.";
				break;
			case "shipbought":
				$op = new User($this->opponent1Id);
				$ent2 = Entity::createFactoryById($this->entity2Id);
				$sellerFleet = new Fleet($this->fleet2Id);
				echo "Du hast folgendes Angebot (#".$this->recordId.") von ".$op->detailLink()." gekauft:<br/><br/>";
				$ts = new Ship($this->shipId);
				echo "".nf($this->shipCount)." <b>".$ts."</b> <br/><br/> ";
				echo "zu einem Preis von: <br/><br/>";
				echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
				echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Preis:</th>
				</tr>";
				foreach ($resNames as $k=>$v)
				{
					if ($this->resSell[$k] + $this->resBuy[$k]>0)
						echo "<tr>
						<td>".$v."</td>
						<td>".nf($this->resBuy[$k])."</td>
						</tr>";
				}
				echo "</table><br/>";

				echo "Die Waren werden vom Marktplatz nach ".$ent->detailLink()." geliefert.";
				if ($sellerFleet->valid())
					echo " Landung: ".df($sellerFleet->landTime())."";
				break;
			case "shipsold":
				$op = new User($this->opponent1Id);
				$ent2 = Entity::createFactoryById($this->entity2Id);

				echo "Du hast folgendes Angebot (#".$this->recordId.") im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=".$this->entity1Id."\">Marktplatz</a>
				auf ".$ent->detailLink()." an ".$op->detailLink()." verkauft:<br/><br/>";
				$ts = new Ship($this->shipId);
				echo "".nf($this->shipCount)." <b>".$ts."</b> <br/><br/> ";
				echo "zu einem Preis von: <br/><br/>";
				echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
				echo "<tr>
				<th style=\"width:100px;\">Rohstoff:</th>
				<th>Preis:</th>
				</tr>";
				foreach ($resNames as $k=>$v)
				{
					if ($this->resSell[$k] + $this->resBuy[$k]>0)
						echo "<tr>
						<td>".$v."</td>
						<td>".nf($this->resBuy[$k])."</td>
						</tr>";
				}
				echo "</table><br/>";
				echo "Die Waren werden vom Marktplatz nach ".$ent->detailLink()." geliefert.";
				$buyerFleet = new Fleet($this->fleet2Id);
				if ($buyerFleet->valid())
					echo " Landung: ".df($buyerFleet->landTime())."";
				break;
			case 'auctionadd':
				echo "Du hast folgendes Angebot (#".$this->recordId.") im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=".$this->entity1Id."\">Marktplatz</a>
				auf ".$ent->detailLink()." eingestellt:<br/><br/>";
				if ($this->content !="")
					echo $this->content."<br/><br/>";
				echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
				echo "<tr>
					<th style=\"width:100px;\">Rohstoff:</th>
					<th>Angebot:</th>
					</tr>";
				foreach ($resNames as $k=>$v)
				{
					if ($this->resSell[$k]>0)
						echo "<tr>
						<td>".$v."</td>
						<td>".nf($this->resSell[$k])."</td>
						</tr>";
				}
				echo "</table><br/>";
				echo 'Die Auktion endet am '.df($this->timestamp2).'.';
				break;
			case 'auctioncancel':
				echo "Du hast das Angebot #".$this->recordId." im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=".$this->entity1Id."\">Marktplatz</a>
				auf ".$ent->detailLink()." abgebrochen!<br/><br/>";
				echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
				echo "<tr>
					<th style=\"width:100px;\">Rohstoff:</th>
					<th>Angebot:</th>
					<th>Retour:</th>
					</tr>";
				foreach ($resNames as $k=>$v)
				{
					if ($this->resSell[$k]>0)
						echo "<tr>
							<td>".$v."</td>
							<td>".nf($this->resSell[$k])."</td>
							<td>".nf($this->resSell[$k]*$this->factor)."</td>
						</tr>";
				}
				echo "</table><br/>";
				echo "Es wurden ".round($this->factor*100)."% der Rohstoffe zurückerstattet.";
				break;
			case 'auctionoverbid':
				$op = new User($this->opponent1Id);
				echo 'Du wurdest bei folgendem Angebot (#'.$this->recordId.') von '.$op->detailLink().' &uuml;berboten:<br/><br/>';
				if ($this->timestamp2)
					echo 'Die Auktion dauert noch bis am '.date("d.m.Y H:i",$this->timestamp2).'<br />';
				else
					echo "Die Auktion ist nun zu Ende und wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht.<br />";
				echo '<a href="?page=market&mode=auction&id='.$this->id.'">Hier</a> gehts zu der Auktion.';
				break;
			case 'auctionfinished':
				$op = new User($this->opponent1Id);
				echo "Du hast folgendes Angebot (#".$this->recordId.") im <a href=\"?page=market&amp;mode=user_sell&amp;change_entity=".$this->entity1Id."\">Marktplatz</a>
				auf ".$ent->detailLink()." an ".$op->detailLink()." versteigert:<br/><br/>";
				echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
				echo "<tr>
					<th style=\"width:100px;\">Rohstoff:</th>
					<th>Angebot:</th>
					<th>Preis:</th>
					</tr>";
				foreach ($resNames as $k=>$v)
				{
					if ($this->resSell[$k] + $this->resBuy[$k]>0)
						echo "<tr>
							<td>".$v."</td>
							<td>".nf($this->resSell[$k])."</td>
							<td>".nf($this->resBuy[$k])."</td>
						</tr>";
				}
				echo "</table><br/>";
				echo 'Die Auktion wird nach '.AUCTION_DELAY_TIME.' Stunden gel&ouml;scht und die Waren werden in wenigen Minuten versendet.<br /><br />';
				break;
			case 'auctionwon':
				$op = new User($this->opponent1Id);
				echo 'Du hast folgendes Angebot (#'.$this->recordId.') von '.$op->detailLink().' ersteigert:<br/><br/>';
				echo "<table class=\"tb\" style=\"width:auto;margin:5px;\">";
				echo "<tr>
					<th style=\"width:100px;\">Rohstoff:</th>
					<th>Angebot:</th>
					<th>Preis:</th>
					</tr>";
				foreach ($resNames as $k=>$v)
				{
					if ($this->resSell[$k] + $this->resBuy[$k]>0)
						echo "<tr>
							<td>".$v."</td>
							<td>".nf($this->resSell[$k])."</td>
							<td>".nf($this->resBuy[$k])."</td>
						</tr>";
				}
				echo "</table><br/>";
				echo 'Die Auktion wird nach '.AUCTION_DELAY_TIME.' Stunden gel&ouml;scht und die Waren werden in wenigen Minuten versendet.<br /><br />';
				break;
			default:
				etoa_dump($this);
		}

		return ob_get_clean();
	}

}
?>
