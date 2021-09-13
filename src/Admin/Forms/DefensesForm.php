<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

use EtoA\Ranking\RankingService;

class DefensesForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Verteidigung";
    }

    protected function getTable(): string
    {
        return "defense";
    }

    protected function getTableId(): string
    {
        return "def_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "def_cat_id,def_order, def_name";
    }

    protected function getTableSort(): ?string
    {
        return 'def_order';
    }

    protected function getTableSortParent(): ?string
    {
        return 'def_cat_id';
    }

    protected function getImagePath(): ?string
    {
        return IMAGE_PATH . "/defense/def<DB_TABLE_ID>_small.png";
    }

    protected function getSwitches(): array
    {
        return [
            "Anzeigen" => 'def_show',
            'Baubar' => 'def_buildable',
        ];
    }

    protected function runPostInsertUpdateHook(): string
    {
        /** @var RankingService $rankingService */
        $rankingService = $this->app[RankingService::class];
        $numDefenses = $rankingService->calcDefensePoints();

        return sprintf("Die Punkte von %d Verteidigungsanlagen wurden aktualisiert!", $numDefenses);
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "def_id",
                "text" => "ID",
                "type" => "readonly",
                "show_overview" => true,
            ], [
                "name" => "def_name",
                "text" => "Name",
                "type" => "text",
                "size" => 20,
                "max_len" => 250,
                "show_overview" => true,
                "link_in_overview" => true,
            ], [
                "name" => "def_shortcomment",
                "text" => "Kurze Beschreibung",
                "type" => "textarea",
                "rows" => 5,
                "cols" => 50,
                "show_overview" => true,
            ], [
                "name" => "def_longcomment",
                "text" => "Lange Beschreibung",
                "type" => "textarea",
                "rows" => 7,
                "cols" => 50,
                "show_overview" => false,
                "line" => true,
            ], [
                "name" => "def_costs_metal",
                "text" => "Kosten Metall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "def_costs_crystal",
                "text" => "Kosten Kristall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "def_costs_plastic",
                "text" => "Kosten Plastik",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "def_costs_fuel",
                "text" => "Kosten Treibstoff",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "def_costs_food",
                "text" => "Kosten Nahrung",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "def_points",
                "text" => "Punkte",
                "type" => "readonly",
                "show_overview" => false,
                "column_end" => true,
            ], [
                "name" => "def_structure",
                "text" => "Struktur",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "def_shield",
                "text" => "Schild",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "def_weapon",
                "text" => "Waffe",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "def_heal",
                "text" => "Reparatur",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "def_jam",
                "text" => "Störsender",
                "type" => "radio",
                "def_val" => "0",
                "items" => [
                    "Ja" => 1,
                    "Nein" => 0,
                ],
                "show_overview" => false,
                "line" => true,
            ], [
                "name" => "def_fields",
                "text" => "Felder",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => true,
            ], [
                "name" => "def_max_count",
                "text" => "Max Anzahl",
                "type" => "numeric",
                "def_val" => "999999",
                "show_overview" => false,
            ], [
                "name" => "def_cat_id",
                "text" => "Kategorie",
                "type" => "select",
                "items" => $this->getSelectElements('def_cat', "cat_id", "cat_name", "cat_name", ["0" => "(Keine)"]),
                "show_overview" => true,
            ], [
                "name" => "def_race_id",
                "text" => "Rasse",
                "type" => "select",
                "def_val" => "",
                "items" => $this->getSelectElements('races', "race_id", "race_name", "race_name", ["0" => "(Keine)"]),
                "show_overview" => true,
            ],
        ];
    }
}
