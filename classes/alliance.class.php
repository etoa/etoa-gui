<?php

	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	File: alliance.class.php
	// 	Created: 01.12.2004
	// 	Last edited: 22.09.2009
	// 	Last edited by: glaubinix <glaubinix@etoa.ch>
	//	

	/**
	* The alliance object
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2009 by EtoA Gaming, www.etoa.ch
	*/
	class Alliance 
	{
		protected $id;
		protected $name;
		protected $tag;
		protected $points;
		protected $memberCount;
		protected $visits;
		protected $visitsExt;
		protected $text;
		protected $image;
		protected $url;
		protected $acceptApplications;
		protected $acceptPact;
		protected $foundationDate;
		protected $publicMemberList;
	
		protected $wings = null;
		protected $wingRequests = null;
		protected $members = null;
		protected $ranks = null;
	
		protected $founderId;
		protected $founder = null;
	
		protected $motherId;
		protected $mother = null;
		protected $motherRequestId;
		protected $motherRequest = null;
		
		protected $valid;
		protected $changedFields;
		
		protected $resMetal;
		protected $resCrystal;
		protected $resPlastic;
		protected $resFuel;
		protected $resFood;
		
		protected $allianceObjectsForMembers;
		protected $buildlist = null;
		protected $techlist = null;
		
		/**
		* Constructor
		*/
	  function Alliance($id) 
	  {
	  	$this->id = $id;
	  	$this->valid = false;
	  	$res = dbquery("
	  	SELECT
	  		alliances.*,
	  		COUNT(users.user_id) as member_count
	  	FROM
	  		alliances
	  	LEFT JOIN
	  		users
	  		ON user_alliance_id=alliance_id
	  	WHERE
	  		alliance_id=".$this->id."
	  	GROUP BY
	  		alliance_id
	  	LIMIT 1;
	  	");
	  	if (mysql_num_rows($res)>0)
	  	{
	  		$arr = mysql_fetch_assoc($res);
	  		$this->name = $arr['alliance_name'];
	  		$this->tag = $arr['alliance_tag'];
	  		$this->motherId = $arr['alliance_mother'];
	  		$this->motherRequestId = $arr['alliance_mother_request'];
	  		$this->points = $arr['alliance_points'];
	  		$this->memberCount = $arr['member_count'];
				$this->founderId = $arr['alliance_founder_id'];  		
				$this->visits = $arr['alliance_visits'];
				$this->visitsExt = $arr['alliance_visits_ext'];
				$this->text = $arr['alliance_text'];
				$this->image = $arr['alliance_img'];
				$this->url = $arr['alliance_url'];
				$this->acceptApplications = (bool)$arr['alliance_accept_applications'];
				$this->acceptPact = (bool)$arr['alliance_accept_bnd'];
				$this->publicMemberList = (bool)$arr['alliance_public_memberlist'];
				$this->foundationDate = $arr['alliance_foundation_date'];
				
				$this->resMetal = $arr['alliance_res_metal'];
				$this->resCrystal = $arr['alliance_res_crystal'];
				$this->resPlastic = $arr['alliance_res_plastic'];
				$this->resFuel = $arr['alliance_res_fuel'];
				$this->resFood = $arr['alliance_res_food'];
				
				$this->allianceObjectsForMembers = $arr['alliance_objects_for_members'];
				
				$this->changedFields = array();
	  		$this->valid = true;
	  	}
	  }
	    
		//
		// Magic functions
		//    
	    
	  /**
	  * Returns a propperly formated alliance name
	  */
		public function __toString()
		{
			return "[".$this->tag."] ".$this->name;
		}
		
		/**
		* Destruktor
		*/
		function __destruct()
		{
			$cnt = count($this->changedFields);
			if ($cnt > 0)
			{
				$sql = "UPDATE 
					alliances
				SET ";
				foreach ($this->changedFields as $k=>$v)
				{
					if ($k == "visits")	
				    $sql.= " alliance_visits=".$this->$k.",";
					elseif ($k == "visitsExt")	
				    $sql.= " alliance_visits_ext=".$this->$k.",";
					elseif ($k == "founderId")
				    $sql.= " alliance_founder_id=".$this->$k.",";
				   /*
					elseif ($k == "profileImage")
					{
						if ($this->profileImage == "")
					    $sql.= " user_profile_img='',user_profile_img_check=0,";
	          else
				    	$sql.= " user_profile_img='".$this->profileImage."',user_profile_img_check=1,";
					}*/
					else
						echo " $k has no valid UPDATE query!<br/>";
				}
				$sql.=" alliance_id=alliance_id WHERE
				    	alliance_id=".$this->id.";";
				dbquery($sql);
			}
			unset($this->changedFields);
			
		}	
		
		/**
		* Chances alliance properties
		*/
		public function __set($key, $val)
		{
			try
			{
				if (!property_exists($this,$key))
					throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);
				
				if ($key=="visits")
				{
					$this->$key = intval($val);
					$this->changedFields[$key] = true;
					return true;				
				}
				if ($key=="visitsExt")
				{
					$this->$key = intval($val);
					$this->changedFields[$key] = true;
					return true;				
				}		
				if ($key=="founderId")
				{
					if ($this->members == null)				
						$this->getMembers();
					if (isset($this->members[$val]))
					{
						$this->$key = intval($val);
						$this->founder = & $this->members[$val];
						$this->addHistory("Der Spieler [b]".$this->founder."[/b] wird zum Gründer befördert.");
						$this->founder->sendMessage(MSG_ALLYMAIL_CAT,"Gründer","Du hast nun die Gründerrechte deiner Allianz!");
						$this->founder->addToUserLog("alliance","{nick} ist nun Gründer der Allianz ".$this);
						$this->changedFields[$key] = true;
						return true;				
					}
					return false;
				}				
			
				throw new EException("Property $key der Klasse  ".__CLASS__." ist nicht änderbar!");
					return false;
			}
			catch (EException $e)
			{
				echo $e;
			}
		}
	
		/**
		* Gets alliance properties
		*/	
		public function __get($key)
		{
			try
			{
				// Return special non-defined properties
				if ($key == "avgPoints")
					return floor($this->points / $this->memberCount);
				if ($key == "imageUrl")
					return ALLIANCE_IMG_DIR."/".$this->image;
	
				// Check if property exists
				if (!property_exists($this,$key))
					throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);
	
				// Do actions for some special properties
				if ($key == "members" && $this->members == null)
					$this->getMembers();
				if ($key == "wings" && $this->wings == null)
					$this->getWings();
				if ($key == "wingRequests" && $this->wingRequests == null)
					$this->getWingRequests();
				if ($key == "mother" && $this->mother == null)
					$this->mother = new Alliance($this->motherId);
				if ($key == "motherRequest" && $this->motherRequest == null)
					$this->motherRequest = new Alliance($this->motherRequestId);
				if ($key == "founder" && $this->founder == null) 
				{
					if (isset($this->members[$this->founderId]))
						$this->founder = & $this->members[$this->founderId];
					else
						$this->founder = new User($this->founderId);
				}
				if ($key == "techlist" && $this->techlist == null)
					$this->techlist = new AllianceTechlist($this->id,TRUE);
				if ($key == "buildlist" && $this->buildlist == null)
					$this->buildlist = new AllianceBuildlist($this->id,TRUE);
	
	
				// Protected properties
				if ($key == "changedFields")
					throw new EException("Property $key der Klasse ".__CLASS__." ist geschützt!");
					
	
				return $this->$key;
			}
			catch (EException $e)
			{
				echo $e;
				return null;
			}
		}	
		
		//
		// Member management
		//
		
		/**
		* Returns alliance members as an array of user objecs
		* Use $object->members from outside of the class
		*/
		private function & getMembers()
		{
			if ($this->members == null)
			{
				$this->members = array();
		  	$res = dbquery("
		  	SELECT
		  		user_id
		  	FROM
		  		users
		  	WHERE
		  		user_alliance_id=".$this->id."
		  	");			
		  	if (mysql_num_rows($res)>0)
		  	{
		  		while ($arr = mysql_fetch_row($res))
		  		{
			  		$this->members[$arr[0]] = new User($arr[0]);
		  		}
		  	}
			}
			return $this->members;
		}
		
		/**
		* Adds a new user to the alliance
		*/
		public function addMember($userId)
		{
			$this->getMembers();
			if (!isset($this->members[$userId]))
			{
				$tmpUser = new User($userId);
				if ($tmpUser->isValid)
				{
					if ($tmpUser->alliance = $this)
					{
						$this->members[$userId] = $tmpUser;
						$this->members[$userId]->sendMessage(MSG_ALLYMAIL_CAT,"Allianzaufnahme","Du wurdest in die Allianz [b]".$this."[/b] aufgenommen!");
						$this->addHistory("[b]".$tmpUser."[/b] wurde als neues Mitglied aufgenommen");
						$this->calcMemberCosts();
						return true;
					}
				}
				unset($tmpUser);
			}
			return false;
		}	
		
		/**
		* Removes an user from the alliance
		*/
		public function kickMember($userId,$kick=1)
		{
			$this->getMembers();
			if ($this->members[$userId]->isValid)
			{
				$this->members[$userId]->alliance = null;
				if ($this->members[$userId]->allianceId == 0)
				{
					if ($kick==1)
						$this->members[$userId]->sendMessage(MSG_ALLYMAIL_CAT,"Allianzausschluss","Du wurdest aus der Allianz [b]".$this."[/b] ausgeschlossen!");
					else
						$this->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Allianzaustritt","Der Spieler ".$this->members[$userId]." trat aus der Allianz aus!");
					
					$this->addHistory("[b]".$this->members[$userId]."[/b] ist nun kein Mitglied mehr von uns.");
					unset($this->members[$userId]);
					return true;
				}
			}
			unset($tmpUser);
			return false;
		}		
		
		//
		// Wing management
		//
		
		/**
		* Returns all wings of this alliance as an array of alliance objects
		* Use $object->wings from outside of the class
		*/
		private function & getWings()
		{
			if ($this->wings == null)
			{
				$this->wings = array();
		  	$res = dbquery("
		  	SELECT
		  		alliance_id
		  	FROM
		  		alliances
		  	WHERE
		  		alliance_mother=".$this->id."
		  		AND alliance_id!=".$this->id."
		  	");			
		  	if (mysql_num_rows($res)>0)
		  	{
		  		while ($arr = mysql_fetch_row($res))
		  		{
			  		$this->wings[$arr[0]] = new Alliance($arr[0]);
		  		}
		  	}
			}
			return $this->wings;
		}
		
		private function & getWingRequests()
		{
			if ($this->wingRequests == null)
			{
				$this->wingRequests = array();
		  	$res = dbquery("
		  	SELECT
		  		alliance_id
		  	FROM
		  		alliances
		  	WHERE
		  		alliance_mother_request=".$this->id."
		  		AND alliance_id!=".$this->id."
		  	");			
		  	if (mysql_num_rows($res)>0)
		  	{
		  		while ($arr = mysql_fetch_row($res))
		  		{
			  		$this->wingRequests[$arr[0]] = new Alliance($arr[0]);
		  		}
		  	}
			}
			return $this->wingRequests;
		}	
		
		/**
		* Add a request for wing membership
		*/
		public function addWingRequest($allianceId)
		{
			$this->getWingRequests();
			if ($allianceId != $this->id && $allianceId!= $this->motherId &&  $allianceId!= $this->motherRequestId)
			{
				$res = dbquery("
				UPDATE
					alliances
				SET
					alliance_mother_request=".$this->id."
				WHERE
					alliance_mother_request=0
					AND alliance_mother=0
					AND alliance_id=".$allianceId."
				");
				if (mysql_affected_rows()>0)
				{
					$this->wingRequests[$allianceId] = new Alliance($allianceId);
					$this->wingRequests[$allianceId]->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing-Anfrage","Die Allianz [b]".$this."[/b] möchte eure Allianz als Wing hinzufügen. [url ?page=alliance&action=wings]Anfrage beantworten[/url]");
					return true;
				}			
			}
			return false;
		}	
		
		/**
		* Cancel the request for wing membership
		*/
		public function cancelWingRequest($wingId)
		{
			$this->getWingRequests();
			dbquery("
			UPDATE
				alliances
			SET
				alliance_mother_request=0
			WHERE
				alliance_mother_request=".$this->id."
				AND alliance_id=".$wingId."
			");
			if (mysql_affected_rows()>0)
			{
				if ($this->wingRequests != null)
				{
					$this->wingRequests[$wingId]->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing-Anfrage zurückgezogen","Die Allianz [b]".$this."[/b] hat die Wing-Anfrage zurückgezogen.");
					unset($this->wingRequests[$wingId]);
				}
				else
				{
					$tmpWing = new Alliance($wingId);
					$tmpWing->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing-Anfrage zurückgezogen","Die Allianz [b]".$this."[/b] hat die Wing-Anfrage zurückgezogen.");
					unset($tmpWing);
				}
				return true;
			}
			return false;
		}	
		
		/**
		* Revoke request for wing membership
		*/
		public function revokeWingRequest()
		{
			if ($this->motherRequestId > 0)
			{
				dbquery("
				UPDATE
					alliances
				SET
					alliance_mother_request=0
				WHERE
					alliance_mother_request=".$this->motherRequestId."
					AND alliance_id=".$this->id."
				");
				if (mysql_affected_rows()>0)
				{
					$this->__get('motherRequest')->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing-Anfrage zurückgewiesen","Die Allianz [b]".$this."[/b] hat die Wing-Anfrage zurückgewiesen.");
					$this->motherRequestId = 0;
					$this->motherRequest = null;
					return true;
				}
			}
			return false;
		}			

		/**
		* Grant request for wing membership
		*/
		public function grantWingRequest()
		{
			if ($this->motherRequestId > 0)
			{			
				$res = dbquery("
				UPDATE
					alliances
				SET
					alliance_mother=".$this->motherRequestId.",
					alliance_mother_request=0
				WHERE
					alliance_id=".$this->id."
				");		
				if (mysql_affected_rows()>0)
				{
					$this->mother = $this->motherRequest;
					$this->motherId = $this->motherRequestId;
					$this->motherRequestId = 0;
					$this->motherRequest = null;					
					$this->__get('mother')->addHistory("[b]".$this."[/b] wurde als neuer Wing hinzugefügt.");
					$this->addHistory("Wir sind nun ein Wing von [b]".$this->__get('mother')."[/b]");
					$this->__get('mother')->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Neuer Wing","Die Allianz [b]".$this."[/b] ist nun ein Wing von [b]".$this->__get('mother')."[/b]");
					return true;
				}					
			}
			return false;
		}		

		/**
		* Add a wing
		*/
		public function addWing($allianceId)
		{
			$this->getWings();
			if ($allianceId != $this->id && !isset($this->wings[$allianceId]))
			{
				$res = dbquery("
				UPDATE
					alliances
				SET
					alliance_mother=".$this->id.",
					alliance_mother_request=0
				WHERE
					alliance_id=".$allianceId."
				");
				if (mysql_affected_rows()>0)
				{
					$this->wings[$allianceId] = new Alliance($allianceId);
					$this->addHistory($this->wings[$allianceId]." wurde als neuer Wing hinzugefügt.");
					$this->wings[$allianceId]->addHistory("Wir sing nun ein Wing von ".$this);
					$this->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Neuer Wing","Die Allianz [b]".$this->wings[$allianceId]."[/b] ist nun ein Wing von [b]".$this."[/b]");
					$this->wings[$allianceId]->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Neuer Wing","Die Allianz [b]".$this->wings[$allianceId]."[/b] ist nun ein Wing von [b]".$this."[/b]");
					return true;
				}			
			}
			return false;
		}
				
		/**
		* Removes a wing
		*/
		public function removeWing($wingId)
		{
			dbquery("
			UPDATE
				alliances
			SET
				alliance_mother=0
			WHERE
				alliance_mother=".$this->id."
				AND alliance_id=".$wingId."
			");
			if (mysql_affected_rows()>0)
			{
				if ($this->wings != null)
				{
					$this->addHistory($this->wings[$wingId]." ist nun kein Wing mehr von uns");
					$this->wings[$wingId]->addHistory("Wir sind nun kein Wing mehr von [b]".$this."[/b]");
					$this->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing aufgelöst","Die Allianz [b]".$this->wings[$allianceId]."[/b] ist kein Wing mehr von [b]".$this."[/b]");
					$this->wings[$wingId]->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing aufgelöst","Die Allianz [b]".$this->wings[$allianceId]."[/b] ist kein Wing mehr von [b]".$this."[/b]");
					unset($this->wings[$wingId]);
				}
				else
				{
					$tmpWing = new Alliance($wingId);
					$this->addHistory($tmpWing." ist nun kein Wing mehr.");
					$tmpWing->addHistory("Wir sind nun kein Wing mehr von [b]".$this."[/b]");
					unset($tmpWing);
				}
				return true;
			}
			return false;
		}		
		
		// 
		// Misc functions
		//
		
		/**
		* Add text to alliance history
		*/
		function addHistory($text)
		{
			dbquery("
			INSERT INTO
				alliance_history
				(
					history_alliance_id,
					history_text,
					history_timestamp
				)
				VALUES
				(
					'".$this->id."',
					'".addslashes($text)."',
					'".time()."'
				);");
		}
		
		/**
		* Delete alliance
		*/
		function delete(&$user = null)
		{
			$res = dbquery("SELECT cat_id FROM allianceboard_cat WHERE cat_alliance_id='".$this->id."';");
			if (mysql_num_rows($res))
			{
				while ($arr = mysql_fetch_row($res))
				{				 
					dbquery("DELETE FROM allianceboard_catranks WHERE cr_rank_id='".$arr[0]."';");
					$res = dbquery("SELECT topic_id FROM allianceboard_topics WHERE topic_cat_id='".$arr[0]."';");
					if (mysql_num_rows($res))
					{
						while ($arr = mysql_fetch_row($res))
						{				 
							dbquery("DELETE FROM allianceboard_posts WHERE post_topic_id='".$arr[0]."';");
						}
					}
					dbquery("DELETE FROM allianceboard_topics WHERE topic_cat_id='".$arr[0]."';");
				}
			}
			dbquery("DELETE FROM allianceboard_cat WHERE cat_alliance_id='".$this->id."';");
			dbquery("DELETE FROM alliance_applications WHERE alliance_id='".$this->id."';");
			$bndres=dbquery("SELECT 
				* 
			FROM 
				alliance_bnd
			WHERE
				alliance_bnd_alliance_id1='".$this->id."'
				OR alliance_bnd_alliance_id2='".$this->id."';");
			if (mysql_num_rows($bndres)>0)
			{
				while ($bndarr=mysql_fetch_assoc($bndres))
				{
					$bres=dbquery("SELECT * FROM allianceboard_topics WHERE topic_bnd_id=".$bndarr['alliance_bnd_id'].";");
					while ($barr=mysql_fetch_assoc($bres))
					{
						dbquery("DELETE FROM allianceboard_posts WHERE post_topic_id=".$barr['topic_id'].";");
					}
					dbquery("DELETE FROM allianceboard_topics WHERE topic_bnd_id=".$bndar['alliance_bnd_id'].";");				
				}
			}
			dbquery("
				DELETE FROM
					alliance_bnd
				WHERE
					alliance_bnd_alliance_id1='".$this->id."'
					OR alliance_bnd_alliance_id2='".$this->id."';
			");
			dbquery("DELETE FROM alliance_buildlist WHERE alliance_buildlist_alliance_id='".$this->id."';");
			dbquery("DELETE FROM alliance_history WHERE history_alliance_id='".$this->id."';");
			dbquery("DELETE FROM alliance_news WHERE alliance_news_alliance_id='".$this->id."';");
			dbquery("DELETE FROM alliance_points WHERE point_alliance_id='".$this->id."';");
			dbquery("DELETE FROM alliance_polls WHERE poll_alliance_id='".$this->id."';");
			dbquery("DELETE FROM alliance_poll_votes WHERE vote_alliance_id='".$this->id."';");
			$res = dbquery("SELECT rank_id FROM alliance_ranks WHERE rank_alliance_id='".$this->id."';");
			if (mysql_num_rows($res))
			{
				while ($arr = mysql_fetch_row($res))
				{
					dbquery("DELETE FROM alliance_rankrights WHERE rr_rank_id=".$arr[0].";");
				}
			}
			dbquery("DELETE FROM alliance_ranks WHERE rank_alliance_id='".$this->id."';");
			dbquery("DELETE FROM alliance_spends WHERE alliance_spend_alliance_id='".$this->id."';");
			dbquery("DELETE FROM alliance_techlist WHERE alliance_techlist_alliance_id='".$this->id."';");
			dbquery("UPDATE alliances 
			SET 
				alliance_mother=0
			WHERE
				alliance_mother='".$this->id."';");
			dbquery("UPDATE alliances 
			SET 
				alliance_mother_request=0
			WHERE
				alliance_mother_request='".$this->id."';");
	
			// Set user alliance link to null
			if ($this->members==null)
				$this->getMembers();
			for($i=0;$i<count($this->members);$i++)
			{
				$this->members[$i]->alliance = null;
			}
			$user->alliance = null;
	
			// Daten löschen
			dbquery("
			DELETE FROM
				alliances
			WHERE
				alliance_id='".$this->id."';");
	
			//Log schreiben
			if($user!=null)
			{
				$user->addToUserLog("alliance","{nick} löst die Allianz [b]".$this."[/b] auf.");
				add_log("5","Die Allianz [b]".$this."[/b] wurde von ".$user." aufgelöst!");
			}
			else
				add_log("5","Die Allianz [b]".$this."[/b] wurde gelöscht!");
			return true;
		}
			
		/**
		* Changes allianceresources
		*/
		function changeRes($m,$c,$p,$fu,$fo,$pw=0)
		{
		    $sql = "
		    UPDATE
		    	alliances
		    SET
                alliance_res_metal=alliance_res_metal+".$m.",
                alliance_res_crystal=alliance_res_crystal+".$c.",
                alliance_res_plastic=alliance_res_plastic+".$p.",
                alliance_res_fuel=alliance_res_fuel+".$fu.",
                alliance_res_food=alliance_res_food+".$fo."
		    WHERE
		    	alliance_id='".$this->id."';";
		    dbquery($sql);
		    $this->resMetal+=$m;
		    $this->resCrystal+=$c;
		    $this->resPlastic+=$p;
		    $this->resFuel+=$fu;
		    $this->resFood+=$fo;
		}
		
		//
		// Statics
		//
		
		/**
		* Create a new alliance and return the object
		* or false in case of error
		*/
		static function create($data, &$returnMsg)
		{
			if (isset($data['name']) 
			&& isset($data['tag']) 
			&& $data['name']!="" 
			&& $data['tag']!="")
			{
				$tagRegExPattern = '/^[^\'\"\?\<\>\$\!\=\;\&]{1,6}$/i';
				if (preg_match($tagRegExPattern,$data['tag'])>0)
				//if (eregi('^[^\'\"\?\<\>\$\!\=\;\&]{1,6}$',$data['tag']))
				{
					if (eregi('^[^\'\"\?\<\>\$\!\=\;\&]{4,25}$',$data['name']))
					{
						if (isset($data['founder']))
						{
							$res = dbquery("
							SELECT 
								COUNT(alliance_id)
							FROM 
								alliances 
							WHERE 
								alliance_tag='".$data['tag']."'
								OR alliance_name='".$data['tag']."'
							LIMIT 1;");
							if (mysql_result($res,0)==0)
							{
								dbquery("
								INSERT INTO 
									alliances 
								(
									alliance_tag,
									alliance_name,
									alliance_founder_id,
									alliance_foundation_date
								) 
								VALUES 
								(
									'".$data['tag']."',
									'".$data['name']."',
									'".$data['founder']->id."',
									'".time()."');");							
								$returnMsg = new Alliance(mysql_insert_id());
								$data['founder']->alliance = $returnMsg;
								$data['founder']->addToUserLog("alliance","{nick} hat die Allianz [b]".$returnMsg."[/b] gegründet.");
								$returnMsg->addHistory("Die Allianz [b]".$returnMsg."[/b] wurde von [b]".$data['founder']."[/b] gegründet!");
								return true;
							}
							else
								$returnMsg = "Eine Allianz mit diesem Tag oder Namen existiert bereits!";
						}
						else
							$returnMsg = "Allianzgründer-ID fehlt!";
					}
					else
						$returnMsg = "Ungültiger Name! Die Länge muss zwischen 4 und 25 Zeichen liegen und darf folgende Zeichen nicht enthalten: ^'\"?<>$!=;&[]";
				}
				else
					$returnMsg = "Ungültiger Tag! Die Länge muss zwischen 3 und 6 Zeichen liegen und darf folgende Zeichen nicht enthalten: ^'\"?<>$!=;&[]";
			}
			else
				$returnMsg = "Name/Tag fehlt!";
			return false;
		}
		
		/**
		* Check rights for an action
		*/
	  static function checkActionRights($action,$msg=TRUE)
	  {
			global $myRight,$isFounder,$page;
			if ($isFounder || $myRight[$action])
			{
				return true;
			}
			
			if ($msg)
			{
				error_msg("Keine Berechtigung!");
				echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
			}
			return false;    	
	  }
	  
	  /**
	  * Check rights for an action
	  * use this function if you're not on the alliance page
	  */
	  
	  function checkActionRightsNA($action)
	  {
		  global $cu;
		  
		  if ($this->founderId == $cu->id) return true;
		  
		  $res = dbquery("
						SELECT
							alliance_rankrights.rr_id
            			FROM
							alliance_ranks
						INNER JOIN
							alliance_rankrights
						ON
							alliance_ranks.rank_id=alliance_rankrights.rr_rank_id
						INNER JOIN
							alliance_rights
						ON
							alliance_rankrights.rr_right_id=alliance_rights.right_id
							AND alliance_ranks.rank_alliance_id='".$this->id."'
							AND alliance_rights.right_key='".$action."'
							AND alliance_rankrights.rr_rank_id=".$cu->allianceRankId.";");
			if (mysql_num_rows($res)) return true;
			
			return false;
	  }
	  
	  /**
	  * Returns a sorted list of all alliances
	  */
	  static public function & getList()
	  {
	  	$rtn = array();
	  	$res = dbquery("
	  	SELECT
	  		alliance_id,
	  		alliance_tag,
	  		alliance_name
	  	FROM
	  		alliances
			ORDER BY
				alliance_name
	  	");  	
	  	if (mysql_num_rows($res)>0)
	  	{
	  		while ($arr = mysql_fetch_row($res))
	  			$rtn[$arr[0]] = "[".$arr[1]."] ".$arr[2];
	  	}
	  	return $rtn;
	  }
	  
	  /**
	  * Calc costs at adding a new Member
	  */
	  public function calcMemberCosts($save=true,$addMembers=1)
	  {
		// Zählt aktuelle Memberanzahl und und läd den Wert, für welche Anzahl User die Allianzobjekte gebaut wurden
		$this->getMembers();
		$newMemberCnt = count($this->members) + $addMembers;
		if ($save)
		$newMemberCnt--;
		
		// Allianzrohstoffe anpassen, wenn die Allianzobjekte nicht für diese Anzahl ausgebaut sind
		$cfg = Config::getInstance();
		
		//Aktuelle, neue und zu zahlende Kosten
		$costs = array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0);
		$new_costs = array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0);
		$to_pay = array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0);
			
		// Berechnet Kostendifferenz
			
		if ($this->buildlist == null)
				$this->buildlist = new AllianceBuildlist($this->id);
		$buildingIterator = $this->buildlist->getIterator();
		
		while ($buildingIterator->valid())
		{
			if ($this->buildlist->getMemberFor($buildingIterator->key())<$newMemberCnt)
			{
				// Wenn ein Gebäude in Bau ist, wird die Stufe zur berechnung bereits erhöht
				$level = $this->buildlist->getLevel($buildingIterator->key());
				if ($this->buildlist->isUnderConstruction($buildingIterator->key()))
					$level++;
				
				// Berechnungen nur durchführen, wenn die Stufe >0 ist oder sich das Objekt in Bau befindet
				// Dies ist eine Sicherheit für den Fall, dass die Stufe manuel zurückgesetzt wird. Es würden falsche Kosten entstehen
				if($level>0 || $this->buildlist->isUnderConstruction($buildingIterator->key()))
				{									
					// Kosten von jedem Level des Gebäudes wird berechnet
					for ($x=1;$x<=$level;$x++)
					{
						$buildCosts = $buildingIterator->current()->getCosts($x,$this->buildlist->getMemberFor($buildingIterator->key()));
						
						foreach ($buildCosts as $rid=>$cost)
							$costs[$rid] += $cost;
						
						$buildCosts = $buildingIterator->current()->getCosts($x,$newMemberCnt);
						
						foreach ($buildCosts as $rid=>$cost)
							$new_costs[$rid] += $cost;
					}
				}
			}
			$buildingIterator->next();
		}
		if ($save)
		{
			dbquery("UPDATE
						alliance_buildlist
					SET
						alliance_buildlist_member_for='".$newMemberCnt."'
					WHERE
						alliance_buildlist_alliance_id='".$this->id."';");
		}
		
			
		if ($this->techlist == null)
				$this->techlist = new AllianceTechlist($this->id);
		$techIterator = $this->techlist->getIterator();
		
		while($techIterator->valid())
		{
			if ($this->buildlist->getMemberFor($techIterator->key())<$newMemberCnt)
			{
				// Wenn eine Techin Bau ist, wird die Stufe zur berechnung bereits erhöht
				$level = $this->techlist->getLevel($techIterator->key());
				if ($this->techlist->isUnderConstruction($techIterator->key()))
					$level++;
				
				// Berechnungen nur durchführen, wenn die Stufe >0 ist oder sich das Objekt in Bau befindet
				// Dies ist eine Sicherheit für den Fall, dass die Stufe manuel zurückgesetzt wird. Es würden falsche Kosten entstehen
				if($level>0 || $this->techlist->isUnderConstruction($techIterator->key()))
				{									
					// Kosten von jedem Level des Gebäudes wird berechnet
					for ($x=1;$x<=$level;$x++)
					{
						$buildCosts = $techIterator->current()->getCosts($x,$this->techlist->getMemberFor($techIterator->key()));
						
						foreach ($buildCosts as $rid=>$cost)
							$costs[$rid] += $cost;
						
						$buildCosts = $techIterator->current()->getCosts($x,$newMemberCnt);
						
						foreach ($buildCosts as $rid=>$cost)
							$new_costs[$rid] += $cost;
					}
				}
			}
			$techIterator->next();
		}
		if ($save)
		{
			dbquery("UPDATE
						alliance_techlist
					SET
						alliance_techlist_member_for='".$newMemberCnt."'
					WHERE
						alliance_techlist_alliance_id='".$this->id."';");
		}
		
		// Berechnet die zu zahlenden Rohstoffe
		foreach ($costs as $rid=>$cost)
			$to_pay[$rid] = $new_costs[$rid] - $cost;
		
		if ($save)
		{
			// Zieht Rohstoffe vom Allianzkonto ab und speichert Anzahl Members, für welche nun bezahlt ist
			if(array_sum($to_pay)>0)
			{
				dbquery("
					  UPDATE
						alliances
					  SET
						alliance_res_metal=alliance_res_metal-'".$to_pay[1]."',
						alliance_res_crystal=alliance_res_crystal-'".$to_pay[2]."',
						alliance_res_plastic=alliance_res_plastic-'".$to_pay[3]."',
						alliance_res_fuel=alliance_res_fuel-'".$to_pay[4]."',
						alliance_res_food=alliance_res_food-'".$to_pay[5]."',
						alliance_objects_for_members='".$newMemberCnt."'
					  WHERE
						alliance_id='".$this->id."'
					LIMIT 1;");
							
					// Log schreiben
					add_alliance_history($this->id,"Dem Allianzkonto wurden folgende Rohstoffe abgezogen:\n[b]".RES_METAL."[/b]: ".nf($to_pay[1])."\n[b]".RES_CRYSTAL."[/b]: ".nf($to_pay[2])."\n[b]".RES_PLASTIC."[/b]: ".nf($to_pay[3])."\n[b]".RES_FUEL."[/b]: ".nf($to_pay[4])."\n[b]".RES_FOOD."[/b]: ".nf($to_pay[5])."\n\nDie Allianzobjekte sind nun für ".$newMemberCnt." Mitglieder verfügbar!");
			}
		}
		else
		{
			return text2html("Bei der Aufnahme von ".$addMembers." Member werden dem Allianzkonto folgende Rohstoffe abgezogen:\n[b]".RES_METAL."[/b]: ".nf($to_pay[1])."\n[b]".RES_CRYSTAL."[/b]: ".nf($to_pay[2])."\n[b]".RES_PLASTIC."[/b]: ".nf($to_pay[3])."\n[b]".RES_FUEL."[/b]: ".nf($to_pay[4])."\n[b]".RES_FOOD."[/b]: ".nf($to_pay[5]));
		}
	  }
	  
	  /**
	  * Warcheck
	  */
	  
	  public function checkWar($allianceId)
	  {
		if ($this->id!=$allianceId && $allianceId>0)
		{
		  $wres=dbquery("
			  SELECT 
				  COUNT(alliance_bnd_id) 
			  FROM 
				  alliance_bnd
			  WHERE 
				  (
					  (
						  alliance_bnd_alliance_id1=".$this->id." 
						  AND alliance_bnd_alliance_id2=".$allianceId."
					  ) 
					  OR 
					  (
						  alliance_bnd_alliance_id2=".$this->id." 
						  AND alliance_bnd_alliance_id1=".$allianceId."
					  )
				  ) 
				  AND alliance_bnd_level=3");
		  $warr=mysql_fetch_row($wres);
		  if ($warr[0]>0) return true;
		}
		return false;
	  }
	  
	  /**
	  * Bndcheck
	  */
	  
	  public function checkBnd($allianceId)
	  {
		if ($this->id!=$allianceId && $allianceId>0)
		{
		  $bres=dbquery("
			  SELECT 
				  COUNT(alliance_bnd_id) 
			  FROM 
				  alliance_bnd
			  WHERE 
				  (
					  (
						  alliance_bnd_alliance_id1=".$this->id." 
						  AND alliance_bnd_alliance_id2=".$allianceId."
					  ) 
					  OR 
					  (
						  alliance_bnd_alliance_id2=".$this->id." 
						  AND alliance_bnd_alliance_id1=".$allianceId."
					  )
				  ) 
				  AND alliance_bnd_level=2");
		  $barr=mysql_fetch_row($bres);
		  if ($barr[0]>0) return true;
		}
		return false;
	  }
	}
?>