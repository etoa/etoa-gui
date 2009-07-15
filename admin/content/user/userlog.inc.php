<?PHP
		echo "<h1>User-Sessionlogs</h1>";

		if (isset($_POST['logshow']) || (isset($_GET['id']) && $_GET['id']>0))
		{
			if(isset($_GET['id']) && $_GET['id']>0)
			{
				$userid=$_GET['id'];
				$ures=dbquery("SELECT user_nick, user_id FROM users WHERE user_id='".$userid."';");
				if (mysql_num_rows($ures)>0)
				{
					$uarr=mysql_fetch_array($ures);
					echo "<h2>Session-Log f&uuml;r <a href=\"?page=user&sub=edit&user_id=".$uarr['user_id']."\">".$uarr['user_nick']."</a></h2>";
				}
				$res=dbquery("SELECT * FROM user_sessionlog WHERE user_id=".$userid." ORDER BY id DESC;");

			}
			else
			{
				$sqlstart="SELECT * FROM user_sessionlog s, users u WHERE s.user_id=u.user_id";
				$sqlend=" ORDER BY s.id DESC;";
				$sql='';
				if ($_POST['user_id']!="")
				{
					$sql.= " AND s.user_id='".$_POST['user_id']."'";
				}
				if ($_POST['user_nick']!="")
				{
					if (stristr($_POST['qmode']['user_nick'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND user_nick ".stripslashes($_POST['qmode']['user_nick']).$_POST['user_nick']."$addchars'";
				}
				if ($_POST['log_ip']!="")
				{
					$sql.= " AND ip_addr='".stripslashes($_POST['log_ip'])."'";
				}
				if ($_POST['log_hostname']!="")
				{
					$sql.= " AND ip_addr='".stripslashes(Net::getAddr($_POST['log_ip']))."'";
				}
				if ($_POST['user_agent']!="")
				{
					if (stristr($_POST['qmode']['user_agent'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND user_agent ".stripslashes($_POST['qmode']['user_agent']).$_POST['user_agent']."$addchars'";
				}
				if ($_POST['duration']>0)
				{
					$sql.= " AND (time_action-time_login)>".($_POST['duration']*$_POST['duration_multiplier'])."";
				}
					$sql=$sqlstart.$sql.$sqlend;
					$res=dbquery($sql);
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
						echo "<td>".date("d.m.Y H:i",$arr['time_login'])."</td>";
						echo "<td>";
						if ($arr['time_action']>0)
							echo date("d.m.Y H:i",$arr['time_action']);
						else
							echo "-";
						echo "</td>";
						echo "<td>";
						if ($arr['time_logout']>0)
							echo date("d.m.Y H:i",$arr['time_logout']);
						else
							echo "-";
						echo "</td>";
						echo "<td>".$arr['ip_addr']."<br/>".Net::getHost($arr['ip_addr'])."</td>";
						echo "<td>".$arr['user_agent']."</td>";
						echo "<td>";
						if (max($arr['time_logout'],$arr['time_action'])-$arr['time_login']>0)
							echo tf(max($arr['time_logout'],$arr['time_action'])-$arr['time_login']);
						else
							echo "-";
						echo "</td></tr>";
						$cnt+= max((max($arr['time_logout'],$arr['time_action'])-$arr['time_login']),0);
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


			echo "<h2>Session-Log</h2>";
			$res=dbquery("SELECT COUNT(id) as cnt FROM user_sessionlog;");
			if (mysql_num_rows($res)>0)
			{
				echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
				echo "<table class=\"tb\">";
				echo "<tr><th>ID</td><td><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
				echo "<tr><th>Nickname</td><td><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\"/> ";fieldqueryselbox('user_nick');echo "<br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td></tr>";
				echo "<tr><th>IP-Adresse</td><td><input type=\"text\" name=\"log_ip\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
				echo "<tr><th>Hostname</td><td><input type=\"text\" name=\"log_hostname\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
				echo "<tr><th>Client</td><td><input type=\"text\" name=\"user_agent\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('log_client');echo "</td></tr>";
				echo "<tr><th>Mindestdauer</td><td><input type=\"text\" name=\"duration\" value=\"\" size=\"20\" maxlength=\"250\" /><select name=\"duration_multiplier\">";
				echo "<option value=\"1\">Sekunden</option>";
				echo "<option value=\"60\">Minuten</option>";
				echo "<option value=\"3600\">Stunden</option>";
				echo "</select></td></tr>";
				echo "</table>";
				echo "<br/><input type=\"submit\" name=\"logshow\" value=\"Suche starten\" /></form>";
				$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM user_sessionlog;"));
				echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br/><br/>";
			}
			else
				echo "<i>Keine Eintr&auml;ge vorhanden</i><br/><br/>";

        }
?>