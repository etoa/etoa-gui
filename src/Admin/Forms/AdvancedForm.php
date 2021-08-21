<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

use FleetAction;
use MessageBox;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

abstract class AdvancedForm extends Form
{
    protected function getTableSort(): ?string
    {
        return null;
    }

    protected function getTableSortParent(): ?string
    {
        return null;
    }

    protected function getImagePath(): ?string
    {
        return null;
    }

    /**
     * @return array<string,string>
     */
    protected function getSwitches(): array
    {
        return [];
    }

    protected function runPostInsertUpdateHook(): string
    {
        return '';
    }

    public static function render(Container $app, Environment $twig, Request $request): void
    {
        (new static($app, $twig))->router($request);
    }

    public function router(Request $request): void
    {
        if ($request->query->get('action') == "new") {
            $this->create();
        } elseif ($request->request->has('new')) {
            $this->store($request);
            $this->index();
        } elseif ($request->query->get('action') == "copy") {
            $this->copy($request);
            $this->index();
        } elseif ($request->query->get('action') == "edit") {
            $this->edit($request);
        } elseif ($request->request->has('edit')) {
            $this->update($request);
            $this->index();
        } elseif ($request->query->has('switch') && $request->query->getInt('id') > 0 && count($this->getSwitches()) > 0) {
            $this->switch($request);
            $this->index();
        } elseif ($request->query->has('moveUp') && $request->query->has('parentId')) {
            $this->moveUp($request);
            $this->index();
        } elseif ($request->query->has('moveDown') && $request->query->has('parentId')) {
            $this->moveDown($request);
            $this->index();
        } elseif ($request->query->get('action') == "del") {
            $this->confirmDelete($request);
        } elseif ($request->request->has('del')) {
            $this->delete($request);
            $this->index();
        } else {
            $this->index();
        }
    }

    public function index(): void
    {
        $this->twig->addGlobal("title", $this->getName());
        $this->twig->addGlobal("subtitle", "Übersicht");

        echo "<form action=\"?" . URL_SEARCH_STRING . "\" method=\"post\">";
        echo "<input type=\"button\" value=\"Neuer Datensatz hinzufügen\" name=\"new\" onclick=\"document.location='?" . URL_SEARCH_STRING . "&amp;action=new'\" /><br/><br/>";

        $rows = $this->createQueryBuilder()
            ->select('*')
            ->from($this->getTable())
            ->orderBy($this->getOverviewOrderField(), $this->getOverviewOrder())
            ->execute()
            ->fetchAllAssociative();

        if (count($rows) > 0) {
            echo "<table width=\"100%\" cellpadding=\"3\" cellspacing=\"1\" align=\"center\"><tr>";
            if ($this->getImagePath() !== null) {
                echo "<th valign=\"top\" class=\"tbltitle\">Bild</a>";
            }
            foreach ($this->getFields() as $k => $field) {
                if ($field['show_overview'] == 1) {
                    echo "<th valign=\"top\" class=\"tbltitle\">" . $field['text'] . "</a>";
                }
            }
            if (count($this->getSwitches()) > 0) {
                foreach ($this->getSwitches() as $k => $v) {
                    echo "<th valign=\"top\" class=\"tbltitle\">";
                    echo "$k";
                    echo "</th>";
                }
            }
            if ($this->getTableSort() !== null && $this->getTableSortParent() !== null) {
                echo "<th valign=\"top\" class=\"tbltitle\">";
                echo "Sort";
                echo "</th>";
            }

            echo "<th valign=\"top\" width=\"70\" colspan=\"2\">&nbsp;</td></tr>";
            $cnt = 0;
            $parId = null;
            foreach ($rows as $arr) {
                echo "<tr>";
                if ($this->getImagePath() !== null) {
                    $path = preg_replace('/<DB_TABLE_ID>/', $arr[$this->getTableId()], $this->getImagePath());
                    if (is_file($path)) {
                        $imsize = getimagesize($path);
                        echo "<td class=\"tbldata\" style=\"background:#000;width:" . $imsize[0] . "px;\">
                            <a href=\"?" . URL_SEARCH_STRING . "&amp;action=edit&amp;id=" . $arr[$this->getTableId()] . "\">
                            <img src=\"" . $path . "\" align=\"top\"/>
                            </a></td>";
                    } else {
                        echo "<td class=\"tbldata\" style=\"background:#000;width:40px;\">
                            <a href=\"?" . URL_SEARCH_STRING . "&amp;action=edit&amp;id=" . $arr[$this->getTableId()] . "\">
                            <img src=\"../images/blank.gif\" style=\"width:40px;height:40px;\" align=\"top\"/>
                            </a></td>";
                    }
                }

                foreach ($this->getFields() as $field) {
                    if ($field['show_overview'] == 1) {
                        $isLink = isset($field['link_in_overview']) && $field['link_in_overview'] == 1;
                        echo "<td class=\"tbldata\">";
                        if ($isLink) {
                            echo "<a href=\"?" . URL_SEARCH_STRING . "&amp;action=edit&amp;id=" . $arr[$this->getTableId()] . "\">";
                        }
                        echo $this->showFieldValue($field, $arr);
                        if ($isLink) {
                            echo "</a>";
                        }
                        echo "</td>";
                    }
                }

                if (count($this->getSwitches()) > 0) {
                    foreach ($this->getSwitches() as $k => $v) {
                        echo "<td valign=\"top\" class=\"tbldata\">
                    <a href=\"?" . URL_SEARCH_STRING . "&amp;switch=" . $v . "&amp;id=" . $arr[$this->getTableId()] . "\">";
                        if ($arr[$v] == 1) {
                            echo "<img src=\"../images/true.gif\" alt=\"true\" />";
                        } else {
                            echo "<img src=\"../images/false.gif\" alt=\"true\" />";
                        }
                        echo "</td>";
                    }
                }

                if ($this->getTableSort() !== null && $this->getTableSortParent() !== null) {
                    echo "<td valign=\"top\" class=\"tbldata\" style=\"width:40px;\">";

                    if ($cnt < count($rows) - 1) {
                        echo "<a href=\"?" . URL_SEARCH_STRING . "&amp;moveDown=" . $arr[$this->getTableId()] . "&amp;parentId=" . $arr[$this->getTableSortParent()] . "\"><img src=\"../images/down.gif\" alt=\"down\" /></a> ";
                    } else {
                        echo "<img src=\"../images/blank.gif\" alt=\"blank\" style=\"width:16px;\" /> ";
                    }

                    if ($cnt != 0 && $parId == $arr[$this->getTableSortParent()]) {
                        echo "<a href=\"?" . URL_SEARCH_STRING . "&amp;moveUp=" . $arr[$this->getTableId()] . "&amp;parentId=" . $arr[$this->getTableSortParent()] . "\"><img src=\"../images/up.gif\" alt=\"up\" /></a> ";
                    } else {
                        echo "<img src=\"../images/blank.gif\" alt=\"blank\" style=\"width:16px;\" /> ";
                    }

                    echo "</td>";

                    $parId = $arr[$this->getTableSortParent()];
                }

                echo "<td valign=\"top\" class=\"tbldata\" style=\"width:50px\">
            " . edit_button("?" . URL_SEARCH_STRING . "&amp;action=edit&amp;id=" . $arr[$this->getTableId()]) . "
            " . copy_button("?" . URL_SEARCH_STRING . "&amp;action=copy&amp;id=" . $arr[$this->getTableId()]) . "
            " . del_button("?" . URL_SEARCH_STRING . "&amp;action=del&amp;id=" . $arr[$this->getTableId()]) . "
            </td>";
                echo "</tr>\n";
                $cnt++;
            }
        }
        echo "</table></form>";
    }

    /**
     * @param array<string,mixed> $field
     * @param array<string,string> $arr
     */
    private function showFieldValue(array $field, array $arr): string
    {
        switch ($field['type']) {
            case "textarea":
                $str = $arr[$field['name']];
                if (strlen($str) > 100) {
                    $str = explode("\n", wordwrap($str, 100));
                    $str = $str[0] . '...';
                }

                return $str;
            case "radio":
                foreach ($field['rcb_elem'] as $rk => $rv) {
                    if ($arr[$field['name']] == $rv) {
                        return $rk;
                    }
                }

                return '-';
            case "select":
                foreach ($field['select_elem'] as $sd => $sv) {
                    if ($arr[$field['name']] == $sv) {
                        return $sd;
                    }
                }

                return '-';
            case "fleetaction":
                $keys = explode(",", $arr[$field['name']]);
                $actions = FleetAction::getAll();
                $labels = [];
                foreach ($actions as $ac) {
                    if (in_array($ac->code(), $keys, true)) {
                        $labels[] = $ac->displayName();
                    }
                }

                return implode(', ', $labels);
            default:
                return $arr[$field['name']];
        }
    }

    public function create(): void
    {
        $this->twig->addGlobal("title", $this->getName());
        $this->twig->addGlobal("subtitle", "Neuer Datensatz");

        echo "<form action=\"?" . URL_SEARCH_STRING . "\" method=\"post\">";
        echo "<table>";
        $this->createNewDataset();
        echo "</table><br/>";
        echo "<input type=\"submit\" value=\"Neuen Datensatz speichern\" name=\"new\" />&nbsp;";
        echo "<input type=\"button\" value=\"Abbrechen\" name=\"newcancel\" onclick=\"document.location='?" . URL_SEARCH_STRING . "'\" />";
        echo "</form>";
    }

    private function createNewDataset(): void
    {
        foreach ($this->getFields() as $field) {
            switch ($field['type']) {
                case "readonly":
                    break;
                case "text":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $field['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\"><input type=\"text\" name=\"" . $field['name'] . "\" size=\"" . $field['size'] . "\" maxlength=\"" . $field['maxlen'] . "\" value=\"" . $field['def_val'] . "\" /></td></tr>";

                    break;
                case "hidden":
                    echo "<input type=\"hidden\" name=\"" . $field['name'] . "\" value=\"" . $field['def_val'] . "\" />";

                    break;
                case "numeric":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $field['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\"><input type=\"text\" name=\"" . $field['name'] . "\" size=\"" . $field['size'] . "\" maxlength=\"" . $field['maxlen'] . "\" value=\"" . $field['def_val'] . "\" /></td></tr>";

                    break;
                case "textarea":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $field['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\"><textarea name=\"" . $field['name'] . "\" rows=\"" . $field['rows'] . "\" cols=\"" . $field['cols'] . "\">" . $field['def_val'] . "</textarea></td></tr>";

                    break;
                case "radio":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $field['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">";
                    foreach ($field['rcb_elem'] as $rk => $rv) {
                        echo $rk . ": <input name=\"" . $field['name'] . "\" type=\"radio\" value=\"$rv\"";
                        if ($field['rcb_elem_checked'] == $rv) {
                            echo " checked=\"checked\"";
                        }
                        echo " /> ";
                    }
                    echo "</td></tr>";

                    break;
                case "select":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $field['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\"><select name=\"" . $field['name'] . "\">";
                    foreach ($field['select_elem'] as $rk => $rv) {
                        echo "<option value=\"$rv\"";
                        if (isset($field['select_elem_checked']) && $field['select_elem_checked'] == $rv) {
                            echo " selected=\"selected\"";
                        }
                        echo ">$rk</option> ";
                    }
                    echo "</td></tr>";

                    break;
                case "fleetaction":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $field['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">";
                    $actions = FleetAction::getAll();
                    foreach ($actions as $ac) {
                        echo "<label><input name=\"" . $field['name'] . "[]\" type=\"checkbox\" value=\"" . $ac->code() . "\" /> " . $ac . "</label><br/>";
                    }
                    echo "</td></tr>";

                    break;
                default:
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $field['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\"><input type=\"text\" name=\"" . $field['name'] . "\" size=\"" . $field['size'] . "\" maxlength=\"" . $field['maxlen'] . "\" value=\"" . $field['def_val'] . "\" /></td></tr>";

                    break;
            }
        }
    }

    public function store(Request $request): void
    {
        $this->insertRecord($request);
        $hookResult = $this->runPostInsertUpdateHook();
        echo MessageBox::ok("", "Neuer Datensatz gespeichert!" . (filled($hookResult) ? ' ' . $hookResult : ''));
    }

    private function insertRecord(Request $request): void
    {
        $values = [];
        $params = [];

        foreach ($this->getFields() as $field) {
            switch ($field['type']) {
                case "readonly":
                    break;
                case "fleetaction":
                    $values[$field['name']] = ':' . $field['name'];
                    $params[$field['name']] = is_array($request->request->get($field['name']))
                        ? implode(",", $request->request->get($field['name']))
                        : "";

                    break;
                default:
                    $values[$field['name']] = ':' . $field['name'];
                    $params[$field['name']] = $request->request->get($field['name']);
            }
        }

        $this->createQueryBuilder()
            ->insert($this->getTable())
            ->values($values)
            ->setParameters($params)
            ->execute();
    }

    public function copy(Request $request): void
    {
        DuplicateMySQLRecord($this->getTable(), $this->getTableId(), $request->query->get('id'));
        echo MessageBox::ok("", "Datensatz kopiert!");
    }

    public function edit(Request $request): void
    {
        $this->twig->addGlobal("title", $this->getName());
        $this->twig->addGlobal("subtitle", "Datensatz bearbeiten");

        $arr = $this->createQueryBuilder()
            ->select('*')
            ->from($this->getTable())
            ->where($this->getTableId() . " = :id")
            ->setParameter('id', $request->query->get('id'))
            ->execute()
            ->fetchAssociative();

        if ($arr !== false) {
            echo "<form action=\"?" . URL_SEARCH_STRING . "\" method=\"post\">";
            echo "<input type=\"submit\" value=\"Übernehmen\" name=\"edit\" />&nbsp;";
            echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?" . URL_SEARCH_STRING . "'\" /><br/><br/>";

            echo "<input type=\"hidden\" name=\"" . $this->getTableId() . "\" value=\"" . $request->query->get('id') . "\" />";
            echo "<table>";
            $this->editDataset($arr);
            echo "</table><br/>";
            echo "<input type=\"submit\" value=\"Übernehmen\" name=\"edit\" />&nbsp;";
            echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?" . URL_SEARCH_STRING . "'\" />";
            echo "</form>";
        } else {
            echo MessageBox::error("", "Datensatz nicht vorhanden.");
            echo "<input type=\"button\" value=\"Übersicht\" onclick=\"document.location='?" . URL_SEARCH_STRING . "'\" />";
        }
    }

    /**
     * @param array<string,string> $arr
     */
    private function editDataset(array $arr): void
    {
        $hidden_rows = array();

        echo "<tr><td style=\"vertical-align:top;\"><table style=\"width:100%;\">";
        foreach ($this->getFields() as $fieldDefinition) {
            echo "<tr id=\"row_" . $fieldDefinition['name'] . "\"";
            if (in_array($fieldDefinition['name'], $hidden_rows, true)) {
                echo " style=\"display:none;\"";
            }

            echo ">\n<th class=\"tbltitle\" width=\"200\">" . $fieldDefinition['text'] . ":</th>\n";
            echo "<td class=\"tbldata\" width=\"200\">\n";
            $stl = (isset($fieldDefinition['def_val']) && $arr[$fieldDefinition['name']] != $fieldDefinition['def_val'] ? ' class="changed"' : '');
            switch ($fieldDefinition['type']) {
                case "readonly":
                    echo $arr[$fieldDefinition['name']];

                    break;
                case "text":
                    echo "<input $stl type=\"text\" name=\"" . $fieldDefinition['name'] . "\" size=\"" . $fieldDefinition['size'] . "\" maxlength=\"" . $fieldDefinition['maxlen'] . "\" value=\"" . $arr[$fieldDefinition['name']] . "\" />";

                    break;
                case "hidden":
                    echo "<input type=\"hidden\" name=\"" . $fieldDefinition['name'] . "\" value=\"" . $arr[$fieldDefinition['name']] . "\" />";

                    break;
                case "numeric":
                    echo "<input $stl type=\"text\" name=\"" . $fieldDefinition['name'] . "\" size=\"" . $fieldDefinition['size'] . "\" maxlength=\"" . $fieldDefinition['maxlen'] . "\" value=\"" . $arr[$fieldDefinition['name']] . "\" />";

                    break;
                case "textarea":
                    echo "<textarea $stl name=\"" . $fieldDefinition['name'] . "\" rows=\"" . $fieldDefinition['rows'] . "\" cols=\"" . $fieldDefinition['cols'] . "\">" . $arr[$fieldDefinition['name']] . "</textarea>";

                    break;
                case "radio":
                    foreach ($fieldDefinition['rcb_elem'] as $rk => $rv) {
                        echo $rk . ": <input name=\"" . $fieldDefinition['name'] . "\" type=\"radio\" value=\"$rv\"";
                        if ($arr[$fieldDefinition['name']] == $rv) {
                            echo " checked=\"checked\"";
                        }

                        $onclick_actions = array();

                        // Zeige andere Elemente wenn Einstellung aktiv
                        if (isset($fieldDefinition['show_hide'])) {
                            foreach ($fieldDefinition['show_hide'] as $sh) {
                                $onclick_actions[] = "document.getElementById('row_" . $sh . "').style.display='" . ($rv == 1 ? "" : "none") . "';";
                            }
                        }

                        // Verstecke andere Elemente wenn Einstellung aktiv
                        if (isset($fieldDefinition['hide_show'])) {
                            foreach ($fieldDefinition['hide_show'] as $sh) {
                                $onclick_actions[] = "document.getElementById('row_" . $sh . "').style.display='" . ($rv == 1 ? "none" : "") . "';";
                            }
                        }

                        if (count($onclick_actions) > 0) {
                            echo " onclick=\"" . implode("", $onclick_actions) . "\"";
                        }

                        echo " /> ";

                        if (isset($fieldDefinition['show_hide']) && $arr[$fieldDefinition['name']] == $rv) {
                            $hidden_rows = $fieldDefinition['show_hide'];
                        }
                        if (isset($fieldDefinition['hide_show']) && $arr[$fieldDefinition['name']] != $rv) {
                            $hidden_rows = $fieldDefinition['hide_show'];
                        }
                    }

                    break;
                case "select":
                    echo "<select name=\"" . $fieldDefinition['name'] . "\">";
                    echo "<option value=\"\">(leer)</option>";
                    foreach ($fieldDefinition['select_elem'] as $rk => $rv) {
                        echo "<option value=\"$rv\"";
                        if ($arr[$fieldDefinition['name']] == $rv) {
                            echo " selected=\"selected\"";
                        }
                        echo ">$rk</option> ";
                    }
                    echo "";

                    break;
                case "fleetaction":
                    echo "";
                    $keys = explode(",", $arr[$fieldDefinition['name']]);
                    $actions = FleetAction::getAll();
                    foreach ($actions as $ac) {
                        echo "<label><input name=\"" . $fieldDefinition['name'] . "[]\" type=\"checkbox\" value=\"" . $ac->code() . "\"";
                        if (in_array($ac->code(), $keys, true)) {
                            echo " checked=\"checked\"";
                        }
                        echo " /> " . $ac . "</label><br/>";
                    }
                    echo "";

                    break;
                default:
                    echo "<input type=\"text\" name=\"" . $fieldDefinition['name'] . "\" size=\"" . $fieldDefinition['size'] . "\" maxlength=\"" . $fieldDefinition['maxlen'] . "\" value=\"" . $arr[$fieldDefinition['name']] . "\" />";
            }
            echo "</td>\n</tr>\n";
            if (isset($fieldDefinition['line']) && $fieldDefinition['line'] == 1) {
                echo "<tr><td style=\"height:4px;background:#000\" colspan=\"2\"></td></tr>";
            }
            if (isset($fieldDefinition['columnend']) && $fieldDefinition['columnend'] == 1) {
                echo "</table></td><td style=\"vertical-align:top;\"><table style=\"width:100%;\">";
            }
        }
        echo "</table></td></tr>";
    }

    public function update(Request $request): void
    {
        if ($this->updateRecord($request)) {
            $hookResult = $this->runPostInsertUpdateHook();
            echo MessageBox::ok("", "Datensatz geändert!" . (filled($hookResult) ? ' ' . $hookResult : ''));
        }
    }

    private function updateRecord(Request $request): bool
    {
        $qb = $this->createQueryBuilder()
            ->update($this->getTable())
            ->where($this->getTableId() . " = :" . $this->getTableId());

        $params = [
            $this->getTableId() => $request->request->get($this->getTableId()),
        ];

        foreach ($this->getFields() as $field) {
            switch ($field['type']) {
                case "readonly":
                    break;
                case "fleetaction":
                    $qb->set($field['name'], ':' . $field['name']);
                    $params[$field['name']] = is_array($request->request->get($field['name']))
                        ? implode(",", $request->request->get($field['name']))
                        : "";

                    break;
                default:
                    $qb->set($field['name'], ':' . $field['name']);
                    $params[$field['name']] = $request->request->get($field['name']);
            }
        }

        $affected = (int) $qb->setParameters($params)
            ->execute();

        return $affected > 0;
    }

    public function switch(Request $request): void
    {
        $affected = (int) $this->createQueryBuilder()
            ->update($this->getTable())
            ->set($request->query->get('switch'), "(" . $request->query->get('switch') . " + 1) % 2")
            ->where($this->getTableId() . ' = :id')
            ->setParameters([
                'id' => $request->query->get('id'),
            ])
            ->execute();

        if ($affected > 0) {
            echo MessageBox::ok("", "Aktion ausgeführt!");
        }
    }

    public function moveUp(Request $request): void
    {
        $ids = $this->createQueryBuilder()
            ->select($this->getTableId())
            ->from($this->getTable())
            ->where($this->getTableSortParent() . " = :parentId")
            ->orderBy($this->getTableSort())
            ->setParameter('parentId', $request->query->get('parentId'))
            ->execute()
            ->fetchFirstColumn();

        $cnt = 0;
        $sorter = 0;
        foreach ($ids as $id) {
            $this->createQueryBuilder()
                ->update($this->getTable())
                ->set($this->getTableSort(), (string) $cnt)
                ->where($this->getTableId() . " = :id")
                ->setParameter('id', $id)
                ->execute();

            if ($request->query->get('moveUp') == $id) {
                $sorter = $cnt;
            }
            $cnt++;
        }

        $this->createQueryBuilder()
            ->update($this->getTable())
            ->set($this->getTableSort(), (string) $sorter)
            ->where($this->getTableSortParent() . " = :parentId")
            ->andWhere($this->getTableSort() . " = " . ($sorter - 1))
            ->setParameter('parentId', $request->query->get('parentId'))
            ->execute();

        $this->createQueryBuilder()
            ->update($this->getTable())
            ->set($this->getTableSort(), (string) ($sorter - 1))
            ->where($this->getTableId() . " = :sortUp")
            ->setParameter('sortUp', $request->query->get('moveUp'))
            ->execute();
    }

    public function moveDown(Request $request): void
    {
        $ids = $this->createQueryBuilder()
            ->select($this->getTableId())
            ->from($this->getTable())
            ->where($this->getTableSortParent() . " = :parentId")
            ->orderBy($this->getTableSort())
            ->setParameter('parentId', $request->query->get('parentId'))
            ->execute()
            ->fetchFirstColumn();

        $cnt = 0;
        $sorter = 0;
        foreach ($ids as $id) {
            $this->createQueryBuilder()
                ->update($this->getTable())
                ->set($this->getTableSort(), (string) $cnt)
                ->where($this->getTableId() . " = :id")
                ->setParameter('id', $id)
                ->execute();

            if ($request->query->get('moveDown') == $id) {
                $sorter = $cnt;
            }
            $cnt++;
        }

        $this->createQueryBuilder()
            ->update($this->getTable())
            ->set($this->getTableSort(), (string) $sorter)
            ->where($this->getTableSortParent() . " = :parentId")
            ->andWhere($this->getTableSort() . " = " . ($sorter + 1))
            ->setParameter('parentId', $request->query->get('parentId'))
            ->execute();

        $this->createQueryBuilder()
            ->update($this->getTable())
            ->set($this->getTableSort(), (string) ($sorter + 1))
            ->where($this->getTableId() . " = :sortUp")
            ->setParameter('sortUp', $request->query->get('moveDown'))
            ->execute();
    }

    public function confirmDelete(Request $request): void
    {
        $this->twig->addGlobal("title", $this->getName());
        $this->twig->addGlobal("subtitle", "Datensatz löschen");

        $arr = $this->createQueryBuilder()
            ->select('*')
            ->from($this->getTable())
            ->where($this->getTableId() . " = :id")
            ->setParameter('id', $request->query->get('id'))
            ->execute()
            ->fetchAssociative();

        if ($arr !== false) {
            echo "<p>Bitte bestätige das Löschen des folgenden Datensatzes:</p>";
            echo "<form action=\"?" . URL_SEARCH_STRING . "\" method=\"post\">";
            echo "<input type=\"hidden\" name=\"" . $this->getTableId() . "\" value=\"" . $request->query->get('id') . "\" />";
            echo "<table>";
            foreach ($this->getFields() as $field) {
                echo "<tr>
                <th class=\"tbltitle\" width=\"200\">" . $field['text'] . ":</th>
                <td class=\"tbldata\" width=\"200\">" . $this->showFieldValue($field, $arr) . "</td>
            </tr>";
            }

            echo "</table><br/>";
            echo "<input type=\"submit\" value=\"Löschen\" name=\"del\" />&nbsp;";
            echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?" . URL_SEARCH_STRING . "'\" />";
            echo "</form>";
        } else {
            echo MessageBox::error("", "Datensatz nicht vorhanden.");
            echo "<input type=\"button\" value=\"Übersicht\" onclick=\"document.location='?" . URL_SEARCH_STRING . "'\" />";
        }
    }

    public function delete(Request $request): void
    {
        $affected = (int) $this->createQueryBuilder()
            ->delete($this->getTable())
            ->where($this->getTableId() . ' = :id')
            ->setParameter('id', $request->request->get($this->getTableId()))
            ->execute();

        if ($affected > 0) {
            echo MessageBox::ok("", "Datensatz wurde gelöscht!");
        }
    }

    /**
     * @param null|array<mixed,string> $additional_values
     * @return array<mixed,string>
     */
    protected function getSelectElements(string $table, string $value_field, string $text_field, string $order, ?array $additional_values = null): array
    {
        $r_array = array();
        if ($additional_values !== null && count($additional_values) > 0) {
            foreach ($additional_values as $val => $key) {
                $r_array[$key] = $val;
            }
        }

        $rows = $this->createQueryBuilder()
            ->select($value_field, $text_field)
            ->from($table)
            ->orderBy($order)
            ->execute()
            ->fetchAllAssociative();

        foreach ($rows as $arr) {
            $r_array[$arr[$text_field]] = $arr[$value_field];
        }

        return $r_array;
    }
}
