<?PHP

if (isset($_GET['sub']) && $_GET['sub'] == "battlelogs") {
    battleLog();
} elseif ($sub == "check_fights") {
    checkFights();
} elseif ($sub == "gamelogs") {
    newGamelogs();
} elseif ($sub == "fleetlogs") {
    newFleetLogs();
} elseif ($sub == "debrislog") {
    debrisLog();
} else {
    newCommonLog();
}

function battleLog()
{
    echo "Battle Log im aufbau!<br>";
}

function checkFights()
{
    header('Location: /admin/attack-ban/');
    die();
}

function newGamelogs()
{
    header('Location: /admin/logs/game/');
    die();
}

function newFleetLogs()
{
    header('Location: /admin/logs/fleets/');
    die();
}

function debrisLog()
{
    header('Location: /admin/logs/debris/');
    die();
}

function newCommonLog()
{
    header('Location: /admin/logs/');
    die();
}
