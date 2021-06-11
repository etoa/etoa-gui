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
	// 	Dateiname: buildings.php	
	// 	Topic: Formular-Definitionen für Gebäude 
	// 	Autor: Nicolas Perrenoud alias MrCage							
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar: 	
	//
	
	// VARIABLES
	
	define("MODUL_NAME","Allianzgebäude");				
	define("DB_TABLE", 'alliance_buildings');
	define("DB_TABLE_ID", "alliance_building_id");
	define("DB_OVERVIEW_ORDER_FIELD","alliance_building_id");

	define("DB_IMAGE_PATH",IMAGE_PATH."/abuildings/building<DB_TABLE_ID>_small.".IMAGE_EXT);

	$form_switches = array("Anzeigen"=>'alliance_building_show');

	
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
  
	$db_fields = array ( array	(	"name" => "alliance_building_id",
																		"text" => "ID",
																		"type" => "readonly",
																		"show_overview" => 1
																	),  
											array	(	"name" => "alliance_building_name",
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
											array	(	"name" => "alliance_building_shortcomment",
																		"text" => "Kurzbeschreibung",
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
																		"show_overview" => 1,
																		"line"=>0
																	),
											array	(	"name" => "alliance_building_longcomment",
																		"text" => "lange Beschreibung",
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
																		"show_overview" => 1,
																		"line"=>1
																	),
												array	(	"name" => "alliance_building_costs_metal",
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
												array	(	"name" => "alliance_building_costs_crystal",
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
											array	(	"name" => "alliance_building_costs_plastic",
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
											array	(	"name" => "alliance_building_costs_fuel",
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
											array	(	"name" => "alliance_building_costs_food",
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
											array	(	"name" => "alliance_building_build_time",
																		"text" => "Bauzeit (Sekunden)",
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
											array	(	"name" => "alliance_building_costs_factor",
																		"text" => "Kostenfaktor",
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
																		"line"=>1
																	),

																	
																																																																																																
												array	(	"name" => "alliance_building_last_level",
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
																	
											array	(	"name" => "alliance_building_needed_id",
																		"text" => "Voraussetzung",
																		"type" => "select",
																		"def_val" => "",
																		"size" => "",
																		"maxlen" => "",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => admin_get_select_elements('alliance_buildings',"alliance_building_id","alliance_building_name","alliance_building_id"),
																		"select_elem_checked" => "",
																		"show_overview" => 1
																	),																				
																	
												array	(	"name" => "alliance_building_needed_level",
																		"text" => "Voraussetzung Stufe",
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
																	
											);

        
?>
