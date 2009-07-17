<?PHP
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
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	// $Author$
	// $Date$
	// $Rev$
	//
	
	$umod = false;

		//
		// Urlaubsmodus einschalten
		//
	
		if (isset($_POST['hmod_on']) && checker_verify())
		{
			if ($cu->lastInvasion < $cfg->user_umod_min_length*3600)
			{
				$cres = dbquery("SELECT id FROM fleet WHERE user_id='".$cu->id."';");
				$carr = mysql_fetch_row($cres);
				if ($carr[0]==0)
				{
					$pres = dbquery("SELECT 
										f.id 
									FROM 
										fleet as f
									INNER JOIN
										planets as p
									ON f.entity_to=p.id
									AND p.planet_user_id='".$cu->id."';");
					$parr = mysql_fetch_row($pres);
					if ($parr[0]==0)
					{
						$sres = dbquery("SELECT 
											queue_id,
											queue_starttime 
										FROM 
											ship_queue 
										WHERE 
											queue_user_id='".$cu->id."';");
						while ($sarr=mysql_fetch_row($sres))
						{
							if ($sarr[1]>time())
							{
								dbquery("UPDATE 
											ship_queue 
										SET 
											queue_build_type=1
										WHERE 
											queue_user_id='".$cu->id."';");
							}
							else
							{
								dbquery("UPDATE 
											ship_queue 
										SET 
											queue_build_type=1, 
											queue_starttime=".time()." 
										WHERE 
											queue_user_id='".$cu->id."';");
							}
						}
						$sres = dbquery("SELECT 
											queue_id,
											queue_starttime 
										FROM 
											def_queue 
										WHERE 
											queue_user_id='".$cu->id."';");
						while ($sarr=mysql_fetch_row($sres))
						{
							if ($sarr[1]>time())
							{
								dbquery("UPDATE 
											def_queue 
										SET 
											queue_build_type=1
										WHERE 
											queue_user_id='".$cu->id."';");
							}
							else
							{
								dbquery("UPDATE 
											def_queue 
										SET 
											queue_build_type=1, 
											queue_starttime=".time()." 
										WHERE 
											queue_user_id='".$cu->id."';");
							}
						}
	
						dbquery("UPDATE 
									buildlist 
								SET 
									buildlist_build_type = buildlist_build_type - 2,
									buildlist_build_start_time=".time()." 
								WHERE 
									buildlist_user_id='".$cu->id."' 
									AND buildlist_build_start_time>0;");
						dbquery("UPDATE 
									techlist 
								SET 
									techlist_build_type=1, 
									techlist_build_start_time=".time()." 
								WHERE 
									techlist_user_id='".$cu->id."' 
									AND techlist_build_start_time>0;");
					
						$hfrom=time();
						$hto=$hfrom+(MIN_UMOD_TIME*24*3600);
						if (dbquery("UPDATE users SET user_hmode_from='$hfrom',user_hmode_to='$hto' WHERE user_id='".$cu->id."';"))
						{
							dbquery ("
								UPDATE 
									planets 
								SET 
									planet_last_updated='0',
									planet_prod_metal=0,
									planet_prod_crystal=0,
									planet_prod_plastic=0,
									planet_prod_fuel=0,
									planet_prod_food=0
								WHERE 
									planet_user_id='".$cu->id."';");
										
								$arr['user_hmode_to'] = $hto;
								success_msg("Du bist nun im Urlaubsmodus bis [b]".df($hto)."[/b].");
								$cu->addToUserLog("settings","{nick} ist nun im Urlaub.",1);
								$umod = true;
						}
					}
					else
					{
						err_msg("Es sind noch Flotten unterwegs!");
					}
				}
				else
				{
					err_msg("Es sind noch Flotten unterwegs!");
				}
			}
			else
			{
				err_msg("Du musst mindestens ".$cfg->user_umod_min_length." Tage nach deiner letzten Invasion warten, bis du in den Urlaubsmodus gehen kannst!");
			}
				
		}
	
		//
		// Urlaubsmodus aufheben
		//
	
		if (isset($_POST['hmod_off']) && checker_verify())
		{
			if ($cu->hmode_from > 0 && $cu->hmode_from < time() && $cu->hmode_to < time())
			{
				$bres = dbquery("
								SELECT
									buildlist_id,
									(buildlist_build_end_time-buildlist_build_start_time) AS time,
									buildlist_build_type
								FROM
									buildlist
								WHERE
									buildlist_build_start_time>0
									AND buildlist_build_type>0
									AND buildlist_user_id=".$cu->id.";");
							
				while ($barr=mysql_fetch_row($bres))
				{
					dbquery("UPDATE buildlist SET buildlist_build_type='".$barr[2]."+2',buildlist_build_start_time=".time().", buildlist_build_end_time='".time()."+".$barr[1]."' WHERE buildlist_id=".$barr[0].";");
				} 
				
				$bres = dbquery("
								SELECT
									techlist_id,
									(techlist_build_end_time-techlist_build_start_time) AS time,
									techlist_build_type
								FROM
									techlist
								WHERE
									techlist_build_start_time>0
									AND techlist_build_type>0
									AND techlist_user_id=".$cu->id.";");
									
				while ($barr=mysql_fetch_row($bres))
				{
					dbquery("UPDATE techlist SET techlist_build_type='".$barr[2]."+2',techlist_build_start_time=".time().", techlist_build_end_time='".time()."+".$barr[1]."' WHERE techlist_id=".$barr[0].";");
				}
				
				$sres = dbquery("SELECT 
									queue_id,
									(queue_endtime-queue_starttime) AS time
								 FROM 
								 	ship_queue 
								WHERE 
									queue_user_id='".$cu->id."'
								ORDER BY 
									queue_starttime ASC;");
				$time = time();
				while ($sarr=mysql_fetch_row($sres))
				{
					dbquery("UPDATE 
								ship_queue
							SET
								queue_build_type=0,
								queue_starttime=".$time.",
								queue_endtime='".$time+$sarr[1]."'
							WHERE
								queue_id=".$sarr[0].";");
					$time+=$sarr[1];
				}
				
			$dres = dbquery("SELECT 
									queue_id,
									(queue_endtime-queue_starttime) AS time
								 FROM 
								 	def_queue 
								WHERE 
									queue_user_id='".$cu->id."'
								ORDER BY 
									queue_starttime ASC;");
				$time = time();
				while ($sarr=mysql_fetch_row($sres))
				{
					dbquery("UPDATE 
								def_queue
							SET
								queue_build_type=0,
								queue_starttime=".$time.",
								queue_endtime='".$time+$sarr[1]."'
							WHERE
								queue_id=".$sarr[0].";");
					$time+=$sarr[1];
				}
					
				dbquery("UPDATE users SET user_hmode_from=0,user_hmode_to=0 WHERE user_id='".$cu->id."';");
				dbquery ("UPDATE planets SET planet_last_updated=".time()." WHERE planet_user_id='".$cu->id."';");
				
				foreach ($planets as $pid)
					sendBackendMessage("planetupdate:".$pid);
				
				success_msg("Urlaubsmodus aufgehoben! Denke daran, auf allen deinen Planeten die Produktion zu überprüfen!");
				$cu->addToUserLog("settings","{nick} ist nun aus dem Urlaub zurück.",1);
				
				echo '<input type="button" value="Zur Übersicht" onclick="document.location=\'?page=overview\'" />';
			}
			else
			{
				err_msg("Urlaubsmodus kann nicht aufgehoben werden!");
			}
		}
	
		//
		// Löschbestätigung
		//
		elseif (isset($_POST['remove']) && checker_verify())
		{
				echo "<form action=\"?page=$page&amp;mode=misc\" method=\"post\">";
	    	iBoxStart("Löschung bestätigen");
				echo "Soll dein Account wirklich zur Löschung vorgeschlagen werden?<br/><br/>";
				echo "<b>Passwort eingeben:</b> <input type=\"password\" name=\"remove_password\" value=\"\" />";
				iBoxEnd();
				echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page&mode=misc'\" /> 
				<input type=\"submit\" name=\"remove_submit\" value=\"Account l&ouml;schen\" />";
				echo "</form>";
		}

		//
		// User löschen
		//	
		elseif (isset($_POST['remove_submit']))
		{
			$pres = dbquery("
			SELECT 
				user_password,
				user_registered 
			FROM 
				users 
			WHERE 
				user_id=".$cu->id."
			;");
			$parr=mysql_fetch_row($pres);
			if ($parr[0]==pw_salt($_POST['remove_password'],$parr[1]))
			{
				$t = time() + ($conf['user_delete_days']['v']*3600*24);
				dbquery("
				UPDATE
					users
				SET
					user_deleted=".$t."
				WHERE
					user_id=".$cu->id."
				;");
				
					$s=Null;
					session_destroy();
					success_msg("Deine Daten werden am ".df($t)." Uhr von unserem System gelöscht! Wir w&uuml;nschen weiterhin viel Erfolg im Netz!");
					$cu->addToUserLog("settings","{nick} hat seinen Account zur Löschung freigegeben.",1);
					echo '<input type="button" value="Zur Startseite" onclick="document.location=\''.Config::getInstance()->loginurl->v.'\'" />';
			}
			else
			{
				error_msg("Falsches Passwort!");
				echo '<input type="button" value="Weiter" onclick="document.location=\'?page=userconfig&mode=misc\'" />';
			}
		}

		//
		// Löschantrag aufheben
		//
		elseif (isset($_POST['remove_cancel']) && checker_verify())
		{
			dbquery("
			UPDATE
				users
			SET
				user_deleted=0
			WHERE
				user_id=".$cu->id."
			;");
			success_msg("Löschantrag aufgehoben!");
			$cu->addToUserLog("settings","{nick} hat seine Accountlöschung aufgehoben.",1);
			echo '<input type="button" value="Weiter" onclick="document.location=\'?page=userconfig&mode=misc\'" />';
		}

		//
		// Auswahl
		//
		else
		{
			$id = $cu->id;
			iBoxStart("Banner");
			echo 'Hilf mit, EtoA bekannter zu machen und binde unser Banner auf deiner Website ein! 
			Hier findest du den Quellcode um das Banner einzubinden:<br><br>';
			
			$name = CACHE_ROOT.'/userbanner/'.md5('user'.$id).'.png';
			if (file_exists($name))
			{
				echo '<div style="text-align: center;">
				<img src="'.$name.'" alt="Banner"><br><br>
				<textarea readonly="readonly" rows="2" cols="65">&lt;a href="http://www.etoa.ch"&gt;&lt;img src="'.$cfg->roundurl.'/'.$name.'" width="468" height="60" alt="EtoA Online-Game" border="0" /&gt;&lt;/a&gt;</textarea>
				<textarea readonly="readonly" rows="2" cols="65">[url=http://www.etoa.ch][img]'.$cfg->roundurl.'/'.$name.'[/img][/url]</textarea>';				
			}
			else
			{
				echo "Momentan ist kein Banner verfügbar!";
			}
			
			
			iBoxEnd();
			
			
				echo "<form action=\"?page=$page&amp;mode=misc\" method=\"post\">";		
	    	checker_init();
	    	tableStart("Sonstige Accountoptionen");
			
	    	// Urlaubsmodus
	    	echo "<tr><th style=\"width:150px;\">Urlaubsmodus</th>
	    	<td>Im Urlaubsmodus kannst du nicht angegriffen werden, aber deine Produktion steht auch still. Du darfst nichts im Bau haben
	    	um den Urlaubsmodus aktivieren zu können.<br/><b>Dauer:</b> mindestens ".MIN_UMOD_TIME." Tage</td>
	    	<td>";
			
	    	if ($cu->hmode_from>0 && $cu->hmode_from<time() && $cu->hmode_to<time())
	    	{
	    		echo "<input type=\"submit\" style=\"color:#0f0\" name=\"hmod_off\" value=\"Urlaubsmodus deaktivieren\" />";
	    	}
	    	elseif ($arr['user_hmode_from']>0 && $cu->hmode_from<time() && $cu->hmode_to>=time() || $umod)
	    	{
	    	  echo "<span style=\"color:#f90\">Urlaubsmodus ist aktiv bis mindestens <b>".df($cu->hmode_to)."</b>!</span>";
	    	}
	    	else
	    	{
	    	  echo "<input type=\"submit\" value=\"Urlaubsmodus aktivieren\" name=\"hmod_on\" onclick=\"return confirm('Soll der Urlaubsmodus wirklich aktiviert werden?')\" />";
	    	} 
	    	echo "</td></tr>";
	
				// Account löschen
	    	echo "<tr><th>Account l&ouml;schen</th>
	    	<td>Hier kannst du deinen Account mitsamt aller Daten löschen.</td>
	    	<td>";
	    	if ($cu->deleted>0)
	    	{
	    		echo "<input type=\"submit\" name=\"remove_cancel\" value=\"Löschantrag aufheben\"  style=\"color:#0f0\" />";
	    	}
	    	else
	    	{
	    		echo "<input type=\"submit\" name=\"remove\" value=\"Account l&ouml;schen\" />";
	    	}
	    	echo "</td></tr>";
	    	

	    	tableEnd();
				echo "</form>";
		}
	
?>