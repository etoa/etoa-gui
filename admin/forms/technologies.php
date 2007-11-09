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
	// 	Dateiname: technologies.php	
	// 	Topic: Formular-Definitionen f�r Technologien 
	// 	Autor: Nicolas Perrenoud alias MrCage							
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar: 	
	//
	
	// VARIABLES
	
	define("MODUL_NAME","Forschung");				
	define("DB_TABLE", $db_table['technologies']);
	define("DB_TABLE_ID", "tech_id");
	define("DB_OVERVIEW_ORDER_FIELD","tech_type_id");
	
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
  
	$db_fields = array ( 0	=> 	array	(	"name" => "tech_name",
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
											1	=> 	array	(	"name" => "tech_type_id",
																		"text" => "Kategorie",
																		"type" => "select",
																		"def_val" => "",
																		"size" => "",
																		"maxlen" => "",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => admin_get_select_elements($db_table['tech_types'],"type_id","type_name","type_name"),
																		"select_elem_checked" => "",
																		"show_overview" => 1
																	),																	
											2	=> 	array	(	"name" => "tech_shortcomment",
																		"text" => "Titel",
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
											3	=> 	array	(	"name" => "tech_longcomment",
																		"text" => "Text",
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
																		"show_overview" => 0
																	),
											4	=> 	array	(	"name" => "tech_costs_metal",
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
											5	=> 	array	(	"name" => "tech_costs_crystal",
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
											6	=> 	array	(	"name" => "tech_costs_plastic",
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
											7	=> 	array	(	"name" => "tech_costs_fuel",
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
											8	=> 	array	(	"name" => "tech_costs_food",
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
											9	=> 	array	(	"name" => "tech_build_costs_factor",
																		"text" => "Kostenfaktor Bau",
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
																																																																																			
											10	=> 	array	(	"name" => "tech_last_level",
																		"text" => "Maximaler Level",
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
											11	=> 	array	(	"name" => "tech_order",
																		"text" => "Reihenfolge",
																		"type" => "text",
																		"def_val" => "",
																		"size" => "1",
																		"maxlen" => "2",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),	
											12	=> 	array	(	"name" => "tech_show",
																		"text" => "Anzeigen",
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
											13	=> 	array	(	"name" => "tech_stealable",
																		"text" => "Stehlbar",
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
																	)																	
											);
        
?>