<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

use EtoA\Ranking\RankingService;
use FleetAction;

class ShipsForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Schiffe";
    }

    protected function getTable(): string
    {
        return "ships";
    }

    protected function getTableId(): string
    {
        return "ship_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "ship_cat_id ASC, ship_order, ship_name";
    }

    protected function getTableSort(): ?string
    {
        return 'ship_order';
    }

    protected function getTableSortParent(): ?string
    {
        return 'ship_cat_id';
    }

    protected function getImagePath(): ?string
    {
        return IMAGE_PATH . "/ships/ship<DB_TABLE_ID>_small." . IMAGE_EXT;
    }

    protected function getSwitches(): array
    {
        return [
            "Anzeigen" => 'ship_show',
            'Baubar' => 'ship_buildable',
            'Startbar' => 'ship_launchable',
        ];
    }

    protected function runPostInsertUpdateHook(): string
    {
        /** @var RankingService $rankingService */
        $rankingService = $this->app[RankingService::class];
        $numShips = $rankingService->calcShipPoints();

        return sprintf("Die Punkte von %d Schiffen wurden aktualisiert!", $numShips);
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "ship_id",
                "text" => "ID",
                "type" => "readonly",
                "show_overview" => true,
            ], [
                "name" => "ship_name",
                "text" => "Name",
                "type" => "text",
                "size" => 20,
                "max_len" => 250,
                "show_overview" => true,
                "link_in_overview" => true,
            ], [
                "name" => "ship_shortcomment",
                "text" => "Kurzbeschreibung",
                "type" => "textarea",
                "rows" => 5,
                "cols" => 50,
                "show_overview" => true,
            ], [
                "name" => "ship_longcomment",
                "text" => "Beschreibung",
                "type" => "textarea",
                "rows" => 7,
                "cols" => 50,
                "show_overview" => false,
            ], [
                "name" => "ship_cat_id",
                "text" => "Kategorie",
                "type" => "select",
                "items" => $this->getSelectElements('ship_cat', "cat_id", "cat_name", "cat_name", ["0" => "-"]),
                "show_overview" => true,
            ], [
                "name" => "ship_race_id",
                "text" => "Rasse",
                "type" => "select",
                "def_val" => "",
                "items" => $this->getSelectElements('races', "race_id", "race_name", "race_name", ["0" => "-"]),
                "show_overview" => true,
                "line" => true,
            ], [
                "name" => "ship_costs_metal",
                "text" => "Kosten Metall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_costs_crystal",
                "text" => "Kosten Kristall",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_costs_plastic",
                "text" => "Kosten Plastik",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_costs_fuel",
                "text" => "Kosten Treibstoff",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_costs_food",
                "text" => "Kosten Nahrung",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_points",
                "text" => "Punkte",
                "type" => "readonly",
                "show_overview" => false,
                "line" => true,
            ], [
                "name" => "ship_fuel_use",
                "text" => "Treibstoffverbrauch",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_fuel_use_launch",
                "text" => "Treibstoff Start",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_fuel_use_landing",
                "text" => "Treibstoff Landung",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_capacity",
                "text" => "Laderaum",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_people_capacity",
                "text" => "Passagierraum",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_pilots",
                "text" => "Piloten",
                "type" => "numeric",
                "def_val" => "1",
                "show_overview" => false,
            ], [
                "name" => "ship_bounty_bonus",
                "text" => "Max. Beute",
                "type" => "decimal",
                "def_val" => "0.25",
                "show_overview" => false,
            ], [
                "name" => "ship_speed",
                "text" => "Geschwindigkeit",
                "type" => "numeric",
                "def_val" => "1",
                "show_overview" => false,
            ], [
                "name" => "ship_time2start",
                "text" => "Startzeit",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_time2land",
                "text" => "Landezeit",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
                "line" => true,
            ], [
                "name" => "ship_structure",
                "text" => "Struktur",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_shield",
                "text" => "Schild",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_weapon",
                "text" => "Waffe",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_heal",
                "text" => "Heilung pro Runde",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
                "line" => true,
            ], [
                "name" => "ship_max_count",
                "text" => "Max. Anzahl (0=unbegrenzt)",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_fieldsprovide",
                "text" => "Zur Verfüg. gest. Felder",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_fakeable",
                "text" => "Verwenden bei Täuschangriff",
                "type" => "radio",
                "def_val" => "0",
                "items" => [
                    "Ja" => 1,
                    "Nein" => 0,
                ],
                "show_overview" => false,
                "column_end" => true,
            ], [
                "name" => "ship_actions",
                "text" => "Aktionen",
                "type" => "comma_list",
                'items' => $this->getFleetActionList(),
                "show_overview" => false,
                "line" => true,
            ], [
                "name" => "ship_alliance_shipyard_level",
                "text" => "Allianzschiff: Benötigte Werftstufe",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_alliance_costs",
                "text" => "Allianzschiff: Kosten (Schiffsteile)",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
                "line" => true,
            ], [
                "name" => "special_ship",
                "text" => "Spezial Schiff",
                "type" => "radio",
                "def_val" => "0",
                "items" => [
                    "Ja" => 1,
                    "Nein" => 0,
                ],
                "show_overview" => false,
                "show_hide" => [
                    "special_ship_max_level",
                    "special_ship_need_exp",
                    "special_ship_exp_factor",
                    "special_ship_bonus_weapon",
                    "special_ship_bonus_structure",
                    "special_ship_bonus_shield",
                    "special_ship_bonus_heal",
                    "special_ship_bonus_capacity",
                    "special_ship_bonus_speed",
                    "special_ship_bonus_pilots",
                    "special_ship_bonus_tarn",
                    "special_ship_bonus_antrax",
                    "special_ship_bonus_forsteal",
                    "special_ship_bonus_build_destroy",
                    "special_ship_bonus_antrax_food",
                    "special_ship_bonus_deactivade",
                    "special_ship_bonus_readiness",
                ],
                "hide_show" => [
                    "ship_tradable",
                ],
            ], [
                "name" => "special_ship_max_level",
                "text" => "Max. Level (0=unbegrenzt)",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "special_ship_need_exp",
                "text" => "EXP",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "special_ship_exp_factor",
                "text" => "EXP Faktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "special_ship_bonus_weapon",
                "text" => "Waffen-Bonus (0.1=10% pro Stufe)",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "special_ship_bonus_structure",
                "text" => "Struktur-Bonus",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "special_ship_bonus_shield",
                "text" => "Schild-Bonus",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "special_ship_bonus_heal",
                "text" => "Heil-Bonus",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "special_ship_bonus_capacity",
                "text" => "Kapazität-Bonus",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "special_ship_bonus_speed",
                "text" => "Speed-Bonus",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "special_ship_bonus_pilots",
                "text" => "Piloten-Bonus",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "special_ship_bonus_tarn",
                "text" => "Tarn-Bonus",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "special_ship_bonus_antrax",
                "text" => "Giftgas-Bonus",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "special_ship_bonus_forsteal",
                "text" => "Techklau-Bonus",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "special_ship_bonus_build_destroy",
                "text" => "Bombardier-Bonus",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "special_ship_bonus_antrax_food",
                "text" => "Antrax-Bonus",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "special_ship_bonus_deactivade",
                "text" => "Deaktivier-Bonus",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "special_ship_bonus_readiness",
                "text" => "Bereitschafts-Bonus (Start/Landung)",
                "type" => "numeric",
                "def_val" => "0",
                "show_overview" => false,
            ], [
                "name" => "ship_tradable",
                "text" => "Handelbar",
                "type" => "radio",
                "def_val" => "1",
                "items" => [
                    "Ja" => 1,
                    "Nein" => 0,
                ],
                "show_overview" => false,
            ],
        ];
    }

    /**
     * @return array<string,string>
     */
    private function getFleetActionList(): array
    {
        $fleetActions = [];
        foreach (FleetAction::getAll() as $ac) {
            $fleetActions[$ac->__toString()] = $ac->code();
        }

        return $fleetActions;
    }
}
