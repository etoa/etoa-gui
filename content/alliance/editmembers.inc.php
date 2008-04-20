<?PHP
if (Alliance::checkActionRights('editmembers'))
{


		echo "<h2>Allianzmitglieder</h2>";
		// Ränge laden
		$rres = dbquery("
		SELECT
			rank_name,
			rank_id
		FROM
			".$db_table['alliance_ranks']."
		WHERE
			rank_alliance_id=".$cu->alliance_id.";");
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
					if (mysql_num_rows(dbquery("SELECT user_id FROM users WHERE user_alliance_rank_id!='$rid' AND user_id='$uid';"))>0)
					{
						add_alliance_history($cu->alliance_id,"Der Spieler [b]".get_user_nick($uid)."[/b] bekommt den Rang [b]".$rank[$rid]."[/b].");
						dbquery("UPDATE users SET user_alliance_rank_id='$rid' WHERE user_id='$uid';");
					}
				}
				echo "&Auml;nderungen wurden übernommen!<br/><br/>";
			}
		}

		// Gründer wechseln
		if (isset($_GET['setfounder']) && $_GET['setfounder']>0 && $isFounder && $cu->id()!=$_GET['setfounder'])
		{
			$ures=dbquery("SELECT user_id FROM users WHERE user_alliance_id=".$arr['alliance_id']." AND user_id=".$_GET['setfounder'].";");
			if (mysql_num_rows($ures)>0)
			{
				dbquery("UPDATE alliances SET alliance_founder_id=".$_GET['setfounder']." WHERE alliance_id=".$arr['alliance_id'].";");
				$arr['alliance_founder_id']=$_GET['setfounder'];
				add_alliance_history($cu->alliance_id,"Der Spieler [b]".get_user_nick($_GET['setfounder'])."[/b] wird vom Spieler [b]".$cu->nick."[/b] zum Gründer befördert.");
				add_log(5,"Der Spieler [b]".get_user_nick($_GET['setfounder'])."[/b] wird vom Spieler [b]".$cu->nick."[/b] zum Gründer befördert.",time());
				send_msg($_GET['setfounder'],MSG_ALLYMAIL_CAT,"Gründer","Du hast nun die Gründerrechte deiner Allianz!");
				echo "Gründer ge&auml;ndert!<br/><br/>";
			}
			else
				echo "<b>Fehler!</b> User nicht gefunden!<br/><br/>";
		}

		// Mitglied kicken
		if (isset($_GET['kickuser']) && intval($_GET['kickuser'])>0 && checker_verify())
		{
			$ures = dbquery("
			SELECT
                        users.user_nick,
                        alliances.alliance_name,
                        alliances.alliance_tag,
                        alliances.alliance_id,
                        alliances.alliance_founder_id
			FROM
                        alliances
     	INNER JOIN
                        users
			ON
                        users.user_alliance_id=alliances.alliance_id
                        AND users.user_id=".intval($_GET['kickuser'])."
                        AND alliances.alliance_id='".$cu->alliance_id."';");
			if (mysql_num_rows($res))
			{
				$uarr = mysql_fetch_assoc($ures);
				echo "Der Spieler wurde aus der Allianz ausgeschlossen!<br/><br/>";
				dbquery("
				UPDATE 
					users 
				SET 
					user_alliance_rank_id=0,
					user_alliance_id=0 
				WHERE 
					user_alliance_id=".$arr['alliance_id']." 
					AND user_id='".intval($_GET['kickuser'])."';");
				send_msg(intval($_GET['kickuser']),MSG_ALLYMAIL_CAT,"Allianzausschluss","Du wurdest aus der Allianz ausgeschlossen!");
				add_alliance_history($cu->alliance_id,"Der Spieler [b]".$uarr['user_nick']."[/b] wurde von [b]".$cu->nick."[/b] aus der Allianz ausgeschlossen!");
				add_log(5,"Der Spieler [b]".$uarr['user_nick']."[/b] wurde von [b]".$cu->nick."[/b] aus der Allianz [b][".$arr['alliance_tag']."] ".$arr['alliance_name']."[/b] ausgeschlossen!",time());
			}
			else
			{
				echo "Der Spieler konnte nicht aus der Allianz ausgeschlossen werden, da er kein Mitglieder dieser Allianz ist!<br/><br/>";
			}
		}

		checker_init();
		echo "<table class=\"tbl\">";
		echo "<tr><td class=\"tbltitle\">Nick</td><td class=\"tbltitle\">Heimatplanet</td><td class=\"tbltitle\">Punkte</td><td class=\"tbltitle\">Rang</td><td class=\"tbltitle\">Angriffe</td><td class=\"tbltitle\">Online</td><td class=\"tbltitle\">Aktionen</td>";
		$ures = dbquery("
		SELECT 
			u.user_acttime,
			u.user_id,u.user_points,
			u.user_nick,
			p.id,
			u.user_alliance_rank_id 
		FROM 
			users AS u
		INNER JOIN
			planets AS p 
		ON 
			p.planet_user_id=u.user_id 
			AND u.user_alliance_id='".$cu->alliance_id."' 
			AND p.planet_user_main=1 
			GROUP BY u.user_id  
		ORDER BY 
			u.user_points DESC, 
			u.user_nick;");
		while ($uarr = mysql_fetch_assoc($ures))
		{
			$tp = new Planet($uarr['id']);
			echo "<tr>";
			// Nick, Planet, Punkte
			echo "<td class=\"tbldata\">".$uarr['user_nick']."</td>
			<td class=\"tbldata\">".$tp."</td>
			<td class=\"tbldata\">".nf($uarr['user_points'])."</td>";
			// Rang
			if ($uarr['user_id']==$arr['alliance_founder_id'])
				echo "<td class=\"tbldata\">Gründer</td>";
			else
			{
				echo "<td class=\"tbldata\"><select name=\"user_alliance_rank_id[".$uarr['user_id']."]\">";
				echo "<option value=\"0\">Rang w&auml;hlen...</option>";
				foreach ($rank as $id=>$name)
				{
					echo "<option value=\"$id\"";
					if ($uarr['user_alliance_rank_id']==$id) echo " selected=\"selected\"";
					echo ">".$name."</option>";
				}
				echo "</select></td>";
			}

      $num=check_fleet_incomming($uarr['user_id']);
      if ($num>0)
      {
          echo "<td style=\"color:#f00;\" align=\"center\">".$num."</td>";
      } else {
          echo "<td class=\"tbldata\">-</td>";
      }

			// Zuletzt online
			if ((time()-$conf['online_threshold']['v']*60) < $uarr['user_acttime'])
				echo "<td class=\"tbldata\" style=\"color:#0f0;\">online</td>";
			else
				echo "<td class=\"tbldata\">".date("d.m.Y H:i",$uarr['user_acttime'])."</td>";
			// Aktionen
			echo "<td class=\"tbldata\">";
			if ($cu->id()!=$uarr['user_id'])
				echo "<a href=\"?page=messages&amp;mode=new&amp;message_user_to=".$uarr['user_id']."\">Nachricht</a><br/>";
			echo "<a href=\"?page=userinfo&amp;id=".$uarr['user_id']."\">Profil</a><br/>";
			if ($isFounder && $cu->id()!=$uarr['user_id'])
				echo "<a href=\"?page=alliance&amp;action=editmembers&amp;setfounder=".$uarr['user_id']."\" onclick=\"return confirm('Soll der Spieler \'".$uarr['user_nick']."\' wirklich zum Gründer bef&ouml;rdert werden? Dir werden dabei die Gründerrechte entzogen!');\">Gründer</a><br/>";

			if ($cu->id()!=$uarr['user_id'] && $uarr['user_id']!=$arr['alliance_founder_id'])
			{
				echo "<a href=\"?page=$page&amp;action=editmembers&amp;kickuser=".$uarr['user_id'].checker_get_link_key()."\" onclick=\"return confirm('Soll ".$uarr['user_nick']." wirklich aus der Allianz ausgeschlosen werden?');\">Kicken</a>";
			}
			echo "</td></tr>";
		}
		echo "</table>";
		echo "<br/><br/><input type=\"submit\" name=\"editmemberssubmit\" value=\"&Uuml;bernehmen\" />&nbsp;&nbsp;&nbsp;
		<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" /></form>";


}
?>