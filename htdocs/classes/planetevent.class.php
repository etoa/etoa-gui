<?PHP
	/**
	* Does a random event on a planet
	*/
	class PlanetEvent extends RandomEvent
	{
		private $planetId;
		
		function PlanetEvent($id,$pid)
		{
			parent::RandomEvent($id,"random_planet");
			$this->planetId = $pid;
		}
				
		function run()
		{	
			$res = dbquery("
			SELECT
				user_points,
				user_id,
				planet_name
			FROM
				planets
			INNER JOIN
				users
				ON planet_user_id=user_id
				AND id=".$this->planetId.";			
			");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				$points = $arr['user_points'];
				$uid = $arr['user_id'];

				$factor = 1;
				if ($this->xml->logbase && $this->xml->logbase>1)
				{
					$factor = log($points,intval($this->xml->logbase));
					$factor = max($factor,1);
				}
				
				$messageParsed = $this->message;
			
				$rewards = array();
				$rewards['metal'] = 0;
				$rewards['crystal'] = 0;
				$rewards['plastic'] = 0;
				$rewards['fuel'] = 0;
				$rewards['food'] = 0;
				$rewards['people'] = 0;

				$costs = array();
				$costs['metal'] = 0;
				$costs['crystal'] = 0;
				$costs['plastic'] = 0;
				$costs['fuel'] = 0;
				$costs['food'] = 0;
				$costs['people'] = 0;
								
				$deactivate = array();
								
				if ($this->xml->rewards)
				{
					$rw = $this->xml->rewards;					
					if($rw->metal)
					{
						$rewards['metal'] = mt_rand($rw->metal['min']*$factor, $rw->metal['max']*$factor);
					}
					if($rw->crystal)
					{
						$rewards['crystal'] = mt_rand($rw->crystal['min']*$factor, $rw->crystal['max']*$factor);
					}					
					if($rw->plastic)
					{
						$rewards['plastic'] = mt_rand($rw->plastic['min']*$factor, $rw->plastic['max']*$factor);
					}					
					if($rw->fuel)
					{
						$rewards['fuel'] = mt_rand($rw->fuel['min']*$factor, $rw->fuel['max']*$factor);
					}					
					if($rw->food)
					{
						$rewards['food'] = mt_rand($rw->food['min']*$factor, $rw->food['max']*$factor);
					}					
					if($rw->people)
					{
						$rewards['people'] = mt_rand($rw->people['min']*$factor, $rw->people['max']*$factor);
					}					
				}
				
				if ($this->xml->costs)
				{
					$cs = $this->xml->costs;					
					if($cs->metal)
					{
						$costs['metal'] = mt_rand($cs->metal['min']*$factor, $cs->metal['max']*$factor);
					}
					if($cs->crystal)
					{
						$costs['crystal'] = mt_rand($cs->crystal['min']*$factor, $cs->crystal['max']*$factor);
					}					
					if($cs->plastic)
					{
						$costs['plastic'] = mt_rand($cs->plastic['min']*$factor, $cs->plastic['max']*$factor);
					}					
					if($cs->fuel)
					{
						$costs['fuel'] = mt_rand($cs->fuel['min']*$factor, $cs->fuel['max']*$factor);
					}					
					if($cs->food)
					{
						$costs['food'] = mt_rand($cs->food['min']*$factor, $cs->food['max']*$factor);
					}					
					if($cs->people)
					{
						$costs['people'] = mt_rand($cs->people['min']*$factor, $cs->people['max']*$factor);
					}					
				}				
				
				if ($this->xml->deactivate)
				{				
					foreach ($this->xml->deactivate as $d)
					{
						$deactivate[$d['id']] = mt_rand($d['min'],$d['max']);
						$t = time() + $deactivate[$d['id']];
						$messageParsed = str_replace('{deactivate:'.$d['id'].'}',tf($deactivate[$d['id']]),$messageParsed);
						dbquery("UPDATE
							buildlist
						SET
							buildlist_deactivated=".$t."
						WHERE
							buildlist_building_id=".$d['id']."
							AND buildlist_entity_id=".$this->planetId."
						");
						
					}				
				}
				

				$messageParsed = str_replace('{planet}',$arr['planet_name'],$messageParsed);

				$messageParsed = str_replace('{reward:metal}',$rewards['metal'],$messageParsed);
				$messageParsed = str_replace('{reward:crystal}',$rewards['crystal'],$messageParsed);
				$messageParsed = str_replace('{reward:plastic}',$rewards['plastic'],$messageParsed);
				$messageParsed = str_replace('{reward:fuel}',$rewards['fuel'],$messageParsed);
				$messageParsed = str_replace('{reward:food}',$rewards['food'],$messageParsed);
				$messageParsed = str_replace('{reward:people}',$rewards['people'],$messageParsed);

				$messageParsed = str_replace('{costs:metal}',$costs['metal'],$messageParsed);
				$messageParsed = str_replace('{costs:crystal}',$costs['crystal'],$messageParsed);
				$messageParsed = str_replace('{costs:plastic}',$costs['plastic'],$messageParsed);
				$messageParsed = str_replace('{costs:fuel}',$costs['fuel'],$messageParsed);
				$messageParsed = str_replace('{costs:food}',$costs['food'],$messageParsed);
				$messageParsed = str_replace('{costs:people}',$costs['people'],$messageParsed);
				
				dbquery("
				UPDATE
					planets
				SET
					planet_res_metal = planet_res_metal + ".$rewards['metal']." - ".$costs['metal'].",
					planet_res_crystal = planet_res_crystal + ".$rewards['crystal']." - ".$costs['crystal'].",
					planet_res_plastic = planet_res_plastic + ".$rewards['plastic']." - ".$costs['plastic'].",
					planet_res_fuel = planet_res_fuel + ".$rewards['fuel']." - ".$costs['fuel'].",
					planet_res_food = planet_res_food + ".$rewards['food']." - ".$costs['food'].",
					planet_people = planet_people + ".$rewards['people']." - ".$costs['people']."
				WHERE
					id=".$this->planetId."
				");

				send_msg($uid,MISC_MSG_CAT_ID,$this->title,$messageParsed);

			}
			else
			{
				echo "Event-Fehler: Planet nicht gefunden!";
			}
		
					
		}		
	}	





?>