<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pimple\Container;
use Twig\Environment;

abstract class Form
{
    protected Container $app;
    protected Environment $twig;

    final public function __construct(Container $app, Environment $twig)
    {
        $this->app = $app;
        $this->twig = $twig;
    }

    protected function createQueryBuilder(): QueryBuilder
    {
        /** @var Connection $connection */
        $connection = $this->app['db'];

        return $connection->createQueryBuilder();
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
     * type                 Field Type: text, textarea, radio, select, numeric, decimal, color, comma_list
     * def_val              Default Value
     * size                 Field length (text)
     * max_len               Max Text length (text)
     * rows                 Rows (textarea)
     * cols                 Cols (textarea)
     * items (Array)	    Checkbox-/Radio-/Select-Elements (label => value)
     * show_overview        Set true to show on overview page
     *
     * @return array<array{name:string,text:string,type:string,def_val?:string,size?:int,max_len?:int,rows?:int,cols?:int,items?:array,show_overview?:bool,link_in_overview?:bool,show_hide?:array<string>,hide_show?:array<string>,line?:bool,column_end?:bool}>
     */
    abstract protected function getFields(): array;

    /**
     * @param array{name:string,text:string,type:string,def_val?:string,size?:int,max_len?:int,rows?:int,cols?:int,items?:array,show_overview?:bool,link_in_overview?:bool,show_hide?:array<string>,hide_show?:array<string>,line?:bool,column_end?:bool} $field
     * @param array<string> $hidden_rows
     */
    protected function createInput(array $field, string $name, string $value, array &$hidden_rows = []): string
    {
        $stl = isset($field['def_val']) && $value != $field['def_val'] ? ' class="changed"' : '';
        switch ($field['type']) {
            case "readonly":
                return $value;
            case "numeric":
                return "<input
                    $stl
                    type=\"number\"
                    name=\"" . $name . "\"
                    value=\"" . $value . "\"
                />";
            case "decimal":
                return "<input
                    $stl
                    type=\"number\"
                    step=\".01\"
                    name=\"" . $name . "\"
                    value=\"" . $value . "\"
                />";
            case "color":
                return "<input
                    type=\"color\"
                    name=\"" . $name . "\"
                    value=\"" . $value . "\"
                />";
            case "textarea":
                return "<textarea
                    $stl
                    name=\"" . $name . "\"
                    rows=\"" . $field['rows'] . "\"
                    cols=\"" . $field['cols'] . "\"
                    >" . $value . "</textarea>";
            case "select":
                $str = "<select name=\"" . $name . "\">";
                if ($value == 0 || $value == "") {
                    $str .= "<option selected=\"selected\">(Wählen...)</option>";
                }
                foreach ($field['items'] ?? [] as $label => $val) {
                    $str .= "<option value=\"$val\"";
                    if ($value == $val) {
                        $str .= " selected=\"selected\"";
                    }
                    $str .= ">$label</option>";
                }
                $str .= "</select>";

                return $str;
            case "radio":
                $str = '';
                foreach ($field['items'] ?? [] as $label => $val) {
                    $str .= "<label><input name=\"" . $field['name'] . "\" type=\"radio\" value=\"$val\"";
                    if ($value == $val) {
                        $str .= " checked=\"checked\"";
                    }

                    $onclick_actions = array();

                    // Zeige andere Elemente wenn Einstellung aktiv
                    if (isset($field['show_hide'])) {
                        foreach ($field['show_hide'] as $sh) {
                            $onclick_actions[] = "document.getElementById('row_" . $sh . "').style.display='" . ($val == 1 ? "" : "none") . "';";
                        }
                    }

                    // Verstecke andere Elemente wenn Einstellung aktiv
                    if (isset($field['hide_show'])) {
                        foreach ($field['hide_show'] as $sh) {
                            $onclick_actions[] = "document.getElementById('row_" . $sh . "').style.display='" . ($val == 1 ? "none" : "") . "';";
                        }
                    }

                    if (count($onclick_actions) > 0) {
                        $str .= " onclick=\"" . implode("", $onclick_actions) . "\"";
                    }

                    $str .= " /> $label</label><br>";

                    if (isset($field['show_hide']) && $value == $val) {
                        $hidden_rows = $field['show_hide'];
                    }
                    if (isset($field['hide_show']) && $value != $val) {
                        $hidden_rows = $field['hide_show'];
                    }
                }

                return $str;
            case "comma_list":
                $keys = explode(",", $value);
                $str = '';
                foreach ($field['items'] ?? [] as $label => $val) {
                    $str .= "<label><input name=\"" . $field['name'] . "[]\" type=\"checkbox\" value=\"" . $val . "\"";
                    if (in_array($val, $keys, true)) {
                        $str .= " checked=\"checked\"";
                    }
                    $str .= " /> " . $label . "</label><br/>";
                }

                return $str;
            default:
                return "<input
                    $stl
                    type=\"text\"
                    name=\"" . $name . "\"
                    value=\"" . $value . "\"
                    size=\"" . $field['size'] . "\"
                    maxlength=\"" . $field['max_len'] . "\"
                />";
        }
    }

    /**
     * @param array{name:string,text:string,type:string,def_val?:string,size?:int,max_len?:int,rows?:int,cols?:int,items?:array,show_overview?:bool,link_in_overview?:bool,show_hide?:array<string>,hide_show?:array<string>,line?:bool,column_end?:bool} $field
     * @param array<string,string> $arr
     */
    protected function showFieldValue(array $field, array $arr): string
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
                foreach ($field['items'] ?? [] as $rk => $rv) {
                    if ($arr[$field['name']] == $rv) {
                        return $rk;
                    }
                }

                return '-';
            case "select":
                foreach ($field['items'] ?? [] as $label => $val) {
                    if ($arr[$field['name']] == $val) {
                        return $label;
                    }
                }

                return '-';
            case "comma_list":
                $keys = explode(",", $arr[$field['name']]);
                $labels = [];
                foreach ($field['items'] ?? [] as $label => $val) {
                    if (in_array($val, $keys, true)) {
                        $labels[] = $label;
                    }
                }

                return implode(', ', $labels);
            default:
                return $arr[$field['name']];
        }
    }
}
