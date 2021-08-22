<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class RacesForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Rassen";
    }

    protected function getTable(): string
    {
        return "races";
    }

    protected function getTableId(): string
    {
        return "race_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "race_name";
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "race_id",
                "text" => "ID",
                "type" => "readonly",
                "show_overview" => true,
            ],
            [
                "name" => "race_name",
                "text" => "Rasse",
                "type" => "text",
                "size" => 30,
                "max_len" => 250,
                "show_overview" => true,
                "link_in_overview" => true,
            ],
            [
                "name" => "race_short_comment",
                "text" => "Kurzer Kommentar",
                "type" => "textarea",
                "rows" => 3,
                "cols" => 40,
                "show_overview" => true,
            ],
            [
                "name" => "race_comment",
                "text" => "Kommentar",
                "type" => "textarea",
                "rows" => 10,
                "cols" => 40,
                "show_overview" => false,
            ],
            [
                "name" => "race_adj1",
                "text" => "Adjektiv männlich",
                "type" => "text",
                "size" => 30,
                "max_len" => 50,
                "show_overview" => false,
            ],
            [
                "name" => "race_adj2",
                "text" => "Adjektiv weiblich",
                "type" => "text",
                "size" => 30,
                "max_len" => 50,
                "show_overview" => false,
            ],
            [
                "name" => "race_adj3",
                "text" => "Adjektiv plural",
                "type" => "text",
                "size" => 30,
                "max_len" => 50,
                "show_overview" => false,
            ],
            [
                "name" => "race_leadertitle",
                "text" => "Leader-Titel",
                "type" => "text",
                "size" => 30,
                "max_len" => 30,
                "show_overview" => true,
            ],
            [
                "name" => "race_f_metal",
                "text" => "Metallfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_crystal",
                "text" => "Kristallfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_plastic",
                "text" => "Plastikfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_fuel",
                "text" => "Treibstofffaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_food",
                "text" => "Nahrungsfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_power",
                "text" => "Stromfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_population",
                "text" => "Bevölkerungsfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_researchtime",
                "text" => "Forschungszeitfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_buildtime",
                "text" => "Bauzeitfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_fleettime",
                "text" => "Bonus Fluggeschwindigkeit (grösser ist besser)",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ],
        ];
    }
}
