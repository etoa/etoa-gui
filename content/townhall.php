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
	// 	File: townhall.php
	// 	Created: 01.10.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* The townhall, a public alliance messageboard
	*
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

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
	<?

	echo "<h1>Ratshaus</h1>";
	echo "Im Rathaus k&ouml;nnen Allianzen Nachrichten an ihre Mitglieder oder an 
	andere Allianzen ver&ouml;ffentlichen; diese Nachrichten k&ouml;nnen &ouml;ffentlich 
	gemacht oder nur f&uuml;r die Empf&auml;nger lesbar publiziert werden. 
	Zum Verfassen einer Nachricht benutze die entsprechende Option auf der Allianzseite.";
	
	
	echo "<h2>Die neusten 10 Nachrichten</h2>";
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
		".$db_table['alliance_news']." 
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
		infobox_start("",1);
		echo "<tr>
						<th class=\"tbltitle\" style=\"width:400px;\">Titel</th>
						<th class=\"tbltitle\" style=\"width:150px;\">Datum</th>
						<th class=\"tbltitle\" style=\"width:250px;\">Absender</th>
						<th class=\"tbltitle\" style=\"width:80px;\">Text</th>
				</tr>";
		while ($anarr=mysql_fetch_array($anres))
		{
			$id = "th".$anarr['alliance_news_id'];
			$sid = "sth".$anarr['alliance_news_id'];				

			echo "<tr><td class=\"tbldata\">".text2html($anarr['alliance_news_title'])."</td>";
			echo "<td class=\"tbldata\">".df($anarr['alliance_news_date'])."</td>";
			if($anarr['afname']!="" && $anarr['aftag']!="")
			{
				echo "<td class=\"tbldata\" ".tm($anarr['aftag'],text2html($anarr['afname'])).">
					<a href=\"?page=alliance&amp;info_id=".$anarr['alliance_news_alliance_id']."\">".$anarr['aftag']."</a>
					
				</td>";
			}
			else
			{
				echo "<td class=\"tbldata\">(gel&ouml;scht)</td>";
			}
			echo "<td class=\"tbldata\">
			[<a href=\"javascript:;\" onclick=\"toggleText('".$id."','".$sid."');\" id=\"".$sid."\">Anzeigen</a>]
			</td></tr>";
			echo "<tr id=\"".$id."\" style=\"display:none;\">
				<td class=\"tbldata\" colspan=\"5\">".text2html(stripslashes($anarr['alliance_news_text']))."
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
		infobox_end(1);		
	}
	else
	{
		infobox_start(": Information :");
		echo "Es sind momentan keine Nachrichten vorhanden!";
		infobox_end();
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
		alliance_news_alliance_to_id=".$s['user']['alliance_id']." 
	ORDER BY 
		alliance_news_date DESC 
	;");
	if (mysql_num_rows($anres))
	{
		echo "<h2>Allianzinterne Nachrichten</h2>";
		infobox_start("",1);
		echo "<tr>
						<th class=\"tbltitle\" style=\"width:400px;\">Titel</th>
						<th class=\"tbltitle\" style=\"width:150px;\">Datum</th>
						<th class=\"tbltitle\" style=\"width:250px;\">Absender</th>
						<th class=\"tbltitle\" style=\"width:80px;\">Text</th>
				</tr>";
		while ($anarr=mysql_fetch_array($anres))
		{
			$id = "th".$anarr['alliance_news_id'];
			$sid = "sth".$anarr['alliance_news_id'];				

			echo "<tr><td class=\"tbldata\">".text2html($anarr['alliance_news_title'])."</td>";
			echo "<td class=\"tbldata\">".df($anarr['alliance_news_date'])."</td>";
			if($anarr['afname']!="" && $anarr['aftag']!="")
			{
				echo "<td class=\"tbldata\" ".tm($anarr['aftag'],text2html($anarr['afname'])).">
					<a href=\"?page=alliance&amp;info_id=".$anarr['alliance_news_alliance_id']."\">".$anarr['aftag']."</a>
					
				</td>";
			}
			else
			{
				echo "<td class=\"tbldata\">(gel&ouml;scht)</td>";
			}
			echo "<td class=\"tbldata\">
			[<a href=\"javascript:;\" onclick=\"toggleText('".$id."','".$sid."');\" id=\"".$sid."\">Anzeigen</a>]
			</td></tr>";
			echo "<tr id=\"".$id."\" style=\"display:none;\">
				<td class=\"tbldata\" colspan=\"5\">".text2html(stripslashes($anarr['alliance_news_text']))."
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
		infobox_end(1);		
	}

	//
	// Bündnisse
	//
	echo "<h2>Neuste Bündnisse</h2>";
	$res = dbquery("
	SELECT	
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
		echo "<table class=\"tb\">";
		echo "<tr>
			<th>Allianz 1</th>
			<th>Allianz 2</th>
			<th>Bündnisname</th>
			<th>Datum</th>
			<th>Erklärung</th>
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
				<td class=\"tbldata\" colspan=\"5\">".text2html(stripslashes($arr['alliance_bnd_text_pub']))."</td>
			</tr>";
		}		
		echo "</table>";
	}


	//
	// Kriege
	//
	echo "<h2>Aktuelle Kriege (Dauer ".round(WAR_DURATION/3600)."h)</h2>";
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
		echo "<table class=\"tb\">";
		echo "<tr>
						<th width=\"25%\">Allianz 1</th>
						<th width=\"25%\">Allianz 2</th>
						<th width=\"15%\">Start</th>
						<th width=\"15%\">Ende</th>
						<th width=\"20%\">Erklärung</th>
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
				<td class=\"tbldata\" colspan=\"5\">".text2html(stripslashes($arr['alliance_bnd_text_pub']))."</td>
			</tr>";
		}		
		echo "</table>";
	}
	else
	{
		echo "<i>Keine Kriege vorhanden!</i><br/>";
	}
	echo "<br/>";

	//
	// Friedensabkommen
	//
	echo "<h2>Aktuelle Friedensabkommen (Dauer ".round(PEACE_DURATION/3600)."h)</h2>";
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
		echo "<table class=\"tb\">";
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
		echo "</table>";
	}
	else
	{
		echo "<i>Keine Friedensabkommen vorhanden!</i><br/>";
	}
	echo "<br/>";



