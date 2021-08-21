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
            foreach ($this->getFields() as $k => $a) {
                $values[$a['name']] = ':'.$a['name'];
                $params[$a['name']] = $a['def_val'] ?? "";
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
            foreach ($this->getFields() as $k => $a) {
                if ($a['show_overview'] == 1) {
                    echo "<th class=\"tbltitle\">" . $a['text'] . "</th>";
                }
            }
            echo "<th class=\"tbltitle\">Löschen</th>";
            echo "</tr>";
            foreach ($rows as $arr) {
                echo "<tr>";
                foreach ($this->getFields() as $k => $a) {
                    echo "<td class=\"tbldata\">";
                    switch ($a['type']) {
                        case "text":
                            echo "<input type=\"text\" name=\"" . $a['name'] . "[" . $arr[$this->getTableId()] . "]\" value=\"" . $arr[$a['name']] . "\" size=\"" . $a['size'] . "\" maxlength=\"" . $a['maxlen'] . "\" /></td>\n";

                            break;
                        case "numeric":
                            echo "<input type=\"text\" name=\"" . $a['name'] . "[" . $arr[$this->getTableId()] . "]\" value=\"" . $arr[$a['name']] . "\" size=\"" . $a['size'] . "\" maxlength=\"" . $a['maxlen'] . "\" /></td>\n";

                            break;
                        case "textarea":
                            echo "<input type=\"text\" name=\"" . $a['name'] . "[" . $arr[$this->getTableId()] . "]\" value=\"";
                            if (strlen($arr[$a['name']]) > 20) {
                                echo substr($arr[$a['name']], 0, 18) . "...";
                            } else {
                                echo $arr[$a['name']];
                            }
                            echo "\" /></td>\n";

                            break;
                        case "radio":
                            echo "<input type=\"text\" name=\"" . $a['name'] . "[" . $arr[$this->getTableId()] . "]\" value=\"" . $arr[$a['name']] . "\" /></td>\n";

                            break;
                        case "select":
                            echo "<select name=\"" . $a['name'] . "[" . $arr[$this->getTableId()] . "]\">\n";
                            if ($arr[$a['name']] == 0 || $arr[$a['name']] == "") {
                                echo "<option selected=\"selected\">(Wählen...)</option>";
                            }
                            foreach ($a['select_elem'] as $sd => $sv) {
                                echo "<option value=\"$sv\"";
                                if ($arr[$a['name']] == $sv) {
                                    echo " selected=\"selected\"";
                                }
                                echo ">$sd</option>\n";
                            }
                            echo "</select></td>\n";

                            break;
                        case "hidden":
                            echo "<input type=\"hidden\" name=\"" . $a['name'] . "[" . $arr[$this->getTableId()] . "]\" value=\"" . $arr[$a['name']] . "\" size=\"" . $a['size'] . "\" maxlength=\"" . $a['maxlen'] . "\" />\n";

                            break;
                    }
                }
                echo "<td class=\"tbldata\"><input type=\"checkbox\" name=\"del[" . $arr[$this->getTableId()] . "]\" value=\"1\" /></td>\n";
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
}
