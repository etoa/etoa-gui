<?PHP
 /**
 * Default-Style navigation
 *
 * @author MrCage mrcage@etoa.ch
 * @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
 */	

	// Links des linken Menüs
	$navmenu=array();
	$navmenu[0]['cat'] = "Allgemeines";
	$navmenu[0]['items'][] = array ("name"=>"Übersicht","url"=>"?page=overview");
	$navmenu[0]['items'][] = array ("name"=>"Raumkarte","url"=>"?page=map");
	$navmenu[0]['items'][] = array ("name"=>"Sonnensystem","url"=>"?page=cell");
	$navmenu[0]['items'][] = array ("name"=>"Flotten","url"=>"?page=fleets");
	$navmenu[0]['items'][] = array ("name"=>"Statistiken","url"=>"?page=stats");
	$navmenu[0]['items'][] = array ("name"=>"Wirtschaftsübersicht","url"=>"?page=planetstats");
	$navmenu[0]['items'][] = array ("name"=>"Schiffsübersicht","url"=>"?page=fleetstats");
	$navmenu[0]['items'][] = array ("name"=>"Spezialisten","url"=>"?page=specialists");

	$navmenu[1]['cat'] = "Kommunikation";
	$navmenu[1]['items'][] = array ("name"=>"Allianz","url"=>"?page=alliance");
	$navmenu[1]['items'][] = array ("name"=>"Allianzforum","url"=>"?page=allianceboard");
	$navmenu[1]['items'][] = array ("name"=>"Ratshaus","url"=>"?page=townhall");
	$navmenu[1]['items'][] = array ("name"=>"Nachrichten","url"=>"?page=messages");

	$navmenu[2]['cat'] = "Planet";
	$navmenu[2]['items'][] = array ("name"=>"Informationen","url"=>"?page=planetoverview");
	$navmenu[2]['items'][] = array ("name"=>"Wirtschaft","url"=>"?page=economy");
	$navmenu[2]['items'][] = array ("name"=>"Bevölkerung","url"=>"?page=population");
	$navmenu[2]['items'][] = array ("name"=>"Technikbaum","url"=>"?page=techtree");
	$navmenu[2]['items'][] = array ("name"=>"Raumschiffhafen","url"=>"?page=haven");
	$navmenu[2]['items'][] = array ("name"=>"Marktplatz","url"=>"?page=market");
	$navmenu[2]['items'][] = array ("name"=>"Raketensilo","url"=>"?page=missiles");
	$navmenu[2]['items'][] = array ("name"=>"Kryptocenter","url"=>"?page=crypto");

	$navmenu[3]['cat'] = "Produktion";
	$navmenu[3]['items'][] = array ("name"=>"Bauhof","url"=>"?page=buildings");
	$navmenu[3]['items'][] = array ("name"=>"Forschungslabor","url"=>"?page=research");
	$navmenu[3]['items'][] = array ("name"=>"Schiffswerft","url"=>"?page=shipyard");
	$navmenu[3]['items'][] = array ("name"=>"Waffenfabrik","url"=>"?page=defense");
	$navmenu[3]['items'][] = array ("name"=>"Schrottplatz","url"=>"?page=recycle");

	$navmenu[4]['cat'] = "Tools";
	$navmenu[4]['items'][] = array ("name"=>"Buddylist","url"=>"?page=buddylist");
	$navmenu[4]['items'][] = array ("name"=>"Notizen","url"=>"?page=notepad");
	$navmenu[4]['items'][] = array ("name"=>"Favoriten","url"=>"?page=bookmarks");
	$navmenu[4]['items'][] = array ("name"=>"Mein Profil","url"=>"?page=userinfo");
	$navmenu[4]['items'][] = array ("name"=>"Einstellungen","url"=>"?page=userconfig");
	$navmenu[4]['items'][] = array ("name"=>"Hilfe","url"=>"?page=help");
	$navmenu[4]['items'][] = array ("name"=>"Kontakt","url"=>"?page=contact");
	
	// Links des oberen Menüs
	$topnav=array();

	$topnav[0]['name']='Forum';
	$topnav[0]['url']=FORUM_PATH;

	$topnav[1]['name']='Helpcenter';
	$topnav[1]['url']=HELPCENTER_URL;
	$topnav[1]['onclick']=HELPCENTER_ONCLICK;
	
	$topnav[2]['name']="Regeln";
	$topnav[2]['url']=RULES_URL;
	$topnav[2]['onclick']=RULES_ONCLICK;

	$topnav[3]['name']="Fehler melden";
	$topnav[3]['url']=DEVCENTER_PATH;
	$topnav[3]['onclick']=DEVCENTER_ONCLICK;

	$topnav[4]['name']="Chat";
	$topnav[4]['url']=CHAT_URL;
	$topnav[4]['onclick']=CHAT_ONCLICK;

	$topnav[5]['name']="TeamSpeak";
	$topnav[5]['url']=TEAMSPEAK_URL;
	$topnav[5]['onclick']=TEAMSPEAK_ONCLICK;
?>