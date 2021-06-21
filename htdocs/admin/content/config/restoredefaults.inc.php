<?PHP

use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

$successMessage = null;
$items = [];
if (isset($_POST['restoresubmit'])) {
    if ($cnt = $config->restoreDefaults()) {
        $config->reload();
        $successMessage = "$cnt Einstellungen wurden wiederhergestellt!";
        BackendMessage::reloadConfig();
    }
}

// Changed values
foreach ($config->categories() as $ck => $cv) {
    foreach ($config->itemInCategory($ck) as $i) {
        $name = $i['name'];
        if (isset($i->v)) {
            if ((string)$i->v != $config->get($name)) {
                $items[] = [
                    'category' => $cv,
                    'label' => (string)$i->v['comment'],
                    'name' => (string)$i['name'],
                    'type' => 'v',
                    'value' => $config->get($name),
                    'default' => (string)$i->v,
                ];
            }
        }
        if (isset($i->p1)) {
            if ((string)$i->p1 != $config->param1($name)) {
                $items[] = [
                    'category' => $cv,
                    'label' => (string)$i->p1['comment'],
                    'name' => (string)$i['name'],
                    'type' => 'p1',
                    'value' => $config->param1($name),
                    'default' => (string)$i->p1,
                ];
            }
        }
        if (isset($i->p2)) {
            if ((string)$i->p2 != $config->param2($name)) {
                $items[] = [
                    'category' => $cv,
                    'label' => (string)$i->p2['comment'],
                    'name' => (string)$i['name'],
                    'type' => 'p2',
                    'value' => $config->param2($name),
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
