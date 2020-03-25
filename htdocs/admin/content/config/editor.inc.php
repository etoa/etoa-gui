<?PHP

$successMessage = null;
$activeTab = null;
// Load categories
$categories = $cfg->categories();

// Current category
$currentCategory = current(array_keys($categories));
if (!empty($_GET['category']) && isset($categories[$_GET['category']])) {
    $currentCategory = $_GET['category'];
}

// Save values
if (isset($_POST['submit'])) {
    foreach ($categories as $ck => $cv) {
        if ($currentCategory == $ck) {
            foreach ($cfg->itemInCategory($ck) as $i) {
                $v = isset($i->v) ? create_sql_value((string)$i->v['type'],(string)$i['name'],"v",$_POST) : "";
                $p1 = isset($i->p1) ? create_sql_value((string)$i->p1['type'],(string)$i['name'],"p1",$_POST) : "";
                $p2 = isset($i->p2) ? create_sql_value((string)$i->p2['type'],(string)$i['name'],"p2",$_POST) : "";
                $cfg->add((string)$i['name'],$v,$p1,$p2);
            }
        }
    }
    BackendMessage::reloadConfig();
    $successMessage = 'Änderungen wurden übernommen!';
    $activeTab = $_POST['activeTab'];
}

// Iterate over all entries and show current category
$configData = array();
foreach ($categories as $ck => $cv) {
    $configData[$ck] = $cv;

    if ($currentCategory == $ck) {
        $items = [];
        foreach ($cfg->itemInCategory($ck) as $i) {
            $name = $i['name'];
            if (isset($i->v)) {
                $items[] = [
                    'label' => $i->v['comment'],
                    'name' => $i['name'],
                    'type' => 'v',
                    'field' => display_field((string)$i->v['type'], (string)$i['name'], "v"),
                    'default' =>  (string)$i->v,
                    'changed' =>  (string)$i->v != $cfg->$name->v
                ];
            }
            if (isset($i->p1)) {
                $items[] = [
                    'label' => $i->p1['comment'],
                    'name' => $i['name'],
                    'type' => 'p1',
                    'field' => display_field((string)$i->p1['type'], (string)$i['name'], "p1"),
                    'default' => (string)$i->p1,
                    'changed' =>  (string)$i->p1 != $cfg->$name->p1
                ];
            }
            if (isset($i->p2)) {
                $items[] = [
                    'label' => $i->p2['comment'],
                    'name' => $i['name'],
                    'type' => 'p2',
                    'field' => display_field((string)$i->p2['type'], (string)$i['name'], "p2"),
                    'default' => (string)$i->p2,
                    'changed' =>  (string)$i->p2 != $cfg->$name->p2
                ];
            }
        }
    }
}

echo $twig->render('admin/config/editor.html.twig', [
    'successMessage' => $successMessage,
    'activeTab' => $activeTab,
    'currentCategory' => $currentCategory,
    'configItems' => $items,
    'configData' => $configData,
]);
exit();
