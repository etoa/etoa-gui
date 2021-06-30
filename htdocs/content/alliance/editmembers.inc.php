<?PHP

use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceManagementService;
use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

/** @var AllianceManagementService */
$allianceManagementService = $app[AllianceManagementService::class];

/** @var AllianceHistoryRepository */
$allianceHistoryRepository = $app[AllianceHistoryRepository::class];

/** @var Alliance $ally */
/** @var bool $isFounder */

if (Alliance::checkActionRights('editmembers'))
{

		echo "<h2>Allianzmitglieder</h2>";
		// Ränge laden
        $rank = [];
		$rres = dbquery("
		SELECT
			rank_name,
			rank_id
		FROM
			alliance_ranks
		WHERE
			rank_alliance_id=".$cu->allianceId.";");
		while ($rarr=mysql_fetch_assoc($rres))
		{
			$rank[$rarr['rank_id']]=$rarr['rank_name'];
		}
		echo "<form action=\"?page=$page&amp;action=editmembers\" method=\"post\">";


		// Mitgliederänderungen speichern
		if (isset($_POST['editmemberssubmit']) && checker_verify())
		{
			if (isset($_POST['user_alliance_rank_id']) && count($_POST['user_alliance_rank_id'])>0)
			{
				foreach ($_POST['user_alliance_rank_id'] as $uid=>$rid)
				{
				    $uid = intval($uid);
					$rid = intval($rid);

					if (mysql_num_rows(dbquery("SELECT user_id FROM users WHERE user_alliance_rank_id!='$rid' AND user_id='$uid';"))>0)
					{
						dbquery("UPDATE users SET user_alliance_rank_id='$rid' WHERE user_id='$uid';");
						$allianceHistoryRepository->addEntry($ally->id, "Der Spieler [b]".get_user_nick($uid)."[/b] erhält den Rang [b]".$rank[$rid]."[/b].");
					}
				}
				success_msg("&Auml;nderungen wurden übernommen!");
			}

			// Handle user move from wing to wing or main

			if($config->getBoolean('allow_wings'))
			{
				$checked_arr = array();
				if (count($ally->wings) > 0)
				{
					if (isset($_POST['moveuser']) && count($_POST['moveuser'])>0)
					{
						foreach ($_POST['moveuser'] as $wf => $wd)	//wf = source alliance id
						{											//wd = array with alliance members
						    $wf = intval($wf);
							foreach ($wd as $uk => $wt)				//uk = user id
							{										//wt = value (target alliance id)
								$uk = intval($uk);
								$wt = intval($wt);
								if ($wt!=0)
								{
									if ($wf!= $wt && ($wf == $ally->id || isset($ally->wings[$wf])) && ($wt == $ally->id || isset($ally->wings[$wt])))
									{
										if ($wf == $ally->id)
										{
                                            $allianceManagementService->kickMember($ally->id, $uk);
										}
										else
										{
                                            $allianceManagementService->kickMember($ally->wings[$wf]->id, $uk);
										}

										$checked_arr[$uk] = $wt;
									}
								}
							}
						}
					}
				}
				// Bug-Workaround by river: First kick all, then reassign them to alliances
				// to prevent ressources penalty for temporary +1 user
				// TODO: A cool user-swap-function for alliances would be a better solution.
				if (count($checked_arr)>0)
				{
					foreach ($checked_arr as $moving_user_id => $target_alliance)
					{
						if ($target_alliance == $ally->id)
						{
							if ($allianceManagementService->addMember($ally->id, $moving_user_id)) {
								success_msg($ally->members[$moving_user_id]." wurde umgeteilt!");
							} else {
								error_msg("Umteilung nicht möglich, User ist bereits Mitglied oder die maximale Anzahl an Mitgliedern wurde erreicht!");
							}
						}
						else
						{
							if ($allianceManagementService->addMember($ally->wings[$target_alliance]->id, $moving_user_id)) {
								success_msg($ally->wings[$target_alliance]->members[$moving_user_id]." wurde verschoben!");
							} else {
								error_msg("Verschiebung nicht möglich, User ist bereits Mitglied oder die maximale Anzahl an Mitgliedern wurde erreicht!");
							}
						}

					}
				}
			}
		}

		// Gründer wechseln
		if (isset($_GET['setfounder']) && intval($_GET['setfounder'])>0 && $isFounder && $cu->id!=intval($_GET['setfounder']))
		{
		    $fid = intval($_GET['setfounder']);

			if (isset($ally->members[$fid]))
			{
				$ally->founderId = $fid;
				Log::add(5,Log::INFO, "Der Spieler [b]".$ally->founder."[/b] wird vom Spieler [b]".$cu."[/b] zum Gründer befördert.");
				success_msg("Gründer ge&auml;ndert!");
			}
			else
				error_msg("User nicht gefunden!");
		}

		// Mitglied kicken
		if (isset($_GET['kickuser']) && intval($_GET['kickuser'])>0 && checker_verify() && !$cu->alliance->isAtWar())
		{
		    $kid = intval($_GET['kickuser']);

			if (isset($ally->members[$kid]))
			{
				$tmpUser = $ally->members[$kid];
				if ($allianceManagementService->kickMember($ally->id, $kid))
				{
					Log::add(5,Log::INFO, "Der Spieler [b]".$tmpUser."[/b] wurde von [b]".$cu."[/b] aus der Allianz [b]".$ally."[/b] ausgeschlossen!");
					success_msg("Der Spieler [b]".$tmpUser."[/b] wurde aus der Allianz ausgeschlossen!");
					unset($tmpUser);
				}
				else
				{
					error_msg("Der Spieler konnte nicht aus der Allianz ausgeschlossen werden, da er in einem Allianzangriff unterwegs ist!");
				}
			}
			else
			{
				error_msg("Der Spieler konnte nicht aus der Allianz ausgeschlossen werden, da er kein Mitglieder dieser Allianz ist!");
			}
		}

		checker_init();
		tableStart();
		echo "<tr>
			<th>Nick:</th>
			<th>Punkte:</th>
			<th>Online:</th>
			<th>Rang:</th>";
			if ($config->getBoolean('allow_wings') && count($ally->wings) > 0)
				echo "<th>Umteilen</th>";
			echo "<th>Aktionen</th>
		</tr>";
		foreach ($ally->members as $mk => $mv)
		{
			echo "<tr>";
			// Nick, Planet, Punkte
			echo "<td>".$mv."</td>
			<td>".nf($mv->points)."</td>";
			// Zuletzt online
			if ((time()-$config->getInt('online_threshold')*60) < $mv->acttime)
				echo "<td style=\"color:#0f0;\">online</td>";
			else
				echo "<td>".date("d.m.Y H:i",$mv->acttime)."</td>";

			// Rang
			if ($mk == $ally->founderId)
				echo "<td>Gründer</td>";
			else
			{
				echo "<td><select name=\"user_alliance_rank_id[".$mk."]\">";
				echo "<option value=\"0\">Rang w&auml;hlen...</option>";
				foreach ($rank as $id=>$name)
				{
					echo "<option value=\"$id\"";
					if ($mv->allianceRankId == $id) echo " selected=\"selected\"";
					echo ">".$name."</option>";
				}
				echo "</select></td>";
			}

			if ($config->getBoolean('allow_wings') && count($ally->wings) > 0)
			{
				echo "<td>";
				if ($ally->founderId != $mk && !$ally->isAtWar()) {
						echo "<select name=\"moveuser[".$ally->id."][".$mk."]\">
					<option value=\"\">Keine Änderung</option>";
					foreach ($ally->wings as $wk => $wv)
					{
						echo "<option value=\"".$wk."\">Wing ".$wv."</option>";
					}
					echo "</select>";
				} elseif ($ally->founderId == $mk) {
					echo "Gründer";
				} else {
					echo "";
				}
				echo "</td>";
			}

			// Aktionen
			echo "<td>";
			if ($cu->id != $mk)
				echo "<a href=\"?page=messages&amp;mode=new&amp;message_user_to=".$mk."\">Nachricht</a><br/>";
			echo "<a href=\"?page=userinfo&amp;id=".$mk."\">Profil</a><br/>";
			if ($isFounder && $cu->id != $mk)
				echo "<a href=\"?page=alliance&amp;action=editmembers&amp;setfounder=".$mk."\" onclick=\"return confirm('Soll der Spieler \'".$mv."\' wirklich zum Gründer bef&ouml;rdert werden? Dir werden dabei die Gründerrechte entzogen!');\">Gründer</a><br/>";

			if ($cu->id != $mk && $mk != $ally->founderId && !$cu->alliance->isAtWar())
			{
				echo "<a href=\"?page=$page&amp;action=editmembers&amp;kickuser=".$mk.checker_get_link_key()."\" onclick=\"return confirm('Soll ".$mv." wirklich aus der Allianz ausgeschlosen werden?');\">Kicken</a>";
			}
			echo "</td></tr>";
		}
		tableEnd();



		if ($config->getBoolean('allow_wings') && count($ally->wings) > 0)
		{
			foreach ($ally->wings as $wid => $wdata)
			{
				tableStart("Mitglieder des Wings ".$wdata);
				echo "<tr>
					<th>Name:</th>
					<th>Punkte:</th>
					<th>Online:</th>
					<th>Umteilen:</th>
					<th>Aktionen:</th>
				</tr>";
				foreach ($wdata->members as $uid => $udata)
				{
						echo "<tr>
						<td>".$udata."</td>
						<td>".nf($udata->points)."</td>";
						// Zuletzt online
						if ((time()-$config->getInt('online_threshold')*60) < $udata->acttime)
							echo "<td style=\"color:#0f0;\">online</td>";
						else
							echo "<td>".date("d.m.Y H:i",$udata->acttime)."</td>";
						echo "<td>";
						if ($wdata->founderId != $uid && !$wdata->isAtWar()) {
								echo "<select name=\"moveuser[".$wid."][".$uid."]\">
							<option value=\"\">Keine Änderung</option>
							<option value=\"".$ally->id."\">Hauptallianz ".$ally."</option>";
							foreach ($ally->wings as $k => $v)
							{
								if ($k != $wid)
									echo "<option value=\"".$k."\">Wing ".$v."</option>";
							}
							echo "</select>";
						} elseif ($wdata->founderId == $uid) {
							echo "Gründer";
						}
						else
						{
							echo "";
						}
						echo "</td><td>
						<a href=\"?page=messages&amp;mode=new&amp;message_user_to=".$uid."\">Nachricht</a><br/>
						<a href=\"?page=userinfo&amp;id=".$uid."\">Profil</a></td></tr>";
				}
				tableEnd();
			}
		}


		echo "<br/><br/><input type=\"submit\" name=\"editmemberssubmit\" value=\"&Uuml;bernehmen\" />&nbsp;&nbsp;&nbsp;
		<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" /></form>";


}
?>
