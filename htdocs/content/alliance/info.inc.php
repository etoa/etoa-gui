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

		if (isset($_GET['id']) && intval($_GET['id'])>0)
			$id = intval($_GET['id']);
		else
			$id = intval($_GET['info_id']);
		
		$infoAlly = new Alliance($id);
		if ($infoAlly->valid)
		{
			if ($cu->allianceId != $infoAlly->id)
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
				alliance_bnd_alliance_id1 as a1id,
				alliance_bnd_alliance_id2 as a2id,
				alliance_bnd_date as date
			FROM 
				alliance_bnd
		 	WHERE 
		 		alliance_bnd_level=3
		 		AND
		 		(alliance_bnd_alliance_id1='".$infoAlly->id."' 
		 		OR alliance_bnd_alliance_id2='".$infoAlly->id."') 
		 	;");
			if (mysql_num_rows($wars)>0)
			{
				
				echo "<tr>
								<th>Kriege:</th>
								<td>
									<table class=\"tbl\">
										<tr>
											<th>Allianz</th>
											<th>Punkte</th>
											<th>Zeitraum</th>
										</tr>";
						while ($war=mysql_fetch_array($wars))
						{
							if ($war['a1id']==$infoAlly->id) 
								$opAlly = new Alliance($war['a2id']);
							else
								$opAlly = new Alliance($war['a1id']);
							echo "<tr>
											<td>
												<a href=\"?page=$page&amp;id=".$opAlly->id."\">".$opAlly."</a>
											</td>
											<td>".nf($opAlly->points)." / ".nf($opAlly->avgPoints)."</td>
											<td>".df($war['date'],0)." bis ".df($war['date']+WAR_DURATION,0)."</td>
										</tr>";
						}
						echo "</table>
								</td>
							</tr>";
			}


			// Friedensabkommen
			$wars=dbquery("
			SELECT 
				alliance_bnd_alliance_id1 as a1id,
				alliance_bnd_alliance_id2 as a2id,
				alliance_bnd_date as date
			FROM 
				alliance_bnd
		 	WHERE 
		 		alliance_bnd_level=4
		 		AND 
		 		(alliance_bnd_alliance_id1='".$infoAlly->id."' 
		 		OR alliance_bnd_alliance_id2='".$infoAlly->id."') 
		 		
		 	;");
			if (mysql_num_rows($wars)>0)
			{			
				echo "<tr>
								<th>Friedensabkommen:</th>
								<td>
									<table class=\"tbl\">
										<tr>
											<th>Allianz</th>
											<th>Punkte</th>
											<th>Zeitraum</th>
										</tr>";					
						while ($war=mysql_fetch_array($wars))
						{
							if ($war['a1id']==$infoAlly->id) 
								$opAlly = new Alliance($war['a2id']);
							else
								$opAlly = new Alliance($war['a1id']);
							echo "<tr>
											<td>
												<a href=\"?page=$page&amp;id=".$opAlly->id."\">".$opAlly."</a>
											</td>
											<td>".nf($opAlly->points)." / ".nf($opAlly->avgPoints)."</td>
											<td>".df($war['date'],0)." bis ".df($war['date']+PEACE_DURATION,0)."</td>
										</tr>";				
						}
						echo "</table>
								</td>
							</tr>";
			}						

			// Bündnisse
			$wars=dbquery("
			SELECT 
				alliance_bnd_alliance_id1 as a1id,
				alliance_bnd_alliance_id2 as a2id,
				alliance_bnd_date as date,
				alliance_bnd_name as name
			FROM 
				alliance_bnd
		 	WHERE 
		 		alliance_bnd_level=2
		 		AND 
		 		(alliance_bnd_alliance_id1='".$infoAlly->id."' 
		 		OR alliance_bnd_alliance_id2='".$infoAlly->id."') 
		 	;");
			if (mysql_num_rows($wars)>0)
			{				
				echo "<tr>
								<th>Bündnisse:</th>
								<td>
									<table class=\"tbl\">
										<tr>
											<th>Bündnisname</th>
											<th>Allianz</th>
											<th>Punkte</th>
											<th>Seit</th>
										</tr>";		

						while ($war=mysql_fetch_array($wars))
						{
							if ($war['a1id']==$infoAlly->id) 
								$opAlly = new Alliance($war['a2id']);
							else
								$opAlly = new Alliance($war['a1id']);
							echo "<tr>
											<td>".stripslashes($war['name'])."</td>
											<td><a href=\"?page=$page&amp;id=".$opAlly->id."\">".$opAlly."</a></td>
											<td>".nf($opAlly->points)." / ".nf($opAlly->avgPoints)."</td>											
											<td>".df($war['date'])."</td>
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
							<td>
								<a href=\"?page=userinfo&amp;id=".$infoAlly->founderId."\">".$infoAlly->founder."</a>
							</td>
						</tr>";

			// Gründung
			echo "<tr>
							<th>Gründungsdatum:</th>
							<td>
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
							<th>Akzeptiert Bewerbungen:</th>
							<td>
								".($infoAlly->acceptApplications ? "Ja" : "Nein")."
							</td>
						</tr>";
			echo "<tr>
							<th>Akzeptiert Bündnissanfragen:</th>
							<td>
								".($infoAlly->acceptPact ? "Ja" : "Nein")."
							</td>
						</tr>";		

			echo "</table>";
		}
		else
		{
			error_msg("Diese Allianz existiert nicht!");
		}
		
		echo "<br/><br/><input type=\"button\" onclick=\"history.back();;\" value=\"Zur&uuml;ck\" />";


?>