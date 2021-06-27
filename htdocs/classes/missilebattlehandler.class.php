<?PHP

use EtoA\Core\Configuration\ConfigurationService;

class MissileBattleHandler
{
	/**
	* Handles missile assault
	*
	* @param int $fid Flight Id
	*/
	static function battle($fid)
	{
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app['etoa.config.service'];

		// Faktor mit dem die Schilde der Verteidigung bei einem Kampf mit einberechnet werden.
		define("MISSILE_BATTLE_SHIELD_FACTOR", $config->getFloat('missile_battle_shield_factor'));

 		// Kampf abbrechen und Raketen zum Startplanet schicken wenn Kampfsperre aktiv ist
 	 	if ($config->getBoolean('battleban') && $config->param1Int('battleban_time') <= time() && $config->param2Int('battleban_time') > time())
		{
			// Lädt Flugdaten
			$res = dbquery("
			SELECT
				flight_entity_from
			FROM
				missile_flights
			WHERE
				flight_id=".$fid."
			;");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_assoc($res);

				// Transferiert Raketen zum Startplanet
				$mres = dbquery("
				SELECT
					obj_missile_id,
					obj_cnt
				FROM
					missile_flights_obj
				WHERE
					obj_flight_id='".$fid."'");
				while($marr = mysql_fetch_assoc($mres))
				{
					dbquery("
					UPDATE
						missilelist
					SET
						missilelist_count=missilelist_count+".$marr['obj_cnt']."
					WHERE
						missilelist_entity_id=".$arr['flight_entity_from']."
						AND missilelist_missile_id=".$marr['obj_missile_id']."
					;");
				}

				// Löscht Flug
				dbquery("
				DELETE FROM
					missile_flights
				WHERE
					flight_id=".$fid."
				;");

				// Löscht Raketen
				dbquery("
				DELETE FROM
					missile_flights_obj
				WHERE
					obj_flight_id=".$fid."
				;");

				// Schickt Nachricht an den Angreifer
				$msg = $config->param2('battleban_arrival_text');
				$uid = get_user_id_by_planet($arr['flight_entity_from']);

                /** @var \EtoA\Message\MessageRepository $messageRepository */
                $messageRepository = $app[\EtoA\Message\MessageRepository::class];
                $messageRepository->createSystemMessage((int) $uid, SHIP_WAR_MSG_CAT_ID, 'Ergebnis des Raketenangriffs', $msg);
			}

			return;
		}

		$res = dbquery("
		SELECT
			flight_entity_to,
			pt.planet_user_id as tuid,
			pt.planet_name,
			pf.planet_user_id as fuid
		FROM
			missile_flights
		INNER JOIN
			planets as pt
			ON flight_entity_to=pt.id
		INNER JOIN
			planets as pf
			ON flight_entity_from=pf.id
			AND flight_id=".$fid."
		;");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			$tid = $arr['flight_entity_to'];
			$tuid = $arr['tuid'];
			$fuid = $arr['fuid'];
			$tname = $arr['planet_name'];
			dbquery("
			DELETE FROM
				missile_flights
			WHERE
				flight_id=".$fid."
			;");
			$mres = dbquery("
			SELECT
				obj_cnt AS cnt,
				missile_damage as dmg,
				missile_deactivate as emp
			FROM
				missile_flights_obj
			INNER JOIN
				missiles
				ON missile_id=obj_missile_id
				AND obj_flight_id='".$fid."'");
			if (mysql_num_rows($mres)>0)
			{
				// Select all attacking missiles
				$m = array();
				$mcnt=0;
				while($marr=mysql_fetch_array($mres))
				{
					for ($x=0;$x<$marr['cnt'];$x++)
					{
						$m[$mcnt]['dmg'] = $marr['dmg'];
						$m[$mcnt]['emp'] = $marr['emp'];
						$mcnt++;
					}
				}
				// Shuffle their order
				shuffle($m);
				// Remove from db
				dbquery("
				DELETE FROM
					missile_flights_obj
				WHERE
					obj_flight_id=".$fid."
				;");

				// Select anti-missiles from target
				$dres =  dbquery("
				SELECT
					missile_def,
					missilelist_count,
					missile_id,
					missilelist_id,
					missile_name
				FROM
					missilelist
				INNER JOIN
					missiles
					ON missilelist_missile_id=missile_id
					AND missilelist_entity_id=".$tid."
					AND missile_def>0
				;");
				$dm = array();
				$dmid = array();
				$dmcnt=0;
				if (mysql_num_rows($dres)>0)
				{
					while($darr=mysql_fetch_array($dres))
					{
						array_push($dmid,$darr['missilelist_id']);
						for ($x=0;$x<$darr['missilelist_count'];$x++)
						{
							$dm[$dmcnt]['id']=$darr['missilelist_id'];
							$dm[$dmcnt]['d']=$darr['missile_def'];
							$dm[$dmcnt]['n']=$darr['missile_name']; // Debug
							$dmcnt++;
						}
					}
				}
				foreach ($dmid as $k)
				{
					dbquery("UPDATE missilelist SET missilelist_count=0 WHERE missilelist_id=".$k.";");
				}
				$dmcnt_start = $dmcnt;

				shuffle($dm);

				$dm_copy = $dm;
				$dmcnt_copy = $dmcnt;
				$def_report = "";
				for ($x=0;$x < $dmcnt;$x++)
				{
					$def_report.= "Feuere ".$dm_copy[$x]['n']." ab...\n";
					for ($y=0;$y < $dm_copy[$x]['d'];$y++)
					{
						$def_report.= "Angreifende Rakete wird getroffen!\n";
						array_pop($m);
						$mcnt--;
					}
					array_pop($dm);
					$dmcnt_copy--;
					if ($mcnt<=0)
						break;
				}
				$dmcnt=$dmcnt_copy;

				if ($def_report!='')
				{
					$def_report = "[b]Verteidigungsbericht:[/b]\n\n".$def_report;
					if ($dmcnt>0)
					{
						$def_report.= "\n[b]Verbleibende Raketen:[/b]\n\n";
						foreach ($dm as $tc=>$tm)
						{
							$def_report.= $tm['n']."\n";
						}
					}
					else
					{
						$def_report.="\nAlle Defensivraketen wurden verbraucht!\n";
					}
				}


				// Check if missiles are left
				if ($mcnt>0)
				{
					$msg_a = "Eure Raketen haben den Planeten [b]".$tname."[/b] angegriffen! ";
					$msg_d = "Euer Planet [b]".$tname."[/b] wurde von einem Raketenangriff getroffen!\n";
					if ($dmcnt_start>0)
					{
						$msg_d .= "Eure Abfangraketen schossen zwar einige angreifende Raketen ab, jedoch kamen die restlichen Raketen trotzdem durch.\n ";
						$msg_d.= "\n".$def_report."\n";
					}

					// Bomb the defense
					$dres = dbquery("
					SELECT
						def_structure,
						def_shield,
						def_name,
						deflist_id,
						deflist_count
					FROM
						deflist
					INNER JOIN
						defense
						ON deflist_def_id=def_id
						AND deflist_entity_id=".$tid."
						AND deflist_count>0
					");
					if (mysql_num_rows($dres)>0)
					{
						// Def values
						$str = 0;
						$sh = 0;
						$def = array();
						$msg_d.="Anlagen vor dem Angriff:\n\n";
						while ($darr=mysql_fetch_array($dres))
						{
							$str += ($darr['def_structure']*$darr['deflist_count']);
							$sh += ($darr['def_shield']*$darr['deflist_count']*MISSILE_BATTLE_SHIELD_FACTOR);
							$def[$darr['deflist_id']]['id']=$darr['deflist_id'];
							$def[$darr['deflist_id']]['count']=$darr['deflist_count'];
							$def[$darr['deflist_id']]['name']=$darr['def_name'];
							$def[$darr['deflist_id']]['structure']=$darr['def_structure'];
							$msg_d.="".$darr['deflist_count']." ".$darr['def_name']."\n";
						}
						shuffle($def);

						// Missile damage
						$mdmg = 0;
						foreach ($m as $mv)
						{
							$mdmg += $mv['dmg'];
						}

						$msg_d.="\nDie Raketen verursachen $mdmg Schaden.\n";

						$sh_rem = $sh - $mdmg;
						if ($sh_rem < 0)
						{
							$msg_d.="Die Schilde halten $sh Schaden auf.\n";

							$str_rem = $str + $sh_rem;
							if ($str_rem > 0)
							{
								$str_det = $str-$str_rem;
								foreach ($def as $k => $do)
								{
									$ds = $do['structure']*$do['count'];
									if ($ds - $str_det > 0)
									{
										$def[$k]['count'] = ceil($def[$k]['count'] * ($ds - $str_det) / $ds);
										break;
									}
									else
									{
										$def[$k]['count']=0;
										$str_det -= ($do['structure']*$do['count']);
									}
								}

								$msg_d.="\nAnlagen nach dem Angriff:\n\n";
								foreach ($def as $v)
								{
									$msg_d .= $v['count']." ".$v['name']."\n";
									dbquery("UPDATE
										deflist
									SET
										deflist_count=".$v['count']."
									WHERE
										deflist_id=".$v['id']."");
								}
							}
							else
							{
								$msg_d .= 'Sämtliche Verteidigungsanlagen wurden zerstört!'."\n";
								dbquery("
								UPDATE
									deflist
								SET
									deflist_count=0
								WHERE
									deflist_entity_id=".$tid.";");
							}
						}
						else
						{
							$msg_d .= 'Es wurden aber keine Schäden festgestellt da eure Schilde allen Schaden abgefangen haben.'."\n";
						}
					}
					else
					{
						$msg_d .= 'Es wurden aber keine Schäden festgestellt da Ihr keine Verteidigungsanlagen habt.'."\n";
					}

					// EMP
					$time = time();
					foreach ($m as $mo)
					{
						if ($mo['emp']>0)
						{
							$res = dbquery("SELECT
								building_name,
								buildlist_id
							FROM
								buildlist
							INNER JOIN
								buildings
								ON building_id=buildlist_building_id
								AND buildlist_entity_id=".$tid."
								AND buildlist_current_level > 0
								AND (
								buildlist_building_id='".FLEET_CONTROL_ID."'
								OR buildlist_building_id='".FACTORY_ID."'
								OR buildlist_building_id='".SHIPYARD_ID."'
								OR buildlist_building_id='".MARKTPLATZ_ID."'
								OR buildlist_building_id='".BUILD_CRYPTO_ID."'
								)
								AND buildlist_deactivated<".$time."
							ORDER BY
								RAND()
							LIMIT
								1;");
							if (mysql_num_rows($res)>0)
							{
								$arr=mysql_fetch_array($res);
								$msg_a.= "Das Gebäude ".$arr['building_name']." wurde für ".tf($mo['emp'])." deaktiviert!\n";
								$msg_d.= "Euer Gebäude ".$arr['building_name']." wurde für ".tf($mo['emp'])." deaktiviert!\n";
								dbquery("
								UPDATE
									buildlist
								SET
									buildlist_deactivated=".($time+$mo['emp'])."
								WHERE
									buildlist_id=".$arr['buildlist_id']."
								;");
							}
						}
					}


				}
				else
				{
					$msg_a = "Der Kontakt zu den Raketen die den Planeten [b]".$tname."[/b] angreifen sollten ist verlorengegangen!";
					$msg_d = "Eure Defensivraketen auf [b]".$tname."[/b] haben erfolgreich einen feindlichen Raketenangriff abgewehrt!";
					$msg_d.= "\n\n".$def_report;
				}

				// Set remaining defense missiles
				foreach ($dm as $dm_obj)
				{
					dbquery("
					UPDATE
						missilelist
					SET
						missilelist_count=missilelist_count+1
					WHERE
						missilelist_id=".$dm_obj['id']."
					");
				}

                /** @var \EtoA\Message\MessageRepository $messageRepository */
                $messageRepository = $app[\EtoA\Message\MessageRepository::class];
                $messageRepository->createSystemMessage((int) $fuid, SHIP_WAR_MSG_CAT_ID, 'Ergebnis des Raketenangriffs', $msg_a);
                $messageRepository->createSystemMessage((int) $tuid, SHIP_WAR_MSG_CAT_ID, 'Raketenangriff', $msg_d);
			}
		}
	}
}
?>
