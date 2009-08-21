<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of spyreport
 *
 * @author glaubinix
 */
class SpyReport extends Report
{
	static $subTypes = array(
		'antrax'=>'Antraxangriff',
		'antraxfailed'=>'Antraxangriff erfolglos',
		'bombard'=>'Gebäude bombardiert',
		'bombardfailed'=>'Bombardierung erfolglos',
		'emp'=>'Deaktivierung',
		'empfailed'=>'Deaktivierung erfolglos',
		'gasattack'=>'Giftgasangriff',
		'gasattackfailed'=>'Giftgasangriff erfolglos',
		'invasion'=>'Planet erfolgreich invasiert',
		'invasionfailed'=>'Invasionsversuch gescheitert',
		'invaded'=>'Kolonie wurde invasiert',
		'invadedfailed'=>'Invasionsversuch abgewehrt',
		'spyattack'=>'Spionageangriff',
		'spyattackfailed'=>'Spionageangriff erfolglos',
		
	);

	protected $subType = 'other';
	protected $buildings;
	protected $technologies;
	protected $ships;
	protected $def;
	protected $res;
	protected $spydefense;
	protected $coverage;
	protected $fleetId;
	
	function __construct($args)
	{
		global $resNames;
		parent::__construct($args);
		if ($this->valid)
		{
			$res = dbquery("SELECT * FROM reports_spy WHERE id=".$this->id.";");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_assoc($res);
				$this->subType = $arr['subtype'];
				$this->buildings = $arr['buildings'];
				$this->technologies = $arr['technologies'];
				$this->ships = $arr['ships'];
				$this->def = $arr['def'];
				$this->res = array();
				foreach ($resNames as $rk => $rn)
					$this->res[$rk] = $arr['res_'.$rk];
				$this->res[5] = $arr['res_5'];
				$this->spydefense = $arr['spydefense'];
				$this->coverage = $arr['coverage'];
				$this->fleetId = $arr['fleet_id'];
			}
			else
			{
				$this->valid = false;
				return;
			}
		}
	}

	static function add($data,$subType,$recordId,$spyData)
	{
		global $resNames;

		$id = parent::add(array_merge($data,array("type"=>"spy")));
		if ($id!=null)
		{
			$fs = "";
			$vs = "";
			foreach ($resNames as $rk => $rn)
			{
				if (isset($spyData['res_'.$rk]))
				{
					$fs.= ",res_".$rk." ";
					$vs.= ",".$spyData['res_'.$rk]." ";
				}
			}
			if (isset($spyData['factor']) && $spyData['factor']>0)
			{
				$fs.= ",factor ";
				$vs.= ",".$marketData['factor']." ";
			}
			if (isset($spyData['fleet_id']) && $spyData['fleet_id']>0)
			{
				$fs.= ",fleet_id ";
				$vs.= ",".$spyData['fleet_id']." ";
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

	function __toString()
	{
		global $resNames;

		ob_start();
		$ent1 = Entity::createFactoryById($this->entity1Id);
		$ent2 = Entity::createFactoryById($this->entity2Id);
		$user = new User($this->opponent1Id);
		
		switch ($this->subType)
		{
			case 'antrax':
				echo 'Eine Flotte vom Planeten '.$ent2->detailLink().' hat einen Antraxangriff auf den Planeten '.$ent1->detailLink().' verübt. Es starben dabei '.PEOPLE.' Bewohner und '.FOOD.' t Nahrung wurden verbrannt.';
				break;
			case 'antraxfailed':
				echo 'Eine Flotte vom Planeten '.$ent2->detailLink().' hat erfolglos einen Antraxangriff auf den Planeten '.$ent1->detailLink().' verübt.';
				break;
			case 'bombard':
				echo 'Eine Flotte vom Planeten '.$ent2->detailLink().' hat';
				break;
			case 'bombardfailed':
				echo 'Eine Flotte vom Planeten '.$ent2->detailLink().' hat erfolglos versucht ein Gebäude des Planeten '.$ent1->detailLink().' zu bombardieren.';
				break;
			case 'emp':
				echo 'Eine Flotte vom Planeten '.$ent2->detailLink().' hat';
				break;
			case 'empfailed':
				echo 'Eine Flotte vom Planeten '.$ent2->detailLink().' hat erfolglos versucht ein Gebäude des Planeten '.$ent1->detailLink().' zu deaktivieren.<br />';
				if ($this->content==1)
					echo 'Hinweis: Der Spieler hat keine Gebäudeeinrichtungen, welche deaktiviert werden können!';
				break;
			case 'gasattack':
				echo 'Eine Flotte vom Planeten '.$ent2->detailLink().' hat einen Giftgasangriff auf den Planeten '.$ent1->detailLink().' verübt. Es starben dabei '.PEOPLE.' Bewohner.';
				break;
			case 'gasattackfailed':
				echo 'Eine Flotte vom Planeten '.$ent2->detailLink().' hat erfolglos einen Giftgasangriff auf den Planeten '.$ent1->detailLink().' verübt.';
				break;
			case 'invasion':
				echo '<strong>Planet:</strong> '.$ent2->detailLink().'<br />';
				echo '<strong>Besitzer:</strong> '.$user.'<br /><br />';
				echo 'Dieser Planet wurde von einer Flotte, welche vom Planeten '.$ent1->detailLink().' stammt, &uuml;bernommen!<br />Ein Invasionsschiff wurde bei der &Uuml;bernahme aufgebraucht!<br /><br />';
				echo '<strong>SCHIFFE</strong><br />';
				if ($this->ships=='' || $this->ships=='0')
					echo '<i>Nichts vorhanden!</i><br />';
				else
				{
					echo '<table>';
					$shipArr = explode(',',$this->ships);
					$ships = Ship::getItems();
					foreach ($shipArr as $ships)
					{
						if ($ship!='')
						{
							$data = explode(':',$ship);
							echo '<tr>
									<td>'.$ships[$data[0] ].' </td>
									<td style="text-align:right;"> '.nf($data[1]).'</td>
								</tr>';
						}
					}
					echo '</table>';
				}
				
				echo '<br /><strong>WAREN</strong><br />';
				echo '<table>';
				foreach ($resNames as $k=>$v)
				{
						echo '<tr>
						<td>'.$v.' </td>
						<td style="text-align:right;"> '.nf($this->res[$k]).'</td>
						</tr>';
				}
				echo '<tr>
						<td>Bewohner </td>
						<td style="text-align:right;"> '.nf($this->res[5]).'</td>
					</tr>';
				echo '</table><br/>';
				break;
			case 'invasionfailed':
				echo 'Eine Flotte vom Planeten '.$ent2->detailLink().' hatversucht den Planeten '.$ent1->detailLink().' zu &uuml;bernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den R&uuml;ckweg!<br />';
				if ($this->content==1)
					echo 'Hinweis: Du hast bereits die maximale Anzahl Planeten erreicht!<br />';
				elseif ($this->content==2)
					echo 'Hinweis: Dies ist ein Hauptplanet!<br />';
				break;
			case 'invaded':
				echo '<strong>Planet:</strong> '.$ent2->detailLink().'<br />';
				echo '<strong>Besitzer:</strong> '.$user.'<br /><br />';
				echo 'Dieser Planet wurde von einer Flotte, welche vom Planeten '.$ent1->detailLink().' stammt, &uuml;bernommen!<br />';
				break;
			case 'invadedfailed':
				echo 'Eine Flotte vom Planeten '.$ent2->detailLink().' hat versucht den Planeten '.$ent1->detailLink().' zu &uuml;bernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den R&uuml;ckweg!';
				break;
			case 'spyattack':
				echo 'Eine Flotte vom Planeten '.$ent2->detailLink().' hat erfolgreich einen Spionageangriff durchgeführt und erfuhr so die Geheimnisse der Forschung';
				break;
			case 'spyattackfailed':
				echo 'Eine Flotte vom Planeten '.$ent2->detailLink().' hat erfolglos einen Spionageangriff auf den Planeten '.$ent1->detailLink().' ver&uuml;bt.';
				break;
			
			default:
				dump($this);
		}

		return ob_get_clean();
	}

}
?>
