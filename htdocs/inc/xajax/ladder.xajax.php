<?PHP

$xajax->register(XAJAX_FUNCTION,'statsShowBox');
$xajax->register(XAJAX_FUNCTION,'statsShowTable');
 

function statsShowBox($mode, $sort="", $sortOrder="")
{
	global $page, $conf;
  $objResponse = new xajaxResponse();

	$_SESSION['statsmode']=$mode;

	$out="";
	
	//
	// Allianzdaten
	//
	if ($mode=="alliances")
	{
		ob_start();
		tableStart("Allianzen");
		echo "<tr>";
		echo "<th style=\"width:50px;\">Rang</th>";
		echo "<th>Tag</th>";
		echo "<th>Name</th>";
		if ($sort=="points")
			echo "<th><i>Punkte</i> ";
		else
			echo "<th>Punkte ";
		echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','points','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','points','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
		if ($sort=="uavg")
			echo "<th><i>User-Schnitt</i> ";
		else
			echo "<th>User-Schnitt ";
		echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','uavg','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','uavg','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
		echo "</th>";
		if ($sort=="cnt")
			echo "<th><i>User</i> ";
		else
			echo "<th>User ";
		echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','cnt','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','cnt','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
		echo "</tr>";
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
				points DESC,
				alliance_name ASC
			;";
		}
		
		$res=dbquery($sql);
		if (mysql_num_rows($res)>0)
		{
			$cnt=1;
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr>";
				echo  "<td ".tm("Punkteverlauf","<div><img src=\"misc/alliance_stats.image.php?alliance=".$arr['alliance_id']."\" alt=\"Diagramm\" style=\"width:600px;height:400px;background:#335 url(images/loading335.gif) no-repeat 300px 200px;\" /></div>").">
				".nf($cnt,1)." ";
				if ($arr['alliance_rank_current']==$arr['alliance_rank_last'])
					echo  "<img src=\"images/stats/stat_same.gif\" alt=\"same\" width=\"21\" height=\"9\" />";
				elseif ($arr['alliance_rank_current']>$arr['alliance_rank_last'])
					echo  "<img src=\"images/stats/stat_down.gif\" alt=\"up\" width=\"9\" height=\"12\" />";
				elseif ($arr['alliance_rank_current']<$arr['alliance_rank_last'])
					echo  "<img src=\"images/stats/stat_up.gif\" alt=\"down\" width=\"9\" height=\"11\" />";
				echo "<td >".($arr['alliance_tag'])."</td>";
				echo "<td >".$arr['alliance_name']."</td>";
				echo "<td >".nf($arr['points'])."</td>";
				echo "<td >".nf($arr['uavg'])."</td>";
				echo "<td >".nf($arr['cnt'])."</td>";
				echo "</tr>";
				$cnt++;
			}
		}
		else
		{
			echo "<tr><td colspan=\"8\" align=\"center\"><i>Keine Allianzen in der Statistik</i></tr>";
		}
		tableEnd();
	 	$objResponse->assign('statsBox', 'innerHTML', ob_get_clean());
		
	}
	
	//
	// Allianzbasis
	//
	elseif ($mode=="base")
	{
		ob_start();
		tableStart("Allianzbasis");
		echo "<tr>";
		echo "<th style=\"width:50px;\">Rang</th>";
		echo "<th>Tag</th>";
		if ($sort=="bpoints")
			echo "<th><i>Gebäude</i> ";
		else
			echo "<th>Gebäude ";
		echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','bpoints','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','bpoints','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a></th>";
	if ($sort=="tpoints")
			echo "<th><i>Forschung</i> ";
		else
			echo "<th>Forschung ";
		echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','tpoints','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','tpoints','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a></th>";
	if ($sort=="spoints")
			echo "<th><i>Schiffe</i> ";
		else
			echo "<th>Schiffe ";
		echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','spoints','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','spoints','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a></th>";
	if ($sort=="epoints")
			echo "<th><i>Erfahrung</i> ";
		else
			echo "<th>Allianzbasis ";
		echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','apoints','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','apoints','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a></th>";
		echo "</tr>";
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
				apoints DESC,
				alliance_name ASC
			;";
		}
		
		$res=dbquery($sql);
		if (mysql_num_rows($res)>0)
		{
			$cnt=1;
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr>
						<td>
							".nf($cnt,1)."
						</td>";
				echo "<td >".$arr['alliance_tag']."</td>";
				echo "<td >".nf($arr['bpoints'])."</td>";
				echo "<td >".nf($arr['tpoints'])."</td>";
				echo "<td >".nf($arr['spoints'])."</td>";
				echo "<td >".nf($arr['apoints'])."</td>";
				echo "</tr>";
				$cnt++;
			}
		}
		else
		{
			echo "<tr><td colspan=\"8\" align=\"center\"><i>Keine Allianzen in der Statistik</i></tr>";
		}
		tableEnd();
	 	$objResponse->assign('statsBox', 'innerHTML', ob_get_clean());
		
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
			users AS u
		LEFT JOIN
			admin_users AS a
		ON
			u.user_ban_admin_id = a.user_id
		WHERE 
			u.user_blocked_from<".time()." 
			AND u.user_blocked_to>".time()." 
		ORDER BY 
			u.user_blocked_from DESC;");
		ob_start();
		tableStart("Pranger");
		echo "
		<tr>
			<th>Nick</th>
			<th>Von:</th>
			<th>Bis:</th>
			<th>Admin</th>
			<th>Grund der Sperrung</th>
		</tr>";
		if (mysql_num_rows($res)>0)
		{
			while ($arr = mysql_fetch_array($res))
			{
				echo "<tr>
				<td>".$arr['user_nick']."</td>
				<td>".df($arr['user_blocked_from'])."</td>
				<td>".df($arr['user_blocked_to'])."</td>
				<td><a href=\"mailto:".$arr['admin_email']."\">".$arr['admin_nick']."</td>
				<td>".text2html($arr['user_ban_reason'])."</td>
				</tr>";
			}
		}
		else
			echo "<tr><td colspan=\"5\"><i>Keine Eintr&auml;ge vorhanden</i></tr>";
		tableEnd();
	 	$objResponse->assign('statsBox', 'innerHTML', ob_get_clean());
	}
	
	//
	// Titles
	//
	elseif ($mode=="titles")
	{
		ob_start();
		if (!@include(CACHE_ROOT."/out/usertitles_ex.gen"))
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
		ob_start();
		iBoxStart("Statistik");
		echo "<div id=\"statsHeaderContainer\">
		<div id=\"statsSearchContainer\">
			<b>&nbsp;&nbsp;Suche:</b> 
		<input type=\"text\" class=\"search\" name=\"user_nick\" autocomplete=\"off\" value=\"\" size=\"\" onclick=\"this.select()\" onkeyup=\"
			if(window.mytimeout) window.clearTimeout(window.mytimeout);
 			window.mytimeout = window.setTimeout('loadingMsg(\'statsTable\',\'Suche Spieler...\');xajax_statsShowTable(\'$mode\',0,document.getElementById(\'searchString\').value);', 500);
 			return true;\" id=\"searchString\"/>
			<input type=\"button\" onclick=\"loadingMsg('statsTable','Lade Statistiktabelle...');getElementById('searchString').value='';xajax_statsShowTable('$mode');\" value=\"Reset\" />
		</div>";
		echo "<div id=\"statsNav1\">";
		// >> AJAX generated content here
		echo "</div>
		<br style=\"clear:both;\"/>
		</div>
		<div id=\"statsTable\">";
		echo "<div class=\"loadingMsg\">Lade Statistiktabelle...</div>";
		// >> AJAX generated content here
		echo "</div>
		<div id=\"statsNav2\">";
		// >> AJAX generated content here
		echo "</div>
		<br style=\"clear:both;\"/>";
		iBoxEnd();
	 	$objResponse->assign('statsBox', 'innerHTML', ob_get_clean());
		$objResponse->script("xajax_statsShowTable('$mode');");
	}

  	return $objResponse;
}


function statsShowTable($mode, $limit=0, $userstring="", $absolute=0, $orderBy='')
{
	global $page;
  $objResponse = new xajaxResponse();
  
	// Datensatznavigation
	if($mode=="diplomacy" || $mode=="battle" || $mode=="trade")
	{
		$res = dbquery("
		SELECT
			COUNT(id)
		FROM
			user_ratings
		INNER JOIN
			users
		ON
			users.user_id=user_ratings.id;");
		$counter = ($limit>0) ? $limit : 0;
	}
	else
	{
		$res = dbquery("
		SELECT 
			COUNT(id) 
		FROM 
			user_stats;");
	}
		$usrcnt = mysql_fetch_row($res);
		$num = $usrcnt[0];
		
		if ($limit>0)
		{
			$limit = $limit.",".STATS_NUM_OF_ROWS;
			$nextlimit = $limit+STATS_NUM_OF_ROWS;
			$prevlimit = $limit-STATS_NUM_OF_ROWS;
		}
		else
		{
			$limit = "0,".STATS_NUM_OF_ROWS;
			$nextlimit = STATS_NUM_OF_ROWS;
			$prevlimit = -1;
		}
		$lastlimit = (ceil($num/STATS_NUM_OF_ROWS)*STATS_NUM_OF_ROWS)-STATS_NUM_OF_ROWS;

		// Punktetabelle
		
		if ($num > 0)
		{
			if($mode=="diplomacy")
			{
				$field="diplomacy_rating";
				$order="diplomacy_rating";
				$title="Diplomatiewertung";
				$titleContainer=array("#","Nick","Rasse","Allianz","Bewertung");
				$contentContainer=array("counter","user_nick","race_name","alliance_tag","diplomacy_rating");
			}
			elseif($mode=="trade")
			{
				$field="trades_buy,trades_sell,trade_rating";
				$order="trade_rating";
				$title="Handelswertung";
				$titleContainer=array("#","Nick","Rasse","Allianz","Einkäufe","Verkäufe","Bewertung");
				$contentContainer=array("counter","user_nick","race_name","alliance_tag","trades_buy","trades_sell","trade_rating");
			}
			elseif ($mode=="battle")
			{
				$field="battles_won,battles_lost,battles_fought,battle_rating";
				$order="battle_rating";
				$title="Kampfwertung";
				$titleContainer=array("#","Nick","Rasse","Allianz","Kämpfe Gewonnen","Kämpfe Verloren","Kämpfe Total","Bewertung");
				$contentContainer=array("counter","user_nick","race_name","alliance_tag","battles_won","battles_lost","battles_fought","battle_rating");
			}
			elseif ($mode=="ships")
			{
				$field="points_ships";
				$rank="rank_ships";
				$order="rank_ships";
				$title="Schiffspunkte";
				$shift="rankshift_ships";
			}
			elseif ($mode=="tech")
			{
				$field="points_tech";
				$rank="rank_tech";
				$order="rank_tech";
				$title="Technologiepunkte";
				$shift="rankshift_tech";
			}
			elseif ($mode=="buildings")
			{
				$field="points_buildings";
				$rank="rank_buildings";
				$order="rank_buildings";
				$title="Gebäudepunkte";
				$shift="rankshift_buildings";
			}
			elseif ($mode=="exp")
			{
				$field="points_exp";
				$rank="rank_exp";
				$order="rank_exp";
				$title="Erfahrungspunkte";
				$shift="rankshift_exp";
			}
			else
			{
				$field="points";
				$rank="rank";
				$order="rank";
				$title="Gesamtpunkte";
				$shift="rankshift";
			}
			$orderDir = "ASC";
			
			if ($orderBy=='nickUp')
			{
				$order = "nick";
				$orderDir = "DESC";
			}
			elseif ($orderBy=='nickDown')
			{
				$order = "nick";
				$orderDir = "ASC";
			}
			elseif ($orderBy=='rankUp')
			{
				$order = "rank";
				$orderDir = "DESC";
			}
			elseif ($orderBy=='rankDown')
			{
				$order = "rank";
				$orderDir = "ASC";
			}
			elseif ($orderBy=='allyUp')
			{
				$order = "alliance_tag";
				$orderDir = "DESC";
			}
			elseif ($orderBy=='allyDown')
			{
				$order = "alliance_tag";
				$orderDir = "ASC";
			}
			

			if ($userstring!="")
			{
				$limit="0,".STATS_NUM_OF_ROWS;
				$userstring=remove_illegal_signs($userstring);
				if ($absolute==1)
				{
					if($mode=="diplomacy" || $mode=="battle" || $mode=="trade")
					{
						$sql="
						SELECT 
							user_id AS id,
							user_nick,
							race_name,
							alliance_tag,
							".$field." 
						FROM 
							users
							INNER JOIN
								races ON user_race_id=race_id
							INNER JOIN
								user_ratings as r ON user_id=r.id
							LEFT JOIN 
								alliances ON user_alliance_id=alliance_id
						WHERE
							LCASE(user_nick) LIKE '".strtolower($userstring)."'
							AND user_ghost=0
						ORDER BY 
							$order DESC
						LIMIT 
							$limit;";
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
							".$rank." AS rank,
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
							$order $orderDir
						LIMIT 
							$limit;";
					}
				}
				else
				{
					if($mode=="diplomacy" || $mode=="battle" || $mode=="trade")
					{
						$sql="
						SELECT 
							user_id AS id,
							user_nick,
							race_name,
							alliance_tag,
							".$field." 
						FROM 
							users
							INNER JOIN
								races ON user_race_id=race_id
							INNER JOIN
								user_ratings as r ON user_id=r.id
							LEFT JOIN 
								alliances ON user_alliance_id=alliance_id
						WHERE
				       		LCASE(user_nick) LIKE '%".strtolower($userstring)."%'
							AND user_ghost=0
						ORDER BY 
							$order DESC
						LIMIT 
							$limit;";
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
							".$rank." AS rank,
							".$field." AS points,
							".$shift." AS shift,
							race_name,
							alliance_tag,
							sx,
							sy
						FROM 
							user_stats
						WHERE 
					LCASE(nick) LIKE '%".strtolower($userstring)."%' 
						ORDER BY 
							$order $orderDir
						LIMIT 
							$limit;";
					}
				}
			}
			else
			{
				if($mode=="diplomacy" || $mode=="battle" || $mode=="trade")
				{
					$sql="
					SELECT 
						user_id AS id,
						user_nick,
						race_name,
						alliance_tag,
						".$field." 
					FROM 
						users
						INNER JOIN
							races ON user_race_id=race_id
						INNER JOIN
							user_ratings as r ON user_id=r.id
						LEFT JOIN 
							alliances ON user_alliance_id=alliance_id
					WHERE
						user_ghost=0
					ORDER BY 
						$order DESC
					LIMIT 
						$limit;";
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
						".$rank." AS rank,
						".$field." AS points,
						".$shift." AS shift,
						race_name,
						alliance_tag,
						sx,
						sy
					FROM 
						user_stats
					ORDER BY 
						$order $orderDir
					LIMIT 
						$limit;";
				}
			}
			$res=dbquery($sql);
			
			$nr = mysql_num_rows($res);
			if ($nr>0)
			{
				// Navigationsfeld		
				ob_start();
				if ($userstring=='')
				{
					if ($prevlimit>-1 && STATS_NUM_OF_ROWS*2<$num)
						echo "<input type=\"button\" value=\"&lt;&lt;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',0,'',0,'$orderBy')\">";
					if ($prevlimit>-1)
						echo "<input type=\"button\" value=\"&lt;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',$prevlimit,'',0,'$orderBy')\">";
					if ($nextlimit<$num)
						echo "<input type=\"button\" value=\"&gt;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',$nextlimit,'',0,'$orderBy')\">";
					if ($nextlimit<$num && STATS_NUM_OF_ROWS*2<$num)
						echo "<input type=\"button\" value=\"&gt;&gt;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',$lastlimit,'',0,'$orderBy')\">";
					echo "<select onchange=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',this.options[this.selectedIndex].value,'',0,'$orderBy')\">";
					for ($x=1;$x<=$num;$x+=STATS_NUM_OF_ROWS)
					{
						$dif = $x+STATS_NUM_OF_ROWS-1;
						if ($dif>$num) $dif=$num;
						$oval=$x-1;
						echo "<option value=\"$oval\"";
						if ($limit==$oval) 
							echo " selected=\"selected\"";
						echo ">$x - $dif</option>";
					}
					echo "</select>";
				}				
				$out = ob_get_clean();
		 		$objResponse->assign('statsNav1', 'innerHTML', $out);
		 		$objResponse->assign('statsNav2', 'innerHTML', $out);				
						
				// Tabelle	
				$out="<table class=\"tb\" style=\"width:100%\">";
				$colspan = ($mode=="diplomacy" || $mode=="battle" || $mode=="trade") ? count($titleContainer) : 7;
				$out.= "<tr><th colspan=\"$colspan\" style=\"text-align:center;\">".$title."</th></tr>";
				$out.= "<tr>";
				if($mode=="diplomacy" || $mode=="battle" || $mode=="trade")
				{
					foreach($titleContainer as $tit)
					{
						if ($tit=="#") $out.="<th style=\"width:25px;\">";
						else $out.="<th>";
						$out.=$tit;
						$out.="</th>";
					}
				}
				else
				{
					$out.="<th style=\"width:50px;\">#
						<a href=\"javascript:;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',0,'',0,'rankDown')\"><img src=\"images/s_asc.png\"/></a>
						<a href=\"javascript:;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',0,'',0,'rankUp')\"><img src=\"images/s_desc.png\"/></a></th>
					</th>
					<th>Nick 
						<a href=\"javascript:;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',0,'',0,'nickDown')\"><img src=\"images/s_asc.png\"/></a>
						<a href=\"javascript:;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',0,'',0,'nickUp')\"><img src=\"images/s_desc.png\"/></a></th>
					<th>Rasse</th>
					<th>Sektor</th>
					<th>Allianz
						<a href=\"javascript:;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',0,'',0,'allyDown')\"><img src=\"images/s_asc.png\"/></a>
						<a href=\"javascript:;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',0,'',0,'allyUp')\"><img src=\"images/s_desc.png\"/></a></th>
					</th>
					<th>Punkte</th>";
				}
				$out.="</tr>";
				while ($arr=mysql_fetch_array($res))
				{
					if($mode=="diplomacy" || $mode=="battle" || $mode=="trade")
					{	
						$out.="<tr>";
						foreach($contentContainer as $content)
						{
							if ($content=="counter") $out.="<td>".++$counter."</td>";
							elseif ($content=="user_nick")
							{
								$out.= "<td>
								<div id=\"ttuser".$arr['id']."\" style=\"display:none;\">
								".popUp("Profil anzeigen","page=userinfo&id=".$arr['id'])."<br/>
								".popUp("Punkteverlauf","page=$page&amp;mode=$mode&amp;userdetail=".$arr['id'])."<br/>";
									if ($arr['id']!=$_SESSION['user_id'])
									{
										$out.=  "<a href=\"?page=messages&mode=new&message_user_to=".$arr['id']."\">Nachricht senden</a><br/>";
										$out.=  "<a href=\"?page=buddylist&add_id=".$arr['id']."\">Als Freund hinzufügen</a>";
									}
								$out.="</div>
								<a  href=\"#\" ".cTT($arr['user_nick'],"ttuser".$arr['id']).">".$arr['user_nick']."</a></td>";
							}
							else
							{
								$out.= "<td>".$arr[$content]."</td>";
							}
						}
					}
					else
					{
						if ($arr['blocked']==1)
						{
							$addstyle=" class=\"userLockedColor\"";
						}
						elseif ($arr['hmod']==1)
						{
							$addstyle=" class=\"userHolidayColor\"";
						}
						elseif ($arr['inactive']==1)
						{
							$addstyle=" class=\"userInactiveColor\"";
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
						$out.= "<td $addstyle >
						<div id=\"ttuser".$arr['id']."\" style=\"display:none;\">
						".popUp("Profil anzeigen","page=userinfo&id=".$arr['id'])."<br/>
						".popUp("Punkteverlauf","page=$page&amp;mode=$mode&amp;userdetail=".$arr['id'])."<br/>";
							if (isset($_SESSION['user_id']) && $arr['id']!=$_SESSION['user_id'])
							{
								$out.=  "<a href=\"?page=messages&mode=new&message_user_to=".$arr['id']."\">Nachricht senden</a><br/>";
								$out.=  "<a href=\"?page=buddylist&add_id=".$arr['id']."\">Als Freund hinzufügen</a>";
							}
						$out.="</div>
						<a $addstyle href=\"#\" ".cTT($arr['nick'],"ttuser".$arr['id']).">".$arr['nick']."</a></td>";
						$out.= "<td $addstyle >".$arr['race_name']."</td>";
						$out.= "<td $addstyle ><a $addstyle href=\"?page=sector&sector=".$arr['sx'].",".$arr['sy']."\">".$arr['sx']."/".$arr['sy']."</a></td>";
						$out.= "<td $addstyle >".$arr['alliance_tag']."</td>";
						$out.= "<td $addstyle >".nf($arr['points'])."</td>";
						$out.= "</tr>";
					}
				}
				$out.= "</table>";
			}
			else
			{
				$out= "<div><i>Es wurden keine Spieler gefunden!</i></div>";
		 		$objResponse->assign('statsNav1', 'innerHTML', '');
		 		$objResponse->assign('statsNav2', 'innerHTML', '');	
			}
		}
		else
		{
			$out= "<div><i>Momentan sind keine Statistiken vorhanden, sie werden 
					zur nächsten vollen Stunde erstellt!
			</i></div>";
		}
		$objResponse->assign('statsTable', 'innerHTML', $out);

		return $objResponse;
}





?>