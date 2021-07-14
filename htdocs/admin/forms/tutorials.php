<?PHP

// VARIABLES

define("MODUL_NAME", "Tutorial Texte");
define("DB_TABLE", 'tutorial_texts');
define("DB_TABLE_ID", "text_id");
define("DB_OVERVIEW_ORDER_FIELD", "text_tutorial_id, text_step");
define("DB_OVERVIEW_ORDER", "ASC");

define("DB_TABLE_SORT", 'text_step');
define("DB_TABLE_SORT_PARENT", 'text_tutorial_id');

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
        "name" => "text_id",
        "text" => "ID",
        "type" => "readonly",
        "show_overview" => 1
    ),
    array(
        "name" => "text_tutorial_id",
        "text" => "Tutorial",
        "type" => "select",
        "def_val" => "",
        "size" => "20",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => admin_get_select_elements('tutorial', "tutorial_id", "tutorial_title", "tutorial_title", array("0" => "-")),
        "select_elem_checked" => "",
        "show_overview" => 1
    ),
    array(
        "name" => "text_title",
        "text" => "Titel",
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
        "name" => "text_content",
        "text" => "Inhalt",
        "type" => "textarea",
        "def_val" => "",
        "size" => "",
        "maxlen" => "",
        "rows" => "15",
        "cols" => "150",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    )
);
