<?PHP

	$navmenu=array();
	
	$navmenu['Allgemeines'] = array(
		'Startseite' => array('page'=>"overview",'sub'=>"",'level'=>0),
		'Ingame-News' => array('page'=>"overview",'sub'=>"ingamenews",'level'=>0),
		'Systemnachricht' => array('page'=>"overview",'sub'=>"systemmessage",'level'=>0),
	);


	$navmenu['Allgemeines']['Offline nehmen']['page']="overview";
	$navmenu['Allgemeines']['Offline nehmen']['sub']="offline";
	$navmenu['Allgemeines']['Offline nehmen']['level']=1;

	$navmenu['Allgemeines']['Rangliste']['page']="overview";
	$navmenu['Allgemeines']['Rangliste']['sub']="stats";
	$navmenu['Allgemeines']['Rangliste']['level']=0;

	$navmenu['Allgemeines']['Backend-Daemon']['page']="overview";
	$navmenu['Allgemeines']['Backend-Daemon']['sub']="daemon";
	$navmenu['Allgemeines']['Backend-Daemon']['level']=0;

	$navmenu['Allgemeines']['bar'][0] = true;

	$navmenu['Allgemeines']['Admin-News']['page']="overview";
	$navmenu['Allgemeines']['Admin-News']['sub']="adminnews";
	$navmenu['Allgemeines']['Admin-News']['level']=2;

	$navmenu['Allgemeines']['Admin-Management']['page']="overview";
	$navmenu['Allgemeines']['Admin-Management']['sub']="adminusers";
	$navmenu['Allgemeines']['Admin-Management']['level']=2;

	$navmenu['Allgemeines']['Admin-Sessionlog']['page']="overview";
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

	$navmenu['Spieler']['IP-Suche']['page']="user";
	$navmenu['Spieler']['IP-Suche']['sub']="ipsearch";
	$navmenu['Spieler']['IP-Suche']['level']=1;

	$navmenu['Spieler']['Sitting']['page']="user";
	$navmenu['Spieler']['Sitting']['sub']="sitting";
	$navmenu['Spieler']['Sitting']['level']=0;

	$navmenu['Spieler']['Punkteverlauf']['page']="user";
	$navmenu['Spieler']['Punkteverlauf']['sub']="point";
	$navmenu['Spieler']['Punkteverlauf']['level']=0;

	$navmenu['Spieler']['Sessionlogs']['page']="user";
	$navmenu['Spieler']['Sessionlogs']['sub']="userlog";
	$navmenu['Spieler']['Sessionlogs']['level']=1;

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

	$navmenu['Allianzen']['Allianz erstellen']['page']="alliances";
	$navmenu['Allianzen']['Allianz erstellen']['sub']="create";
	$navmenu['Allianzen']['Allianz erstellen']['level']=0;

	$navmenu['Allianzen']['Fehlerhafte Daten']['page']="alliances";
	$navmenu['Allianzen']['Fehlerhafte Daten']['sub']="crap";
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

	$navmenu['Allianzen']['bar'][0] = true;

	$navmenu['Allianzen']['Gebäude bearbeiten']['page']="alliances";
	$navmenu['Allianzen']['Gebäude bearbeiten']['sub']="buildingsdata";
	$navmenu['Allianzen']['Gebäude bearbeiten']['level']=2;

	$navmenu['Allianzen']['Technologien bearbeiten']['page']="alliances";
	$navmenu['Allianzen']['Technologien bearbeiten']['sub']="techdata";
	$navmenu['Allianzen']['Technologien bearbeiten']['level']=2;



	$navmenu['Gebäude']['Liste']['page']="buildings";
	$navmenu['Gebäude']['Liste']['sub']="";
	$navmenu['Gebäude']['Liste']['level']=1;

	$navmenu['Gebäude']['Preisrechner']['page']="buildings";
	$navmenu['Gebäude']['Preisrechner']['sub']="prices";
	$navmenu['Gebäude']['Preisrechner']['level']=0;

	$navmenu['Gebäude']['bar'][0] = true;

	$navmenu['Gebäude']['Gebäude bearbeiten']['page']="buildings";
	$navmenu['Gebäude']['Gebäude bearbeiten']['sub']="data";
	$navmenu['Gebäude']['Gebäude bearbeiten']['level']=2;

	$navmenu['Gebäude']['Kategorien']['page']="buildings";
	$navmenu['Gebäude']['Kategorien']['sub']="type";
	$navmenu['Gebäude']['Kategorien']['level']=2;

	$navmenu['Gebäude']['Voraussetzungen']['page']="buildings";
	$navmenu['Gebäude']['Voraussetzungen']['sub']="req";
	$navmenu['Gebäude']['Voraussetzungen']['level']=2;

	$navmenu['Gebäude']['Gebäudepunkte']['page']="buildings";
	$navmenu['Gebäude']['Gebäudepunkte']['sub']="points";
	$navmenu['Gebäude']['Gebäudepunkte']['level']=2;

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
	
	$navmenu['Verteidigung']['Bauliste']['page']="def";
	$navmenu['Verteidigung']['Bauliste']['sub']="queue";
	$navmenu['Verteidigung']['Bauliste']['level']=1;	

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

	$navmenu['Verteidigung']['Transformationen']['page']="def";
	$navmenu['Verteidigung']['Transformationen']['sub']="transforms";
	$navmenu['Verteidigung']['Transformationen']['level']=2;


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
	
	

*/

	$navmenu['Galaxie']['Entitäten']['page']="galaxy";
	$navmenu['Galaxie']['Entitäten']['sub']="";
	$navmenu['Galaxie']['Entitäten']['level']=1;

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



	$res=dbquery("SELECT cat_name,cat_id,COUNT(*) as cnt FROM config_cat,config WHERE cat_id=config_cat_id GROUP BY cat_id ORDER BY cat_order,cat_name;");
	if (mysql_num_rows($res)>0)
	{
		while ($arr=mysql_fetch_array($res))
		{
			$navmenu['Konfiguration'][$arr['cat_name']]['page']="config";
			$navmenu['Konfiguration'][$arr['cat_name']]['sub']=$arr['cat_id'];
			$navmenu['Konfiguration'][$arr['cat_name']]['level']=3;			
		}
	}

	$navmenu['Konfiguration']['Variable erstellen'] = array('page'=>"config",'sub'=>"createvar",'level'=>3);

	$navmenu['Konfiguration']['bar'][0] = true;

	$navmenu['Konfiguration']['Tipps'] = array('page'=>"config",'sub'=>"tipps",'level'=>0);
	$navmenu['Konfiguration']['Ticket-Kategorien'] = array('page'=>"config",'sub'=>"ticketcat",'level'=>3);

	$navmenu['Setup']['Universum']['page']="setup";
	$navmenu['Setup']['Universum']['sub']="uni";
	$navmenu['Setup']['Universum']['level']=3;

	$navmenu['Setup']['Bildpakete']['page']="setup";
	$navmenu['Setup']['Bildpakete']['sub']="imagepacks";
	$navmenu['Setup']['Bildpakete']['level']=3;

	$navmenu['Setup']['Cronjob']['page']="setup";
	$navmenu['Setup']['Cronjob']['sub']="cronjob";
	$navmenu['Setup']['Cronjob']['level']=2;

	$navmenu['Setup']['Start-Items']['page']="setup";
	$navmenu['Setup']['Start-Items']['sub']="defaultitems";
	$navmenu['Setup']['Start-Items']['level']=3;
	

	$navmenu['Tools']['Datei-Austausch']['page']="tools";
	$navmenu['Tools']['Datei-Austausch']['sub']="filesharing";
	$navmenu['Tools']['Datei-Austausch']['level']=1;

	$navmenu['Tools']['PHP-Infos']['page']="tools";
	$navmenu['Tools']['PHP-Infos']['sub']="php";
	$navmenu['Tools']['PHP-Infos']['level']=3;

	$navmenu['Tools']['Game-Statistik']['page']="tools";
	$navmenu['Tools']['Game-Statistik']['sub']="gamestats";
	$navmenu['Tools']['Game-Statistik']['level']=3;

	$navmenu['Tools']['IP-Resolver']['page']="tools";
	$navmenu['Tools']['IP-Resolver']['sub']="ipresolver";
	$navmenu['Tools']['IP-Resolver']['level']=1;
	

?>