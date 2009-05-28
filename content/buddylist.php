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
	
	/**
	* Manage buddys
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	


	echo "<h1>Buddylist</h1>";	//Titel angepasst <h1> by Lamborghini
	echo "F&uuml;ge Freunde zu deiner Buddylist hinzu um auf einen Blick zu sehen wer alles online ist:<br/><br/>";

	//
	// Erlaubnis erteilen
	//
	if (isset($_GET['allow']) && $_GET['allow']>0)
	{
		$res=dbquery("
		SELECT
			users.user_nick
		FROM
			buddylist
		INNER JOIN
			users
		ON
			buddylist.bl_user_id=users.user_id
			AND buddylist.bl_user_id=".$_GET['allow']."
			AND buddylist.bl_buddy_id=".$cu->id.";");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			dbquery("UPDATE buddylist SET bl_allow=1 WHERE bl_user_id=".$_GET['allow']." AND bl_buddy_id=".$cu->id.";");
			$res = dbquery("
			SELECT
				bl_id
			FROM
				buddylist
			WHERE
				bl_user_id=".$cu->id."
				AND bl_buddy_id=".$_GET['allow']."
			");
			if (mysql_num_rows($res)>0)
			{
				dbquery("UPDATE buddylist SET bl_allow=1 WHERE bl_user_id=".$cu->id." AND bl_buddy_id=".$_GET['allow'].";");
			}
			else
			{
				dbquery("INSERT INTO buddylist (bl_allow,bl_user_id,bl_buddy_id) VALUES (1,".$cu->id.",".$_GET['allow'].");");
			}
			ok_msg("Erlaubnis erteilt!");
		}
		else
			err_msg("Die Erlaubnis kann nicht erteilt werden weil die Anfrage gel&ouml;scht wurde!");
	}

	//
	// Erlaubnis verweigern
	//
	if (isset($_GET['deny']) && $_GET['deny']>0)
	{
		$res=dbquery("
		SELECT
			users.user_nick
		FROM
			buddylist,
			users
		WHERE
			buddylist.bl_user_id=".$_GET['deny']."
			AND buddylist.bl_user_id=users.user_id
			AND buddylist.bl_buddy_id=".$cu->id.";");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			dbquery("DELETE FROM buddylist WHERE bl_user_id=".$_GET['deny']." AND bl_buddy_id=".$cu->id.";");
			ok_msg("Die Anfrage wurde gel&ouml;scht!");
		}
		else
			err_msg("Die Anfrage konnte nicht gel&ouml;scht werden weil sie nicht mehr existiert!");
	}

	//
	// Freund hinzufügen
	//
	if ((isset($_POST['buddy_nick']) && $_POST['buddy_nick']!="" && $_POST['submit_buddy']!="") || (isset($_GET['add_id']) && $_GET['add_id']>0))
	{
		if (isset($_GET['add_id']) && $_GET['add_id']>0)
			$res=dbquery("SELECT user_id,user_nick FROM users WHERE user_id='".$_GET['add_id']."';");
		else
			$res=dbquery("SELECT user_id,user_nick FROM users WHERE user_nick='".$_POST['buddy_nick']."';");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			if ($cu->id!=$arr['user_id'])
			{
				if (mysql_num_rows(dbquery("SELECT bl_user_id FROM buddylist WHERE bl_user_id='".$cu->id."' AND bl_buddy_id='".$arr['user_id']."';"))==0)
				{
					dbquery("INSERT INTO buddylist (bl_user_id,bl_buddy_id,bl_allow) VALUES('".$cu->id."','".$arr['user_id']."',0);");
					ok_msg("[b]".$arr['user_nick']."[/b] wurde zu deiner Liste hinzugef&uuml;gt und ihm wurde eine Best&auml;tigungsnachricht gesendet!");
					send_msg($arr['user_id'],5,"Buddylist-Anfrage von ".$cu->nick,"Der Spieler will dich zu seiner Freundesliste hinzuf&uuml;gen.\n\n[url ?page=buddylist]Anfrage bearbeiten[/url]");
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
	if (isset($_GET['remove']) && $_GET['remove']>0)
	{
		$c = 0;
		dbquery("DELETE FROM buddylist WHERE bl_user_id='".$cu->id."' AND bl_buddy_id='".$_GET['remove']."';");
		$c+=mysql_affected_rows();
		dbquery("DELETE FROM buddylist WHERE bl_user_id='".$_GET['remove']."' AND bl_buddy_id='".$cu->id."';");
		$c+=mysql_affected_rows();
		if ($c>0)
		{
			ok_msg("Der Spieler wurde von der Freundesliste entfern!");
		}
	}

	//
	// In einer anderen Liste entfernen
	//
	if (isset($_GET['removeremote']) && $_GET['removeremote']>0)
	{
		dbquery("DELETE FROM buddylist WHERE bl_user_id='".$_GET['removeremote']."' AND bl_buddy_id='".$cu->id."';");
	}

	if (isset($_GET['comment']) && $_GET['comment']>0)
	{
		$res = dbquery("
		SELECT 
			bl_user_id,
			bl_buddy_id,		
		  bl_comment,
		  bl_comment_buddy,
		  bl_id
		FROM 
			buddylist 
		WHERE 
			bl_id='".$_GET['comment']."'
			AND
			(
				bl_user_id=".$cu->id."
				OR bl_buddy_id=".$cu->id."
			)
		;");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			echo "<form action=\"?page=$page\" method=\"post\">";
			if ($arr['bl_user_id']==$cu->id)
			{
				$nick = get_user_nick($arr['bl_buddy_id']);
				iBoxStart("Kommentar für ".$nick."");
				echo "<textarea name=\"bl_comment\" rows=\"5\" cols=\"60\">".stripslashes($arr['bl_comment'])."</textarea>";
				iBoxEnd();
			}
			else
			{
				$nick = get_user_nick($arr['bl_user_id']);
				iBoxStart("Kommentar für ".$nick."");
				echo "<textarea name=\"bl_comment_buddy\" rows=\"5\" cols=\"60\">".stripslashes($arr['bl_comment_buddy'])."</textarea>";
				iBoxEnd();
			}			
			
			echo "<input type=\"hidden\" name=\"bl_id\" value=\"".$arr['bl_id']."\" />";
			echo "<input type=\"submit\" name=\"cmt_submit\" value=\"Speichern\" /> ";
			echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Abbrechen\" />";
			echo "</form>";
		}		
		else
		{
			echo "Daten nicht gefunden!";
		}
	}
	else
	{
		if (isset($_POST['cmt_submit']))
		{
			if (isset($_POST['bl_comment']))
			{
				dbquery("UPDATE buddylist SET bl_comment='".addslashes($_POST['bl_comment'])."' WHERE bl_id=".$_POST['bl_id'].";");
			}
			else
			{
				dbquery("UPDATE buddylist SET bl_comment_buddy='".addslashes($_POST['bl_comment_buddy'])."' WHERE bl_id=".$_POST['bl_id'].";");
			}			
		}

	$res=dbquery("
	SELECT
        users.user_id,
        users.user_nick,
        users.user_points,
        users.user_acttime,
        buddylist.bl_allow,
        buddylist.bl_comment,
        buddylist.bl_comment_buddy,
        buddylist.bl_id,
        bl_user_id,
        bl_buddy_id,
        planets.id as pid
	FROM
        buddylist
  INNER JOIN
  (
    users
   	INNER JOIN
			planets
		ON    
        user_id=planets.planet_user_id
        AND planets.planet_user_main=1
  )
	ON 
  	buddylist.bl_user_id='".$cu->id."'
    AND buddylist.bl_buddy_id=users.user_id
	ORDER BY
		users.user_nick ASC;");
	if (mysql_num_rows($res)>0)
	{
		tableStart("Meine Freunde");
		echo "<tr>
			<th>Nick</th>
			<th>Punkte</th>
			<th>Hauptplanet</th>
			<th>Online</th>
			<th>Kommentar</th>
			<th>Aktion</th>
		</tr>";
		while($arr=mysql_fetch_array($res))
		{
			echo "<tr>
			<td>".$arr['user_nick']."</td>";
			if ($arr['bl_allow']==1)
			{
				$tp = new Planet($arr['pid']);
				echo "<td>".nf($arr['user_points'])."</td>";
				echo "<td><a href=\"?page=cell&amp;id=".$tp->cellId()."&amp;hl=".$tp->id()."\">".$tp."</a></td>";
				if ((time()-$conf['online_threshold']['v']*60) < $arr['user_acttime'])
					echo "<td style=\"color:#0f0;\">online</td>";
				else
					echo "<td>".date("d.m.Y H:i",$arr['user_acttime'])."</td>";
			}
			else
				echo "<td colspan=\"3\"><i>Noch keine Erlaubnis</i></td>";
			echo "<td>";
			if ($arr['bl_comment']!="" && $arr['bl_user_id']==$cu->id)
			{
				echo text2html($arr['bl_comment']);
			}
			if ($arr['bl_comment_buddy']!="" && $arr['bl_buddy_id']==$cu->id)
			{
				echo text2html($arr['bl_comment_buddy']);
			}
			echo "</td>";
			echo "<td>
				<a href=\"?page=messages&mode=new&message_user_to=".$arr['user_id']."\" title=\"Nachricht\">Nachricht</a>  
				<a href=\"?page=userinfo&amp;id=".$arr['user_id']."\" title=\"Info\">Profil</a><br/>
				<a href=\"?page=$page&comment=".$arr['bl_id']."\" title=\"Kommentar bearbeiten\">Kommentar</a> ";
			echo "<a href=\"?page=$page&remove=".$arr['user_id']."\" onclick=\"return confirm('Willst du ".$arr['user_nick']." wirklich von deiner Liste entfernen?');\">Entfernen</a></td>";

			echo "</tr>";
		}
		tableEnd();
	}
	else
	{
		error_msg("Es sind noch keine Freunde in deiner Buddyliste eingetragen!",1);
	}

$res=dbquery("
	SELECT
    users.user_id,
    users.user_nick,
    users.user_points,
    bl_id,
    bl_user_id,
    bl_buddy_id,
    bl_comment,
    bl_comment_buddy
	FROM
    buddylist
  INNER JOIN
  (
    users
  )
	ON 
  	buddylist.bl_buddy_id='".$cu->id."'
    AND buddylist.bl_user_id=users.user_id
    AND bl_allow=0
	ORDER BY
		users.user_nick ASC;");
	if (mysql_num_rows($res)>0)
	{
		tableStart("Offene Anfragen");
		echo "<tr>
			<th class=\"tbltitle\">Nick</th>
			<th class=\"tbltitle\">Punkte</th>
			<th class=\"tbltitle\">Aktion</th>
		</tr>";
		while($arr=mysql_fetch_array($res))
		{
			echo "<tr>
				<td class=\"tbldata\">".$arr['user_nick']." ";
			if ($arr['bl_comment']!="" && $arr['bl_user_id']==$cu->id)
			{
				echo " <img src=\"images/infohelp.png\" alt=\"Info\" style=\"height:10px;\" ".tm("Kommentar",text2html($arr['bl_comment']))."></a>";
			}
			if ($arr['bl_comment_buddy']!="" && $arr['bl_buddy_id']==$cu->id)
			{
				echo " <img src=\"images/infohelp.png\" alt=\"Info\" style=\"height:10px;\" ".tm("Kommentar",text2html($arr['bl_comment_buddy']))."></a>";
			}

			echo "</td>";
			echo "<td>".nf($arr['user_points'])."</td>";
			echo "<td style=\"width:280px;\">
				<a href=\"?page=messages&mode=new&message_user_to=".$arr['user_id']."\" title=\"Nachricht\">Nachricht</a>  
				<a href=\"?page=userinfo&amp;id=".$arr['user_id']."\" title=\"Info\">Profil</a> 
				<a href=\"?page=$page&amp;allow=".$arr['user_id']."\" style=\"color:#0f0\">Annehmen</a> 
				<a href=\"?page=$page&amp;deny=".$arr['user_id']."\" style=\"color:#f90\">Zurückweisen</a>
			</td>";

			echo "</tr>";
		}
		tableEnd();
	}

	echo "
	<h2>Füge einen Freund hinzu</h2>
	<form action=\"?page=$page\" method=\"post\"><b>Nick:</b> <input type=\"text\" name=\"buddy_nick\" id=\"user_nick\"  maxlength=\"20\" size=\"20\" autocomplete=\"off\" value=\"\" onkeyup=\"xajax_searchUser(this.value)\"><br/><div class=\"citybox\" id=\"citybox\">&nbsp;</div><br>
  <input type=\"submit\" name=\"submit_buddy\" value=\"Freund hinzuf&uuml;gen\" />
	</form><br/><br/>";

	}
	
	
	
	
	
	
?>
