<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

use MessageBox;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

abstract class SimpleForm extends Form
{
    public static function render(Container $app, Environment $twig, Request $request): void
    {
        (new static($app, $twig))->index($request);
    }

    public function index(Request $request): void
    {
        $this->twig->addGlobal("title", $this->getName());

        if ($request->request->has('apply_submit')) {
            foreach ($request->request->all() as $key => $val) {
                if ($key != "apply_submit" && $key != "del") {
                    foreach ($val as $k => $vl) {
                        $this->createQueryBuilder()
                            ->update($this->getTable())
                            ->set($key, ':val')
                            ->where($this->getTableId() . " = :id")
                            ->setParameters([
                                'id' => $k,
                                'val' => $vl,
                            ])
                            ->execute();
                    }
                }
            }
            echo MessageBox::ok("", "Änderungen wurden übernommen!");

            $deleted = false;
            foreach ($request->request->all() as $key => $val) {
                if ($key == "del") {
                    foreach ($val as $k => $vl) {
                        $this->createQueryBuilder()
                            ->delete($this->getTable())
                            ->where($this->getTableId() . " = :id")
                            ->setParameter('id', $k)
                            ->execute();
                    }
                    $deleted = true;
                }
            }
            if ($deleted) {
                echo MessageBox::ok("", "Bestimmte Daten wurden gelöscht!");
            }
        }
        if ($request->request->has('new_submit')) {
            $values = [];
            $params = [];
            foreach ($this->getFields() as $k => $field) {
                $values[$field['name']] = ':' . $field['name'];
                $params[$field['name']] = $field['def_val'] ?? "";
            }

            $this->createQueryBuilder()
                ->insert($this->getTable())
                ->values($values)
                ->setParameters($params)
                ->execute();

            echo MessageBox::ok("", "Neuer leerer Datensatz wurde hinzugefügt!");
        }

        echo "<form action=\"?" . URL_SEARCH_STRING . "\" method=\"post\">";
        $rows = $this->createQueryBuilder()
            ->select('*')
            ->from($this->getTable())
            ->orderBy($this->getOverviewOrderField(), $this->getOverviewOrder())
            ->execute()
            ->fetchAllAssociative();

        if (count($rows) > 0) {
            echo "<table>";
            echo "<tr>";
            foreach ($this->getFields() as $k => $field) {
                if ($field['show_overview'] == 1) {
                    echo "<th class=\"tbltitle\">" . $field['text'] . "</th>";
                }
            }
            echo "<th class=\"tbltitle\">Löschen</th>";
            echo "</tr>";
            foreach ($rows as $arr) {
                echo "<tr>";
                foreach ($this->getFields() as $key => $field) {
                    echo "<td class=\"tbldata\">";
                    echo $this->createInput($field, $arr);
                    echo "</td>\n";
                }
                echo "<td class=\"tbldata\">
                    <input type=\"checkbox\" name=\"del[" . $arr[$this->getTableId()] . "]\" value=\"1\" />
                </td>\n";
                echo "</tr>\n";
            }
            echo "</table><br/>";
            echo "<input type=\"submit\" name=\"apply_submit\" value=\"Übernehmen\" />&nbsp;";
            echo "<input type=\"submit\" name=\"new_submit\" value=\"Neuer Datensatz\" />&nbsp;";
        } else {
            echo "<p align=\"center\"><i>Es existieren keine Datensätze!</i></p>";
            echo "<p align=\"center\"><input type=\"submit\" name=\"new_submit\" value=\"Neuer Datensatz\" />&nbsp;</p>";
        }
        echo "</form>";
    }

    /**
     * @param array<string,mixed> $field
     * @param array<string,string> $arr
     */
    private function createInput(array $field, array $arr): string
    {
        switch ($field['type']) {
            case "readonly":
                return $arr[$field['name']];
            case "numeric":
                return "<input
                    type=\"number\"
                    name=\"" . $field['name'] . "[" . $arr[$this->getTableId()] . "]\"
                    value=\"" . $arr[$field['name']] . "\"
                />";
            case "color":
                return "<input
                    type=\"color\"
                    name=\"" . $field['name'] . "[" . $arr[$this->getTableId()] . "]\"
                    value=\"" . $arr[$field['name']] . "\"
                />";
            case "textarea":
                return "<textarea
                    name=\"" . $field['name'] . "[" . $arr[$this->getTableId()] . "]\"
                    >" . $arr[$field['name']] . "</textarea>";
            case "select":
                $str = "<select name=\"" . $field['name'] . "[" . $arr[$this->getTableId()] . "]\">";
                if ($arr[$field['name']] == 0 || $arr[$field['name']] == "") {
                    $str .= "<option selected=\"selected\">(Wählen...)</option>";
                }
                foreach ($field['select_elem'] as $sd => $sv) {
                    $str .= "<option value=\"$sv\"";
                    if ($arr[$field['name']] == $sv) {
                        $str .= " selected=\"selected\"";
                    }
                    $str .= ">$sd</option>\n";
                }
                $str .= "</select>";

                return $str;
            case "hidden":
                return "<input
                    type=\"hidden\"
                    name=\"" . $field['name'] . "[" . $arr[$this->getTableId()] . "]\"
                    value=\"" . $arr[$field['name']] . "\"
                />";
            default:
                return "<input
                    type=\"text\"
                    name=\"" . $field['name'] . "[" . $arr[$this->getTableId()] . "]\"
                    value=\"" . $arr[$field['name']] . "\"
                    size=\"" . $field['size'] . "\"
                    maxlength=\"" . $field['maxlen'] . "\"
                />";
        }
    }
}
