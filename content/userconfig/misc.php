<?PHP

	$umod = false;

		//
		// Urlaubsmodus einschalten
		//
	
		if (isset($_POST['hmod_on']) && checker_verify())
		{
			$cres = dbquery("SELECT COUNT(*) FROM ship_queue WHERE queue_user_id='".$cu->id()."';");
			$carr = mysql_fetch_row($cres);
			if ($carr[0]==0)
			{
				$cres = dbquery("SELECT COUNT(*) FROM def_queue WHERE queue_user_id='".$cu->id()."';");
				$carr = mysql_fetch_row($cres);
				if ($carr[0]==0)
				{
					$cres = dbquery("SELECT COUNT(*) FROM buildlist WHERE buildlist_user_id='".$cu->id()."' AND buildlist_build_start_time>0;");
					$carr = mysql_fetch_row($cres);
					if ($carr[0]==0)
					{
						$cres = dbquery("SELECT COUNT(*) FROM techlist WHERE techlist_user_id='".$cu->id()."' AND techlist_build_start_time>0;");
						$carr = mysql_fetch_row($cres);
						if ($carr[0]==0)
						{
							$cres = dbquery("SELECT fleet_id FROM ".$db_table['fleet']." WHERE fleet_user_id='".$cu->id()."';");
							$carr = mysql_fetch_row($cres);
							if ($carr[0]==0)
							{
								$hfrom=time();
								$hto=$hfrom+(MIN_UMOD_TIME*24*3600);
								if (dbquery("UPDATE ".$db_table['users']." SET user_hmode_from='$hfrom',user_hmode_to='$hto' WHERE user_id='".$cu->id()."';"))
								{
									dbquery ("
									UPDATE 
										".$db_table['planets']." 
									SET 
										planet_last_updated='0',
										planet_prod_metal=0,
										planet_prod_crystal=0,
										planet_prod_plastic=0,
										planet_prod_fuel=0,
										planet_prod_food=0
									WHERE 
										planet_user_id='".$cu->id()."';");
									
									$s['user']['hmode_from'] = $hfrom;
									$s['user']['hmode_to'] = $hto;
									$arr['user_hmode_to'] = $hto;
									success_msg("Du bist nun im Urlaubsmodus bis [b]".df($hto)."[/b].");
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
							err_msg("Es sind noch Technologien in Entwicklung!");
						}
					}
					else
					{
						err_msg("Es sind noch Geb&auml;ude im Bau!");
					}
				}
				else
					err_msg("Es sind noch Verteidigungsanlagen im Bau!");
			}
			else
			{
				
				err_msg("Es sind noch Schiffe im Bau!");
			}
		}
	
		//
		// Urlaubsmodus aufheben
		//
	
		if (isset($_POST['hmod_off']) && checker_verify())
		{
			if ($s['user']['hmode_from']>0 && $s['user']['hmode_from']<time() && $s['user']['hmode_to']<time())
			{
				dbquery("UPDATE ".$db_table['users']." SET user_hmode_from=0,user_hmode_to=0 WHERE user_id='".$cu->id()."';");
				dbquery ("UPDATE ".$db_table['planets']." SET planet_last_updated=".time()." WHERE planet_user_id='".$cu->id()."';");
				$s['user']['hmode_from']=0;
				$s['user']['hmode_to']=0;
				success_msg("Urlaubsmodus aufgehoben! Denke daran, auf allen deinen Planeten die Produktion zu überprüfen!");
				
				$rres = dbquery ("
				SELECT
					planet_id
				FROM
					".$db_table['planets']." 
				WHERE 
					planet_user_id='".$cu->id()."';");				
				while ($rarr=mysql_fetch_row($rres))
				{
					$tp = new Planet($rarr[0]);
					$tp->updateEconomy();
					$tp->update(1);
				}
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
	    	infobox_start("Löschung bestätigen");
				echo "Soll dein Account wirklich zur Löschung vorgeschlagen werden?<br/><br/>";
				echo "<b>Passwort eingeben:</b> <input type=\"password\" name=\"remove_password\" value=\"\" />";
				infobox_end();
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
				".$db_table['users']." 
			WHERE 
				user_id=".$cu->id()."
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
					user_id=".$cu->id()."
				;");
				
					$s=Null;
					session_destroy();
					success_msg("Deine Daten werden am ".df($t)." Uhr von unserem System gelöscht! <br/>Wir w&uuml;nschen weiterhin viel Erfolg im Netz!");
					echo '<input type="button" value="Zur Startseite" onclick="document.location=\''.LOGINSERVER_URL.'\'" />';
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
				user_id=".$cu->id()."
			;");
			success_msg("Löschantrag aufgehoben!");
			echo '<input type="button" value="Weiter" onclick="document.location=\'?page=userconfig&mode=misc\'" />';
		}

		//
		// Neu anfangen
		//
		elseif (isset($_POST['reroll']) && checker_verify())
		{
			if ($s['allow_planet_change'])
			{
				if ($s['allow_planet_change_counter']<MAX_MAINPLANET_CHANGES)
				{
					$arr1 = mysql_fetch_row(dbquery("SELECT COUNT(shiplist_id) FROM ".$db_table['shiplist']." WHERE shiplist_user_id='".$cu->id()."';"));
					$arr2 = mysql_fetch_row(dbquery("SELECT COUNT(deflist_id) FROM ".$db_table['deflist']." WHERE deflist_user_id='".$cu->id()."';"));
					$arr3 = mysql_fetch_row(dbquery("SELECT COUNT(buildlist_id) FROM ".$db_table['buildlist']." WHERE buildlist_user_id='".$cu->id()."';"));
					$arr4 = mysql_fetch_row(dbquery("SELECT COUNT(techlist_id) FROM ".$db_table['techlist']." WHERE techlist_user_id='".$cu->id()."';"));
					$arr5 = mysql_fetch_row(dbquery("SELECT COUNT(fleet_id) FROM ".$db_table['fleet']." WHERE fleet_user_id='".$cu->id()."';"));
					if ($c->isMain && $arr1[0]==0 && $arr2[0]==0 && $arr3[0]==0 && $arr4[0]==0 && $arr5[0]==0)
					{
						reset_planet($c->id);
						success_msg("Alter Planet wurde zurückgesetzt und ein neuer Planet wird gesucht...");
						echo '<input type="button" value="Weiter" onclick="document.location=\'?page=overview\'" />';
						if (!isset($s['allow_planet_change_counter']))
						{
							$s['allow_planet_change_counter']=1;
						}
						else
						{
							$s['allow_planet_change_counter']++;
						}
					}
					else
					{
						error_msg("Du hast bereits auf deinem Planeten etwas gebaut!");			
						echo '<input type="button" value="Weiter" onclick="document.location=\'?page=userconfig&mode=misc\'" />';
					}
				}
				else
				{
					error_msg("Du kannst nicht mehr als ".MAX_MAINPLANET_CHANGES." mal neu anfangen!");			
					echo '<input type="button" value="Weiter" onclick="document.location=\'?page=userconfig&mode=misc\'" />';
				}				
			}
			else
			{
				error_msg("Ein Neuanfang ist nur während des ersten Logins möglich!");			
				echo '<input type="button" value="Weiter" onclick="document.location=\'?page=userconfig&mode=misc\'" />';
			}
		}
		
		//
		// Auswahl
		//
		else
		{
				echo "<form action=\"?page=$page&amp;mode=misc\" method=\"post\">";		
	    	checker_init();
	    	infobox_start("Sonstige Accountoptionen",1);
	  		 
	    	// Urlaubsmodus
	    	echo "<tr><th class=\"tbltitle\" style=\"width:150px;\">Urlaubsmodus</th>
	    	<td class=\"tbldata\">Im Urlaubsmodus kannst du nicht angegriffen werden, aber deine Produktion steht auch still. Du darfst nichts im Bau haben
	    	um den Urlaubsmodus aktivieren zu können.<br/><b>Dauer:</b> mindestens ".MIN_UMOD_TIME." Tage</td>
	    	<td class=\"tbldata\">";
	    	if ($arr['user_hmode_from']>0 && $arr['user_hmode_from']<time() && $arr['user_hmode_to']<time())
	    	{
	    		echo "<input type=\"submit\" style=\"color:#0f0\" name=\"hmod_off\" value=\"Urlaubsmodus deaktivieren\" />";
	    	}
	    	elseif ($arr['user_hmode_from']>0 && $arr['user_hmode_from']<time() && $arr['user_hmode_to']>=time() || $umod)
	    	{
	    	  echo "<span style=\"color:#f90\">Urlaubsmodus ist aktiv bis mindestens <b>".df($arr['user_hmode_to'])."</b>!</span>";
	    	}
	    	else
	    	{
	    	  echo "<input type=\"submit\" value=\"Urlaubsmodus aktivieren\" name=\"hmod_on\" onclick=\"return confirm('Soll der Urlaubsmodus wirklich aktiviert werden?')\" />";
	    	} 
	    	echo "</td></tr>";
	
				// Account löschen
	    	echo "<tr><th class=\"tbltitle\">Account l&ouml;schen</th>
	    	<td class=\"tbldata\">Hier kannst du deinen Account mitsamt aller Daten löschen</td>
	    	<td class=\"tbldata\">";
	    	if ($arr['user_deleted']>0)
	    	{
	    		echo "<input type=\"submit\" name=\"remove_cancel\" value=\"Löschantrag aufheben\"  style=\"color:#0f0\" />";
	    	}
	    	else
	    	{
	    		echo "<input type=\"submit\" name=\"remove\" value=\"Account l&ouml;schen\" />";
	    	}
	    	echo "</td></tr>";
	    	
				// Neu anfangen löschen
	    	echo "<tr><th class=\"tbltitle\">Neu anfangen</th>
	    	<td class=\"tbldata\">";
	    	$disabled = "";
	    	if (isset($s['allow_planet_change']) && $s['allow_planet_change'])
	    	{
	    		if ($s['allow_planet_change_counter']<MAX_MAINPLANET_CHANGES)
					{
			    	echo "Sollte dein Startplanet nicht deinen Vorstellungen entsprechen kannst du hier das Spiel mit einem
			    	neuen Planeten anfangen. Voraussetzung ist jedoch dass auf dem Startplanet noch nichts gebaut wurde!
			    	<b>Du kannst noch ".(MAX_MAINPLANET_CHANGES-$s['allow_planet_change_counter'])." mal neu anfangen.</b> Beachte dass ein Neuanfang nur währden des
			    	erstmaligen Anmeldens möglich ist!";
			    }
			    else
			    {
			    	echo "Ein Neuanfang ist leider nicht mehr möglich, es sind maximal ".MAX_MAINPLANET_CHANGES." Neuanfänge erlaubt!";
			    	$disabled = "disabled=\"disabled\"";
			    }
	    	}
	    	else
	    	{
	    		echo "Ein Neuanfang ist leider nicht mehr möglich!";
	    		$disabled = "disabled=\"disabled\"";
	    	}
	    	echo "</td>
	    	<td class=\"tbldata\">";
    		echo "<input type=\"submit\" name=\"reroll\" value=\"Neu anfangen\" ".$disabled." onclick=\"return confirm('Wirklich neu anfangen?')\" />";
	    	echo "</td></tr>";


	    	infobox_end(1);
				echo "</form>";
		}
	
?>