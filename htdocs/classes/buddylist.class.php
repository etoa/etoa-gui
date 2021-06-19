<?PHP

	class Buddylist implements IteratorAggregate
	{
		private $ownerId;
		private $buddys = array();
		private $requests = array();
		private $buddyCount = 0;
		private $requestCount = 0;

		private $lastError = '';
		private $loaded = false;

        public function __construct($userId=0, $load=0)
		{
			$this->ownerId = $userId;

			if ($load)
			{
				$this->loadBuddys(1);
			}

		}

		public function __toString()
		{
			return $this->name;
		}

		/**
		 * setter but there is no need to set any value so everything is private
		 *
		 * @param string $key
		 * @param mixed $val
		 */
		public function __set($key, $val)
		{
			try
			{
				throw new EException("Properties der Klasse ".__CLASS__." sind read-only!");
				/*
				if (!property_exists($this,$key))
					throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);
				$this->$key = $val;*/
			}
			catch (EException $e)
			{
				echo $e;
			}
		}

		/**
		 * getter
		 *
		 * @param string $key
		 * @return mixed
		 */
		public function __get($key)
		{
			try
			{
				if (!property_exists($this,$key))
					throw new EException("Property $key existiert nicht in ".__CLASS__);


				return $this->$key;
			}
			catch (EException $e)
			{
				echo $e;
				return null;
			}
		}

		/**
		 *  Returns an Iterator with every element in the buddylist,
		 *
		 * @return ArrayIterator
		 *
		 * @access public
		 */
		public function getIterator($type='buddys')
		{
			if (!$this->loaded)
			{
				$this->loadBuddys(1);
			}

			if ($type == 'buddys')
			{
				if ($this->buddyCount)
				{
					return new ArrayIterator($this->buddys);
				}
			}
			else
			{
				if ($this->requestCount)
				{
					return new ArrayIterator($this->requests);
				}
			}
			return new ArrayIterator([]);
		}

		public function getBuddy($userId)
		{
			if (!$this->loaded)
			{
				$this->loadBuddy($userId);
			}

			if ( isset($this->buddys[$userId]) )
			{
				return $this->buddys[$userId];
			}
			elseif ( isset($this->requests[$userId]) )
			{
				return $this->requests[$userId];
			}

			return false;
		}

		/**
		 * Creates an buddyrequest if there is no entry in the db yet
		 *  if there is alerady an entry then check who created the request
		 *      if it was the other user then accept the already created request
		 *      else do nothing
		 *
		 * @param int $userId user id for the requested buddy
		 * @param string $comment adds an comment to the entry if the user wishes
		 *
		 * @return bool true if an entry was created, false if not
		 *
		 * @access public
		 */
		public function addBuddy($userId, $comment="")
		{
			// if there is no entry
			if ($this->isBuddy($userId) == -1)
			{
				dbquery("INSERT INTO `buddylist` (`bl_user_id`, `bl_buddy_id`, `bl_comment`) VALUES ('".$this->ownerId."', '".$userId."', '".$comment,"');");
				$this->loadBuddy($userId);
//TODO: create a request msg or better a report

				return true;
			}
			elseif ($this->isBuddy($userId) == 1)
			{
				$this->lastError = "Es besteht bereits ein Eintrag in der Buddyliste.";

				return false;
			}
			else
			{
				$res = dbquery("SELECT bl_user_id FROM buddylist WHERE bl_user_id='".$userId."' AND bl_buddy_id='".$this->ownerId."' LIMIT 1;");

				// if there is already an entry where the other user sent the request then accept the request
				if (mysql_num_rows($res))
				{
					if ($this->acceptBuddy($userId))
					{
						 $this->lastError = "Es bestand schon eine Anfrage des User für einen Eintrag. Dieser wurde nun angenommen";
					}
					return false;
				}

				// else the request already exists
				else
				{
					$this->lastError = "Die Anfrage besteht bereits.";
					return false;
				}
			}


		}

		/**
		 * accepts a buddy request
		 *
		 * @param int $userId buddy user id who sent the request
		 *
		 * @return bool succeeded or not
		 *
		 * @access public
		 */
		public function acceptBuddy($userId)
		{
			// if there is an buddy request
			if ($this->isBuddy($userId) == 0)
			{
				dbquery("UPDATE buddylist SET bl_allow='1' WHERE bl_user_id='".$userId."' AND bl_buddy_id='".$this->ownerId."' LIMIT 1;");

				// if the request was updated -> success
				if (mysql_affected_rows () > 0)
				{
					// TODO create msg or report

					// TODO move buddy to other array
					if ( $this->loaded )
					{
						unset($this->requests['user_id']);
						$this->loadBuddy($userId);
					}
					return true;
				}
				else
				{
					$this->lastError = "Es konnte keine Anfrage angenommen werden.";
					return false;
				}
			}

			// if there is no request pending create error
			$this->lastError = "Es ist kein Anfrage vorhanden.";
			return false;
		}

		/**
		 * declines an buddy request
		 *
		 * @param int $userId buddy user id who sent the request
		 *
		 * @return bool succeeded or not
		 *
		 * @access public
		 */
		public function declineBuddy($userId)
		{
			if ($this->isBuddy($userId) == 0)
			{
				dbquery("DELETE FROM buddylist WHERE bl_user_id='".$userId."' AND bl_buddy_id='".$this->ownerId."' LIMIT 1;");

				if ( $this->loaded )
				{
					unset($this->requests['user_id']);
				}
//TODO create msg or report
				return true;
			}

			// if there is no request pending create an error
			$this->lastError = "Keine Anfrage vorhanden.";
			return false;
		}

		/**
		 * deletes an buddy entry between the owner and sent user id
		 *
		 * @param int $userId
		 *
		 * @return bool succeeded or not
		 *
		 * @access public
		 */
		public function deleteBudyy($userId)
		{
			if ($this->isBuddy($userId) == 1)
			{
				dbquery("DELETE FROM buddylist WHERE (bl_user_id='".$userId."' AND bl_buddy_id='".$this->ownerId."') OR (bl_user_id='".$this->ownerId."' AND bl_buddy_id='".$userId."') LIMIT 1;");

				if ( $this->loaded )
				{
					unset($this->buddys['user_id']);
				}
				return true;
			}
			$this->lastError = "Eintrag nicht vorhanden.";
			return false;
		}

		/**
		 * checks if the users are already buddys
		 *
		 * @param int $userId
		 *
		 * @return int status -1 -> no entry in db, 0 -> request created, 1 -> request accepted and buddys
		 *
		 * @access public
		 */
		public function isBuddy($userId)
		{
			// if the list is no loaded, load just the data from the buddylist table. Userdata and all that stuff are not needed
			if (!$this->loaded)
			{
				$res = dbquery("SELECT bl_allow FROM buddylist WHERE (bl_user_id='".$this->ownerId."' AND bl_buddy_id='".$userId."') OR (bl_buddy_id='".$this->ownerId."' AND bl_user_id='".$userId."');");
				if (mysql_num_rows($res))
				{
					$arr = mysql_fetch_row($res);
					return $arr[0];
				}
			}
			// check if there is an entry in the db
			if (isset($this->buddys[$userId]))
			{
				return $this->buddys[$userId];
			}

			// no entry in the db
			return -1;

		}

				/**
				 * loads data of one buddy
				 * @param int $userId
				 */
				private function loadBuddy($userId)
				{
					//TODO check if already loaded
					$res = dbquery("SELECT * FROM buddylist WHERE (bl_user_id='".$this->ownerId."' AND bl_buddy_id='".$userId."') OR (bl_buddy_id='".$this->ownerId."' AND bl_user_id='".$userId."');");
					if (mysql_num_rows($res))
					{
						$arr = mysql_fetch_assoc($res);

						if ($this->ownerId == $arr['bl_user_id'])
						{
							$this->buddys[$arr['bl_buddy_id'] ] = array("id" => $arr['bl_id'],
																		"allow" => $arr['bl_allow'],
																		"comment" => $arr['bl_comment'],
																		"user" => new User($arr['bl_buddy_id']) );
						}
						else
						{
							if ($arr['bl_allow'] == 1)
							{
								$this->buddys[$arr['bl_user_id'] ] = array("id" => $arr['bl_id'],
																			"allow" => $arr['bl_allow'],
																			"comment" => $arr['bl_comment_buddy'],
																			"user" => new User($arr['bl_user_id']) );
							}
							else
							{
								$this->requests[$arr['bl_user_id'] ] = array("id" => $arr['bl_id'],
																			"allow" => $arr['bl_allow'],
																			"comment" => $arr['bl_comment_buddy'],
																			"user" => new User($arr['bl_user_id']) );
							}
						}
					}

				}

                /**
                 * Loads the buddylist, the data will be stored in an array
                 *
                 * @param int $load 0->loads just the necessary to check if users are buddys, 1->load complete data and create an user object for the buddy
                 *
                 * @access public
                 */
				private function loadBuddys($load=0)
                {
                    if ($load !== 0)
                    {
                        $res = dbquery("SELECT * FROM buddylist WHERE bl_user_id='".$this->ownerId."' OR bl_buddy_id='".$this->ownerId."';");
                    }
                    else
                    {
                        $res = dbquery("SELECT bl_user_id, bl_buddy_id, bl_allow FROM buddylist WHERE bl_user_id='".$this->ownerId."' OR bl_buddy_id='".$this->ownerId."';");
                    }

                    $this->buddyCount = mysql_num_rows($res);
                    if ($this->buddyCount)
                    {
                        while ($arr = mysql_fetch_assoc($res))
                        {
                            if ($load !== 0)
                            {
                                if ($this->ownerId == $arr['bl_user_id'])
                                {
                                    $this->buddys[$arr['bl_buddy_id'] ] = array("id" => $arr['bl_id'],
                                                                                  "allow" => $arr['bl_allow'],
                                                                                  "comment" => $arr['bl_comment'],
                                                                                  "user" => new User($arr['bl_buddy_id']) );
                                }
                                else
                                {
                                    if ($arr['bl_allow'] == 1)
                                    {
                                        $this->buddys[$arr['bl_user_id'] ] = array("id" => $arr['bl_id'],
                                                                                  "allow" => $arr['bl_allow'],
                                                                                  "comment" => $arr['bl_comment_buddy'],
                                                                                  "user" => new User($arr['bl_user_id']) );
                                    }
                                    else
                                    {
                                        $this->requests[$arr['bl_user_id'] ] = array("id" => $arr['bl_id'],
                                                                                  "allow" => $arr['bl_allow'],
                                                                                  "comment" => $arr['bl_comment_buddy'],
                                                                                  "user" => new User($arr['bl_user_id']) );
                                        $this->requestCount++;
                                    }

                                 }
                            }
                            else
                            {
                                if ($this->ownerId == $arr['bl_user_id'])
                                {
                                    $this->buddys[$arr['bl_buddy_id'] ] = $arr['bl_allow'];
                                }
                                else
                                {
                                    $this->buddys[$arr['bl_user_id'] ] = $arr['bl_allow'];
                                }
                            }

                        }
                    }
                    else
                    {
                        $this->lastError = "Es sind keine Buddyeinträge vorhanden.";
                    }
                    $this->loaded = true;

                }
	}

		/*
	 * @TODO needs to create a buddy row depending on request status on color
	 * needs to be a function cause xajax
	 */
	function createBuddyRow(&$buddy, $allow, $comment)
	{
		global $cp, $page;
		$cfg = Config::getInstance();
		// check several options to choose the right font-color (banned, admin, ...
		$class = getFormatingColorByUser($buddy);

		// create the row
		$out = '<tr id="'.$buddy->id.'">
					<td class="tbldata '.$class.'" >
						<div id="ttuser'.$buddy->id.'" style="display:none;">
							'.popUp('Profil anzeigen','page=userinfo&id='.$buddy->id).'<br/>
							'.popUp('Punkteverlauf','page=$page&amp;mode=$mode&amp;userdetail='.$buddy->id).'<br/>
							<a href="?page=messages&mode=new&message_user_to='.$buddy->id.'">Nachricht senden</a>
						</div>
						<a class="'.$class.'" href="#" '.cTT($buddy,"ttuser".$buddy->id).'>'.$buddy.'</a>
					</td>';

		// if the request is still pending
		if ($allow == 0)
			$out .= '<td class="tbldata '.$class.'" colspan="3"><i>Noch keine Erlaubnis</i></td>';
		else
		{
			// create the planet object
			//@TODO
			$tp = $cp;
			$out .= '<td class="tbldata '.$class.'">'.nf($buddy->points).'</td>
					<td style="padding:2px 2px 3px 6px;background:#000;color:#fff;height:47px;">
						<img src="'.$tp->imagePath().'" style="float:left;" >
						<br/>&nbsp;&nbsp; '.$tp.'
					</td>';
			$time = $buddy->loadLastAction();
			if ($time + (int) $cfg->value('user_timeout') > time() )
				$out .= '<td style="color:#0f0;">online</td>';
			elseif ($time)
				$out .= '<td class="tbldata '.$class.'">'.date("d.m.Y H:i",$time).'</td>';
			else
				$out .= '<td class="tbldata '.$class.'"><i>Noch nicht eingeloggt!</i></td>';
		}

		// create the comment
		$out .= '<td class="tbldata '.$class.'"><p class="editable">'.text2html($comment).'</p></td>';

		// and the last part, the icons
		$out .= '<td>
					<p class="icons">
						<a class="msg"href="?page=messages&amp;mode=new&amp;message_user_to='.$buddy->id.'" title="Nachricht senden">'.icon('mail').'</a>'."
						<a href=\"?page=userinfo&amp;id=".$buddy->id."\" title=\"Profil anzeigen\">".icon("profil")."</a>
						<a href=\"?page=$page&comment=".$buddy->id."\" title=\"Kommentar bearbeiten\">".icon("edit")."</a>
						<a href=\"?page=$page&remove=".$buddy->id."\" onclick=\"return confirm('Willst du ".$buddy." wirklich von deiner Liste entfernen?');\" title=\"Kontakt entfernen\">".icon("delete")."</a>
					</p>
				</td>
			</tr>";

		return $out;
	}

?>
