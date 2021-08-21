<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class MissilesForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Raketen";
    }

    protected function getTable(): string
    {
        return "missiles";
    }

    protected function getTableId(): string
    {
        return "missile_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "missile_name";
    }

    protected function getImagePath(): ?string
    {
        return IMAGE_PATH . "/missiles/missile<DB_TABLE_ID>_small." . IMAGE_EXT;
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "missile_name",
                "text" => "Name",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 1,
                "link_in_overview" => 1,
            ], [
                "name" => "missile_sdesc",
                "text" => "Titel",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "5",
                "cols" => "50",
                "show_overview" => 0,
            ], [
                "name" => "missile_ldesc",
                "text" => "Text",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "7",
                "cols" => "50",
                "show_overview" => 0,
            ], [
                "name" => "missile_costs_metal",
                "text" => "Kosten Metall",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "missile_costs_crystal",
                "text" => "Kosten Kristall",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "missile_costs_plastic",
                "text" => "Kosten Plastik",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "missile_costs_fuel",
                "text" => "Kosten Treibstoff",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "missile_costs_food",
                "text" => "Kosten Nahrung",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "missile_damage",
                "text" => "Schaden",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 1,
            ], [
                "name" => "missile_speed",
                "text" => "Geschwindigkeit",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 1,
            ], [
                "name" => "missile_range",
                "text" => "Reichweite",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 1,
            ], [
                "name" => "missile_deactivate",
                "text" => "EMP",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 1,
            ], [
                "name" => "missile_def",
                "text" => "Verteidigung",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 1,
            ], [
                "name" => "missile_launchable",
                "text" => "Startbar",
                "type" => "radio",
                "def_val" => "1",
                "size" => "",
                "maxlen" => "",
                "rows" => "",
                "cols" => "",
                "items" => [
                    "Ja" => 1,
                    "Nein" => 0,
                ],
                "show_overview" => 0,
            ], [
                "name" => "missile_show",
                "text" => "Anzeigen",
                "type" => "radio",
                "def_val" => "1",
                "size" => "",
                "maxlen" => "",
                "rows" => "",
                "cols" => "",
                "items" => [
                    "Ja" => 1,
                    "Nein" => 0,
                ],
                "show_overview" => 0,
            ],
        ];
    }
}
