<?PHP

use EtoA\Admin\AdminUserRepository;
use EtoA\Core\Logging\GameLog;
use EtoA\Help\TicketSystem\TicketRepository;

$xajax->register(XAJAX_FUNCTION,"showTimeBox");
$xajax->register(XAJAX_FUNCTION,"allianceRankSelector");
$xajax->register(XAJAX_FUNCTION,"userPointsTable");
$xajax->register(XAJAX_FUNCTION,"addUserComment");
$xajax->register(XAJAX_FUNCTION,"delUserComment");
$xajax->register(XAJAX_FUNCTION,"userLogs");
$xajax->register(XAJAX_FUNCTION,"addUserLog");
$xajax->register(XAJAX_FUNCTION,"userTickets");
$xajax->register(XAJAX_FUNCTION,"userComments");
$xajax->register(XAJAX_FUNCTION,"sendUrgendMsg");
$xajax->register(XAJAX_FUNCTION,"showLast5Messages");
$xajax->register(XAJAX_FUNCTION,"loadEconomy");

function showTimeBox($parent,$name,$value,$show=1)
{
	$or = new xajaxResponse();
	ob_start();
	if ($show>0)
	{
		show_timebox($name,intval($value),1);
	}
	else
	{
		echo "-";
	}
	$out = ob_get_contents();
	ob_end_clean();
	$or->assign($parent,"innerHTML",$out);
	return $or;
}

function allianceRankSelector($parent,$name,$value=0,$aid=0)
{
	$or = new xajaxResponse();
	ob_start();
	if ($aid!=0)
	{
		$rres = dbquery("
		SELECT
			rank_id,
			rank_name
		FROM
			alliance_ranks
		WHERE
			rank_alliance_id=".$aid."");
		if (mysql_num_rows($rres)>0)
		{
			echo "<select name=\"".$name."\"><option value=\"0\">(Kein Rang)</option>";
			while ($rarr=mysql_fetch_array($rres))
			{
				echo "<option value=\"".$rarr['rank_id']."\"";
				if ($value==$rarr['rank_id'])
				{
					echo " selected=\"selected\"";
				}
				echo ">".$rarr['rank_name']."</option>";
			}
			echo "</select>";
		}
		else
		{
			echo "-";
		}
	}
	else
	{
		echo "-";
	}
	$out = ob_get_contents();
	ob_end_clean();
	$or->assign($parent,"innerHTML",$out);
	return $or;
}

function userPointsTable($uid,$target,$length=100,$start=-1,$end=-1)
{
	$t = time();
	if ($start==-1)
	{
		$start = $t-172800;
	}
	if ($end==-1)
	{
		$end = $t;
	}

	$or = new xajaxResponse();
	ob_start();
	$limitarr = array(10,20,30,50,100,200);

	echo "<div id=\"pointGraphDetail\" style=\"text-align:center;margin-bottom:6px;\">
	<img src=\"../misc/stats.image.php?user=".$uid."&amp;limit=".$length."&amp;start=".$start."&amp;end=".$end."\" alt=\"Diagramm\" />
	<br/>";
	echo "Zeige maximal <select id=\"pointsLimit\" onchange=\"xajax_userPointsTable($uid,'$target',
	document.getElementById('pointsLimit').options[document.getElementById('pointsLimit').selectedIndex].value,
	document.getElementById('pointsTimeStart').options[document.getElementById('pointsTimeStart').selectedIndex].value,
	document.getElementById('pointsTimeEnd').options[document.getElementById('pointsTimeEnd').selectedIndex].value
	);\">";
	foreach($limitarr as $x)
	{
		echo "<option value=\"$x\"";
		if ($x==$length) echo " selected=\"selected\"";
		echo ">$x</option>";
	}
	echo "</select> Datensätze von <select id=\"pointsTimeStart\" onchange=\"xajax_userPointsTable($uid,'$target',
	document.getElementById('pointsLimit').options[document.getElementById('pointsLimit').selectedIndex].value,
	document.getElementById('pointsTimeStart').options[document.getElementById('pointsTimeStart').selectedIndex].value,
	document.getElementById('pointsTimeEnd').options[document.getElementById('pointsTimeEnd').selectedIndex].value
	);\">";
	for ($x = $t-86400; $x > $t-(14*86400);$x-=86400)
	{
		echo "<option value=\"$x\"";
		if ($x<=$start+300 && $x>=$start-300) echo " selected=\"selected\"";
		echo ">".df($x)."</option>";
	}
	echo "</select> bis <select id=\"pointsTimeEnd\" onchange=\"xajax_userPointsTable($uid,'$target',
	document.getElementById('pointsLimit').options[document.getElementById('pointsLimit').selectedIndex].value,
	document.getElementById('pointsTimeStart').options[document.getElementById('pointsTimeStart').selectedIndex].value,
	document.getElementById('pointsTimeEnd').options[document.getElementById('pointsTimeEnd').selectedIndex].value
	);\">";
	for ($x = $t; $x > $t-(13*86400);$x-=86400)
	{
		echo "<option value=\"$x\"";
		if ($x<=$end+300 && $x>=$end-300) echo " selected=\"selected\"";
		echo ">".df($x)."</option>";
	}
	echo "</select>

	<br/></div>";
	echo "<table class=\"tb\">";
	$lres=dbquery("
	SELECT
		*
	FROM
		user_points
	WHERE
		point_user_id=".$uid."
		AND point_timestamp > ".$start."
		AND point_timestamp < ".$end."
	ORDER BY
		point_timestamp DESC
	LIMIT ".$length."
	;");
	if (mysql_num_rows($lres)>0)
	{
		echo "<tr>
			<th>Datum</th>
			<th>Zeit</th>
			<th>Punkte</th>
			<th>Gebäude</th>
			<th>Forschung</th>
			<th>Flotte</th>
		</tr>";
		while ($larr=mysql_fetch_array($lres))
		{
			echo "<tr>
				<td class=\"tbldata\">".date("d.m.Y",$larr['point_timestamp'])."</td>
				<td class=\"tbldata\">".date("H:i",$larr['point_timestamp'])."</td>
				<td class=\"tbldata\">".nf($larr['point_points'])."</td>
				<td class=\"tbldata\">".nf($larr['point_building_points'])."</td>
				<td class=\"tbldata\">".nf($larr['point_tech_points'])."</td>
				<td class=\"tbldata\">".nf($larr['point_ship_points'])."</td>
			</tr>";
		}
	}
	else
	{
		echo "<tr><td class=\"tbldata\">Keine fehlgeschlagenen Logins</td></tr>";
	}
	echo "</table>";

	$out = ob_get_contents();
	ob_end_clean();
	$or->assign($target,"innerHTML",$out);
	return $or;
}

function userTickets($uid,$target)
{
	global $app;

	/** @var AdminUserRepository */
	$adminUserRepo = $app['etoa.admin.user.repository'];

	/** @var TicketRepository */
	$ticketRepo = $app['etoa.help.ticket.repository'];

	$or = new xajaxResponse();
	ob_start();
	echo "<table class=\"tb\">";

	$tickets = $ticketRepo->findBy(["user_id" => $uid]);
	if (count($tickets) > 0)
	{
		echo "<tr>
			<th>ID</th>
			<th>Datum</th>
			<th>Kategorie</th>
			<th>Status</th>
			<th>Admin</th>
		</tr>";
		foreach ($tickets as $ticket)
		{
			$adminNick = $adminUserRepo->getNick($ticket->adminId);
			echo "<tr>
				<td><a href=\"?page=tickets&id=".$ticket->id."\">".$ticket->getIdString()."</a></td>
				<td class=\"tbldata\">".df($ticket->timestamp)."</td>
				<td class=\"tbldata\">".$ticketRepo->getCategoryName($ticket->catId)."</td>
				<td class=\"tbldata\">".$ticket->getStatusName()."</td>
				<td class=\"tbldata\">".$adminNick."</td>
			</tr>";
		}
	}
	else
	{
		echo "<tr><td class=\"tbldata\">Keine Tickets</td></tr>";
	}
	echo "</table>";

	$out = ob_get_contents();
	ob_end_clean();
	$or->assign($target,"innerHTML",$out);
	return $or;
}


function sendUrgendMsg($uid,$subject,$text)
{
    global $app;

	$or = new xajaxResponse();
	if ($text!="" && $subject!="")
	{
	    /** @var \EtoA\Message\MessageRepository $messageRepository */
	    $messageRepository = $app[\EtoA\Message\MessageRepository::class];
	    $messageRepository->createSystemMessage((int) $uid, USER_MSG_CAT_ID, $subject, $text);

		$or->alert("Nachricht gesendet!");
		$or->assign('urgendmsgsubject',"value","");
		$or->assign('urgentmsg',"value","");
		$or->script("showLoader('lastmsgbox');xajax_showLast5Messages(".$uid.",'lastmsgbox');");
	}
	else
	{
		$or->alert("Titel oder Text fehlt!");
	}
	return $or;
}

function showLast5Messages($uid,$target,$limit=5)
{
	$or = new xajaxResponse();
	ob_start();
	echo "<table class=\"tb\">";
	$lres=dbquery("
	SELECT
		user_nick,
		user_points,
		message_user_from,
		md.subject,
		md.text,
		message_id,
		message_timestamp,
		message_read,
		alliance_id,
		alliance_name,
		alliance_tag
	FROM
		messages
	INNER JOIN
		message_data as md
		on md.id=message_id
	LEFT JOIN
		(
			users
		INNER JOIN
			alliances
		ON
			user_alliance_id=alliance_id
		)
	ON
		message_user_from=user_id
	WHERE
		message_user_to='".$uid."'
	ORDER BY
		message_timestamp DESC
	LIMIT
		".$limit."
	;");
	if (mysql_num_rows($lres)>0)
	{
		echo "<tr>
			<th>Datum</th>
			<th>Sender</th>
			<th>Titel</th>
			<th>Text</th>
			<th>Gelesen</th>
			<th>Optionen</th>
		</tr>";
		while ($larr=mysql_fetch_array($lres))
		{
			// Generiert MouseOver Text
			$tm = "";
			if($larr['user_nick']!="")
			{
				$tm .= "Punkte: ".nf($larr['user_points'])."";
			}
			if($larr['alliance_id'] != 0)
			{
				$tm .= "<br>Allianz: [".$larr['alliance_tag']."] ".$larr['alliance_name']."";
			}


			echo "<tr>
				<td class=\"tbldata\">".df($larr['message_timestamp'])."</td>
				<td class=\"tbldata\" ";
				if($larr['user_nick']!="")
				{
					echo tm("",$tm);
				}
				echo "><a href=\"?page=user&sub=edit&user_id=".$larr['message_user_from']."\">".$larr['user_nick']."</a></td>
				<td class=\"tbldata\">".$larr['subject']."</td>
				<td class=\"tbldata\">".text2html($larr['text'])."</td>
				<td class=\"tbldata\">".($larr['message_read']==1 ? "Ja" : "Nein")."</td>
				<td class=\"tbldata\">[<a href=\"?page=messages&sub=edit&message_id=".$larr['message_id']."\">Details</a>]</td>
			</tr>";
		}
	}
	else
	{
		echo "<tr><td class=\"tbldata\">Keine Nachrichten vorhanden!</td></tr>";
	}
	echo "</table>";

	$out = ob_get_contents();
	ob_end_clean();
	$or->assign($target,"innerHTML",$out);
	return $or;
}

function userComments($uid,$target)
{
	$or = new xajaxResponse();
	ob_start();
	echo "<h2>Neuer Kommentar:</h2><textarea rows=\"4\" cols=\"70\" id=\"new_comment_text\"></textarea><br/><br/>";
	echo "<input type=\"button\" onclick=\"xajax_addUserComment('$uid','$target',document.getElementById('new_comment_text').value);\" value=\"Speichern\" />";
	echo "<h2>Gespeicherte Kommentare</h2><table class=\"tb\">";
	$lres=dbquery("
	SELECT
		*
	FROM
		user_comments
	LEFT JOIN
		admin_users
	ON
		comment_admin_id=user_id
	WHERE
		comment_user_id=".$uid."
	ORDER BY
		comment_timestamp DESC
	;");
	if (mysql_num_rows($lres)>0)
	{
		echo "<tr>
			<th>Text</th>
			<th>Verfasst</th>
			<th>Aktionen</th>
		</tr>";
		while ($larr=mysql_fetch_array($lres))
		{
			echo "<tr>
				<td class=\"tbldata\" >".text2html($larr['comment_text'])."</td>
				<td class=\"tbldata\" style=\"width:200px;\">".df($larr['comment_timestamp'])." von ".$larr['user_nick']."</td>
				<td class=\"tbldata\" style=\"width:50px;\"><a href=\"javascript:;\" onclick=\"if (confirm('Wirklich löschen?')) {xajax_delUserComment('".$uid."','".$target."',".$larr['comment_id'].")}\">Löschen</a></td>
			</tr>";
		}
	}
	else
	{
		echo "<tr><td class=\"tbldata\">Keine Kommentare</td></tr>";
	}
	echo "</table></div>";

	$out = ob_get_contents();
	ob_end_clean();
	$or->assign($target,"innerHTML",$out);
	return $or;
}

function addUserComment($uid,$target,$text)
{
	$or = new xajaxResponse();
	if ($text!="")
	{
		$or->script("showLoader('$target');");
		dbquery("INSERT INTO user_comments (comment_timestamp,comment_user_id,comment_admin_id,comment_text) VALUES ('".time()."','$uid','".$_SESSION['user_id']."','".addslashes($text)."');");
		$or->script("xajax_userComments('$uid','$target')");
	}
	else
	{
		$or->alert("Fehler! Kein Text!");

	}
	return $or;
}

function delUserComment($uid,$target,$id)
{
	$or = new xajaxResponse();
	if ($id>0)
	{
		$or->script("showLoader('$target');");
		dbquery("DELETE FROM user_comments WHERE comment_id=".$id.";");
		$or->script("xajax_userComments('$uid','$target')");
	}
	else
	{
		$or->alert("Fehler! Falsche ID!");

	}
	return $or;
}


function userLogs($uid,$target)
{
	$or = new xajaxResponse();
	ob_start();
	tableStart("",'100%');
	echo "<tr><th>Nachricht</th><th>Datum</th><th>IP</th></tr>";
	$lres = dbquery("
					SELECT
						*
					FROM
						user_log
					WHERE
						user_id=".$uid."
					ORDER BY timestamp DESC
					LIMIT 100;");
	if (mysql_num_rows($lres) > 0)
	{
		while ($larr = mysql_fetch_array($lres))
		{
			echo "<tr><td>".text2html($larr['message'])."</td>
							<td>".df($larr['timestamp'])."</td>
							<td><a href=\"?page=user&amp;sub=ipsearch&amp;ip=".$larr['host']."\">".$larr['host']."</a></td></tr>";
		}
	}
	tableEnd();

	echo "<h2>Neuer Log:</h2><textarea rows=\"4\" cols=\"70\" id=\"new_log\"></textarea><br/><br/>";

	echo "<input type=\"button\" onclick=\"xajax_addUserLog('$uid','$target',document.getElementById('new_log').value);\" value=\"Speichern\" />";


	$out = ob_get_contents();
	ob_end_clean();
	$or->assign($target,"innerHTML",$out);
	return $or;
}

function addUserLog($uid,$target,$text) {
	$or = new xajaxResponse();
	if ($text!="")
	{
		$or->script("showLoader('$target');");
		$user = new User($uid);
		$user->addToUserLog("settings",$text,1);
		$or->script("xajax_userLogs('$uid','$target')");
	}
	else
	{
		$or->alert("Fehler! Kein Text!");

	}
	return $or;
}

function loadEconomy($uid,$target)
{
	$or = new xajaxResponse();
	ob_start();

				// Stopt Ladedauer
				$tmr = timerStart();

				//
				// Rohstoff- und Produktionsübersicht
				//

				echo "<fieldset><legend>Rohstoff- und Produktionsübersicht</legend>";

				// Sucht alle Planet IDs des Users
				$pres = dbquery("
					SELECT
						id
					FROM
						planets
					WHERE
						planet_user_id='".$uid."'");
				if(mysql_num_rows($pres)>0)
				{

					// Läd alle "Planetclass" Daten in ein Array
					$planets = array();
					while($parr=mysql_fetch_row($pres))
					{
						$planets[] = Planet::getById($parr[0]);
					}

					$cnt_res=0;
					$max_res=array(0,0,0,0,0,0);
					$min_res=array(9999999999,9999999999,9999999999,9999999999,9999999999,9999999999);
					$tot_res=array(0,0,0,0,0,0);

					$cnt_prod=0;
					$max_prod=array(0,0,0,0,0,0);
					$min_prod=array(9999999999,9999999999,9999999999,9999999999,9999999999,9999999999);
					$tot_prod=array(0,0,0,0,0,0);
                    $val_res = [];
                    $val_prod = [];
                    $val_store = [];
                    $val_time = [];
					foreach ($planets as $p)
					{
						//Speichert die aktuellen Rohstoffe in ein Array
						$val_res[$p->id][0]=floor($p->resMetal);
						$val_res[$p->id][1]=floor($p->resCrystal);
						$val_res[$p->id][2]=floor($p->resPlastic);
						$val_res[$p->id][3]=floor($p->resFuel);
						$val_res[$p->id][4]=floor($p->resFood);
						$val_res[$p->id][5]=floor($p->people);

						for ($x=0;$x<6;$x++)
						{
							$max_res[$x]=max($max_res[$x],$val_res[$p->id][$x]);
							$min_res[$x]=min($min_res[$x],$val_res[$p->id][$x]);
							$tot_res[$x]+=$val_res[$p->id][$x];
						}

						//Speichert die aktuellen Rohstoffproduktionen in ein Array
						$val_prod[$p->id][0]=floor($p->prodMetal);
						$val_prod[$p->id][1]=floor($p->prodCrystal);
						$val_prod[$p->id][2]=floor($p->prodPlastic);
						$val_prod[$p->id][3]=floor($p->prodFuel);
						$val_prod[$p->id][4]=floor($p->prodFood);
						$val_prod[$p->id][5]=floor($p->prodPeople);

						for ($x=0;$x<6;$x++)
						{
							$max_prod[$x]=max($max_prod[$x],$val_prod[$p->id][$x]);
							$min_prod[$x]=min($min_prod[$x],$val_prod[$p->id][$x]);
							$tot_prod[$x]+=$val_prod[$p->id][$x];
						}

						//Speichert die aktuellen Speicher in ein Array
						$val_store[$p->id][0]=floor($p->storeMetal);
						$val_store[$p->id][1]=floor($p->storeCrystal);
						$val_store[$p->id][2]=floor($p->storePlastic);
						$val_store[$p->id][3]=floor($p->storeFuel);
						$val_store[$p->id][4]=floor($p->storeFood);
						$val_store[$p->id][5]=floor($p->people_place);

						//Berechnet die dauer bis die Speicher voll sind (zuerst prüfen ob Division By Zero!)

						//Titan
						if($p->prodMetal>0)
						{
				      if ($p->storeMetal - $p->resMetal > 0)
				      {
				      	$val_time[$p->id][0]=ceil(($p->storeMetal-$p->resMetal)/$p->prodMetal*3600);
				      }
				      else
				      {
				        $val_time[$p->id][0]=0;
				      }
				    }
				    else
				    {
				    	$val_time[$p->id][0]=0;
				    }

						//Silizium
						if($p->prodCrystal>0)
						{
				      if ($p->storeCrystal - $p->resCrystal > 0)
				      {
				      	$val_time[$p->id][1]=ceil(($p->storeCrystal-$p->resCrystal)/$p->prodCrystal*3600);
				      }
				      else
				      {
				      	$val_time[$p->id][1]=0;
				      }
				    }
				    else
				    {
				    	$val_time[$p->id][1]=0;
				    }

						//PVC
						if($p->prodPlastic>0)
						{
				      if ($p->storePlastic - $p->resPlastic > 0)
				      {
				        $val_time[$p->id][2]=ceil(($p->storePlastic-$p->resPlastic)/$p->prodPlastic*3600);
				      }
				      else
				      {
				      	$val_time[$p->id][2]=0;
				      }
				    }
				    else
				    {
				    	$val_time[$p->id][2]=0;
				    }

						//Tritium
						if($p->prodFuel>0)
						{
				      if ($p->storeFuel - $p->resFuel > 0)
				      {
				       	$val_time[$p->id][3]=ceil(($p->storeFuel-$p->resFuel)/$p->prodFuel*3600);
				      }
				      else
				      {
				      	$val_time[$p->id][3]=0;
				      }
				    }
				    else
				    {
				    	$val_time[$p->id][3]=0;
				    }

						//Nahrung
						if($p->prodFood>0)
						{
					    if ($p->storeFood - $p->resFood > 0)
					    {
					      $val_time[$p->id][4]=ceil(($p->storeFood-$p->resFood)/$p->prodFood*3600);
					    }
					    else
					   	{
					    	$val_time[$p->id][4]=0;
					    }
				    }
				    else
				    {
				    	$val_time[$p->id][4]=0;
				    }

						//Bewohner
						if($p->prodPeople>0)
						{
				      if ($p->people_place - $p->people > 0)
				      {
				        $val_time[$p->id][5]=ceil(($p->people_place-$p->people)/$p->prodPeople*3600);
				      }
				      else
				      {
				      	$val_time[$p->id][5]=0;
				      }
				    }
				    else
				    {
				    	$val_time[$p->id][5]=0;
				    }
					}


					//
					// Rohstoffe/Bewohner und Speicher
					//

					echo "<h2>Rohstoffe und Bewohner</h2>";
					echo "<table class=\"tbl\">";
					echo "<tr>
									<td class=\"tbltitle\">Name:</td>
									<td class=\"tbltitle\">".RES_METAL."</td>
									<td class=\"tbltitle\">".RES_CRYSTAL."</td>
									<td class=\"tbltitle\">".RES_PLASTIC."</td>
									<td class=\"tbltitle\">".RES_FUEL."</td>
									<td class=\"tbltitle\">".RES_FOOD."</td>
									<td class=\"tbltitle\">Bewohner</td>
								</tr>";
					foreach ($planets as $p)
					{
						echo "<tr>
										<td class=\"tbldata\">
											<a href=\"?page=galaxy&sub=edit&id=".$p->id()."\">".$p."</a>
										</td>";
						for ($x=0;$x<6;$x++)
						{
							echo "<td";
							if ($max_res[$x]==$val_res[$p->id][$x])
							{
								echo " class=\"tbldata3\"";
							}
							elseif ($min_res[$x]==$val_res[$p->id][$x])
							{
								 echo " class=\"tbldata2\"";
							}
							else
							{
								 echo " class=\"tbldata\"";
							}


							//Der Speicher ist noch nicht gefüllt
							if($val_res[$p->id][$x]<$val_store[$p->id][$x] && $val_time[$p->id][$x]!=0)
							{
								echo " ".tm("Speicher","Speicher voll in ".tf($val_time[$p->id][$x])."")." ";
								if ($val_time[$p->id][$x]<43200)
								{
									echo " style=\"font-style:italic;\" ";
								}
								echo ">".nf($val_res[$p->id][$x])."</td>";
							}
							//Speicher Gefüllt
							else
							{
								echo " ".tm("Speicher","Speicher voll!")."";
								echo " style=\"\" ";
								echo "><b>".nf($val_res[$p->id][$x])."</b></td>";
							}

						}
						echo "</tr>";
						$cnt_res++;
					}
					echo "<tr>
									<td colspan=\"6\"></td>
								</tr>
								<tr>
									<td class=\"tbltitle\">Total</td>";
					for ($x=0;$x<6;$x++)
					{
						echo "<td class=\"tbltitle\">".nf($tot_res[$x])."</td>";
					}
					echo "</tr><tr><th class=\"tbltitle\">Durchschnitt</th>";
					for ($x=0;$x<6;$x++)
					{
						echo "<td class=\"tbltitle\">".nf($tot_res[$x]/$cnt_res)."</td>";
					}
					echo "</tr>";
					echo "</table>";



					//
					// Rohstoffproduktion inkl. Energie
					//

					// Ersetzt Bewohnerwerte durch Energiewerte
					$max_prod[5] = 0;
					$min_prod[5] = 9999999999;
					$tot_prod[5] = 0;
					foreach ($planets as $p)
					{
						//Speichert die aktuellen Energieproduktionen in ein Array (Bewohnerproduktion [5] wird überschrieben)
						$val_prod[$p->id][5]=floor($p->prodPower);

						// Gibt Min. / Max. aus
						$max_prod[5]=max($max_prod[5],$val_prod[$p->id][5]);
						$min_prod[5]=min($min_prod[5],$val_prod[$p->id][5]);
						$tot_prod[5]+=$val_prod[$p->id][5];
					}

					echo "<p>Legende: Minimum, Maximum,
						<span style=\"font-style:italic\">Speicher bald voll</span>,
						<span class=\"tbldata\" style=\"font-weight:bold\">Speicher voll</span>
					</p>";


					echo "<h2>Produktion</h2>";
					echo "<table class=\"tbl\">";
					echo "<tr><th class=\"tbltitle\">Name:</th>
					<th class=\"tbltitle\">".RES_METAL."</th>
					<th class=\"tbltitle\">".RES_CRYSTAL."</th>
					<th class=\"tbltitle\">".RES_PLASTIC."</th>
					<th class=\"tbltitle\">".RES_FUEL."</th>
					<th class=\"tbltitle\">".RES_FOOD."</th>
					<th class=\"tbltitle\">Energie</th></tr>";
					foreach ($planets as $p)
					{
						echo "<tr><td class=\"tbldata\"><a href=\"?page=galaxy&amp;sub=edit&amp;id=".$p->id()."\">".$p."</a></td>";
						for ($x=0;$x<6;$x++)
						{
/*
							// Erstellt TM-Box für jeden Rohstoff
							// Titan
							if($x == 0)
							{
								$tm_header = "Titan-Bonis";
								$tm = "".$arr['race_name'].": ".$arr['race_f_metal']."<br\>".$p->type->name.": ".$p->type->metal."<br\>".$p->sol_type_name.": ".$p->sol->type->metal."";
							}
							elseif($x == 1)
							{
								$tm_header = "Silizium-Bonis";
								$tm = "".$arr['race_name'].": ".$arr['race_f_crystal']."<br\>".$p->type->name.": ".$p->type->crystal."<br\>".$p->sol_type_name.": ".$p->sol->type->crystal."";
							}
							elseif($x == 2)
							{
								$tm_header = "PVC-Bonis";
								$tm = "".$arr['race_name'].": ".$arr['race_f_plastic']."<br\>".$p->type->name.": ".$p->type->plastic."<br\>".$p->sol_type_name.": ".$p->sol->type->plastic."";
							}
							elseif($x == 3)
							{
								$tm_header = "Tritium-Bonis";
								$tm = "".$arr['race_name'].": ".$arr['race_f_fuel']."<br\>".$p->type->name.": ".$p->type->fuel."<br\>".$p->sol_type_name.": ".$p->sol->type->fuel."";
							}
							elseif($x == 4)
							{
								$tm_header = "Nahrungs-Bonis";
								$tm = "".$arr['race_name'].": ".$arr['race_f_food']."<br\>".$p->type->name.": ".$p->type->food."<br\>".$p->sol_type_name.": ".$p->sol->type->food."";
							}
							elseif($x == 5)
							{
								$tm_header = "Energie-Bonis";
								$tm = "".$arr['race_name'].": ".$arr['race_f_power']."<br\>".$p->type->name.": ".$p->type->power."<br\>".$p->sol_type_name.": ".$p->sol->type->power."";
							}
							else
							{
								$tm_header = "";
								$tm = "";
							}
							*/
								$tm_header = "";
								$tm = "";

							echo "<td";
							if ($max_prod[$x]==$val_prod[$p->id][$x])
							{
								echo " class=\"tbldata3\"";
							}
							elseif ($min_prod[$x]==$val_prod[$p->id][$x])
							{
								 echo " class=\"tbldata2\"";
							}
							else
							{
								 echo " class=\"tbldata\"";
							}
							echo " ".tm($tm_header,$tm).">".nf($val_prod[$p->id][$x])."</td>";
						}
						echo "</tr>";
						$cnt_prod++;
					}
					echo "<tr><td colspan=\"6\"></td></tr>";
					echo "<tr><th class=\"tbltitle\">Total</th>";
					for ($x=0;$x<6;$x++)
						echo "<td class=\"tbltitle\">".nf($tot_prod[$x])."</td>";
					echo "</tr><tr><th class=\"tbltitle\">Durchschnitt</th>";
					for ($x=0;$x<6;$x++)
						echo "<td class=\"tbltitle\">".nf($tot_prod[$x]/$cnt_prod)."</td>";
					echo "</tr>";
					echo "</table>";

				}
				else
				{
					echo "Der User hat noch keinen Planeten!";
				}
				echo "</fieldset>";


				echo "<fieldset><legend>Die fünf letzten Aufträge</legend>";


				//
				// 5 letzte Bauaufträge
				//

				echo "<h2>Bauaufträge</h2>";

				$lbres = dbquery("
				SELECT
					log.id,
					log.severity,
					log.object_id,
					log.message,
					log.status,
					log.level,
					log.entity_id,
					log.ip,
					log.timestamp
				FROM
					logs_game AS log
				WHERE
					log.facility='".GameLog::F_BUILD."'
					AND log.user_id='".$uid."'
				ORDER BY
					log.timestamp DESC
				LIMIT
					5;");
				if(mysql_num_rows($lbres)>0)
				{
					tableStart();
					echo "<tr>
						<th style=\"width:140px;\">Datum</th>
						<th style=\"\">Schweregrad</th>
						<th>Raumobjekt</th>
						<th>Einheit</th>
						<th>Status</th>
						<th>Optionen</th>
					</tr>";

					while ($lbarr = mysql_fetch_array($lbres))
					{
						$te = ($lbarr['entity_id']>0) ? Entity::createFactoryById($lbarr['entity_id']) : "-";
						$ob = new Building($lbarr['object_id'])." ".($lbarr['level']>0 ? $lbarr['level'] : '');
						switch ($lbarr['status'])
						{
							case 1: $obStatus="Ausbau abgebrochen";break;
							case 2: $obStatus="Abriss abgebrochen";break;
							case 3: $obStatus="Ausbau";break;
							case 4: $obStatus="Abriss";break;
							default: $obStatus='-';
						}

						echo "<tr>
						<td>".df($lbarr['timestamp'])."</td>
						<td>".GameLog::$severities[$lbarr['severity']]."</td>
						<td>".$te."</td>
						<td>".$ob."</td>
						<td>".$obStatus."</td>
						<td><a href=\"javascript:;\" onclick=\"toggleBox('details".$lbarr['id']."')\">Details</a></td>
						</tr>";
						echo "<tr id=\"details".$lbarr['id']."\" style=\"display:none;\"><td colspan=\"9\">".text2html($lbarr['message'])."
						<br/><br/>IP: ".$lbarr['ip']."</td></tr>";

					}

					tableEnd();
				}
				else
				{
					echo "Es sind keine Logs vorhanden!";
				}


				//
				// 5 letzte Forschungsaufträge
				//

				echo "<h2>Forschungsaufträge</h2>";

				$lres = dbquery("
				SELECT
					log.id,
					log.severity,
					log.object_id,
					log.message,
					log.status,
					log.level,
					log.entity_id,
					log.ip,
					log.timestamp
				FROM
					logs_game AS log
				WHERE
					log.facility='".GameLog::F_TECH."'
					AND log.user_id='".$uid."'
				ORDER BY
					log.timestamp DESC
				LIMIT
					5;");
				if(mysql_num_rows($lres)>0)
				{
					tableStart();
					echo "<tr>
						<th style=\"width:140px;\">Datum</th>
						<th style=\"\">Schweregrad</th>
						<th>Raumobjekt</th>
						<th>Einheit</th>
						<th>Status</th>
						<th>Optionen</th>
					</tr>";

					while ($larr = mysql_fetch_array($lres))
					{
						$te = ($larr['entity_id']>0) ? Entity::createFactoryById($larr['entity_id']) : "-";
						$ob = new Technology($larr['object_id'])." ".($larr['level']>0 ? $larr['level'] : '');
						switch ($larr['status'])
						{
							case 3: $obStatus="Erforschung";break;
							case 0: $obStatus="Erforschung abgebrochen";break;
							default: $obStatus='-';
						}

						echo "<tr>
						<td>".df($larr['timestamp'])."</td>
						<td>".GameLog::$severities[$larr['severity']]."</td>
						<td>".$te."</td>
						<td>".$ob."</td>
						<td>".$obStatus."</td>
						<td><a href=\"javascript:;\" onclick=\"toggleBox('details".$larr['id']."')\">Details</a></td>
						</tr>";
						echo "<tr id=\"details".$larr['id']."\" style=\"display:none;\"><td colspan=\"9\">".text2html($larr['message'])."
						<br/><br/>IP: ".$larr['ip']."</td></tr>";
					}

					tableEnd();
				}
				else
				{
					echo "Es sind keine Logs vorhanden!";
				}


				//
				// 5 letzte Schiffsaufträge
				//

				echo "<h2>Schiffsaufträge</h2>";

				$lres = dbquery("
				SELECT
					log.id,
					log.severity,
					log.object_id,
					log.message,
					log.status,
					log.level,
					log.entity_id,
					log.ip,
					log.timestamp
				FROM
					logs_game AS log
				WHERE
					log.facility='".GameLog::F_SHIP."'
					AND log.user_id='".$uid."'
				ORDER BY
					log.timestamp DESC
				LIMIT
					5;");

				if(mysql_num_rows($lres)>0)
				{
					tableStart();
					echo "<tr>
						<th style=\"width:140px;\">Datum</th>
						<th style=\"\">Schweregrad</th>
						<th>Raumobjekt</th>
						<th>Einheit</th>
						<th>Status</th>
						<th>Optionen</th>
					</tr>";

					while ($larr = mysql_fetch_array($lres))
					{
						$te = ($larr['entity_id']>0) ? Entity::createFactoryById($larr['entity_id']) : "-";
						$ob = $larr['object_id'] > 0 ? new Ship($larr['object_id']).' '.($larr['level']>0 ? $larr['level'].'x' : '') : '-';
						switch ($larr['status'])
						{
							case 1: $obStatus="Bau";break;
							case 0: $obStatus="Bau abgebrochen";break;
							default: $obStatus='-';
						}

						echo "<tr>
						<td>".df($larr['timestamp'])."</td>
						<td>".GameLog::$severities[$larr['severity']]."</td>
						<td>".$te."</td>
						<td>".$ob."</td>
						<td>".$obStatus."</td>
						<td><a href=\"javascript:;\" onclick=\"toggleBox('details".$larr['id']."')\">Details</a></td>
						</tr>";
						echo "<tr id=\"details".$larr['id']."\" style=\"display:none;\"><td colspan=\"9\">".text2html($larr['message'])."
						<br/><br/>IP: ".$larr['ip']."</td></tr>";
					}

					tableEnd();
				}
				else
				{
					echo "Es sind keine Logs vorhanden!";
				}



				//
				// 5 letzte Verteidigungsaufträge
				//

				echo "<h2>Verteidigungsaufträge</h2>";

				$lres = dbquery("
				SELECT
					log.id,
					log.severity,
					log.object_id,
					log.message,
					log.status,
					log.level,
					log.entity_id,
					log.ip,
					log.timestamp
				FROM
					logs_game AS log
				WHERE
					log.facility='".GameLog::F_DEF."'
					AND log.user_id='".$uid."'
				ORDER BY
					log.timestamp DESC
				LIMIT
					5;");
				if(mysql_num_rows($lres)>0)
				{
					tableStart();
					echo "<tr>
						<th style=\"width:140px;\">Datum</th>
						<th style=\"\">Schweregrad</th>
						<th>Raumobjekt</th>
						<th>Einheit</th>
						<th>Status</th>
						<th>Optionen</th>
					</tr>";

					while ($larr = mysql_fetch_array($lres))
					{
						$te = ($larr['entity_id']>0) ? Entity::createFactoryById($larr['entity_id']) : "-";
						$ob = $larr['object_id'] > 0 ? new Defense($larr['object_id']).' '.($larr['level']>0 ? $larr['level'].'x' : '') : '-';
						switch ($larr['status'])
						{
							case 1: $obStatus="Bau";break;
							case 0: $obStatus="Bau abgebrochen";break;
							default: $obStatus='-';
						}

						echo "<tr>
						<td>".df($larr['timestamp'])."</td>
						<td>".GameLog::$severities[$larr['severity']]."</td>
						<td>".$te."</td>
						<td>".$ob."</td>
						<td>".$obStatus."</td>
						<td><a href=\"javascript:;\" onclick=\"toggleBox('details".$larr['id']."')\">Details</a></td>
						</tr>";
						echo "<tr id=\"details".$larr['id']."\" style=\"display:none;\"><td colspan=\"9\">".text2html($larr['message'])."
						<br/><br/>IP: ".$larr['ip']."</td></tr>";
					}

					tableEnd();
				}
				else
				{
					echo "Es sind keine Logs vorhanden!";
				}
				echo "</fieldset>";

				echo "<p>Wirtschaftsseite geladen in ".timerStop($tmr)." sec <input type=\"button\" value=\"Wirtschaftsdaten neu laden\" onclick=\"showLoader('tabEconomy');xajax_loadEconomy(".$uid.",'tabEconomy');\" /></p>";

	$out = ob_get_contents();
	ob_end_clean();
	$or->assign($target,"innerHTML",$out);
	return $or;
}



?>
