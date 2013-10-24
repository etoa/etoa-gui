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

	echo "<h2>Umfragen</h2>";

	if (isset($_POST['vote_submit']) && checker_verify() && isset($_GET['vote']) && $_GET['vote']>0 && isset($_POST['poll_answer']) && $_POST['poll_answer']>0)
	{
		dbquery("UPDATE alliance_polls SET poll_a".$_POST['poll_answer']."_count=poll_a".$_POST['poll_answer']."_count+1 WHERE poll_alliance_id=".$arr['alliance_id']." AND poll_id=".$_GET['vote'].";");
		if (mysql_affected_rows()==1)
		{
			dbquery("INSERT INTO alliance_poll_votes (
				vote_poll_id,
				vote_user_id,
				vote_alliance_id,
				vote_number
			) VALUES (
			'".$_GET['vote']."',
			'".$cu->id."',
			'".$arr['alliance_id']."',
			'".$_POST['poll_answer']."'
			)");
		}
	}

	$pres=dbquery("
	SELECT
		*
	FROM
		alliance_polls
	WHERE
		poll_alliance_id=".$arr['alliance_id']."
	ORDER BY
		poll_timestamp DESC;");
	if (mysql_num_rows($pres)>0)
	{
		define("POLL_BAR_WIDTH",120);
		$chk=checker_init();
		while ($parr=mysql_fetch_array($pres))
		{
			$upres=dbquery("
			SELECT
				vote_id
			FROM
				alliance_poll_votes
			WHERE
				vote_poll_id=".$parr['poll_id']."
				AND vote_user_id=".$cu->id."
				AND vote_alliance_id=".$arr['alliance_id'].";");
			if (mysql_num_rows($upres)>0 || $parr['poll_active']==0)
			{
				tableStart(stripslashes($parr['poll_title']));
				echo "<tr><th colspan=\"2\">".stripslashes($parr['poll_question'])."</th></tr>";
				$num_votes = $parr['poll_a1_count']+$parr['poll_a2_count']+$parr['poll_a3_count']+$parr['poll_a4_count']+$parr['poll_a5_count']+$parr['poll_a6_count']+$parr['poll_a7_count']+$parr['poll_a8_count'];
				for ($x=1;$x<=8;$x++)
				{
					if ($parr['poll_a'.$x.'_text']!="")
					{
						echo "<tr><td>".stripslashes($parr['poll_a'.$x.'_text'])."</td>";
						if ($parr['poll_a'.$x.'_count']>0)
						{
							$p = 100/$num_votes*$parr['poll_a'.$x.'_count'];
							$iw = (POLL_BAR_WIDTH/$num_votes*$parr['poll_a'.$x.'_count'])+1;
						}
						else
						{
							$p = 0;
							$iw = 1;
						}
						$iiw = POLL_BAR_WIDTH-$iw;
						$img = "poll".$x;
						echo "<td style=\"width:250px;\"><img src=\"images/".$img.".jpg\" width=\"$iw\" height=\"10\" alt=\"Poll\" /><img src=\"images/blank.gif\" width=\"$iiw\" height=\"10\"> ".round($p,2)." % (".$parr['poll_a'.$x.'_count']." Stimmen)</td></tr>";
					}
				}
				tableEnd();
			}
			else
			{
				echo "<form action=\"?page=$page&amp;action=".$_GET['action']."&amp;vote=".$parr['poll_id']."\" method=\"post\">";
				echo $chk;
				tableStart(stripslashes($parr['poll_title']));
				echo "<tr><th colspan=\"2\" class=\"tbltitle\">".stripslashes($parr['poll_question'])."</th></tr>";
				for ($x=1;$x<=8;$x++)
				{
					if ($parr['poll_a'.$x.'_text']!="")
						echo "<tr><td class=\"tbldata\"><input type=\"radio\" name=\"poll_answer\" value=\"$x\" /> ".stripslashes($parr['poll_a'.$x.'_text'])."</td>";
				}
				tableEnd();
				echo "<input type=\"submit\" value=\"Abstimmen!\" name=\"vote_submit\"></form><br/><br/>";
			}
		}
	}
	else
		error_msg("Keine Umfragen vorhanden");
	echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
	

?>