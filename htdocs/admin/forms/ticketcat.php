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
	// 	Topic: Formular-Definitionen f�r Rassen
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	// VARIABLES

	define("MODUL_NAME","Ticket-Kategorien");
	define("DB_TABLE", "ticket_cat");
	define("DB_TABLE_ID", "id");
	define("DB_TABLE_SORT",'sort');
	define("DB_OVERVIEW_ORDER_FIELD","sort");
	define("DB_OVERVIEW_ORDER","ASC");





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

	$db_fields = array (
											array	(	"name" => "name",
																		"text" => "Name",
																		"type" => "text",
																		"def_val" => "",
																		"size" => "40",
																		"maxlen" => "100",
																		"rows" => "10",
																		"cols" => "40",
																		"rcb_elem" => "",
																		"rcb_elem_chekced" => "",
																		"select_elem" => "",
																		"select_elem_checked" => "",
																		"show_overview" => 1
																	),
												array	(	"name" => "sort",
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

											);

?>
