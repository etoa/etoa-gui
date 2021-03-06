<?PHP

// VARIABLES

define("MODUL_NAME", "Tipps");
define("DB_TABLE", "tips");
define("DB_TABLE_ID", "tip_id");
define("DB_OVERVIEW_ORDER_FIELD", "tip_text");

$form_switches = array("Anzeigen" => 'tip_active');

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
    1    =>     array(
        "name" => "tip_text",
        "text" => "Tipp",
        "type" => "textarea",
        "def_val" => "",
        "size" => "",
        "maxlen" => "",
        "rows" => "10",
        "cols" => "40",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 1
    )

);
