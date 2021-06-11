<?PHP

$xajax->register(XAJAX_FUNCTION,'showAllianceMembers');
$xajax->register(XAJAX_FUNCTION,'showAllianceMemberAddCosts');

//Listet User einer Allianz auf
function showAllianceMembers($alliance_id=0,$field_id)
{
	ob_start();
  $objResponse = new xajaxResponse();

	if($alliance_id!=0)
	{
		$members = "";
		$cnt = 0;
		$out = "Allianz-ID nicht angegeben!";

		$res = dbquery("
		SELECT
			user_id,
			user_nick,
			rank,
			rankshift,
			points,
			points_ships,
			points_tech,
			points_buildings,
			points_exp
		FROM
			users
		LEFT JOIN
			user_stats
			ON id=user_id
		WHERE
			user_alliance_id='".$alliance_id."'
		ORDER BY
			user_rank;");
		if (mysql_num_rows($res)>0)
		{
			while ($arr = mysql_fetch_array($res))
			{
				$cnt++;

				if ($arr['rankshift']==2)
				{
					$rank =  "<img src=\"images/stats/stat_up.gif\" alt=\"up\" width=\"9\" height=\"12\" />";
				}
				elseif ($arr['rankshift']==1)
				{
					$rank =  "<img src=\"images/stats/stat_down.gif\" alt=\"down\" width=\"9\" height=\"11\" />";
				}
				else
				{
					$rank =  "<img src=\"images/stats/stat_same.gif\" alt=\"same\" width=\"21\" height=\"9\" />";
				}

				$members .= "
				<tr>
					<td>
						".$arr['rank']."
					</td>
					<td>
						".$rank."
					</td>
					<td>
						<a href=\"?page=userinfo&id=".$arr['user_id']."\">".$arr['user_nick']."</a>
					</td>
					<td>
						".nf($arr['points'])."
					</td>
					<td>
						".nf($arr['points_buildings'])."
					</td>
					<td>
						".nf($arr['points_ships'])."
					</td>
					<td>
						".nf($arr['points_tech'])."
					</td>
					<td>
						".nf($arr['points_exp'])."
					</td>
				</tr>";
			}
			$out = "<table class=\"tbl\">
							<tr>
								<th width=\"5%\" colspan=\"2\">Rang</th>
								<th width=\"15%\">User</th>
								<th>Punkte</th>
								<th>Gebäude</th>
								<th>Flotten</th>
								<th>Tech</th>
								<th>XP</th>
							</tr>
							".$members."
							</table>";
		}
	}


	$objResponse->assign($field_id, "innerHTML", $out);


	$objResponse->assign("allianceinfo","innerHTML",ob_get_contents());
	ob_end_clean();

	return $objResponse;
}

function showAllianceMemberAddCosts($allianceId=0,$form)
{
	ob_start();
	$objResponse = new xajaxResponse();
	$cnt = 0;

	foreach ($form['application_answer'] as $answear)
	{
		if ($answear==2) $cnt++;
	}
	if($allianceId!=0)
	{
		$alliance = new Alliance($allianceId);

		echo $alliance->calcMemberCosts(false,$cnt);
	}

	$objResponse->assign("memberCostsTD","innerHTML",ob_get_contents());
	if ($cnt>0)
	{
		$objResponse->assign("memberCosts","style.display",'');
	}
	else
	{
		$objResponse->assign("memberCosts","style.display",'none');
	}
	ob_end_clean();

	return $objResponse;

}


?>
