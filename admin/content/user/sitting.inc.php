<?PHP

		if (isset($_GET['id']) && $_GET['id']!="")
		{

            $res = dbquery("
            SELECT
                user_sitting_user_id,
                user_sitting_sitter_user_id,
                user_sitting_date,
                user_sitting_days
            FROM
                user_sitting,
                users
            WHERE
                user_sitting_id='".$_GET['id']."'
                AND user_sitting_user_id=user_id;");
            $arr = mysql_fetch_array($res);

			echo "<h1>Sitting: Details (".get_user_nick($arr['user_sitting_user_id']).")</h1>";

			echo "<table class=\"tbl\" width=\"100%\">";
            echo "<tr>
            		<td class=\"tbltitle\" valign=\"top\">Account Besitzer</td>
            		<td class=\"tbldata\"><a href=\"?page=user&sub=edit&user_id=".$arr['user_sitting_user_id']."\">".get_user_nick($arr['user_sitting_user_id'])."</a></td>
                 </tr>";
            echo "<tr>
            		<td class=\"tbltitle\" valign=\"top\">Rest. Sittertage</td>
            		<td class=\"tbldata\">".$arr['user_sitting_days']."</td>
                 </tr>";
            echo "<tr>
            		<td class=\"tbltitle\" valign=\"top\">Aktiviert am</td>
            		<td class=\"tbldata\">".date("d.m.Y H:i",$arr['user_sitting_date'])."</td>
                 </tr>";
            echo "<tr>
            		<td class=\"tbltitle\" valign=\"top\">Sitter</td>
            		<td class=\"tbldata\"><a href=\"?page=user&sub=edit&user_id=".$arr['user_sitting_sitter_user_id']."\">".get_user_nick($arr['user_sitting_sitter_user_id'])."</a></td>
                 </tr>";
            echo "<tr>
            		<td class=\"tbltitle\" valign=\"top\">Zugriffsdaten</td>
            		<td class=\"tbldata\">";

            $date_res = dbquery("
            SELECT
                *
            FROM
                user_sitting_date
            WHERE
                user_sitting_date_user_id='".$arr['user_sitting_user_id']."'
                AND user_sitting_date_from!=0
                AND user_sitting_date_to!=0
            ORDER BY
                user_sitting_date_from;");

            while ($date_arr=mysql_fetch_array($date_res))
            {
            	if($date_arr['user_sitting_date_to']<time())
            	{
                	echo "<span style=\"color:#f00\">Von ".date("d.m.Y H:i",$date_arr['user_sitting_date_from'])." bis ".date("d.m.Y H:i",$date_arr['user_sitting_date_to'])."</span><br>";
                }
                elseif($date_arr['user_sitting_date_from']<time() && $date_arr['user_sitting_date_to']>time())
                {
                	echo "<span style=\"color:#0f0\">Von ".date("d.m.Y H:i",$date_arr['user_sitting_date_from'])." bis ".date("d.m.Y H:i",$date_arr['user_sitting_date_to'])."</span><br>";
                }
                else
                {
                	echo "Von ".date("d.m.Y H:i",$date_arr['user_sitting_date_from'])." bis ".date("d.m.Y H:i",$date_arr['user_sitting_date_to'])."<br>";
                }
            }
            echo "</td></tr>";
            echo "</table>";

            echo "<div align=\"center\"><br><input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /></div>";

		}
		else
		{
            echo "<h1>Sitting: Laufende Sitteraccounts</h1>";

            $res = dbquery("
            SELECT
                user_sitting_id,
                user_sitting_user_id,
                user_sitting_sitter_user_id,
                user_sitting_date
            FROM
                user_sitting
            WHERE
                user_sitting_active='1';");
            if (mysql_num_rows($res)>0)
            {
            echo "<table class=\"tbl\" width=\"100%\">";
            echo "<tr><th class=\"tbltitle\">User</th><th class=\"tbltitle\">Sitter</th><th class=\"tbltitle\">Aktiviert am</th><th class=\"tbltitle\">Details</th></tr>";
            while ($arr = mysql_fetch_array($res))
            {
                echo "<tr>";
                    echo "<td class=\"tbltitle\">".get_user_nick($arr['user_sitting_user_id'])."</td>";
                    echo "<td class=\"tbldata\">".get_user_nick($arr['user_sitting_sitter_user_id'])."</td>";
                    echo "<td class=\"tbldata\">".date("d.m.Y H:i",$arr['user_sitting_date'])."</td>";
                    echo "<td class=\"tbldata\"><a href=\"?page=$page&amp;sub=$sub&amp;id=".$arr['user_sitting_id']."\">Details</a></td>";
                echo "</tr>";
            }
            echo "</table>";
            }
            else
            {
            	echo "<i>Keine Datensätze vorhanden!</i>";
            }
		}
		
?>