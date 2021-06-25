<?php

use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

echo "<h2>Aktive Sessions</h2>";
echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
echo "<p>Das User-Timeout betr&auml;gt ".tf($config->getInt('user_timeout'))."</p>";

if (isset($_GET['kick']))
{
    UserSession::kick($_GET['kick']);
    success_msg("Session ".$_GET['kick']." gelöscht!");
}
if (isset($_POST['kick_all']))
{
    $res=dbquery("SELECT
        id
    FROM
        user_sessions s
    ;");
    if (mysql_num_rows($res)>0)
    {
        while ($arr=mysql_fetch_array($res))
        {
            UserSession::kick($arr['id']);
        }
        success_msg("Alle Sessions gelöscht!");
    }
}


$res=dbquery("SELECT
    s.*,
    u.user_nick
    FROM
        user_sessions s
    INNER JOIN
        users u
        ON s.user_id=u.user_id
    ORDER BY time_action DESC;");
if (mysql_num_rows($res)>0)
{
    echo "<p>Es sind ".mysql_num_rows($res)." Sessions aktiv. <input type=\"submit\" name=\"kick_all\" value=\"Alle User kicken\" onclick=\"return confirm('Sollen wirklich alle User aus dem Spiel geworfen werden?');\" /></p>";
    echo "<table><tr>
        <th class=\"tbltitle\">Nick</th>
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
			<td class=\"tbldata\">".date("d.m.Y H:i",$arr['time_login'])."</td>
			<td class=\"tbldata\">".date("d.m.Y  H:i",$arr['time_action'])."</td>";
        if (time() - $config->getInt('user_timeout') < $arr['time_action'] && $arr['id']!='' )
        {
            echo "<td class=\"tbldata\" style=\"color:#0f0\">Online [<a href=\"?page=$page&amp;sub=$sub&amp;kick=".$arr['id']."\">kick</a>]</td>";
        }
        else
            echo "<td class=\"tbldata\" style=\"color:#f72\">offline</td>";
        echo "<td class=\"tbldata\">".$arr['ip_addr']."<br/>".Net::getHost($arr['ip_addr'])."</td>";
        $browserParser = new \WhichBrowser\Parser($arr['user_agent']);
        echo "<td class=\"tbldata\">".$browserParser->toString()."</td>";
        echo "<td class=\"tbldata\">";
        if (max($arr['time_login'],$arr['time_action'])-$arr['time_login']>0)
            echo tf($arr['time_action']-$arr['time_login']);
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
    echo "<br/><br/><i>Keine Eintr&auml;ge vorhanden!</i><br/>";
