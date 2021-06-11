<?PHP
	echo '<h2>Clean-Up</h2>';

	/* Proces actions */
	if (isset($_POST['submit_cleanup_selected']) || isset($_POST['submit_cleanup_all']))
	{
		echo "Clean-Up wird durchgeführt...<br/>";
		$all = isset($_POST['submit_cleanup_all']) ? true : false;

		// Log cleanup
		if (isset($_POST['cl_log']) || $all)
		{
			$nr = Log::removeOld($_POST['log_timestamp']);
			echo $nr." Logs wurden gelöscht!<br/>";
		}

		// Session-Log cleanup
		if ((isset($_POST['cl_sesslog']) && $_POST['cl_sesslog']==1) || $all)
		{
			$nr = UserSession::cleanupLogs($_POST['sess_log_timestamp']);
			$nr += AdminSession::cleanupLogs($_POST['sess_log_timestamp']);
			echo $nr." Session-Logs wurden gelöscht!<br/>";
		}


		/* Message cleanup */
		if ((isset($_POST['cl_msg']) && $_POST['cl_msg']==1) || $all)
		{
			if ($_POST['only_deleted']==1)
				$nr = Message::removeOld($_POST['message_timestampd'],1);
			else
				$nr = Message::removeOld($_POST['message_timestamp']);
				echo $nr." Nachrichten wurden gelöscht!<br/>";

		}

		/* Reprots cleanup */
		if ((isset($_POST['cl_report']) && $_POST['cl_report']==1) || $all)
		{
			if ($_POST['only_deletedr']==1)
				$nr = Report::removeOld($_POST['report_timestampd'],1);
			else
				$nr = Report::removeOld($_POST['report_timestamp']);
				echo $nr." Berichte wurden gelöscht!<br/>";

		}

		// User-Point-History
		if ((isset($_POST['cl_points']) && $_POST['cl_points']==1) || $all)
		{
			$nr = Users::cleanUpPoints($_POST['del_user_points']);
			echo $nr." Benutzerpunkte-Logs und ";
			$nr = Alliance::cleanUpPoints($_POST['del_user_points']);
			echo $nr." Allianzpunkte-Logs wurden gelöscht!<br/>";
		}

		// Inactive and delete jobs
		if ((isset($_POST['cl_inactive']) && $_POST['cl_inactive']==1) || $all)
		{
			$num = Users::removeInactive(true);
			echo $num." inaktive User wurden gelöscht!<br/>";
			$num = Users::removeDeleted(true);
			echo $num." gelöschte User wurden endgültig gelöscht!<br/>";
		}

		//Observer
		if ((isset($_POST['cl_surveillance']) && $_POST['cl_surveillance']==1) || $all)
		{
			$num = 0;
			$ores =	dbquery("SELECT
								`user_surveillance`.user_id
							FROM
								`user_surveillance`
							INNER JOIN
								`users`
							ON
								`user_surveillance`.user_id=`users`.user_id
								AND `users`.user_observe IS NULL
							GROUP BY
								`user_surveillance`.user_id;");
			while ($oarr = mysql_fetch_row($ores))
			{
				dbquery("DELETE FROM
							`user_surveillance`
						WHERE
							user_id='".$oarr[0]."';");
				$num += mysql_affected_rows();
			}
			echo $num." verwaiste Beobachtereinträge gelöscht<br/>";
		}

		// Userdata
		if ((isset($_POST['cl_userdata']) && $_POST['cl_userdata']==1) || $all)
		{
			$ures = dbquery("SELECT
								user_id
							FROM
								`users`;");
			$ustring = "";
			$set = false;
			while ($uarr = mysql_fetch_row($ures))
			{
				if  ($set) $ustring .=",";
				else $set = true;
				$ustring .= $uarr[0];
			}
			if (empty($ustring)) {
				$ustring="0";
			}

			if ($_POST['del_user_log']==1)
			{
				$lres = dbquery("DELETE	FROM
									`user_log`
								WHERE
									!(`user_log`.user_id IN (".$ustring."))");
				echo mysql_affected_rows()." verwaiste Userlogs wurden gelöscht!<br/>";
			}
			if ($_POST['del_user_ratings']==1)
			{
				$rres = dbquery("DELETE	FROM
									`user_ratings`
								WHERE
									!(`user_ratings`.id IN (".$ustring."))");
				echo mysql_affected_rows()." verwaiste Ratings wurden gelöscht!<br/>";
			}
			if ($_POST['del_user_properties']==1)
			{
				$pres = dbquery("DELETE FROM
									`user_properties`
								WHERE
									!(`user_properties`.id IN (".$ustring."))");
				echo mysql_affected_rows()." verwaiste Properties wurden gelöscht!<br/>";
			}
			if ($_POST['del_user_multi']==1)
			{
				$mres = dbquery("DELETE FROM
									`user_multi`
								WHERE
									!(`user_multi`.user_id IN (".$ustring."))
									OR !(`user_multi`.multi_id IN (".$ustring."))");
				echo mysql_affected_rows()." verwaiste Multieinträge wurden gelöscht!<br/>";
			}
			if ($_POST['del_user_comments']==1)
			{
				$cres = dbquery("DELETE FROM
									`user_comments`
								WHERE
									!(`user_comments`.comment_user_id IN (".$ustring."))");
				echo mysql_affected_rows()." verwaiste Adminkommentare wurden gelöscht!<br/>";
			}
			if ($_POST['del_tickets']==1)
			{
				$res = dbquery("
						SELECT
							id
						FROM
							`tickets`
						WHERE
							!(`tickets`.user_id IN (".$ustring."))");
				$tstring = "";
				$set = false;
				while ($uarr = mysql_fetch_row($res))
				{
					if  ($set) $tstring .=",";
					else $set = true;
					$tstring .= $uarr[0];
				}
				dbquery("DELETE FROM
							`ticket_msg`
						WHERE
							`ticket_id` IN (".$tstring.")");

				$tres = dbquery("DELETE FROM
									`tickets`
								WHERE
									!(`tickets`.user_id IN (".$ustring."))");
				echo mysql_affected_rows()." verwaiste Tickets wurden gelöscht!<br/>";
			}
			if ($_POST['del_reports']==1)
			{
				$res = dbquery("
						SELECT
							id,
							type
						FROM
							`reports`
						WHERE
							!(`reports`.user_id IN (".$ustring."))");
				if (mysql_num_rows($res)>0)
				{
					$battle = array();
					$spy = array();
					$market = array();
					$other = array();
					$counter = 0;

					while ($arr=mysql_fetch_row($res))
					{
						switch ($arr[1]) {
							case 'battle':
								array_push($battle,$arr[0]);
								break;
							case 'spy':
								array_push($spy,$arr[0]);
								break;
							case 'market':
								array_push($market,$arr[0]);
								break;
							case 'other':
								array_push($other,$arr[0]);
								break;
						}

						if ($counter > 10000) {
							if (count($battle)>0)
								dbquery("
									DELETE FROM
										reports_battle
									WHERE
										id IN (".implode(",", $battle).");");

							if (count($market)>0)
								dbquery("
									DELETE FROM
										reports_market
									WHERE
										id IN (".implode(",", $market).");");

							if (count($spy)>0)
								dbquery("
									DELETE FROM
										reports_spy
									WHERE
										id IN (".implode(",", $spy).");");

							if (count($other)>0)
								dbquery("
									DELETE FROM
										reports_other
									WHERE
										id IN (".implode(",", $other).");");

							$battle = array();
							$spy = array();
							$market = array();
							$other = array();
							$counter = 0;
						}
						else $counter++;

					}

					if (count($battle)>0)
						dbquery("
							DELETE FROM
								reports_battle
							WHERE
								id IN (".implode(",", $battle).");");

					if (count($market)>0)
						dbquery("
							DELETE FROM
								reports_market
							WHERE
								id IN (".implode(",", $market).");");

					if (count($spy)>0)
						dbquery("
							DELETE FROM
								reports_spy
							WHERE
								id IN (".implode(",", $spy).");");

					if (count($other)>0)
						dbquery("
							DELETE FROM
								reports_other
							WHERE
								id IN (".implode(",", $other).");");
				}
				$tres = dbquery("DELETE FROM
									`reports`
								WHERE
									!(`reports`.user_id IN (".$ustring."))");
				echo mysql_affected_rows()." verwaiste Berichte wurden gelöscht!<br/>";
			}
			if ($_POST['del_notepad']==1)
			{
				$nres = dbquery("DELETE FROM
									`notepad`
								WHERE
									!(`notepad`.user_id IN (".$ustring."))");
				echo mysql_affected_rows()." verwaiste Notizen wurden gelöscht!<br/>";
			}
			if ($_POST['del_shiplist']==1)
			{
				$slres = dbquery("DELETE FROM
									`shiplist`
								WHERE
									!(`shiplist`.shiplist_user_id IN (".$ustring."))");
				echo mysql_affected_rows()." verwaiste Schiffe wurden gelöscht!<br/>";
			}
			if ($_POST['del_deflist']==1)
			{
				$dlres = dbquery("DELETE FROM
									`deflist`
								WHERE
									!(`deflist`.deflist_user_id IN (".$ustring."))");
				echo mysql_affected_rows()." verwaiste Verteidigungen wurden gelöscht!<br/>";
			}
			if ($_POST['del_missilelist']==1)
			{
				$dlres = dbquery("DELETE FROM
									`missilelist`
								WHERE
									!(`missilelist`.missilelist_user_id IN (".$ustring."))");
				echo mysql_affected_rows()." verwaiste Raketen wurden gelöscht!<br/>";
			}
			if ($_POST['del_buildlist']==1)
			{
				$blres = dbquery("DELETE FROM
									`buildlist`
								WHERE
									!(`buildlist`.buildlist_user_id IN (".$ustring."))");
				echo mysql_affected_rows()." verwaiste Gebäude wurden gelöscht!<br/>";
			}
			if ($_POST['del_techlist']==1)
			{
				$tlres = dbquery("DELETE FROM
									`techlist`
								WHERE
									!(`techlist`.techlist_user_id IN (".$ustring."))");
				echo mysql_affected_rows()." verwaiste Technologien wurden gelöscht!<br/>";
			}
			if ($_POST['del_def_queue']==1)
			{
				$dqres = dbquery("DELETE FROM
									`def_queue`
								WHERE
									!(`def_queue`.queue_user_id IN (".$ustring."))");
				echo mysql_affected_rows()." verwaiste Bauaufträge (Def) wurden gelöscht!<br/>";
			}
			if ($_POST['del_ship_queue']==1)
			{
				$sqres = dbquery("DELETE FROM
									`ship_queue`
								WHERE
									!(`ship_queue`.queue_user_id IN (".$ustring."))");
				echo mysql_affected_rows()." verwaiste Bauaufträge (Schiff) wurden gelöscht!<br/>";
			}
		}

		/* object lists */
		if ((isset($_POST['cl_objlist']) && $_POST['cl_objlist']==1) || $all)
		{
			$nr = ShipList::cleanUp();
			echo $nr." leere Schiffdaten wurden gelöscht!<br/>";
			$nr = DefList::cleanUp();
			echo $nr." leere Verteidigungsdaten wurden gelöscht!<br/>";
			dbquery("
			DELETE FROM
				buildlist
			WHERE
				buildlist_current_level=0
				AND buildlist_build_start_time=0
				AND buildlist_build_end_time=0
			;");
			echo mysql_affected_rows()." leere Gebäudedaten wurden gelöscht!<br/>";
			dbquery("
			DELETE FROM
				techlist
			WHERE
				techlist_current_level=0
				AND techlist_build_start_time=0
				AND techlist_build_end_time=0
			;");
			echo mysql_affected_rows()." leere Forschungsdaten wurden gelöscht!<br/>";
		}

		echo "Clean-Up fertig!<br/><br/>";
	}

	echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";

	/* Messages */
	echo '<fieldset><legend><input type="checkbox" value="1" name="cl_msg" /> Nachrichten</legend>';
	$tblcnt = mysql_fetch_row(dbquery("
	SELECT
		COUNT(message_id)
	FROM
		messages
	WHERE
		message_archived=0
	;"));
	echo '<input type="radio" name="only_deleted" value="0" /><b>Nachrichten löschen:</b> ';
	echo "Älter als <select name=\"message_timestamp\">";
	$days = array(1,7,14,21,28);
	if (!in_array($cfg->get('messages_threshold_days'),$days))
		$days[] = $cfg->get('messages_threshold_days');
	sort($days);
	foreach ($days as $ds)
	{
		echo "<option value=\"".(24*3600*$ds)."\" ".($ds==$cfg->get('messages_threshold_days')  ? " selected=\"selected\"" : "").">".$ds." Tage</option>";
	}
	echo "</select> (".nf($tblcnt[0])." total).<br/>";
	$tblcnt = mysql_fetch_row(dbquery("
	SELECT
		COUNT(message_id)
	FROM
		messages
	WHERE
		message_deleted=1
	;"));
	echo '<input type="radio" name="only_deleted" value="1" checked="checked" /> <b>Nur \'gelöschte\' Nachrichten löschen:</b> ';
	echo 'Älter als <select name="message_timestampd">';
	$days = array(7,14,21,28);
	if (!in_array($cfg->p1('messages_threshold_days'),$days))
		$days[] = $cfg->p1('messages_threshold_days');
	sort($days);
	foreach ($days as $ds)
	{
		echo "<option value=\"".(24*3600*$ds)."\" ".($ds==$cfg->p1('messages_threshold_days')  ? " selected=\"selected\"" : "").">".$ds." Tage</option>";
	}
	echo "</select> (".nf($tblcnt[0])." total).";
	echo '</fieldset><br/>';

	/* Reports */
	echo '<fieldset><legend><input type="checkbox" value="1" name="cl_report" /> Berichte</legend>';
	$tblcnt = mysql_fetch_row(dbquery("
	SELECT
		COUNT(id)
	FROM
		reports
	WHERE
		archived=0
	;"));
	echo '<input type="radio" name="only_deletedr" value="0" /><b>Berichte löschen:</b> ';
	echo "Älter als <select name=\"repor_timestamp\">";
	$days = array(1,7,14,21,28);
	if (!in_array($cfg->get('report_threshold_days'),$days))
		$days[] = $cfg->get('report_threshold_days');
	sort($days);
	foreach ($days as $ds)
	{
		echo "<option value=\"".(24*3600*$ds)."\" ".($ds==$cfg->get('rerport_threshold_days')  ? " selected=\"selected\"" : "").">".$ds." Tage</option>";
	}
	echo "</select> (".nf($tblcnt[0])." total).<br/>";
	$tblcnt = mysql_fetch_row(dbquery("
	SELECT
		COUNT(id)
	FROM
		reports
	WHERE
		deleted=1
	;"));
	echo '<input type="radio" name="only_deletedr" value="1" checked="checked" /> <b>Nur \'gelöschte\' Berichte löschen:</b> ';
	echo 'Älter als <select name="message_timestampd">';
	$days = array(7,14,21,28);
	if (!in_array($cfg->p1('reports_threshold_days'),$days))
		$days[] = $cfg->p1('reports_threshold_days');
	sort($days);
	foreach ($days as $ds)
	{
		echo "<option value=\"".(24*3600*$ds)."\" ".($ds==$cfg->p1('reports_threshold_days')  ? " selected=\"selected\"" : "").">".$ds." Tage</option>";
	}
	echo "</select> (".nf($tblcnt[0])." total).";
	echo '</fieldset><br/>';

	// Logs
	echo '<fieldset><legend><input type="checkbox" value="1" name="cl_log" /> Logs</legend>';
	$tblcnt = mysql_fetch_row(dbquery("
	SELECT
		count(id)
	FROM
		logs
	;"));
	echo "<b>Logs löschen:</b> Einträge löschen welche &auml;lter als <select name=\"log_timestamp\">";
	$days = array(7,14,21,28);
	if (!in_array($cfg->get('log_threshold_days'),$days))
		$days[] = $cfg->get('log_threshold_days');
	sort($days);
	foreach ($days as $ds)
	{
		echo "<option value=\"".(24*3600*$ds)."\" ".($ds==$cfg->get('log_threshold_days')  ? " selected=\"selected\"" : "").">".$ds." Tage</option>";
	}
	echo "</select> sind (".nf($tblcnt[0])." total).";
	echo '</fieldset><br/>';

	// User-Sessions
	echo '<fieldset><legend><input type="checkbox" value="1" name="cl_sesslog" /> Session-Logs</legend>';
	$tblcnt = mysql_fetch_row(dbquery("
	SELECT
		COUNT(*)
	FROM
		user_sessionlog
	;"));
	echo "<b>Session-Logs löschen:</b> ";
	echo "Eintr&auml;ge löschen die &auml;lter als <select name=\"sess_log_timestamp\">";
	$days = array(7,14,21,28);
	if (!in_array($cfg->get('log_threshold_days'),$days))
		$days[] = $cfg->get('log_threshold_days');
	sort($days);
	foreach ($days as $ds)
	{
		echo "<option value=\"".(24*3600*$ds)."\" ".($ds==$cfg->get('log_threshold_days')  ? " selected=\"selected\"" : "").">".$ds." Tage</option>";
	}
	echo "</select> sind (".nf($tblcnt[0])." total).";
	echo '</fieldset><br/>';

	// User-Points
	echo '<fieldset><legend><input type="checkbox" value="1" name="cl_points" /> Punkteverlauf</legend>';
	$tblcnt = mysql_fetch_row(dbquery("
	SELECT
		COUNT(*)
	FROM
		user_points
	;"));
	echo "<b>Punkteverläufe löschen:</b> Eintr&auml;ge löschen die &auml;lter als <select name=\"del_user_points\">";
	$days = array(2,5,7,14,21,28);
	if (!in_array($cfg->get('log_threshold_days'),$days))
		$days[] = $cfg->get('log_threshold_days');
	sort($days);
	foreach ($days as $ds)
	{
		echo "<option value=\"".(24*3600*$ds)."\" ".($ds==$cfg->get('log_threshold_days')  ? " selected=\"selected\"" : "").">".$ds." Tage</option>";
	}
	echo "</select> sind (Total: ".nf($tblcnt[0])." User,";
	$tblcnt = mysql_fetch_row(dbquery("
	SELECT
		COUNT(*)
	FROM
		alliance_points
	;"));
	echo " ".nf($tblcnt[0])." Allianz).";
	echo '</fieldset><br/>';

	// Inactive
	echo '<fieldset><legend><input type="checkbox" value="1" name="cl_inactive" /> User</legend>';
	echo nf(Users::getNumInactive())." inaktive Benutzer löschen (".$conf['user_inactive_days']['p2']." Tage seit der Registration ohne Login oder ".$conf['user_inactive_days']['p1']." Tage nicht mehr eingeloggt)<br/>";
	$res =	dbquery("
		SELECT
			COUNT(user_id)
		FROM
			users
		WHERE
			user_deleted>0
			AND user_deleted<".time()."
	;");
	$tblcnt = mysql_fetch_row($res);
	echo nf($tblcnt[0])." als gelöscht markierte Benutzer endgültig löschen";

	echo '</fieldset><br/>';

	// Beobachter
	echo '<fieldset><legend><input type="checkbox" value="1" name="cl_surveillance" /> Beobachter</legend>';
	$ores =	dbquery("SELECT
						count(`user_surveillance`.user_id)
					FROM
						`user_surveillance`
					INNER JOIN
						`users`
					ON
						`user_surveillance`.user_id=`users`.user_id
						AND `users`.user_observe IS NULL
					GROUP BY
						`user_surveillance`.user_id;");
	$tblcnt = mysql_fetch_row($ores);
	echo nf($tblcnt[0])." verwaiste Beobachtereinträge gefunden";
	echo '</fieldset><br/>';

	// Userdata
	echo '<fieldset><legend><input type="checkbox" value="1" name="cl_userdata" /> Userdata von gelöschten Spielern</legend>';
	$ures = dbquery("SELECT
						user_id
					FROM
						`users`;");
	$ustring = "";
	$set = false;
	while ($uarr = mysql_fetch_row($ures))
	{
		if  ($set) $ustring .=",";
		else $set = true;
		$ustring .= $uarr[0];
	}
	if (empty($ustring)) {
		$ustring="0";
	}

	$lres = dbquery("SELECT
						count(`user_log`.id)
					FROM
						`user_log`
					WHERE
						!(`user_log`.user_id IN (".$ustring."))");
	$rres = dbquery("SELECT
						count(`user_ratings`.id)
					FROM
						`user_ratings`
					WHERE
						!(`user_ratings`.id IN (".$ustring."))");
	$pres = dbquery("SELECT
						count(`user_properties`.id)
					FROM
						`user_properties`
					WHERE
						!(`user_properties`.id IN (".$ustring."))");
	$mres = dbquery("SELECT
						count(`user_multi`.id)
					FROM
						`user_multi`
					WHERE
						!(`user_multi`.user_id IN (".$ustring."))
						OR !(`user_multi`.multi_id IN (".$ustring."))");
	$cres = dbquery("SELECT
						count(`user_comments`.comment_id)
					FROM
						`user_comments`
					WHERE
						!(`user_comments`.comment_user_id IN (".$ustring."))");
	$tres = dbquery("SELECT
						count(`tickets`.id)
					FROM
						`tickets`
					WHERE
						!(`tickets`.user_id IN (".$ustring."))");
	$reres = dbquery("SELECT
						count(`reports`.id)
					FROM
						`reports`
					WHERE
						!(`reports`.user_id IN (".$ustring."))");
	$nres = dbquery("SELECT
						count(`notepad`.id)
					FROM
						`notepad`
					WHERE
						!(`notepad`.user_id IN (".$ustring."))");
	$slres = dbquery("SELECT
						count(`shiplist`.shiplist_id)
					FROM
						`shiplist`
					WHERE
						!(`shiplist`.shiplist_user_id IN (".$ustring."))");
	$dlres = dbquery("SELECT
						count(`deflist`.deflist_id)
					FROM
						`deflist`
					WHERE
						!(`deflist`.deflist_user_id IN (".$ustring."))");
	$blres = dbquery("SELECT
						count(`buildlist`.buildlist_id)
					FROM
						`buildlist`
					WHERE
						!(`buildlist`.buildlist_user_id IN (".$ustring."))");
	$tlres = dbquery("SELECT
						count(`techlist`.techlist_id)
					FROM
						`techlist`
					WHERE
						!(`techlist`.techlist_user_id IN (".$ustring."))");
	$mlres = dbquery("SELECT
						count(`missilelist`.missilelist_id)
					FROM
						`missilelist`
					WHERE
						!(`missilelist`.missilelist_user_id IN (".$ustring."))");
	$dqres = dbquery("SELECT
						count(`def_queue`.queue_id)
					FROM
						`def_queue`
					WHERE
						!(`def_queue`.queue_user_id IN (".$ustring."))");
	$sqres = dbquery("SELECT
						count(`ship_queue`.queue_id)
					FROM
						`ship_queue`
					WHERE
						!(`ship_queue`.queue_user_id IN (".$ustring."))");

	$tblcnt = mysql_fetch_row($lres);
	echo '<input type="checkbox" value="1" name="del_user_log" /> '.nf($tblcnt[0])." verwaiste <strong>Userlogs</strong> gefunden<br/>";
	$tblcnt = mysql_fetch_row($rres);
	echo '<input type="checkbox" value="1" name="del_user_ratings" /> '.nf($tblcnt[0])." verwaiste <strong>Ratings</strong> gefunden<br/>";
	$tblcnt = mysql_fetch_row($pres);
	echo '<input type="checkbox" value="1" name="del_user_properties" /> '.nf($tblcnt[0])." verwaiste <strong>Properties</strong> gefunden<br/>";
	$tblcnt = mysql_fetch_row($mres);
	echo '<input type="checkbox" value="1" name="del_user_multi" /> '.nf($tblcnt[0])." verwaiste <strong>Multieinträge</strong> gefunden<br/>";
	$tblcnt = mysql_fetch_row($cres);
	echo '<input type="checkbox" value="1" name="del_user_comments" /> '.nf($tblcnt[0])." verwaiste <strong>Adminkommentare</strong> gefunden<br/>";
	$tblcnt = mysql_fetch_row($tres);
	echo '<input type="checkbox" value="1" name="del_tickets" /> '.nf($tblcnt[0])." verwaiste <strong>Tickets</strong> gefunden<br/>";
	$tblcnt = mysql_fetch_row($reres);
	echo '<input type="checkbox" value="1" name="del_reports" /> '.nf($tblcnt[0])." verwaiste <strong>Berichte</strong> gefunden<br/>";
	$tblcnt = mysql_fetch_row($nres);
	echo '<input type="checkbox" value="1" name="del_notepad" /> '.nf($tblcnt[0])." verwaiste <strong>Notizen</strong> gefunden<br/>";
	$tblcnt = mysql_fetch_row($slres);
	echo '<input type="checkbox" value="1" name="del_shiplist" /> '.nf($tblcnt[0])." verwaiste <strong>Schiffdatensätze</strong> gefunden<br/>";
	$tblcnt = mysql_fetch_row($sqres);
	echo '<input type="checkbox" value="1" name="del_ship_queue" /> '.nf($tblcnt[0])." verwaiste <strong>Schiffbauaufträge</strong> gefunden<br/>";
	$tblcnt = mysql_fetch_row($dlres);
	echo '<input type="checkbox" value="1" name="del_deflist" /> '.nf($tblcnt[0])." verwaiste <strong>Defdatensätze</strong> gefunden<br/>";
	$tblcnt = mysql_fetch_row($dqres);
	echo '<input type="checkbox" value="1" name="del_def_queue" /> '.nf($tblcnt[0])." verwaiste <strong>Defbauaufträge</strong> gefunden<br/>";
	$tblcnt = mysql_fetch_row($blres);
	echo '<input type="checkbox" value="1" name="del_buildlist" /> '.nf($tblcnt[0])." verwaiste <strong>Gebäude</strong> gefunden<br/>";
	$tblcnt = mysql_fetch_row($tlres);
	echo '<input type="checkbox" value="1" name="del_techlist" /> '.nf($tblcnt[0])." verwaiste <strong>Technologien</strong> gefunden<br/>";
	$tblcnt = mysql_fetch_row($mlres);
	echo '<input type="checkbox" value="1" name="del_missilelist" /> '.nf($tblcnt[0])." verwaiste <strong>Raketen</strong> gefunden<br/>";
	echo '</fieldset><br/>';

	/* Object lists */
	echo '<fieldset><legend><input type="checkbox" value="1" name="cl_objlist" /> Objektlisten</legend>';
	$res =	dbquery("
	SELECT
		 COUNT( shiplist_id )
	FROM
		shiplist
	WHERE
		shiplist_count =0
		AND shiplist_bunkered =0
		AND shiplist_special_ship=0
	;");
	$scnt = mysql_fetch_row($res);
	$res =	dbquery("
	SELECT
		 COUNT( shiplist_id )
	FROM
		shiplist
	;");
	$stcnt = mysql_fetch_row($res);

	$res =	dbquery("
	SELECT
		 COUNT( deflist_id )
	FROM
		deflist
	WHERE
		deflist_count =0
	;");
	$dcnt = mysql_fetch_row($res);
	$res =	dbquery("
	SELECT
		 COUNT( deflist_id )
	FROM
		deflist
	;");
	$dtcnt = mysql_fetch_row($res);

	$res =	dbquery("
	SELECT
		 COUNT( missilelist_id )
	FROM
		missilelist
	WHERE
		missilelist_count =0
	;");
	$mcnt = mysql_fetch_row($res);
	$res =	dbquery("
	SELECT
		 COUNT( missilelist_id )
	FROM
		missilelist
	;");
	$mtcnt = mysql_fetch_row($res);

	$res =	dbquery("
	SELECT
		 COUNT( buildlist_id )
	FROM
		buildlist
	WHERE
		buildlist_current_level=0
		AND buildlist_build_start_time=0
		AND buildlist_build_end_time=0
	;");
	$bcnt = mysql_fetch_row($res);
	$res =	dbquery("
	SELECT
		 COUNT( buildlist_id )
	FROM
		buildlist
	;");
	$btcnt = mysql_fetch_row($res);

	$res =	dbquery("
	SELECT
		 COUNT( techlist_id )
	FROM
		techlist
	WHERE
		techlist_current_level=0
		AND techlist_build_start_time=0
		AND techlist_build_end_time=0
	;");
	$tcnt = mysql_fetch_row($res);
	$res =	dbquery("
	SELECT
		 COUNT( techlist_id )
	FROM
		techlist
	;");
	$ttcnt = mysql_fetch_row($res);

	echo "<b>Leere Schiffdatensätze:</b> ".nf($scnt[0])." vorhanden (".nf($stcnt[0])." total).<br/>";
	echo "<b>Leere Verteidigungsdatensätze:</b> ".nf($dcnt[0])." vorhanden (".nf($dtcnt[0])." total).<br/>";
	echo "<b>Leere Raketendatensäte:</b> ".nf($mcnt[0])." vorhanden (".nf($mtcnt[0])." total).<br/>";
	echo "<b>Leere Gebäudedatensätze:</b> ".nf($bcnt[0])." vorhanden (".nf($btcnt[0])." total).<br/>";
	echo "<b>Leere Forschungsdatensätze:</b> ".nf($tcnt[0])." vorhanden (".nf($ttcnt[0])." total).<br/>";
	echo '</fieldset><br/>';

	echo '<input type="submit" name="submit_cleanup_selected" value="Selektiere ausführen" /> &nbsp; ';
	echo '<input type="submit" name="submit_cleanup_all" value="Alle ausführen" />';

	echo '</form>';
?>
