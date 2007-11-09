<?PHP

//Listet User einer Allianz auf
function showAllianceMembers($alliance_id=0,$field_id)
{
	ob_start();
  $objResponse = new xajaxResponse();
  
  global $db_table;
	
	if($alliance_id!=0)
	{
		$members = "";
		$cnt = 0;
		$out = "Allianz-ID nicht angegeben!";
		
		$res = dbquery("
		SELECT 
			user_id,
			user_nick,
			user_points,
			user_points_ships,
			user_points_tech,
			user_points_buildings,
			user_rank_current,
			user_rank_last
		FROM 
			".$db_table['users']."
		WHERE
			user_alliance_id='".$alliance_id."' 
			AND user_alliance_application=''
		ORDER BY 
			user_rank_current;");
		if (mysql_num_rows($res)>0)
		{
			while ($arr = mysql_fetch_array($res))
			{
				$cnt++;
				
				if ($arr['user_rank_current']==$arr['user_rank_last'])
				{
					$rank =  "<img src=\"images/stats/stat_same.gif\" alt=\"same\" width=\"21\" height=\"9\" />";
				}
				elseif ($arr['user_rank_current']<$arr['user_rank_last'])
				{
					$rank =  "<img src=\"images/stats/stat_up.gif\" alt=\"up\" width=\"9\" height=\"12\" />";
				}
				elseif ($arr['user_rank_current']>$arr['user_rank_last'])
				{
					$rank =  "<img src=\"images/stats/stat_down.gif\" alt=\"down\" width=\"9\" height=\"11\" />";
				}
				
				$members .= "
				<tr>
					<td class=\"tbldata\">
						".$arr['user_rank_current']."
					</td>
					<td class=\"tbldata\">
						".$rank."
					</td>
					<td class=\"tbldata\">
						<a href=\"?page=userinfo&id=".$arr['user_id']."\">".$arr['user_nick']."</a>
					</td>
					<td class=\"tbldata\">
						".nf($arr['user_points'])."
					</td>
					<td class=\"tbldata\">
						".nf($arr['user_points_buildings'])."
					</td>
					<td class=\"tbldata\">
						".nf($arr['user_points_ships'])."
					</td>
					<td class=\"tbldata\">
						".nf($arr['user_points_tech'])."
					</td>
				</tr>";
			}
			$out = "<table class=\"tbl\">
							<tr>
								<td class=\"tbltitle\" width=\"5%\" colspan=\"2\">Rang</td>
								<td class=\"tbltitle\" width=\"15%\">User</td>
								<td class=\"tbltitle\" width=\"20%\">Punkte Total</td>
								<td class=\"tbltitle\" width=\"20%\">Gebäude</td>
								<td class=\"tbltitle\" width=\"20%\">Flotten</td>
								<td class=\"tbltitle\" width=\"20%\">Technologien</td>
							</tr>
							".$members."
							</table>";
			//$out = "".$cnt." Mitglieder:<br><br>".$members."";
		}
	}
	
	
	$objResponse->addAssign($field_id, "innerHTML", $out);
	
	
	$objResponse->addAssign("allianceinfo","innerHTML",ob_get_contents());
	ob_end_clean();
	
	return $objResponse->getXML();
}

$objAjax->registerFunction('showAllianceMembers');
?>