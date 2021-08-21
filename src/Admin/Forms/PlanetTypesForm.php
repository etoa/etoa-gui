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
                "def_val" => "",
                "size" => "30",
                "maxlen" => "250",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_checked" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1,
                "link_in_overview" => 1,
            ], [
                "name" => "type_comment",
                "text" => "Kommentar",
                "type" => "textarea",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "5",
                "cols" => "25",
                "rcb_elem" => "",
                "rcb_elem_checked" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1,
            ], [
                "name" => "type_habitable",
                "text" => "Bewohnbar",
                "type" => "radio",
                "def_val" => "",
                "size" => "",
                "maxlen" => "",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => array("Ja" => 1, "Nein" => 0),
                "rcb_elem_checked" => "1",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 1,
            ], [
                "name" => "type_f_metal",
                "text" => "Metallfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_checked" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ], [
                "name" => "type_f_crystal",
                "text" => "Kristallfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_checked" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ], [
                "name" => "type_f_plastic",
                "text" => "Plastikfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_checked" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ], [
                "name" => "type_f_fuel",
                "text" => "Treibstofffaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_checked" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ], [
                "name" => "type_f_food",
                "text" => "Nahrungsfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_checked" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ], [
                "name" => "type_f_power",
                "text" => "Stromfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_checked" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ], [
                "name" => "type_f_population",
                "text" => "Bevölkerungsfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_checked" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ], [
                "name" => "type_f_researchtime",
                "text" => "Forschungszeitfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_checked" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ], [
                "name" => "type_f_buildtime",
                "text" => "Bauzeitfaktor",
                "type" => "text",
                "def_val" => "1",
                "size" => "5",
                "maxlen" => "5",
                "rows" => "",
                "cols" => "",
                "rcb_elem" => "",
                "rcb_elem_checked" => "",
                "select_elem" => "",
                "select_elem_checked" => "",
                "show_overview" => 0,
            ],
        ];
    }
}
