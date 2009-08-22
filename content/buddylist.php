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
        Max(user_sessionlog.time_action) as last_log,
		user_sessions.time_action,
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
	LEFT JOIN
		user_sessionlog
	ON
		users.user_id=user_sessionlog.user_id
	LEFT JOIN
		user_sessions
	ON
		users.user_id=user_sessions.user_id
   	INNER JOIN
			planets
		ON    
        users.user_id=planets.planet_user_id
        AND planets.planet_user_main=1
  )
	ON 
  		buddylist.bl_user_id='".$cu->id."'
    	AND buddylist.bl_buddy_id=users.user_id
	GROUP BY
		users.user_id
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
				if ($arr['time_action'])
					echo "<td style=\"color:#0f0;\">online</td>";
				elseif ($arr['last_log'])
					echo "<td>".date("d.m.Y H:i",$arr['last_log'])."</td>";
				else
					echo "<td>Noch nicht eingeloggt!</td>";
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
	
	
	
// Renderzeit-Start festlegen
	$render_time = explode(" ",microtime());
	$render_starttime=$render_time[1]+$render_time[0];

/*
	mysql_query("
	DELETE
	FROM
		dl4_3_items2
	WHERE
		item2_for_op=0
		AND item2_for_opmp=0
		AND item2_for_ap=0
		AND item2_for_vp=0
		AND item2_for_hp=0
		AND item2_for_mp=0
		AND item2_for_tp=0;");

*/
mysql_query("UPDATE dl4_3_items2 SET `item2_opmp_value`=(item2_ap*1.2 + item2_vp*0.85 + item2_hp*0.7 + item2_mp*0) LIMIT 100;";
echo "ok";
/*

	//20,30,35,40,45,50,60,65,70,75,80,85,90,100,115,130,140,160,180,190,195,220,230,240,245,270,290,300,305,315,330,340,350,360,365,375,390,400,410,415,430,435,440,445,450,465,470,490,520,535,560,580,610,650,690,710,740,900,940,960,990,1150,1200,1250,1300,1350,1400,1450,1500
	
	$kraft_array = array(20);
	$op_array = array();
	$res = mysql_query("SELECT item_op FROM dl4_3_items GROUP BY item_op;");
	while($arr=mysql_fetch_array($res))
	{
		$op_array[] = $arr['item_op'];
	}


	foreach($kraft_array as $kraft)
	{
		foreach($op_array as $op)
		{
			// OPs
			$res = mysql_query("
			SELECT
			(
				SELECT 
					item2_id
				FROM
					dl4_3_items2
				WHERE
					item2_kraft <= ".$kraft."
					AND item2_op <= ".$op."
					AND item2_distance = 0
				ORDER BY (`item2_ap`*1.2 + `item2_vp`*0.85 + `item2_hp`*0.7 + `item2_mp`*0.9) desc
				LIMIT 1
			) AS OP,
			(
				SELECT 
					item2_id
				FROM
					dl4_3_items2
				WHERE
					item2_kraft <= ".$kraft."
					AND item2_op <= ".$op."
					AND item2_distance = 0
				ORDER BY (`item2_ap`*1.2 + `item2_vp`*0.85 + `item2_hp`*0.7 + `item2_mp`*0) desc
				LIMIT 1
			) AS OPMP,
			(
				SELECT 
					item2_id
				FROM
					dl4_3_items2
				WHERE
					item2_kraft <= ".$kraft."
					AND item2_op <= ".$op."
					AND item2_distance = 0
				ORDER BY item2_ap desc
				LIMIT 1
			) AS AP,
			(
				SELECT 
					item2_id
				FROM
					dl4_3_items2
				WHERE
					item2_kraft <= ".$kraft."
					AND item2_op <= ".$op."
					AND item2_distance = 0
				ORDER BY item2_vp desc
				LIMIT 1
			) AS VP,
			(
				SELECT 
					item2_id
				FROM
					dl4_3_items2
				WHERE
					item2_kraft <= ".$kraft."
					AND item2_op <= ".$op."
					AND item2_distance = 0
				ORDER BY item2_hp desc
				LIMIT 1
			) AS HP,
			(
				SELECT 
					item2_id
				FROM
					dl4_3_items2
				WHERE
					item2_kraft <= ".$kraft."
					AND item2_op <= ".$op."
					AND item2_distance = 0
				ORDER BY item2_mp desc
				LIMIT 1
			) AS MP,
			(
				SELECT 
					item2_id
				FROM
					dl4_3_items2
				WHERE
					item2_kraft <= ".$kraft."
					AND item2_op <= ".$op."
					AND item2_distance = 0
				ORDER BY item2_tp desc
				LIMIT 1
			) AS TP
			");
			$arr=mysql_fetch_array($res);
		
			if($arr['OP']>0)
			{
				mysql_query("
			  UPDATE
			   dl4_3_items2
			  SET
			    item2_for_op=1
			  WHERE
			    item2_id=".$arr['OP'].";");
			}
			if($arr['OPMP']>0)
			{
				mysql_query("
			  UPDATE
			   dl4_3_items2
			  SET
			    item2_for_opmp=1
			  WHERE
			    item2_id=".$arr['OPMP'].";");
			}
			if($arr['AP']>0)
			{
				mysql_query("
			  UPDATE
			   dl4_3_items2
			  SET
			    item2_for_ap=1
			  WHERE
			    item2_id=".$arr['AP'].";");
			}
			if($arr['VP']>0)
			{
				mysql_query("
			  UPDATE
			   dl4_3_items2
			  SET
			    item2_for_vp=1
			  WHERE
			    item2_id=".$arr['VP'].";");
			}
			if($arr['HP']>0)
			{
				mysql_query("
			  UPDATE
			   dl4_3_items2
			  SET
			    item2_for_hp=1
			  WHERE
			    item2_id=".$arr['HP'].";");
			}
			if($arr['MP']>0)
			{
				mysql_query("
			  UPDATE
			   dl4_3_items2
			  SET
			    item2_for_mp=1
			  WHERE
			    item2_id=".$arr['MP'].";");
			}
			if($arr['TP']>0)
			{
				mysql_query("
			  UPDATE
			   dl4_3_items2
			  SET
			    item2_for_tp=1
			  WHERE
			    item2_id=".$arr['TP'].";");
			}
		    
		    

		}
	}
*/



/*

// Waffen
$res = mysql_query("SELECT * FROM dl4_3_items WHERE item_cat_id='1';");
$weapons = array();
while($arr=mysql_fetch_array($res))
{
	$weapons[] = $arr;
}

// schilder
$res = mysql_query("SELECT * FROM dl4_3_items WHERE item_cat_id='2';");
$shields = array();
while($arr=mysql_fetch_array($res))
{
	$shields[] = $arr;
}
// rüstung
$res = mysql_query("SELECT * FROM dl4_3_items WHERE item_cat_id='3';");
$armor = array();
while($arr=mysql_fetch_array($res))
{
	$armor[] = $arr;
}
$res = mysql_query("SELECT * FROM dl4_3_items WHERE item_cat_id='4';");
$helms = array();
while($arr=mysql_fetch_array($res))
{
	$helms[] = $arr;
}
$res = mysql_query("SELECT * FROM dl4_3_items WHERE item_cat_id='5';");
$acc = array();
while($arr=mysql_fetch_array($res))
{
	$acc[] = $arr;
}
echo "<br>teest:<br>";
$count = 1; 
$count2 = 1;
$count3 = 1;
$sql_values = "";
foreach($weapons as $weapons_data) 
{ 
	if($count == 0) 
	{ 
		// schilder 
		foreach($shields as $shields_data) 
		{ 
			// rüstung 
			foreach($armor as $armor_data) 
			{ 
				// helme 
				foreach($helms as $helms_data) 
				{ 
					// zubehör 
					foreach($acc as $acc_data) 
					{ 
						$kraft_sum = $weapons_data['item_kraft'] + $shields_data['item_kraft'] + $armor_data['item_kraft'] + $helms_data['item_kraft'] + $acc_data['item_kraft']; 
						$max_op = max($weapons_data['item_op'],$shields_data['item_op'],$armor_data['item_op'],$helms_data['item_op'], $acc_data['item_op']);
						
						$ap_sum = $weapons_data['item_ap'] + $shields_data['item_ap'] + $armor_data['item_ap'] + $helms_data['item_ap'] + $acc_data['item_ap']; $vp_sum = $weapons_data['item_vp'] + $shields_data['item_vp'] + $armor_data['item_vp'] + $helms_data['item_vp'] + $acc_data['item_vp']; $hp_sum = $weapons_data['item_hp'] + $shields_data['item_hp'] + $armor_data['item_hp'] + $helms_data['item_hp'] + $acc_data['item_hp']; $mp_sum = $weapons_data['item_mp'] + $shields_data['item_mp'] + $armor_data['item_mp'] + $helms_data['item_mp'] + $acc_data['item_mp']; $tp_sum = $weapons_data['item_tp'] + $shields_data['item_tp'] + $armor_data['item_tp'] + $helms_data['item_tp'] + $acc_data['item_tp']; 
						
						$kombi = "".$weapons_data['item_name']." / ".$shields_data['item_name']." / ".$armor_data['item_name']." / ".$helms_data['item_name']." / ".$acc_data['item_name']." / ";
						
						if($weapons_data['item_skills']=="distance;")
						{
							$distance = 1;
						}
						else
						{
							$distance = 0;
						}
						
						$solve = true;
						
						//Element
						if($weapons_data['item_element']!="" || $shields_data['item_element'] != "" || $armor_data['item_element'] != "" || $helms_data['item_element'] != "" || $acc_data['item_element'] != "")
						{
							
							//feuer
							if(($weapons_data['item_element']=="fire" || $shields_data['item_element']=="fire" || $armor_data['item_element']=="fire" || $helms_data['item_element']=="fire" || $acc_data['item_element']=="fire") 
							AND 
							($weapons_data['item_element']=="ice" || $shields_data['item_element']=="ice" || $armor_data['item_element']=="ice" || $helms_data['item_element']=="ice" || $acc_data['item_element']=="ice" ||
							$weapons_data['item_element']=="stone" || $shields_data['item_element']=="stone" || $armor_data['item_element']=="stone" || $helms_data['item_element']=="stone" || $acc_data['item_element']=="stone"))
							{
								$solve = false;
							}
							
							//eis
							if(($weapons_data['item_element']=="ice" || $shields_data['item_element']=="ice" || $armor_data['item_element']=="ice" || $helms_data['item_element']=="ice" || $acc_data['item_element']=="ice")
							AND 
							($weapons_data['item_element']=="fire" || $shields_data['item_element']=="fire" || $armor_data['item_element']=="fire" || $helms_data['item_element']=="fire" || $acc_data['item_element']=="fire" ||
							$weapons_data['item_element']=="air" || $shields_data['item_element']=="air" || $armor_data['item_element']=="air" || $helms_data['item_element']=="air" || $acc_data['item_element']=="air"))
							{
								$solve = false;
							}
							
							//luft
							if(($weapons_data['item_element']=="air" || $shields_data['item_element']=="air" || $armor_data['item_element']=="air" || $helms_data['item_element']=="air" || $acc_data['item_element']=="air")
							AND 
							($weapons_data['item_element']=="stone" || $shields_data['item_element']=="stone" || $armor_data['item_element']=="stone" || $helms_data['item_element']=="stone" || $acc_data['item_element']=="stone" ||
							$weapons_data['item_element']=="ice" || $shields_data['item_element']=="ice" || $armor_data['item_element']=="ice" || $helms_data['item_element']=="ice" || $acc_data['item_element']=="ice"))
							{
								$solve = false;
							}
							
							//erde
							if(($weapons_data['item_element']=="stone" || $shields_data['item_element']=="stone" || $armor_data['item_element']=="stone" || $helms_data['item_element']=="stone" || $acc_data['item_element']=="stone")
							AND 
							($weapons_data['item_element']=="fire" || $shields_data['item_element']=="fire" || $armor_data['item_element']=="fire" || $helms_data['item_element']=="fire" || $acc_data['item_element']=="fire" ||
							$weapons_data['item_element']=="air" || $shields_data['item_element']=="air" || $armor_data['item_element']=="air" || $helms_data['item_element']=="air" || $acc_data['item_element']=="air"))
							{
								$solve = false;
							}
							
						}
						
						if($solve)
						{
							if($sql_values=="")
							{
								//$sql_values .= "(".$max_op.", ".$kraft_sum.", ".$ap_sum.", ".$vp_sum.", ".$hp_sum.", ".$mp_sum.", ".$tp_sum.", '".$weapons_data['item_name']."', '".$shields_data['item_name']."', '".$armor_data['item_name']."', '".$helms_data['item_name']."', '".$acc_data['item_name']."')";
								
								$sql_values .= "(".$max_op.", ".$kraft_sum.", ".$ap_sum.", ".$vp_sum.", ".$hp_sum.", ".$mp_sum.", ".$tp_sum.",  ".$distance.", ".$weapons_data['item_id'].", ".$shields_data['item_id'].", ".$armor_data['item_id'].", ".$helms_data['item_id'].", ".$acc_data['item_id'].")";
							}
							else
							{
								//$sql_values .= ",(".$max_op.", ".$kraft_sum.", ".$ap_sum.", ".$vp_sum.", ".$hp_sum.", ".$mp_sum.", ".$tp_sum.", '".$weapons_data['item_name']."', '".$shields_data['item_name']."', '".$armor_data['item_name']."', '".$helms_data['item_name']."', '".$acc_data['item_name']."')";
								
								$sql_values .= ",(".$max_op.", ".$kraft_sum.", ".$ap_sum.", ".$vp_sum.", ".$hp_sum.", ".$mp_sum.", ".$tp_sum.", ".$distance.", ".$weapons_data['item_id'].", ".$shields_data['item_id'].", ".$armor_data['item_id'].", ".$helms_data['item_id'].", ".$acc_data['item_id'].")";
							}
							
							$count3++;
							if($count3==10000)
							{
								mysql_query("INSERT INTO dl4_3_items2 (item2_op, item2_kraft, item2_ap, item2_vp, item2_hp, item2_mp, item2_tp, item2_distance, item2_weapon, item2_shield, item2_armor, item2_helm, item2_acc) VALUES ".$sql_values.";");
								
								$sql_values = "";
								$count3=1;
							}
						}

						//echo "count:$count / count2:$count2<br>$kombi<br>kraft:$kraft_sum<br>op:$max_op<br>ap:$ap_sum<br>vp:$vp_sum<br>hp:$hp_sum<br>mp:$mp_sum<br>tp:$tp_sum<br><br>";
						$count2++;
						
					} 
				}
			}
		}
	}
	$count++;
}
mysql_query("INSERT INTO dl4_3_items2 (item2_op, item2_kraft, item2_ap, item2_vp, item2_hp, item2_mp, item2_tp, item2_distance, item2_weapon, item2_shield, item2_armor, item2_helm, item2_acc) VALUES ".$sql_values.";");

echo "Count: $count / count2: $count2";
*/


$render_time = explode(' ',microtime());
	$rtime = $render_time[1]+$render_time[0]-$render_starttime;
	echo "<br><br>Erstellt in ".$rtime." sec<br>";
	
	
?>
