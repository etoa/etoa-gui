<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

use EtoA\Core\Configuration\ConfigurationService;
use FleetAction;
use MessageBox;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

abstract class AdvancedForm
{
    protected Container $app;
    private Environment $twig;

    public function __construct(Container $app, Environment $twig)
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

    protected function getSwitches(): array
    {
        return [];
    }

    protected function runPostInsertUpdateHook(): string
    {
        return '';
    }

    /**
     * Parameters:
     *
     * name	                DB Field Name
     * text	                Field Description
     * type                 Field Type: text, password, textarea, timestamp, radio, select, checkbox, email, url, numeric
     * def_val              Default Value
     * size                 Field length (text, password, date, email, url)
     * maxlen               Max Text length (text, password, date, email, url)
     * rows                 Rows (textarea)
     * cols                 Cols (textarea)
     * rcb_elem (Array)	    Checkbox-/Radio Elements (desc=>value)
     * rcb_elem_chekced	    Value of default checked Checkbox-/Radio Element (Checkbox: has to be an array)
     * select_elem (Array)  Select Elements (desc=>value)
     * select_elem_checked  Value of default checked Select Element (desc=>value)
     * show_overview        Set 1 to show on overview page
     */
    abstract protected function getFields(): array;

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
            $this->index($request);
        } elseif ($request->query->get('action') == "copy") {
            $this->copy($request);
            $this->index($request);
        } elseif ($request->query->get('action') == "edit") {
            $this->edit($request);
        } elseif ($request->request->has('edit')) {
            $this->update($request);
            $this->index($request);
        } elseif ($request->query->get('action') == "del") {
            $this->confirmDelete($request);
        } elseif ($request->request->has('del')) {
            $this->delete($request);
            $this->index($request);
        } else {
            $this->index($request);
        }
    }

    public function index(Request $request): void
    {
        $this->twig->addGlobal("title", $this->getName());
        $this->twig->addGlobal("subtitle", "Übersicht");

        echo "<p>Um einen Datensatz hinzuzufügen, zu ändern oder zu löschen klicke  bitte auf die entsprechenden Links oder Buttons!</p>";

        if ($request->query->has('sortup') && $request->query->has('parentid')) {
            $res = dbquery("SELECT " . $this->getTableId() . " FROM " . $this->getTable() . " WHERE " . $this->getTableSortParent() . "=" . $request->query->get('parentid') . " ORDER BY " . $this->getTableSort() . "");
            $cnt = 0;
            $sorter = 0;
            while ($arr = mysql_fetch_array($res)) {
                dbquery("UPDATE " . $this->getTable() . " SET " . $this->getTableSort() . "=" . $cnt . " WHERE " . $this->getTableId() . "=" . $arr[$this->getTableId()] . "");
                if ($request->query->get('sortup') == $arr[$this->getTableId()]) {
                    $sorter = $cnt;
                }
                $cnt++;
            }
            dbquery("UPDATE " . $this->getTable() . " SET " . $this->getTableSort() . "=" . ($sorter) . " WHERE " . $this->getTableSortParent() . "=" . $request->query->get('parentid') . " AND " . $this->getTableSort() . "=" . ($sorter - 1) . "");
            dbquery("UPDATE " . $this->getTable() . " SET " . $this->getTableSort() . "=" . ($sorter - 1) . " WHERE " . $this->getTableId() . "=" . $request->query->get('sortup') . "");
        }

        if ($request->query->has('sortdown') && $request->query->has('parentid')) {
            $res = dbquery("SELECT " . $this->getTableId() . " FROM " . $this->getTable() . " WHERE " . $this->getTableSortParent() . "=" . $request->query->get('parentid') . " ORDER BY " . $this->getTableSort() . ";");
            $cnt = 0;
            $sorter = 0;
            while ($arr = mysql_fetch_array($res)) {
                dbquery("UPDATE " . $this->getTable() . " SET " . $this->getTableSort() . "=" . $cnt . " WHERE " . $this->getTableId() . "=" . $arr[$this->getTableId()] . "");
                if ($request->query->get('sortdown') == $arr[$this->getTableId()]) {
                    $sorter = $cnt;
                }
                $cnt++;
            }
            dbquery("UPDATE " . $this->getTable() . " SET " . $this->getTableSort() . "=" . ($sorter) . " WHERE " . $this->getTableSortParent() . "=" . $request->query->get('parentid') . " AND " . $this->getTableSort() . "=" . ($sorter + 1) . "");
            dbquery("UPDATE " . $this->getTable() . " SET " . $this->getTableSort() . "=" . ($sorter + 1) . " WHERE " . $this->getTableId() . "=" . $request->query->get('sortdown') . "");
        }


        // Switcher
        if (count($this->getSwitches()) > 0 && $request->query->has('switch') && $request->query->getInt('id') > 0) {
            dbquery("UPDATE " . $this->getTable() . " SET `" . $request->query->get('switch') . "`=(`" . $request->query->get('switch') . "`+1)%2 WHERE `" . $this->getTableId() . "`=" . $request->query->get('id') . "");
            success_msg("Aktion ausgeführt!");
        }

        // Show overview
        echo "<form action=\"?" . URL_SEARCH_STRING . "\" method=\"post\">";
        echo "<input type=\"button\" value=\"Neuer Datensatz hinzufügen\" name=\"new\" onclick=\"document.location='?" . URL_SEARCH_STRING . "&amp;action=new'\" /><br/><br/>";

        $sql = "SELECT * FROM " . $this->getTable() . " ORDER BY " . $this->getOverviewOrderField() . " " . $this->getOverviewOrder() . ";";
        if ($res = dbquery($sql)) {
            echo "<table width=\"100%\" cellpadding=\"3\" cellspacing=\"1\" align=\"center\"><tr>";
            if ($this->getImagePath() !== null) {
                echo "<th valign=\"top\" class=\"tbltitle\">Bild</a>";
            }
            foreach ($this->getFields() as $k => $a) {
                if ($a['show_overview'] == 1) {
                    echo "<th valign=\"top\" class=\"tbltitle\">" . $a['text'] . "</a>";
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
            while ($arr = mysql_fetch_array($res)) {
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

                $this->showOverview($arr);

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

                    if ($cnt < mysql_num_rows($res) - 1) {
                        echo "<a href=\"?" . URL_SEARCH_STRING . "&amp;sortdown=" . $arr[$this->getTableId()] . "&amp;parentid=" . $arr[$this->getTableSortParent()] . "\"><img src=\"../images/down.gif\" alt=\"down\" /></a> ";
                    } else {
                        echo "<img src=\"../images/blank.gif\" alt=\"blank\" style=\"width:16px;\" /> ";
                    }

                    if ($cnt != 0 && $parId == $arr[$this->getTableSortParent()]) {
                        echo "<a href=\"?" . URL_SEARCH_STRING . "&amp;sortup=" . $arr[$this->getTableId()] . "&amp;parentid=" . $arr[$this->getTableSortParent()] . "\"><img src=\"../images/up.gif\" alt=\"up\" /></a> ";
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

    private function showOverview(array $arr): void
    {
        /** @var ConfigurationService $config */
        $config = $this->app[ConfigurationService::class];

        foreach ($this->getFields() as $k => $a) {
            if ($a['show_overview'] == 1) {
                echo "<td class=\"tbldata\">";
                if (isset($a['link_in_overview']) && $a['link_in_overview'] == 1) {
                    echo "<a href=\"?" . URL_SEARCH_STRING . "&amp;action=edit&amp;id=" . $arr[$this->getTableId()] . "\">";
                }

                switch ($a['type']) {
                    case "readonly":
                        echo "" . $arr[$a['name']] . "";

                        break;
                    case "text":
                        echo "" . $arr[$a['name']] . "";

                        break;
                    case "email":
                        echo "" . $arr[$a['name']] . "";

                        break;
                    case "url":
                        echo "" . $arr[$a['name']] . "";

                        break;
                    case "numeric":
                        echo "" . $arr[$a['name']] . "";

                        break;
                    case "password":
                        echo "" . $arr[$a['name']] . "";

                        break;
                    case "timestamp":
                        echo "" . date($config->get('admin_dateformat'), $arr[$a['name']]) . "";

                        break;
                    case "textarea":
                        echo "";
                        echo stripslashes($arr[$a['name']]);
                        echo "";

                        break;
                    case "radio":
                        echo "";
                        foreach ($a['rcb_elem'] as $rk => $rv) {
                            if ($arr[$a['name']] == $rv) {
                                echo $rk;
                            }
                        }
                        echo "";

                        break;
                    case "checkbox":
                        echo "";
                        $cb_temp_arr = array();
                        foreach ($a['rcb_elem'] as $rk => $rv) {
                            if (in_array($rv, explode(";", $arr[$a['name']]), true)) {
                                array_push($cb_temp_arr, $rk);
                            }
                        }
                        for ($cbx = 0; $cbx < count($cb_temp_arr); $cbx++) {
                            echo $cb_temp_arr[$cbx];
                            if ($cbx = count($cb_temp_arr) - 1) {
                                echo ";";
                            }
                        }
                        echo "";

                        break;
                    case "select":
                        echo "";
                        foreach ($a['select_elem'] as $sd => $sv) {
                            if ($sv == $arr[$a['name']]) {
                                echo $sd;
                            }
                        }
                        echo "";

                        break;
                    default:
                        echo "" . $arr[$a['name']] . "";

                        break;
                }
                echo "</td>";
            }
        }
    }

    public function create(): void
    {
        $this->twig->addGlobal("title", $this->getName());
        $this->twig->addGlobal("subtitle", "Neuer Datensatz");

        echo "<p>Gib die Daten des neuen Datensatzes in die untenstehende Maske ein:</p>";
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
        foreach ($this->getFields() as $k => $a) {
            switch ($a['type']) {
                case "readonly":
                    break;
                case "text":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\"><input type=\"text\" name=\"" . $a['name'] . "\" size=\"" . $a['size'] . "\" maxlength=\"" . $a['maxlen'] . "\" value=\"" . $a['def_val'] . "\" /></td></tr>";

                    break;
                case "hidden":
                    echo "<input type=\"hidden\" name=\"" . $a['name'] . "\" value=\"" . $a['def_val'] . "\" />";

                    break;
                case "email":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\"><input type=\"text\" name=\"" . $a['name'] . "\" size=\"" . $a['size'] . "\" maxlength=\"" . $a['maxlen'] . "\" value=\"" . $a['def_val'] . "\" /></td></tr>";

                    break;
                case "url":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\"><input type=\"text\" name=\"" . $a['name'] . "\" size=\"" . $a['size'] . "\" maxlength=\"" . $a['maxlen'] . "\" value=\"" . $a['def_val'] . "\" /></td></tr>";

                    break;
                case "numeric":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\"><input type=\"text\" name=\"" . $a['name'] . "\" size=\"" . $a['size'] . "\" maxlength=\"" . $a['maxlen'] . "\" value=\"" . $a['def_val'] . "\" /></td></tr>";

                    break;
                case "password":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\"><input type=\"password\" name=\"" . $a['name'] . "\" size=\"" . $a['size'] . "\" maxlength=\"" . $a['maxlen'] . "\" value=\"" . $a['def_val'] . "\" /></td></tr>";

                    break;
                case "timestamp":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\"><input type=\"text\" name=\"" . $a['name'] . "\" size=\"" . $a['size'] . "\" maxlength=\"" . $a['maxlen'] . "\" value=\"" . $a['def_val'] . "\" /></td></tr>";

                    break;
                case "textarea":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\"><textarea name=\"" . $a['name'] . "\" rows=\"" . $a['rows'] . "\" cols=\"" . $a['cols'] . "\">" . $a['def_val'] . "</textarea></td></tr>";

                    break;
                case "radio":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">";
                    foreach ($a['rcb_elem'] as $rk => $rv) {
                        echo $rk . ": <input name=\"" . $a['name'] . "\" type=\"radio\" value=\"$rv\"";
                        if ($a['rcb_elem_chekced'] == $rv) {
                            echo " checked=\"checked\"";
                        }
                        echo " /> ";
                    }
                    echo "</td></tr>";

                    break;
                case "checkbox":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">";
                    foreach ($a['rcb_elem'] as $rk => $rv) {
                        echo $rk . ": <input name=\"" . $a['name'] . "\" type=\"checkbox\" value=\"$rv\"";
                        if (in_array($rv, $a['rcb_elem_chekced'])) {
                            echo " checked=\"checked\"";
                        }
                        echo " /> ";
                    }
                    echo "</td></tr>";

                    break;
                case "select":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\"><select name=\"" . $a['name'] . "\">";
                    foreach ($a['select_elem'] as $rk => $rv) {
                        echo "<option value=\"$rv\"";
                        if (isset($a['select_elem_checked']) && $a['select_elem_checked'] == $rv) {
                            echo " selected=\"selected\"";
                        }
                        echo ">$rk</option> ";
                    }
                    echo "</td></tr>";

                    break;
                case "dbimage":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\"><input type=\"file\" name=\"" . $a['name'] . "\" size=\"" . $a['size'] . "\" maxlength=\"" . $a['maxlen'] . "\" /></td></tr>";

                    break;
                case "fleetaction":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">";
                    $actions = FleetAction::getAll();
                    foreach ($actions as $ac) {
                        echo "<input name=\"" . $a['name'] . "[]\" type=\"checkbox\" value=\"" . $ac->code() . "\"";
                        echo " /> " . $ac . "<br/>";
                    }
                    echo "</td></tr>";

                    break;
                default:
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\"><input type=\"text\" name=\"" . $a['name'] . "\" size=\"" . $a['size'] . "\" maxlength=\"" . $a['maxlen'] . "\" value=\"" . $a['def_val'] . "\" /></td></tr>";

                    break;
            }
        }
    }

    public function store(Request $request): void
    {
        dbquery($this->createNewDatasetQuery($request));
        $hookResult = $this->runPostInsertUpdateHook();
        echo MessageBox::ok("", "Neuer Datensatz gespeichert!" . (filled($hookResult) ? ' ' . $hookResult : ''));
    }

    private function createNewDatasetQuery(Request $request): string
    {
        global $_FILES;
        $type = "";
        $form_data = "";

        $cnt = 1;
        $fsql = "";
        $vsql = "";
        $vsqlsp = "";
        foreach ($this->getFields() as $k => $a) {
            if ($a['type'] != "readonly") {
                $fsql .= "`" . $a['name'] . "`";
                if ($cnt < sizeof($this->getFields())) {
                    $fsql .= ",";
                }
            }
            $cnt++;
        }
        $cnt = 1;
        foreach ($this->getFields() as $k => $a) {
            switch ($a['type']) {
                case "readonly":
                    break;
                case "text":
                    $vsql .= "'" . addslashes($request->request->get($a['name'])) . "'";

                    break;
                case "email":
                    $vsql .= "'" . $request->request->get($a['name']) . "'";

                    break;
                case "url":
                    $vsql .= "'" . $request->request->get($a['name']) . "'";

                    break;
                case "numeric":
                    $vsql .= "'" . $request->request->get($a['name']) . "'";

                    break;
                case "password":
                    $vsql .= "'" . md5($request->request->get($a['name'])) . "'";

                    break;
                case "timestamp":
                    $vsql .= "UNIX_TIMESTAMP('" . $request->request->get($a['name']) . "')";

                    break;
                case "textarea":
                    $vsql .= "'" . addslashes($request->request->get($a['name'])) . "'";

                    break;
                case "radio":
                    $vsql .= "'" . $request->request->get($a['name']) . "'";

                    break;
                case "checkbox":
                    $vsql .= "'" . $request->request->get($a['name']) . "'";

                    break;
                case "select":
                    $vsql .= "'" . $request->request->get($a['name']) . "'";

                    break;
                case "dbimage":

                    if ($_FILES[$a['name']]['name'] != "") {
                        $image_type = $_FILES[$a['name']]['type'];
                        if (stristr($type, "image/")) {
                            $iminfo = getimagesize($_FILES[$a['name']]['tmp_name']);
                            $imdata = addslashes(fread(fopen($form_data, "r"), filesize($form_data)));

                            $image = imagecreatefromjpeg($_FILES[$a['name']]['tmp_name']);
                            $image1 = imagecreate(150, 150 * $iminfo['1'] / $iminfo['0']);
                            $farbe_body = imagecolorallocate($image1, 51, 51, 51);
                            imagecopyresized($image1, $image, 0, 0, 0, 0, 150, 150 * $iminfo['1'] / $iminfo['0'], $iminfo['0'], $iminfo['1']);
                            imagejpeg($image1, $_FILES[$a['name']]['tmp_name']);
                            $imtdata = addslashes(fread(fopen($_FILES[$a['name']]['tmp_name'], "r"), filesize($form_data)));
                        } else {
                            die("Sorry, this file is not an image!<br/><br/><a href=\"?\">Back</a>");
                        }
                    } else {
                        die("Sorry, you haven't choosen a file!<br/><br/><a href=\"?\">Back</a>");
                    }


                    $fsql .= ",`" . $a['db_image_thumb_field'] . "`,`" . $a['db_image_type_field'] . "`";
                    $vsqlsp .= ",'" . $imtdata . "','" . $_FILES[$a['name']]['type'] . "'";
                    $vsql .= "'" . $imdata . "'";

                    break;
                case "fleetaction":
                    if (is_array($request->request->get($a['name']))) {
                        $str = implode(",", $request->request->get($a['name']));
                    } else {
                        $str = "";
                    }
                    $vsql .= "'" . $str . "'";

                    break;
                default:
                    $vsql .= "'" . addslashes($request->request->get($a['name'])) . "'";

                    break;
            }
            if ($cnt < sizeof($this->getFields()) && $a['type'] != "readonly") {
                $vsql .= ",";
            }
            $cnt++;
        }

        $sql = "INSERT INTO " . $this->getTable() . " (";
        $sql .= $fsql;
        $sql .= ") VALUES(";
        $sql .= $vsql . $vsqlsp;
        $sql .= ");";

        return $sql;
    }

    public function copy(Request $request): void
    {
        DuplicateMySQLRecord($this->getTable(), $this->getTableId(), $request->query->get('id'));
        success_msg("Datensatz kopiert!");
    }

    public function edit(Request $request): void
    {
        $this->twig->addGlobal("title", $this->getName());
        $this->twig->addGlobal("subtitle", "Datensatz bearbeiten");

        $res = mysql_query("SELECT * FROM " . $this->getTable() . " WHERE " . $this->getTableId() . "='" . $request->query->get('id') . "';");
        $arr = mysql_fetch_array($res);
        echo "<p>Ändere die Daten des Datensatzes und klicke auf 'Übernehmen', um die Daten zu speichern:</p>";
        echo "<form action=\"?" . URL_SEARCH_STRING . "\" method=\"post\">";
        echo "<input type=\"submit\" value=\"Übernehmen\" name=\"edit\" />&nbsp;";
        echo "<input type=\"button\" value=\"Abbrechen\" name=\"editcancel\" onclick=\"document.location='?" . URL_SEARCH_STRING . "'\" /><br/><br/>";

        echo "<input type=\"hidden\" name=\"" . $this->getTableId() . "\" value=\"" . $request->query->get('id') . "\" />";
        echo "<table>";
        $this->editDataset($arr);
        echo "</table><br/>";
        echo "<input type=\"submit\" value=\"Übernehmen\" name=\"edit\" />&nbsp;";
        echo "<input type=\"button\" value=\"Abbrechen\" name=\"editcancel\" onclick=\"document.location='?" . URL_SEARCH_STRING . "'\" />";
        echo "</form>";
    }

    private function editDataset(array $arr): void
    {
        /** @var ConfigurationService $config */
        $config = $this->app[ConfigurationService::class];

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
                    echo "<input $stl type=\"text\" name=\"" . $fieldDefinition['name'] . "\" size=\"" . $fieldDefinition['size'] . "\" maxlength=\"" . $fieldDefinition['maxlen'] . "\" value=\"" . stripslashes($arr[$fieldDefinition['name']]) . "\" />";

                    break;
                case "hidden":
                    echo "<input type=\"hidden\" name=\"" . $fieldDefinition['name'] . "\" value=\"" . $arr[$fieldDefinition['name']] . "\" />";

                    break;
                case "email":
                    echo "<input $stl type=\"text\" name=\"" . $fieldDefinition['name'] . "\" size=\"" . $fieldDefinition['size'] . "\" maxlength=\"" . $fieldDefinition['maxlen'] . "\" value=\"" . $arr[$fieldDefinition['name']] . "\" />";

                    break;
                case "url":
                    echo "<input $stl type=\"text\" name=\"" . $fieldDefinition['name'] . "\" size=\"" . $fieldDefinition['size'] . "\" maxlength=\"" . $fieldDefinition['maxlen'] . "\" value=\"" . $arr[$fieldDefinition['name']] . "\" />";

                    break;
                case "numeric":
                    echo "<input $stl type=\"text\" name=\"" . $fieldDefinition['name'] . "\" size=\"" . $fieldDefinition['size'] . "\" maxlength=\"" . $fieldDefinition['maxlen'] . "\" value=\"" . $arr[$fieldDefinition['name']] . "\" />";

                    break;
                case "password":
                    echo "<input $stl type=\"password\" name=\"" . $fieldDefinition['name'] . "\" size=\"" . $fieldDefinition['size'] . "\" maxlength=\"" . $fieldDefinition['maxlen'] . "\" value=\"\" />";

                    break;
                case "timestamp":
                    echo "<input $stl type=\"text\" name=\"" . $fieldDefinition['name'] . "\" size=\"" . $fieldDefinition['size'] . "\" maxlength=\"" . $fieldDefinition['maxlen'] . "\" value=\"" . date($config->get('admin_dateformat'), $arr[$fieldDefinition['name']]) . "\" />";

                    break;
                case "textarea":
                    echo "<textarea $stl name=\"" . $fieldDefinition['name'] . "\" rows=\"" . $fieldDefinition['rows'] . "\" cols=\"" . $fieldDefinition['cols'] . "\">" . stripslashes($arr[$fieldDefinition['name']]) . "</textarea>";

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
                case "checkbox":
                    foreach ($fieldDefinition['rcb_elem'] as $rk => $rv) {
                        echo $rk . ": <input name=\"" . $fieldDefinition['name'] . "\" type=\"checkbox\" value=\"$rv\"";
                        if (in_array($rv, explode(";", $arr[$fieldDefinition['name']]), true)) {
                            echo " checked=\"checked\"";
                        }
                        echo " /> ";
                    }
                    echo "";

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
                        echo "<input name=\"" . $fieldDefinition['name'] . "[]\" type=\"checkbox\" value=\"" . $ac->code() . "\"";
                        if (in_array($ac->code(), $keys, true)) {
                            echo " checked=\"checked\"";
                        }
                        echo " /> " . $ac . "<br/>";
                    }
                    echo "";

                    break;
                default:
                    echo "<input type=\"text\" name=\"" . $fieldDefinition['name'] . "\" size=\"" . $fieldDefinition['size'] . "\" maxlength=\"" . $fieldDefinition['maxlen'] . "\" value=\"" . stripslashes($arr[$fieldDefinition['name']]) . "\" />";
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
        dbquery($this->editDatasetQuery($request));
        $hookResult = $this->runPostInsertUpdateHook();
        echo MessageBox::ok("", "Datensatz geändert!" . (filled($hookResult) ? ' ' . $hookResult : ''));
    }

    private function editDatasetQuery(Request $request): string
    {
        $sql = "UPDATE " . $this->getTable() . " SET ";
        $cnt = 1;
        foreach ($this->getFields() as $k => $a) {
            $cntadd = 1;
            switch ($a['type']) {
                case "readonly":
                    //Case readonly: do *nothing* with the field!
                    //but instead do *not* add a comma
                    $cntadd = 0;

                    break;
                case "text":
                    $sql .= "`" . $a['name'] . "` = '" . addslashes($request->request->get($a['name'])) . "'";

                    break;
                case "email":
                    $sql .= "`" . $a['name'] . "` = '" . $request->request->get($a['name']) . "'";

                    break;
                case "url":
                    $sql .= "`" . $a['name'] . "` = '" . $request->request->get($a['name']) . "'";

                    break;
                case "numeric":
                    $sql .= "`" . $a['name'] . "` = '" . $request->request->get($a['name']) . "'";

                    break;
                case "password":
                    if ($request->request->get($a['name']) != "") {
                        $sql .= "`" . $a['name'] . "` = '" . md5($request->request->get($a['name'])) . "'";
                    } else {
                        $cntadd = 0;
                    }

                    break;
                case "timestamp":
                    $sql .= "`" . $a['name'] . "` = UNIX_TIMESTAMP('" . $request->request->get($a['name']) . "')";

                    break;
                case "textarea":
                    $sql .= "`" . $a['name'] . "` = '" . addslashes($request->request->get($a['name'])) . "'";

                    break;
                case "radio":
                    $sql .= "`" . $a['name'] . "` = '" . $request->request->get($a['name']) . "'";

                    break;
                case "checkbox":
                    $sql .= "`" . $a['name'] . "` = '" . $request->request->get($a['name']) . "'";

                    break;
                case "select":
                    $sql .= "`" . $a['name'] . "` = '" . $request->request->get($a['name']) . "'";

                    break;
                case "fleetaction":
                    if (is_array($request->request->get($a['name']))) {
                        $str = implode(",", $request->request->get($a['name']));
                    } else {
                        $str = "";
                    }
                    $sql .= "`" . $a['name'] . "` = '" . $str . "'";

                    break;
                default:
                    $sql .= "`" . $a['name'] . "` = '" . addslashes($request->request->get($a['name'])) . "'";

                    break;
            }
            if ($cntadd == 1) {
                if ($cnt < sizeof($this->getFields())) {
                    $sql .= ",";
                }
            }
            $cnt++;
        }
        $sql .= " WHERE " . $this->getTableId() . "='" . $request->request->get($this->getTableId()) . "';";

        return $sql;
    }

    public function confirmDelete(Request $request): void
    {
        $this->twig->addGlobal("title", $this->getName());
        $this->twig->addGlobal("subtitle", "Datensatz löschen");

        $res = mysql_query("SELECT * FROM " . $this->getTable() . " WHERE " . $this->getTableId() . "='" . $request->query->get('id') . "';");
        $arr = mysql_fetch_array($res);
        echo "<p>Bitte bestätige das Löschen des folgenden Datensatzes:</p>";
        echo "<form action=\"?" . URL_SEARCH_STRING . "\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"" . $this->getTableId() . "\" value=\"" . $request->query->get('id') . "\" />";
        echo "<table>";
        $this->deleteDataset($arr);
        echo "</table><br/>";
        echo "<input type=\"submit\" value=\"Löschen\" name=\"del\" />&nbsp;";
        echo "<input type=\"button\" value=\"Abbrechen\" name=\"delcancel\" onclick=\"document.location='?" . URL_SEARCH_STRING . "'\" />";
        echo "</form>";
    }

    private function deleteDataset(array $arr): void
    {
        /** @var ConfigurationService $config */
        $config = $this->app[ConfigurationService::class];

        foreach ($this->getFields() as $k => $a) {
            switch ($a['type']) {
                case "text":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">" . $arr[$a['name']] . "</td></tr>";

                    break;
                case "email":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">" . $arr[$a['name']] . "</td></tr>";

                    break;
                case "url":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">" . $arr[$a['name']] . "</td></tr>";

                    break;
                case "numeric":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">" . $arr[$a['name']] . "</td></tr>";

                    break;
                case "password":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">" . $arr[$a['name']] . "</td></tr>";

                    break;
                case "timestamp":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">" . date($config->get('admin_dateformat'), $arr[$a['name']]) . "</td></tr>";

                    break;
                case "textarea":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">" . stripslashes(nl2br($arr[$a['name']])) . "</td></tr>";

                    break;
                case "radio":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">";
                    foreach ($a['rcb_elem'] as $rk => $rv) {
                        if ($arr[$a['name']] == $rv) {
                            echo $rk;
                        }
                    }
                    echo "</td></tr>";

                    break;
                case "checkbox":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">";
                    $cb_temp_arr = array();
                    foreach ($a['rcb_elem'] as $rk => $rv) {
                        if (in_array($rv, explode(";", $arr[$a['name']]), true)) {
                            array_push($cb_temp_arr, $rk);
                        }
                    }
                    for ($cbx = 0; $cbx < count($cb_temp_arr); $cbx++) {
                        echo $cb_temp_arr[$cbx];
                        if ($cbx = count($cb_temp_arr) - 1) {
                            echo "<br/>";
                        }
                    }
                    echo "</td></tr>";

                    break;
                case "select":
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">" . $arr[$a['name']] . "</td></tr>";

                    break;
                default:
                    echo "<tr><th class=\"tbltitle\" width=\"200\">" . $a['text'] . ":</th>";
                    echo "<td class=\"tbldata\" width=\"200\">" . $arr[$a['name']] . "</td></tr>";

                    break;
            }
        }
    }

    public function delete(Request $request): void
    {
        if (dbquery("DELETE FROM " . $this->getTable() . " WHERE " . $this->getTableId() . "='" . $request->request->get($this->getTableId()) . "';")) {
            echo MessageBox::ok("", "Datensatz wurde gelöscht!");
        }
    }

    protected function getSelectElements($table, $value_field, $text_field, $order, $additional_values = null): array
    {
        $r_array = array();
        if ($additional_values && count($additional_values) > 0) {
            foreach ($additional_values as $val => $key) {
                $r_array[$key] = $val;
            }
        }
        $res = dbquery("SELECT `$value_field`,`$text_field` FROM $table ORDER BY $order;");
        while ($arr = mysql_fetch_array($res)) {
            $r_array[$arr[$text_field]] = $arr[$value_field];
        }

        return $r_array;
    }
}
