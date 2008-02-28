<?

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
	* @package etoa_gameserver
	* @author MrCage mrcage@etoa.ch
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	define('GAMEROUND_NAME',ROUNDID);

	// Fehlermeldungs-Level feststellen
	if (ETOA_DEBUG==1)
		Error_reporting(E_ALL);
	else
		error_reporting(E_ERROR | E_WARNING | E_PARSE);

	// OS-Version feststellen
	if (stristr($_SERVER['SERVER_SIGNATURE'],"win32") || stristr($_SERVER['SERVER_SIGNATURE'],"win64"))
	{
		define('UNIX',false);
		define('WINDOWS',true);
	}
	else
	{
		define('UNIX',true);
		define('WINDOWS',false);
		define('UNIX_USER',"etoa");
		define('UNIX_GROUP',"apache");
	}

	///////////////////////////////////////////////////////////////
	//////////////            Definitions			            /////////
	///////////////////////////////////////////////////////////////

	define('REGEXP_NAME','^.[^0-9\'\"\?\<\>\$\!\=\;\&]*$');
	define('REGEXP_NICK','^.[^\'\"\?\<\>\$\!\=\;\&]*$');


	// Homepage
	define('DEFAULT_PAGE',"overview");
	
	// Backup-Dir
	define('BACKUP_DIR',$conf['backup']['v']);	
	if (!defined('CACHE_ROOT')) define('CACHE_ROOT','cache');
	if (!defined('CLASS_ROOT'))	define('CLASS_ROOT','classes');
	define('RSS_DIR',CACHE_ROOT."/rss");

	// Townhall-RSS-File
	define('RSS_TOWNHALL_FILE',RSS_DIR."/townhall.rss");
	
  /***********************************/
  /* Libraries                       */
  /***********************************/
	
	// Smarty Path
	define('SMARTY_DIR', "libs/smarty/");
	define('SMARTY_TEMPLATE_DIR', "cache/smarty_templates");
	define('SMARTY_COMPILE_DIR', "cache/smarty_compile");

	// xAjax
	define('XAJAX_DIR',"libs/xajax");

  /***********************************/
  /* Design, Layout, Allgmeine Pfade */
  /***********************************/
  
  // Layout
	define("TBL_SPACING",$conf['general_table_offset']['v']);		// ???
	define("TBL_PADDING",$conf['general_table_offset']['p1']);	// ???
	define ("NUM_OF_ROWS",$conf['stats_num_rows']['v']);				// ??? "Statistik" (komische bezeichnung!)

	// Pfade
	define("SMILIE_DIR","images/smilies");								// Smilies
	define("IMAGEPACK_DIRECTORY","images/imagepacks");				// Bilder
	define("IMAGEPACK_DOWNLOAD_DIRECTORY","cache/imagepacks");				// Bilder

	// Externe Pfade
	define("HELPCENTER_URL","http://help.etoa.ch");	// Helpcenter Link
	define('HELPCENTER_ONCLICK',"window.open('".HELPCENTER_URL."','helpcenter','width=1024,height=700,scrollbars=yes');");

	define("FORUM_PATH","http://forum.etoa.ch");	// Forum Link
	
	define("DEVCENTER_PATH","https://dev.etoa.ch");	// Entwickler Link

	define('CHAT_URL',"http://chat.etoa.ch");	// Chat
	define('CHAT_ONCLICK',"window.open('".CHAT_URL."','chat','width=900,height=700,scrollbars=yes');");

	define('TEAMSPEAK_URL',"http://ts.etoa.ch");	// Teamspeak
	define('TEAMSPEAK_ONCLICK',"window.open('".TEAMSPEAK_URL."','ts','width=800,height=600,scrollbars=yes');");

	define('RULES_URL','http://www.etoa.ch/rules'); // Game-Rules
	define('RULES_ONCLICK',"window.open('".RULES_URL."','rules','width=600,height=500,scrollbars=yes');");


	// Ordner
	define("DESIGN_DIRECTORY","designs");					// CSS Style
	define("IMAGE_TECHNOLOGY_DIR","technologies");	// Tech Ordner
	define("IMAGE_SHIP_DIR","ships");								// Schiffe Ordner
	define("IMAGE_PLANET_DIR","planets");						// Planeten Ordner
	define("IMAGE_BUILDING_DIR","buildings");				// Gebäude Ordner
	define("IMAGE_DEF_DIR","defense");							// Def Ordner

	// Farben (In der Statistik, Raumkarte...)
	define ("COLOR_BANNED",$conf['color_banned']['v']);			// Gesperrte
	define ("COLOR_UMOD",$conf['color_umod']['v']);					// Urlaubsmodus
	define ("COLOR_INACTIVE",$conf['color_inactive']['v']);	// Inaktive
	define ("COLOR_INACTIVE_LONG",$conf['color_inactive']['p1']);	// Inaktive
	define ("COLOR_ALLIANCE",$conf['color_alliance']['v']);	// Allianzmitglied
	define ("COLOR_DEFAULT",$conf['color_default']['v']);		// alle anderen


	define('ONLINE_TIME',$conf['online_threshold']['v']);





  /****************************/
  /* Allgemeine Einstellungen */
  /****************************/

	//Paswort und Nicklänge
	define("PASSWORD_MINLENGHT",$conf['password_minlength']['v']); 		// Minimale Passwortlänge
	define("PASSWORD_MAXLENGHT",$conf['password_minlength']['p1']); 	// Minimale Passwortlänge
	define("NICK_MINLENGHT",$conf['nick_length']['p1']);							// Minimale Nicklänge
	define("NICK_MAXLENGHT",$conf['nick_length']['p2']);							// Maximale Nicklänge
	define("NAME_MAXLENGTH",$conf['name_length']['v']);								// Minimale Nicklänge
	
	// Inaktive & Urlaubsmodus
  define("MIN_UMOD_TIME",$conf['hmode_days']['v']);									// Minimale Umode-Dauer
  define("USER_INACTIVE_DELETE",$conf['user_inactive_days']['p1']);	// Vergangene Zeit bis Löschung eines Users
  define("USER_NOTLOGIN_DELETE",$conf['user_inactive_days']['p2']);	// Vergangene Zeit bis Löschung falls nie eingeloggt
  define("USER_INACTIVE_SHOW",$conf['user_inactive_days']['v']);		// Zeit bis "Inaktiv" Status
	define("USER_INACTIVE_TIME",time()-(24*3600*$conf['user_inactive_days']['v']));
  define("USER_INACTIVE_LONG",14);		// Zeit bis "Inaktiv" Status
	define("USER_INACTIVE_TIME_LONG",time()-(24*3600*14));
	
	// Rohstoffbenennung
	define("RES_METAL","Titan");
	define("RES_CRYSTAL","Silizium");
	define("RES_PLASTIC","PVC");
	define("RES_FUEL","Tritium");
	define("RES_FOOD","Nahrung");

	// Universum
	define("CELL_NUM_X",$conf['num_of_cells']['p1']);		// Anzahl Zellen x
	define("CELL_NUM_Y",$conf['num_of_cells']['p2']);		// Anzahl Zellen y
	define("CELL_LENGTH",$ae=$conf['cell_length']['v']);			// Länge vom Solsys in AE
	define("PLANETS_MAX",$np=$conf['num_planets']['p2']);			// Max. Planeten im Solsys		

	// Wurmlöcher
	define("WH_UPDATE_AFFECT_TIME",$conf["wh_update"]["v"]);	// ???
	define("WH_UPDATE_AFFECT_CNT",$conf["wh_update"]["p1"]);	// ???
	
	// Planetenkennungs-Länge
	define("PLANET_ID_LENGTH",5);	// Länge der Planetenkennung

	// Minimale Sperrzeit für Kolonielöschung
	define("COLONY_DELETE_THRESHOLD",24*3600*5);

	// Nachrichten
	define("USER_MSG_CAT_ID",1);															// Cat-ID Persönliche Nachrichten
	define('MISC_MSG_CAT_ID',5);
	define("SHIP_SPY_MSG_CAT_ID",2);													// Cat-ID Spionageberichte
	define("SHIP_WAR_MSG_CAT_ID",3);													// Cat-ID Kriegsberichte
	define("SHIP_MONITOR_MSG_CAT_ID",4);											// Cat-ID Überwachungsberichte
	define("SHIP_MISC_MSG_CAT_ID",5);													// Cat-ID Sonstige Nachrichten
	define("MSG_ALLYMAIL_CAT",6);															// Cat-ID Allianz
	define("FLOOD_CONTROL",$conf["msg_flood_control"]["v"]);	// Wartezeit bis zur nächsten Nachricht
	
	// Punkteberechnung
	define("STATS_USER_POINTS",$conf["points_update"]["p1"]);					// 1 Punkt für X (STATS_USER_POINTS) verbaute Rohstoffe
	define("STATS_ALLIANCE_POINTS",$conf["points_update"]["p2"]);			// 1 Punkt für X (STATS_ALLIANCE_POINTS) User Punkte

	define("ENABLE_USERTITLES",0);
	define("USERTITLES_MIN_POINTS",10000)	;
	
	define('DIPLOMACY_POINTS_PER_NEWS',4);
	define('DIPLOMACY_POINTS_PER_PACT',1);
	define('DIPLOMACY_POINTS_MIN_PACT_DURATION',3600*24*2);
	define('DIPLOMACY_POINTS_PER_WAR',1);

	define('TRADE_POINTS_PER_TRADE',1);
	define('TRADE_POINTS_PER_AUCTION',1);
	define('TRADE_POINTS_PER_TRADETEXT',1);
	define('TRADE_POINTS_TRADETEXT_MIN_LENGTH',15);

	// Sonstiges
	define("RECYC_MAX_PAYBACK",0.9);																	// Maxmimale Recyclingtech effizient
	define("STD_FIELDS",intval($conf["def_store_capacity"]["v"]));		// ???
	define("PEOPLE_FOOD_USE",$conf["people_food_require"]["v"]);			// Anzahl Nahrung, welche Arbeiter benötigen
	define("COLLECT_FUEL_MAX_AMOUNT",10000);													// ???
	define("USER_MAX_PLANETS",$conf["user_max_planets"]["v"]);				// Maximale Anzahl Planeten
	define("SPECIALIST_MIN_POINTS_REQ",100000);												// Minimal Punkte für Spezialist

	// User Planetwechsel
	define("MAX_MAINPLANET_CHANGES",20);
	
	
	// Kriegsdauer
	define("WAR_DURATION",3600*48);
	define("PEACE_DURATION",3600*48);
	
	// Tipps beim Start aktivieren
	define("ENABLE_TIPS",1);


  /****************************************************/
  /* Startwerte (bei erstellung eines neuen Accounts) */
  /****************************************************/

	define("USR_START_METAL",$conf['user_start_metal']['v']);				// Anzahl Titan
	define("USR_START_CRYSTAL",$conf['user_start_crystal']['v']);		// Anzahl Silizium
	define("USR_START_PLASTIC",$conf['user_start_plastic']['v']);		// Anzahl PVC
	define("USR_START_FUEL",$conf['user_start_fuel']['v']);					// Anzahl Tritium
	define("USR_START_FOOD",$conf['user_start_food']['v']);					// Anzahl Nahrung
	define("USR_START_PEOPLE",$conf['user_start_people']['v']);			// Anzahl Bewohner
	define("USR_PLANET_NAME",$conf['user_planet_name']['v']);				// "Startplanet" Name

	$firsttime_buildings = array();																
	$firsttime_techs = array();

  /*********/
  /* Zeit  */
  /*********/
  
	define("GLOBAL_TIME",$conf['global_time']['v']);								// Allgegenwertiger Faktor in allen build_times
	define("BUILD_BUILD_TIME",$conf['build_build_time']['v']);			// Gebäudebau Faktor
	define("RES_BUILD_TIME",$conf['res_build_time']['v']);					// Forschungsbau Faktor
	define("SHIP_BUILD_TIME",$conf['ship_build_time']['v']);				// Schiffsbau Faktor
	define("DEF_BUILD_TIME",$conf['def_build_time']['v']);					// Verteidigungsbau Faktor
	define("FLIGHT_FLIGHT_TIME",$conf['flight_flight_time']['v']);	// Flugzeit Faktor (wirkt nicht auf Start/Landezeit)
	define("FLIGHT_START_TIME",$conf['flight_start_time']['v']);		// Startzeit Faktor
	define("FLIGHT_LAND_TIME",$conf['flight_land_time']['v']);			// Landezeit Faktor
	define("FLEET_FACTOR_F",$conf['flight_flight_time']['v']);			// ??? doppelt
	define("FLEET_FACTOR_S",$conf['flight_start_time']['v']);				// ??? doppelt
	define("FLEET_FACTOR_L",$conf['flight_land_time']['v']);				// ??? doppelt






  /****************/
  /* Technologien */
  /****************/
  
	define("STRUCTURE_TECH_ID",9);						// ID der Strukturtechnik
	define("SHIELD_TECH_ID",10);							// ID der Schildtechnik
	define("WEAPON_TECH_ID",8);								// ID der Waffentechnik
	define("REGENA_TECH_ID",19);							// ID der Regenatechnik	
	define("TARN_TECH_ID",11);								// ID der Tarntechnik	
	define("RECYC_TECH_ID",12);								// ID der Recyclingtechnologie	
	define("BOMB_TECH_ID",15); 								// ID der Bombentechnik
	define('GEN_TECH_ID',23);									// ID der Gentechnologie
	define("TECH_WORMHOLE",22);								// ID der Wurmlochforschung
	
	// Ab diesem Level Sieht man von gegnerischen flotten diese infos...
	define("SPY_TECH_ID",7);									// ...Gebäude des Gegners
	define("SPY_TECH_SHOW_ATTITUDE",1);				// ...Gesinnung des Gegners (friedlich/feindlich)
	define("SPY_TECH_SHOW_NUM",3);						// ...Anzahl der Schiffe
	define("SPY_TECH_SHOW_SHIPS",5);					// ...die verschiedenen Schiffe in der Flotte
	define("SPY_TECH_SHOW_NUMSHIPS",7);				// ...die genaue Anzahl von jedem Schiffstyp
	define("SPY_TECH_SHOW_ACTION",9);					// ...Aktion (Angriff/Spionage etc.)

	// Ab diesem Level Sieht man beim Spionieren...
	define("SPY_ATTACK_SHOW_BUILDINGS",1);		// ...die Gebäude des Gegners
	define("SPY_ATTACK_SHOW_RESEARCH",3);			// ...die Forschung des Gegners
	define("SPY_ATTACK_SHOW_SHIPS",7);				// ...die Schiffe des Gegners
	define("SPY_ATTACK_SHOW_DEFENSE",5);			// ...die Defense des Gegners
	define("SPY_ATTACK_SHOW_RESSOURCEN",9);		// ...die Ressourcen des Gegners

	// Spionageabwehr
	define('SPY_DEFENSE_MAX',90);							// Maximale Spionageabwehr in Prozent
	define('SPY_DEFENSE_FACTOR_TECH',20);			// Spionageabwehr: Gewichtung der Technologien
	define('SPY_DEFENSE_FACTOR_SHIPS',0.5);		// Spionageabwehr: Gewichtung der Sonden
	define('SPY_DEFENSE_FACTOR_TARN',10);			// Spionageabwehr/Tarnabwehr: Gewichtung der Tarntechnik	
	
	
	
	
	
  /***********/
  /* Gebäude */
  /***********/

	define("BUILD_BUILDING_ID",6);								// Gebäude welches den Status des Bauhofes wiedergibt
	define("SHIP_BUILDING_ID",9);									// Gebäude welches den Status der Schiffswerft wiedergibt
	define("DEF_BUILDING_ID",10);									// Gebäude welches den Status der Waffenfabrik wiedergibt
	
	define("SHIPYARD_ID",9);											// ID der Schiffswerft
	define("FACTORY_ID",10);											// ID der Waffenfabrik
	define('MARKTPLATZ_ID',21);										// ID des Marktplatzes
	define("FLEET_CONTROL_ID",11);								// ID der Flottenkontrolle
	define("BUILD_CRYPTO_ID",24);									// ID des Kryptocenters
	define("BUILD_MISSILE_ID",25);								// ID des Raketensilos
	define("BUILD_TECH_ID",14);										// ???

	// Schiffswerft
	define("SHIPYARD_MIN_BUILD_TIME",20);					// Absolute minimal Bauzeit in Sekunden
	define("SHIPQUEUE_CANCEL_MIN_LEVEL",5);				// Ben. Level für Autragsabbruch
	define("SHIPQUEUE_CANCEL_START",0.3);					// ???
	define("SHIPQUEUE_CANCEL_FACTOR",0.03);				// ???
	define("SHIPQUEUE_CANCEL_END",0.8);						// ???

	// Waffenfabrik
	define("DEFENSE_MIN_BUILD_TIME",20);					// Absolute minimal Bauzeit in Sekunden
	define("DEFQUEUE_CANCEL_MIN_LEVEL",5);				// Ben. Level für Autragsabbruch
	define("DEFQUEUE_CANCEL_START",0.3);					// ???
	define("DEFQUEUE_CANCEL_FACTOR",0.03);				// ???
	define("DEFQUEUE_CANCEL_END",0.8);						// ???

	// ??? war unter "sonstiges" auch wieder doppel eintrag
	// DEPRECATED!
	define("SHIPDEFBUILD_CANCEL_TIME",$conf['shipdefbuild_cancel_time']['v']);	// Abbruchszeit bei einem bauauftrag im Schiffswerft/Waffenfabrik

	// Raketensilo
	define("MISSILE_SILO_MISSILES_PER_LEVEL",5); 	// Raketen, die pro Stufe im Silo gelagert werden können
	define("MISSILE_SILO_FLIGHTS_PER_LEVEL",1);		// Anzahl gleichzeitiger Flüge pro Silostufe
	define("MISSILE_BATTLE_SHIELD_FACTOR",0.3);		// Faktor mit dem die Schilde der Verteidigung bei einem Kampf mit einberechnet werden.
	
	// Kryptocenter
	define("CRYPTO_RANGE_PER_LEVEL",1000);				// Reichweite in AE für Kryptoanalyse pro Ausbaustufe
	define("CRYPTO_FUEL_COSTS_PER_SCAN",10000);		// Kosten an Tritium pro Kryptoanalyse






  /*************************/
  /* Flotten & Kampfsystem */
  /*************************/

	//Invasion
	define("INVADE_POSSIBILITY",$conf['invade_possibility']['v']);				// Grundinvasionschance
	define("INVADE_MAX_POSSIBILITY",$conf['invade_possibility']['p1']);		// MAX. Invasionschance
	define("INVADE_MIN_POSSIBILITY",$conf['invade_possibility']['p2']);		// Min. Invasionschance
	define("INVADE_SHIP_DESTROY",$conf['invade_ship_destroy']['v']);			// wird nicht benötigt!

	// Sonstige Flottendefinitionen
	define("FLEET_ACTION_LOG_CAT",13);	// Flotten Log ID (Kategorie)
	define("FLEET_NOCONTROL_NUM",1);		// Anzahl Flotten die OHNE Flottenkontrolle fliegen können
	define("TECH_SPEED_CAT",1);					// ???
	define("DEFAULT_ACTION","to");			// Standartflug "Transport hinflug" ??? (wieso das?)

	// Kampfsystem
	define("BATTLE_ROUNDS",5); 																				// Anzahl Runden
	define("DEF_RESTORE_PERCENT",$conf['def_restore_percent']['v']);	// Prozentualer Wiederaufbau der Def
	define("DEF_WF_PERCENT",$conf['def_wf_percent']['v']);						// Def ins Trümmerfeld
	define("SHIP_WF_PERCENT",$conf['ship_wf_percent']['v']);					// Ship ins Trümmerfeld
	define("SHIP_BOMB_FACTOR",$conf['ship_bomb_factor']['v']); 				// Chance-Faktor beim Bombardieren + Deaktivieren

	// Anfängerschutz
	define("USER_ATTACK_MIN_POINTS",$conf['user_attack_min_points']['v']);		// Absolute Puntktegrenze (momentan ausgeschaltet)
	define("USER_ATTACK_PERCENTAGE",$conf['user_attack_percentage']['v']); 		// Prozentualer Punkteunterschied






  /*********/
  /* Markt */
  /*********/

	define("MARKET_SHIP_ID",16);				// Handelsschiff ID
	define("LOG_CAT",7);								// Log-Cat ID
	define("FLEET_ACTION_RESS",$conf["market_ship_action_ress"]["v"]); // Aktion beim versenden von Rohstoffen
	define("FLEET_ACTION_SHIP",$conf["market_ship_action_ship"]["v"]); // Aktion beim versenden von Schiffen oder Schiffe&Rohstoffe
	define("CANCEL_TIME",1);						// ??? :P
	define("HANDELSMINISTER",1);				// ??? luegi de no säuber noche ^^
	define("FLIGHT_TIME_MIN",$conf["market_ship_flight_time"]["p1"]);	// Minimal Flugzeit
	define("FLIGHT_TIME_MAX",$conf["market_ship_flight_time"]["p2"]);	// Maximal Flugzeit
	define("SHIP_PRICE_FACTOR_MIN",1);		//Mindestpreisgrenze der Schiffe 1=100%
	define("SHIP_PRICE_FACTOR_MAX",2);		//Höchstpreisgrenze der Schiffe
	define("RESS_PRICE_FACTOR_MIN",0.7);		//Mindestpreisgrenze der Rohstoffe
	define("RESS_PRICE_FACTOR_MAX",2);			//Höchstpreisgrenze der Schiffe
	define("AUCTION_PRICE_FACTOR_MIN",0.333);	//Mindestpreisgrenze der Autkionen (summiert aus Roshtoffen und Schiffen)
	define("AUCTION_PRICE_FACTOR_MAX",3);		//Höchstpreisgrenze der Autkionen (summiert aus Roshtoffen und Schiffen)
	define("MARKET_SELL_TAX",1.005);				//Zuschlagsfaktor auf die Preise
	define("AUCTION_DELAY_TIME",$conf["market_auction_delay_time"]["v"]);		// Zeit in stunden, wie lange die auktion nach ablauf noch zu sehen ist
	define("AUCTION_MIN_DURATION",2);				//Mindestdauer einer Autkion (in Tagen)
	define("MIN_MARKET_LEVEL_RESS",1);			//Mindest Marktlevel um Rohstoffe zu kaufen und verkaufen
	define("MIN_MARKET_LEVEL_SHIP",3);			//Mindest Marktlevel um Schiffe zu kaufen und verkaufen
	define("MIN_MARKET_LEVEL_AUCTION",5);		//Mindest Marktlevel um Auktionen anzubieten und selber zu bieten
	define("MARKET_METAL_FACTOR",$conf["market_metal_factor"]["v"]);			// Titan Taxe
	define("MARKET_CRYSTAL_FACTOR",$conf["market_crystal_factor"]["v"]);	// Silizium Taxe
	define("MARKET_PLASTIC_FACTOR",$conf["market_plastic_factor"]["v"]);	// PVC Taxe
	define("MARKET_FUEL_FACTOR",$conf["market_fuel_factor"]["v"]);				// Tritium Taxe
	define("MARKET_FOOD_FACTOR",$conf["market_food_factor"]["v"]);				// Nahrung Taxe





  /****************/
  /* Allianzboard */
  /****************/
  
	define("BOARD_BULLET_DIR","images/boardbullets");		// Verzeichnis der Forenicons
	define("BOARD_AVATAR_DIR","cache/avatars");		// Verzeichnis der Avatare
	define("BOARD_DEFAULT_IMAGE","default.png");									// Standard Foren-Icon
	define("BOARD_ADMIN_RANK",4);																	// ???
	define("BOARD_TOPIC_TABLE","allianceboard_topics");						// Tabelle der Forentopics
	define("BOARD_POSTS_TABLE","allianceboard_posts");						// Tabelle der Forenposts
	define("BOARD_CAT_TABLE","allianceboard_cat");								// Tabelle der Kategorien
	define("BOARD_AVATAR_WIDTH",64);															// Avatar-Breite
	define("BOARD_AVATAR_HEIGHT",64);															// Avatar-Höhe

	if (!defined('GD_VERSION'))
		define("GD_VERSION",2);	
	
	// Profilbild
	define("PROFILE_IMG_DIR","cache/userprofiles");							// Verzeichnis der User-Profilbilder
	define("PROFILE_IMG_WIDTH",640);															// Profilbild-Breite
	define("PROFILE_IMG_HEIGHT",480);															// Profilbild-Höhe
	define("PROFILE_MAX_IMG_WIDTH",1280);													// Max. Profilbild-Breite
	define("PROFILE_MAX_IMG_HEIGHT",1024);												// Max. Profilbild-Höhe
	define("PROFILE_IMG_MAX_SIZE",2000000);												// Profilbild-Grösse in Byte
	
	// Allianzbild
	define("ALLIANCE_IMG_DIR","cache/allianceprofiles");					// Verzeichnis der Allianz-Bilder
	define("ALLIANCE_IMG_WIDTH",800);															// Allianzbild-Breite
	define("ALLIANCE_IMG_HEIGHT",600);														// Allianzbild-Höhe
	define("ALLIANCE_IMG_MAX_WIDTH",1280);												// Max. Allianzbild-Breite
	define("ALLIANCE_IMG_MAX_HEIGHT",1024);												// Max. Allianzbild-Höhe
	define("ALLIANCE_IMG_MAX_SIZE",2000000);											// Max. Allianzbild-Grösse in Byte


	// Icons
	define('RES_ICON_METAL','<img class="resIcon" src="images/resources/metal_s.png" alt="'.RES_METAL.'" />');
	define('RES_ICON_CRYSTAL','<img class="resIcon" src="images/resources/crystal_s.png" alt="'.RES_CRYSTAL.'" />');
	define('RES_ICON_PLASTIC','<img class="resIcon" src="images/resources/plastic_s.png" alt="'.RES_PLASTIC.'" />');
	define('RES_ICON_FUEL','<img class="resIcon" src="images/resources/fuel_s.png" alt="'.RES_FUEL.'" />');
	define('RES_ICON_FOOD','<img class="resIcon" src="images/resources/food_s.png" alt="Nahrung" />');
	define('RES_ICON_POWER','<img class="resIcon" src="images/resources/power_s.png" alt="Energie" />');
	define('RES_ICON_POWER_USE','<img class="resIcon" src="images/resources/poweru_s.png" alt="Energieverbrauch" />');
	define('RES_ICON_PEOPLE','<img class="resIcon" src="images/resources/people_s.png" alt="Bevölkerung" />');
	define('RES_ICON_TIME','<img class="resIcon" src="images/resources/time_s.png" alt="Zeit" />');
	
  /****************/
  /* Sonstiges */
  /****************/	
	
	// Ticket-System Kategorien
	$abuse_cats = array("messages"=>"Beleidigung in Nachricht",
											"townhall"=>"Rathaus-Missbrauch",
											"attack"=>"Missachtung der Angriffsregeln",
											"pushing"=>"Pushing-Verdach",
											"cheating"=>"Cheat-Verdach",
											"bugusing"=>"Bugusing-Verdach",
											"image"=>"Anstössiges Bild",
											"rules"=>"Sonstiger Regelverstoss");	
											
	$abuse_status = array("Neu","Zugeteilt","Abgeschlossen","Gelöscht");
											
	
	// Werbebanner
	define('ADD_BANNER','
	<div style="color:#0f0;font-size:8pt;font-weight:bold;">Unterstütze EtoA:<br/>
	(SHIFT+Click = neues Fenster)</div><br/>
	<script type="text/javascript"><!--
google_ad_client = "pub-4873671285923921";
google_ad_width = 120;
google_ad_height = 600;
google_ad_format = "120x600_as";
google_ad_type = "text";
//2007-08-16: EtoA InGame
google_ad_channel = "8017728947";
google_color_border = "000000";
google_color_bg = "000000";
google_color_link = "FFFFFF";
google_color_text = "CCCCCC";
google_color_url = "999999";
//-->
</script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script><br/>
	<script type="text/javascript"><!--
google_ad_client = "pub-4873671285923921";
google_ad_width = 120;
google_ad_height = 600;
google_ad_format = "120x600_as";
google_ad_type = "text";
//2007-08-16: EtoA InGame
google_ad_channel = "8017728947";
google_color_border = "000000";
google_color_bg = "000000";
google_color_link = "FFFFFF";
google_color_text = "CCCCCC";
google_color_url = "999999";
//-->
</script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>

');
	define('FORCE_ADDS',0); // Banner immer anzeigen
	
	// Referers
	$referers=explode("\n",$conf['referers']['v']);
	foreach ($referers as $k=>$v)
	$referers[$k] = trim($v);


	///////////////////////////////////////////////////////////////
	//////////////       MYSQL - Table Names		          /////////
	///////////////////////////////////////////////////////////////

	// Deprecated, use table names directly in code

	$db_table['admin_groups']				= "admin_groups";				// Administratorgeuppen
	$db_table['admin_users']				= "admin_users";				// Administratoren
	$db_table['admin_user_log']				= "admin_user_log";				// Administrator-Log
	$db_table['allianceboard_cat']			= "allianceboard_cat";			// Allianzforum-Kategorie
	$db_table['allianceboard_catranks']		= "allianceboard_catranks";		// Allianzforum-Kategorie-Verknüpfung
	$db_table['allianceboard_posts']		= "allianceboard_posts";		// Allianzforum Beiträge
	$db_table['allianceboard_topics']		= "allianceboard_topics";		// Allianzforum Themen
	$db_table['alliances']					= "alliances";					// Allianzen
	$db_table['alliance_bnd']				= "alliance_bnd";				// Allianz-Bündnisse/Kriege
	$db_table['alliance_history']			= "alliance_history";			// Allianz-Geschichte
	$db_table['alliance_news']				= "alliance_news"; 				// Allianz-News (Rathaus)
	$db_table['alliance_polls']				= "alliance_polls";				// Allianzumfragen
	$db_table['alliance_poll_votes']		= "alliance_poll_votes";		// User die bei Allianzumfragen abgestimmt haben
	$db_table['alliance_rankrights'] 		= "alliance_rankrights";		// Verknüpfung Ränge-Rechte
	$db_table['alliance_ranks']				= "alliance_ranks";				// Allianz-Ränge
	$db_table['alliance_rights']			= "alliance_rights";			// Allianz-Rechte
	$db_table['buddylist']					= "buddylist";					// Freundesliste
	$db_table['buildings']					= "buildings";					// Gebäude	
	$db_table['building_points'] 			= "building_points";			// Gebäudepunkte	
	$db_table['building_requirements']		= "building_requirements";		// Gebäude-Voraussetzungen
	$db_table['building_types']				= "building_types";				// Gebäude-Typen
	$db_table['buildlist']					= "buildlist";					// Gebäude-Bauliste
	$db_table['buttons']					= "buttons";					// Werbe/Partner-Buttons
	$db_table['config']						= "config";						// Konfiguration
	$db_table['config_cat']					= "config_cat";					// Konfiguration Kategorie	
	$db_table['defense']					= "defense";					// Verteidigung
	$db_table['deflist']					= "deflist";					// Verteidigung-Bauliste	
	$db_table['def_cat']					= "def_cat";					// Verteidigung-kategorie	
	$db_table['def_queue']					= "def_queue";					// Verteidigung-Bauliste	
	$db_table['def_requirements']			= "def_requirements";			// Verteidigung-Voraussetzugen
	$db_table['events']						= "events";						// Ereignisse (Vorlagen)
	$db_table['events_exec']				= "events_exec";				// Geplante Ereignisse
	$db_table['fleet']						= "fleet";						// Flotten
	$db_table['fleet_ships']				= "fleet_ships";				// Flotten-Schiffe-Verknüpfungen
	$db_table['login_failures']				= "login_failures";				// Fehlgeschlagene Logins	
	$db_table['logs']						= "logs";						// Logs	
	$db_table['logs_alliance']				= "logs_alliance";				// Logs Allianzen
	$db_table['logs_battle']				= "logs_battle";				// Logs Kampfberichte	
	$db_table['logs_fleet']					= "logs_fleet";					// Logs Flotten/Flottenaktionen	
	$db_table['logs_game']					= "logs_game";					// Logs Game
	$db_table['logs_game_cat']				= "logs_game_cat";				// Logs Game Kategorie
	$db_table['log_cat']					= "log_cat";					// Log-Kategorien
	$db_table['mail_queue']					= "mail_queue";					// User History (Banns, Verwarnungen, Passwort-Änderungen, etc)
	$db_table['market_auction']				= "market_auction";				// Marktplatz-Auktionen
	$db_table['market_ressource']			= "market_ressource";			// Marktplatz-Ressourcen
	$db_table['market_ship']				= "market_ship";				// Marktplatz-Schiffe
	$db_table['messages']					= "messages";					// Nachrichten
	$db_table['message_cat']				= "message_cat";				// Nachrichten-Kategorien
	$db_table['message_ignore']				= "message_ignore";				// Ignorierliste
	$db_table['missiles']					= "missiles";					// Raketen
	$db_table['missile_requirements']	= "missile_requirements";					// Raketen-Voraussetzungen
	$db_table['notepad']					= "notepad";					// Notizen
	$db_table['planets']					= "planets";					// Planeten	
	$db_table['planet_types']				= "planet_types";				// Planetentypen
	$db_table['races']						= "races";						// Rassen
	$db_table['resources']					= "resources";					// Ressourcen-Namen
	$db_table['shiplist']					= "shiplist";					// Schiff-Bauliste
	$db_table['ships']						= "ships";						// Schiffe
	$db_table['ship_cat']					= "ship_cat";					// Schiff-Kategorien
	$db_table['ship_queue'] 				= "ship_queue";					// Schiff-Bauliste
	$db_table['ship_requirements']			= "ship_requirements";			// Schiff-Voraussetzungen	
	$db_table['sol_types']					= "sol_types";					// Sonnentypen
	$db_table['space_cells']				= "space_cells";				// Weltraum-Zellen
	$db_table['specialists']				= "specialists";				// Spezialisten
	$db_table['target_bookmarks']			= "target_bookmarks";			// Flottenziel-Favoriten
	$db_table['techlist']					= "techlist";					// Forschungsliste
	$db_table['technologies']				= "technologies";				// Forschungen
	$db_table['tech_points'] 				= "tech_points";				// Gebäudepunkte	
	$db_table['tech_requirements']			= "tech_requirements";			// Forschung-Voraussetzungen
	$db_table['tech_types']					= "tech_types";					// Forschung-Kategorien
	$db_table['users']						= "users";						// Benutzer
	$db_table['user_history']				= "user_history";				// User History (Banns, Verwarnungen, Passwort-Änderungen, etc)
	$db_table['user_log']					= "user_log";					// Sessionarchivierung
	$db_table['user_multi']					= "user_multi";					// Multi eintragung (user am gleichen pc)
	$db_table['user_onlinestats']			= "user_onlinestats";			// Momentane Onlinezahlspeicherung	
	$db_table['user_points']				= "user_points";				// Punktearchivierung
	$db_table['user_requests']				= "user_requests";				// Änderungsanfragen
	$db_table['user_sitting']				= "user_sitting";				// Sitter eintragung
	$db_table['user_sitting_date']			= "user_sitting_date";			// Siting datums


?>
