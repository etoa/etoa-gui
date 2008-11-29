<?PHP

		if (isset($_GET['id']) && intval($_GET['id'])>0)
			$id = intval($_GET['id']);
		else
			$id = intval($_GET['info_id']);
		
		$infoAlly = new Alliance($id);
		if ($infoAlly->valid)
		{
			$infoAlly->visitsExt++;
			
			tableStart($infoAlly);	
			if ($infoAlly->image != "" && is_file($infoAlly->imageUrl))
			{
				$ims = getimagesize($infoAlly->imageUrl);
				echo "<tr>
								<td colspan=\"3\" style=\"text-align:center;background:#000\">
									<img src=\"".$infoAlly->imageUrl."\" alt=\"Allianz-Logo\" style=\"width:".$ims[0]."px;height:".$ims[1]."\" />
								</td>
							</tr>";
			}
			if ($infoAlly->motherId != 0)
			{
				echo "<tr>
								<th colspan=\"2\" style=\"text-align:center;\">
									Diese Allianz ist ein Wing von <b><a href=\"?page=$page&amp;action=info&amp;id=".$infoAlly->motherId."\">".$infoAlly->mother."</a></b>
								</th>
							</tr>";				
			}
			if ($infoAlly->text != "")
			{
				echo "<tr>
								<td colspan=\"2\" style=\"text-align:center;\">
									".text2html($infoAlly->text)."
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
		 		(alliance_bnd_alliance_id1='".$infoAlly->id."' 
		 		OR alliance_bnd_alliance_id2='".$infoAlly->id."') 
		 		AND alliance_bnd_level=3
		 	;");
			if (mysql_num_rows($wars)>0)
			{
				
				echo "<tr>
								<th>Kriege:</th>
								<td class=\"tbldata\">
									<table class=\"tbl\">
										<tr>
											<td class=\"tbltitle\" style=\"width:50%;\">Allianz</td>
											<td class=\"tbltitle\" style=\"width:25%;\">Von</td>
											<td class=\"tbltitle\" style=\"width:25%;\">Bis</td>
										</tr>";
						while ($war=mysql_fetch_array($wars))
						{
							if ($war['a1id']==$infoAlly->id) 
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
		 		(alliance_bnd_alliance_id1='".$infoAlly->id."' 
		 		OR alliance_bnd_alliance_id2='".$infoAlly->id."') 
		 		AND alliance_bnd_level=4
		 	;");
			if (mysql_num_rows($wars)>0)
			{			
				echo "<tr>
								<th>Friedensabkommen:</th>
								<td class=\"tbldata\">
									<table class=\"tbl\">
										<tr>
											<td class=\"tbltitle\" style=\"width:50%;\">Allianz</td>
											<td class=\"tbltitle\" style=\"width:25%;\">Von</td>
											<td class=\"tbltitle\" style=\"width:25%;\">Bis</td>
										</tr>";					
						while ($war=mysql_fetch_array($wars))
						{
							if ($war['a1id']==$infoAlly->id) 
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
		 		(alliance_bnd_alliance_id1='".$infoAlly->id."' 
		 		OR alliance_bnd_alliance_id2='".$infoAlly->id."') 
		 		AND alliance_bnd_level=2
		 	;");
			if (mysql_num_rows($wars)>0)
			{				
				echo "<tr>
								<th>Bündnisse:</th>
								<td class=\"tbldata\">
									<table class=\"tbl\">
										<tr>
											<td class=\"tbltitle\" style=\"width:50%;\">Allianz</td>
											<td class=\"tbltitle\" style=\"width:25%;\">Von</td>
											<td class=\"tbltitle\" style=\"width:25%;\">Bündnisname</td>
										</tr>";		

						while ($war=mysql_fetch_array($wars))
						{
							if ($war['a1id']==$infoAlly->id) 
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

			// Mitglieder
			echo "<tr>
							<th style=\"width:250px;\">Mitglieder:</th>
							<td id=\"members\">";
							echo $infoAlly->memberCount;
							if ($infoAlly->publicMemberList)
								echo " [<a href=\"javascript:;\" onclick=\"xajax_showAllianceMembers('".intval($id)."','members')\" >Anzeigen</a>]";
							echo "</td>
						</tr>";

			// Punkte
			echo "<tr>
							<th>Punkte / Durchschnitt:</th>
							<td>";
							echo nf($infoAlly->points)." / ".nf($infoAlly->avgPoints)."";
							echo "</td>
						</tr>";

			// Gründer
			echo "<tr>
							<th>Gr&uuml;nder:</th>
							<td class=\"tbldata\">
								<a href=\"?page=userinfo&amp;id=".$infoAlly->founderId."\">".$infoAlly->founder."</a>
							</td>
						</tr>";

			// Gründung
			echo "<tr>
							<th>Gründungsdatum:</th>
							<td class=\"tbldata\">
								".df($infoAlly->foundationDate)." (vor ".tf(time() - $infoAlly->foundationDate).")
							</td>
						</tr>";						
						
			// Url
			if ($infoAlly->url != "")
			{
				echo "<tr>
								<th>Website/Forum:</th>
								<td><b>".format_link($infoAlly->url)."</b></td>
							</tr>";
			}
			
			// Diverses
			echo "<tr>
							<th>Akzeptiert Bewerbungen:</tdh
							<td class=\"tbldata\">
								".($infoAlly->acceptApplications ? "Ja" : "Nein")."
							</td>
						</tr>";
			echo "<tr>
							<th>Akzeptiert Bündnissanfragen:</th>
							<td class=\"tbldata\">
								".($infoAlly->acceptPact ? "Ja" : "Nein")."
							</td>
						</tr>";		

			echo "</table>";
		}
		else
		{
			echo "Diese Allianz existiert nicht!";
		}
		
		echo "<br/><br/><input type=\"button\" onclick=\"history.back();;\" value=\"Zur&uuml;ck\" />";


?>