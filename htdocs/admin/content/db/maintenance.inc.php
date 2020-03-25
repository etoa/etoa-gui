<?PHP

$action = $_GET['action'] ?? null;
$subTitle = null;
// Datenbanktabellen optimieren
if ($action === 'optimize') {
    $subTitle = 'Optimierungsbericht';
    $ores = DBManager::getInstance()->optimizeTables(true);
}
// Datenbanktabellen analysieren
elseif ($action === 'analyze') {
    $subTitle = 'Analysebericht';
    $successMessage = 'Tabellen deren Analysestatus bereits aktuell ist werden nicht angezeigt!';
    $ores = DBManager::getInstance()->analyzeTables();
}
// Datenbanktabellen prüfen
elseif ($action === 'check') {
    $subTitle = 'Überprüfungsbericht';
    $successMessage = 'Es werden nur Tabellen mit einem Status != OK angezeigt!';
    $ores = DBManager::getInstance()->checkTables();
}
// Datenbanktabellen reparieren
elseif ($action === 'repair') {
    $subTitle = 'Reparaturbericht';
    $ores = DBManager::getInstance()->repairTables(true);
}

// Fields
$fields = [];
while ($fo = mysql_fetch_field($ores)) {
    $fields[] = $fo->name;
}

// Records
$rows = [];
while ($arr = mysql_fetch_assoc($ores)) {
    // When checking, filter all rows with OK status
    if ($action === 'check' && isset($arr['Msg_text']) && $arr['Msg_text'] === 'OK') {
        continue;
    }

    // Filter all rows which are already up do date
    if ($action === 'analyze' && isset($arr['Msg_text']) && $arr['Msg_text'] === 'Table is already up to date') {
        continue;
    }

    $rows[] = $arr;
}

echo $twig->render('admin/database/maintenance.html.twig', [
    'rows' => $rows,
    'fields' => $fields,
    'subTitle' => $subTitle,
    'successMessage' => $successMessage,
]);
exit();
