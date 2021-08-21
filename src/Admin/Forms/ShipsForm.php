<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

use EtoA\Ranking\RankingService;

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
                "show_overview" => 1,
            ], [
                "name" => "ship_name",
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
                "name" => "ship_shortcomment",
                "text" => "Kurzbeschreibung",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "5",
                "cols" => "50",
                "show_overview" => 0,
            ], [
                "name" => "ship_longcomment",
                "text" => "Beschreibung",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "7",
                "cols" => "50",
                "show_overview" => 0,
            ], [
                "name" => "ship_cat_id",
                "text" => "Kategorie",
                "type" => "select",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "select_elem" => $this->getSelectElements('ship_cat', "cat_id", "cat_name", "cat_name", array("0" => "-")),
                "show_overview" => 1,
            ], [
                "name" => "ship_race_id",
                "text" => "Rasse",
                "type" => "select",
                "def_val" => "",
                "select_elem" => $this->getSelectElements('races', "race_id", "race_name", "race_name", array("0" => "-")),
                "show_overview" => 1,
                "line" => 1,
            ], [
                "name" => "ship_costs_metal",
                "text" => "Kosten Metall",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_costs_crystal",
                "text" => "Kosten Kristall",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_costs_plastic",
                "text" => "Kosten Plastik",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_costs_fuel",
                "text" => "Kosten Treibstoff",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_costs_food",
                "text" => "Kosten Nahrung",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_points",
                "text" => "Punkte",
                "type" => "readonly",
                "show_overview" => 0,
                "line" => 1,
            ], [
                "name" => "ship_fuel_use",
                "text" => "Treibstoffverbrauch",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_fuel_use_launch",
                "text" => "Treibstoff Start",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_fuel_use_landing",
                "text" => "Treibstoff Landung",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_capacity",
                "text" => "Laderaum",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_people_capacity",
                "text" => "Passagierraum",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_pilots",
                "text" => "Piloten",
                "type" => "text",
                "def_val" => "1",
                "size" => "2",
                "maxlen" => "3",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_bounty_bonus",
                "text" => "max Beute",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_speed",
                "text" => "Geschwindigkeit",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_time2start",
                "text" => "Startzeit",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_time2land",
                "text" => "Landezeit",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
                "line" => 1,
            ], [
                "name" => "ship_structure",
                "text" => "Struktur",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_shield",
                "text" => "Schild",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_weapon",
                "text" => "Waffe",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_heal",
                "text" => "Heilung pro Runde",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
                "line" => 1,
            ], [
                "name" => "ship_max_count",
                "text" => "Max. Anzahl (0=unentlich)",
                "type" => "text",
                "def_val" => "",
                "size" => "7",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_fieldsprovide",
                "text" => "Zur Verfüg. gest. Felder",
                "type" => "text",
                "def_val" => "0",
                "size" => "1",
                "maxlen" => "3",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_fakeable",
                "text" => "Verwenden bei Täuschangriff",
                "type" => "radio",
                "def_val" => "0",
                "size" => "",
                "maxlen" => "",
                "rows" => "",
                "cols" => "",
                "items" => [
                    "Ja" => 1,
                    "Nein" => 0,
                ],
                "show_overview" => 0,
                "columnend" => 1,
            ], [
                "name" => "ship_actions",
                "text" => "Aktionen",
                "type" => "fleetaction",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "2",
                "cols" => "60",
                "show_overview" => 0,
                "line" => 1,
            ], [
                "name" => "ship_alliance_shipyard_level",
                "text" => "Allianzschiff: Ben&ouml;tigte Werftstufe",
                "type" => "text",
                "def_val" => "",
                "size" => "2",
                "maxlen" => "3",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_alliance_costs",
                "text" => "Allianzschiff: Kosten (Schiffsteile)",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
                "line" => 1,
            ], [
                "name" => "special_ship",
                "text" => "Spezial Schiff",
                "type" => "radio",
                "def_val" => "0",
                "size" => "",
                "maxlen" => "",
                "rows" => "",
                "cols" => "",
                "items" => [
                    "Ja" => 1,
                    "Nein" => 0,
                ],
                "show_overview" => 0,
                "show_hide" => array("special_ship_max_level", "special_ship_need_exp", "special_ship_exp_factor", "special_ship_bonus_weapon", "special_ship_bonus_structure", "special_ship_bonus_shield", "special_ship_bonus_heal", "special_ship_bonus_capacity", "special_ship_bonus_speed", "special_ship_bonus_pilots", "special_ship_bonus_tarn", "special_ship_bonus_antrax", "special_ship_bonus_forsteal", "special_ship_bonus_build_destroy", "special_ship_bonus_antrax_food", "special_ship_bonus_deactivade", "special_ship_bonus_readiness"),
                "hide_show" => array("ship_tradable"),
            ], [
                "name" => "special_ship_max_level",
                "text" => "Max. Level (0=unentlich)",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_need_exp",
                "text" => "EXP",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_exp_factor",
                "text" => "EXP Faktor",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_bonus_weapon",
                "text" => "Waffen-Bonus (0.1=10% pro Stufe)",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_bonus_structure",
                "text" => "Struktur-Bonus",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_bonus_shield",
                "text" => "Schild-Bonus",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_bonus_heal",
                "text" => "Heil-Bonus",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_bonus_capacity",
                "text" => "Kapazität-Bonus",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_bonus_speed",
                "text" => "Speed-Bonus",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_bonus_pilots",
                "text" => "Piloten-Bonus",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_bonus_tarn",
                "text" => "Tarn-Bonus",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_bonus_antrax",
                "text" => "Giftgas-Bonus",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_bonus_forsteal",
                "text" => "Techklau-Bonus",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_bonus_build_destroy",
                "text" => "Bombardier-Bonus",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_bonus_antrax_food",
                "text" => "Antrax-Bonus",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_bonus_deactivade",
                "text" => "Deaktivier-Bonus",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "special_ship_bonus_readiness",
                "text" => "Bereitschafts-Bonus (Start/Landung)",
                "type" => "text",
                "def_val" => "",
                "size" => "20",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "ship_tradable",
                "text" => "Handelbar",
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
