<?PHP

$successMessage = null;
$items = [];
if (isset($_POST['restoresubmit'])) {
    if ($cnt = Config::restoreDefaults()) {
        Config::getInstance()->reload();
        $successMessage = "$cnt Einstellungen wurden wiederhergestellt!";
        BackendMessage::reloadConfig();
    }
}

// Changed values
foreach ($cfg->categories() as $ck => $cv) {
    foreach ($cfg->itemInCategory($ck) as $i) {
        $name = $i['name'];
        if (isset($i->v)) {
            if ((string)$i->v != $cfg->$name->v) {
                $items[] = [
                    'category' => $cv,
                    'label' => (string)$i->v['comment'],
                    'name' => (string)$i['name'],
                    'type' => 'v',
                    'value' => $cfg->$name->v,
                    'default' => (string)$i->v,
                ];
            }
        }
        if (isset($i->p1)) {
            if ((string)$i->p1 != $cfg->$name->p1) {
                $items[] = [
                    'category' => $cv,
                    'label' => (string)$i->p1['comment'],
                    'name' => (string)$i['name'],
                    'type' => 'p1',
                    'value' => $cfg->$name->p1,
                    'default' => (string)$i->p1,
                ];
            }
        }
        if (isset($i->p2)) {
            if ((string)$i->p2 != $cfg->$name->p2) {
                $items[] = [
                    'category' => $cv,
                    'label' => (string)$i->p2['comment'],
                    'name' => (string)$i['name'],
                    'type' => 'p2',
                    'value' => $cfg->$name->p2,
                    'default' => (string)$i->p2,
                ];
            }
        }
    }
}

echo $twig->render('admin/config/restore-defaults.html.twig', [
    'successMessage' => $successMessage,
    'changedValues' => $items,
]);
exit();
