<?PHP
	
	/**
	* Provides methods for accessing 
	* the current logged in user
	*
	* @author Nicolas Perrenoud<mrcage@etoa.ch>
	*/
	class CurrentUser extends User
	{
		protected $property;
		
		/**
		* Constructor which calls the default parent constructor
		* and loads settings
		*/
		public function CurrentUser($userId)
		{
			parent::User($userId);
			$this->loadProperties();
			
			// Todo: remove and add where it is needed
			$this->loadRaceData($this->id);
		}

		//
		// Getters
		//
		
		final public function realName() { return $this->realName; }
		final public function email() { return $this->email; }
		final public function emailFix() { return $this->emailFix; }


		//
		// Methods
		//		

		/**
		* Loads the users personal settings 
		from the user settings table
		*/
		private function loadProperties()
		{
			$res = dbquery("
			SELECT 
				*
			FROM 
				user_properties
			WHERE 
				id='".$this->id."' 
			;");			
			if (mysql_num_rows($res)>0)		
			{			
				$arr = mysql_fetch_assoc($res);
				
				$this->property = array();
				
				$this->property['css_style'] = $arr['css_style'];
				$this->property['image_url'] = $arr['image_url'];
				$this->property['image_ext'] = $arr['image_ext'];
				$this->property['game_width'] = $arr['game_width'];
				$this->property['planet_circle_width'] = $arr['planet_circle_width'];
				$this->property['item_show'] = $arr['item_show'];
				$this->property['item_order_ship'] = $arr['item_order_ship'];
				$this->property['item_order_def'] = $arr['item_order_def'];
				$this->property['item_order_way'] = $arr['item_order_way'];
				$this->property['image_filter'] = $arr['image_filter'];
				$this->property['msgsignature'] = $arr['msgsignature'];
				$this->property['msgcreation_preview'] = $arr['msgcreation_preview'];
				$this->property['msg_preview'] = $arr['msg_preview'];
				$this->property['helpbox'] = $arr['helpbox'];
				$this->property['notebox'] = $arr['notebox'];
				$this->property['msg_copy'] = $arr['msg_copy'];
				$this->property['msg_blink'] = $arr['msg_blink'];
        $this->property['spyship_id'] = $arr['spyship_id'];
        $this->property['spyship_count'] = $arr['spyship_count'];
        $this->property['havenships_buttons'] = $arr['havenships_buttons'];
		    $this->property['show_adds'] = $arr['show_adds'];
		    $this->property['fleet_rtn_msg'] = $arr['fleet_rtn_msg'];
		    
        return true;
			}
			// This part should never be used, it is for repairing missing user settings only
			else
			{
				dbquery("
				INSERT INTO 
					user_properties
				(id)
				VALUES
				(".$this->id.")
				");
				// Take care: This is a recursion! With mysql_insert_id we check that the record has been created and thus 
				// the recursion should has to finish the next time
				if (mysql_insert_id()>0)
				{
					$this->loadSettings();
				}
			}
		}
		
    public function getp($property)
    {
      return $this->property[$property];
    }

    public function setp($property,$argument)
    {
    	if ($this->property[$property] != $argument)
    	{
    		$this->property[$property] = $argument;
    		dbquery("
    		UPDATE
    			user_properties
    		SET
    			".$property."='".$argument."'
    		WHERE
    			id=".$this->id."");
    		return true;
    	}
    }
		
		
		/**
		* Validates the user session against a given key
		*/
		public function validateSession($sessionKey)
		{
			$session_valid=false;
			if ($sessionKey!="")
			{
				// Valid browser values
				if (substr($sessionKey,64,32)==md5(ROUNDID) 
				&& substr($sessionKey,96,32)==md5($_SERVER['REMOTE_ADDR']) 
				&& substr($sessionKey,128,32)==md5($_SERVER['HTTP_USER_AGENT']) 
				&& substr($sessionKey,160)==session_id() )
				{
					// Valid user valies
					if ($this->lt=substr($sessionKey,0,32) && 
					$this->uid==substr($sessionKey,32,32) && 
					$this->sk==$sessionKey)
					{
						$session_valid=true;
					}
				}
			}
			return $session_valid;			
		}
		
		/**
		* Set setup status to false
		*/
		public function setNotSetup()
		{
			$this->setup = false;
		}
		
		/**
		* Loads data for the given race and sets it 
		* as the users race
		* THIS FUNCTION OVERLOADS THE DEFAULT
		*/
		private function loadRaceData($raceId)
		{
			$rres = dbquery("
			SELECT
				race_name,
		  	race_f_researchtime,
				race_f_buildtime,
				race_f_fleettime,
				race_f_metal,
				race_f_crystal,
				race_f_plastic,
				race_f_fuel,
				race_f_food,
				race_f_power,
				race_f_population		
			FROM
				races
			WHERE
				race_id=".$raceId."			
			");
			if (mysql_num_rows($rres)>0)
			{
				$rarr = mysql_fetch_assoc($rres);
		    $this->raceId = $raceId;
				$this->raceName = $rarr['race_name'];
				$this->raceResearchtime = $rarr['race_f_researchtime'];
				$this->raceBuildtime = $rarr['race_f_buildtime'];
				$this->raceFleettime = $rarr['race_f_fleettime'];
				$this->raceMetal = $rarr['race_f_metal'];
				$this->raceCrystal = $rarr['race_f_crystal'];
				$this->racePlastic = $rarr['race_f_plastic'];
				$this->raceFuel = $rarr['race_f_fuel'];
				$this->raceFood = $rarr['race_f_food'];
				$this->racePower = $rarr['race_f_power'];
				$this->racePopulation = $rarr['race_f_population'];
				return true;
			}

	    $this->raceId = 0;
	    $this->raceName = "Keine Rasse";
			$this->raceResearchtime = 1;
			$this->raceBuildtime = 1;
			$this->raceFleettime = 1;
			$this->raceMetal = 1;
			$this->raceCrystal = 1;
			$this->racePlastic = 1;
			$this->raceFuel = 1;
			$this->raceFood = 1;
			$this->racePower = 1;
			$this->racePopulation = 1;
			return false;
		}

		function setSetupFinished()
		{
	    $sql = "
	    UPDATE
	    	users
	    SET
				user_setup=1
	    WHERE
	    	user_id='".$this->id."';";
	    dbquery($sql);
	    $this->setup=true;					
		}

		function loadDiscoveryMask()
		{
			$res = dbquery("
			SELECT
				discoverymask
			FROM				
				users
			WHERE
				user_id=".$this->id()."
			");
			$this->dmask = '';
			$arr = mysql_fetch_row($res);
			if ($arr[0]=='')
			{
				for ($x=1;$x<=30;$x++)
				{
					for ($y=1;$y<=30;$y++)
					{
						$this->dmask.= '0';
					}
				}
			}
			else
			{
				$this->dmask=$arr[0];
			}			
		}

		function discovered($absX,$absY)
		{
			$cfg = Config::getInstance();
			$sy_num=$cfg->param2('num_of_sectors');
			$cy_num=$cfg->param2('num_of_cells');
			
			if (!isset($this->dmask))
			{
				$this->loadDiscoveryMask();
			}	
			
			$pos = $absX + ($cy_num*$sy_num)*($absY-1)-1;
			return ($this->dmask{$pos}%4);		
		}
		
		function setDiscovered($absX,$absY,$owner=1,$save=1)
		{
			$cfg = Config::getInstance();
			$sx_num=$cfg->param1('num_of_sectors');
			$cx_num=$cfg->param1('num_of_cells');
			$sy_num=$cfg->param2('num_of_sectors');
			$cy_num=$cfg->param2('num_of_cells');
			
			for ($x=$absX-1; $x<=$absX+1; $x++)
			{
				for ($y=$absY-1; $y<=$absY+1; $y++)
				{
					$pos = $x + ($cy_num*$sy_num)*($y-1)-1;
					if ($pos>= 0 && $pos <= $sx_num*$sy_num*$cx_num*$cy_num)
					{
						if ($owner==1)
						{
							$this->dmask{$pos} = '5';				
						}
						else
						{
							$this->dmask{$pos} = '1';
						}
					}
				}
			}	
			
			if ($save==1)
			{
				$this->saveDiscoveryMask();
			}			
		}	

		function saveDiscoveryMask()
		{
			dbquery("
			UPDATE
				users
			SET
				discoverymask='".$this->dmask."'
			WHERE
				user_id=".$this->id()."
			");
		}
		
		function raceSpeedFactor()
		{
			if ($this->raceFleettime!=1)
			{
				return 2-$this->raceFleettime;
			}
			else
			{
				return 1;
			}		
		}

		function setPassword($oldPassword, $newPassword1, $newPassword2, &$returnMsg)
		{
			$res = dbquery("
			SELECT
				COUNT(user_id)
			FROM
				users
			WHERE
				user_password='".pw_salt($oldPassword,$this->registered)."'
				AND user_id=".$this->id.";");
			$arr = mysql_fetch_row($res);
			if ($arr[0]>0)
			{
				$res = dbquery("
				SELECT 
					COUNT(user_sitting_sitter_password) 
				FROM 
					user_sitting
				WHERE 
					user_sitting_sitter_password='".md5($_POST['user_password1'])."' 
					AND user_sitting_user_id=".$this->id.";");
				$arr = mysql_fetch_row($res);				
				if ($arr[0]==0)
				{
						if ($newPassword1==$newPassword2)
						{
								if (strlen($newPassword1)>=PASSWORD_MINLENGHT)
								{
										if (dbquery("
											UPDATE
											 	users
											SET
												user_password='".pw_salt($newPassword1,$this->registered)."'
											WHERE
												user_id='".$this->id()."'
											;"))
										{
											add_log(3,"Der Spieler [b]".$this->nick()."[/b] &auml;ndert sein Passwort!",time());
											send_mail("",$this->email,"Passwortänderung","Hallo ".$this->nick."\n\nDies ist eine Bestätigung, dass du dein Passwort für deinen Account erfolgreich geändert hast!\n\nSolltest du dein Passwort nicht selbst geändet haben, so nimm bitte sobald wie möglich Kontakt mit einem Game-Administrator auf: http://www.etoa.ch/?page=kontakt","","");
											$this->addToUserLog("settings","{nick} ändert sein Passwort.",0);
											return true;
										}
								}
								else
								{
									$returnMsg = "Das Passwort muss mindestens ".PASSWORD_MINLENGHT." Zeichen lang sein!";
								}
						}
						else
						{
							$returnMsg="Die Eingaben m&uuml;ssen identisch sein!";
						}
				}
				else
				{
					$returnMsg="Das Passwort darf nicht identisch mit dem Sitterpasswort sein!";
				}
			}
			else
			{
				$returnMsg = "Dein altes Passwort stimmt nicht mit dem gespeicherten Passwort &uuml;berein!";
			}
			return false;
		}
	
	}  
	
	

?>