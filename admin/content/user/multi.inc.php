<?PHP
		if (isset($_GET['ip']))
		{
			$ip = $_GET['ip'];
			echo "<h1>Multi-Erkennung - Details</h1>";
			
			echo "<b>IP:</b> <a href=\"?page=$page&amp;sub=ipsearch&amp;ip=".$ip."\">$ip</a><br/>
			<b>Host:</b> <a href=\"?page=$page&amp;sub=ipsearch&amp;host=".resolveIp($ip)."\">".resolveIp($ip)."</a><br/><br/>";
			$ipres = dbquery("
			SELECT 
				user_blocked_from,
				user_blocked_to,
				user_alliance_id,
				user_id,user_points,
				user_nick,user_acttime,
				user_name,user_email,
				user_email_fix,
				user_multi_delets 
			FROM 
				users
			WHERE 
				user_ip='$ip' 
			ORDER BY 
				user_acttime DESC;");

			echo "<table class=\"tbl\" width=\"100%\">";
			echo "<tr><td class=\"tbltitle\">Nick</td><td class=\"tbltitle\">Name</td><td class=\"tbltitle\">E-Mail</td><td class=\"tbltitle\">Online</td><td class=\"tbltitle\">Punkte</td><td class=\"tbltitle\">Eingetragene Multis</td><td class=\"tbltitle\">Gel&ouml;schte Multis</td></tr>";
			while ($iparr = mysql_fetch_array($ipres))
			{
        $multi_res = dbquery("
        SELECT
            *
        FROM
            user_multi
        WHERE
            user_multi_user_id='".$iparr['user_id']."'
            AND user_multi_multi_user_id!='0';");

				if ($iparr['user_blocked_from']<time() && $iparr['user_blocked_to']>time())
					$uCol=USER_COLOR_BANNED;
				else
					$uCol=USER_COLOR_DEFAULT;
				echo "<tr>";
				echo "<td class=\"tbldata\" valign=\"top\" style=\"color:$uCol;\">
				<a href=\"?page=$page&amp;sub=ipsearch&amp;user=".$iparr['user_id']."\">".$iparr['user_nick']."</a>
				";
				if ($iparr['user_alliance_id']>0)
				{
					$aarr = mysql_fetch_array(dbquery("SELECT alliance_tag FROM alliances WHERE alliance_id=".$iparr['user_alliance_id'].";"));
					echo "<br/><b>".$aarr['alliance_tag']."</b>";
				}
				echo "</td>";
				echo "<td class=\"tbldata\" valign=\"top\" style=\"color:$uCol;\">".$iparr['user_name']."</td>";
				echo "<td class=\"tbldata\" valign=\"top\" style=\"color:$uCol;\">".$iparr['user_email_fix']."<br/>".$iparr['user_email']."</td>";
				echo "<td class=\"tbldata\" valign=\"top\" style=\"color:$uCol;\">";
				if ($iparr['user_acttime']+$conf['user_timeout']['v'] > time())
					echo "<span style=\"color:#0f0\">online</span>";
				else
					echo date("Y-m-d H:i:s",$iparr['user_acttime']);
				echo "</td><td class=\"tbldata\" valign=\"top\" style=\"color:$uCol;\">".nf($iparr['user_points'])."</td>";
        if(mysql_num_rows($multi_res)>0)
        {
            $multi = 1;

            echo "<td class=\"tbldata\" valign=\"top\" style=\"color:$uCol;\">";
            while($multi_arr = mysql_fetch_array($multi_res))
            {
                echo "<span title=\"".$multi_arr['user_multi_connection']."\">".get_user_nick($multi_arr['user_multi_multi_user_id'])."</span>";

                if($multi<mysql_num_rows($multi_res))
                {
                    echo ", ";
                }

                $multi++;
            }
            echo "</td>";
        }
        else
        {
            echo "<td class=\"tbldata\" valign=\"top\" style=\"color:$uCol;\">-</td>";
        }
        echo "<td class=\"tbldata\" valign=\"top\" style=\"color:$uCol;\">".nf($iparr['user_multi_delets'])."</td>";
				echo "</tr>";
			}
			echo "</table>";
			echo "<br/><a href=\"?page=$page&amp;sub=$sub\">Multi-&Uuml;bersicht</a>";
		}
		else
		{
			echo "<h1>Multi-Erkennung - Zusammenfassung</h1>";
			echo "Multi-Merkmale:</br><ul><li>Gleiche IP (durch dieses Tool pr&uuml;fen)</li><li>&Auml;hnliche Onlinezeit (mit Session-Log pr&uuml;fen)</li><li>evtl. dieselbe Allianz</li><li>&Auml;hnliche Mailadresse</li><li>&Auml;hnliche Fantasienamen</li></ul></br>";
			$res = dbquery("SELECT count(user_ip) as ip_count,user_id,user_ip FROM users WHERE user_ip!='' GROUP by user_ip ORDER BY ip_count DESC;");
			$multi_ips=array();
			while ($arr=mysql_fetch_array($res))
			{
				if ($arr['ip_count']>1)
				{
					array_push($multi_ips,$arr['user_ip']);
				}
				if ($arr['ip_count']==1)
					break;
			}
			if (count($multi_ips)>0)
			{
				echo "<table class=\"tbl\" width=\"100%\">";
				echo "<tr><th class=\"tbltitle\">IP-Adresse</th><th class=\"tbltitle\">Nick</th><th class=\"tbltitle\">Realer Name</th><th class=\"tbltitle\">Zuletzt online</th><th class=\"tbltitle\">Eingetragene Multis</th></tr>";
				$multi_ip_cnt=0;
				$multi_total_cnt=0;
				foreach ($multi_ips as $ip)
				{
					$ipres = dbquery("
					SELECT
                        user_id,
                        user_blocked_from,
                        user_blocked_to,
                        user_nick,
                        user_acttime,
                        user_name,
                        user_email
					FROM
						users
					WHERE
						user_ip='$ip'
					ORDER BY
						user_acttime DESC;");




					echo "<tr>
					<td rowspan=\"".mysql_num_rows($ipres)."\" valign=\"top\" class=\"tbldata\"><a href=\"?page=$page&amp;sub=$sub&amp;ip=$ip\">$ip</a></td>";
					$cnt=0;
					while ($iparr = mysql_fetch_array($ipres))
					{

                        $multi_res = dbquery("
                        SELECT
                            *
                        FROM
                            user_multi
                        WHERE
                            user_multi_user_id='".$iparr['user_id']."'
                            AND user_multi_multi_user_id!='0';");

						if ($cnt!=0) echo "<tr>"; else $cnt=1;
						if ($iparr['user_blocked_from']<time() && $iparr['user_blocked_to']>time())
							$uCol=USER_COLOR_BANNED;
						else
							$uCol=USER_COLOR_DEFAULT;
						echo "<td class=\"tbldata\" style=\"color:$uCol;\">".$iparr['user_nick']."</td>";
						echo "<td class=\"tbldata\" style=\"color:$uCol;\" title=\"".$iparr['user_email']."\">".$iparr['user_name']."</td>";
						echo "<td class=\"tbldata\" style=\"color:$uCol;\">";
						if ($iparr['user_acttime']+$conf['user_timeout']['v'] > time())
							echo "<span style=\"color:#0f0\">online</span>";
						else
							echo date("Y-m-d H:i:s",$iparr['user_acttime']);
						echo "</td>";

						if(mysql_num_rows($multi_res)>0)
						{
							$multi = 1;

							echo "<td class=\"tbldata\" style=\"color:$uCol;\">";
							while($multi_arr = mysql_fetch_array($multi_res))
							{
								echo "<span title=\"".$multi_arr['user_multi_connection']."\">".get_user_nick($multi_arr['user_multi_multi_user_id'])."</span>";

								if($multi<mysql_num_rows($multi_res))
								{
									echo ", ";
								}

								$multi++;
							}
							echo "</td></tr>";
						}
						else
						{
							echo "<td class=\"tbldata\" style=\"color:$uCol;\">-</td></tr>";
						}

						$multi_total_cnt++;
					}
					$multi_ip_cnt++;
				}
				echo "</table>";
				echo "<p>Total $multi_ip_cnt IP-Adressen mit $multi_total_cnt Spielern entdeckt.</p>";
			}
			else
				echo "<br/><i>Nichts gefunden!</i>";
		}
?>