<?PHP

$cnt=0;
$message = '';
if ($xml = simplexml_load_file(RELATIVE_ROOT."config/defaults.xml")) {
    foreach ($xml->items->item as $i) {
        if (!isset($cfg->{$i['name']})) {
            $message .= $i['name'] . ' existiert in der Standardkonfiguration, aber nicht in der Datenbank! ';
            $cfg->add((string)$i['name'],(string)$i->v,(string)$i->p1,(string)$i->p2);
            $message .= '<b>Behoben</b><br/>';
        }
        $cnt++;
    }
}
$message .= '<p>' . $cnt . ' Einträge in der Standardkonfiguration.</p>';

$cnt=0;
foreach ($cfg->getArray() as $cn => $ci) {
    $cnt++;
    $found = false;
    foreach ($xml->items->item as $i) {
        if ($i['name']==$cn) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        $message .= $cn . ' existiert in der Datenbank, aber nicht in der Standardkonfiguration! ';
        $cfg->del($cn);
        $message .= '<b>Gelöscht</b><br/>';
    }
}

$message .= '<p>' . $cnt . ' Datensätze in der Datenbank.</p>';
$message .= '<p>Prüfung abgeschlossen!</p>';

echo $twig->render('admin/config/check.html.twig', [
    'message' => $message,
]);
exit();
