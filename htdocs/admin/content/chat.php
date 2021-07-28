<?PHP

//
// Updates
//
use EtoA\Chat\ChatBanRepository;
use EtoA\Chat\ChatLogRepository;
use EtoA\Chat\ChatManager;
use EtoA\Chat\ChatUserRepository;

/** @var ChatBanRepository $chatBanRepository */
$chatBanRepository = $app[ChatBanRepository::class];

/** @var ChatUserRepository $chatUserRepository */
$chatUserRepository = $app[ChatUserRepository::class];

/** @var ChatLogRepository $chatLogRepository */
$chatLogRepository = $app[ChatLogRepository::class];

/** @var ChatManager */
$chatManager = $app[ChatManager::class];

if ($sub == 'log') {
    echo "<h1>InGame-Chat Log</h1>";
    echo "<table class=\"tb\">
        <colgroup>
        <col style=\"width:80px;\" />
        <col style=\"width:80px;\" />
        <col />
        <col />
        </colgroup>";
    echo "<tr>";
    echo "<tr>";
    echo "<th colspan=\"2\">";
    if (isset($_GET['order_field']) && $_GET['order_field'] == "timestamp") {
        echo "<i>Datum</i> ";
    } else {
        echo "Datum ";
    }
    echo "<a href=\"?page=$page&amp;sub=$sub&amp;order_field=timestamp&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
    echo "<a href=\"?page=$page&amp;sub=$sub&amp;order_field=timestamp&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
    echo "</th>";

    if (isset($_GET['order_field']) && $_GET['order_field'] == "nick") {
        echo "<th><i>User</i> ";
    } else {
        echo "<th>User ";
    }
    echo "<a href=\"?page=$page&amp;sub=$sub&amp;order_field=nick&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
    echo "<a href=\"?page=$page&amp;sub=$sub&amp;order_field=nick&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
    echo "<th>Nachricht</th>";
    echo "</tr>";

    if (isset($_GET['order_field']) && $_GET['order_field'] == "nick") {
        $order = "nick,timestamp";
    } else {
        $order = "timestamp";
    }

    if (isset($_GET['order']) && $_GET['order'] == "ASC") {
        $sort = "ASC";
    } else {
        $sort = "DESC";
    }

    $logs = $chatLogRepository->getLogs($order, $sort);
    if (count($logs) > 0) {
        foreach ($logs as $chatLog) {
            echo "<tr>";
            echo "<td>" . date("d.m.Y", $chatLog->timestamp) . "</td>";
            echo "<td>" . date("H:i:s", $chatLog->timestamp) . "</td>";
            echo "<td><a href=\"?page=user&sub=edit&id=" . $chatLog->userId . "\">" . $chatLog->nick . "</a></td>";
            echo "<td>" . $chatLog->text . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan=\"5\" align=\"center\"><i>Keine Nachrichten vorhanden!</i></tr>";
    }
    echo "</table>";
}

//
// Live Chat
//
else {
    if (isset($_GET['ban']) && $_GET['ban'] > 0) {
        $userId = (int) $_GET['ban'];
        $chatBanRepository->banUser($userId, 'Banned by Admin');
        $chatUserRepository->kickUser($userId, 'Bannend by Admin');
        $chatManager->sendSystemMessage(get_user_nick($userId) . " wurde gebannt!");
    } elseif (isset($_GET['unban']) && $_GET['unban'] > 0) {
        $userId = (int) $_GET['unban'];
        $chatBanRepository->deleteBan($userId);
    } elseif (isset($_GET['kick']) && $_GET['kick'] > 0) {
        $userId = (int) $_GET['kick'];
        $chatUserRepository->kickUser($userId, 'Bannend by Admin');
        $chatManager->sendSystemMessage(get_user_nick($userId) . " wurde gekickt!");
    } elseif (isset($_GET['del']) && $_GET['del'] > 0) {
        $userId = (int) $_GET['del'];
        $chatUserRepository->deleteUser($userId);
    }
?>

    <h1>InGame-Chat</h1>
    <fieldset style="width:70%;float:left;height:500px;">
        <legend>Live-Chat</legend>
        <div id="chatitems" style="height:100%;overflow:auto;background:#222;padding:3px">

        </div>
        <div id="lastid" style="display:none;visibility:hidden">
            <?PHP //echo $lastid;
            ?>
        </div>
        <script type="text/javascript">
            xajax_loadChat(0);
        </script>
    </fieldset>
    <fieldset style="width:25%;float:right;height:300px;">
        <legend>Users online</legend>
        <div id="chatuserlist" style="display:block;">
            Lade...
        </div>
        <script type="text/javascript">
            xajax_showChatUsers();
        </script>
    </fieldset>

    <fieldset style="width:25%;float:right;height:163px;margin-top:20px">
        <legend>Gebannte User</legend>
        <div id="bannedchatuserlist" style="display:block;">
            Lade...
        </div>
        <script type="text/javascript">
            xajax_showBannedChatUsers();
        </script>
    </fieldset>
<?PHP
}
?>
