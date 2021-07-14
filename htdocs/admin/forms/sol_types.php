<?PHP

// VARIABLES

define("MODUL_NAME", "Sonnentypen");
define("DB_TABLE", 'sol_types');
define("DB_TABLE_ID", "sol_type_id");
define("DB_OVERVIEW_ORDER_FIELD", "sol_type_name");

$form_switches = array("Standardtyp" => 'sol_type_consider');


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
        "name" => "sol_type_name",
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
        "show_overview" => 1,
        "link_in_overview" => 1
    ),
    1    =>     array(
        "name" => "sol_type_comment",
        "text" => "Kommentar",
        "type" => "textarea",
        "def_val" => "",
        "size" => "",
        "maxlen" => "",
        "rows" => "5",
        "cols" => "25",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 1
    ),
    3    =>     array(
        "name" => "sol_type_f_metal",
        "text" => "Metallfaktor",
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
        "show_overview" => 0
    ),
    4    =>     array(
        "name" => "sol_type_f_crystal",
        "text" => "Kristallfaktor",
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
        "show_overview" => 0
    ),
    5    =>     array(
        "name" => "sol_type_f_plastic",
        "text" => "Plastikfaktor",
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
        "show_overview" => 0
    ),
    6    =>     array(
        "name" => "sol_type_f_fuel",
        "text" => "Treibstofffaktor",
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
        "show_overview" => 0
    ),
    7    =>     array(
        "name" => "sol_type_f_food",
        "text" => "Nahrungsfaktor",
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
        "show_overview" => 0
    ),
    8    =>     array(
        "name" => "sol_type_f_power",
        "text" => "Stromfaktor",
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
        "show_overview" => 0
    ),
    9    =>     array(
        "name" => "sol_type_f_population",
        "text" => "Bevölkerungsfaktor",
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
        "show_overview" => 0
    ),
    10    =>     array(
        "name" => "sol_type_f_researchtime",
        "text" => "Forschungszeitfaktor",
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
        "show_overview" => 0
    ),
    11    =>     array(
        "name" => "sol_type_f_buildtime",
        "text" => "Bauzeitfaktor",
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
        "show_overview" => 0
    )
);
