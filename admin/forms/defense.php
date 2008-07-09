<?PHP

	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	Dateiname: defense.php
	// 	Topic: Formular-Definitionen für Verteidigung
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	// VARIABLES

	define("MODUL_NAME","Verteidigung");
	define("DB_TABLE", 'defense');
	define("DB_TABLE_ID", "def_id");
	define("DB_OVERVIEW_ORDER_FIELD","def_cat_id,def_order, def_name");

	define("DB_IMAGE_PATH",IMAGE_PATH."/defense/def<DB_TABLE_ID>_small.".IMAGE_EXT);

	define("DB_TABLE_SORT",'def_order');
	define("DB_TABLE_SORT_PARENT",'def_cat_id');

	define('POST_INSERT_UPDATE_METHOD','calcDefensePoints');

	$form_switches = array("Anzeigen"=>'def_show','Baubar'=>'def_buildable');


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

	$db_fields = array (	array	(	"name" => "def_id",
																		"text" => "ID",
																		"type" => "readonly",
																		"show_overview" => 1
																	),  
												array	(	"name" => "def_name",
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

											array	(	"name" => "def_shortcomment",
																		"text" => "Kurze Beschreibung",
																		"type" => "textarea",
																		"def_val" => "",
																		"size" => "",
																		"maxlen" => "",
																		"rows" => "5",
																		"cols" => "50",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),
											array	(	"name" => "def_longcomment",
																		"text" => "Lange Beschreibung",
																		"type" => "textarea",
																		"def_val" => "",
																		"size" => "",
																		"maxlen" => "",
																		"rows" => "7",
																		"cols" => "50",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0,
																		"line" => 1
																	),
											array	(	"name" => "def_costs_metal",
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
											array	(	"name" => "def_costs_crystal",
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
											array	(	"name" => "def_costs_plastic",
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
											array	(	"name" => "def_costs_fuel",
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
											array	(	"name" => "def_costs_food",
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
											array	(	"name" => "def_points",
																		"text" => "Punkte",
																		"type" => "readonly",
																		"show_overview" => 0,
																		"columnend" => 1
																	), 																	

											array	(	"name" => "def_structure",
																		"text" => "Struktur",
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
											array	(	"name" => "def_shield",
																		"text" => "Schild",
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
											array	(	"name" => "def_weapon",
																		"text" => "Waffe",
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
											array	(	"name" => "def_heal",
																		"text" => "Reparatur",
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
											array	(	"name" => "def_fields",
																		"text" => "Felder",
																		"type" => "text",
																		"def_val" => "",
																		"size" => "10",
																		"maxlen" => "255",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),
											array	(	"name" => "def_max_count",
																		"text" => "Max Anzahl",
																		"type" => "text",
																		"def_val" => "",
																		"size" => "10",
																		"maxlen" => "255",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),
											array	(	"name" => "def_cat_id",
																		"text" => "Kategorie",
																		"type" => "select",
																		"def_val" => "",
																		"size" => "20",
																		"maxlen" => "250",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => admin_get_select_elements($db_table['def_cat'],"cat_id","cat_name","cat_name",array("0"=>"(Keine)")),
																		"select_elem_checked" => "",
																		"show_overview" => 1
																	),																	
											array	(	"name" => "def_race_id",
																		"text" => "Rasse",
																		"type" => "select",
																		"def_val" => "",
																		"size" => "20",
																		"maxlen" => "250",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => admin_get_select_elements($db_table['races'],"race_id","race_name","race_name",array("0"=>"(Keine)")),
																		"select_elem_checked" => "",
																		"show_overview" => 1
																	)
											);

?>
