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
	// 	File: conf.inc.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.09.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//
	/**
	* Main config file, stores database access data and table names
	*
	* @package etoa_gameserver
	* @author MrCage mrcage@etoa.ch
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/

	///////////////////////////////////////////////////////////////
	//////////////       MYSQL - Connection Data          /////////
	///////////////////////////////////////////////////////////////

	$db_access['server'] 			= 'test.etoa.net';
	$db_access['db'] 				= 'etoatest';
	$db_access['user'] 				= 'etoatest';
	$db_access['pw'] 				= 'etoatest';
	$db_access['adminuser'] 		= 'etoatest';
	$db_access['adminpw'] 			= 'etoatest';

	// This is a new aproach and only for testing, because constants ar better than variables for such things
	define('DB_SERVER',$db_access['server']);
	define('DB_USER',$db_access['user']);
	define('DB_PASSWORD',$db_access['pw']);
	define('DB_DATABASE',$db_access['db']);
	
	define("PASSWORD_SALT","wokife63wigire64reyodi69");								// Passwort-Salt (während einer laufenden Runde NICHT ändern!
	
	///////////////////////////////////////////////////////////////
	//////////////            FTP-Backup			            /////////
	///////////////////////////////////////////////////////////////

	define('BACKUP_REMOTE_IP',"88.198.42.35");
	define('BACKUP_REMOTE_USER',"13274");
	define('BACKUP_REMOTE_PASSWORD',"NZnYtarU");
	define('BACKUP_REMOTE_PATH',"/sql_test");	

	///////////////////////////////////////////////////////////////
	//////////////            Definitions			            /////////
	///////////////////////////////////////////////////////////////

	define('LOGINSERVER_URL',"http://www.etoa.ch/servers");					// Adresse des Loginservers
	define('GAMEROUND_NAME',"Testrunde");									// Name der Runde, erschein als Seitentitel
	define('REGEXP_NAME','^.[^0-9\'\"\?\<\>\$\!\=\;\&]*$');
	define('REGEXP_NICK','^.[^\'\"\?\<\>\$\!\=\;\&]*$');

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
