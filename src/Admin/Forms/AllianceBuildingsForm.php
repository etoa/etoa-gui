<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class AllianceBuildingsForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Allianzgebäude";
    }

    protected function getTable(): string
    {
        return "alliance_buildings";
    }

    protected function getTableId(): string
    {
        return "alliance_building_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "alliance_building_id";
    }

    protected function getImagePath(): ?string
    {
        return IMAGE_PATH . "/abuildings/building<DB_TABLE_ID>_small." . IMAGE_EXT;
    }

    protected function getSwitches(): array
    {
        return [
            "Anzeigen" => 'alliance_building_show',
        ];
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "alliance_building_id",
                "text" => "ID",
                "type" => "readonly",
                "show_overview" => 1,
            ], [
                "name" => "alliance_building_name",
                "text" => "Name",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1,
                "link_in_overview" => 1,
            ], [
                "name" => "alliance_building_shortcomment",
                "text" => "Kurzbeschreibung",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "7",
                "cols" => "35",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1,
                "line" => 0,
            ], [
                "name" => "alliance_building_longcomment",
                "text" => "lange Beschreibung",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "7",
                "cols" => "35",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1,
                "line" => 1,
            ], [
                "name" => "alliance_building_costs_metal",
                "text" => "Kosten Metall",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ], [
                "name" => "alliance_building_costs_crystal",
                "text" => "Kosten Kristall",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ], [
                "name" => "alliance_building_costs_plastic",
                "text" => "Kosten Plastik",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ], [
                "name" => "alliance_building_costs_fuel",
                "text" => "Kosten Treibstoff",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ], [
                "name" => "alliance_building_costs_food",
                "text" => "Kosten Nahrung",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ], [
                "name" => "alliance_building_build_time",
                "text" => "Bauzeit (Sekunden)",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ], [
                "name" => "alliance_building_costs_factor",
                "text" => "Kostenfaktor",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
                "line" => 1,
            ], [
                "name" => "alliance_building_last_level",
                "text" => "Max Level",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1,
            ], [
                "name" => "alliance_building_needed_id",
                "text" => "Voraussetzung",
                "type" => "select",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => $this->getSelectElements('alliance_buildings', "alliance_building_id", "alliance_building_name", "alliance_building_id"),
                "select_elem_checked" => "",
                "show_overview" => 1,
            ], [
                "name" => "alliance_building_needed_level",
                "text" => "Voraussetzung Stufe",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1,
            ],
        ];
    }
}
