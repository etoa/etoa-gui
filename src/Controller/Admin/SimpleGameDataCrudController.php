<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class SimpleGameDataCrudController extends GameDataCrudController
{
    protected function handleRequest(Request $request): Response
    {
        ob_start();
        $this->index($request);
        $content = ob_get_clean();

        return $this->render('admin/default.html.twig', [
            'title' => $this->getName(),
            'content' => $content,
        ]);
    }

    public function index(Request $request): void
    {
        if ($request->request->has('apply_submit')) {
            $this->applyChanges($request);
            $this->applyDeletions($request);
        }

        if ($request->request->has('new_submit')) {
            $this->createRecord();
        }

        echo '<form action="?" method="post">';
        $rows = $this->createQueryBuilder()
            ->select('*')
            ->from($this->getTable())
            ->orderBy($this->getOverviewOrderField(), $this->getOverviewOrder())
            ->fetchAllAssociative();

        if (count($rows) > 0) {
            echo '<table class="table">';
            echo "<tr>";
            foreach ($this->getFields() as $k => $field) {
                echo "<th>" . $field['text'] . "</th>";
            }
            echo "<th>Löschen</th>";
            echo "</tr>\n";
            foreach ($rows as $arr) {
                echo "<tr>";
                foreach ($this->getFields() as $key => $field) {
                    echo "<td>";
                    $name = $field['name'] . "[" . $arr[$this->getTableId()] . "]";
                    $value = $arr[$field['name']];
                    echo $this->createInput($field, $name, strval($value));
                    echo "</td>";
                }
                echo "<td>
                    <input type=\"checkbox\" name=\"del[" . $arr[$this->getTableId()] . "]\" value=\"1\" />
                </td>";
                echo "</tr>\n";
            }
            echo "</table><br/>\n";
            echo '<input type="submit" name="apply_submit" value="Übernehmen" />&nbsp;';
            echo '<input type="submit" name="new_submit" value="Neuer Datensatz" />&nbsp;';
        } else {
            echo '<p><i>Es existieren keine Datensätze!</i></p>';
            echo '<p><input type="submit" name="new_submit" value="Neuer Datensatz" />&nbsp;</p>';
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
            ->executeQuery();

        $this->addFlash('success', "Neuer leerer Datensatz wurde hinzugefügt!");
    }

    private function applyChanges(Request $request): void
    {
        $affected = 0;
        foreach ($request->request->all() as $key => $val) {
            if ($key != "apply_submit" && $key != "del") {
                foreach ($val as $k => $vl) {
                    $affected += $this->createQueryBuilder()
                        ->update($this->getTable())
                        ->set($key, ':val')
                        ->where($this->getTableId() . " = :id")
                        ->setParameters([
                            'id' => $k,
                            'val' => $vl,
                        ])
                        ->executeQuery()
                        ->rowCount();
                }
            }
        }

        if ($affected > 0) {
            $this->addFlash('success', "Änderungen wurden übernommen!");
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
                        ->executeQuery();
                }
                $deleted = true;
            }
        }

        if ($deleted) {
            $this->addFlash('success', "Bestimmte Daten wurden gelöscht!");
        }
    }
}
