<?PHP
	//////////////////////////////////////////////////////
	//								 											    				//
	//  Website-Admin Script								  			  	//
	//  (C)2004 by Nicolas Perrenoud, www.dysign.ch			//
	//  Version 0.1	 																		//
	//							 											    					//
	//  Variables																				//
	//							 											    					//
	//////////////////////////////////////////////////////
	
	// VARIABLES
	
	define("MODUL_NAME","Schiff-Kategorien");				
	define("DB_TABLE", 'ship_cat');
	define("DB_TABLE_ID", "cat_id");
	define("DB_OVERVIEW_ORDER_FIELD","cat_order");
	
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
  
	$db_fields = array ( 0	=> 	array	(	"name" => "cat_id",
																		"text" => "ID",
																		"type" => "text",
																		"def_val" => "",
																		"size" => "1",
																		"maxlen" => "0",
																		"rows" => "0",
																		"cols" => "0",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 1
																	),
												1	=> 	array	(	"name" => "cat_name",
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
												2	=> 	array	(	"name" => "cat_order",
																		"text" => "Sortierung",
																		"type" => "numeric",
																		"def_val" => "1",
																		"size" => "1",
																		"maxlen" => "2",
																		"rows" => "",
																		"cols" => "",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 1
																	)	,
												3	=> 	array	(	"name" => "cat_color",
																		"text" => "Farbe",
																		"type" => "text",
																		"def_val" => "#ffffff",
																		"size" => "7",
																		"maxlen" => "7",
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