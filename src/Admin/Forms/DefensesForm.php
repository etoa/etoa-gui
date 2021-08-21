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
        return IMAGE_PATH . "/defense/def<DB_TABLE_ID>_small." . IMAGE_EXT;
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
                "show_overview" => 1
            ], [
                "name" => "def_name",
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
                "link_in_overview" => 1
            ], [
                "name" => "def_shortcomment",
                "text" => "Kurze Beschreibung",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "5",
                "cols" => "50",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0
            ], [
                "name" => "def_longcomment",
                "text" => "Lange Beschreibung",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "7",
                "cols" => "50",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
                "line" => 1
            ], [
                "name" => "def_costs_metal",
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
                "show_overview" => 0
            ], [
                "name" => "def_costs_crystal",
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
                "show_overview" => 0
            ], [
                "name" => "def_costs_plastic",
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
                "show_overview" => 0
            ], [
                "name" => "def_costs_fuel",
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
                "show_overview" => 0
            ], [
                "name" => "def_costs_food",
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
                "show_overview" => 0
            ], [
                "name" => "def_points",
                "text" => "Punkte",
                "type" => "readonly",
                "show_overview" => 0,
                "columnend" => 1
            ], [
                "name" => "def_structure",
                "text" => "Struktur",
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
                "show_overview" => 0
            ], [
                "name" => "def_shield",
                "text" => "Schild",
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
                "show_overview" => 0
            ], [
                "name" => "def_weapon",
                "text" => "Waffe",
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
                "show_overview" => 0
            ], [
                "name" => "def_heal",
                "text" => "Reparatur",
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
                "line" => 1
            ], [
                "name" => "def_fields",
                "text" => "Felder",
                "type" => "text",
                "def_val" => "",
                "size" => "10",
                "maxlen" => "255",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1
            ], [
                "name" => "def_max_count",
                "text" => "Max Anzahl",
                "type" => "text",
                "def_val" => "",
                "size" => "10",
                "maxlen" => "255",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0
            ], [
                "name" => "def_cat_id",
                "text" => "Kategorie",
                "type" => "select",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => $this->getSelectElements('def_cat', "cat_id", "cat_name", "cat_name", array("0" => "(Keine)")),
                "select_elem_checked" => "",
                "show_overview" => 1
            ], [
                "name" => "def_race_id",
                "text" => "Rasse",
                "type" => "select",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => $this->getSelectElements('races', "race_id", "race_name", "race_name", array("0" => "(Keine)")),
                "select_elem_checked" => "",
                "show_overview" => 1
            ]
        ];
    }
}
