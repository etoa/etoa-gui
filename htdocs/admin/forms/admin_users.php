<?PHP

// VARIABLES

define("MODUL_NAME", "Admin Benutzer");
define("DB_TABLE", 'admin_users');
define("DB_TABLE_ID", "user_id");
define("DB_OVERVIEW_ORDER_FIELD", "user_nick");

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
        "name" => "user_name",
        "text" => "Name",
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
        "show_overview" => 0
    ),
    1    =>     array(
        "name" => "user_nick",
        "text" => "Nickname",
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
    2    =>     array(
        "name" => "user_email",
        "text" => "E-Mail",
        "type" => "email",
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
    3    =>     array(
        "name" => "user_password",
        "text" => "Passwort",
        "type" => "password",
        "def_val" => "",
        "size" => "30",
        "maxlen" => "250",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => "",
        "rcb_elem_chekced" => "",
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 0
    ),
    5    =>     array(
        "name" => "user_locked",
        "text" => "Gesperrt",
        "type" => "radio",
        "def_val" => "",
        "size" => "",
        "maxlen" => "",
        "rows" => "",
        "cols" => "",
        "rcb_elem" => array("Ja" => 1, "Nein" => 0),
        "rcb_elem_chekced" => 0,
        "select_elem" => "",
        "select_elem_checked" => "",
        "show_overview" => 1
    ),
    6    =>     array(
        "name" => "user_board_url",
        "text" => "Forum-Profil",
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
        "show_overview" => 0
    )
);
