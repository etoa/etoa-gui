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
	$navmenu[0]['items'][0] = array ("name"=>"Übersicht","url"=>"?page=overview");
	$navmenu[0]['items'][1] = array ("name"=>"Allianz","url"=>"?page=alliance");
	$navmenu[0]['items'][2] = array ("name"=>"Allianzforum","url"=>"?page=allianceboard");
	$navmenu[0]['items'][3] = array ("name"=>"Ratshaus","url"=>"?page=townhall");
	$navmenu[0]['items'][4] = array ("name"=>"Nachrichten","url"=>"?page=messages");
	$navmenu[0]['items'][5] = array ("name"=>"Statistiken","url"=>"?page=stats");
	$navmenu[0]['items'][6] = array ("name"=>"Raumkarte","url"=>"?page=space");
	$navmenu[0]['items'][7] = array ("name"=>"Flotten","url"=>"?page=fleets");

	$navmenu[1]['cat'] = "Planet";
	$navmenu[1]['items'][0] = array ("name"=>"Informationen","url"=>"?page=planetoverview");
	$navmenu[1]['items'][1] = array ("name"=>"Wirtschaft","url"=>"?page=economy");
	$navmenu[1]['items'][2] = array ("name"=>"Bevölkerung","url"=>"?page=population");
	$navmenu[1]['items'][3] = array ("name"=>"Technikbaum","url"=>"?page=techtree");
	$navmenu[1]['items'][4] = array ("name"=>"Raumschiffhafen","url"=>"?page=haven");
	$navmenu[1]['items'][5] = array ("name"=>"Marktplatz","url"=>"?page=market");
	$navmenu[1]['items'][6] = array ("name"=>"Raketensilo","url"=>"?page=missiles");
	//$navmenu[1]['items'][7] = array ("name"=>"Kryptocenter","url"=>"?page=crypto");

	$navmenu[2]['cat'] = "Produktion";
	$navmenu[2]['items'][0] = array ("name"=>"Bauhof","url"=>"?page=buildings");
	$navmenu[2]['items'][1] = array ("name"=>"Forschungslabor","url"=>"?page=research");
	$navmenu[2]['items'][2] = array ("name"=>"Schiffswerft","url"=>"?page=shipyard");
	$navmenu[2]['items'][3] = array ("name"=>"Waffenfabrik","url"=>"?page=defense");
	$navmenu[2]['items'][4] = array ("name"=>"Schrottplatz","url"=>"?page=recycle");

	$navmenu[3]['cat'] = "Tools";
	$navmenu[3]['items'][0] = array ("name"=>"Buddylist","url"=>"?page=buddylist");
	$navmenu[3]['items'][1] = array ("name"=>"Notizen","url"=>"?page=notepad");
	$navmenu[3]['items'][2] = array ("name"=>"Favoriten","url"=>"?page=bookmarks");
	$navmenu[3]['items'][3] = array ("name"=>"Einstellungen","url"=>"?page=userconfig");
	$navmenu[3]['items'][4] = array ("name"=>"Hilfe","url"=>"?page=help");
	$navmenu[3]['items'][5] = array ("name"=>"Kontakt","url"=>"?page=contact");
	
	// Links des oberen Menüs
	$topnav=array();
	$topnav[0]['name']='Forum';
	$topnav[0]['url']='http://www.etoa.ch/forum';
	$topnav[0]['newwindow']=true;
	$topnav[1]['name']='FAQ';
	$topnav[1]['url']='http://www.etoa.ch/faq';
	$topnav[1]['newwindow']=true;
	$topnav[2]['name']="Regeln";
	$topnav[2]['url']=$conf['url_rules']['v'];
	$topnav[2]['newwindow']=true;
	$topnav[3]['name']="Fehler melden";
	$topnav[3]['url']=$conf['url_bugs']['v'];
	$topnav[3]['newwindow']=true;
	$topnav[4]['name']="Chat";
	$topnav[4]['url']=CHAT_URL;
	$topnav[4]['newwindow']=true;
	$topnav[4]['nw_js']="width=800,height=650,status=no,scrollbars=yes";
	$topnav[5]['name']="TeamSpeak";
	$topnav[5]['url']=$conf['url_teamspeak']['v'];
	$topnav[5]['newwindow']=true;
?>