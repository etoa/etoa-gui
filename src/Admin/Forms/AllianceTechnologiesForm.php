<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class AllianceTechnologiesForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Allianztechnologien";
    }

    protected function getTable(): string
    {
        return "alliance_technologies";
    }

    protected function getTableId(): string
    {
        return "alliance_tech_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "alliance_tech_id";
    }

    protected function getImagePath(): ?string
    {
        return IMAGE_PATH . "/atechnologies/technology<DB_TABLE_ID>_small." . IMAGE_EXT;
    }

    protected function getSwitches(): array
    {
        return [
            "Anzeigen" => 'alliance_tech_show',
        ];
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "alliance_tech_id",
                "text" => "ID",
                "type" => "readonly",
                "show_overview" => true,
            ], [
                "name" => "alliance_tech_name",
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
                "name" => "alliance_tech_longcomment",
                "text" => "Beschreibung",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "7",
                "cols" => "35",
                "show_overview" => true,
                "line" => 1,
            ], [
                "name" => "alliance_tech_costs_metal",
                "text" => "Kosten Metall",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ], [
                "name" => "alliance_tech_costs_crystal",
                "text" => "Kosten Kristall",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ], [
                "name" => "alliance_tech_costs_plastic",
                "text" => "Kosten Plastik",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ], [
                "name" => "alliance_tech_costs_fuel",
                "text" => "Kosten Treibstoff",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ], [
                "name" => "alliance_tech_costs_food",
                "text" => "Kosten Nahrung",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ], [
                "name" => "alliance_tech_build_time",
                "text" => "Bauzeit (Sekunden)",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ], [
                "name" => "alliance_tech_costs_factor",
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
                "name" => "alliance_tech_last_level",
                "text" => "Max Level",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => true,
            ], [
                "name" => "alliance_tech_needed_id",
                "text" => "Voraussetzung",
                "type" => "select",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "",
                "cols" => "",
                "select_elem" => $this->getSelectElements('alliance_technologies', "alliance_tech_id", "alliance_tech_name", "alliance_tech_id"),
                "show_overview" => true,
            ], [
                "name" => "alliance_tech_needed_level",
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
