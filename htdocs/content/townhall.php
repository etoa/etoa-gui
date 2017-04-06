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
	//

	/**
	* The townhall, a public alliance messageboard
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/

	//
	// Script zum Anzeigen/Verbergen von Texten
	//
	?>
	<script type="text/javascript">
	function toggleText(elemId,switchId)
	{
		if (document.getElementById(switchId).innerHTML=="Anzeigen")
		{
			document.getElementById(elemId).style.display='';
			document.getElementById(switchId).innerHTML="Verbergen";
		}
		else
		{
			document.getElementById(elemId).style.display='none';
			document.getElementById(switchId).innerHTML="Anzeigen";
		}
	}

	</script>
	<?PHP

	echo "<h1>Rathaus</h1>";
	echo "Im Rathaus k&ouml;nnen Allianzen Nachrichten an ihre Mitglieder oder an 
	andere Allianzen ver&ouml;ffentlichen; diese Nachrichten k&ouml;nnen &ouml;ffentlich 
	gemacht oder nur f&uuml;r die Empf&auml;nger lesbar publiziert werden. 
	Zum Verfassen einer Nachricht benutze die entsprechende Option auf der Allianzseite.<br><br><br>";


	//
	// Neuste Nachrichten
	//
	$anres=dbquery("
	SELECT 
		alliance_news_title,
		alliance_news_date,
		alliance_news_id,
		alliance_news_alliance_id,
		alliance_news_alliance_to_id,
		alliance_news_text,
		alliance_news_user_id,
		af.alliance_name as afname,
		af.alliance_tag as aftag,
		user_id,
		user_nick
	FROM 
		alliance_news 
	LEFT JOIN
		alliances as af
	ON 
		alliance_news_alliance_id=af.alliance_id
	LEFT JOIN
		users
	ON
		user_id = alliance_news_user_id
	WHERE 
		alliance_news_alliance_to_id=0
	ORDER BY 
		alliance_news_date DESC 
	LIMIT 10;");
	if (mysql_num_rows($anres))
	{
		tableStart("Die neusten 10 Nachrichten");
		echo "<tr>
						<th style=\"width:50%;\">Titel</th>
						<th style=\"width:20%;\">Datum</th>
						<th style=\"width:20%;\">Absender</th>
						<th style=\"width:10%;\">Text</th>
				</tr>";
		while ($anarr=mysql_fetch_array($anres))
		{
			$id = "th".$anarr['alliance_news_id'];
			$sid = "sth".$anarr['alliance_news_id'];

			echo "<tr><td>".text2html($anarr['alliance_news_title'])."</td>";
			echo "<td>".df($anarr['alliance_news_date'])."</td>";
			if($anarr['afname']!="" && $anarr['aftag']!="")
			{
				echo "<td ".tm($anarr['aftag'],text2html($anarr['afname'])).">
								<a href=\"?page=alliance&amp;info_id=".$anarr['alliance_news_alliance_id']."\">".$anarr['aftag']."</a>
							</td>";
			}
			else
			{
				echo "<td>(gel&ouml;scht)</td>";
			}
			echo "<td>
			[<a href=\"javascript:;\" onclick=\"toggleText('".$id."','".$sid."');\" id=\"".$sid."\">Anzeigen</a>]
			</td></tr>";
			echo "<tr id=\"".$id."\" style=\"display:none;\">
				<td colspan=\"5\">".text2html(stripslashes($anarr['alliance_news_text']))."
				<br/><br/>-------------------------------------<br/>";
				if ($anarr['user_id']>0)
				{
					echo "Geschrieben von <b><a href=\"?page=userinfo&amp;id=".$anarr['user_id']."\">".$anarr['user_nick']."</a></b>";
				}
				else
				{
					echo "<i>Unbekannter Verfasser</i>";
				}
				echo "</td>
			</tr>";
		}
		tableEnd();
	}
	else
	{
		iBoxStart("Die neuesten 10 Nachrichten");
		echo "Es sind momentan keine Nachrichten vorhanden!";
		iBoxEnd();
	}


	//
	// Internal messages
	//
	$anres=dbquery("
	SELECT 
		alliance_news_title,
		alliance_news_date,
		alliance_news_id,
		alliance_news_alliance_id,
		alliance_news_alliance_to_id,
		alliance_news_text,
		alliance_news_user_id,
		af.alliance_name as afname,
		af.alliance_tag as aftag,
		user_id,
		user_nick
	FROM 
		alliance_news
	LEFT JOIN
		alliances as af
	ON 
		alliance_news_alliance_id=af.alliance_id
	LEFT JOIN
		users
	ON
		user_id = alliance_news_user_id
	WHERE 
		alliance_news_alliance_to_id=".$cu->allianceId()." 
	ORDER BY 
		alliance_news_date DESC 
	;");
	if (mysql_num_rows($anres))
	{
		tableStart("Allianzinterne Nachrichten");
		echo "<tr>
						<th style=\"width:50%;\">Titel</th>
						<th style=\"width:20%;\">Datum</th>
						<th style=\"width:20%;\">Absender</th>
						<th style=\"width:10%;\">Text</th>
				</tr>";
		while ($anarr=mysql_fetch_array($anres))
		{
			$id = "th".$anarr['alliance_news_id'];
			$sid = "sth".$anarr['alliance_news_id'];

			echo "<tr><td>".text2html($anarr['alliance_news_title'])."</td>";
			echo "<td>".df($anarr['alliance_news_date'])."</td>";
			if($anarr['afname']!="" && $anarr['aftag']!="")
			{
				echo "<td ".tm($anarr['aftag'],text2html($anarr['afname'])).">
					<a href=\"?page=alliance&amp;info_id=".$anarr['alliance_news_alliance_id']."\">".$anarr['aftag']."</a>
					
				</td>";
			}
			else
			{
				echo "<td>(gel&ouml;scht)</td>";
			}
			echo "<td>
			[<a href=\"javascript:;\" onclick=\"toggleText('".$id."','".$sid."');\" id=\"".$sid."\">Anzeigen</a>]
			</td></tr>";
			echo "<tr id=\"".$id."\" style=\"display:none;\">
				<td colspan=\"5\">".text2html(stripslashes($anarr['alliance_news_text']))."
				<br/><br/>-------------------------------------<br/>";
				if ($anarr['user_id']>0)
				{
					echo "Geschrieben von <b><a href=\"?page=userinfo&amp;id=".$anarr['user_id']."\">".$anarr['user_nick']."</a></b>";
				}
				else
				{
					echo "<i>Unbekannter Verfasser</i>";
				}
				echo "</td>
			</tr>";
		}
		tableEnd();
	}
	else
	{
		iBoxStart("Allianzinterne Nachrichten");
		echo "Es sind momentan keine Nachrichten vorhanden!";
		iBoxEnd();
	}


	//
	// Bündnisse
	//
	$res = dbquery("
	SELECT	
		alliance_bnd_id,
		alliance_bnd_name,
		a1.alliance_name as an1,
		a2.alliance_name as an2,  
		a1.alliance_tag as at1,
		a2.alliance_tag as at2,  
		alliance_bnd_alliance_id1 as aid1,
		alliance_bnd_alliance_id2 as aid2,
		alliance_bnd_date,
		alliance_bnd_text_pub
	FROM
		alliance_bnd
	INNER JOIN
		alliances AS a1
	ON alliance_bnd_alliance_id1 = a1.alliance_id
	INNER JOIN
		alliances AS a2
	ON alliance_bnd_alliance_id2 = a2.alliance_id
	WHERE
		alliance_bnd_level=2
	ORDER BY
		alliance_bnd_date DESC
	LIMIT 15;
	");
	if (mysql_num_rows($res)>0)
	{
		tableStart("Neuste Bündnisse");
		echo "
		<tr>
			<th style=\"width:25%;\">Allianz 1</th>
			<th style=\"width:25%;\">Allianz 2</th>
			<th style=\"width:20%;\">Bündnisname</th>
			<th style=\"width:20%;\">Datum</th>
			<th style=\"width:10%;\">Erklärung</th>
		</tr>";
		while ($arr=mysql_fetch_array($res))
		{
			$id = "bnd".$arr['alliance_bnd_id'];
			$sid = "sbnd".$arr['alliance_bnd_id'];
			echo "<tr>
				<td><a href=\"?page=alliance&amp;info_id=".$arr['aid1']."\" ".tm($arr['at1'],text2html($arr['an1'])).">".text2html($arr['an1'])."</td>
				<td><a href=\"?page=alliance&amp;info_id=".$arr['aid2']."\" ".tm($arr['at2'],text2html($arr['an2'])).">".text2html($arr['an2'])."</td>
				<td>".stripslashes($arr['alliance_bnd_name'])."</td>				
				<td>".df($arr['alliance_bnd_date'])."</td>
				<td>";
				if ($arr['alliance_bnd_text_pub']!="")
				{
					echo "[<a href=\"javascript:;\" onclick=\"toggleText('".$id."','".$sid."');\" id=\"".$sid."\">Anzeigen</a>]";
				}
				else
				{
					echo "-";
				}
				echo "</td>
			</tr>";
			echo "<tr id=\"".$id."\" style=\"display:none;\">
				<td colspan=\"5\">".text2html(stripslashes($arr['alliance_bnd_text_pub']))."</td>
			</tr>";
		}
		tableEnd();
	}
	else
	{
		iBoxStart("Neuste Bündnisse");
		echo "Es sind momentan keine Nachrichten vorhanden!";
		iBoxEnd();
	}


	//
	// Kriege
	//
	$res = dbquery("
	SELECT	
		alliance_bnd_id,
		a1.alliance_name as an1,
		a2.alliance_name as an2,  
		a1.alliance_tag as at1,
		a2.alliance_tag as at2,  
		alliance_bnd_alliance_id1 as aid1,
		alliance_bnd_alliance_id2 as aid2,
		alliance_bnd_date,
		alliance_bnd_text_pub
	FROM
		alliance_bnd
	INNER JOIN
		alliances AS a1
	ON alliance_bnd_alliance_id1 = a1.alliance_id
	INNER JOIN
		alliances AS a2
	ON alliance_bnd_alliance_id2 = a2.alliance_id
	WHERE
		alliance_bnd_level=3
	ORDER BY
		alliance_bnd_date DESC;
	");
	if (mysql_num_rows($res)>0)
	{
		tableStart("Aktuelle Kriege (Dauer ".round(WAR_DURATION/3600)."h)");
		echo "<tr>
						<th width=\"25%\">Allianz 1</th>
						<th width=\"25%\">Allianz 2</th>
						<th width=\"20%\">Start</th>
						<th width=\"20%\">Ende</th>
						<th width=\"10%\">Erklärung</th>
					</tr>";
		while ($arr=mysql_fetch_array($res))
		{
			$id = "war".$arr['alliance_bnd_id'];
			$sid = "swar".$arr['alliance_bnd_id'];
			echo "<tr>
				<td><a href=\"?page=alliance&amp;info_id=".$arr['aid1']."\" ".tm($arr['at1'],text2html($arr['an1'])).">".text2html($arr['an1'])."</td>
				<td><a href=\"?page=alliance&amp;info_id=".$arr['aid2']."\" ".tm($arr['at2'],text2html($arr['an2'])).">".text2html($arr['an2'])."</td>
				<td>".df($arr['alliance_bnd_date'])."</td>
				<td>".df($arr['alliance_bnd_date']+WAR_DURATION)."</td>
				<td>";
				if ($arr['alliance_bnd_text_pub']!="")
				{
					echo "[<a href=\"javascript:;\" onclick=\"toggleText('".$id."','".$sid."');\" id=\"".$sid."\">Anzeigen</a>]";
				}
				else
				{
					echo "-";
				}
				echo "</td>
			</tr>";
			echo "<tr id=\"".$id."\" style=\"display:none;\">
				<td colspan=\"5\">".text2html(stripslashes($arr['alliance_bnd_text_pub']))."</td>
			</tr>";
		}
		tableEnd();
	}
	else
	{
		iBoxStart("Aktuelle Kriege (Dauer ".round(WAR_DURATION/3600)."h)");
		echo "Es sind momentan keine Nachrichten vorhanden!";
		iBoxEnd();
	}


	//
	// Friedensabkommen
	//
	$res = dbquery("
	SELECT	
		a1.alliance_name as an1,
		a2.alliance_name as an2,  
		a1.alliance_tag as at1,
		a2.alliance_tag as at2,  
		alliance_bnd_alliance_id1 as aid1,
		alliance_bnd_alliance_id2 as aid2,
		alliance_bnd_date
	FROM
		alliance_bnd
	INNER JOIN
		alliances AS a1
	ON alliance_bnd_alliance_id1 = a1.alliance_id
	INNER JOIN
		alliances AS a2
	ON alliance_bnd_alliance_id2 = a2.alliance_id
	WHERE
		alliance_bnd_level=4
	ORDER BY
		alliance_bnd_date DESC;
	");
	if (mysql_num_rows($res)>0)
	{
		tableStart("Aktuelle Friedensabkommen (Dauer ".round(PEACE_DURATION/3600)."h)");
		echo "<tr>
			<th width=\"30%\">Allianz 1</th>
			<th width=\"30%\">Allianz 2</th>
			<th width=\"20%\">Start</th>
			<th width=\"20%\">Ende</th>
		</tr>";
		while ($arr=mysql_fetch_array($res))
		{
			echo "<tr>
				<td><a href=\"?page=alliance&amp;info_id=".$arr['aid1']."\" ".tm($arr['at1'],text2html($arr['an1'])).">".text2html($arr['an1'])."</td>
				<td><a href=\"?page=alliance&amp;info_id=".$arr['aid2']."\" ".tm($arr['at2'],text2html($arr['an2'])).">".text2html($arr['an2'])."</td>
				<td>".df($arr['alliance_bnd_date'])."</td>
				<td>".df($arr['alliance_bnd_date']+PEACE_DURATION)."</td>
			</tr>";
		}
		tableEnd();
	}
	else
	{
		iBoxStart("Aktuelle Friedensabkommen (Dauer ".round(PEACE_DURATION/3600)."h)");
		echo "Es sind momentan keine Nachrichten vorhanden!";
		iBoxEnd();
	}



