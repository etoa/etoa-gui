<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class StarTypesForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Sonnentypen";
    }

    protected function getTable(): string
    {
        return "sol_types";
    }

    protected function getTableId(): string
    {
        return "sol_type_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "sol_type_name";
    }

    protected function getSwitches(): array
    {
        return [
            "Standardtyp" => 'sol_type_consider',
        ];
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "sol_type_name",
                "text" => "Kategoriename",
                "type" => "text",
                "def_val" => "",
                "size" => "30",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "show_overview" => 1,
                "link_in_overview" => 1,
            ], [
                "name" => "sol_type_comment",
                "text" => "Kommentar",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "5",
                "cols" => "25",
                "show_overview" => 1,
            ], [
                "name" => "sol_type_f_metal",
                "text" => "Metallfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "sol_type_f_crystal",
                "text" => "Kristallfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "sol_type_f_plastic",
                "text" => "Plastikfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "sol_type_f_fuel",
                "text" => "Treibstofffaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "sol_type_f_food",
                "text" => "Nahrungsfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "sol_type_f_power",
                "text" => "Stromfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "sol_type_f_population",
                "text" => "Bevölkerungsfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "sol_type_f_researchtime",
                "text" => "Forschungszeitfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ], [
                "name" => "sol_type_f_buildtime",
                "text" => "Bauzeitfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "show_overview" => 0,
            ],
        ];
    }
}
