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
	// 	Dateiname: config.php
	// 	Topic: Formular-Definitionen für Konfiguration
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	// VARIABLES

	define("MODUL_NAME","Config");
	define("DB_TABLE", 'config');
	define("DB_TABLE_ID", "config_id");
	define("DB_OVERVIEW_ORDER_FIELD","config_name");

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
	// select_elem_checked				// Value of default checked Select Element (text=>value)
	// show_overview							// Set 1 to show on overview page

	$db_fields = array ( 0	=> 	array	(	"name" => "config_name",
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
																		"show_overview" => 1
																	),
											1	=> 	array	(	"name" => "config_value",
																		"text" => "Wert",
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
											2	=> 	array	(	"name" => "config_param1",
																		"text" => "Parameter 1",
																		"type" => "text",
																		"def_val" => "",
																		"size" => "10",
																		"maxlen" => "250",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 1
																	),
											3	=> 	array	(	"name" => "config_param2",
																		"text" => "Parameter 2",
																		"type" => "text",
																		"def_val" => "",
																		"size" => "10",
																		"maxlen" => "250",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 1
																	)
											);

?>
