<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

class PlanetTypesForm extends AdvancedForm
{
    protected function getName(): string
    {
        return "Planetentypen";
    }

    protected function getTable(): string
    {
        return "planet_types";
    }

    protected function getTableId(): string
    {
        return "type_id";
    }

    protected function getOverviewOrderField(): string
    {
        return "type_name";
    }

    protected function getSwitches(): array
    {
        return [
            "Standardtyp" => 'type_consider',
        ];
    }

    protected function getFields(): array
    {
        return [
            [
                "name" => "type_name",
                "text" => "Kategoriename",
                "type" => "text",
                "size" => 30,
                "maxlen" => 250,
                "show_overview" => true,
                "link_in_overview" => true,
            ], [
                "name" => "type_comment",
                "text" => "Kommentar",
                "type" => "textarea",
                "rows" => 5,
                "cols" => 25,
                "show_overview" => true,
            ], [
                "name" => "type_habitable",
                "text" => "Bewohnbar",
                "type" => "radio",
                "def_val" => "1",
                "items" => [
                    "Ja" => 1,
                    "Nein" => 0,
                ],
                "show_overview" => true,
            ], [
                "name" => "type_f_metal",
                "text" => "Metallfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "type_f_crystal",
                "text" => "Kristallfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "type_f_plastic",
                "text" => "Plastikfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "type_f_fuel",
                "text" => "Treibstofffaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "type_f_food",
                "text" => "Nahrungsfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "type_f_power",
                "text" => "Stromfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "type_f_population",
                "text" => "Bevölkerungsfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "type_f_researchtime",
                "text" => "Forschungszeitfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ], [
                "name" => "type_f_buildtime",
                "text" => "Bauzeitfaktor",
                "type" => "decimal",
                "def_val" => "1.00",
                "show_overview" => false,
            ],
        ];
    }
}
