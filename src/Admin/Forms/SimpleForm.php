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
            $this->applyChanges($request);
            $this->applyDeletions($request);
        }

        if ($request->request->has('new_submit')) {
            $this->createRecord();
        }

        echo "<form action=\"?" . URL_SEARCH_STRING . "\" method=\"post\">";
        $rows = $this->createQueryBuilder()
            ->select('*')
            ->from($this->getTable())
            ->orderBy($this->getOverviewOrderField(), $this->getOverviewOrder())
            ->execute()
            ->fetchAllAssociative();

        if (count($rows) > 0) {
            echo "<table>\n";
            echo "<tr>";
            foreach ($this->getFields() as $k => $field) {
                echo "<th class=\"tbltitle\">" . $field['text'] . "</th>";
            }
            echo "<th class=\"tbltitle\">Löschen</th>";
            echo "</tr>\n";
            foreach ($rows as $arr) {
                echo "<tr>";
                foreach ($this->getFields() as $key => $field) {
                    echo "<td class=\"tbldata\">";
                    $name = $field['name'] . "[" . $arr[$this->getTableId()] . "]";
                    $value = $arr[$field['name']];
                    echo $this->createInput($field, $name, $value);
                    echo "</td>";
                }
                echo "<td class=\"tbldata\">
                    <input type=\"checkbox\" name=\"del[" . $arr[$this->getTableId()] . "]\" value=\"1\" />
                </td>";
                echo "</tr>\n";
            }
            echo "</table><br/>\n";
            echo "<input type=\"submit\" name=\"apply_submit\" value=\"Übernehmen\" />&nbsp;";
            echo "<input type=\"submit\" name=\"new_submit\" value=\"Neuer Datensatz\" />&nbsp;";
        } else {
            echo "<p align=\"center\"><i>Es existieren keine Datensätze!</i></p>";
            echo "<p align=\"center\"><input type=\"submit\" name=\"new_submit\" value=\"Neuer Datensatz\" />&nbsp;</p>";
        }
        echo "</form>";
    }

    private function createRecord(): void
    {
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

    private function applyChanges(Request $request): void
    {
        $affected = 0;
        foreach ($request->request->all() as $key => $val) {
            if ($key != "apply_submit" && $key != "del") {
                foreach ($val as $k => $vl) {
                    $affected += (int) $this->createQueryBuilder()
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

        if ($affected > 0) {
            echo MessageBox::ok("", "Änderungen wurden übernommen!");
        }
    }

    private function applyDeletions(Request $request): void
    {
        $deleted = false;
        foreach ($request->request->all() as $key => $val) {
            if ($key == "del") {
                foreach (array_keys($val) as $id) {
                    $this->createQueryBuilder()
                        ->delete($this->getTable())
                        ->where($this->getTableId() . " = :id")
                        ->setParameter('id', $id)
                        ->execute();
                }
                $deleted = true;
            }
        }

        if ($deleted) {
            echo MessageBox::ok("", "Bestimmte Daten wurden gelöscht!");
        }
    }
}
