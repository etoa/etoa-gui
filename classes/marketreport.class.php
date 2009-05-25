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
		'resbought'=>'Rohstoffe gekauft'
	);

	protected $subType = 'other';
	protected $recordId=0;
	protected $factor=1.0;
	protected $resSell;
	protected $resBuy;

	function __construct($args)
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
			}
			else
			{
				$this->valid = false;
				return;
			}
		}
	}

	static function add($data,$subType,$recordId,$marketData)
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
}
?>
