<?PHP

// VARIABLES

define("MODUL_NAME", "Objekt-Transformationen");
define("DB_TABLE", 'obj_transforms');
define("DB_TABLE_ID", "id");
define("DB_OVERVIEW_ORDER_FIELD", "id");

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
        "name" => "def_id",
        "text" => "Verteidigung",
        "type" => "select",
        "def_val" => "",
        "size" => "",
        "maxlen" => "",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => admin_get_select_elements('defense', "def_id", "def_name", "def_order"),
        "select_elem_checked" => "",
        "show_overview" => 1
    ),
    array(
        "name" => "ship_id",
        "text" => "Schiff",
        "type" => "select",
        "def_val" => "",
        "size" => "",
        "maxlen" => "",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => admin_get_select_elements('ships', "ship_id", "ship_name", "ship_order"),
        "select_elem_checked" => "",
        "show_overview" => 1
    ),
    array(
        "name" => "costs_metal",
        "text" => "Kosten Metall",
        "type" => "text",
        "def_val" => "0",
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
        "name" => "costs_crystal",
        "text" => "Kosten Kristall",
        "type" => "text",
        "def_val" => "0",
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
        "name" => "costs_plastic",
        "text" => "Kosten Plastik",
        "type" => "text",
        "def_val" => "0",
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
        "name" => "costs_fuel",
        "text" => "Kosten Treibstoff",
        "type" => "text",
        "def_val" => "0",
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
        "name" => "costs_food",
        "text" => "Kosten Nahrung",
        "type" => "text",
        "def_val" => "0",
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
        "name" => "costs_factor_sd",
        "text" => "Kostenfaktor S -> V",
        "type" => "text",
        "def_val" => "0",
        "size" => "5",
        "maxlen" => "5",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 1
    ),
    array(
        "name" => "costs_factor_ds",
        "text" => "Kostenfaktor V -> S",
        "type" => "text",
        "def_val" => "1",
        "size" => "5",
        "maxlen" => "5",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 1
    ),
    array(
        "name" => "num_def",
        "text" => "Anzahl V pro S",
        "type" => "text",
        "def_val" => "1",
        "size" => "5",
        "maxlen" => "5",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 1
    ),
);
