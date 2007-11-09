<?PHP

function statsShowBox($mode, $sort="", $sortOrder="")
{
	global $db_table, $page, $conf;
  	$objResponse = new xajaxResponse();

	$_SESSION['statsmode']=$mode;

	for ($x=0;$x<6;$x++)
		$objResponse->addAssign('tabMenu'.$x, 'className', "tabDefault");
	switch ($mode)
	{
		case "users":
			$objResponse->addAssign('tabMenu0', 'className', "tabEnabled"); break;
		case "ships":
			$objResponse->addAssign('tabMenu1', 'className', "tabEnabled"); break;
		case "tech":
			$objResponse->addAssign('tabMenu2', 'className', "tabEnabled"); break;
		case "buildings":
			$objResponse->addAssign('tabMenu3', 'className', "tabEnabled"); break;
		case "alliances":
			$objResponse->addAssign('tabMenu4', 'className', "tabEnabled"); break;
		case "pillory":
			$objResponse->addAssign('tabMenu5', 'className', "tabEnabled"); break;
		default:
			$objResponse->addAssign('tabMenu0', 'className', "tabEnabled"); break;
	}

	//
	// Allianzdaten
	//
	if ($mode=="alliances")
	{
		$out.= "<table class=\"tbl\"><tr>";
		$out.= "<th class=\"tbltitle\">#</th>";
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
			/*$sql="
			SELECT 
                a.alliance_tag,
                a.alliance_name,
                a.alliance_id,
                COUNT(*) AS cnt, 
                SUM(u.user_points) AS upoints, 
                AVG(u.user_points) AS uavg, 
                u.user_alliance_application 
			FROM 
                ".$db_table['alliances']." AS a,
                ".$db_table['users']." AS u 
			WHERE 
                u.user_alliance_id=a.alliance_id 
                AND u.user_alliance_application='' 
                AND u.user_show_stats=1 
			GROUP BY 
				a.alliance_id 
			ORDER BY 
				upoints DESC,
				a.alliance_foundation_date DESC;";*/

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
				$out.= "<td class=\"tbldata\" $style>".(text2html($arr['alliance_tag']))."</td>";
				$out.= "<td class=\"tbldata\" $style>".text2html($arr['alliance_name'])."</td>";
				$out.= "<td class=\"tbldata\" $style>".nf2($arr['upoints'])."</td>";
				$out.= "<td class=\"tbldata\" $style>".nf2($arr['uavg'])."</td>";
				$out.= "<td class=\"tbldata\" $style>".nf2($arr['cnt'])."</td>";
				$out.= "<td class=\"tbldata\"><a href=\"?page=alliance&amp;info_id=".$arr['alliance_id']."\">Info</a></td>";
				$out.= "</tr>";
				$cnt++;
			}
		}
		else
		{
			$out.= "<tr><td colspan=\"5\" align=\"center\"><i>Keine Allianzen in der Statistik</i></tr>";
		}
	 	$objResponse->addAssign('statsBox', 'innerHTML', $out);
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
	 	$objResponse->addAssign('statsBox', 'innerHTML', $out);
	}
	else
	{
		$out.= "<table class=\"tbl\"><tr><td style=\"width:470px;text-align:left;\" class=\"statsNav\"><b>&nbsp;&nbsp;Suche:</b> <input type=\"text\" name=\"user_nick\" value=\"\" size=\"\" onkeyup=\"xajax_statsShowTable('$mode',0,this.value);\" id=\"searchString\"/>
		<input type=\"button\" onclick=\"getElementById('searchString').value='';xajax_statsShowTable('$mode');\" value=\"Reset\" />
		<input type=\"button\" onclick=\"xajax_statsShowTable('$mode',0,'".$_SESSION[ROUNDID]['user']['nick']."',1);\" value=\"".$_SESSION[ROUNDID]['user']['nick']."\" />
		</td>";
		$out.= "<td class=\"statsNav\" id=\"statsNav1\" style=\"text-align:right;\">";
		// >> AJAX generated content here
		$out.= "</td></tr>";
		$out.="<tr><td colspan=\"2\" style=\"margin:0px;padding:0px;\" id=\"statsTable\">";
		// >> AJAX generated content here
		$out.= "</td></tr>";
		$out.= "<td style=\"width:470px;\" class=\"statsNav\"></td><td id=\"statsNav2\" style=\"text-align:right;\" class=\"statsNav\">";
		// >> AJAX generated content here
		$out.= "</td></tr></table>";
	 	$objResponse->addAssign('statsBox', 'innerHTML', $out);
		$objResponse->addScript("xajax_statsShowTable('$mode');");
	}

  	return $objResponse->getXML();
}


function statsShowTable($mode, $limit=0, $userstring="", $absolute=0)
{
	global $db_table, $page;
  $objResponse = new xajaxResponse();
		// Datensatznavigation
		$res = dbquery("
		SELECT 
			COUNT(user_id) 
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
		$out="<table>";
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
 		$objResponse->addAssign('statsNav1', 'innerHTML', $out);
 		$objResponse->addAssign('statsNav2', 'innerHTML', $out);

		// Punktetabelle
		$out="<table class=\"tbl\">";
		
		$sql="
				SELECT 
	        COUNT(*)
				FROM 
					user_stats";
		$res = dbquery($sql);
		$arr = mysql_fetch_row($res);
		if ($arr[0]>0)
		{
			if ($mode=="ships")
				$order="user_points_ships";
			elseif ($mode=="tech")
				$order="user_points_tech";
			elseif ($mode=="buildings")
				$order="user_points_buildings";
			else
				$order="user_points";
			if ($userstring!="")
			{
				$limit="0,".NUM_OF_ROWS;
				$userstring=remove_illegal_signs($userstring);
				if ($absolute==1)
				{
					$sql="
					SELECT 
		        user_rank_current,
		        user_rank_last,
		        user_id,
		        user_nick,
		        alliance_tag,
		        alliance_id,
		        $order AS points,
		        race_name,
		        cell_sx,
		        cell_sy,			
		        user_blocked,
		        user_hmod,
		        user_inactive
					FROM 
						user_stats
					WHERE 
	         	LCASE(user_nick) LIKE '".strtolower($userstring)."' 
					ORDER BY 
	          $order DESC,
						user_nick ASC 
					LIMIT $limit;";
				}
				else
				{
					$sql="
					SELECT 
		        user_rank_current,
		        user_rank_last,
		        user_id,
		        user_nick,
		        alliance_tag,
		        alliance_id,
		        $order AS points,
		        race_name,
		        cell_sx,
		        cell_sy,			
		        user_blocked,
		        user_hmod,
		        user_inactive
					FROM 
						user_stats
					WHERE 
	       		LCASE(user_nick) LIKE '%".strtolower($userstring)."%' 
					ORDER BY 
	        	$order DESC,
						user_nick ASC 
					LIMIT $limit;";
				}
			}
			else
			{
				// Prevents chaotic ranks in the beginning of the round
				if ($mode=="user")
				{
					$usrsql = "user_rank_current,";
				}
				else
				{
					$usrsql = "";
				}
				$sql="SELECT
	        user_rank_current,
	        user_rank_last,
	        user_id,
	        user_nick,
	        alliance_tag,
	        alliance_id,
	        $order AS points,
	        race_name,
	        cell_sx,
	        cell_sy,			
	        user_blocked,
	        user_hmod,
	        user_inactive
				FROM 
					user_stats
				ORDER BY 
					$order DESC,
					".$usrsql."
					user_nick ASC
				LIMIT 
					$limit;";
			}
			$res=dbquery($sql);
	
			if (mysql_num_rows($res)>0)
			{
				$alliances = get_alliance_names();
				$out.= "<tr>";
				if ($mode=="user")
					$out.=  "<th class=\"tbltitle\" colspan=\"2\">Rang</th>";
				else
					$out.=  "<th class=\"tbltitle\">Rang</th>";
				$out.=  "<th class=\"tbltitle\">Nick</th><th class=\"tbltitle\">Rasse</th><th class=\"tbltitle\">Allianz</th><th class=\"tbltitle\">Punkte</th><th class=\"tbltitle\">Sektor</th><th class=\"tbltitle\" style=\"width:220px;\">Aktion</th></tr>";
				$cnt=1+$limit;
				while ($arr=mysql_fetch_array($res))
				{
					if ($arr['user_blocked']==1)
					{
						$addstyle=" style=\"color:".COLOR_BANNED.";\"";
					}
					elseif ($arr['user_hmod']==1)
					{
						$addstyle=" style=\"color:".COLOR_UMOD.";\"";
					}
					elseif ($arr['user_inactive']==1)
					{
						$addstyle=" style=\"color:".COLOR_INACTIVE.";\"";
					}
					elseif ($arr['alliance_id']==$_SESSION[ROUNDID]['user']['alliance_id'] 
									AND $_SESSION[ROUNDID]['user']['alliance_id']!='0'
									AND $_SESSION[ROUNDID]['user']['alliance_application']==0)
					{
						$addstyle=" style=\"color:".COLOR_ALLIANCE.";\"";
					}
					else
					{
						$addstyle="";
					}
					if ($arr['user_id']==$_SESSION[ROUNDID]['user']['id'])
					{
						$_SESSION[ROUNDID]['user']['points']=$arr['points'];
					}
					$out.=  "<tr";
					if ($arr['user_id']==$_SESSION[ROUNDID]['user']['id']) $out.=  " style=\"font-weight:bold;\"";
					$out.=  ">";
					$out.=  "<td $addstyle class=\"tbldata\" align=\"right\">";
					if ($mode=="user")
						$out.=  nf2($arr['user_rank_current'])."</td>";
					else
						$out.=  "$cnt (".nf2($arr['user_rank_current']).")</td>";
	
					if ($mode=="user")
					{
						$out.=  "<td $addstyle class=\"tbldata\" align=\"right\" ".tm("Punkteverlauf","<img src=\"misc/stats.image.php?user=".$arr['user_id']."\" alt=\"Diagramm\" style=\"width:600px;height:400px;\" />").">";
						if ($arr['user_rank_current']==$arr['user_rank_last'])
							$out.=  "<img src=\"images/stats/stat_same.gif\" alt=\"same\" width=\"21\" height=\"9\" />";
						elseif ($arr['user_rank_current']<$arr['user_rank_last'])
							$out.=  "<img src=\"images/stats/stat_up.gif\" alt=\"up\" width=\"9\" height=\"12\" />";
						elseif ($arr['user_rank_current']>$arr['user_rank_last'])
							$out.=  "<img src=\"images/stats/stat_down.gif\" alt=\"down\" width=\"9\" height=\"11\" />";
						$out.=  "</td>";
					}
					$out.=  "<td $addstyle class=\"tbldata\">".text2html($arr['user_nick'])."</td>";
					$out.=  "<td $addstyle class=\"tbldata\">".$arr['race_name']."</td>";
					$out.=  "<td class=\"tbldata\" $addstyle>".text2html($arr['alliance_tag'])."</td>";
					$out.=  "<td $addstyle class=\"tbldata\">".nf2($arr['points'])."</td>";
					$out.=  "<td $addstyle class=\"tbldata\"><a href=\"?page=space&amp;sx=".$arr['cell_sx']."&amp;sy=".$arr['cell_sy']."\">".$arr['cell_sx']."/".$arr['cell_sy']."</a></td>";
					$out.=  "<td $addstyle class=\"tbldata\">";
					$out.=  "<a href=\"?page=userinfo&id=".$arr['user_id']."\" title=\"Userinfo\">Info</a> ";
					$out.=  "<a href=\"?page=$page&amp;mode=$mode&amp;limit=".$_GET['limit']."&amp;userdetail=".$arr['user_id']."\">Punktedetails</a> ";
					if ($arr['user_id']!=$_SESSION[ROUNDID]['user']['id'])
					{
						$out.=  "<a href=\"?page=messages&mode=new&message_user_to=".$arr['user_id']."\" title=\"Nachricht senden\">Mail</a> ";
						$out.=  "<a href=\"?page=buddylist&add_id=".$arr['user_id']."\" title=\"Info\">Buddy</a></td>";
					}
					$out.=  "</tr>\n";
					$cnt++;
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
		$objResponse->addAssign('statsTable', 'innerHTML', $out);

		return $objResponse->getXML();
}

$objAjax->registerFunction('statsShowBox');
$objAjax->registerFunction('statsShowTable');



?>