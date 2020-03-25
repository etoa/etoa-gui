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
	// 	Dateiname: ships.php
	// 	Topic: Formular-Definitionen für Schiffe
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	// VARIABLES

	define("MODUL_NAME","Schiffe");
	define("DB_TABLE", 'ships');
	define("DB_TABLE_ID", "ship_id");
	define("DB_OVERVIEW_ORDER_FIELD","ship_cat_id ASC, ship_order, ship_name");
	define("DB_OVERVIEW_ORDER","ASC");

	define("DB_IMAGE_PATH",IMAGE_PATH."/ships/ship<DB_TABLE_ID>_small.".IMAGE_EXT);

	define("DB_TABLE_SORT",'ship_order');
	define("DB_TABLE_SORT_PARENT",'ship_cat_id');

	define('POST_INSERT_UPDATE_METHOD','Ranking::calcShipPoints');
	
	$form_switches = array("Anzeigen"=>'ship_show','Baubar'=>'ship_buildable','Startbar'=>'ship_launchable');
	
	// FIELDS

	// Description:

	// name					// DB Field Name
	// text					// Field Description
	// type					// Field Type: text, password, textarea, timestamp, radio, select, checkbox, email, url, numeric
	// def_val				// Default Value
	// size 				// Field length (text, password, date, email, url)
	// maxlen 				// Max Text length (text, password, date, email, url)
	// rows 				// Rows (textarea)
	// cols					// Cols (textarea)
	// rcb_elem (Array)			// Checkbox-/Radio Elements (desc=>value)
	// rcb_elem_chekced			// Value of default checked Checkbox-/Radio Element (Checkbox: has to be an array)
	// select_elem (Array)			// Select Elements (desc=>value)
	// select_elem_checked			// Value of default checked Select Element (desc=>value)
	// show_overview			// Set 1 to show on overview page
        // show_hide                            // Array of columns to show when radio is True
        // hide_show                            // Array of columns to show when radio is False

$db_fields = array(
	/** ship_id */
	array(
		"name" => "ship_id",
		"text" => "ID",
		"type" => "readonly",
		"show_overview" => 1
	),
	/** ship_name */
	array(
		"name" => "ship_name",
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
	/** ship_shortcomment */
	array(
		"name" => "ship_shortcomment",
		"text" => "Kurzbeschreibung",
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
	/** ship_longcomment */
	array(
		"name" => "ship_longcomment",
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
	/** ship_cat_id */
	array(
		"name" => "ship_cat_id",
		"text" => "Kategorie",
		"type" => "select",
		"def_val" => "",
		"size" => "20",
		"maxlen" => "250",
		"rows" => "",
		"cols" => "",
		"rcb_elem" => "",
		"rcb_elem_chekced" => "",
		"select_elem" => admin_get_select_elements('ship_cat',"cat_id","cat_name","cat_name",array("0"=>"-")),
		"select_elem_checked" => "",
		"show_overview" => 1
	),
	/** ship_race_id */
	array(
		"name" => "ship_race_id",
		"text" => "Rasse",
		"type" => "select",
		"def_val" => "",
		"select_elem" => admin_get_select_elements('races',"race_id","race_name","race_name",array("0"=>"-")),
		"select_elem_checked" => "",
		"show_overview" => 1,
		"line" => 1
	),
	/** ship_costs_metal */
	array(
		"name" => "ship_costs_metal",
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
	/** ship_costs_crystal */
	array(
		"name" => "ship_costs_crystal",
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
	/** ship_costs_plastic */
	array(
		"name" => "ship_costs_plastic",
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
	/** ship_costs_fuel */
	array(
		"name" => "ship_costs_fuel",
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
	/** ship_costs_food */
	array(
		"name" => "ship_costs_food",
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
	/** ship_points */
	array(
		"name" => "ship_points",
		"text" => "Punkte",
		"type" => "readonly",
		"show_overview" => 0,
		"line" => 1
	),
	/** ship_fuel_use */
	array(
		"name" => "ship_fuel_use",
		"text" => "Treibstoffverbrauch",
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
	/** ship_fuel_use_launch */
	array(
		"name" => "ship_fuel_use_launch",
		"text" => "Treibstoff Start",
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
	/** ship_fuel_use_landing */
	array(
		"name" => "ship_fuel_use_landing",
		"text" => "Treibstoff Landung",
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
	/** ship_capacity */
	array(
		"name" => "ship_capacity",
		"text" => "Laderaum",
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
	/** ship_people_capacity */
	array(
		"name" => "ship_people_capacity",
		"text" => "Passagierraum",
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
	/** ship_pilots */
	array(
		"name" => "ship_pilots",
		"text" => "Piloten",
		"type" => "text",
		"def_val" => "1",
		"size" => "2",
		"maxlen" => "3",
		"rows" => "",
		"cols" => "",
		"rcb_elem" => "",
		"rcb_elem_chekced" => "",
		"select_elem" => "",
		"select_elem_checked" => "",
		"show_overview" => 0
	),
	/** ship_bounty_bonus */
	array(
		"name" => "ship_bounty_bonus",
		"text" => "max Beute",
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
	/** ship_speed */
	array(
		"name" => "ship_speed",
		"text" => "Geschwindigkeit",
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
	/** ship_time2start */
	array(
		"name" => "ship_time2start",
		"text" => "Startzeit",
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
	/** ship_time2land */
	array(
		"name" => "ship_time2land",
		"text" => "Landezeit",
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
	/** ship_structure */
	array(
		"name" => "ship_structure",
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
	/** ship_shield */
	array(
		"name" => "ship_shield",
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
	/** ship_weapon */
	array(
		"name" => "ship_weapon",
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
	/** ship_heal */
	array(
		"name" => "ship_heal",
		"text" => "Heilung pro Runde",
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
		"line"=> 1
	),
	/** ship_max_count */
	array(
		"name" => "ship_max_count",
		"text" => "Max. Anzahl (0=unentlich)",
		"type" => "text",
		"def_val" => "",
		"size" => "7",
		"maxlen" => "250",
		"rows" => "",
		"cols" => "",
		"rcb_elem" => "",
		"rcb_elem_chekced" => "",
		"select_elem" => "",
		"select_elem_checked" => "",
		"show_overview" => 0
	),
	/** ship_fieldsprovide */
	array(
		"name" => "ship_fieldsprovide",
		"text" => "Zur Verfüg. gest. Felder",
		"type" => "text",
		"def_val" => "0",
		"size" => "1",
		"maxlen" => "3",
		"rows" => "",
		"cols" => "",
		"rcb_elem" => "",
		"rcb_elem_chekced" => "",
		"select_elem" => "",
		"select_elem_checked" => "",
		"show_overview" => 0
	),
	/** ship_fakeable */
	array(
		"name" => "ship_fakeable",
		"text" => "Verwenden bei Täuschangriff",
		"type" => "radio",
		"def_val" => "",
		"size" => "",
		"maxlen" => "",
		"rows" => "",
		"cols" => "",
		"rcb_elem" => array("Ja"=>1,"Nein"=>0),
		"rcb_elem_chekced" => "0",
		"select_elem" => "",
		"select_elem_checked" => "",
		"show_overview" => 0,
		"columnend" => 1
	),
	/** ship_actions */
	array(
		"name" => "ship_actions",
		"text" => "Aktionen",
		"type" => "fleetaction",
		"def_val" => "",
		"size" => "20",
		"maxlen" => "250",
		"rows" => "2",
		"cols" => "60",
		"rcb_elem" => "",
		"rcb_elem_chekced" => "",
		"select_elem" => "",
		"select_elem_checked" => "",
		"show_overview" => 0,
		"line"=> 1
	),
	/** ship_alliance_shipyard_level */
	array(
		"name" => "ship_alliance_shipyard_level",
		"text" => "Allianzschiff: Ben&ouml;tigte Werftstufe",
		"type" => "text",
		"def_val" => "",
		"size" => "2",
		"maxlen" => "3",
		"rows" => "",
		"cols" => "",
		"rcb_elem" => "",
		"rcb_elem_chekced" => "",
		"select_elem" => "",
		"select_elem_checked" => "",
		"show_overview" => 0,
	),
	/** ship_alliance_costs */
	array(
		"name" => "ship_alliance_costs",
		"text" => "Allianzschiff: Kosten (Schiffsteile)",
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
		"line"=> 1
	),
	/** special_ship */
	array(
		"name" => "special_ship",
		"text" => "Spezial Schiff",
		"type" => "radio",
		"def_val" => "",
		"size" => "",
		"maxlen" => "",
		"rows" => "",
		"cols" => "",
		"rcb_elem" => array("Ja"=>1,"Nein"=>0),
		"rcb_elem_chekced" => "0",
		"select_elem" => "",
		"select_elem_checked" => "",
		"show_overview" => 0,
		"show_hide" => array("special_ship_max_level","special_ship_need_exp","special_ship_exp_factor","special_ship_bonus_weapon","special_ship_bonus_structure","special_ship_bonus_shield","special_ship_bonus_heal","special_ship_bonus_capacity","special_ship_bonus_speed","special_ship_bonus_pilots","special_ship_bonus_tarn","special_ship_bonus_antrax","special_ship_bonus_forsteal","special_ship_bonus_build_destroy","special_ship_bonus_antrax_food","special_ship_bonus_deactivade","special_ship_bonus_readiness"),
		"hide_show" => array("ship_tradable")
	),
	/** special_ship_max_level */
	array(
		"name" => "special_ship_max_level",
		"text" => "Max. Level (0=unentlich)",
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
	/** special_ship_need_exp */
	array(
		"name" => "special_ship_need_exp",
		"text" => "EXP",
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
	/** special_ship_exp_factor */
	array(
		"name" => "special_ship_exp_factor",
		"text" => "EXP Faktor",
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
	/** special_ship_bonus_weapon */
	array(
		"name" => "special_ship_bonus_weapon",
		"text" => "Waffen-Bonus (0.1=10% pro Stufe)",
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
	/** special_ship_bonus_structure */
	array(
		"name" => "special_ship_bonus_structure",
		"text" => "Struktur-Bonus",
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
	/** special_ship_bonus_shield */
	array(
		"name" => "special_ship_bonus_shield",
		"text" => "Schild-Bonus",
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
	/** special_ship_bonus_heal */
	array(
		"name" => "special_ship_bonus_heal",
		"text" => "Heil-Bonus",
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
	/** special_ship_bonus_capacity */
	array(
		"name" => "special_ship_bonus_capacity",
		"text" => "Kapazität-Bonus",
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
	/** special_ship_bonus_speed */
	array(
		"name" => "special_ship_bonus_speed",
		"text" => "Speed-Bonus",
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
	/** special_ship_bonus_pilots */
	array(
		"name" => "special_ship_bonus_pilots",
		"text" => "Piloten-Bonus",
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
	/** special_ship_bonus_tarn */
	array(
		"name" => "special_ship_bonus_tarn",
		"text" => "Tarn-Bonus",
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
	/** special_ship_bonus_antrax */
	array(
		"name" => "special_ship_bonus_antrax",
		"text" => "Giftgas-Bonus",
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
	/** special_ship_bonus_forsteal */
	array(
		"name" => "special_ship_bonus_forsteal",
		"text" => "Techklau-Bonus",
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
	/** special_ship_bonus_build_destroy */
	array(
		"name" => "special_ship_bonus_build_destroy",
		"text" => "Bombardier-Bonus",
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
	/** special_ship_bonus_antrax_food */
	array(
		"name" => "special_ship_bonus_antrax_food",
		"text" => "Antrax-Bonus",
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
	/** special_ship_bonus_deactivade */
	array(
		"name" => "special_ship_bonus_deactivade",
		"text" => "Deaktivier-Bonus",
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
	/** special_ship_bonus_readiness */
	array(
		"name" => "special_ship_bonus_readiness",
		"text" => "Bereitschafts-Bonus (Start/Landung)",
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
	/** ship_tradable */
	array(
		"name" => "ship_tradable",
		"text" => "Handelbar",
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
);
?>
