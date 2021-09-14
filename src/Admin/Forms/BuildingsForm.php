<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

use EtoA\Core\ObjectWithImage;
use EtoA\Ranking\RankingService;

class BuildingsForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Gebäude";
    }

    protected function getTable(): string
    {
        return "buildings";
    }

    protected function getTableId(): string
    {
        return "building_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "building_type_id, building_order, building_name";
    }

    protected function getTableSort(): ?string
    {
        return 'building_order';
    }

    protected function getTableSortParent(): ?string
    {
        return 'building_type_id';
    }

    protected function getImagePath(): ?string
    {
        return ObjectWithImage::BASE_PATH . "/buildings/building<DB_TABLE_ID>_small.png";
    }

    protected function getSwitches(): array
    {
        return [
            "Anzeigen" => 'building_show',
        ];
    }

    protected function runPostInsertUpdateHook(): string
    {
        /** @var RankingService $rankingService */
        $rankingService = $this->app[RankingService::class];
        $numBuildings = $rankingService->calcBuildingPoints();

        return sprintf("Die Gebäudepunkte von %s Gebäuden wurden aktualisiert!", $numBuildings);
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "building_id",
                "text" => "ID",
                "type" => "readonly",
                "show_overview" => true,
            ],
            [
                "name" => "building_name",
                "text" => "Name",
                "type" => "text",
                "size" => 20,
                "max_len" => 250,
                "show_overview" => true,
                "link_in_overview" => true,
            ],
            [
                "name" => "building_type_id",
                "text" => "Kategorie",
                "type" => "select",
                "def_val" => "",
                "items" => $this->getSelectElements('building_types', "type_id", "type_name", "type_name"),
                "show_overview" => true,
            ],
            [
                "name" => "building_shortcomment",
                "text" => "Kurzbeschrieb",
                "type" => "textarea",
                "rows" => 7,
                "cols" => 35,
                "show_overview" => true,
            ],
            [
                "name" => "building_longcomment",
                "text" => "Beschreibung",
                "type" => "textarea",
                "rows" => 9,
                "cols" => 35,
                "show_overview" => false,
                "line" => true,
            ],
            [
                "name" => "building_costs_metal",
                "text" => "Kosten Metall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_costs_crystal",
                "text" => "Kosten Kristall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_costs_plastic",
                "text" => "Kosten Plastik",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_costs_fuel",
                "text" => "Kosten Treibstoff",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_costs_food",
                "text" => "Kosten Nahrung",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_costs_power",
                "text" => "Kosten Energie",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_build_costs_factor",
                "text" => "Kostenfaktor Bau",
                "type" => "decimal",
                "def_val" => "0.00",
                "show_overview" => false,
            ],
            [
                "name" => "building_demolish_costs_factor",
                "text" => "Kostenfaktor Abbruch",
                "type" => "decimal",
                "def_val" => "0.00",
                "show_overview" => false,
                "column_end" => true,
            ],
            [
                "name" => "building_power_use",
                "text" => "Stromverbrauch",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_fuel_use",
                "text" => "Tritiumverbrauch",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_power_req",
                "text" => "Strombedarf (wird nicht verbraucht)",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
                "line" => true,
            ],
            [
                "name" => "building_prod_metal",
                "text" => "Produktion Metall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_prod_crystal",
                "text" => "Produktion Kristall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_prod_plastic",
                "text" => "Produktion Plastik",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_prod_fuel",
                "text" => "Produktion Treibstoff",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_prod_food",
                "text" => "Produktion Nahrung",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_prod_power",
                "text" => "Produktion Strom",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_production_factor",
                "text" => "Produktionsfaktor",
                "type" => "decimal",
                "def_val" => "0.00",
                "show_overview" => false,
                "line" => true,
            ],
            [
                "name" => "building_store_metal",
                "text" => "Speicher Metall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_store_crystal",
                "text" => "Speicher Kristall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_store_plastic",
                "text" => "Speicher Plastik",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_store_fuel",
                "text" => "Speicher Treibstoff",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_store_food",
                "text" => "Speicher Nahrung",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_store_factor",
                "text" => "Speicherfaktor",
                "type" => "decimal",
                "def_val" => "0.00",
                "show_overview" => false,
                "line" => true,
            ],
            [
                "name" => "building_last_level",
                "text" => "Max Level",
                "type" => "numeric",
                "def_val" => "99",
                "show_overview" => true,
            ],
            [
                "name" => "building_fields",
                "text" => "Felderverbrauch",
                "type" => "numeric",
                "def_val" => "1",
                "show_overview" => false,
            ],
            [
                "name" => "building_people_place",
                "text" => "Bewohnbare Fläche",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_fieldsprovide",
                "text" => "Zur Verfügung gestellte Felder",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_bunker_res",
                "text" => "Ressourcen-Grundkapazität Bunker",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_bunker_fleet_count",
                "text" => "Schiffszahl-Grundkapazität Bunker",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "building_bunker_fleet_space",
                "text" => "Schiffsstruktur-Grundkapazität Bunker",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
        ];
    }
}
