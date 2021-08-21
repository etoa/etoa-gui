<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class ShipCategoriesForm extends SimpleForm
{
    protected function getName(): string
    {
        return "Schiff-Kategorien";
    }

    protected function getTable(): string
    {
        return "ship_cat";
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
                "name" => "cat_id",
                "text" => "ID",
                "type" => "text",
                "def_val" => "",
                "size" => "1",
                "maxlen" => "0",
                "rows" => "0",
                "cols" => "0",
                "rcb_elem" => "",
                "rcb_elem_checked" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1,
            ], [
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
            ], [
                "name" => "cat_color",
                "text" => "Farbe",
                "type" => "text",
                "def_val" => "#ffffff",
                "size" => "7",
                "maxlen" => "7",
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
