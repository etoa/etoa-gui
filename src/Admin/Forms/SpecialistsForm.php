<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class SpecialistsForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Spezialisten";
    }

    protected function getTable(): string
    {
        return "specialists";
    }

    protected function getTableId(): string
    {
        return "specialist_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "specialist_name";
    }

    protected function getSwitches(): array
    {
        return [
            "Anzeigen" => 'specialist_enabled',
        ];
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "specialist_id",
                "text" => "ID",
                "type" => "readonly",
                "show_overview" => true,
            ],
            [
                "name" => "specialist_name",
                "text" => "Name",
                "type" => "text",
                "size" => 20,
                "max_len" => 250,
                "show_overview" => true,
                "link_in_overview" => true,
            ],
            [
                "name" => "specialist_desc",
                "text" => "Beschreibung",
                "type" => "textarea",
                "rows" => 7,
                "cols" => 50,
                "show_overview" => true,
            ],
            [
                "name" => "specialist_points_req",
                "text" => "Punkteminimum",
                "type" => "numeric",
                "def_val" => "100000",
                "show_overview" => true,
            ],
            [
                "name" => "specialist_days",
                "text" => "Anstellungsdauer (Tage)",
                "type" => "numeric",
                "def_val" => "7",
                "show_overview" => true,
                "line" => true,
            ],
            [
                "name" => "specialist_costs_metal",
                "text" => "Kosten Metall",
                "type" => "numeric",
                "def_val" => "100000",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_costs_crystal",
                "text" => "Kosten Kristall",
                "type" => "numeric",
                "def_val" => "100000",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_costs_plastic",
                "text" => "Kosten Plastik",
                "type" => "numeric",
                "def_val" => "100000",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_costs_fuel",
                "text" => "Kosten Treibstoff",
                "type" => "numeric",
                "def_val" => "100000",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_costs_food",
                "text" => "Kosten Nahrung",
                "type" => "numeric",
                "def_val" => "100000",
                "show_overview" => false,
                "line" => true,
            ],
            [
                "name" => "specialist_prod_metal",
                "text" => "Metallproduktion",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_prod_crystal",
                "text" => "Kristallproduktion",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_prod_plastic",
                "text" => "Plastikproduktion",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_prod_fuel",
                "text" => "Treibstoffproduktion",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_prod_food",
                "text" => "Nahrungsproduktion",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_power",
                "text" => "Energieproduktion",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_population",
                "text" => "Bevölkerungswachstum",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_time_tech",
                "text" => "Forschungszeit",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_time_buildings",
                "text" => "Gebäudebauzeit",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_time_defense",
                "text" => "Verteidigungsbauzeit",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_time_ships",
                "text" => "Schiffbauzeit",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_costs_defense",
                "text" => "Verteidigungskosten",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_costs_ships",
                "text" => "Schiffkosten",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_costs_tech",
                "text" => "Forschungskosten",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_fleet_speed",
                "text" => "Flottengeschwindigkeit",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_fleet_max",
                "text" => "Zusätzliche Flotten",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_def_repair",
                "text" => "Verteidigungsreparatur",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_spy_level",
                "text" => "Zusätzlicher Spionagelevel",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_tarn_level",
                "text" => "Zusätzlicher Tarnlevel",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_trade_time",
                "text" => "Geschwindigkeit der Handelsflotten",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
            [
                "name" => "specialist_trade_bonus",
                "text" => "Handelskosten",
                "type" => "decimal",
                "def_val" => "1.0",
                "show_overview" => false,
            ],
        ];
    }
}
