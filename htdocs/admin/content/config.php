<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

/** @var Request */
$request = Request::createFromGlobals();

if ($sub === 'restoredefaults') {
    restore($request, $config, $twig);
} elseif ($sub === 'check') {
    check($config, $twig);
} elseif ($sub === 'editor') {
    editor($request, $config, $twig);
} else {
    commonConfig($request, $config, $twig);
}

function restore(Request $request, ConfigurationService $config, Environment $twig)
{
    $successMessage = null;
    $items = [];
    if ($request->request->has('restoresubmit')) {
        if (($cnt = $config->restoreDefaults()) > 0) {
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
}

function check(ConfigurationService $config, Environment $twig)
{
    $cnt = 0;
    $message = '';
    if ($xml = simplexml_load_file(RELATIVE_ROOT . "config/defaults.xml")) {
        foreach ($xml->items->item as $i) {
            if ($config->has($i['name'])) {
                $message .= $i['name'] . ' existiert in der Standardkonfiguration, aber nicht in der Datenbank! ';
                $config->set((string)$i['name'], (string)$i->v, (string)$i->p1, (string)$i->p2);
                $message .= '<b>Behoben</b><br/>';
            }
            $cnt++;
        }
    }
    $message .= '<p>' . $cnt . ' Einträge in der Standardkonfiguration.</p>';

    $cnt = 0;
    foreach ($config->all() as $cn => $ci) {
        $cnt++;
        $found = false;
        foreach ($xml->items->item as $i) {
            if ($i['name'] == $cn) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $message .= $cn . ' existiert in der Datenbank, aber nicht in der Standardkonfiguration! ';
            $config->forget($cn);
            $message .= '<b>Gelöscht</b><br/>';
        }
    }

    $message .= '<p>' . $cnt . ' Datensätze in der Datenbank.</p>';
    $message .= '<p>Prüfung abgeschlossen!</p>';

    echo $twig->render('admin/config/check.html.twig', [
        'message' => $message,
    ]);
    exit();
}

function editor(Request $request, ConfigurationService $config, Environment $twig)
{
    $successMessage = null;
    $activeTab = null;
    // Load categories
    $categories = $config->categories();

    // Current category
    $currentCategory = current(array_keys($categories));
    if ($request->query->has('category') && isset($categories[$request->query->get('category')])) {
        $currentCategory = $request->query->get('category');
    }

    // Save values
    if ($request->request->has('submit')) {
        foreach ($categories as $ck => $cv) {
            if ($currentCategory == $ck) {
                foreach ($config->itemInCategory($ck) as $i) {
                    $v = isset($i->v) ? getFormValue((string)$i->v['type'], (string)$i['name'], "v", $request->request->all()) : "";
                    $p1 = isset($i->p1) ? getFormValue((string)$i->p1['type'], (string)$i['name'], "p1", $request->request->all()) : "";
                    $p2 = isset($i->p2) ? getFormValue((string)$i->p2['type'], (string)$i['name'], "p2", $request->request->all()) : "";
                    $config->set((string)$i['name'], $v, $p1, $p2);
                }
            }
        }
        BackendMessage::reloadConfig();
        $successMessage = 'Änderungen wurden übernommen!';
        $activeTab = $request->request->get('activeTab');
    }

    // Iterate over all entries and show current category
    $configData = array();
    $items = [];
    foreach ($categories as $ck => $cv) {
        $configData[$ck] = $cv;

        if ($currentCategory == $ck) {
            foreach ($config->itemInCategory($ck) as $i) {
                $name = $i['name'];
                if (isset($i->v)) {
                    $items[] = [
                        'label' => $i->v['comment'],
                        'name' => $i['name'],
                        'type' => 'v',
                        'field' => displayField($config, (string)$i->v['type'], (string) $i['name'], "v"),
                        'default' =>  (string) $i->v,
                        'changed' =>  (string) $i->v != $config->get($name)
                    ];
                }
                if (isset($i->p1)) {
                    $items[] = [
                        'label' => $i->p1['comment'],
                        'name' => $i['name'],
                        'type' => 'p1',
                        'field' => displayField($config, (string)$i->p1['type'], (string) $i['name'], "p1"),
                        'default' => (string) $i->p1,
                        'changed' => (string) $i->p1 != $config->param1($name)
                    ];
                }
                if (isset($i->p2)) {
                    $items[] = [
                        'label' => $i->p2['comment'],
                        'name' => $i['name'],
                        'type' => 'p2',
                        'field' => displayField($config, (string)$i->p2['type'], (string) $i['name'], "p2"),
                        'default' => (string) $i->p2,
                        'changed' => (string) $i->p2 != $config->param2($name)
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
}

function commonConfig(Request $request, ConfigurationService $config, Environment $twig)
{
    $successMessage = null;
    if ($request->request->has('submit')) {
        foreach ($config->getBaseItems() as $i) {
            $v = isset($i->v) ? getFormValue((string)$i->v['type'], (string)$i['name'], "v", $request->request->all()) : "";
            $p1 = isset($i->p1) ? getFormValue((string)$i->p1['type'], (string)$i['name'], "p1", $request->request->all()) : "";
            $p2 = isset($i->p2) ? getFormValue((string)$i->p2['type'], (string)$i['name'], "p2", $request->request->all()) : "";
            $config->set((string)$i['name'], $v, $p1, $p2);
        }
        BackendMessage::reloadConfig();
        $successMessage = 'Änderungen wurden übernommen!';
    }
    $items = [];
    foreach ($config->getBaseItems() as $i) {
        if (isset($i->v)) {
            $items[] = [
                'label' => $i->v['comment'],
                'name' => $i['name'],
                'field' => displayField($config, (string)$i->v['type'], (string)$i['name'], "v"),
            ];
        }
        if (isset($i->p1)) {
            $items[] = [
                'label' => $i->p1['comment'],
                'name' => $i['name'],
                'field' => displayField($config, (string)$i->p1['type'], (string)$i['name'], "p1"),
            ];
        }
        if (isset($i->p2)) {
            $items[] = [
                'label' => $i->p2['comment'],
                'name' => $i['name'],
                'field' => displayField($config, (string)$i->p2['type'], (string)$i['name'], "p2"),
            ];
        }
    }

    echo $twig->render('admin/config/index.html.twig', [
        'successMessage' => $successMessage,
        'items' => $items,
    ]);
    exit();
}

function displayField(ConfigurationService $config, $type, $confname, $field)
{
    $id = "config_" . $field . "[" . $confname . "]";
    if ($field == 'p1') {
        $value = $config->param1($confname);
    } elseif ($field == 'p2') {
        $value = $config->param2($confname);
    } else {
        $value = $config->get($confname);
    }

    ob_start();
    switch ($type) {
        case "text":
            echo "<input type=\"text\" id=\"$id\" name=\"$id\" class=\"inputfield-$type\" value=\"" . $value . "\" />";
            break;
        case "int":
            echo "<input type=\"number\" id=\"$id\" name=\"$id\" class=\"inputfield-$type\" value=\"" . $value . "\" />";
            break;
        case "float":
            echo "<input type=\"number\" id=\"$id\" name=\"$id\" step=\"any\" class=\"inputfield-$type\" value=\"" . $value . "\" />";
            break;
        case "textarea":
            echo "<textarea id=\"$id\" name=\"$id\" rows=\"4\" cols=\"50\" class=\"inputfield-$type\">" . $value . "</textarea>";
            break;
        case "onoff":
            echo "<input type=\"radio\" id=\"" . $id . "_1\" name=\"" . $id . "\" value=\"1\" class=\"inputfield-$type\" ";
            if ($value == 1) echo " checked=\"checked\"";
            echo " /><label for=\"" . $id . "_1\">Ja</label>  &nbsp;  <input type=\"radio\" id=\"" . $id . "_0\" name=\"" . $id . "\" value=\"0\"  class=\"inputfield-$type\" ";
            if ($value == 0) echo " checked=\"checked\"";
            echo " /> <label for=\"" . $id . "_0\">Nein</label>";
            break;
        case "timedate":
            $confValue = $value;
            if ($confValue instanceof SimpleXMLElement) {
                $confValue = (string)$confValue;
            }

            echo "<select name=\"config_" . $field . "_d[" . $confname . "]\" class=\"inputfield-$type\">";
            for ($x = 1; $x < 32; $x++) {
                echo "<option value=\"$x\"";
                if (date("d", $confValue) == $x) echo " selected=\"selected\"";
                echo ">";
                if ($x < 10) echo 0;
                echo "$x</option>";
            }
            echo "</select>.";
            echo "<select name=\"config_" . $field . "_m[" . $confname . "]\" class=\"inputfield-$type\">";
            for ($x = 1; $x < 32; $x++) {
                echo "<option value=\"$x\"";
                if (date("m", $confValue) == $x) echo " selected=\"selected\"";
                echo ">";
                if ($x < 10) echo 0;
                echo "$x</option>";
            }
            echo "</select>.";
            echo "<select name=\"config_" . $field . "_y[" . $confname . "]\" class=\"inputfield-$type\">";
            for ($x = (int) date("Y") - 50; $x < (int) date("Y") + 50; $x++) {
                echo "<option value=\"$x\"";
                if (date("Y", $confValue) == $x) echo " selected=\"selected\"";
                echo ">$x</option>";
            }
            echo "</select> ";
            echo "<select name=\"config_" . $field . "_h[" . $confname . "]\" class=\"inputfield-$type\">";
            for ($x = 0; $x < 25; $x++) {
                echo "<option value=\"$x\"";
                if (date("H", $confValue) == $x) echo " selected=\"selected\"";
                echo ">";
                if ($x < 10) echo 0;
                echo "$x</option>";
            }
            echo "</select>:";
            echo "<select name=\"config_" . $field . "_i[" . $confname . "]\" class=\"inputfield-$type\">";
            for ($x = 0; $x < 60; $x++) {
                echo "<option value=\"$x\"";
                if (date("i", $confValue) == $x) echo " selected=\"selected\"";
                echo ">";
                if ($x < 10) echo 0;
                echo "$x</option>";
            }
            echo "</select>:";
            echo "<select name=\"config_" . $field . "_s[" . $confname . "]\" class=\"inputfield-$type\">";
            for ($x = 0; $x < 60; $x++) {
                echo "<option value=\"$x\"";
                if (date("s", $confValue) == $x) echo " selected=\"selected\"";
                echo ">";
                if ($x < 10) echo 0;
                echo "$x</option>";
            }
            echo "</select>";
    }
    return ob_get_clean();
}

function getFormValue($type, $confname, $field, $postarray)
{
    switch ($type) {
        case "timedate":
            return mktime(
                $postarray['config_' . $field . '_h'][$confname],
                $postarray['config_' . $field . '_i'][$confname],
                $postarray['config_' . $field . '_s'][$confname],
                $postarray['config_' . $field . '_m'][$confname],
                $postarray['config_' . $field . '_d'][$confname],
                $postarray['config_' . $field . '_y'][$confname]
            );
        default:
            return $postarray['config_' . $field][$confname];
    }
}
