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

	// Fehlermeldungs-Level feststellen
	if (ETOA_DEBUG==1)
		error_reporting(E_ALL);
	else
		error_reporting(E_ERROR | E_WARNING | E_PARSE);


  /***********************************/
  /* Directory- and file paths       */
  /***********************************/

	// Backup-Dir
	define('BACKUP_DIR',$cfg->get('backup'));	

	// RSS Dir
	define('RSS_DIR',CACHE_ROOT."/rss");

	// Townhall-RSS-File
	define('RSS_TOWNHALL_FILE',RSS_DIR."/townhall.rss");
	
	// Smarty Path
	define('SMARTY_DIR', "libs/smarty/");
	define('SMARTY_TEMPLATE_DIR', CACHE_ROOT."/smarty_templates");
	define('SMARTY_COMPILE_DIR', CACHE_ROOT."/smarty_compile");

	// xAjax
	define('XAJAX_DIR',"libs/xajax");

	// Pfade
	define("SMILIE_DIR",IMAGE_DIR.DIRECTORY_SEPARATOR."smilies");								// Smilies
	define("IMAGEPACK_DIRECTORY",IMAGE_DIR.DIRECTORY_SEPARATOR."imagepacks");				// Bilder
	define("IMAGEPACK_DOWNLOAD_DIRECTORY",CACHE_ROOT."/imagepacks");				// Bilder

  /***********************************/
  /* Directory names                 */
  /***********************************/

	define("DESIGN_DIRECTORY","designs");						// CSS Style
	define("IMAGE_TECHNOLOGY_DIR","technologies");	// Tech Ordner
	define("IMAGE_SHIP_DIR","ships");								// Schiffe Ordner
	define("IMAGE_PLANET_DIR","planets");						// Planeten Ordner
	define("IMAGE_BUILDING_DIR","buildings");				// Gebäude Ordner
	define("IMAGE_DEF_DIR","defense");							// Def Ordner
	define("IMAGE_ALLIANCE_BUILDING_DIR","abuildings");	// Allianzgebäude

  /***********************************/
  /* Design, Layout, Allgmeine Pfade */
  /***********************************/
  
  // Layout
	define ("STATS_NUM_OF_ROWS", $cfg->get('stats_num_rows')); // Statistik Anzahl Zeilen

	// Externe Pfade
	define("HELPCENTER_URL","http://www.etoa.ch/help/?page=faq");	// Helpcenter Link
	define('HELPCENTER_ONCLICK',"window.open('".HELPCENTER_URL."','helpcenter','width=1024,height=700,scrollbars=yes');");

	define("FORUM_PATH","http://www.etoa.ch/forum");	// Forum Link
	
	define("DEVCENTER_PATH","https://dev.etoa.ch");	// Entwickler Link
	define("DEVCENTER_ONCLICK","window.open('".DEVCENTER_PATH."','dev','width=1024,height=768,scrollbars=yes');");	// Entwickler Link
	define("BUGREPORT_URL","http://dev.etoa.ch:8000/game/wiki/TicketTutorial");

	define('CHAT_URL',"chatframe.php");	// Chat
	define('CHAT_ONCLICK',"parent.top.location='chatframe.php';");

	define('TEAMSPEAK_URL',"http://ts.etoa.ch");	// Teamspeak
	define('TEAMSPEAK_ONCLICK',"window.open('".TEAMSPEAK_URL."','ts','width=800,height=600,scrollbars=yes');");

	define('RULES_URL','http://www.etoa.ch/rules'); // Game-Rules
	define('RULES_ONCLICK',"window.open('".RULES_URL."','rules','width=600,height=500,scrollbars=yes');");

  /*********************/
  /* Zufallsereignisse */
  /*********************/

	define("RANDOM_EVENTS_PER_UPDATE",1);

  /****************************/
  /* Allgemeine Einstellungen */
  /****************************/

	// Homepage
	define('DEFAULT_PAGE',"overview");

	// Onlinetime-Threshold
	define('ONLINE_TIME',$cfg->get('online_threshold'));

	//Paswort und Nicklänge
	define("PASSWORD_MINLENGHT",$cfg->get('password_minlength')); 		// Minimale Passwortlänge
	define("PASSWORD_MAXLENGHT",$cfg->get('password_minlength')); 	// Minimale Passwortlänge
	define("NICK_MINLENGHT",$cfg->p1('nick_length'));							// Minimale Nicklänge
	define("NICK_MAXLENGHT",$cfg->p2('nick_length'));							// Maximale Nicklänge
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
	define("RES_POWER","Energiezellen");

	define("RES_1",RES_METAL);
	define("RES_2",RES_CRYSTAL);
	define("RES_3",RES_PLASTIC);
	define("RES_4",RES_FUEL);
	define("RES_5",RES_FOOD);
	define("RES_6",RES_POWER);

	define('RES_ICON_METAL','<img class="resIcon" src="images/resources/metal_s.png" alt="'.RES_METAL.'" />');
	define('RES_ICON_CRYSTAL','<img class="resIcon" src="images/resources/crystal_s.png" alt="'.RES_CRYSTAL.'" />');
	define('RES_ICON_PLASTIC','<img class="resIcon" src="images/resources/plastic_s.png" alt="'.RES_PLASTIC.'" />');
	define('RES_ICON_FUEL','<img class="resIcon" src="images/resources/fuel_s.png" alt="'.RES_FUEL.'" />');
	define('RES_ICON_FOOD','<img class="resIcon" src="images/resources/food_s.png" alt="Nahrung" />');
	define('RES_ICON_POWER','<img class="resIcon" src="images/resources/power_s.png" alt="Energie" />');
	define('RES_ICON_POWER_USE','<img class="resIcon" src="images/resources/poweru_s.png" alt="Energieverbrauch" />');
	define('RES_ICON_PEOPLE','<img class="resIcon" src="images/resources/people_s.png" alt="Bevölkerung" />');
	define('RES_ICON_TIME','<img class="resIcon" src="images/resources/time_s.png" alt="Zeit" />');


	// Regular expressions
	define('REGEXP_NAME','^.[^0-9\'\"\?\<\>\$\!\=\;\&]*$');
	define('REGEXP_NICK','^.[^\'\"\?\<\>\$\!\=\;\&]*$');

	// Universum
	define("CELL_NUM_X",$conf['num_of_cells']['p1']);		// Anzahl Zellen x
	define("CELL_NUM_Y",$conf['num_of_cells']['p2']);		// Anzahl Zellen y
	define("CELL_LENGTH",$ae=$conf['cell_length']['v']);			// Länge vom Solsys in AE
	define("PLANETS_MAX",$np=$conf['num_planets']['p2']);			// Max. Planeten im Solsys		

	// Wurmlöcher
	define("WH_UPDATE_AFFECT_TIME",$conf["wh_update"]["v"]);	// ???
	define("WH_UPDATE_AFFECT_CNT",$conf["wh_update"]["p1"]);	// ???
	
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

	define("ENABLE_USERTITLES",1);
	define("USERTITLES_MIN_POINTS",10000)	;
	
	define('DIPLOMACY_POINTS_PER_NEWS',4);
	define('DIPLOMACY_POINTS_PER_PACT',1);
	define('DIPLOMACY_POINTS_MIN_PACT_DURATION',3600*24*1);
	define('DIPLOMACY_POINTS_PER_WAR',1);

	define('TRADE_POINTS_PER_TRADE',1);
	define('TRADE_POINTS_PER_AUCTION',1);
	define('TRADE_POINTS_PER_TRADETEXT',1);
	define('TRADE_POINTS_TRADETEXT_MIN_LENGTH',15);
	
	define('BATTLE_POINTS_A_W',3);
	define('BATTLE_POINTS_A_D',1);
	define('BATTLE_POINTS_A_L',1);
	define('BATTLE_POINTS_D_W',2);
	define('BATTLE_POINTS_D_D',1);
	define('BATTLE_POINTS_D_L',0);
	define('BATTLE_POINTS_SPECIAL',1);

	// Sonstiges
	define("RECYC_MAX_PAYBACK",0.9);																	// Maxmimale Recyclingtech effizient
	define("PEOPLE_FOOD_USE",$conf["people_food_require"]["v"]);			// Anzahl Nahrung, welche Arbeiter benötigen
	define("USER_MAX_PLANETS",$conf["user_max_planets"]["v"]);				// Maximale Anzahl Planeten
	
	// Spezialiasten
	define("SPECIALIST_MIN_POINTS_REQ",$cfg->p2('specialistconfig'));												// Minimal Punkte für Spezialist (VERALTET)
	define('SPECIALIST_MAX_COSTS_FACTOR',$cfg->p1('specialistconfig'));													// Maximale Kostensteigerung
	define('SPECIALIST_AVAILABILITY_FACTOR',$cfg->get('specialistconfig'));											// Verfügbare Spezialisten pro Typ basierend auf Faktor * Anzahl User
	
	// Kriegsdauer
	define("WAR_DURATION",3600*48);
	define("PEACE_DURATION",3600*48);
	
	// Tipps beim Start aktivieren
	define("ENABLE_TIPS",1);

	// Permissions for uploaded files
	define('FILE_UPLOAD_PERMS',0644); 

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

  /*********/
  /* Zeit  */
  /*********/
  
	define("GLOBAL_TIME",$conf['global_time']['v']);								// Allgegenwertiger Faktor in allen build_times
	define("BUILD_BUILD_TIME",$conf['build_build_time']['v']);			// Gebäudebau Faktor
	define("RES_BUILD_TIME",$conf['res_build_time']['v']);					// Forschungsbau Faktor
	define("SHIP_BUILD_TIME",$conf['ship_build_time']['v']);				// Schiffsbau Faktor
	define("DEF_BUILD_TIME",$conf['def_build_time']['v']);					// Verteidigungsbau Faktor
	define("FLEET_FACTOR_F",$conf['flight_flight_time']['v']);			// Flugzeit Faktor (wirkt nicht auf Start/Landezeit)
	define("FLEET_FACTOR_S",$conf['flight_start_time']['v']);				// Startzeit Faktor
	define("FLEET_FACTOR_L",$conf['flight_land_time']['v']);				// Landezeit Faktor
	define("BUILDING_QUEUE_DELAY",60);															// Zeitverzögerung zwischen zwei Bauaufträgen in der Warteschlange

  /****************/
  /* Technologien */
  /****************/
  
	define("STRUCTURE_TECH_ID",9);						// ID der Strukturtechnik
	define("SHIELD_TECH_ID",10);							// ID der Schildtechnik
	define("WEAPON_TECH_ID",8);								// ID der Waffentechnik
	define("REGENA_TECH_ID",19);							// ID der Regenatechnik	
	define("TARN_TECH_ID",11);								// ID der Tarntechnik	
  define("COMPUTER_TECH_ID",25);            // ID der Tarntechnik  
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
	define("SPY_ATTACK_SHOW_SUPPORT",11);		// ...die Supportflotten auf dem Planeten

	// Spionageabwehr
	define('SPY_DEFENSE_MAX',90);							// Maximale Spionageabwehr in Prozent
	define('SPY_DEFENSE_FACTOR_TECH',20);			// Spionageabwehr: Gewichtung der Technologien
	define('SPY_DEFENSE_FACTOR_SHIPS',0.5);		// Spionageabwehr: Gewichtung der Sonden
	define('SPY_DEFENSE_FACTOR_TARN',10);			// Spionageabwehr/Tarnabwehr: Gewichtung der Tarntechnik	
		
	
  /***********/
  /* Gebäude */
  /***********/

	define("BUILDING_GENERAL_CAT",1);
	define("BUILDING_RES_CAT",2);
	define("BUILDING_POWER_CAT",3);
	define("BUILDING_STORE_CAT",4);

	define("RES_BUILDING_CAT",2);
	define("BUILD_BUILDING_ID",6);								// Gebäude welches den Status des Bauhofes wiedergibt
	define("TECH_BUILDING_ID",8);									// Gebäude welches den Status des Forschungslabors wiedergibt
	define("SHIP_BUILDING_ID",9);									// Gebäude welches den Status der Schiffswerft wiedergibt
	define("DEF_BUILDING_ID",10);									// Gebäude welches den Status der Waffenfabrik wiedergibt
	
	define("SHIPYARD_ID",9);											// ID der Schiffswerft
	define("FACTORY_ID",10);											// ID der Waffenfabrik
	define('MARKTPLATZ_ID',21);										// ID des Marktplatzes
	define("FLEET_CONTROL_ID",11);								// ID der Flottenkontrolle
	define("BUILD_CRYPTO_ID",24);									// ID des Kryptocenters
	define("BUILD_MISSILE_ID",25);								// ID des Raketensilos

	// Allianzgebäude
	define("ALLIANCE_MARKET_ID",2);								// ID des Allianzmarktplatzes
	define("ALLIANCE_SHIPYARD_ID",3);								// ID des Allianzschiffwerftes
	define("ALLIANCE_BUILD_CRYPTO_ID",6);

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

	// Raketensilo
	define("MISSILE_SILO_MISSILES_PER_LEVEL",5); 	// Raketen, die pro Stufe im Silo gelagert werden können
	define("MISSILE_SILO_FLIGHTS_PER_LEVEL",1);		// Anzahl gleichzeitiger Flüge pro Silostufe
	define("MISSILE_BATTLE_SHIELD_FACTOR",0.3);		// Faktor mit dem die Schilde der Verteidigung bei einem Kampf mit einberechnet werden.
	
	// Kryptocenter
	define("CRYPTO_RANGE_PER_LEVEL",500);				// Reichweite in AE für Kryptoanalyse pro Ausbaustufe
	define("CRYPTO_FUEL_COSTS_PER_SCAN",10000);		// Kosten an Tritium pro Kryptoanalyse

	// Resourcenbunker
	define("RES_BUNKER_ID",26);
	define("RES_BUNKER_SPACE",5000);
	define("RES_BUNKER_FACTOR",2);

	// Flottenbunker
	define("FLEET_BUNKER_ID",27);
	define("FLEET_BUNKER_SPACE",100000);
	define("FLEET_BUNKER_FACTOR",2);

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
	define("TECH_SPEED_CAT",1);					// Kategorie der Antriebstechniken
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
	define("BOARD_AVATAR_DIR",CACHE_ROOT."/avatars");		// Verzeichnis der Avatare
	define("BOARD_DEFAULT_IMAGE","default.png");									// Standard Foren-Icon
	define("BOARD_ADMIN_RANK",4);																	// ???
	define("BOARD_TOPIC_TABLE","allianceboard_topics");						// Tabelle der Forentopics
	define("BOARD_POSTS_TABLE","allianceboard_posts");						// Tabelle der Forenposts
	define("BOARD_CAT_TABLE","allianceboard_cat");								// Tabelle der Kategorien

	define("BOARD_AVATAR_MAX_WIDTH",1024);															// Avatar-Breite
	define("BOARD_AVATAR_MAX_HEIGHT",1024);															// Avatar-Höhe
	define("BOARD_AVATAR_MAX_SIZE",2097152);												// Profilbild-Grösse in Byte
	define("BOARD_AVATAR_WIDTH",64);															// Avatar-Breite
	define("BOARD_AVATAR_HEIGHT",64);															// Avatar-Höhe

	if (!defined('GD_VERSION'))
		define("GD_VERSION",2);	
	
	// Profilbild
	define("PROFILE_IMG_DIR",CACHE_ROOT."/userprofiles");							// Verzeichnis der User-Profilbilder
	define("PROFILE_IMG_WIDTH",640);															// Profilbild-Breite
	define("PROFILE_IMG_HEIGHT",480);															// Profilbild-Höhe
	define("PROFILE_MAX_IMG_WIDTH",1280);													// Max. Profilbild-Breite
	define("PROFILE_MAX_IMG_HEIGHT",1024);												// Max. Profilbild-Höhe
	define("PROFILE_IMG_MAX_SIZE",2097152);												// Profilbild-Grösse in Byte
	
	// Allianzbild
	define("ALLIANCE_IMG_DIR",CACHE_ROOT."/allianceprofiles");					// Verzeichnis der Allianz-Bilder
	define("ALLIANCE_IMG_WIDTH",800);															// Allianzbild-Breite
	define("ALLIANCE_IMG_HEIGHT",600);														// Allianzbild-Höhe
	define("ALLIANCE_IMG_MAX_WIDTH",1280);												// Max. Allianzbild-Breite
	define("ALLIANCE_IMG_MAX_HEIGHT",1024);												// Max. Allianzbild-Höhe
	define("ALLIANCE_IMG_MAX_SIZE",2000000);											// Max. Allianzbild-Grösse in Byte
	
  /****************/
  /* Allianz Flottenkontrolle */
  /****************/		
	define("ALLIANCE_FLEET_SHOW",1);
	define("ALLIANCE_FLEET_SHOW_DETAIL",2);
	define("ALLIANCE_FLEET_SEND_HOME",3);
	define("ALLIANCE_FLEET_SHOW_PART",4);
	define("ALLIANCE_FLEET_SEND_HOME_PART",5);

  /****************/
  /* Sonstiges */
  /****************/	
	
	define('ADD_BANNER','');		// Advertising banner code
	define('FORCE_ADDS',0); // Banner immer anzeigen
	define('IPC_ID','A');
	
	/***********
	* Updates *
	***********/
	
	define('LOG_UPDATES',false);
	define('LOG_UPDATES_THRESHOLD',10);	
	define('GAMESTATS_FILE',CACHE_ROOT."/out/gamestats.html");
	define('GAMESTATS_ROW_LIMIT',15);
	define('USERSTATS_OUTFILE',CACHE_ROOT."/out/userstats.png");
	define('XML_INFO_FILE',CACHE_ROOT."/xml/info.xml");

	// Daemon
	// Todo: make changeable
	$daemonId = $cfg->daemonIdentifier->v;
	$daemonLogfile = "/var/log/etoa/".$daemonId.".log";
	$daemonPidfile = "/var/run/etoa/".$daemonId.".pid";
	$daemonExe = "/home/etoa/backend/bin/etoad";




?>
