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
	// 	Dateiname: races.php	
	// 	Topic: Formular-Definitionen für Rassen 
	// 	Autor: Nicolas Perrenoud alias MrCage							
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar: 	
	//
	
	// VARIABLES
	
	define("MODUL_NAME","Rassen");				
	define("DB_TABLE", $db_table['races']);
	define("DB_TABLE_ID", "race_id");
	define("DB_OVERVIEW_ORDER_FIELD","race_name");
	
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
  
	$db_fields = array ( 0	=> 	array	(	"name" => "race_name",
																		"text" => "Rasse",
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
											1	=> 	array	(	"name" => "race_comment",
																		"text" => "Kommentar",
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
																		"show_overview" => 1,
																		"overview_length" => 400
																	),		
											2	=> 	array	(	"name" => "race_leadertitle",
																		"text" => "Leader-Titel",
																		"type" => "text",
																		"def_val" => "",
																		"size" => "30",
																		"maxlen" => "30",
																		"rows" => "0",
																		"cols" => "0",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 1,
																		"overview_length" => 200
																	),															
												3	=> 	array	(	"name" => "race_f_metal",
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
												4	=> 	array	(	"name" => "race_f_crystal",
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
												5	=> 	array	(	"name" => "race_f_plastic",
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
												6	=> 	array	(	"name" => "race_f_fuel",
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
												7	=> 	array	(	"name" => "race_f_food",
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
												8	=> 	array	(	"name" => "race_f_power",
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
												9	=> 	array	(	"name" => "race_f_population",
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
												10	=> 	array	(	"name" => "race_f_researchtime",
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
												11	=> 	array	(	"name" => "race_f_buildtime",
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
																	),																														
												12	=> 	array	(	"name" => "race_f_fleettime",
																		"text" => "Flugzeitfaktor",
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
