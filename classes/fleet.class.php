<?PHP

class Fleet
{
	function Fleet($user_id, $action)
	{
		$this->user_id = $user_id;
		$this->ships=array();		
		$this->fuel = 0;
		$this->distance = 0;
		$this->duration = 0;
		$this->speed = -1;
		$this->time2start = -1;
		$this->time2land = -1;
		$this->pilots = 0;
		$this->action = $action;
	}
	
	function setSourceByPlanetId($id)
	{		
		$res = dbquery("
		SELECT
			cell_sx,
			cell_sy,
			cell_cx,
			cell_cy,
			cell_id,
			planet_id,
			planet_solsys_pos,
			planet_user_id
		FROM
			planets
		INNER JOIN
			space_cells
			ON planet_solsys_id=cell_id
			AND planet_id='".$id."'				
		");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			$this->source->id = $arr['cell_id'];
			$this->source->sx = $arr['cell_sx'];
			$this->source->sy = $arr['cell_sy'];
			$this->source->cx = $arr['cell_cx'];
			$this->source->cy = $arr['cell_cy'];
			$this->source->pp = $arr['planet_solsys_pos'];			
			$this->source->user_id = $arr['planet_user_id'];
			$this->source->planet_id = $id;
			return true;
		}		
		return false;		
	}		

	function setSourceByCoords($sx,$sy,$cx,$cy,$pp)
	{
		global $conf;
		
		if ($sx > 0 && $sy > 0 && $cx > 0 && $cy > 0 &&
		$sx <= $conf['num_of_sectors']['p1'] &&
		$sy <= $conf['num_of_sectors']['p2'] &&
		$cx <= $conf['num_of_cells']['p1'] &&
		$cy <= $conf['num_of_cells']['p2'] &&
		$pp <= $conf['num_planets']['p2']
		)
		{
			$res = dbquery("
			SELECT
				cell_id,
				cell_solsys_num_planets
			FROM
				space_cells
			WHERE
				cell_sx='".$sx."',
				cell_sy='".$sy."',
				cell_cx='".$cx."',
				cell_cy='".$cy."'
			");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array();
				$this->source->id = $arr['cell_id'];
				$this->source->sx = $sx;
				$this->source->sy = $sy;
				$this->source->cx = $cx;
				$this->source->cy = $cy;
				if ($arr['cell_solsys_num_planets']>0)
				{
					$pres = dbquery("
					SELECT
						planet_id,
						planet_user_id
					FROM
						planets
					WHERE
						planet_solsys_id=".$arr['cell_id']."
						AND planet_solsys_pos='".$pp."'
					");
					if (mysql_num_rows($pres)>0)
					{
						mysql_fetch_row($res);
						$this->source->pp = $pp;					
						$this->source->planet_id = $parr[0];
						$this->source->user_id = $parr[1];

					}
					else
					{
						return false;
					}
				}
				else
				{
					$this->source->pp = 0;	
				}
				return true;				
			}
		}
		return false;
	}
	
	function setTargetByPlanetId($id)
	{		
		$res = dbquery("
		SELECT
			cell_sx,
			cell_sy,
			cell_cx,
			cell_cy,
			cell_id,
			planet_id,
			planet_solsys_pos,
			planet_user_id
		FROM
			planets
		INNER JOIN
			space_cells
			ON planet_solsys_id=cell_id
			AND planet_id='".$id."'				
		");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			$this->target->id = $arr['cell_id'];
			$this->target->sx = $arr['cell_sx'];
			$this->target->sy = $arr['cell_sy'];
			$this->target->cx = $arr['cell_cx'];
			$this->target->cy = $arr['cell_cy'];
			$this->target->pp = $arr['planet_solsys_pos'];	
			$this->target->user_id = $arr['planet_user_id'];
			$this->target->planet_id = $id;
			return true;
		}		
		return false;		
	}	
	
	function setTargetByCellId($id)
	{		
		global $conf;
		
			$res = dbquery("
			SELECT
				cell_sx,
				cell_sy,
				cell_cx,
				cell_cy,
				cell_solsys_num_planets
			FROM
				space_cells
			WHERE
				cell_id='".$id."'				
			");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array();
				$this->target->id = $id;
				$this->target->sx = $arr['cell_sx'];
				$this->target->sy = $arr['cell_sy'];
				$this->target->cx = $arr['cell_cx'];
				$this->target->cy = $arr['cell_cy'];
				if ($arr['cell_solsys_num_planets']>0)
				{
					$pres = dbquery("
					SELECT
						planet_id,
						planet_solsys_pos,
						planet_user_id
					FROM
						planets
					WHERE
						planet_solsys_id=".$arr['cell_id']."
					");
					if (mysql_num_rows($pres)>0)
					{
						mysql_fetch_row($res);
						$this->target->pp = $parr[1];					
						$this->target->planet_id = $parr[0];
						$this->target->user_id = $parr[2];
					}
					else
					{
						return false;
					}
				}
				else
				{
					$this->target->pp = 0;	
				}
				return true;				
			}
		
		return false;		
	}
	
	function setTargetByCoords($sx,$sy,$cx,$cy,$pp=0)
	{
		global $conf;
		
		if ($sx > 0 && $sy > 0 && $cx > 0 && $cy > 0 &&
		$sx <= $conf['num_of_sectors']['p1'] &&
		$sy <= $conf['num_of_sectors']['p2'] &&
		$cx <= $conf['num_of_cells']['p1'] &&
		$cy <= $conf['num_of_cells']['p2'] &&
		$pp <= $conf['num_planets']['p2']
		)
		{
			$res = dbquery("
			SELECT
				cell_id,
				cell_solsys_num_planets
			FROM
				space_cells
			WHERE
				cell_sx='".$sx."',
				cell_sy='".$sy."',
				cell_cx='".$cx."',
				cell_cy='".$cy."'
			");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array();
				$this->target->id = $arr['cell_id'];
				$this->target->sx = $sx;
				$this->target->sy = $sy;
				$this->target->cx = $cx;
				$this->target->cy = $cy;
				if ($arr['cell_solsys_num_planets']>0)
				{
					$pres = dbquery("
					SELECT
						planet_id,
						planet_user_id
					FROM
						planets
					WHERE
						planet_solsys_id=".$arr['cell_id']."
						AND planet_solsys_pos='".$pp."'
					");
					if (mysql_num_rows($pres)>0)
					{
						mysql_fetch_row($res);
						$this->target->pp = $pp;					
						$this->target->planet_id = $parr[0];
						$this->target->user_id = $parr[1];

					}
					else
					{
						return false;
					}
				}
				else
				{
					$this->target->pp = 0;	
				}
				return true;				
			}
		}
		return false;
	}
	
	function addShip($id,$count)	
	{
		if ($count>0)
		{
			$res = dbquery("
			SELECT
				ship_speed,
				ship_fuel_use,
				ship_fuel_use_launch,
				ship_fuel_use_landing,
				ship_capacity,
		  	ship_people_capacity,
				ship_pilots,
				ship_time2start,
				ship_time2land
			FROM
				ships
			WHERE
				ship_id=".$id.";
			");
			if (mysql_num_rows($res))
			{
				$arr=mysql_fetch_array($res);

				$this->ships[$id]->count=$count;
				$this->ships[$id]->speed=$arr['ship_speed'];
				$this->ships[$id]->time2start=$arr['ship_time2start'];
				$this->ships[$id]->time2land=$arr['ship_time2land'];
				$this->ships[$id]->fuel_use=$count*$arr['ship_fuel_use'];
				$this->ships[$id]->fuel_use_launch=$count*$arr['ship_fuel_use_launch'];
				$this->ships[$id]->fuel_use_landing=$count*$arr['ship_fuel_use_landing'];
				$this->ships[$id]->capacity=$count*$arr['ship_capacity'];
				$this->ships[$id]->people_capacity=$count*$arr['ship_people_capacity'];
				$this->ships[$id]->pilots=$count*$arr['ship_pilots'];

				return true;
			}
		}
		return false;
	}
	
	function calcDist()
	{
		$this->distance = self::calcDistance($this->source->sx,
																					$this->source->sy,
																					$this->source->cx,
																					$this->source->cy,
																					$this->source->pp,
																					$this->target->sx,
																					$this->target->sy,
																					$this->target->cx,
																					$this->target->cy,
																					$this->target->pp);
	}
	
	static function calcDistance($sx1,$sy1,$cx1,$cy1,$pp1,$sx2,$sy2,$cx2,$cy2,$pp2)
	{
		global $conf;
		// Calc time and distance
		$nx=$conf['num_of_cells']['p1'];		// Anzahl Zellen Y
		$ny=$conf['num_of_cells']['p2'];		// Anzahl Zellen X
		$ae=$conf['cell_length']['v'];			// Länge vom Solsys in AE
		$np=$conf['num_planets']['p2'];			// Max. Planeten im Solsys
		$dx = abs(((($sx2-1) * $nx) + $cx2) - ((($sx1-1) * $nx) + $cx1));
		$dy = abs(((($sy2-1) * $nx) + $cy2) - ((($sy1-1) * $nx) + $cy1));
		$sd = sqrt(pow($dx,2)+pow($dy,2));			// Distanze zwischen den beiden Zellen
		$sae = $sd * $ae;											// Distance in AE units
		if ($sx1==$sx2 && $sy1==$sy2 && $cx1==$cx2 && $cy1=$cy2)
			$ps = abs($pp2-$pp1)*$ae/4/$np;				// Planetendistanz wenn sie im selben Solsys sind
		else
			$ps = ($ae/2) - (($pp2)*$ae/4/$np);	// Planetendistanz wenn sie nicht im selben Solsys sind
		$ssae = $sae + $ps;
		return $ssae;	
	}		

	function calcFlight()
	{
		// Rassenspeed laden
		$rres=dbquery("SELECT race_f_fleettime FROM races INNER JOIN users on user_race_id=race_id AND user_id=".$this->user_id.";");
		$rarr=mysql_fetch_array($rres);
		if ($rarr['race_f_fleettime']!=1)
		{
			$timefactor =  2-$rarr['race_f_fleettime'];
		}
		else
		{
			$timefactor =  1;
		}		
		
		foreach ($this->ships as $sid => $s)
		{
	    $vres=dbquery("
	    SELECT
	        techlist.techlist_current_level,
	        technologies.tech_name,
	        ship_requirements.req_req_tech_level
	    FROM
	       techlist,
	       ship_requirements,
	       technologies
	    WHERE
	        ship_requirements.req_ship_id=".$sid."
	        AND technologies.tech_type_id='".TECH_SPEED_CAT."'
	        AND ship_requirements.req_req_tech_id=technologies.tech_id
	        AND technologies.tech_id=techlist.techlist_tech_id
	        AND techlist.techlist_tech_id=ship_requirements.req_req_tech_id
	        AND techlist.techlist_user_id=".$this->user_id."
	    GROUP BY
	        ship_requirements.req_id;");		
	    $speedfactor+=$timefactor;
		 	if (mysql_num_rows($vres)>0)
	    {
		    while ($varr=mysql_fetch_array($vres))
		    {
		      if($varr['techlist_current_level']-$varr['req_req_tech_level']<=0)
		      {
		          $speedfactor+=0;
		      }
		      else
		      {
		          $speedfactor+=($varr['techlist_current_level']-$varr['req_req_tech_level'])*0.1;
		      }
		    }
	    }	    
	   	$s->speed*=$speedfactor;
			$s->speed/=FLEET_FACTOR_F;			
			
			if ($this->speed == -1)	
				$this->speed = $s->speed;
			else
				$this->speed = min($this->speed,$s->speed);
			if ($this->time2start == -1)	
				$this->time2start = $s->time2start;
			else
				$this->time2start = max($s->time2start,$this->time2start);
			if ($this->time2land == -1)	
				$this->time2land = $s->time2land;
			else
				$this->time2land = max($s->time2land,$this->time2land);
			$this->fuel += (($this->distance * $s->fuel_use / 100) + $s->fuel_use_launch + $s->fuel_use_landing)*$s->count;			
		}		
		
 		$this->duration = ($this->distance / $this->speed * 3600) + $this->time2start + $this->time2land;	
	}

	function launch()
	{
		// Subtract fuel
		dbquery("
		UPDATE
			planets
		SET
			planet_res_fuel = planet_res_fuel-".$this->fuel."
		WHERE
			planet_id=".$this->source->planet_id."
		;");		                
			                
		// Create fleet entry
		$t = time();	
		dbquery("
		INSERT INTO
			fleet
		(
			fleet_user_id,
			fleet_cell_from,
			fleet_cell_to,
			fleet_planet_from,
			fleet_planet_to,
			fleet_launchtime,
			fleet_landtime,
			fleet_action
		)
		VALUES
		(
			".$this->user_id.",
			".$this->source->id.",
			".$this->target->id.",
			".$this->source->planet_id.",
			".$this->target->planet_id.",
			".$t.",
			".($t+$this->duration).",
			'".$this->action."'
		)
		");		
		$fid = mysql_insert_id();
		// Create ships entry and remove ships from list
		if ($fid >0)
		{
			foreach ($this->ships as $id => $s)
			{
				dbquery("UPDATE 
					shiplist 
				SET 
					shiplist_count = shiplist_count - ".$s->count." 
				WHERE 
					shiplist_ship_id=".$id."
					AND shiplist_planet_id=".$this->source->planet_id."
					AND shiplist_user_id=".$this->source->user_id."
					AND shiplist_count>=".$s->count."
				;");
				if (mysql_affected_rows()>0)
				{
					dbquery("
					INSERT INTO
						fleet_ships
					(
						fs_fleet_id,
						fs_ship_id,
						fs_ship_cnt
					)
					VALUES
					(
						".$fid.",
						".$id.",
						".$s->count."				
					);");
				}
			}
			return true;
		}
		return false;		
	}
	
}






?>