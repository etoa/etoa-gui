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
		'spy'=>'Spionagebericht',
		'surveillance'=>'Raum&uuml;berwachung',
		'spyfailed'=>'Spionage fehlgeschlagen',
		'surveillancefailed'=>'Raum&uuml;berwachung (verhindert)',
		'analyze'=>'Ziel analysiert',
		'analyzefailed'=>'Analyseversuch gescheitert',

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

    public function __construct($args)
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
				$this->def = $arr['defense'];
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

	static function add($data)
	{
		return null;
	}

	function createSubject()
	{
		switch ($this->subType)
		{
			case 'spy':
				$ent1 = Entity::createFactoryById($this->entity1Id);
				return 'Spionagebericht '.$ent1;
			case 'spyfailed':
				$ent1 = Entity::createFactoryById($this->entity1Id);
				return 'Spionage fehlgeschlagen auf '.$ent1;
			default:
				return self::$subTypes[$this->subType];
		}
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
			case 'spy':
				echo '<strong>Planet:</strong> '.$ent1->detailLink().'<br />';
				echo '<strong>Besitzer:</strong> '.$user.'<br />';

				if ($this->buildings!='')
				{
					echo '<br /><strong>GEB&Auml;UDE:</strong><br />';
					if ($this->buildings=='0')
						echo '<i>Nichts vorhanden!</i><br />';
					else
					{
						echo '<table>';
						$buildArr = explode(',',$this->buildings);
						$buildings = Building::getItems();
						foreach ($buildArr as $building)
						{
							if ($building!='')
							{
								$data = explode(':',$building);
								echo '<tr>
										<td>'.$buildings[$data[0] ].' </td>
										<td style="text-align:right;"> '.$data[1].'</td>
									</tr>';
							}
						}
						echo '</table>';
					}
				}
				if ($this->technologies!='')
				{
					echo '<br /><strong>TECHNOLOGIEN:</strong><br />';
					if ($this->technologies=='0')
						echo '<i>Nichts vorhanden!</i><br />';
					else
					{
						echo '<table>';
						$techArr = explode(',',$this->technologies);
						$technologies = Technology::getItems();
						foreach ($techArr as $tech)
						{
							if ($tech!='')
							{
								$data = explode(':',$tech);
								echo '<tr>
										<td>'.$technologies[$data[0] ].' </td>
										<td style="text-align:right;"> '.$data[1].'</td>
									</tr>';
							}
						}
						echo '</table>';
					}
				}
				if ($this->ships!='')
				{
					echo '<br /><strong>SCHIFFE:</strong><br />';
					if ($this->ships=='0')
						echo '<i>Nichts vorhanden!</i><br />';
					else
					{
						echo '<table>';
						$shipArr = explode(',',$this->ships);
						$ships = Ship::getItems();
						foreach ($shipArr as $ship)
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
				}
				if ($this->def!='')
				{
					echo '<br /><strong>VERTEIDIGUNG:</strong><br />';
					if ($this->def=='0')
						echo '<i>Nichts vorhanden!</i><br />';
					else
					{
						echo '<table>';
						$defArr = explode(',',$this->def);
						$def = Defense::getItems();
						foreach ($defArr as $defense)
						{
							if ($defense!='')
							{
								$data = explode(':',$defense);
								echo '<tr>
										<td>'.$def[$data[0] ].' </td>
										<td style="text-align:right;"> '.nf($data[1]).'</td>
									</tr>';
							}
						}
						echo '</table>';
					}
				}
				if (array_sum($this->res)>0)
				{
					echo '<br /><strong>ROHSTOFFE:</strong><br />';
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
				}
				echo '<br /><strong>Spionageabwehr:</strong> '.$this->spydefense.'%<br />';
				echo '<strong>Tarnung:</strong> '.$this->coverage.'%';
				break;

			case 'surveillance':
				echo 'Eine fremde Flotte vom Planeten '.$ent2->detailLink().' wurde in der Nähe deines Planeten '.$ent1->detailLink().' gesichtet!<br /><br />';
				echo '<strong>Spionageabwehr:</strong> '.$this->spydefense.'%';
				break;

			case 'surveillancefailed':
				echo 'Auf deinem Planeten '.$ent1->detailLink().' wurde ein Spionageversuch vom Planeten '.$ent2->detailLink().' erfolgreich verhindert!<br /><br />';
				echo '<strong>Spionageabwehr:</strong> '.$this->spydefense.'%';
				break;

			case 'spyfailed':
				echo 'Dein Versuch, den Planeten'.$ent1->detailLink().' auszuspionieren schlug fehl, da du entdeckt wurdest. Deine Sonden kehren ohne Ergebniss zurück!<br /><br />';
				echo '<strong>Spionageabwehr:</strong> '.$this->spydefense.'%';
				break;
			case 'analyze':
				echo 'Eine Flotte vom Planeten '.$ent2->detailLink().' hat das Ziel '.$ent1->detailLink().' um '.df($this->timestamp).' analysiert.<br /><br />';
				echo '<br /><strong>ROHSTOFFE:</strong><br />';
				echo '<table>';
				foreach ($resNames as $k=>$v)
				{
						echo '<tr><td>'.$v.' </td><td style="text-align:right;"> '.nf($this->res[$k]).'</td></tr>';
				}
				echo '<tr><td>Bewohner </td><td style="text-align:right;"> '.nf($this->res[5]).'</td></tr>';
				echo '</table><br/>';
				break;
			case 'analyzefaild':
				echo 'Eine Flotte vom Planeten '.$ent2->detailLink().' versuchte das Ziel '.$ent1->detailLink().' um '.df($this->timestamp).' zu analysiert, kehrte jedoch erfolglos wieder zurück.<br />';
				break;

			default:
				dump($this);
		}

		return ob_get_clean();
	}

}
?>
