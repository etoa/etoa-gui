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
                "show_overview" => 1,
            ],
            [
                "name" => "specialist_name",
                "text" => "Name",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 1,
                "link_in_overview" => 1,
            ],
            [
                "name" => "specialist_desc",
                "text" => "Beschreibung",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "7",
                "cols" => "50",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_points_req",
                "text" => "Punkteminimum",
                "type" => "text",
                "def_val" => "100000",
                "size" => "10",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 1,
            ],
            [
                "name" => "specialist_days",
                "text" => "Anstellungsdauer (Tage)",
                "type" => "text",
                "def_val" => "7",
                "size" => "2",
                "maxlen" => "3",
                "rows" => "",
                "cols" => "",
                "show_overview" => 1,
                "line" => 1,
            ],
            [
                "name" => "specialist_costs_metal",
                "text" => "Kosten Metall",
                "type" => "text",
                "def_val" => "100000",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_costs_crystal",
                "text" => "Kosten Kristall",
                "type" => "text",
                "def_val" => "100000",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_costs_plastic",
                "text" => "Kosten Plastik",
                "type" => "text",
                "def_val" => "100000",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_costs_fuel",
                "text" => "Kosten Treibstoff",
                "type" => "text",
                "def_val" => "100000",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_costs_food",
                "text" => "Kosten Nahrung",
                "type" => "text",
                "def_val" => "100000",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
                "line" => 1,
            ],
            [
                "name" => "specialist_prod_metal",
                "text" => "Metallproduktion",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_prod_crystal",
                "text" => "Kristallproduktion",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_prod_plastic",
                "text" => "Plastikproduktion",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_prod_fuel",
                "text" => "Treibstoffproduktion",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_prod_food",
                "text" => "Nahrungsproduktion",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_power",
                "text" => "Energieproduktion",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_population",
                "text" => "Bevölkerungswachstum",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_time_tech",
                "text" => "Forschungszeit",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_time_buildings",
                "text" => "Gebäudebauzeit",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_time_defense",
                "text" => "Verteidigungsbauzeit",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_time_ships",
                "text" => "Schiffbauzeit",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_costs_defense",
                "text" => "Verteidigungskosten",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_costs_ships",
                "text" => "Schiffkosten",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_costs_tech",
                "text" => "Forschungskosten",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_fleet_speed",
                "text" => "Flottengeschwindigkeit",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_fleet_max",
                "text" => "Zusätzliche Flotten",
                "type" => "text",
                "def_val" => "0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_def_repair",
                "text" => "Verteidigungsreparatur",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_spy_level",
                "text" => "Zusätzlicher Spionagelevel",
                "type" => "text",
                "def_val" => "0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_tarn_level",
                "text" => "Zusätzlicher Tarnlevel",
                "type" => "text",
                "def_val" => "0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_trade_time",
                "text" => "Geschwindigkeit der Handelsflotten",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
            [
                "name" => "specialist_trade_bonus",
                "text" => "Handelskosten",
                "type" => "text",
                "def_val" => "1.0",
                "size" => "4",
                "maxlen" => "7",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
        ];
    }
}
