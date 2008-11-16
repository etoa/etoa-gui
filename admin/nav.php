<?PHP

	$topnav=array();
	$navmenu=array();
	
	
	// Links des oberen Menüs
	$topnav['Login-Admin']['url']="http://www.etoa.ch/admin";
	$topnav['Login-Admin']['newwindow']=true;

	$topnav['Forum']['url']=FORUM_PATH;
	$topnav['Forum']['newwindow']=true;
	
	$topnav['Hilfecenter']['url']=HELPCENTER_URL;
	$topnav['Hilfecenter']['newwindow']=true;
	
	$topnav['Regeln']['url']=RULES_URL;
	$topnav['Regeln']['newwindow']=true;
	
	$topnav['Fehler melden']['url']=DEVCENTER_PATH;
	$topnav['Fehler melden']['newwindow']=true;
	
	$topnav['Chat']['url']=CHAT_URL;
	$topnav['Chat']['newwindow']=true;
	
	$topnav['TeamSpeak']['url']=TEAMSPEAK_URL;
	$topnav['TeamSpeak']['newwindow']=true;

	// Links des linken Menüs
	$navmenu['Allgemeines']['Startseite']['page']="home";
	$navmenu['Allgemeines']['Startseite']['sub']="";
	$navmenu['Allgemeines']['Startseite']['level']=0;

	$navmenu['Allgemeines']['Ingame-News']['page']="home";
	$navmenu['Allgemeines']['Ingame-News']['sub']="ingamenews";
	$navmenu['Allgemeines']['Ingame-News']['level']=0;

	$navmenu['Allgemeines']['Systemnachricht']['page']="home";
	$navmenu['Allgemeines']['Systemnachricht']['sub']="systemmessage";
	$navmenu['Allgemeines']['Systemnachricht']['level']=0;

	$navmenu['Allgemeines']['Rangliste']['page']="home";
	$navmenu['Allgemeines']['Rangliste']['sub']="stats";
	$navmenu['Allgemeines']['Rangliste']['level']=0;

	$navmenu['Allgemeines']['RSS']['page']="home";
	$navmenu['Allgemeines']['RSS']['sub']="rss";
	$navmenu['Allgemeines']['RSS']['level']=0;

	$navmenu['Allgemeines']['Tipps']['page']="home";
	$navmenu['Allgemeines']['Tipps']['sub']="tipps";
	$navmenu['Allgemeines']['Tipps']['level']=0;

	$navmenu['Allgemeines']['Offline nehmen']['page']="home";
	$navmenu['Allgemeines']['Offline nehmen']['sub']="offline";
	$navmenu['Allgemeines']['Offline nehmen']['level']=1;

	$navmenu['Allgemeines']['Filesharing']['page']="home";
	$navmenu['Allgemeines']['Filesharing']['sub']="filesharing";
	$navmenu['Allgemeines']['Filesharing']['level']=1;


	$navmenu['Allgemeines']['bar'][0] = true;

	$navmenu['Allgemeines']['Admin-News']['page']="home";
	$navmenu['Allgemeines']['Admin-News']['sub']="adminnews";
	$navmenu['Allgemeines']['Admin-News']['level']=2;

	$navmenu['Allgemeines']['Admin-Management']['page']="home";
	$navmenu['Allgemeines']['Admin-Management']['sub']="adminusers";
	$navmenu['Allgemeines']['Admin-Management']['level']=2;

	$navmenu['Allgemeines']['Admin-Sessionlog']['page']="home";
	$navmenu['Allgemeines']['Admin-Sessionlog']['sub']="adminlog";
	$navmenu['Allgemeines']['Admin-Sessionlog']['level']=2;

	$navmenu['In-Game Hilfe']['Übersicht']['page']="help";
	$navmenu['In-Game Hilfe']['Übersicht']['sub']="";
	$navmenu['In-Game Hilfe']['Übersicht']['level']=0;
	
	$navmenu['In-Game Hilfe']['Technikbaum']['page']="help";
	$navmenu['In-Game Hilfe']['Technikbaum']['sub']="techtree";
	$navmenu['In-Game Hilfe']['Technikbaum']['level']=1;
	

	$navmenu['Spieler']['Spieler bearbeiten']['page']="user";
	$navmenu['Spieler']['Spieler bearbeiten']['sub']="";
	$navmenu['Spieler']['Spieler bearbeiten']['level']=0;

	$navmenu['Spieler']['Spieler erstellen']['page']="user";
	$navmenu['Spieler']['Spieler erstellen']['sub']="create";
	$navmenu['Spieler']['Spieler erstellen']['level']=0;

	$navmenu['Spieler']['Multi-Kontrolle']['page']="user";
	$navmenu['Spieler']['Multi-Kontrolle']['sub']="multi";
	$navmenu['Spieler']['Multi-Kontrolle']['level']=0;

	$navmenu['Spieler']['Sitting']['page']="user";
	$navmenu['Spieler']['Sitting']['sub']="sitting";
	$navmenu['Spieler']['Sitting']['level']=0;

	$navmenu['Spieler']['Punkteverlauf']['page']="user";
	$navmenu['Spieler']['Punkteverlauf']['sub']="point";
	$navmenu['Spieler']['Punkteverlauf']['level']=0;

	$navmenu['Spieler']['Sessionlogs']['page']="user";
	$navmenu['Spieler']['Sessionlogs']['sub']="userlog";
	$navmenu['Spieler']['Sessionlogs']['level']=1;

	$navmenu['Spieler']['&Auml;nderungsantr&auml;ge']['page']="user";
	$navmenu['Spieler']['&Auml;nderungsantr&auml;ge']['sub']="requests";
	$navmenu['Spieler']['&Auml;nderungsantr&auml;ge']['level']=0;

	$navmenu['Spieler']['Profilbilder pr&uuml;fen']['page']="user";
	$navmenu['Spieler']['Profilbilder pr&uuml;fen']['sub']="imagecheck";
	$navmenu['Spieler']['Profilbilder pr&uuml;fen']['level']=0;

	$navmenu['Spieler']['Fehlerhafte Logins']['page']="user";
	$navmenu['Spieler']['Fehlerhafte Logins']['sub']="loginfailures";
	$navmenu['Spieler']['Fehlerhafte Logins']['level']=1;

	$navmenu['Spieler']['Beobachter']['page']="user";
	$navmenu['Spieler']['Beobachter']['sub']="observed";
	$navmenu['Spieler']['Beobachter']['level']=1;

	$navmenu['Spieler']['Verwarnungen']['page']="user";
	$navmenu['Spieler']['Verwarnungen']['sub']="warnings";
	$navmenu['Spieler']['Verwarnungen']['level']=1;

	$navmenu['Spieler']['Tickets']['page']="user";
	$navmenu['Spieler']['Tickets']['sub']="tickets";
	$navmenu['Spieler']['Tickets']['level']=1;

	$navmenu['Spieler']['XML-Export/Import']['page']="user";
	$navmenu['Spieler']['XML-Export/Import']['sub']="xml";
	$navmenu['Spieler']['XML-Export/Import']['level']=1;

	$navmenu['Spieler']['Userstatistiken']['page']="user";
	$navmenu['Spieler']['Userstatistiken']['sub']="userstats";
	$navmenu['Spieler']['Userstatistiken']['level']=0;

	$navmenu['Spieler']['bar'][0] = true;

	$navmenu['Spieler']['Rassen']['page']="user";
	$navmenu['Spieler']['Rassen']['sub']="race";
	$navmenu['Spieler']['Rassen']['level']=2;

	$navmenu['Spieler']['Spezialisten']['page']="user";
	$navmenu['Spieler']['Spezialisten']['sub']="specialists";
	$navmenu['Spieler']['Spezialisten']['level']=2;


	$navmenu['Allianzen']['Allianzen bearbeiten']['page']="alliances";
	$navmenu['Allianzen']['Allianzen bearbeiten']['sub']="";
	$navmenu['Allianzen']['Allianzen bearbeiten']['level']=0;

	$navmenu['Allianzen']['Fehlerhafte Daten']['page']="alliances";
	$navmenu['Allianzen']['Fehlerhafte Daten']['sub']="crab";
	$navmenu['Allianzen']['Fehlerhafte Daten']['level']=1;

	$navmenu['Allianzen']['Allianz-News (Rathaus)']['page']="alliances";
	$navmenu['Allianzen']['Allianz-News (Rathaus)']['sub']="news";
	$navmenu['Allianzen']['Allianz-News (Rathaus)']['level']=0;

	$navmenu['Allianzen']['Allianz-Geschichte']['page']="alliances";
	$navmenu['Allianzen']['Allianz-Geschichte']['sub']="history";
	$navmenu['Allianzen']['Allianz-Geschichte']['level']=0;

	$navmenu['Allianzen']['Bilder pr&uuml;fen']['page']="alliances";
	$navmenu['Allianzen']['Bilder pr&uuml;fen']['sub']="imagecheck";
	$navmenu['Allianzen']['Bilder pr&uuml;fen']['level']=0;

	$navmenu['Geb&auml;ude']['Liste']['page']="buildings";
	$navmenu['Geb&auml;ude']['Liste']['sub']="";
	$navmenu['Geb&auml;ude']['Liste']['level']=1;

	$navmenu['Geb&auml;ude']['Preisrechner']['page']="buildings";
	$navmenu['Geb&auml;ude']['Preisrechner']['sub']="prices";
	$navmenu['Geb&auml;ude']['Preisrechner']['level']=0;

	$navmenu['Geb&auml;ude']['bar'][0] = true;

	$navmenu['Geb&auml;ude']['Geb&auml;ude bearbeiten']['page']="buildings";
	$navmenu['Geb&auml;ude']['Geb&auml;ude bearbeiten']['sub']="data";
	$navmenu['Geb&auml;ude']['Geb&auml;ude bearbeiten']['level']=2;

	$navmenu['Geb&auml;ude']['Kategorien']['page']="buildings";
	$navmenu['Geb&auml;ude']['Kategorien']['sub']="type";
	$navmenu['Geb&auml;ude']['Kategorien']['level']=2;

	$navmenu['Geb&auml;ude']['Voraussetzungen']['page']="buildings";
	$navmenu['Geb&auml;ude']['Voraussetzungen']['sub']="req";
	$navmenu['Geb&auml;ude']['Voraussetzungen']['level']=2;

	$navmenu['Geb&auml;ude']['Geb&auml;udepunkte']['page']="buildings";
	$navmenu['Geb&auml;ude']['Geb&auml;udepunkte']['sub']="points";
	$navmenu['Geb&auml;ude']['Geb&auml;udepunkte']['level']=2;

	$navmenu['Forschung']['Liste']['page']="techs";
	$navmenu['Forschung']['Liste']['sub']="";
	$navmenu['Forschung']['Liste']['level']=1;

	$navmenu['Forschung']['bar'][0] = true;

	$navmenu['Forschung']['Technologien bearbeiten']['page']="techs";
	$navmenu['Forschung']['Technologien bearbeiten']['sub']="data";
	$navmenu['Forschung']['Technologien bearbeiten']['level']=2;

	$navmenu['Forschung']['Kategorien']['page']="techs";
	$navmenu['Forschung']['Kategorien']['sub']="type";
	$navmenu['Forschung']['Kategorien']['level']=2;

	$navmenu['Forschung']['Voraussetzungen']['page']="techs";
	$navmenu['Forschung']['Voraussetzungen']['sub']="req";
	$navmenu['Forschung']['Voraussetzungen']['level']=2;

	$navmenu['Forschung']['Forschungspunkte']['page']="techs";
	$navmenu['Forschung']['Forschungspunkte']['sub']="points";
	$navmenu['Forschung']['Forschungspunkte']['level']=2;


	$navmenu['Schiffe']['Liste']['page']="ships";
	$navmenu['Schiffe']['Liste']['sub']="";
	$navmenu['Schiffe']['Liste']['level']=1;

	$navmenu['Schiffe']['Bauliste']['page']="ships";
	$navmenu['Schiffe']['Bauliste']['sub']="queue";
	$navmenu['Schiffe']['Bauliste']['level']=1;

	$navmenu['Schiffe']['XP-Rechner']['page']="ships";
	$navmenu['Schiffe']['XP-Rechner']['sub']="xpcalc";
	$navmenu['Schiffe']['XP-Rechner']['level']=1;

	$navmenu['Schiffe']['bar'][0] = true;

	$navmenu['Schiffe']['Schiffe bearbeiten']['page']="ships";
	$navmenu['Schiffe']['Schiffe bearbeiten']['sub']="data";
	$navmenu['Schiffe']['Schiffe bearbeiten']['level']=2;

	$navmenu['Schiffe']['Voraussetzungen']['page']="ships";
	$navmenu['Schiffe']['Voraussetzungen']['sub']="req";
	$navmenu['Schiffe']['Voraussetzungen']['level']=2;

	$navmenu['Schiffe']['Kategorien']['page']="ships";
	$navmenu['Schiffe']['Kategorien']['sub']="cat";
	$navmenu['Schiffe']['Kategorien']['level']=2;

	$navmenu['Schiffe']['Punkte']['page']="ships";
	$navmenu['Schiffe']['Punkte']['sub']="battlepoints";
	$navmenu['Schiffe']['Punkte']['level']=2;


	$navmenu['Flotten']['Flotten']['page']="fleets";
	$navmenu['Flotten']['Flotten']['sub']="";
	$navmenu['Flotten']['Flotten']['level']=1;

	$navmenu['Flotten']['Flottenoptionen']['page']="fleets";
	$navmenu['Flotten']['Flottenoptionen']['sub']="fleetoptions";
	$navmenu['Flotten']['Flottenoptionen']['level']=1;

	$navmenu['Verteidigung']['Liste']['page']="def";
	$navmenu['Verteidigung']['Liste']['sub']="";
	$navmenu['Verteidigung']['Liste']['level']=1;

	$navmenu['Verteidigung']['bar'][0] = true;

	$navmenu['Verteidigung']['Verteidigung bearbeiten']['page']="def";
	$navmenu['Verteidigung']['Verteidigung bearbeiten']['sub']="data";
	$navmenu['Verteidigung']['Verteidigung bearbeiten']['level']=2;

	$navmenu['Verteidigung']['Voraussetzungen']['page']="def";
	$navmenu['Verteidigung']['Voraussetzungen']['sub']="req";
	$navmenu['Verteidigung']['Voraussetzungen']['level']=2;

	$navmenu['Verteidigung']['Kategorien']['page']="def";
	$navmenu['Verteidigung']['Kategorien']['sub']="cat";
	$navmenu['Verteidigung']['Kategorien']['level']=2;	

	$navmenu['Verteidigung']['Punkte']['page']="def";
	$navmenu['Verteidigung']['Punkte']['sub']="battlepoints";
	$navmenu['Verteidigung']['Punkte']['level']=2;

	$navmenu['Raketen']['Liste']['page']="missiles";
	$navmenu['Raketen']['Liste']['sub']="";
	$navmenu['Raketen']['Liste']['level']=2;

	$navmenu['Raketen']['bar'][0] = true;

	$navmenu['Raketen']['Bearbeiten']['page']="missiles";
	$navmenu['Raketen']['Bearbeiten']['sub']="data";
	$navmenu['Raketen']['Bearbeiten']['level']=2;

	$navmenu['Raketen']['Voraussetzungen']['page']="missiles";
	$navmenu['Raketen']['Voraussetzungen']['sub']="req";
	$navmenu['Raketen']['Voraussetzungen']['level']=2;


	$navmenu['Ereignisse']['Vorlagen']['page']="events";
	$navmenu['Ereignisse']['Vorlagen']['sub']="";
	$navmenu['Ereignisse']['Vorlagen']['level']=1;

/*

	$navmenu['Ereignisse']['Aktuelle Ereignisse']['page']="events";
	$navmenu['Ereignisse']['Aktuelle Ereignisse']['sub']="exec";
	$navmenu['Ereignisse']['Aktuelle Ereignisse']['level']=1;

	$navmenu['Ereignisse']['Ereignisse testen']['page']="events";
	$navmenu['Ereignisse']['Ereignisse testen']['sub']="test";
	$navmenu['Ereignisse']['Ereignisse testen']['level']=0;
	
	
	$navmenu['Minimap']['Minimap bearbeiten']['page']="minimap";
	$navmenu['Minimap']['Minimap bearbeiten']['sub']="";
	$navmenu['Minimap']['Minimap bearbeiten']['level']=1;
	
	$navmenu['Minimap']['Events']['page']="minimap";
	$navmenu['Minimap']['Events']['sub']="events";
	$navmenu['Minimap']['Events']['level']=1;

*/

	$navmenu['Galaxie']['Entitäten']['page']="galaxy";
	$navmenu['Galaxie']['Entitäten']['sub']="";
	$navmenu['Galaxie']['Entitäten']['level']=1;

//	$navmenu['Galaxie']['Zellen']['page']="galaxy";
//	$navmenu['Galaxie']['Zellen']['sub']="cells";
//	$navmenu['Galaxie']['Zellen']['level']=1;
//
	$navmenu['Galaxie']['Karte']['page']="galaxy";
	$navmenu['Galaxie']['Karte']['sub']="map";
	$navmenu['Galaxie']['Karte']['level']=1;


	$navmenu['Galaxie']['Planetenbesitzer pr&uuml;fen']['page']="galaxy";
	$navmenu['Galaxie']['Planetenbesitzer pr&uuml;fen']['sub']="planetcheck";
	$navmenu['Galaxie']['Planetenbesitzer pr&uuml;fen']['level']=1;

	$navmenu['Galaxie']['Integrität prüfen']['page']="galaxy";
	$navmenu['Galaxie']['Integrität prüfen']['sub']="galaxycheck";
	$navmenu['Galaxie']['Integrität prüfen']['level']=1;


	$navmenu['Galaxie']['bar'][0] = true;

	$navmenu['Galaxie']['Planetentypen']['page']="galaxy";
	$navmenu['Galaxie']['Planetentypen']['sub']="planet_types";
	$navmenu['Galaxie']['Planetentypen']['level']=2;

	$navmenu['Galaxie']['Sonnentypen']['page']="galaxy";
	$navmenu['Galaxie']['Sonnentypen']['sub']="sol_types";
	$navmenu['Galaxie']['Sonnentypen']['level']=2;



	$navmenu['Nachrichten']['Nachrichten verwalten']['page']="messages";
	$navmenu['Nachrichten']['Nachrichten verwalten']['sub']="";
	$navmenu['Nachrichten']['Nachrichten verwalten']['level']=1;

	$navmenu['Nachrichten']['Nachricht senden']['page']="messages";
	$navmenu['Nachrichten']['Nachricht senden']['sub']="sendmsg";
	$navmenu['Nachrichten']['Nachricht senden']['level']=0;

	$navmenu['Nachrichten']['Rundmail (IGM)']['page']="messages";
	$navmenu['Nachrichten']['Rundmail (IGM)']['sub']="infomail";
	$navmenu['Nachrichten']['Rundmail (IGM)']['level']=1;

	$navmenu['Nachrichten']['E-Mail verschicken']['page']="messages";
	$navmenu['Nachrichten']['E-Mail verschicken']['sub']="email";
	$navmenu['Nachrichten']['E-Mail verschicken']['level']=2;

	$navmenu['Nachrichten']['E-Mail-Warteschlange']['page']="messages";
	$navmenu['Nachrichten']['E-Mail-Warteschlange']['sub']="queue";
	$navmenu['Nachrichten']['E-Mail-Warteschlange']['level']=2;

	$navmenu['Chat']['Chat']['page']="chat";
	$navmenu['Chat']['Chat']['sub']="";
	$navmenu['Chat']['Chat']['level']=0;


	$navmenu['Logs']['Logs anschauen']['page']="logs";
	$navmenu['Logs']['Logs anschauen']['sub']="";
	$navmenu['Logs']['Logs anschauen']['level']=0;
	
	$navmenu['Logs']['Logs anschauen (new)']['page']="logs";
	$navmenu['Logs']['Logs anschauen (new)']['sub']="new_logs_page";
	$navmenu['Logs']['Logs anschauen (new)']['level']=0;	
	
	$navmenu['Logs']['Angriffsverletzung']['page']="logs";
	$navmenu['Logs']['Angriffsverletzung']['sub']="check_fights";
	$navmenu['Logs']['Angriffsverletzung']['level']=0;

	$navmenu['Marktplatz']['Schiffe']['page']="market";
	$navmenu['Marktplatz']['Schiffe']['sub']="ships";
	$navmenu['Marktplatz']['Schiffe']['level']=1;

	$navmenu['Marktplatz']['Rohstoffe']['page']="market";
	$navmenu['Marktplatz']['Rohstoffe']['sub']="ress";
	$navmenu['Marktplatz']['Rohstoffe']['level']=1;

	$navmenu['Marktplatz']['Auktionen']['page']="market";
	$navmenu['Marktplatz']['Auktionen']['sub']="auction";
	$navmenu['Marktplatz']['Auktionen']['level']=1;

	$navmenu['Datenbank']['Backups']['page']="db";
	$navmenu['Datenbank']['Backups']['sub']="backup";
	$navmenu['Datenbank']['Backups']['level']=2;

	$navmenu['Datenbank']['Clean-Up']['page']="db";
	$navmenu['Datenbank']['Clean-Up']['sub']="cleanup";
	$navmenu['Datenbank']['Clean-Up']['level']=2;

	$navmenu['Datenbank']['Updates']['page']="db";
	$navmenu['Datenbank']['Updates']['sub']="updates";
	$navmenu['Datenbank']['Updates']['level']=2;

	$navmenu['Konfiguration']['Konfiguration']['page']="config";
	$navmenu['Konfiguration']['Konfiguration']['sub']="";
	$navmenu['Konfiguration']['Konfiguration']['level']=3;

	$navmenu['Konfiguration']['Passwort-Schutz']['page']="config";
	$navmenu['Konfiguration']['Passwort-Schutz']['sub']="htaccess";
	$navmenu['Konfiguration']['Passwort-Schutz']['level']=3;

	$navmenu['Konfiguration']['Runde & Universum']['page']="config";
	$navmenu['Konfiguration']['Runde & Universum']['sub']="uni";
	$navmenu['Konfiguration']['Runde & Universum']['level']=3;

	$navmenu['Konfiguration']['Bildpaket-Downloads']['page']="config";
	$navmenu['Konfiguration']['Bildpaket-Downloads']['sub']="imagepacks";
	$navmenu['Konfiguration']['Bildpaket-Downloads']['level']=3;

	$navmenu['Konfiguration']['Start-Items']['page']="config";
	$navmenu['Konfiguration']['Start-Items']['sub']="defaultitems";
	$navmenu['Konfiguration']['Start-Items']['level']=3;



	$navmenu['Tools']['PHP-Infos']['page']="tools";
	$navmenu['Tools']['PHP-Infos']['sub']="php";
	$navmenu['Tools']['PHP-Infos']['level']=3;

	$navmenu['Tools']['IP-Resolver']['page']="tools";
	$navmenu['Tools']['IP-Resolver']['sub']="ipresolver";
	$navmenu['Tools']['IP-Resolver']['level']=1;
	
	$navmenu['Tools']['Kampfsimulator']['page']="tools";
	$navmenu['Tools']['Kampfsimulator']['sub']="battle_simulation";
	$navmenu['Tools']['Kampfsimulator']['level']=2;

?>