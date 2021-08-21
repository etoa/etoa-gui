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
                "def_val" => "",
                "size" => "30",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => true,
                "link_in_overview" => true,
            ],
            [
                "name" => "race_short_comment",
                "text" => "Kurzer Kommentar",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "3",
                "cols" => "40",
                "show_overview" => true,
                "overview_length" => 400,

            ],
            [
                "name" => "race_comment",
                "text" => "Kommentar",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "10",
                "cols" => "40",
                "show_overview" => false,
            ],
            [
                "name" => "race_adj1",
                "text" => "Adjektiv männlich",
                "type" => "text",
                "def_val" => "",
                "size" => "30",
                "maxlen" => "50",
                "show_overview" => false,
            ],
            [
                "name" => "race_adj2",
                "text" => "Adjektiv weiblich",
                "type" => "text",
                "def_val" => "",
                "size" => "30",
                "maxlen" => "50",
                "show_overview" => false,
            ],
            [
                "name" => "race_adj3",
                "text" => "Adjektiv plural",
                "type" => "text",
                "def_val" => "",
                "size" => "30",
                "maxlen" => "50",
                "show_overview" => false,
            ],
            [
                "name" => "race_leadertitle",
                "text" => "Leader-Titel",
                "type" => "text",
                "def_val" => "",
                "size" => "30",
                "maxlen" => "30",
                "rows" => "0",
                "cols" => "0",
                "show_overview" => true,
                "overview_length" => 200,
            ],
            [
                "name" => "race_f_metal",
                "text" => "Metallfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_crystal",
                "text" => "Kristallfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_plastic",
                "text" => "Plastikfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_fuel",
                "text" => "Treibstofffaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_food",
                "text" => "Nahrungsfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_power",
                "text" => "Stromfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_population",
                "text" => "Bevölkerungsfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_researchtime",
                "text" => "Forschungszeitfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_buildtime",
                "text" => "Bauzeitfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ],
            [
                "name" => "race_f_fleettime",
                "text" => "Bonus Fluggeschwindigkeit (grösser ist besser)",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => false,
            ],
        ];
    }
}
