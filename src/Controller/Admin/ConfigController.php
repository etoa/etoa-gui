<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Backend\BackendMessageService;
use EtoA\Core\Configuration\ConfigurationDefinitionsRepository;
use EtoA\Core\Configuration\ConfigurationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ConfigController extends AbstractAdminController
{
    public function __construct(
        private readonly ConfigurationDefinitionsRepository $definitions,
        private readonly ConfigurationService               $config,
        private readonly BackendMessageService              $backendMessageService
    )
    {
    }

    #[Route("/admin/config/", name: "admin.config")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function common(Request $request): Response
    {
        $successMessage = null;
        if ($request->isMethod('POST')) {
            foreach ($this->definitions->getBaseItems() as $i) {
                $v = isset($i->v) ? $this->getFormValue((string)$i->v['type'], (string)$i['name'], "v", $request->request->all()) : "";
                $p1 = isset($i->p1) ? $this->getFormValue((string)$i->p1['type'], (string)$i['name'], "p1", $request->request->all()) : "";
                $p2 = isset($i->p2) ? $this->getFormValue((string)$i->p2['type'], (string)$i['name'], "p2", $request->request->all()) : "";
                $this->config->set((string)$i['name'], $v, $p1, $p2);
            }
            $this->backendMessageService->reloadConfig();
            $this->addFlash('success', 'Änderungen wurden übernommen!');
        }

        $items = [];
        foreach ($this->definitions->getBaseItems() as $i) {
            if (isset($i->v)) {
                $items[] = [
                    'label' => $i->v['comment'],
                    'name' => $i['name'],
                    'field' => $this->displayField($this->config, (string)$i->v['type'], (string)$i['name'], "v"),
                ];
            }
            if (isset($i->p1)) {
                $items[] = [
                    'label' => $i->p1['comment'],
                    'name' => $i['name'],
                    'field' => $this->displayField($this->config, (string)$i->p1['type'], (string)$i['name'], "p1"),
                ];
            }
            if (isset($i->p2)) {
                $items[] = [
                    'label' => $i->p2['comment'],
                    'name' => $i['name'],
                    'field' => $this->displayField($this->config, (string)$i->p2['type'], (string)$i['name'], "p2"),
                ];
            }
        }

        return $this->render('admin/config/index.html.twig', [
            'successMessage' => $successMessage,
            'items' => $items,
        ]);
    }

    #[Route("/admin/config/editor", name: "admin.config.editor")]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function editor(Request $request): Response
    {
        $activeTab = null;
        // Load categories
        $categories = $this->definitions->categories();

        // Current category
        $currentCategory = current(array_keys($categories));
        if ($request->query->has('category') && isset($categories[$request->query->getInt('category')])) {
            $currentCategory = $request->query->getInt('category');
        }

        // Save values
        if ($request->isMethod('POST')) {
            foreach ($categories as $ck => $cv) {
                if ($currentCategory === $ck) {
                    foreach ($this->definitions->itemInCategory($ck) as $i) {
                        $name = (string)$i['name'];
                        $v = isset($i->v) ? $this->getFormValue((string)$i->v['type'], $name, "v", $request->request->all()) : "";
                        $p1 = isset($i->p1) ? $this->getFormValue((string)$i->p1['type'], $name, "p1", $request->request->all()) : "";
                        $p2 = isset($i->p2) ? $this->getFormValue((string)$i->p2['type'], $name, "p2", $request->request->all()) : "";
                        $this->config->set($name, $v, $p1, $p2);
                    }
                }
            }
            $this->backendMessageService->reloadConfig();
            $activeTab = $request->request->get('activeTab');

            $this->addFlash('success', 'Änderungen wurden übernommen!');
        }

        // Iterate over all entries and show current category
        $configData = array();
        $items = [];
        foreach ($categories as $ck => $cv) {
            $configData[$ck] = $cv;

            if ($currentCategory == $ck) {
                foreach ($this->definitions->itemInCategory($ck) as $i) {
                    $name = (string)$i['name'];
                    if (isset($i->v)) {
                        $items[] = [
                            'label' => $i->v['comment'],
                            'name' => $i['name'],
                            'type' => 'v',
                            'field' => $this->displayField($this->config, (string)$i->v['type'], $name, "v"),
                            'default' => (string)$i->v,
                            'changed' => (string)$i->v != $this->config->get($name),
                        ];
                    }
                    if (isset($i->p1)) {
                        $items[] = [
                            'label' => $i->p1['comment'],
                            'name' => $i['name'],
                            'type' => 'p1',
                            'field' => $this->displayField($this->config, (string)$i->p1['type'], $name, "p1"),
                            'default' => (string)$i->p1,
                            'changed' => (string)$i->p1 != $this->config->param1($name),
                        ];
                    }
                    if (isset($i->p2)) {
                        $items[] = [
                            'label' => $i->p2['comment'],
                            'name' => $i['name'],
                            'type' => 'p2',
                            'field' => $this->displayField($this->config, (string)$i->p2['type'], $name, "p2"),
                            'default' => (string)$i->p2,
                            'changed' => (string)$i->p2 != $this->config->param2($name),
                        ];
                    }
                }
            }
        }

        return $this->render('admin/config/editor.html.twig', [
            'activeTab' => $activeTab,
            'currentCategory' => $currentCategory,
            'configItems' => $items,
            'configData' => $configData,
        ]);
    }

    #[Route("/admin/config/check", name: "admin.config.check")]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function check(): Response
    {
        $cnt = 0;
        $message = '';
        $xml = $this->definitions->getXmlDefinitions();
        foreach ($xml->items->item as $i) {
            if (!$this->config->has((string)$i['name'])) {
                $message .= $i['name'] . ' existiert in der Standardkonfiguration, aber nicht in der Datenbank! ';
                $this->config->set((string)$i['name'], (string)$i->v, (string)$i->p1, (string)$i->p2);
                $message .= '<b>Behoben</b><br/>';
            }
            $cnt++;
        }
        $message .= '<p>' . $cnt . ' Einträge in der Standardkonfiguration.</p>';

        $cnt = 0;
        foreach ($this->config->all() as $cn => $ci) {
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
                $this->config->forget($cn);
                $message .= '<b>Gelöscht</b><br/>';
            }
        }

        $message .= '<p>' . $cnt . ' Datensätze in der Datenbank.</p>';
        $message .= '<p>Prüfung abgeschlossen!</p>';

        return $this->render('admin/config/check.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route("/admin/config/restore", name: "admin.config.restore")]
    #[IsGranted('ROLE_ADMIN_MASTER')]
    public function restore(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            if (($cnt = $this->config->restoreDefaults()) > 0) {
                $this->config->reload();
                $this->backendMessageService->reloadConfig();

                $this->addFlash('success', "$cnt Einstellungen wurden wiederhergestellt!");
            }
        }

        // Changed values
        $items = [];
        foreach ($this->definitions->categories() as $ck => $cv) {
            foreach ($this->definitions->itemInCategory($ck) as $i) {
                $name = (string)$i['name'];
                if (isset($i->v)) {
                    if ((string)$i->v != $this->config->get($name)) {
                        $items[] = [
                            'category' => $cv,
                            'label' => (string)$i->v['comment'],
                            'name' => $name,
                            'type' => 'v',
                            'value' => $this->config->get($name),
                            'default' => (string)$i->v,
                        ];
                    }
                }
                if (isset($i->p1)) {
                    if ((string)$i->p1 != $this->config->param1($name)) {
                        $items[] = [
                            'category' => $cv,
                            'label' => (string)$i->p1['comment'],
                            'name' => $name,
                            'type' => 'p1',
                            'value' => $this->config->param1($name),
                            'default' => (string)$i->p1,
                        ];
                    }
                }
                if (isset($i->p2)) {
                    if ((string)$i->p2 != $this->config->param2($name)) {
                        $items[] = [
                            'category' => $cv,
                            'label' => (string)$i->p2['comment'],
                            'name' => $name,
                            'type' => 'p2',
                            'value' => $this->config->param2($name),
                            'default' => (string)$i->p2,
                        ];
                    }
                }
            }
        }

        return $this->render('admin/config/restore-defaults.html.twig', [
            'changedValues' => $items,
        ]);
    }

    private function displayField(ConfigurationService $config, string $type, string $confname, string $field): string
    {
        $id = "config_" . $field . "[" . $confname . "]";
        if ($field === 'p1') {
            $value = $config->param1($confname);
        } elseif ($field === 'p2') {
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
                if ($value == 1) {
                    echo " checked=\"checked\"";
                }
                echo " /><label for=\"" . $id . "_1\">Ja</label>  &nbsp;  <input type=\"radio\" id=\"" . $id . "_0\" name=\"" . $id . "\" value=\"0\"  class=\"inputfield-$type\" ";
                if ($value == 0) {
                    echo " checked=\"checked\"";
                }
                echo " /> <label for=\"" . $id . "_0\">Nein</label>";

                break;
            case "timedate":
                $confValue = $value;

                echo "<select name=\"config_" . $field . "_d[" . $confname . "]\" class=\"inputfield-$type\">";
                for ($x = 1; $x < 32; $x++) {
                    echo "<option value=\"$x\"";
                    if (date("d", (int)$confValue) == $x) {
                        echo " selected=\"selected\"";
                    }
                    echo ">";
                    if ($x < 10) {
                        echo 0;
                    }
                    echo "$x</option>";
                }
                echo "</select>.";
                echo "<select name=\"config_" . $field . "_m[" . $confname . "]\" class=\"inputfield-$type\">";
                for ($x = 1; $x < 32; $x++) {
                    echo "<option value=\"$x\"";
                    if (date("m", (int)$confValue) == $x) {
                        echo " selected=\"selected\"";
                    }
                    echo ">";
                    if ($x < 10) {
                        echo 0;
                    }
                    echo "$x</option>";
                }
                echo "</select>.";
                echo "<select name=\"config_" . $field . "_y[" . $confname . "]\" class=\"inputfield-$type\">";
                for ($x = (int)date("Y") - 50; $x < (int)date("Y") + 50; $x++) {
                    echo "<option value=\"$x\"";
                    if (date("Y", (int)$confValue) == $x) {
                        echo " selected=\"selected\"";
                    }
                    echo ">$x</option>";
                }
                echo "</select> ";
                echo "<select name=\"config_" . $field . "_h[" . $confname . "]\" class=\"inputfield-$type\">";
                for ($x = 0; $x < 25; $x++) {
                    echo "<option value=\"$x\"";
                    if (date("H", (int)$confValue) == $x) {
                        echo " selected=\"selected\"";
                    }
                    echo ">";
                    if ($x < 10) {
                        echo 0;
                    }
                    echo "$x</option>";
                }
                echo "</select>:";
                echo "<select name=\"config_" . $field . "_i[" . $confname . "]\" class=\"inputfield-$type\">";
                for ($x = 0; $x < 60; $x++) {
                    echo "<option value=\"$x\"";
                    if (date("i", (int)$confValue) == $x) {
                        echo " selected=\"selected\"";
                    }
                    echo ">";
                    if ($x < 10) {
                        echo 0;
                    }
                    echo "$x</option>";
                }
                echo "</select>:";
                echo "<select name=\"config_" . $field . "_s[" . $confname . "]\" class=\"inputfield-$type\">";
                for ($x = 0; $x < 60; $x++) {
                    echo "<option value=\"$x\"";
                    if (date("s", (int)$confValue) == $x) {
                        echo " selected=\"selected\"";
                    }
                    echo ">";
                    if ($x < 10) {
                        echo 0;
                    }
                    echo "$x</option>";
                }
                echo "</select>";
        }

        return ob_get_clean();
    }

    /**
     * @param array<string, array<string, mixed>> $postarray
     * @return mixed
     */
    private function getFormValue(string $type, string $confname, string $field, array $postarray)
    {
        switch ($type) {
            case "timedate":
                return mktime(
                    (int)$postarray['config_' . $field . '_h'][$confname],
                    (int)$postarray['config_' . $field . '_i'][$confname],
                    (int)$postarray['config_' . $field . '_s'][$confname],
                    (int)$postarray['config_' . $field . '_m'][$confname],
                    (int)$postarray['config_' . $field . '_d'][$confname],
                    (int)$postarray['config_' . $field . '_y'][$confname]
                );
            default:
                return $postarray['config_' . $field][$confname];
        }
    }
}
