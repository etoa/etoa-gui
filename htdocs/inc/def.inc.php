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
	// 	File: def.inc.php
	// 	Created: 07.5.2007
	// 	Last edited: 06.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Definition file, load definitions from database and assigns constants
	*
	* @author MrCage mrcage@etoa.ch
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/
	
	$cfg = Config::getInstance();
	
	/***********************************/
	/* Design, Layout, Allgmeine Pfade */
	/***********************************/
  
	//
	// Layout
	//
	
	// Statistik Anzahl Zeilen
	define ("STATS_NUM_OF_ROWS", $cfg->get('stats_num_rows'));

	/****************************/
	/* Allgemeine Einstellungen */
	/****************************/

	// Onlinetime-Threshold
	define('ONLINE_TIME', $cfg->get('online_threshold'));

	//
	// Paswort und Nicklänge
	//
	
	// Minimale Passwortlänge
	define("PASSWORD_MINLENGHT", $cfg->get('password_minlength'));
	
	// Minimale Passwortlänge
	define("PASSWORD_MAXLENGHT", $cfg->get('password_minlength'));
	
	// Minimale Nicklänge
	define("NICK_MINLENGHT", $cfg->p1('nick_length'));
	
	// Maximale Nicklänge
	define("NICK_MAXLENGHT", $cfg->p2('nick_length'));
	
	// Minimale Nicklänge
	define("NAME_MAXLENGTH", $cfg->name_length->v);
	
	//
	// Inaktive & Urlaubsmodus
	//
	
	// Minimale Umode-Dauer
  	define("MIN_UMOD_TIME", $cfg->hmode_days->v);
	
  //MAximale Umode-Dauer
    define("MAX_UMOD_TIME", $cfg->hmode_days->p1);
    
	// Vergangene Zeit bis Löschung eines Users (atm 21 Tage)
  	define("USER_INACTIVE_DELETE", $cfg->user_inactive_days->p1);
	
	// Vergangene Zeit bis Löschung falls nie eingeloggt & Zeit bis "Inaktiv" Status Long (atm 14 Tage)
  	define("USER_NOTLOGIN_DELETE", $cfg->user_inactive_days->p2);
	
	// Zeit bis "Inaktiv" Status (atm 7 Tage)
  	define("USER_INACTIVE_SHOW", $cfg->user_inactive_days->v);
	
	// UNIX-Time (last user action atm -7d)
	define("USER_INACTIVE_TIME", time() - (24 * 3600 * $cfg->user_inactive_days->v));
	
	// Zeit bis "Inaktiv" Status Long (atm 14 Tage)
  	define("USER_INACTIVE_LONG", $cfg->user_inactive_days->p2);
	
	// UNIX-Time (last user action long -14d)
	define("USER_INACTIVE_TIME_LONG", time() - (24 * 3600 * USER_INACTIVE_LONG));
	
	//
	// Universum
	//
	
	// Anzahl Zellen x
	define("CELL_NUM_X", $cfg->num_of_cells->p1);
	
	// Anzahl Zellen y
	define("CELL_NUM_Y", $cfg->num_of_cells->p2);
	
	// Wurmlöcher
	define("WH_UPDATE_AFFECT_TIME", $cfg->wh_update->v);
	define("WH_UPDATE_AFFECT_CNT", $cfg->wh_update->p1);
	
	// Nachrichten
	define("FLOOD_CONTROL", $cfg->msg_flood_control->v);	// Wartezeit bis zur nächsten Nachricht
	
	//
	// Punkteberechnung
	//
	
	// 1 Punkt für X (STATS_USER_POINTS) verbaute Rohstoffe
	define("STATS_USER_POINTS", $cfg->points_update->p1);
	
	// 1 Punkt für X (STATS_ALLIANCE_POINTS) User Punkte
	define("STATS_ALLIANCE_POINTS", $cfg->points_update->p2);

	//
	// Sonstiges
	//
	
	// Anzahl Nahrung, welche Arbeiter benötigen
	define("PEOPLE_FOOD_USE", $cfg->people_food_require->v);
	
	// Maximale Anzahl Planeten
	define("USER_MAX_PLANETS", $cfg->user_max_planets->v);
	
	//
	// Spezialiasten
	//
	
	// Minimal Punkte für Spezialist (VERALTET)
	define("SPECIALIST_MIN_POINTS_REQ", $cfg->p2('specialistconfig'));
	
	// Maximale Kostensteigerung
	define('SPECIALIST_MAX_COSTS_FACTOR', $cfg->p1('specialistconfig'));
	
	// Verfügbare Spezialisten pro Typ basierend auf Faktor * Anzahl User
	define('SPECIALIST_AVAILABILITY_FACTOR', $cfg->get('specialistconfig'));
	
	// Kriegsdauer
	define("WAR_DURATION", 3600 * $cfg->alliance_war_time->v);
	define("PEACE_DURATION", 3600 * $cfg->alliance_war_time->p1);

  /****************************************************/
  /* Startwerte (bei erstellung eines neuen Accounts) */
  /****************************************************/

	// Anzahl Titan
	define("USR_START_METAL", $cfg->user_start_metal->v);
	
	// Anzahl Silizium
	define("USR_START_CRYSTAL", $cfg->user_start_crystal->v);
	
	// Anzahl PVC
	define("USR_START_PLASTIC", $cfg->user_start_plastic->v);
	
	// Anzahl Tritium
	define("USR_START_FUEL", $cfg->user_start_fuel->v);
	
	// Anzahl Nahrung
	define("USR_START_FOOD", $cfg->user_start_food->v);
	
	// Anzahl Bewohner
	define("USR_START_PEOPLE", $cfg->user_start_people->v);
	
	// "Startplanet" Name
	define("USR_PLANET_NAME", $cfg->user_planet_name->v);

  /*********/
  /* Zeit  */
  /*********/
  
	// Allgegenwertiger Faktor in allen build_times
	define("GLOBAL_TIME", $cfg->global_time->v);
	
	// Gebäudebau Faktor
	define("BUILD_BUILD_TIME", $cfg->build_build_time->v);
	
	// Forschungsbau Faktor
	define("RES_BUILD_TIME", $cfg->res_build_time->v);
	
	// Schiffsbau Faktor
	define("SHIP_BUILD_TIME", $cfg->ship_build_time->v);
	
	// Verteidigungsbau Faktor
	define("DEF_BUILD_TIME", $cfg->def_build_time->v);
	
	// Flugzeit Faktor (wirkt nicht auf Start/Landezeit)
	define("FLEET_FACTOR_F", $cfg->flight_flight_time->v);
	
	// Startzeit Faktor
	define("FLEET_FACTOR_S", $cfg->flight_start_time->v);
	
	// Landezeit Faktor
	define("FLEET_FACTOR_L", $cfg->flight_land_time->v);

  /*************************/
  /* Flotten & Kampfsystem */
  /*************************/

	//
	// Invasion
	//
	
	// Grundinvasionschance
	define("INVADE_POSSIBILITY", $cfg->invade_possibility->v);
	
	// MAX. Invasionschance
	define("INVADE_MAX_POSSIBILITY", $cfg->invade_possibility->p1);
	
	// Min. Invasionschance
	define("INVADE_MIN_POSSIBILITY", $cfg->invade_possibility->p2);
	
	// wird nicht benötigt!
	define("INVADE_SHIP_DESTROY", $cfg->invade_ship_destroy->v);
	
	// = true/1 um aktive user zu invasieren
	define("INVADE_ACTIVE_USER", (boolean)$cfg->invade_active_users->v);

	//
	// Kampfsystem
	//
	
	// Prozentualer Wiederaufbau der Def
	define("DEF_RESTORE_PERCENT", $cfg->def_restore_percent->v);
	
	// Def ins Trümmerfeld
	define("DEF_WF_PERCENT", $cfg->def_wf_percent->v);
	
	// Ship ins Trümmerfeld
	define("SHIP_WF_PERCENT", $cfg->ship_wf_percent->v);
	
	// Chance-Faktor beim Bombardieren + Deaktivieren
	define("SHIP_BOMB_FACTOR", $cfg->ship_bomb_factor->v);

	//
	// Anfängerschutz
	//
	
	// Absolute Puntktegrenze
	define("USER_ATTACK_MIN_POINTS", $cfg->user_attack_min_points->v);
	
	// Prozentualer Punkteunterschied
	define("USER_ATTACK_PERCENTAGE", $cfg->user_attack_percentage->v);

  /*********/
  /* Markt */
  /*********/

	// Aktion beim versenden von Rohstoffen
	define("FLEET_ACTION_RESS", $cfg->market_ship_action_ress->v);
	
	// Aktion beim versenden von Schiffen oder Schiffe&Rohstoffe
	define("FLEET_ACTION_SHIP", $cfg->market_ship_action_ship->v);
	
	// Minimal Flugzeit
	define("FLIGHT_TIME_MIN", $cfg->market_ship_flight_time->p1);
	
	// Maximal Flugzeit
	define("FLIGHT_TIME_MAX", $cfg->market_ship_flight_time->p2);
	
	// Zeit in stunden, wie lange die auktion nach ablauf noch zu sehen ist
	define("AUCTION_DELAY_TIME", $cfg->market_auction_delay_time->v);
	
	// Titan Taxe
	define("MARKET_METAL_FACTOR", RuntimeDataStore::get('market_rate_0', 1);
	
	// Silizium Taxe
	define("MARKET_CRYSTAL_FACTOR", RuntimeDataStore::get('market_rate_1', 1);
	
	// PVC Taxe
	define("MARKET_PLASTIC_FACTOR", RuntimeDataStore::get('market_rate_2', 1);
	
	// Tritium Taxe
	define("MARKET_FUEL_FACTOR", RuntimeDataStore::get('market_rate_3', 1);
	
	// Nahrung Taxe
	define("MARKET_FOOD_FACTOR", RuntimeDataStore::get('market_rate_4', 1);

?>