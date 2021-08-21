<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class ObjectTransformsForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Objekt-Transformationen";
    }

    protected function getTable(): string
    {
        return "obj_transforms";
    }

    protected function getTableId(): string
    {
        return "id";
    }

    protected function getOverviewOrderField(): string
    {
        return "id";
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "def_id",
                "text" => "Verteidigung",
                "type" => "select",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => $this->getSelectElements('defense', "def_id", "def_name", "def_order"),
                "select_elem_checked" => "",
                "show_overview" => 1
            ], [
                "name" => "ship_id",
                "text" => "Schiff",
                "type" => "select",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => $this->getSelectElements('ships', "ship_id", "ship_name", "ship_order"),
                "select_elem_checked" => "",
                "show_overview" => 1
            ], [
                "name" => "costs_metal",
                "text" => "Kosten Metall",
                "type" => "text",
                "def_val" => "0",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0
            ], [
                "name" => "costs_crystal",
                "text" => "Kosten Kristall",
                "type" => "text",
                "def_val" => "0",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0
            ], [
                "name" => "costs_plastic",
                "text" => "Kosten Plastik",
                "type" => "text",
                "def_val" => "0",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0
            ], [
                "name" => "costs_fuel",
                "text" => "Kosten Treibstoff",
                "type" => "text",
                "def_val" => "0",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0
            ], [
                "name" => "costs_food",
                "text" => "Kosten Nahrung",
                "type" => "text",
                "def_val" => "0",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0
            ], [
                "name" => "costs_factor_sd",
                "text" => "Kostenfaktor S -> V",
                "type" => "text",
                "def_val" => "0",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1
            ], [
                "name" => "costs_factor_ds",
                "text" => "Kostenfaktor V -> S",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1
            ], [
                "name" => "num_def",
                "text" => "Anzahl V pro S",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1
            ],
        ];
    }
}
