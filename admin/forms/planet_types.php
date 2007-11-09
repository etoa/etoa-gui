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
	// 	Dateiname: planet_types.php	
	// 	Topic: Formular-Definitionen für Planetentypen
	// 	Autor: Nicolas Perrenoud alias MrCage							
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar: 	
	//
	
	// VARIABLES
	
	define("MODUL_NAME","Planetentypen");				
	define("DB_TABLE", $db_table['planet_types']);
	define("DB_TABLE_ID", "type_id");
	define("DB_OVERVIEW_ORDER_FIELD","type_name");
	
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
  
	$db_fields = array ( 0	=> 	array	(	"name" => "type_name",
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
											1	=> 	array	(	"name" => "type_comment",
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
											2	=> 	array	(	"name" => "type_habitable",
																		"text" => "Bewohnbar",
																		"type" => "radio",
																		"def_val" => "",
																		"size" => "",
																		"maxlen" => "",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => array("Ja"=>1,"Nein"=>0),
																		"rcb_elem_chekced" => "1",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),																	
												3	=> 	array	(	"name" => "type_f_metal",
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
												4	=> 	array	(	"name" => "type_f_crystal",
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
												5	=> 	array	(	"name" => "type_f_plastic",
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
												6	=> 	array	(	"name" => "type_f_fuel",
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
												7	=> 	array	(	"name" => "type_f_food",
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
												8	=> 	array	(	"name" => "type_f_power",
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
												9	=> 	array	(	"name" => "type_f_population",
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
												10	=> 	array	(	"name" => "type_f_researchtime",
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
												11	=> 	array	(	"name" => "type_f_buildtime",
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
        
?>