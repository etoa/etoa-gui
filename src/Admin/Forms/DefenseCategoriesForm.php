<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class DefenseCategoriesForm extends SimpleForm
{
    protected function getName(): string
    {
        return "Verteidigungs-Kategorien";
    }

    protected function getTable(): string
    {
        return "def_cat";
    }

    protected function getTableId(): string
    {
        return "cat_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "cat_order";
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "cat_name",
                "text" => "Kategoriename",
                "type" => "text",
                "def_val" => "",
                "size" => "30",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_checked" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1,
            ], [
                "name" => "cat_order",
                "text" => "Sortierung",
                "type" => "numeric",
                "def_val" => "1",
                "size" => "1",
                "maxlen" => "2",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_checked" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1,
            ],
        ];
    }
}
