<?PHP

$errorMessage = null;
$successMessage = null;
$users = [];
$res=dbquery("
SELECT 
    user_id,
    user_nick 
FROM 
    users 
ORDER BY 
    user_nick
;");
if (mysql_num_rows($res)>0) {
    while ($arr = mysql_fetch_assoc($res)) {
        $users[$arr['user_id']] = $arr['user_nick'];
    }
} else {
    $errorMessage = 'Keine Benutzer vorhanden!';
}

$uid = null;
$user = null;
if (isset($_GET['user_id']) && $_GET['user_id']>0) {
    $uid = $_GET['user_id'];

    $user = new User($uid);

    $sx = 1;
    $sy = 1;
    $cx = 1;
    $cy = 1;
    $radius = 1;

    // Discover selected cell
    if (isset($_POST['discover_selected'])) {
        $sx = intval($_POST['sx']);
        $sy = intval($_POST['sy']);
        $cx = intval($_POST['cx']);
        $cy = intval($_POST['cy']);
        $radius = abs(intval($_POST['radius']));

        $res = dbQuerySave("
            SELECT
                id
            FROM 
                cells
            WHERE 
                sx=? 
                AND sy=? 
                AND cx=? 
                AND cy=?;",
            array(
                $sx,
                $sy,
                $cx,
                $cy
            ));
        if (mysql_num_rows($res)) {
            $arr = mysql_fetch_row($res);
            $cell = new Cell($arr[0]);
            if ($cell->isValid()) {
                $user->setDiscovered($cell->absX(), $cell->absY(), $radius);
                $successMessage = 'Koordinaten erkundet!';
            }
        } else {
            $errorMessage = 'Ungültige Koordinate!';
        }
    }

    // Reset discovered coordinates
    else if (isset($_POST['discover_reset'])) {
        $user->setDiscoveredAll(false);
        $successMessage = 'Erkundung zurückgesetzt!';
    }

    // Discover all coordinates
    else if (isset($_POST['discover_all'])) {
        $user->setDiscoveredAll(true);
        $successMessage = 'Alles erkundet!';
    }

    echo $twig->render('admin/galaxy/exploration.html.twig', [
        'successMessage' => $successMessage,
        'errorMessage' => $errorMessage,
        'users' => $users,
        'uid' => $uid,
        'user' => $user,
        'discoveredPercent' => $user->getDiscoveredPercent(),
        'sx' => $sx,
        'sy' => $sy,
        'cx' => $cx,
        'cy' => $cy,
        'radius' => $radius,
    ]);
    exit();
}

echo $twig->render('admin/galaxy/exploration.html.twig', [
    'successMessage' => $successMessage,
    'errorMessage' => $errorMessage,
    'users' => $users,
    'uid' => $uid,
]);
exit();
