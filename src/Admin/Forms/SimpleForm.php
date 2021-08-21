<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

use MessageBox;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

abstract class SimpleForm
{
    protected Container $app;
    private Environment $twig;

    final public function __construct(Container $app, Environment $twig)
    {
        $this->app = $app;
        $this->twig = $twig;
    }

    abstract protected function getName(): string;

    abstract protected function getTable(): string;
    abstract protected function getTableId(): string;

    abstract protected function getOverviewOrderField(): string;

    protected function getOverviewOrder(): string
    {
        return "ASC";
    }

    /**
     * Parameters:
     *
     * name	                DB Field Name
     * text	                Field Description
     * type                 Field Type: text, textarea, radio, select, numeric
     * def_val              Default Value
     * size                 Field length (text, date)
     * maxlen               Max Text length (text, date)
     * rows                 Rows (textarea)
     * cols                 Cols (textarea)
     * rcb_elem (Array)	    Checkbox-/Radio Elements (desc=>value)
     * rcb_elem_chekced	    Value of default checked Checkbox-/Radio Element (Checkbox: has to be an array)
     * select_elem (Array)  Select Elements (desc=>value)
     * select_elem_checked  Value of default checked Select Element (desc=>value)
     * show_overview        Set 1 to show on overview page
     *
     * @return array<array<string,mixed>>
     */
    abstract protected function getFields(): array;

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
                        $sql = "UPDATE " . $this->getTable() . " set $key='$vl' WHERE " . $this->getTableId() . "=$k;";
                        dbquery($sql);
                    }
                }
            }
            echo MessageBox::ok("", "Änderungen wurden übernommen!");

            $deleted = false;
            foreach ($request->request->all() as $key => $val) {
                if ($key == "del") {
                    foreach ($val as $k => $vl) {
                        dbquery("DELETE FROM " . $this->getTable() . " WHERE " . $this->getTableId() . "='$k';");
                    }
                    $deleted = true;
                }
            }
            if ($deleted) {
                echo MessageBox::ok("", "Bestimmte Daten wurden gelöscht!");
            }
        }
        if ($request->request->has('new_submit')) {
            $cnt = 1;
            $fsql = "";
            $vsql = "";
            $vsqlsp = "";
            foreach ($this->getFields() as $k => $a) {
                $fsql .= "`" . $a['name'] . "`";
                if ($cnt < sizeof($this->getFields())) {
                    $fsql .= ",";
                }
                $cnt++;
            }
            $cnt = 1;
            foreach ($this->getFields() as $k => $a) {
                $vsql .= "'" . $a['def_val'] . "'";
                if ($cnt < sizeof($this->getFields())) {
                    $vsql .= ",";
                }
                $cnt++;
            }

            $sql = "INSERT INTO " . $this->getTable() . " (";
            $sql .= $fsql;
            $sql .= ") VALUES(";
            $sql .= $vsql . $vsqlsp;
            $sql .= ");";

            dbquery($sql);
            if (mysql_error() !== '') {
                echo MessageBox::ok("", "Neuer leerer Datensatz wurde hinzugefügt!");
            }
        }

        echo "<form action=\"?" . URL_SEARCH_STRING . "\" method=\"post\">";
        $sql = "SELECT * FROM " . $this->getTable() . " ORDER BY `" . $this->getOverviewOrderField() . "` " . $this->getOverviewOrder() . ";";
        $res = dbquery($sql);
        if (mysql_num_rows($res) != 0) {
            echo "<table>";
            echo "<tr>";
            foreach ($this->getFields() as $k => $a) {
                if ($a['show_overview'] == 1) {
                    echo "<th class=\"tbltitle\">" . $a['text'] . "</th>";
                }
            }
            echo "<th class=\"tbltitle\">Löschen</th>";
            echo "</tr>";
            while ($arr = mysql_fetch_assoc($res)) {
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
                                echo stripslashes(substr($arr[$a['name']], 0, 18) . "...");
                            } else {
                                echo stripslashes($arr[$a['name']]);
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
