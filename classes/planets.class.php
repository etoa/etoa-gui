<?PHP


	//
	// Planetenklasse
	//
	// Diese Klasse beinhaltet alle Planeten des Spielers als Objekte (weiter oben definiert)
	//

	class Planets
	{
		function Planets($cur_id=0)
		{
			global $conf,$db_table;

			$this->currentId=$cur_id;

			$sql = "
			SELECT
				planets.*,
				space_cells.*,
				races.race_f_metal,
				races.race_f_crystal,
				races.race_f_plastic,
				races.race_f_fuel,
				races.race_f_food,
				races.race_f_power,
				races.race_f_population,
				races.race_f_researchtime,
				races.race_f_buildtime,
				sol_types.type_name as sol_type_name,
				sol_types.type_f_metal as sol_type_f_metal,
				sol_types.type_f_crystal as sol_type_f_crystal,
				sol_types.type_f_plastic as sol_type_f_plastic,
				sol_types.type_f_fuel as sol_type_f_fuel,
				sol_types.type_f_food as sol_type_f_food,
				sol_types.type_f_power as sol_type_f_power,
				sol_types.type_f_population as sol_type_f_population,
				sol_types.type_f_researchtime as sol_type_f_researchtime,
				sol_types.type_f_buildtime as sol_type_f_buildtime,
				planet_types.type_name as planet_type_name,
				planet_types.type_f_metal as planet_type_f_metal,
				planet_types.type_f_crystal as planet_type_f_crystal,
				planet_types.type_f_plastic as planet_type_f_plastic,
				planet_types.type_f_fuel as planet_type_f_fuel,
				planet_types.type_f_food as planet_type_f_food,
				planet_types.type_f_power as planet_type_f_power,
				planet_types.type_f_population as planet_type_f_population,
				planet_types.type_f_researchtime as planet_type_f_researchtime,
				planet_types.type_f_buildtime as planet_type_f_buildtime
            FROM 
                (
                    (
                        (
                                planets
                            INNER JOIN 
                                planet_types 
                            ON planets.planet_type_id = planet_types.type_id
                        )
                        INNER JOIN 
                        (   
                            space_cells
                            INNER JOIN sol_types ON space_cells.cell_solsys_solsys_sol_type = sol_types.type_id
                        )
                        ON planets.planet_solsys_id = space_cells.cell_id
                    )
                    INNER JOIN 
                        users 
                    ON planets.planet_user_id = users.user_id
                    AND planets.planet_user_id='".$_SESSION[ROUNDID]['user']['id']."'
                )
                INNER JOIN 
                    races 
                ON users.user_race_id = races.race_id        
			ORDER BY
				planets.planet_user_main DESC,
				planets.planet_name,
				planets.planet_solsys_id ASC,
				planets.planet_solsys_pos ASC                       
            ";
				
				
			/*Alte Form
			FROM
				".$db_table['planets'].",
				".$db_table['space_cells'].",
				".$db_table['sol_types'].",
				".$db_table['users'].",
				".$db_table['races'].",
				".$db_table['planet_types']."
			WHERE
				planets.planet_user_id=users.user_id
				AND users.user_race_id=races.race_id
				AND space_cells.cell_solsys_solsys_sol_type=sol_types.type_id
				AND planets.planet_type_id=planet_types.type_id
				AND planets.planet_solsys_id=space_cells.cell_id
				AND planets.planet_user_id='".$_SESSION[ROUNDID]['user']['id']."'
			ORDER BY
				planets.planet_user_main DESC,
				planets.planet_name,
				planets.planet_solsys_id ASC,
				planets.planet_solsys_pos ASC;*/
			$pres = dbquery($sql);

			$first_time = false;
			// Falls der User noch keinen Planeten besitzt einen zuweisen
			if (mysql_num_rows($pres)==0)
			{
				$tres = dbquery("
				SELECT
					planets.planet_id
				FROM
					(
                            ".$db_table['planets']."
                        INNER JOIN
                            ".$db_table['planet_types']." 
                        ON planets.planet_type_id=planet_types.type_id
                        AND planet_types.type_habitable=1
					)
					INNER JOIN
						".$db_table['space_cells']."
					ON  planets.planet_solsys_id=space_cells.cell_id
					AND planets.planet_fields>'".$conf['user_min_fields']['v']."'
					AND planets.planet_user_id='0'
				ORDER BY
					RAND()
				LIMIT 1");				
				if (mysql_num_rows($tres)==0)
				{
					die("Es hat keine freien Planeten mehr! Wende dich an einen Administrator, Kontaktadressen findest du auf der Login-Seite!");
				}
				$tarr = mysql_fetch_array($tres);
				reset_planet($tarr['planet_id']);

				// Set default resources
				dbquery("
				UPDATE
					".$db_table['planets']."
				SET
                    planet_user_id=".$_SESSION[ROUNDID]['user']['id'].",
                    planet_user_main=1,
                    planet_name='".USR_PLANET_NAME."',
                    planet_res_metal='".USR_START_METAL."',
                    planet_res_crystal='".USR_START_CRYSTAL."',
                    planet_res_plastic='".USR_START_PLASTIC."',
                    planet_res_fuel='".USR_START_FUEL."',
                    planet_res_food='".USR_START_FOOD."',
                    planet_people=".USR_START_PEOPLE."
				WHERE
					planet_id=".$tarr['planet_id'].";");
				$this->currentId=$tarr['planet_id'];
				$pres = dbquery($sql);
				
				$first_time = true;
			}

			$cpid=false;
			$pids=array();
			while ($parr=mysql_fetch_array($pres))	// Alle Planeten durchgehen
			{
				array_push($pids,$parr['planet_id']);
				$this->own->$parr['planet_id']=new Planet($parr);
				if ($this->currentId==0)	// Aktueller Planet zuweisen falls noch nicht definiert
					$this->currentId=$parr['planet_id'];
				if ($this->currentId==$parr['planet_id'])	// Planetendaten des aktuellen Planeten verlinken
				{
					$cpid=true;
					$this->current = $this->own->$parr['planet_id'];
				}
			}
			
			// Update for the first time
			if ($first_time)
			{
				$this->current->update(1);
			}
			$this->first_time = $first_time;

			// Vorheriger und nÃ¤chster Planet bestimmen
			for ($x=0;$x<count($pids);$x++)
			{
				if ($pids[$x]==$this->currentId)
				{
					if ($x==0)
						$this->prevId=$pids[count($pids)-1];
					else
						$this->prevId=$pids[$x-1];
					if ($x+1==count($pids))
						$this->nextId=$pids[0];
					else
						$this->nextId=$pids[$x+1];
				}
			}

			if ($this->currentId==0 || !$cpid)
			{
				echo "Du kannst keine unbekannten oder fremde Planeten kontrollieren!<br/><br/><a href=\"".LOGINSERVER_URL."\">Logout!</a>";
				session_destroy();
				exit;
			}
		}

		static function getFreePlanet($sx=0,$sy=0)
		{
			$cfg = Config::getInstance();
			$sql = "
			SELECT
				planets.planet_id
			FROM
			(
      	planets
	      INNER JOIN
          planet_types
      	ON planets.planet_type_id=planet_types.type_id
      		AND planet_types.type_habitable=1
			)
			INNER JOIN
				space_cells
			ON  planets.planet_solsys_id=space_cells.cell_id
				AND planets.planet_fields>'".$cfg->value('user_min_fields')."'
				AND planets.planet_user_id='0'";
			if ($sx>0)
				$sql.=" AND cell_sx=".$sx." ";
			if ($sy>0)
				$sql.=" AND cell_sy=".$sy." ";
				
			$sql.="ORDER BY
					RAND()
			LIMIT 1";
			$tres = dbquery($sql);				
			if (mysql_num_rows($tres)==0)
			{
				return false;
			}
			$tarr = mysql_fetch_row($tres);			
			return $tarr[0];
		}


		function getCurrentId()
		{
			return $this->currentId;
		}

		function getCurrentData()
		{
			return $this->current;
		}

		function toSelectField()
		{
			global $page;
			echo "<select name=\"nav_mode_select\" id=\"nav_mode_select\" onchange=\"changeNav(this.selectedIndex,'".$page."')\">";
			foreach ($this->own as $id=>$p)
			{
				echo "<option value=\"".$id."\"";
				if ($this->currentId==$id)
					echo " selected=\"selected\"";
				echo ">".$p->getString()."</option>\n";
			}
			echo "</select>";
		}
		function getSelectField()
		{
			global $page;
			$str='';
			$str.= "<select name=\"nav_mode_select\" id=\"nav_mode_select\" onchange=\"changeNav(this.selectedIndex,'".$page."')\">";
			foreach ($this->own as $id=>$p)
			{
				$str.= "<option value=\"".$id."\"";
				if ($this->currentId==$id)
					$str.= " selected=\"selected\"";
				$str.= ">".$p->getString()."</option>\n";
			}
			$str.= "</select>";
			return $str;
		}		

		function toLinkList()
		{
			global $page;
			foreach ($this->own as $id=>$p)
			{
				if ($this->currentId==$id)
				echo "<a href=\"?page=$page&amp;planet_id=".$id."\"><b>".$p->getString()."</b></a>\n";
				else
				echo "<a href=\"?page=$page&amp;planet_id=".$id."\">".$p->getString()."</a>\n";
			}
		}
		
		function getLinkList()
		{
			global $page;
			$str = '';
			foreach ($this->own as $id=>$p)
			{
				if ($this->currentId==$id)
				$str.= "<a href=\"?page=$page&amp;planet_id=".$id."\"><b>".$p->getString()."</b></a>\n";
				else
				$str.= "<a href=\"?page=$page&amp;planet_id=".$id."\">".$p->getString()."</a>\n";
			}
			return $str;
		}		

	
		

	}

?>
