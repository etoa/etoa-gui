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
                "size" => 30,
                "max_len" => 250,
                "show_overview" => true,
                "link_in_overview" => true,
            ], [
                "name" => "sol_type_comment",
                "text" => "Kommentar",
                "type" => "textarea",
                "rows" => 5,
                "cols" => 25,
                "show_overview" => true,
            ], [
                "name" => "sol_type_f_metal",
                "text" => "Metallfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "sol_type_f_crystal",
                "text" => "Kristallfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "sol_type_f_plastic",
                "text" => "Plastikfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "sol_type_f_fuel",
                "text" => "Treibstofffaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "sol_type_f_food",
                "text" => "Nahrungsfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "sol_type_f_power",
                "text" => "Stromfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "sol_type_f_population",
                "text" => "Bevölkerungsfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "sol_type_f_researchtime",
                "text" => "Forschungszeitfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "sol_type_f_buildtime",
                "text" => "Bauzeitfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ],
        ];
    }
}
