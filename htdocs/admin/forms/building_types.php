<?PHP

// VARIABLES

define("MODUL_NAME", "Gebäudekategorien");
define("DB_TABLE", 'building_types');
define("DB_TABLE_ID", "type_id");
define("DB_OVERVIEW_ORDER_FIELD", "type_order");

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
    0    =>     array(
        "name" => "type_name",
        "text" => "Kategoriename",
        "type" => "text",
        "def_val" => "",
        "size" => "30",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 1
    ),
    1    =>     array(
        "name" => "type_order",
        "text" => "Sortierung",
        "type" => "numeric",
        "def_val" => "1",
        "size" => "1",
        "maxlen" => "2",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 1
    )
);
