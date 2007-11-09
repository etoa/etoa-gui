<?PHP
		echo "<h1>User-Sessions und -Logs</h1>";

		if (isset($_POST['logshow']) || (isset($_GET['id']) && $_GET['id']>0))
		{
			if(isset($_GET['id']) && $_GET['id']>0)
			{
				$userid=$_GET['id'];
				$ures=dbquery("SELECT user_nick, user_id FROM ".$db_table['users']." WHERE user_id='".$userid."';");
				if (mysql_num_rows($ures)>0)
				{
					$uarr=mysql_fetch_array($ures);
					echo "<h2>Session-Log f&uuml;r <a href=\"?page=user&sub=edit&user_id=".$uarr['user_id']."\">".$uarr['user_nick']."</a></h2>";
				}
				$res=dbquery("SELECT * FROM ".$db_table['user_log']." WHERE log_user_id=".$userid." ORDER BY log_id DESC;");

			}
			else
			{
				$sqlstart="SELECT * FROM ".$db_table['user_log'].",".$db_table['users']." WHERE user_id=log_user_id";
				$sqlend=" ORDER BY log_user_id, log_id DESC;";
				$sql='';
				if ($_POST['user_id']!="")
				{
					$sql.= " AND user_id='".$_POST['user_id']."'";
				}
				if ($_POST['user_nick']!="")
				{
					if (stristr($_POST['qmode']['user_nick'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND user_nick ".stripslashes($_POST['qmode']['user_nick']).$_POST['user_nick']."$addchars'";
				}
				if ($_POST['log_ip']!="")
				{
					if (stristr($_POST['qmode']['log_ip'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND log_ip ".stripslashes($_POST['qmode']['log_ip']).$_POST['log_ip']."$addchars'";
				}
				if ($_POST['log_hostname']!="")
				{
					if (stristr($_POST['qmode']['log_hostname'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND log_hostname ".stripslashes($_POST['qmode']['log_hostname']).$_POST['log_hostname']."$addchars'";
				}
				if ($_POST['log_client']!="")
				{
					if (stristr($_POST['qmode']['log_client'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND log_client ".stripslashes($_POST['qmode']['log_client']).$_POST['log_client']."$addchars'";
				}
				if ($_POST['duration']>0)
				{
					$sql.= " AND (log_acttime-log_logintime)>".($_POST['duration']*$_POST['duration_multiplier'])."";
				}
				if ($sql!="")
				{
					$sql=$sqlstart.$sql.$sqlend;
					$res=dbquery($sql);
				}
			}

				if (mysql_num_rows($res)>20)
					echo "<input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /><br/><br/>";
				if (mysql_num_rows($res)>0)
				{
					echo mysql_num_rows($res)." Sessions gefunden!<br/><br/>";
					$cnt=0;
					echo "<table class=\"tb\"><tr>";
					$nid = isset($_GET['id']) ? $_GET['id'] : 0;
					if ($nid==0)
						echo "<th>Nick</th>";
					echo "<th>Login</th><th>Letzte Aktivit&auml;t</th><th>Logout</th><th>IP/Host</th><th>Client</th><th>Session-Dauer</th>";
					while ($arr=mysql_fetch_array($res))
					{
						echo "<tr>";
						if ($nid==0)
							echo "<tr><td><a href=\"?page=$page&amp;sub=edit&amp;user_id=".$arr['user_id']."\">".$arr['user_nick']."</a></td>";
						echo "<td>".date("d.m.Y H:i",$arr['log_logintime'])."</td>";
						echo "<td>";
						if ($arr['log_acttime']>0)
							echo date("d.m.Y H:i",$arr['log_acttime']);
						else
							echo "-";
						echo "</td>";
						echo "<td>";
						if ($arr['log_logouttime']>0)
							echo date("d.m.Y H:i",$arr['log_logouttime']);
						else
							echo "-";
						echo "</td>";
						echo "<td>".$arr['log_ip']."<br/>".$arr['log_hostname']."</td>";
						echo "<td>".$arr['log_client']."</td>";
						echo "<td>";
						if (max($arr['log_logouttime'],$arr['log_acttime'])-$arr['log_logintime']>0)
							echo tf(max($arr['log_logouttime'],$arr['log_acttime'])-$arr['log_logintime']);
						else
							echo "-";
						echo "</td></tr>";
						$cnt+= max((max($arr['log_logouttime'],$arr['log_acttime'])-$arr['log_logintime']),0);
					}
					echo "<tr><td colspan=\"7\"></td></tr>";
					echo "<tr><td colspan=\"6\"></td>";
					echo "<td>".tf($cnt)."</td></tr>";
					echo "</table>";
				}
				else
					echo "<i>Keine Eintr&auml;ge vorhanden</i>";
			echo "<br/><br/><input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" />";
		}
		else
		{
			// Alle User kicken
			if(isset($_POST['kick_all']))
			{
        dbquery("UPDATE ".$db_table['users']." SET user_session_key='';");
        add_log(8,$_SESSION[SESSION_NAME]['user_nick']." l&ouml;scht die Sessions aller Spieler / wirft alle Spieler aus dem Spiel",time());
        cms_ok_msg("Alle Spieler wurden gekickt!");
			}
			// Ein User kicken
			if (isset($_GET['kick']) && $_GET['kick']>0)
			{
				dbquery("UPDATE ".$db_table['users']." SET user_session_key='' WHERE user_id=".$_GET['kick'].";");
				add_log(8,$_SESSION[SESSION_NAME]['user_nick']." l&ouml;scht die Session des Spielers mit der ID ".$_GET['kick'],time());
				cms_ok_msg("Der Spieler mit der ID ".$_GET['kick']." wurde gekickt!");
			}

			echo "<h2>Session-Log</h2>";
			$res=dbquery("SELECT user_nick,user_id,COUNT(*) as cnt FROM ".$db_table['users'].",".$db_table['user_log']." WHERE log_user_id=user_id GROUP BY user_id ORDER BY user_nick;");
			if (mysql_num_rows($res)>0)
			{
				echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
				echo "<table class=\"tb\">";
				echo "<tr><th>ID</td><td><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
				echo "<tr><th>Nickname</td><td><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\"/> ";fieldqueryselbox('user_nick');echo "<br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td></tr>";
				echo "<tr><th>IP-Adresse</td><td><input type=\"text\" name=\"log_ip\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('log_ip');echo "</td></tr>";
				echo "<tr><th>Hostname</td><td><input type=\"text\" name=\"log_hostname\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('log_hostname');echo "</td></tr>";
				echo "<tr><th>Client</td><td><input type=\"text\" name=\"log_client\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('log_client');echo "</td></tr>";
				echo "<tr><th>Mindestdauer</td><td><input type=\"text\" name=\"duration\" value=\"\" size=\"20\" maxlength=\"250\" /><select name=\"duration_multiplier\">";
				echo "<option value=\"1\">Sekunden</option>";
				echo "<option value=\"60\">Minuten</option>";
				echo "<option value=\"3600\">Stunden</option>";
				echo "</select></td></tr>";
				echo "</table>";
				echo "<br/><input type=\"submit\" name=\"logshow\" value=\"Suche starten\" /></form>";
				$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM ".$db_table['user_log'].";"));
				echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br/><br/>";
			}
			else
				echo "<i>Keine Eintr&auml;ge vorhanden</i><br/><br/>";

			echo "<h2>Aktive Sessions</h2>";
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "Das User-Timeout betr&auml;gt ".$conf['user_timeout']['v']." Sekunden.";
			$res=dbquery("SELECT * FROM ".$db_table['users']." WHERE user_acttime>".(time()-$conf['user_timeout']['v'])." AND user_session_key!='' ORDER BY user_acttime DESC;");
			if (mysql_num_rows($res)>0)
			{
 				echo "Es sind ".mysql_num_rows($res)." Sessions aktiv. <input type=\"submit\" name=\"kick_all\" value=\"Alle User kicken\" onclick=\"return confirm('Sollen wirklich alle User aus dem Spiel geworfen werden?');\" /><br/><br/>";
				echo "<table><tr><th class=\"tbltitle\">Nick</th>
				<th class=\"tbltitle\">Login</th>
				<th class=\"tbltitle\">Letzte Aktion</th>
				<th class=\"tbltitle\">Status</th>
				<th class=\"tbltitle\">IP / Hostname</th>
				<th class=\"tbltitle\">Client</th>
				<th class=\"tbltitle\">Dauer</th>
				<th class=\"tbltitle\">Info</th>
				</tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><td class=\"tbldata\"><a href=\"?page=user&sub=edit&user_id=".$arr['user_id']."\">".$arr['user_nick']."</a></td>
					<td class=\"tbldata\">".date("d.m.Y H:i",$arr['user_logintime'])."</td>
					<td class=\"tbldata\">".date("d.m.Y  H:i",$arr['user_acttime'])."</td>";
					if (time()-$conf['user_timeout']['v']< $arr['user_acttime'] && $arr['user_session_key']!='' )
					{
						echo "<td class=\"tbldata\" style=\"color:#0f0\">Online [<a href=\"?page=$page&amp;sub=$sub&amp;kick=".$arr['user_id']."\">kick</a>]</td>";
					}
					else
						echo "<td class=\"tbldata\" style=\"color:#f72\">offline</td>";
					echo "<td class=\"tbldata\">".$arr['user_ip']."<br/>".$arr['user_hostname']."</td>";
					echo "<td class=\"tbldata\">".$arr['user_client']."</td>";
					echo "<td class=\"tbldata\">";
					if (max($arr['user_logouttime'],$arr['user_acttime'])-$arr['user_logintime']>0)
						echo tf(max($arr['user_logouttime'],$arr['user_acttime'])-$arr['user_logintime']);
					else
						echo "-";
					echo "</td>";
					echo "<input type=\"hidden\" name=\"user_id\" value=\"".$arr['user_id']."\">";
					echo "<td class=\"tbldata\"><input type=\"button\" onclick=\"document.location='?page=$page&sub=$sub&id=".$arr['user_id']."'\" value=\"Info\" /></td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
				echo "</form><br/>";
			}
			else
				echo "<i>Keine Eintr&auml;ge vorhanden!</i><br/>";
		}
?>