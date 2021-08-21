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
                "show_overview" => true,
            ], [
                "name" => "alliance_building_name",
                "text" => "Name",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => true,
                "link_in_overview" => true,
            ], [
                "name" => "alliance_building_shortcomment",
                "text" => "Kurzbeschreibung",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "7",
                "cols" => "35",
                "show_overview" => true,
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
                "show_overview" => true,
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
                "show_overview" => false,
            ], [
                "name" => "alliance_building_costs_crystal",
                "text" => "Kosten Kristall",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ], [
                "name" => "alliance_building_costs_plastic",
                "text" => "Kosten Plastik",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ], [
                "name" => "alliance_building_costs_fuel",
                "text" => "Kosten Treibstoff",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ], [
                "name" => "alliance_building_costs_food",
                "text" => "Kosten Nahrung",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ], [
                "name" => "alliance_building_build_time",
                "text" => "Bauzeit (Sekunden)",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ], [
                "name" => "alliance_building_costs_factor",
                "text" => "Kostenfaktor",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
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
                "show_overview" => true,
            ], [
                "name" => "alliance_building_needed_id",
                "text" => "Voraussetzung",
                "type" => "select",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "",
                "cols" => "",
                "select_elem" => $this->getSelectElements('alliance_buildings', "alliance_building_id", "alliance_building_name", "alliance_building_id"),
                "show_overview" => true,
            ], [
                "name" => "alliance_building_needed_level",
                "text" => "Voraussetzung Stufe",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => true,
            ],
        ];
    }
}
