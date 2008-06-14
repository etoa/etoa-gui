<?PHP

$xajax->register(XAJAX_FUNCTION,'statsShowBox');
$xajax->register(XAJAX_FUNCTION,'statsShowTable');
 

function statsShowBox($mode, $sort="", $sortOrder="")
{
	global $db_table, $page, $conf;
  $objResponse = new xajaxResponse();

	$_SESSION['statsmode']=$mode;

	$out="";
	
	//
	// Allianzdaten
	//
	if ($mode=="alliances")
	{
		$out.= "<table class=\"tbl\"><tr>";
		$out.= "<th class=\"tbltitle\" colspan=\"2\">Rang</th>";
		$out.= "<th class=\"tbltitle\">Tag</th>";
		$out.= "<th class=\"tbltitle\">Name</th>";
		if ($sort=="upoints")
			$out.= "<th class=\"tbltitle\"><i>Punkte</i> ";
		else
			$out.= "<th class=\"tbltitle\">Punkte ";
		$out.= "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','upoints','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		$out.= "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','upoints','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
		if ($sort=="uavg")
			$out.= "<th class=\"tbltitle\"><i>User-Schnitt</i> ";
		else
			$out.= "<th class=\"tbltitle\">User-Schnitt ";
		$out.= "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','uavg','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		$out.= "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','uavg','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
		$out.= "</th>";
		if ($sort=="cnt")
			$out.= "<th class=\"tbltitle\"><i>User</i> ";
		else
			$out.= "<th class=\"tbltitle\">User ";
		$out.= "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','cnt','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		$out.= "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','cnt','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";

		$out.= "<th class=\"tbltitle\">Aktionen</th>";
		$out.= "</tr>";
		if ($sort!="" && $sortOrder!="")
			$sql="
			SELECT
				*
			FROM
       			alliance_stats
			ORDER BY
				".$sort." ".$sortOrder.",
				alliance_name ASC;";
		else
		{
			$sql="
			SELECT 
				*
			FROM 
				alliance_stats
			ORDER BY 
				upoints DESC,
				alliance_name ASC
			;";
		}
		
		//$out.=$sql;
		$res=dbquery($sql);
		if (mysql_num_rows($res)>0)
		{
			$cnt=1;
			while ($arr=mysql_fetch_array($res))
			{
				if($_SESSION[ROUNDID]['user']['alliance_id']==$arr['alliance_id'] && $arr['alliance_id']!='0' && $arr['user_alliance_application']=="" && $_SESSION[ROUNDID]['user']['alliance_application']==0)
					$style="style=\"color:".COLOR_ALLIANCE.";\"";
				else
					$style="";
				$out.= "<tr>";
				$out.= "<td class=\"tbldata\" align=\"right\" $style>".nf2($cnt)."</td>";
				$out.=  "<td $addstyle class=\"tbldata\" align=\"right\" ".tm("Punkteverlauf","<div><img src=\"misc/alliance_stats.image.php?alliance=".$arr['alliance_id']."\" alt=\"Diagramm\" style=\"width:600px;height:400px;background:#335 url(images/loading335.gif) no-repeat 300px 200px;\" /></div>").">";
				if ($arr['alliance_rank_current']==$arr['alliance_rank_last'])
					$out.=  "<img src=\"images/stats/stat_same.gif\" alt=\"same\" width=\"21\" height=\"9\" />";
				elseif ($arr['alliance_rank_current']<$arr['alliance_rank_last'])
					$out.=  "<img src=\"images/stats/stat_down.gif\" alt=\"up\" width=\"9\" height=\"12\" />";
				elseif ($arr['alliance_rank_current']>$arr['alliance_rank_last'])
					$out.=  "<img src=\"images/stats/stat_up.gif\" alt=\"down\" width=\"9\" height=\"11\" />";
				$out.= "<td class=\"tbldata\" $style>".(text2html($arr['alliance_tag']))."</td>";
				$out.= "<td class=\"tbldata\" $style>".text2html($arr['alliance_name'])."</td>";
				$out.= "<td class=\"tbldata\" $style>".nf2($arr['upoints'])."</td>";
				$out.= "<td class=\"tbldata\" $style>".nf2($arr['uavg'])."</td>";
				$out.= "<td class=\"tbldata\" $style>".nf2($arr['cnt'])."</td>";
				$out.= "<td class=\"tbldata\"><a href=\"?page=alliance&amp;info_id=".$arr['alliance_id']."\">Info</a> <a href=\"?page=$page&amp;mode=$mode&amp;limit=".$_GET['limit']."&amp;alliancedetail=".$arr['alliance_id']."\">Punktedetails</a></td>";
				$out.= "</tr>";
				$cnt++;
			}
		}
		else
		{
			$out.= "<tr><td colspan=\"8\" align=\"center\" class=\"tbldata\"><i>Keine Allianzen in der Statistik</i></tr>";
		}
	 	$objResponse->assign('statsBox', 'innerHTML', $out);
		$out.="</table>";
	}
	
	//
	// Pranger
	//
	elseif ($mode=="pillory")
	{
		$res = dbquery("SELECT 
			u.user_nick,
			u.user_blocked_from,
			u.user_blocked_to,
			u.user_ban_reason, 
			a.user_nick AS admin_nick,
			a.user_email AS admin_email
		FROM 
			".$db_table['users']." AS u
		LEFT JOIN
			".$db_table['admin_users']." AS a
		ON
			u.user_ban_admin_id = a.user_id
		WHERE 
			u.user_blocked_from<".time()." 
			AND u.user_blocked_to>".time()." 
		ORDER BY 
			u.user_blocked_from DESC;");
		$out.= "<table class=\"tbl\"><tr><td class=\"tbltitle\">Nick</td><td class=\"tbltitle\">Von:</td><td class=\"tbltitle\">Bis:</td><td class=\"tbltitle\">Admin</td><td class=\"tbltitle\">Grund der Sperrung</td></tr>";
		if (mysql_num_rows($res)>0)
		{
			while ($arr = mysql_fetch_array($res))
			{
				$out.="<tr>
				<td class=\"tbldata\" valign=\"top\" width=\"90\">".$arr['user_nick']."</td>
				<td class=\"tbldata\" valign=\"top\">".df($arr['user_blocked_from'])."</td>
				<td class=\"tbldata\" valign=\"top\">".df($arr['user_blocked_to'])."</td>
				<td class=\"tbldata\"><a href=\"mailto:".$arr['admin_email']."\">".$arr['admin_nick']."</a></td>
				<td class=\"tbldata\">".text2html($arr['user_ban_reason'])."</td>
				</tr>";
			}
		}
		else
			$out.= "<tr><td class=\"tbldata\" colspan=\"5\"><i>Keine Eintr&auml;ge vorhanden</i></tr>";
		$out.="</table>";
	 	$objResponse->assign('statsBox', 'innerHTML', $out);
	}

	//
	// Special Points
	//
	elseif($mode=="diplomacy" || $mode=="battle" || $mode=="trade")
	{
		ob_start();		
		
		if ($mode=="diplomacy")
		{
			$res=dbquery("
			SELECT 
				r.diplomacy_rating,
				user_id AS id,
				user_nick,
				race_name,
				alliance_tag		
			FROM 
				users
			INNER JOIN
				races ON user_race_id=race_id
			INNER JOIN
				user_ratings as r ON user_id=r.id
			LEFT JOIN 
				alliances ON user_alliance_id=alliance_id
			ORDER BY 
				battle_rating DESC
			;");
			echo "<table class=\"tb\">";
			echo "<tr><th colspan=\"9\" style=\"text-align:center\">Diplomatiewertung</th></tr>";
			$cnt=1;
			if (mysql_num_rows($res)>0)
			{
				echo "<tr>
					<th style=\"width:50px;\">#</th>
					<th style=\"\">Nick</th>
					<th style=\"\">Rasse</th>
					<th style=\"\">Allianz</th>
					<th style=\"\">Bewertung</th>
					<th style=\"width:160px;\">Details</th>
				</tr>";
				while ($arr=mysql_fetch_array($res))
				{
					if ($arr['diplomacy_rating']>0)
					{
						echo "<tr>";
						echo "<td>".$cnt."</td>";
						echo "<td>".$arr['user_nick']."</td>";
						echo "<td>".$arr['race_name']."</td>";
						echo "<td >".$arr['alliance_tag']."</td>";
						echo "<td>".nf($arr['diplomacy_rating'])."</td>";
						echo "<td>";
						echo "<a href=\"?page=userinfo&id=".$arr['id']."\" title=\"Userinfo\">Info</a> ";
						echo "<a href=\"?page=$page&amp;mode=$mode&amp;userdetail=".$arr['id']."\">Verlauf</a> ";
						if ($arr['id']!=$_SESSION[ROUNDID]['user_id'])
						{
							echo "<a href=\"?page=messages&mode=new&message_user_to=".$arr['id']."\" title=\"Nachricht senden\">Mail</a> ";
							echo "<a href=\"?page=buddylist&add_id=".$arr['id']."\" title=\"Info\">Buddy</a></td>";
						}
						echo "</td>";
						echo "</tr>";
						$cnt++;
					}
				}
			}
			if ($cnt==1)
			{
				echo "<tr><td align=\"center\" colspan=\"8\"><i>Es wurde keine User gefunden!</i></tr>";
			}
			echo "</table><br/>";											
		}
		elseif ($mode=="battle")
		{
			$res=dbquery("
			SELECT 
				r.battles_fought,
				r.battles_won,
				r.battles_lost,
				r.battle_rating,
				user_id AS id,
				user_nick,
				race_name,
				alliance_tag		
			FROM 
				users
			INNER JOIN
				races ON user_race_id=race_id
			INNER JOIN
				user_ratings as r ON user_id=r.id
			LEFT JOIN 
				alliances ON user_alliance_id=alliance_id
			ORDER BY 
				battle_rating DESC
			;");
			echo "<table class=\"tb\">";
			echo "<tr><th colspan=\"9\" style=\"text-align:center\">Kampfwertung</th></tr>";
			$cnt=1;
			if (mysql_num_rows($res)>0)
			{
				echo "<tr>
					<th style=\"width:50px;\">#</th>
					<th style=\"\">Nick</th>
					<th style=\"\">Rasse</th>
					<th style=\"\">Allianz</th>
					<th style=\"\">Kämpfe Gewonnen</th>
					<th style=\"\">Kämpfe Verloren</th>
					<th style=\"\">Kämpfe Total</th>
					<th style=\"\">Bewertung</th>
					<th style=\"width:160px;\">Details</th>
				</tr>";
				while ($arr=mysql_fetch_array($res))
				{
					if ($arr['battle_rating']>0)
					{
						echo "<tr>";
						echo "<td>".$cnt."</td>";
						echo "<td>".$arr['user_nick']."</td>";
						echo "<td>".$arr['race_name']."</td>";
						echo "<td >".$arr['alliance_tag']."</td>";
						echo "<td>".nf($arr['battles_won'])."</td>";
						echo "<td>".nf($arr['battles_lost'])."</td>";
						echo "<td>".nf($arr['battles_fought'])."</td>";
						echo "<td>".nf($arr['battle_rating'])."</td>";
						echo "<td>";
						echo "<a href=\"?page=userinfo&id=".$arr['id']."\" title=\"Userinfo\">Info</a> ";
						echo "<a href=\"?page=$page&amp;mode=$mode&amp;userdetail=".$arr['id']."\">Verlauf</a> ";
						if ($arr['id']!=$_SESSION[ROUNDID]['user_id'])
						{
							echo "<a href=\"?page=messages&mode=new&message_user_to=".$arr['id']."\" title=\"Nachricht senden\">Mail</a> ";
							echo "<a href=\"?page=buddylist&add_id=".$arr['id']."\" title=\"Info\">Buddy</a></td>";
						}
						echo "</td>";
						echo "</tr>";
						$cnt++;
					}
				}
			}
			if ($cnt==1)
			{
				echo "<tr><td align=\"center\"  colspan=\"9\"><i>Es wurde keine User gefunden!</i></tr>";
			}
			echo "</table><br/>";											
		}
		elseif ($mode=="trade")
		{
			$res=dbquery("
			SELECT 
				r.trades_sell,
				r.trades_buy,
				r.trade_rating,
				user_id AS id,
				user_nick,
				race_name,
				alliance_tag		
			FROM 
				users
			INNER JOIN
				races ON user_race_id=race_id
			INNER JOIN
				user_ratings as r ON user_id=r.id
			LEFT JOIN 
				alliances ON user_alliance_id=alliance_id
			ORDER BY 
				battle_rating DESC
			;");
			echo "<table class=\"tb\">";
			echo "<tr><th colspan=\"9\" style=\"text-align:center\">Handelswertung</th></tr>";
			$cnt=1;
			if (mysql_num_rows($res)>0)
			{
				echo "<tr>
					<th style=\"width:50px;\">#</th>
					<th style=\"\">Nick</th>
					<th style=\"\">Rasse</th>
					<th style=\"\">Allianz</th>
					<th style=\"\">Einkäufe</th>
					<th style=\"\">Verkäufe</th>
					<th style=\"\">Bewertung</th>
					<th style=\"width:160px;\">Details</th>
				</tr>";
				while ($arr=mysql_fetch_array($res))
				{
					if ($arr['trade_rating']>0)
					{
						echo "<tr>";
						echo "<td>".$cnt."</td>";
						echo "<td>".$arr['user_nick']."</td>";
						echo "<td>".$arr['race_name']."</td>";
						echo "<td >".$arr['alliance_tag']."</td>";
						echo "<td>".nf($arr['trades_buy'])."</td>";
						echo "<td>".nf($arr['trades_sell'])."</td>";
						echo "<td>".nf($arr['trade_rating'])."</td>";
						echo "<td>";
						echo "<a href=\"?page=userinfo&id=".$arr['id']."\" title=\"Userinfo\">Info</a> ";
						echo "<a href=\"?page=$page&amp;mode=$mode&amp;userdetail=".$arr['id']."\">Verlauf</a> ";
						if ($arr['id']!=$_SESSION[ROUNDID]['user_id'])
						{
							echo "<a href=\"?page=messages&mode=new&message_user_to=".$arr['id']."\" title=\"Nachricht senden\">Mail</a> ";
							echo "<a href=\"?page=buddylist&add_id=".$arr['id']."\" title=\"Info\">Buddy</a></td>";
						}
						echo "</td>";
						echo "</tr>";
						$cnt++;
					}
				}
			}
			if ($cnt==1)
			{
				echo "<tr><td align=\"center\" colspan=\"8\"><i>Es wurde keine User gefunden!</i></tr>";
			}
			echo "</table><br/>";											
		}
	
		$out.= ob_get_contents();
		ob_end_clean();
	 	$objResponse->assign('statsBox', 'innerHTML', $out);
	}		


	
	//
	// Titles
	//
	elseif ($mode=="titles")
	{
		ob_start();
		if (!@include(CACHE_ROOT."/out/usertitles.gen"))
		{
			echo "<b>Fehler! Die Liste wurde noch nicht erstellt! Bitte das nächste Statistikupdate abwarten.<br/><br/>"; 	
		}
		$out.= ob_get_contents();
		ob_end_clean();
	 	$objResponse->assign('statsBox', 'innerHTML', $out);
	}

	
	// 
	// Normal Stats
	//
	else
	{
		$out.= "<table class=\"tbl\">
			<tr>
				<td style=\"width:470px;text-align:left;\" class=\"statsNav\"><b>&nbsp;&nbsp;Suche:</b> 
					<input type=\"text\" name=\"user_nick\" autocomplete=\"off\" value=\"\" size=\"\" onkeyup=\"statsSearchLoader();xajax_statsShowTable('$mode',0,this.value);\" id=\"searchString\"/>
					<input type=\"button\" onclick=\"getElementById('searchString').value='';xajax_statsShowTable('$mode');\" value=\"Reset\" />
					<input type=\"button\" onclick=\"xajax_statsShowTable('$mode',0,'".$_SESSION[ROUNDID]['user_nick']."',1);\" value=\"".$_SESSION[ROUNDID]['user_nick']."\" />
		</td>";
		$out.= "<td class=\"statsNav\" id=\"statsNav1\" style=\"text-align:right;\">";
		// >> AJAX generated content here
		$out.= "</td></tr>";
		$out.="<tr><td colspan=\"2\" style=\"margin:0px;padding:0px;\" id=\"statsTable\">" .
		"<div style=\"padding:20px;background:#335\"><img src=\"images/loading335.gif\" alt=\"Loading\" /> Lade Statistiktabelle...</div>";
		// >> AJAX generated content here
		$out.= "</td></tr>";
		$out.= "<tr>
			<td style=\"width:470px;text-align:left;\" class=\"statsNav\"></td>
			<td id=\"statsNav2\" style=\"text-align:right;\" class=\"statsNav\">";
		// >> AJAX generated content here
		$out.= "</td></tr></table>";
	 	$objResponse->assign('statsBox', 'innerHTML', $out);
		$objResponse->script("xajax_statsShowTable('$mode');");
	}

  	return $objResponse;
}


function statsShowTable($mode, $limit=0, $userstring="", $absolute=0)
{
	global $db_table, $page;
  $objResponse = new xajaxResponse();
		// Datensatznavigation
		$res = dbquery("
		SELECT 
			COUNT(id) 
		FROM 
			user_stats;");
		$usrcnt = mysql_fetch_row($res);
		$num = $usrcnt[0];
		
		if ($limit>0)
		{
			$limit = $limit.",".NUM_OF_ROWS;
			$nextlimit = $limit+NUM_OF_ROWS;
			$prevlimit = $limit-NUM_OF_ROWS;
		}
		else
		{
			$limit = "0,".NUM_OF_ROWS;
			$nextlimit = NUM_OF_ROWS;
			$prevlimit = -1;
		}
		$lastlimit = (ceil($num/NUM_OF_ROWS)*NUM_OF_ROWS)-NUM_OF_ROWS;

		// Navigationsfeld
		$out="<table style=\"width:100%\">";
		if ($prevlimit>-1 && NUM_OF_ROWS*2<$num)
			$out.= "<td style=\"width:40px;text-align:center;\"><input type=\"button\" value=\"&lt;&lt;\" onclick=\"xajax_statsShowTable('$mode',0)\"></td>";
		else
			$out.= "<td style=\"width:40px;\"></td>";
		if ($prevlimit>-1)
			$out.= "<td style=\"width:40px;text-align:center;\"><input type=\"button\" value=\"&lt;\" onclick=\"xajax_statsShowTable('$mode',$prevlimit)\"></td>";
		else
			$out.= "<td style=\"width:40px;\"></td>";
		if ($nextlimit<$num)
			$out.= "<td style=\"width:40px;text-align:center;\"><input type=\"button\" value=\"&gt;\" onclick=\"xajax_statsShowTable('$mode',$nextlimit)\"></td>";
		else
			$out.= "<td style=\"width:40px;\"></td>";
		if ($nextlimit<$num && NUM_OF_ROWS*2<$num)
			$out.= "<td style=\"width:40px;text-align:center;\"><input type=\"button\" value=\"&gt;&gt;\" onclick=\"xajax_statsShowTable('$mode',$lastlimit)\"></td>";
		else
			$out.= "<td style=\"width:40px;\"></td>";
		$out.= "<td><select onchange=\"xajax_statsShowTable('$mode',this.options[this.selectedIndex].value)\">";
		for ($x=1;$x<=$num;$x+=NUM_OF_ROWS)
		{
			$dif = $x+NUM_OF_ROWS-1;
			if ($dif>$num) $dif=$num;
			$oval=$x-1;
			$out.= "<option value=\"$oval\"";
			if ($limit==$oval) $out.= " selected=\"selected\"";
			$out.= ">$x - $dif</option>";
		}
		$out.= "</select></td></table>";
 		$objResponse->assign('statsNav1', 'innerHTML', $out);
 		$objResponse->assign('statsNav2', 'innerHTML', $out);

		// Punktetabelle
		$out="<table class=\"tb\">";
		
		if ($num > 0)
		{

			// Punktetabelle
			if ($mode=="ships")
			{
				$field="points_ships";
				$order="rank_ships";
				$title="Schiffspunkte";
				$shift="rankshift_ships";
			}
			elseif ($mode=="tech")
			{
				$field="points_tech";
				$order="rank_tech";
				$title="Technologiepunkte";
				$shift="rankshift_tech";
			}
			elseif ($mode=="buildings")
			{
				$field="points_buildings";
				$order="rank_buildings";
				$title="Gebäudepunkte";
				$shift="rankshift_buildings";
			}
			elseif ($mode=="exp")
			{
				$field="points_exp";
				$order="rank_exp";
				$title="Erfahrungspunkte";
				$shift="rankshift_exp";
			}
			else
			{
				$field="points";
				$order="rank";
				$title="Gesamtpunkte";
				$shift="rankshift";
			}

			if ($userstring!="")
			{
				$limit="0,".NUM_OF_ROWS;
				$userstring=remove_illegal_signs($userstring);
				if ($absolute==1)
				{
					$sql="
					SELECT 
						id,
						nick,
						blocked,
						hmod,
						inactive,
						".$order." AS rank,
						".$field." AS points,
						".$shift." AS shift,
						race_name,
						alliance_tag,
						sx,
						sy
					FROM 
						user_stats
					WHERE 
	         	LCASE(nick) LIKE '".strtolower($userstring)."' 
					ORDER BY 
						$order ASC,
						nick ASC 
					LIMIT $limit;";
				}
				else
				{
					$sql="
					SELECT 
						id,
						nick,
						blocked,
						hmod,
						inactive,
						".$order." AS rank,
						".$field." AS points,
						".$shift." AS shift,
						race_name,
						alliance_tag,
						sx,
						sy
					FROM 
						user_stats
					WHERE 
	       		LCASE(nick) LIKE '".strtolower($userstring)."%' 
					ORDER BY 
						$order ASC,
						nick ASC 
					LIMIT $limit;";
				}
			}
			else
			{
				$sql="
				SELECT 
					id,
					nick,
					blocked,
					hmod,
					inactive,
					".$order." AS rank,
					".$field." AS points,
					".$shift." AS shift,
					race_name,
					alliance_tag,
					sx,
					sy
				FROM 
					user_stats
				ORDER BY 
					$order ASC,
					nick ASC 
				LIMIT 
					$limit;";
			}
			$res=dbquery($sql);
	
			if (mysql_num_rows($res)>0)
			{
				$out.= "<tr><th colspan=\"7\" style=\"text-align:center;\">".$title."</th></tr>";
				$out.= "<tr>
					<th style=\"width:50px;\">#</th>
					<th style=\"\">Nick</th>
					<th style=\"\">Rasse</th>
					<th style=\"\">Sektor</th>
					<th style=\"\">Allianz</th>
					<th style=\"\">Punkte</th>
					<th style=\"width:150px;\">Details</th>
				</tr>";
				while ($arr=mysql_fetch_array($res))
				{
					if ($arr['blocked']==1)
					{
						$addstyle=" style=\"color:".COLOR_BANNED.";\"";
					}
					elseif ($arr['hmod']==1)
					{
						$addstyle=" style=\"color:".COLOR_UMOD.";\"";
					}
					elseif ($arr['inactive']==1)
					{
						$addstyle=" style=\"color:".COLOR_INACTIVE.";\"";
					}
					else
					{
						$addstyle="";
					}
					$out.= "<tr>";
	
					$out.= "<td $addstyle  align=\"right\" ";
					if ($mode=="user")
						$out.= tm("Punkteverlauf","<div><img src=\"misc/stats.image.php?user=".$arr['id']."\" alt=\"Diagramm\" style=\"width:600px;height:400px;background:#335 url(images/loading335.gif) no-repeat 300px 200px;\" /></div>");
					$out.= ">".nf($arr['rank'])." ";
					if ($arr['shift']==2)
						$out.= "<img src=\"images/stats/stat_down.gif\" alt=\"down\" width=\"9\" height=\"12\" />";
					elseif ($arr['shift']==1)
						$out.= "<img src=\"images/stats/stat_up.gif\" alt=\"up\" width=\"9\" height=\"11\" />";
					else
						$out.= "<img src=\"images/stats/stat_same.gif\" alt=\"same\" width=\"21\" height=\"9\" />";
					$out.= "</td>";
					$out.= "<td $addstyle >".$arr['nick']."</td>";
					$out.= "<td $addstyle >".$arr['race_name']."</td>";
					$out.= "<td $addstyle >".$arr['sx']."/".$arr['sy']."</td>";
					$out.= "<td $addstyle >".$arr['alliance_tag']."</td>";
					$out.= "<td $addstyle >".nf($arr['points'])."</td>";
					$out.= "<td $addstyle >";
					$out.=  "<a href=\"?page=userinfo&id=".$arr['id']."\" title=\"Userinfo\">Info</a> ";
					$out.=  "<a href=\"?page=$page&amp;mode=$mode&amp;userdetail=".$arr['id']."\">Verlauf</a> ";
					if ($arr['id']!=$_SESSION[ROUNDID]['user_id'])
					{
						$out.=  "<a href=\"?page=messages&mode=new&message_user_to=".$arr['id']."\" title=\"Nachricht senden\">Mail</a> ";
						$out.=  "<a href=\"?page=buddylist&add_id=".$arr['id']."\" title=\"Info\">Buddy</a></td>";
					}
					$out.= "</td>";
					$out.= "</tr>";
				}
				
			}
			else
				$out.= "<tr><td align=\"center\" class=\"tbldata\"><i>Es wurden keine Spieler gefunden!</i></tr>";
		}
		else
		{
			$out.= "<tr>
				<td align=\"center\" class=\"tbldata\">
					<i>Momentan sind keine Statistiken vorhanden, sie werden 
					zur nächsten vollen Stunde erstellt!
			</i></tr>";
		}
		$out.= "</table>";
		$objResponse->assign('statsTable', 'innerHTML', $out);

		return $objResponse;
}





?>