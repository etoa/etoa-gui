<?PHP
	class PlanetEventHandler
	{
		function PlanetEventHandler()
		{
			
		}
		
		static function doEvent($cnt=1,$force=0,$forceEvent="")
		{
			$cfg = Config::getInstance();
			
			for ($i=0; $i < $cnt; $i++)
			{
				
				// Force using a planet which belong to a user. This is NOT high-performance
				if ($force==1)
				{
		      $res = dbquery("
		      SELECT
		      	id,
		      	planet_user_id
		      FROM
		      	planets
		      WHERE 
		      	planet_user_id>0
		      ORDER BY RAND()
		      LIMIT 1;
		      ");				
				}
				else
				{
					$res = dbquery("
					SELECT 
						planets.id,
						planet_user_id
		      FROM
		      	planets
		      JOIN 
		      (
		        SELECT (
		        	CEIL(
			          RAND( ) * (
			            SELECT MAX( planets.id )
			            FROM planets
			          )
		          )
		        ) AS randID
		      ) AS randTable
		      WHERE planets.id=randTable.randID
		  		  LIMIT 1;");
		  	}
	      
				if (mysql_num_rows($res)>0)
				{
					$arr = mysql_fetch_row($res);
					$pid = $arr[0];
					$uid = $arr[1];
					
					if ($uid>0)
					{
						if ($forceEvent!="")
						{
							$eventId = $forceEvent;
						}
						else
						{
							$eventId = RandomEvent::chooseFromDir("random_planet");
						}
						$evt = new PlanetEvent($eventId,$pid);
						$evt->run();
						$result = true;
						
						$cfg->set("random_event_hits",$cfg->get("random_event_hits")+1);
					}
					else
					{				
						$result = false;
						$cfg->set("random_event_misses",$cfg->get("random_event_misses")+1);
					}				
				}		
				else
				{
					echo "Event-Fehler: Kein Planet gefunden!";
				}	
				
			}
			return $result;	
		}	
		
		static function getEventList()
		{
			return RandomEvent::getList("random_planet");
		}
			
	}



?>