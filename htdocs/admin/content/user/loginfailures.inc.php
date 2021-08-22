<?PHP

use EtoA\Support\StringUtils;
use EtoA\User\UserLoginFailureRepository;
/** @var UserLoginFailureRepository $userLoginFailureRepository */
$userLoginFailureRepository = $app[UserLoginFailureRepository::class];

echo "<h1>Fehlerhafte Logins</h1>";
echo "Es werden maximal 300 Einträge angezeigt!<br/><br/>";

switch (isset($_GET['order']) ? $_GET['order'] : 0) {
    case 1:
        $sort = 'user_nick';
        $order = "ASC";
        $orderstring = "Nickname";
        break;
    case 2:
        $sort = 'failure_ip';
        $order = "ASC";
        $orderstring = "IP";
        break;
    case 3:
        $sort = 'failure_host';
        $order = "ASC";
        $orderstring = "Host";
        break;
    case 3:
        $sort = 'failure_client';
        $order = "ASC";
        $orderstring = "Client";
        break;
    default:
        $sort = 'failure_time';
        $order = "DESC";
        $orderstring = "Datum";
}

$failures = $userLoginFailureRepository->findLoginFailures($sort, $order);
if (count($failures) > 0) {
    echo "Sortiert nach: " . $orderstring . "<br/><br/>";
    echo "<table class=\"tb\">";
    echo "<tr>
                <th><a href=\"?page=$page&amp;sub=$sub&amp;order=0\">Zeit</a></th>
                <th><a href=\"?page=$page&amp;sub=$sub&amp;order=1\">User</a></th>
                <th><a href=\"?page=$page&amp;sub=$sub&amp;order=2\">IP-Adresse</a></th>
                <th><a href=\"?page=$page&amp;sub=$sub&amp;order=3\">Hostname</a></th>
                <th><a href=\"?page=$page&amp;sub=$sub&amp;order=4\">Client</a></th>
                </tr>";
    foreach ($failures as $failure) {
        echo "<tr><td class=\"tbldata\">" . StringUtils::formatDate($failure->time) . "</td>";
        echo "<td class=\"tbldata\"><a href=\"?page=user&amp;sub=edit&amp;id=" . $failure->userId . "\">" . $failure->userNick . "</a></td>";
        echo "<td class=\"tbldata\"><a href=\"?page=user&amp;sub=ipsearch&amp;ip=" . $failure->ip . "\">" . $failure->ip . "</a></td>";
        echo "<td class=\"tbldata\"><a href=\"?page=user&amp;sub=ipsearch&amp;host=" . $failure->host . "\">" . $failure->host . "</a></td>
                    <td class=\"tbldata\">" . $failure->client . "</td>
                    </tr>";
    }
    echo "</table>";
} else {
    echo "<i>Keine fehlgeschlagenen Logins</i>";
}
