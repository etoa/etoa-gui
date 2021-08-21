<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

use EtoA\Ranking\RankingService;

class TechnologiesForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Forschung";
    }

    protected function getTable(): string
    {
        return "technologies";
    }

    protected function getTableId(): string
    {
        return "tech_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "tech_type_id, tech_order,tech_name";
    }

    protected function getTableSort(): ?string
    {
        return 'tech_order';
    }

    protected function getTableSortParent(): ?string
    {
        return 'tech_type_id';
    }

    protected function getImagePath(): ?string
    {
        return IMAGE_PATH . "/technologies/technology<DB_TABLE_ID>_small." . IMAGE_EXT;
    }

    protected function getSwitches(): array
    {
        return [
            "Anzeigen" => 'tech_show',
        ];
    }

    protected function runPostInsertUpdateHook(): string
    {
        /** @var RankingService $rankingService */
        $rankingService = $this->app[RankingService::class];
        $numTechnologies = $rankingService->calcTechPoints();

        return sprintf("Die Punkte von %s Technologien wurden aktualisiert!", $numTechnologies);
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "tech_id",
                "text" => "ID",
                "type" => "readonly",
                "show_overview" => 1,
            ],
            [
                "name" => "tech_name",
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
            ],
            [
                "name" => "tech_type_id",
                "text" => "Kategorie",
                "type" => "select",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_chekced" => "",
                "select_elem" => $this->getSelectElements('tech_types', "type_id", "type_name", "type_name"),
                "select_elem_checked" => "",
                "show_overview" => 1,
            ],
            [
                "name" => "tech_shortcomment",
                "text" => "Titel",
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
                "show_overview" => 0,
            ],
            [
                "name" => "tech_longcomment",
                "text" => "Text",
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
                "line" => 1,
            ],
            [
                "name" => "tech_costs_metal",
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
            ],
            [
                "name" => "tech_costs_crystal",
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
            ],
            [
                "name" => "tech_costs_plastic",
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
            ],
            [
                "name" => "tech_costs_fuel",
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
            ],
            [
                "name" => "tech_costs_food",
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
            ],
            [
                "name" => "tech_build_costs_factor",
                "text" => "Kostenfaktor Bau",
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
            ],
            [
                "name" => "tech_last_level",
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
            ],
            [
                "name" => "tech_stealable",
                "text" => "Stehlbar",
                "type" => "radio",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => array("Ja" => 1, "Nein" => 0),
                "rcb_elem_chekced" => "1",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ],
        ];
    }
}