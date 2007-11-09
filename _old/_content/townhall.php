<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: messages.php													//
	// Topic: Nachrichten-Modul		 									//
	// Version: 0.1																	//
	// Letzte Änderung: 01.10.2004									//
	//////////////////////////////////////////////////

	// DEFINITIONEN //

	define(TOWNHALL_PUBLIC_COLOR,"#f90");
	define(TOWNHALL_PRIVATE_COLOR,"#9f9");

	echo "<h1>Ratshaus</h1>";

	if ($_GET['id']>0)
	{

		$res=dbquery("SELECT * FROM ".$db_table['alliance_news']." WHERE (alliance_news_public=1 OR alliance_news_alliance_to_id=".$_SESSION[ROUNDID]['user']['alliance_id'].") AND alliance_news_id=".$_GET['id']."");
		if (mysql_num_rows($res)==1)
		{
			$alliances = get_alliance_names();
			$arr=mysql_fetch_array($res);
			if ($arr['alliance_news_public']==1) 
				$public="<span style=\"color:".TOWNHALL_PUBLIC_COLOR.";\">&ouml;ffentlich</span>"; 
			else 
				$public="<span style=\"color:".TOWNHALL_PRIVATE_COLOR.";\">privat</span>";
			infobox_start(text2html($arr['alliance_news_title'])." ($public)",1);
			echo "<tr><td class=\"tbldata\" colspan=\"2\">".text2html(stripslashes($arr['alliance_news_text']))."</td></tr>";
			echo "<tr><th class=\"tbltitle\">Datum:</td><td class=\"tbldata\">".df($arr['alliance_news_date'])."</td></tr>";

			//überprüft ob diese allianz noch vorhanden ist, wenn nicht = (gelöscht)
			if($alliances[$arr['alliance_news_alliance_id']]['tag']=="")
			{
				$alliance_tag_from="(gel&ouml;scht)";
			}else{
				$alliance_tag_from=$alliances[$arr['alliance_news_alliance_id']]['tag'];
			}

			echo "<tr><th class=\"tbltitle\">Geschrieben von:</td><td class=\"tbldata\">".get_user_nick($arr['alliance_news_user_id']).", Angeh&ouml;riger der Allianz $alliance_tag_from";

			if ($arr['alliance_news_alliance_id']!=$arr['alliance_news_alliance_to_id'])
			{
				//überprüft ob diese allianz noch vorhanden ist, wenn nicht = (gelöscht)
				if($alliances[$arr['alliance_news_alliance_to_id']]['tag']=="")
                {
                    $alliance_tag_to="(gel&ouml;scht)";
                }else{
                    $alliance_tag_to=$alliances[$arr['alliance_news_alliance_to_id']]['tag'];
                }

				echo " an die Allianz $alliance_tag_to";
			}
			echo "</td></tr>";
			infobox_end(1);
		}
		echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
	}
	else
	{
		echo "Im Rathaus k&ouml;nnen Allianzen Nachrichten an ihre Mitglieder oder an andere Allianzen ver&ouml;ffentlichen; diese Nachrichten k&ouml;nnen &ouml;ffentlich gemacht oder nur f&uuml;r die Empf&auml;nger lesbar publiziert werden. Zum Verfassen einer Nachricht benutze die entsprechende Option auf der Allianzseite.<br/><br/>";
		$anres=dbquery("SELECT alliance_news_title,alliance_news_date,alliance_news_id,alliance_news_alliance_id,alliance_news_alliance_to_id,alliance_news_public FROM ".$db_table['alliance_news']." WHERE alliance_news_alliance_id=".$_SESSION[ROUNDID]['user']['alliance_id']." OR alliance_news_alliance_to_id=".$_SESSION[ROUNDID]['user']['alliance_id']." OR alliance_news_public=1 ORDER BY alliance_news_date DESC LIMIT 10;");
		if (mysql_num_rows($anres))
		{
			$alliances = get_alliance_names();
			infobox_start("",1);
			echo "<tr><th class=\"tbltitle\">Titel</th><th class=\"tbltitle\">Datum</th><th class=\"tbltitle\">Allianz</th><th class=\"tbltitle\">Empf&auml;nger</th><th class=\"tbltitle\">&nbsp;</th></tr>";
			while ($anarr=mysql_fetch_array($anres))
			{
				if ($anarr['alliance_news_public']==1) $style="color:".TOWNHALL_PUBLIC_COLOR.";"; else $style="color:".TOWNHALL_PRIVATE_COLOR.";";
				echo "<tr><td class=\"tbldata\" style=\"$style\">".text2html($anarr['alliance_news_title'])."</td>";
				echo "<td class=\"tbldata\" style=\"$style\">".df($anarr['alliance_news_date'])."</td>";
				if($alliances[$anarr['alliance_news_alliance_id']]['tag']==""){
					echo "<td class=\"tbldata\" style=\"$style\">(gel&ouml;scht)</td>";
				}else{
					echo "<td class=\"tbldata\" style=\"$style\">".$alliances[$anarr['alliance_news_alliance_id']]['tag']."</td>";
				}
				if ($anarr['alliance_news_public']==1){
					echo "<td class=\"tbldata\" style=\"$style\"> Alle </td>";
				}else{
					if($alliances[$anarr['alliance_news_alliance_to_id']]['tag']=="")
					{
						echo "<td class=\"tbldata\" style=\"$style\">(gel&ouml;scht)</td>";
					}
					else
					{
						echo "<td class=\"tbldata\" style=\"$style\">".$alliances[$anarr['alliance_news_alliance_to_id']]['tag']."</td>";
					}
				}
				echo "<td class=\"tbldata\" style=\"$style\"><a href=\"?page=townhall&id=".$anarr['alliance_news_id']."\">mehr</a></td></tr>";
			}
			infobox_end(1);
			echo "<b>Legende:</b> <span style=\"color:".TOWNHALL_PUBLIC_COLOR.";\">&ouml;ffentlich</span>, <span style=\"color:".TOWNHALL_PRIVATE_COLOR.";\">privat</span>";
		}
		else
		{
			infobox_start(": Information :");
			echo "Es sind momentan keine Nachrichten vorhanden!";
			infobox_end();
		}

	}



