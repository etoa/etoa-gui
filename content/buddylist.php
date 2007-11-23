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
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	File: buddylist.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Manage buddys
	*
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	


	echo "<h1>Buddylist</h1>";	//Titel angepasst <h1> by Lamborghini
	echo "F&uuml;ge Freunde zu deiner Buddylist hinzu um auf einen Blick zu sehen wer alles online ist:<br/><br/>";

	//
	// Erlaubnis erteilen
	//
	if ($_GET['allow']>0)
	{
		$res=dbquery("
		SELECT
			users.user_nick
		FROM
			".$db_table['buddylist']."
		INNER JOIN
			".$db_table['users']."
		ON
			buddylist.bl_user_id=users.user_id
			AND buddylist.bl_user_id=".$_GET['allow']."
			AND buddylist.bl_buddy_id=".$s['user']['id'].";");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			dbquery("UPDATE ".$db_table['buddylist']." SET bl_allow=1 WHERE bl_user_id=".$_GET['allow']." AND bl_buddy_id=".$s['user']['id'].";");
			$res = dbquery("
			SELECT
				bl_id
			FROM
				buddylist
			WHERE
				bl_user_id=".$s['user']['id']."
				AND bl_buddy_id=".$_GET['allow']."
			");
			if (mysql_num_rows($res)>0)
			{
				dbquery("UPDATE ".$db_table['buddylist']." SET bl_allow=1 WHERE bl_user_id=".$s['user']['id']." AND bl_buddy_id=".$_GET['allow'].";");
			}
			else
			{
				dbquery("INSERT INTO ".$db_table['buddylist']." (bl_allow,bl_user_id,bl_buddy_id) VALUES (1,".$s['user']['id'].",".$_GET['allow'].");");
			}
			dbquery("DELETE FROM ".$db_table['messages']." WHERE message_user_to=".$s['user']['id']." AND message_user_from=0 AND message_subject='Buddylist-Anfrage von ".$arr['user_nick']."';");
			ok_msg("Erlaubnis erteilt!");
		}
		else
			err_msg("Die Erlaubnis kann nicht erteilt werden weil die Anfrage gel&ouml;scht wurde!");
	}

	//
	// Erlaubnis verweigern
	//
	if ($_GET['deny']>0)
	{
		$res=dbquery("
		SELECT
			users.user_nick
		FROM
			".$db_table['buddylist'].",
			".$db_table['users']."
		WHERE
			buddylist.bl_user_id=".$_GET['deny']."
			AND buddylist.bl_user_id=users.user_id
			AND buddylist.bl_buddy_id=".$s['user']['id'].";");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			dbquery("DELETE FROM ".$db_table['buddylist']." WHERE bl_user_id=".$_GET['deny']." AND bl_buddy_id=".$s['user']['id'].";");
			dbquery("DELETE FROM ".$db_table['messages']." WHERE message_user_to=".$s['user']['id']." AND message_user_from=0 AND message_subject='Buddylist-Anfrage von ".$arr['user_nick']."';");
			ok_msg("Die Anfrage wurde gel&ouml;scht!");
		}
		else
			err_msg("Die Anfrage konnte nicht gel&ouml;scht werden weil sie nicht mehr existiert!");
	}

	//
	// Freund hinzufügen
	//
	if (($_POST['buddy_nick']!="" && $_POST['submit_buddy']!="") || $_GET['add_id']>0)
	{
		if ($_GET['add_id']>0)
			$res=dbquery("SELECT user_id,user_nick FROM ".$db_table['users']." WHERE user_id='".$_GET['add_id']."';");
		else
			$res=dbquery("SELECT user_id,user_nick FROM ".$db_table['users']." WHERE user_nick='".$_POST['buddy_nick']."';");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			if ($s['user']['id']!=$arr['user_id'])
			{
				if (mysql_num_rows(dbquery("SELECT bl_user_id FROM ".$db_table['buddylist']." WHERE bl_user_id='".$s['user']['id']."' AND bl_buddy_id='".$arr['user_id']."';"))==0)
				{
					dbquery("INSERT INTO ".$db_table['buddylist']." (bl_user_id,bl_buddy_id,bl_allow) VALUES('".$s['user']['id']."','".$arr['user_id']."',0);");
					ok_msg("[b]".$arr['user_nick']."[/b] wurde zu deiner Liste hinzugef&uuml;gt und ihm wurde eine Best&auml;tigungsnachricht gesendet!");
					send_msg($arr['user_id'],5,"Buddylist-Anfrage von ".$s['user']['nick'],"Der Spieler will dich zu seiner Freundesliste hinzuf&uuml;gen. Willst du dies erlauben?\n\n[url ?page=buddylist&allow=".$s['user']['id']."]Erlauben[/url] [url ?page=buddylist&deny=".$s['user']['id']."]Verbieten[/url]");
				}
				else
					err_msg("Dieser Eintrag ist schon vorhanden!");
			}
			else
				err_msg("Du kannst nicht dich selbst zur Buddyliste hinzuf&uuml;gen!");
		}
		else
			err_msg("Der Spieler [b]".$_POST['buddy_nick']."[/b] konnte nicht gefunden werden!");
	}

	//
	// Entfernen
	//
	if ($_GET['remove']>0)
	{
		$c = 0;
		dbquery("DELETE FROM ".$db_table['buddylist']." WHERE bl_user_id='".$s['user']['id']."' AND bl_buddy_id='".$_GET['remove']."';");
		$c+=mysql_affected_rows();
		dbquery("DELETE FROM ".$db_table['buddylist']." WHERE bl_user_id='".$_GET['remove']."' AND bl_buddy_id='".$s['user']['id']."';");
		$c+=mysql_affected_rows();
		if ($c>0)
		{
			ok_msg("Der Spieler wurde von der Freundesliste entfern!");
		}
	}

	//
	// In einer anderen Liste entfernen
	//
	if ($_GET['removeremote']>0)
	{
		dbquery("DELETE FROM ".$db_table['buddylist']." WHERE bl_user_id='".$_GET['removeremote']."' AND bl_buddy_id='".$s['user']['id']."';");
	}

	$res=dbquery("
	SELECT
        users.user_id,
        users.user_nick,
        users.user_points,
        users.user_acttime,
        buddylist.bl_allow,
        planets.planet_id
	FROM
        ".$db_table['buddylist']."
  INNER JOIN
  (
    ".$db_table['users']."
   	INNER JOIN
			".$db_table['planets']."
		ON    
        user_id=planets.planet_user_id
        AND planets.planet_user_main=1
  )
	ON 
  	buddylist.bl_user_id='".$s['user']['id']."'
    AND buddylist.bl_buddy_id=users.user_id
	ORDER BY
		users.user_nick ASC;");
	if (mysql_num_rows($res)>0)
	{
		infobox_start("Meine Freunde",1);
		echo "<tr><th class=\"tbltitle\">Nick</th><th class=\"tbltitle\">Punkte</th><th class=\"tbltitle\">Hauptplanet</th><th class=\"tbltitle\">Zuletzt online</th><th class=\"tbltitle\">Aktion</th></tr>";
		while($arr=mysql_fetch_array($res))
		{
			echo "<tr><td class=\"tbldata\">".$arr['user_nick']."</td>";
			if ($arr['bl_allow']==1)
			{
				echo "<td class=\"tbldata\">".nf($arr['user_points'])."</td>";
				echo "<td class=\"tbldata\">".coords_format2($arr['planet_id'],1)."</td>";
				if ((time()-$conf['online_threshold']['v']*60) < $arr['user_acttime'])
					echo "<td class=\"tbldata\" style=\"color:#0f0;\">online</td>";
				else
					echo "<td class=\"tbldata\">".date("d.m.Y H:i",$arr['user_acttime'])."</td>";
			}
			else
				echo "<td class=\"tbldata\" colspan=\"3\"><i>Noch keine Erlaubnis</i></td>";
			echo "<td class=\"tbldata\"><a href=\"?page=messages&mode=new&message_user_to=".$arr['user_id']."\" title=\"Nachricht\">Nachricht</a> &nbsp; ";
			echo "<a href=\"?page=$page&remove=".$arr['user_id']."\" onclick=\"return confirm('Willst du ".$arr['user_nick']." wirklich von deiner Liste entfernen?');\">Entfernen</a></td>";

			echo "</tr>";
		}
		infobox_end(1);
	}
	else
		echo "Es sind noch keine Freunde in deiner Buddyliste eingetragen!<br/><br/>";
		echo "<form action=\"?page=$page\" method=\"post\"><b>Nick:</b> <input type=\"text\" name=\"buddy_nick\" id=\"user_nick\"  maxlength=\"20\" size=\"20\" autocomplete=\"off\" value=\"\" onkeyup=\"xajax_searchUser(this.value)\"><br/><div class=\"citybox\" id=\"citybox\">&nbsp;</div><br>
	  <input type=\"submit\" name=\"submit_buddy\" value=\"Freund hinzuf&uuml;gen\" />
		</form><br/><br/>";

/*
	$res=dbquery("
	SELECT
        users.user_id,
        users.user_nick,
        users.user_points,
        users.user_acttime
	FROM
        ".$db_table['buddylist'].",
        ".$db_table['users']."
	WHERE
        buddylist.bl_buddy_id='".$s['user']['id']."'
        AND buddylist.bl_user_id=users.user_id
        AND buddylist.bl_allow=1
	ORDER BY
		users.user_points DESC;");
	if (mysql_num_rows($res)>0)
	{
		infobox_start("Ich bin bei folgenden Spielern in der Liste",1);
		echo "<tr><th class=\"tbltitle\">Nick</th><th class=\"tbltitle\">Punkte</th><th class=\"tbltitle\">Zuletzt online</th><th class=\"tbltitle\">Aktion</th></tr>";
		while($arr=mysql_fetch_array($res))
		{
			echo "<tr><td class=\"tbldata\">".$arr['user_nick']."</td>";
			echo "<td class=\"tbldata\">".nf($arr['user_points'])."</td>";
			if ((time()-$conf['online_threshold']['v']*60) < $arr['user_acttime'])
				echo "<td class=\"tbldata\" style=\"color:#0f0;\">online</td>";
			else
				echo "<td class=\"tbldata\">".date("d.m.Y H:i",$arr['user_acttime'])."</td>";
			echo "<td class=\"tbldata\"><a href=\"?page=messages&mode=new&message_user_to=".$arr['user_id']."\" title=\"Nachricht\">Nachricht</a> &nbsp; ";
			echo "<a href=\"?page=$page&removeremote=".$arr['user_id']."\" onclick=\"return confirm('Willst du dich wirklich von der Buddyliste von ".$arr['user_nick']." löschen?');\">Entfernen</a></td>";
			echo "</tr>";
		}
		infobox_end(1);
	}
*/

?>
