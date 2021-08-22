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

    protected function getSwitches(): array
    {
        return [
            "Anzeigen" => 'missile_show',
            "Startbar" => 'missile_launchable',
        ];
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
                "size" => 20,
                "max_len" => 250,
                "show_overview" => true,
                "link_in_overview" => true,
            ], [
                "name" => "missile_sdesc",
                "text" => "Kurzbeschreibung",
                "type" => "textarea",
                "rows" => 5,
                "cols" => 50,
                "show_overview" => true,
            ], [
                "name" => "missile_ldesc",
                "text" => "Lange Beschreibung",
                "type" => "textarea",
                "rows" => 7,
                "cols" => 50,
                "show_overview" => false,
            ], [
                "name" => "missile_costs_metal",
                "text" => "Kosten Metall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "missile_costs_crystal",
                "text" => "Kosten Kristall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "missile_costs_plastic",
                "text" => "Kosten Plastik",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "missile_costs_fuel",
                "text" => "Kosten Treibstoff",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "missile_costs_food",
                "text" => "Kosten Nahrung",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "missile_damage",
                "text" => "Schaden",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => true,
            ], [
                "name" => "missile_speed",
                "text" => "Geschwindigkeit",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => true,
            ], [
                "name" => "missile_range",
                "text" => "Reichweite",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => true,
            ], [
                "name" => "missile_deactivate",
                "text" => "EMP",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => true,
            ], [
                "name" => "missile_def",
                "text" => "Verteidigung",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => true,
            ],
        ];
    }
}
