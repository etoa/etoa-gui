<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AdvancedGameDataCrudController extends GameDataCrudController
{
    protected function handleRequest(Request $request): Response
    {
        ob_start();
        if ($request->query->get('action') == "new") {
            $this->create();
            $subtitle = 'Datensatz hinzufügen';
        } elseif ($request->request->has('new')) {
            $this->store($request);
            $this->index();
        } elseif ($request->query->get('action') == "copy") {
            $this->copy($request);
            $this->index();
        } elseif ($request->query->get('action') == "edit") {
            $this->edit($request);
            $subtitle = 'Datensatz bearbeiten';
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
            $subtitle = 'Datensatz löschen';
        } elseif ($request->request->has('del')) {
            $this->delete($request);
            $this->index();
        } else {
            $this->index();
            $subtitle = "Übersicht";
        }
        $content = ob_get_clean();

        return $this->render('admin/default.html.twig', [
            'title' => $this->getName(),
            'subtitle' => $subtitle ?? null,
            'content' => $content,
        ]);
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

    public function index(): void
    {
        echo '<form action="?" method="post">';
        echo '<input type="button" value="Neuer Datensatz hinzufügen" name="new" onclick="document.location=\'?&amp;action=new\'" /><br/><br/>';

        $rows = $this->createQueryBuilder()
            ->select('*')
            ->from($this->getTable())
            ->orderBy($this->getOverviewOrderField(), $this->getOverviewOrder())
            ->fetchAllAssociative();

        if (count($rows) > 0) {
            echo '<table class="table"><thead><tr>';
            if ($this->getImagePath() !== null) {
                echo "<th>Bild</a>";
            }
            foreach ($this->getFields() as $k => $field) {
                if ($field['show_overview'] ?? false) {
                    echo "<th>" . $field['text'] . "</a>";
                }
            }
            if (count($this->getSwitches()) > 0) {
                foreach ($this->getSwitches() as $k => $v) {
                    echo "<th>";
                    echo "$k";
                    echo "</th>";
                }
            }
            if ($this->getTableSort() !== null && $this->getTableSortParent() !== null) {
                echo "<th>";
                echo "Sort";
                echo "</th>";
            }

            echo '<th colspan="2">&nbsp;</td></tr></thead><tbody>';
            $cnt = 0;
            $parId = null;
            foreach ($rows as $arr) {
                echo "<tr>";
                if ($this->getImagePath() !== null) {
                    $imagePath = preg_replace('/<DB_TABLE_ID>/', strval($arr[$this->getTableId()]), $this->getImagePath());
                    $path = $this->projectDir . '/assets' . $imagePath;
                    if (is_file($path)) {
                        $imageSize = getimagesize($path);
                        echo '<td style="background:#000;width:' . $imageSize[0] . 'px;">
                            <a href="?action=edit&amp;id=' . $arr[$this->getTableId()] . '">
                            <img src="/build/' . $imagePath . '"/>
                            </a></td>';
                    } else {
                        echo '<td style="background:#000;width:40px;">
                            <a href="action=edit&amp;id=' . $arr[$this->getTableId()] . '">
                            <img src="/build/images/blank.gif" style="width:40px;height:40px;"/>
                            </a></td>';
                    }
                }

                foreach ($this->getFields() as $field) {
                    if ($field['show_overview'] ?? false) {
                        $isLink = $field['link_in_overview'] ?? false;
                        echo '<td>';
                        if ($isLink) {
                            echo '<a href="?action=edit&amp;id=' . $arr[$this->getTableId()] . "\">";
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
                        echo '<td>
                    <a href="?switch=' . $v . "&amp;id=" . $arr[$this->getTableId()] . "\">";
                        if ($arr[$v] == 1) {
                            echo '<img src="/build/images/true.gif" alt="true" />';
                        } else {
                            echo '<img src="/build/false.gif" alt="true" />';
                        }
                        echo "</td>";
                    }
                }

                if ($this->getTableSort() !== null && $this->getTableSortParent() !== null) {
                    echo '<td style="width:40px;">';

                    if ($cnt < count($rows) - 1) {
                        echo '<a href="?moveDown=' . $arr[$this->getTableId()] . "&amp;parentId=" . $arr[$this->getTableSortParent()] . "\"><img src=\"../images/down.gif\" alt=\"down\" /></a> ";
                    } else {
                        echo '<img src="/build/images/blank.gif" alt="blank" style="width:16px;" /> ';
                    }

                    if ($cnt != 0 && $parId == $arr[$this->getTableSortParent()]) {
                        echo '<a href="?moveUp=' . $arr[$this->getTableId()] . "&amp;parentId=" . $arr[$this->getTableSortParent()] . "\"><img src=\"../images/up.gif\" alt=\"up\" /></a> ";
                    } else {
                        echo '<img src="/build/images/blank.gif" alt="blank" style="width:16px;" /> ';
                    }

                    echo "</td>";

                    $parId = $arr[$this->getTableSortParent()];
                }

                echo '<td style="width:70px">
                    <a href="?action=edit&id=' . $arr[$this->getTableId()] . '"><img src="/build/images/admin/icons/edit.png" alt="Bearbeiten" style="width:16px;height:18px;border:none;" title="Bearbeiten" /></a>
                    <a href="?action=copy&id=' . $arr[$this->getTableId()] . '"><img src="/build/images/admin/icons/copy.png" alt="Kopieren" style="width:16px;height:18px;border:none;" title="Kopieren" /></a>
                    <a href="?action=del&id=' . $arr[$this->getTableId()] . '"><img src="/build/images/admin/icons/delete.png" alt="Löschen" style="width:18px;height:15px;border:none;" title="Löschen" /></a>
                </td>
                </tr>';
                $cnt++;
            }
        }
        echo "</tbody></table></form>";
    }

    public function create(): void
    {
        echo "<form action=\"?\" method=\"post\">";
        echo "<table>";
        foreach ($this->getFields() as $field) {
            if ($field['type'] == "readonly") {
                continue;
            }

            $name = $field['name'];
            $value = $field['def_val'] ?? '';
            echo "<tr>
                <th>" . $field['text'] . ":</th>
                <td width=\"200\">" . $this->createInput($field, $name, strval($value)) . "</td>
            </tr>";
        }
        echo "</table><br/>";
        echo "<input type=\"submit\" value=\"Neuen Datensatz speichern\" name=\"new\" />&nbsp;";
        echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?'\" />";
        echo "</form>";
    }

    public function store(Request $request): void
    {
        $this->insertRecord($request);
        $hookResult = $this->runPostInsertUpdateHook();
        $this->addFlash('success', "Neuer Datensatz gespeichert!" . (filled($hookResult) ? ' ' . $hookResult : ''));
    }

    private function insertRecord(Request $request): void
    {
        $values = [];
        $params = [];

        foreach ($this->getFields() as $field) {
            switch ($field['type']) {
                case "readonly":
                    break;
                case "comma_list":
                    $values[$field['name']] = ':' . $field['name'];
                    $params[$field['name']] = implode(",", $request->request->all($field['name']));

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
            ->executeQuery();
    }

    public function copy(Request $request): void
    {
        if ($this->duplicateRecord($this->getTable(), $this->getTableId(), $request->query->getInt('id'))) {
            $this->addFlash('success', "Datensatz kopiert!");
        }
    }

    private function duplicateRecord(string $table, string $id_field, int $id): bool
    {
        $arr = $this->createQueryBuilder()
            ->select('*')
            ->from($table)
            ->where($id_field . ' = :' . $id_field)
            ->setParameter($id_field, $id)
            ->fetchAssociative();

        if ($arr === false) {
            return false;
        }

        unset($arr[$id_field]);

        $values = $params = [];
        foreach ($arr as $key => $value) {
            $values[$key] = ':' . $key;
            $params[$key] = $value;
        }

        $this->createQueryBuilder()
            ->insert($table)
            ->values($values)
            ->setParameters($params)
            ->executeQuery();

        return true;
    }

    public function edit(Request $request): void
    {
        $arr = $this->createQueryBuilder()
            ->select('*')
            ->from($this->getTable())
            ->where($this->getTableId() . " = :id")
            ->setParameter('id', $request->query->get('id'))
            ->fetchAssociative();

        if ($arr !== false) {
            echo "<form action=\"?\" method=\"post\">";
            echo "<input type=\"submit\" value=\"Übernehmen\" name=\"edit\" />&nbsp;";
            echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?'\" /><br/><br/>";
            echo "<input type=\"hidden\" name=\"" . $this->getTableId() . "\" value=\"" . $request->query->get('id') . "\" />";
            echo "<table>";

            $hiddenRows = [];
            foreach ($this->getFields() as $field) {
                if ($field['type'] == "radio") {
                    $value = $arr[$field['name']];
                    foreach ($field['items'] ?? [] as $val) {
                        if (isset($field['show_hide']) && $value == $val) {
                            $hiddenRows = $field['show_hide'];
                        }
                        if (isset($field['hide_show']) && $value != $val) {
                            $hiddenRows = $field['hide_show'];
                        }
                    }
                }
            }

            echo "<tr><td style=\"vertical-align:top;\"><table style=\"width:100%;\">";
            foreach ($this->getFields() as $field) {
                echo "<tr id=\"row_" . $field['name'] . "\"";
                if (in_array($field['name'], $hiddenRows, true)) {
                    echo " style=\"display:none;\"";
                }
                echo ">\n<th>" . $field['text'] . ":</th>\n";
                echo "<td>\n";
                $name = $field['name'];
                $value = $arr[$field['name']];
                echo $this->createInput($field, $name, strval($value));
                echo "</td>\n</tr>\n";
                if ($field['line'] ?? false) {
                    echo "<tr><td style=\"height:4px;background:#000\" colspan=\"2\"></td></tr>";
                }
                if ($field['column_end'] ?? false) {
                    echo "</table></td><td style=\"vertical-align:top;\"><table style=\"width:100%;\">";
                }
            }
            echo "</table></td></tr>";
            echo "</table><br/>";
            echo "<input type=\"submit\" value=\"Übernehmen\" name=\"edit\" />&nbsp;";
            echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?'\" />";
            echo "</form>";
        } else {
            $this->addFlash('error', "Datensatz nicht vorhanden.");
            echo "<input type=\"button\" value=\"Übersicht\" onclick=\"document.location='?'\" />";
        }
    }

    public function update(Request $request): void
    {
        if ($this->updateRecord($request)) {
            $hookResult = $this->runPostInsertUpdateHook();
            $this->addFlash('success', "Datensatz geändert!" . (filled($hookResult) ? ' ' . $hookResult : ''));
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
                case "comma_list":
                    $qb->set($field['name'], ':' . $field['name']);
                    $params[$field['name']] = implode(",", $request->request->all($field['name']));

                    break;
                default:
                    $qb->set($field['name'], ':' . $field['name']);
                    $params[$field['name']] = $request->request->get($field['name']);
            }
        }

        $affected = $qb->setParameters($params)
            ->executeQuery()
            ->rowCount();

        return $affected > 0;
    }

    public function switch(Request $request): void
    {
        $affected = $this->createQueryBuilder()
            ->update($this->getTable())
            ->set($request->query->get('switch'), "(" . $request->query->get('switch') . " + 1) % 2")
            ->where($this->getTableId() . ' = :id')
            ->setParameters([
                'id' => $request->query->get('id'),
            ])
            ->executeQuery()
            ->rowCount();

        if ($affected > 0) {
            $this->addFlash('success', "Aktion ausgeführt!");
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
            ->fetchFirstColumn();

        $cnt = 0;
        $sorter = 0;
        foreach ($ids as $id) {
            $this->createQueryBuilder()
                ->update($this->getTable())
                ->set($this->getTableSort(), (string)$cnt)
                ->where($this->getTableId() . " = :id")
                ->setParameter('id', $id)
                ->executeQuery();

            if ($request->query->get('moveUp') == $id) {
                $sorter = $cnt;
            }
            $cnt++;
        }

        $this->createQueryBuilder()
            ->update($this->getTable())
            ->set($this->getTableSort(), (string)$sorter)
            ->where($this->getTableSortParent() . " = :parentId")
            ->andWhere($this->getTableSort() . " = " . ($sorter - 1))
            ->setParameter('parentId', $request->query->get('parentId'))
            ->executeQuery();

        $this->createQueryBuilder()
            ->update($this->getTable())
            ->set($this->getTableSort(), (string)($sorter - 1))
            ->where($this->getTableId() . " = :sortUp")
            ->setParameter('sortUp', $request->query->get('moveUp'))
            ->executeQuery();
    }

    public function moveDown(Request $request): void
    {
        $ids = $this->createQueryBuilder()
            ->select($this->getTableId())
            ->from($this->getTable())
            ->where($this->getTableSortParent() . " = :parentId")
            ->orderBy($this->getTableSort())
            ->setParameter('parentId', $request->query->get('parentId'))
            ->fetchFirstColumn();

        $cnt = 0;
        $sorter = 0;
        foreach ($ids as $id) {
            $this->createQueryBuilder()
                ->update($this->getTable())
                ->set($this->getTableSort(), (string)$cnt)
                ->where($this->getTableId() . " = :id")
                ->setParameter('id', $id)
                ->executeQuery();

            if ($request->query->get('moveDown') == $id) {
                $sorter = $cnt;
            }
            $cnt++;
        }

        $this->createQueryBuilder()
            ->update($this->getTable())
            ->set($this->getTableSort(), (string)$sorter)
            ->where($this->getTableSortParent() . " = :parentId")
            ->andWhere($this->getTableSort() . " = " . ($sorter + 1))
            ->setParameter('parentId', $request->query->get('parentId'))
            ->executeQuery();

        $this->createQueryBuilder()
            ->update($this->getTable())
            ->set($this->getTableSort(), (string)($sorter + 1))
            ->where($this->getTableId() . " = :sortUp")
            ->setParameter('sortUp', $request->query->get('moveDown'))
            ->executeQuery();
    }

    public function confirmDelete(Request $request): void
    {
        $arr = $this->createQueryBuilder()
            ->select('*')
            ->from($this->getTable())
            ->where($this->getTableId() . " = :id")
            ->setParameter('id', $request->query->get('id'))
            ->fetchAssociative();

        if ($arr !== false) {
            echo "<p>Bitte bestätige das Löschen des folgenden Datensatzes:</p>";
            echo "<form action=\"?\" method=\"post\">";
            echo "<input type=\"hidden\" name=\"" . $this->getTableId() . "\" value=\"" . $request->query->get('id') . "\" />";
            echo "<table>";
            foreach ($this->getFields() as $field) {
                echo "<tr>
                <th>" . $field['text'] . ":</th>
                <td>" . $this->showFieldValue($field, $arr) . "</td>
            </tr>";
            }

            echo "</table><br/>";
            echo "<input type=\"submit\" value=\"Löschen\" name=\"del\" />&nbsp;";
            echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?'\" />";
            echo "</form>";
        } else {
            $this->addFlash('error', "Datensatz nicht vorhanden.");
            echo "<input type=\"button\" value=\"Übersicht\" onclick=\"document.location='?'\" />";
        }
    }

    public function delete(Request $request): void
    {
        $affected = $this->createQueryBuilder()
            ->delete($this->getTable())
            ->where($this->getTableId() . ' = :id')
            ->setParameter('id', $request->request->get($this->getTableId()))
            ->executeQuery()
            ->rowCount();

        if ($affected > 0) {
            $this->addFlash('success', "Datensatz wurde gelöscht!");
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
            ->fetchAllAssociative();

        foreach ($rows as $arr) {
            $r_array[$arr[$text_field]] = $arr[$value_field];
        }

        return $r_array;
    }

}
