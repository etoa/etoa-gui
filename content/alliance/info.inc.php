<?PHP

		if (isset($_GET['id']) && intval($_GET['id'])>0)
			$id = intval($_GET['id']);
		else
			$id = intval($_GET['info_id']);
		
		$res = dbquery("
		SELECT
			*
		FROM
			alliances
		WHERE
			alliance_id='".$id."';");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			dbquery("UPDATE alliances SET alliance_visits_ext=alliance_visits_ext+1 WHERE alliance_id='".$id."';");

			$member_count = mysql_num_rows(dbquery("
			SELECT 
				user_id 
			FROM 
				users
			WHERE 
				user_alliance_id='".$id."' 
				;"));
			
 			echo "<table width=\"500\" cellspacing=\"".TBL_SPACING."\" cellpadding=\"".TBL_PADDING."\" align=\"center\" class=\"tbl\">";
			echo "<tr>
							<td class=\"tbltitle\" colspan=\"2\" style=\"text-align:center;\">
								".stripslashes($arr['alliance_tag'])." ".stripslashes($arr['alliance_name'])."
							</td>
						</tr>";
						if ($arr['alliance_img']!="")
						{
							$im = ALLIANCE_IMG_DIR."/".$arr['alliance_img'];
							if (is_file($im))
							{
								$ims = getimagesize($im);
								echo "<tr>
												<td class=\"tblblack\" colspan=\"3\" style=\"text-align:center;background:#000\">
													<img src=\"".$im."\" alt=\"Allianz-Logo\" style=\"width:".$ims[0]."px;height:".$ims[1]."\" />
												</td>
											</tr>";
							}
						}
						if ($arr['alliance_text']!="")
						{
							echo "<tr>
											<td class=\"tbldata\" colspan=\"2\" style=\"text-align:center;\">
												".text2html($arr['alliance_text'])."
											</td>
										</tr>";
						}

			// Kriege
			$wars=dbquery("
			SELECT 
				a1.alliance_tag as a1tag,
				a1.alliance_name as a1name,
				a1.alliance_id as a1id,
				a2.alliance_tag as a2tag,
				a2.alliance_name as a2name,
				a2.alliance_id as a2id,
				alliance_bnd_date as date
			FROM 
				alliance_bnd
			INNER JOIN
				alliances as a1
				ON alliance_bnd_alliance_id1=a1.alliance_id
			INNER JOIN
				alliances as a2
				ON alliance_bnd_alliance_id2=a2.alliance_id
		 	WHERE 
		 		(alliance_bnd_alliance_id1='".$id."' 
		 		OR alliance_bnd_alliance_id2='".$id."') 
		 		AND alliance_bnd_level=3
		 	;");
			if (mysql_num_rows($wars)>0)
			{
				
				echo "<tr>
								<td class=\"tbltitle\">Kriege:</td>
								<td class=\"tbldata\">
									<table class=\"tbl\">
										<tr>
											<td class=\"tbltitle\" style=\"width:50%;\">Allianz</td>
											<td class=\"tbltitle\" style=\"width:25%;\">Von</td>
											<td class=\"tbltitle\" style=\"width:25%;\">Bis</td>
										</tr>";
						while ($war=mysql_fetch_array($wars))
						{
							if ($war['a1id']==$id) 
							{
								$opId = $war['a2id'];
								$opTag = $war['a2tag'];
								$opName = $war['a2name'];
							}
							else
							{
								$opId = $war['a1id'];
								$opTag = $war['a1tag'];
								$opName = $war['a1name'];
							}
							echo "<tr>
											<td class=\"tbldata\">
												[".$opTag."] ".$opName." 
												[<a href=\"?page=$page&amp;id=".$opId."\">Info</a>]
											</td>
											<td class=\"tbldata\">".df($war['date'])."</td>
											<td class=\"tbldata\">".df($war['date']+WAR_DURATION)."</td>
										</tr>";
						}
						echo "</table>
								</td>
							</tr>";
			}


			// Friedensabkommen
			$wars=dbquery("
			SELECT 
				a1.alliance_tag as a1tag,
				a1.alliance_name as a1name,
				a1.alliance_id as a1id,
				a2.alliance_tag as a2tag,
				a2.alliance_name as a2name,
				a2.alliance_id as a2id,
				alliance_bnd_date as date
			FROM 
				alliance_bnd
			INNER JOIN
				alliances as a1
				ON alliance_bnd_alliance_id1=a1.alliance_id
			INNER JOIN
				alliances as a2
				ON alliance_bnd_alliance_id2=a2.alliance_id
		 	WHERE 
		 		(alliance_bnd_alliance_id1='".$id."' 
		 		OR alliance_bnd_alliance_id2='".$id."') 
		 		AND alliance_bnd_level=4
		 	;");
			if (mysql_num_rows($wars)>0)
			{			
				echo "<tr>
								<td class=\"tbltitle\">Friedensabkommen:</td>
								<td class=\"tbldata\">
									<table class=\"tbl\">
										<tr>
											<td class=\"tbltitle\" style=\"width:50%;\">Allianz</td>
											<td class=\"tbltitle\" style=\"width:25%;\">Von</td>
											<td class=\"tbltitle\" style=\"width:25%;\">Bis</td>
										</tr>";					
						while ($war=mysql_fetch_array($wars))
						{
							if ($war['a1id']==$id) 
							{
								$opId = $war['a2id'];
								$opTag = $war['a2tag'];
								$opName = $war['a2name'];
							}
							else
							{
								$opId = $war['a1id'];
								$opTag = $war['a1tag'];
								$opName = $war['a1name'];
							}
							echo "<tr>
											<td class=\"tbldata\">
												[".$opTag."] ".$opName." 
												[<a href=\"?page=$page&amp;id=".$opId."\">Info</a>]
											</td>
											<td class=\"tbldata\">".df($war['date'])."</td>
											<td class=\"tbldata\">".df($war['date']+PEACE_DURATION)."</td>
										</tr>";				
						}
						echo "</table>
								</td>
							</tr>";
			}						

			// Bündnisse
			$wars=dbquery("
			SELECT 
				a1.alliance_tag as a1tag,
				a1.alliance_name as a1name,
				a1.alliance_id as a1id,
				a2.alliance_tag as a2tag,
				a2.alliance_name as a2name,
				a2.alliance_id as a2id,
				alliance_bnd_date as date,
				alliance_bnd_name as name
			FROM 
				alliance_bnd
			INNER JOIN
				alliances as a1
				ON alliance_bnd_alliance_id1=a1.alliance_id
			INNER JOIN
				alliances as a2
				ON alliance_bnd_alliance_id2=a2.alliance_id
		 	WHERE 
		 		(alliance_bnd_alliance_id1='".$id."' 
		 		OR alliance_bnd_alliance_id2='".$id."') 
		 		AND alliance_bnd_level=2
		 	;");
			if (mysql_num_rows($wars)>0)
			{				
				echo "<tr>
								<td class=\"tbltitle\">Bündnisse:</td>
								<td class=\"tbldata\">
									<table class=\"tbl\">
										<tr>
											<td class=\"tbltitle\" style=\"width:50%;\">Allianz</td>
											<td class=\"tbltitle\" style=\"width:25%;\">Von</td>
											<td class=\"tbltitle\" style=\"width:25%;\">Bündnisname</td>
										</tr>";		

						while ($war=mysql_fetch_array($wars))
						{
							if ($war['a1id']==$id) 
							{
								$opId = $war['a2id'];
								$opTag = $war['a2tag'];
								$opName = $war['a2name'];
							}
							else
							{
								$opId = $war['a1id'];
								$opTag = $war['a1tag'];
								$opName = $war['a1name'];
							}
							echo "<tr>
											<td class=\"tbldata\">
												[".$opTag."] ".$opName." 
												[<a href=\"?page=$page&amp;id=".$opId."\">Info</a>]
											</td>
											<td class=\"tbldata\">".df($war['date'])."</td>
											<td class=\"tbldata\">".stripslashes($war['name'])."</td>
										</tr>";							
													
						}
						echo "</table>
								</td>
							</tr>";
			}						

			// Url
			if ($arr['alliance_url']!="")
			{
				echo "<tr>
								<td class=\"tbltitle\" width=\"15%\">Website/Forum:</td>
								<td class=\"tbldata\"><b>".format_link($arr['alliance_url'])."</b></td>
							</tr>";
			}
			
			// Gründer
			echo "<tr>
							<td class=\"tbltitle\" width=\"20%\">Gr&uuml;nder:</td>
							<td class=\"tbldata\">
								<a href=\"?page=userinfo&amp;id=".$arr['alliance_founder_id']."\">".get_user_nick($arr['alliance_founder_id'])."</a>
							</td>
						</tr>";

			// Diverses
			echo "<tr>
							<td class=\"tbltitle\" width=\"20%\">Akzeptiert Bewerbungen:</td>
							<td class=\"tbldata\">
								".($arr['alliance_accept_applications']==1 ? "Ja" : "Nein")."
							</td>
						</tr>";
			echo "<tr>
							<td class=\"tbltitle\" width=\"20%\">Akzeptiert Bündnissanfragen:</td>
							<td class=\"tbldata\">
								".($arr['alliance_accept_bnd']==1 ? "Ja" : "Nein")."
							</td>
						</tr>";						
						
			
			echo "<tr>
							<td class=\"tbltitle\" width=\"20%\">Mitglieder:</td>
							<td class=\"tbldata\" id=\"members\">";
							echo $member_count." [<a href=\"javascript:;\" onclick=\"xajax_showAllianceMembers('".intval($id)."','members')\" >Anzeigen</a>]";
							echo "</td>
						</tr>";
			echo "</table>";
		}
		else
		{
			echo "Diese Allianz existiert nicht!";
		}
		
		echo "<br/><br/><input type=\"button\" onclick=\"history.back();;\" value=\"Zur&uuml;ck\" />";


?>