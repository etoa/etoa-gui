<?PHP

echo "<h1>Sitting: Laufende Sitteraccounts</h1>";

$t = time();
$res = dbquery("
            SELECT
                s.id,
                s.user_id,
                s.sitter_id,
                s.date_from,
                s.date_to,
                u.user_nick as unick,
                us.user_nick as usnick
            FROM
                user_sitting s
            LEFT JOIN
                users u on u.user_id=s.user_id
            LEFT JOIN
                users us on us.user_id=s.sitter_id
            WHERE
                (date_from<$t and $t<date_to)

            ;");
if (mysql_num_rows($res) > 0) {
    echo "<table class=\"tb\" width=\"100%\">";
    echo "<tr><th class=\"tbltitle\">User</th>
            <th class=\"tbltitle\">Sitter</th>
            <th class=\"tbltitle\">Von</th>
            <th class=\"tbltitle\">Bis</th>
            </tr>";
    while ($arr = mysql_fetch_array($res)) {
        echo "<tr>";
        echo "<td>" . ($arr['unick']) . "</td>";
        echo "<td>" . ($arr['usnick']) . "</td>";
        echo "<td>" . df($arr['date_from']) . "</td><td>" . df($arr['date_to']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<i>Keine Datensätze vorhanden!</i>";
}
