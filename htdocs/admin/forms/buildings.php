<?PHP

// VARIABLES

define("MODUL_NAME", "Gebäude");
define("DB_TABLE", 'buildings');
define("DB_TABLE_ID", "building_id");
define("DB_OVERVIEW_ORDER_FIELD", "building_type_id, building_order, building_name");

define("DB_TABLE_SORT", 'building_order');
define("DB_TABLE_SORT_PARENT", 'building_type_id');

define("DB_IMAGE_PATH", IMAGE_PATH . "/buildings/building<DB_TABLE_ID>_small." . IMAGE_EXT);

$form_switches = array("Anzeigen" => 'building_show');

define('POST_INSERT_UPDATE_METHOD', 'Ranking::calcBuildingPoints');

// FIELDS

// Description:

// name	 											// DB Field Name
// text												// Field Description
// type												// Field Type: text, password, textarea, timestamp, radio, select, checkbox, email, url, numeric
// def_val										// Default Value
// size 											// Field length (text, password, date, email, url)
// maxlen 										// Max Text length (text, password, date, email, url)
// rows 											// Rows (textarea)
// cols												// Cols (textarea)
// rcb_elem (Array)						// Checkbox-/Radio Elements (desc=>value)
// rcb_elem_chekced						// Value of default checked Checkbox-/Radio Element (Checkbox: has to be an array)
// select_elem (Array)				// Select Elements (desc=>value)
// select_elem_checked				// Value of default checked Select Element (desc=>value)
// show_overview							// Set 1 to show on overview page

$db_fields = array(
    array(
        "name" => "building_id",
        "text" => "ID",
        "type" => "readonly",
        "show_overview" => 1
    ),
    array(
        "name" => "building_name",
        "text" => "Name",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 1,
        "link_in_overview" => 1
    ),
    array(
        "name" => "building_type_id",
        "text" => "Kategorie",
        "type" => "select",
        "def_val" => "",
        "size" => "",
        "maxlen" => "",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => admin_get_select_elements('building_types', "type_id", "type_name", "type_name"),
        "select_elem_checked" => "",
        "show_overview" => 1
    ),
    array(
        "name" => "building_shortcomment",
        "text" => "Kurzbeschrieb",
        "type" => "textarea",
        "def_val" => "",
        "size" => "",
        "maxlen" => "",
        "rows" => "7",
        "cols" => "35",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_longcomment",
        "text" => "Beschreibung",
        "type" => "textarea",
        "def_val" => "",
        "size" => "",
        "maxlen" => "",
        "rows" => "9",
        "cols" => "35",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0,
        "line" => 1
    ),
    array(
        "name" => "building_costs_metal",
        "text" => "Kosten Metall",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_costs_crystal",
        "text" => "Kosten Kristall",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_costs_plastic",
        "text" => "Kosten Plastik",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_costs_fuel",
        "text" => "Kosten Treibstoff",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_costs_food",
        "text" => "Kosten Nahrung",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_costs_power",
        "text" => "Kosten Energie",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_build_costs_factor",
        "text" => "Kostenfaktor Bau",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_demolish_costs_factor",
        "text" => "Kostenfaktor Abbruch",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0,
        "columnend" => 1
    ),

    array(
        "name" => "building_power_use",
        "text" => "Stromverbrauch",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_fuel_use",
        "text" => "Tritiumverbrauch",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_power_req",
        "text" => "Strombedarf (wird nicht verbraucht)",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0,
        "line" => 1
    ),

    array(
        "name" => "building_prod_metal",
        "text" => "Produktion Metall",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_prod_crystal",
        "text" => "Produktion Kristall",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_prod_plastic",
        "text" => "Produktion Plastik",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_prod_fuel",
        "text" => "Produktion Treibstoff",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_prod_food",
        "text" => "Produktion Nahrung",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_prod_power",
        "text" => "Produktion Strom",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_production_factor",
        "text" => "Produktionsfaktor",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0,
        "line" => 1
    ),

    array(
        "name" => "building_store_metal",
        "text" => "Speicher Metall",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_store_crystal",
        "text" => "Speicher Kristall",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_store_plastic",
        "text" => "Speicher Plastik",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_store_fuel",
        "text" => "Speicher Treibstoff",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_store_food",
        "text" => "Speicher Nahrung",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_store_factor",
        "text" => "Speicherfaktor",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0,
        "line" => 1
    ),

    array(
        "name" => "building_last_level",
        "text" => "Max Level",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 1
    ),
    array(
        "name" => "building_fields",
        "text" => "Felderverbrauch",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_people_place",
        "text" => "Bewohnbare Fläche",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_fieldsprovide",
        "text" => "Zur Verfügung gestellte Felder",
        "type" => "text",
        "def_val" => "",
        "size" => "3",
        "maxlen" => "4",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_bunker_res",
        "text" => "Ressourcen-Grundkapazität Bunker",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_bunker_fleet_count",
        "text" => "Schiffszahl-Grundkapazität Bunker",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    array(
        "name" => "building_bunker_fleet_space",
        "text" => "Schiffsstruktur-Grundkapazität Bunker",
        "type" => "text",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    )
);
