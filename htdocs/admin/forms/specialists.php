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
	// 	Topic: Formular-Definitionen für Technologien 
	// 	Autor: Nicolas Perrenoud alias MrCage							
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar: 	
	//
	
	// VARIABLES
	
	define("MODUL_NAME","Spezialisten");				
	define("DB_TABLE", 'specialists');
	define("DB_TABLE_ID", "specialist_id");
	define("DB_OVERVIEW_ORDER_FIELD","specialist_name");

	$form_switches = array("Anzeigen"=>'specialist_enabled');
	
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
  
	$db_fields = array (  array	(	"name" => "specialist_id",
																		"text" => "ID",
																		"type" => "readonly",
																		"show_overview" => 1
																	), 	
												array	(	"name" => "specialist_name",
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
											array	(	"name" => "specialist_desc",
																		"text" => "Beschreibung",
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
												array	(	"name" => "specialist_points_req",
																		"text" => "Punkteminimum",
																		"type" => "text",
																		"def_val" => "100000",
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
												array	(	"name" => "specialist_days",
																		"text" => "Anstellungsdauer (Tage)",
																		"type" => "text",
																		"def_val" => "7",
																		"size" => "2",
																		"maxlen" => "3",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 1,
																		"line" => 1
																	),	
																																		
											array	(	"name" => "specialist_costs_metal",
																		"text" => "Kosten Metall",
																		"type" => "text",
																		"def_val" => "100000",
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
											array	(	"name" => "specialist_costs_crystal",
																		"text" => "Kosten Kristall",
																		"type" => "text",
																		"def_val" => "100000",
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
											array	(	"name" => "specialist_costs_plastic",
																		"text" => "Kosten Plastik",
																		"type" => "text",
																		"def_val" => "100000",
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
											array	(	"name" => "specialist_costs_fuel",
																		"text" => "Kosten Treibstoff",
																		"type" => "text",
																		"def_val" => "100000",
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
											array	(	"name" => "specialist_costs_food",
																		"text" => "Kosten Nahrung",
																		"type" => "text",
																		"def_val" => "100000",
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

												array	(	"name" => "specialist_prod_metal",
																		"text" => "Metallproduktion",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),																		
												array	(	"name" => "specialist_prod_crystal",
																		"text" => "Kristallproduktion",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),		
												array	(	"name" => "specialist_prod_plastic",
																		"text" => "Plastikproduktion",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),		
												array	(	"name" => "specialist_prod_fuel",
																		"text" => "Treibstoffproduktion",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),		
												array	(	"name" => "specialist_prod_food",
																		"text" => "Nahrungsproduktion",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),		
												array	(	"name" => "specialist_power",
																		"text" => "Energieproduktion",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),		
												array	(	"name" => "specialist_population",
																		"text" => "Bevölkerungswachstum",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),		
												array	(	"name" => "specialist_time_tech",
																		"text" => "Forschungszeit",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),		
												array	(	"name" => "specialist_time_buildings",
																		"text" => "Gebäudebauzeit",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),																		
												array	(	"name" => "specialist_time_defense",
																		"text" => "Verteidigungsbauzeit",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),																		
												array	(	"name" => "specialist_time_ships",
																		"text" => "Schiffbauzeit",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),																		
												array	(	"name" => "specialist_costs_defense",
																		"text" => "Verteidigungskosten",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),																		
												array	(	"name" => "specialist_costs_ships",
																		"text" => "Schiffkosten",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),																		
												array	(	"name" => "specialist_costs_tech",
																		"text" => "Forschungskosten",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),																		
												array	(	"name" => "specialist_fleet_speed",
																		"text" => "Flottengeschwindigkeit",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),																																		
												array	(	"name" => "specialist_fleet_max",
																		"text" => "Zusätzliche Flotten",
																		"type" => "text",
																		"def_val" => "0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),
												array	(	"name" => "specialist_def_repair",
																		"text" => "Verteidigungsreparatur",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),	
												array	(	"name" => "specialist_spy_level",
																		"text" => "Zusätzlicher Spionagelevel",
																		"type" => "text",
																		"def_val" => "0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),
												array	(	"name" => "specialist_tarn_level",
																		"text" => "Zusätzlicher Tarnlevel",
																		"type" => "text",
																		"def_val" => "0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),																																		
												array	(	"name" => "specialist_trade_time",
																		"text" => "Geschwindigkeit der Handelsflotten",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 0
																	),																																		
												array	(	"name" => "specialist_trade_bonus",
																		"text" => "Handelskosten",
																		"type" => "text",
																		"def_val" => "1.0",
																		"size" => "4",
																		"maxlen" => "7",
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
