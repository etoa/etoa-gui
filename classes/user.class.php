<?PHP
	class User
	{
		private $id;
		private $setup;
		private $valid;
		
		function User($id)
		{
			$this->valid = false;
			$this->id = $id;

			$res = dbquery("
			SELECT 
				users.*,
				MD5(user_id) AS uid,
				MD5(user_logintime) AS lt,
				user_session_key AS sk
			FROM 
				users 
			WHERE 
				user_id='".$id."' 
			;");			
			if (mysql_num_rows($res)>0)		
			{
				$arr = mysql_fetch_assoc($res);

				// Those are for session controll				
				$this->uid = $arr['uid'];
				$this->lt = $arr['lt'];
				$this->sk = $arr['sk'];
				
				$this->acttime = $arr['user_acttime'];
		    $this->race_id = $arr['user_race_id'];
		    $this->blocked_from = $arr['user_blocked_from'];
		    $this->blocked_to = $arr['user_blocked_to'];
		    $this->ban_reason = $arr['user_ban_reason'];
		    $this->ban_admin_id = $arr['user_ban_admin_id'];
		    $this->hmode_from = $arr['user_hmode_from'];
		    $this->hmode_to = $arr['user_hmode_to'];
		    $this->points = $arr['user_points'];
		    $this->deleted = $arr['user_deleted'];
		    // Todo: change this
		    $this->alliance_application = $arr['user_alliance_application']!="" ? 1 : 0;
		    $this->registered = $arr['user_registered'];
		    $this->show_adds = $arr['user_show_adds'];
		    $this->setup = $arr['user_setup']==1 ? true : false;
	

				$this->id=$arr['user_id'];
				$this->nick=$arr['user_nick'];
				$this->email=$arr['user_email'];
				$this->last_online=$arr['user_last_online'];
				$this->race_id=$arr['user_race_id'];
				$this->points=$arr['user_points'];
				$this->alliance_id=$arr['user_alliance_id'];
				$this->css_style=$arr['user_css_style'];
				$this->game_width=$arr['user_game_width'];
				$this->planet_circle_width=$arr['user_planet_circle_width'];
				$this->image_url=$arr['user_image_url'];
				$this->image_ext=$arr['user_image_ext'];
				$this->item_show=$arr['user_item_show'];
				$this->item_order_ship=$arr['user_item_order_ship'];
				$this->item_order_def=$arr['user_item_order_def'];
				$this->item_order_way=$arr['user_item_order_way'];
				$this->image_filter=$arr['user_image_filter'];
				$this->blocked_from=$arr['user_blocked_from'];
				$this->blocked_to=$arr['user_blocked_to'];
				$this->hmode_from=$arr['user_hmode_from'];
				$this->hmode_to=$arr['user_hmode_to'];
				$this->helpbox=$arr['user_helpbox'];
				$this->notebox=$arr['user_notebox'];
				 
				$this->admin=$arr['user_admin']==1 ? true : false;
				$this->ip=$_SERVER['REMOTE_ADDR'];
				$this->msg_preview=$arr['user_msg_preview'];
				$this->msgcreation_preview=$arr['user_msgcreation_preview'];
				$this->msgsignature=$arr['user_msgsignature'];
				$this->msg_copy=$arr['user_msg_copy'];
				$this->msg_blink=$arr['user_msg_blink'];
			
				$this->specialist_time=$arr['user_specialist_time'];
				$this->specialist_id=$arr['user_specialist_id'];
         
        $this->spyship_count=$arr['user_spyship_count'];
        $this->spyship_id=$arr['user_spyship_id'];
        $this->havenships_buttons=$arr['user_havenships_buttons'];
				 
				$this->valid=true;
			}
		}
		
		function isValid()
		{
			return $this->valid;
		}
		
		function isTimeout()
		{
			$cfg = Config::getInstance();
			return $this->acttime + $cfg->value('user_timeout') < time();			
		}
		
		function id()
		{
			return $this->id;
		}
		
		function isSetup()
		{
			return $this->setup;
		}		
		
		function setNotSetup()
		{
			$this->setup = false;
		}
		
		function setRace($raceid)
		{
	    $sql = "
	    UPDATE
	    	users
	    SET
				user_race_id=".$raceid."
	    WHERE
	    	user_id='".$this->id."';";
	    dbquery($sql);					
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

		
	}

?>