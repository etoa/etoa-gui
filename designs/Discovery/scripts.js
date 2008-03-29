// Tippmessage-Stil
//The Style array parameters come in the following order 
//stl=[titleColor,TitleBgColor,TitleBgImag,TitleTextAlign,TitleFontFace,TitleFontSize,TextColor,TextBgColor,TextBgImag,TextTextAlign,TextFontFace,TextFontSize,Width,Height,BorderSize,BorderColor,Textpadding,transition number,Transition duration,Transparency level,shadow type,shadow color,Appearance behavior,TipPositionType,Xpos,Ypos]
stl=["white","##222255","","","",,"white","#606578","","","",,,,2,"#222255",2,,,,,"",,,,]
tooltipstyle=["#000","#BBBBBB","","","",,"white","#111111","","","",,,,2,"#BBBBBB",2,,,,,"",,,,]

userAgent = window.navigator.userAgent;
browserVers = parseInt(userAgent.charAt(userAgent.indexOf("/")+1),10);
function newImage(arg) {
	if (document.images) {
		rslt = new Image();
		rslt.src = arg;
		return rslt;
	}
}

function findElement(n,ly) {
	if (browserVers < 4)		return document[n];
	var curDoc = ly ? ly.document : document;
	var elem = curDoc[n];
	if (!elem) {
		for (var i=0;i<curDoc.layers.length;i++) {
			elem = findElement(n,curDoc.layers[i]);
			if (elem) return elem;
		}
	}
	return elem;
}

function changeImages() {
	//window.alert('test');
	if (document.images && (preloadFlag == true)) {
		var img;
		for (var i=0; i<changeImages.arguments.length; i+=2) {
			img = null;
			if (document.layers) {
				img = findElement(changeImages.arguments[i],0);
			}
			else {
				img = document.images[changeImages.arguments[i]];
			}
			if (img) {
				img.src = changeImages.arguments[i+1];
			}
		}
	}
}

// Set this to true (temporarily fix)
var preloadFlag = true;
function preloadImages() 
{
	if (document.images) 
	{
		TopPanel_02_imap_forum_over = newImage("designs/Discovery/images/TopPanel_02-imap_forum_over.gif");
		TopPanel_02_imap_forum_down = newImage("designs/Discovery/images/TopPanel_02-imap_forum_down.gif");
		TopPanel_02_imap_forum_sel_item_forum = newImage("designs/Discovery/images/TopPanel_02-imap_forum_sel_item_forum.gif");
		TopPanel_02_imap_statistiken_over = newImage("designs/Discovery/images/TopPanel_02-imap_statistiken_over.gif");
		TopPanel_02_imap_statistiken_down = newImage("designs/Discovery/images/TopPanel_02-imap_statistiken_down.gif");
		TopPanel_02_imap_statistiken_sel_item_statistiken = newImage("designs/Discovery/images/TopPanel_02-imap_statistiken_sel_item_statistiken.gif");
		TopPanel_03_imap_chat_over = newImage("designs/Discovery/images/TopPanel_03-imap_chat_over.gif");
		TopPanel_03_imap_chat_down = newImage("designs/Discovery/images/TopPanel_03-imap_chat_down.gif");
		TopPanel_03_imap_chat_sel_item_chat = newImage("designs/Discovery/images/TopPanel_03-imap_chat_sel_item_chat.gif");
		TopPanel_03_imap_forum_over = newImage("designs/Discovery/images/TopPanel_03-imap_forum_over.gif");
		TopPanel_03_imap_forum_down = newImage("designs/Discovery/images/TopPanel_03-imap_forum_down.gif");
		TopPanel_03_imap_forum_sel_item_forum = newImage("designs/Discovery/images/TopPanel_03-imap_forum_sel_item_forum.gif");
		TopPanel_03_imap_teamspeak_over = newImage("designs/Discovery/images/TopPanel_03-imap_teamspeak_over.gif");
		TopPanel_03_imap_teamspeak_down = newImage("designs/Discovery/images/TopPanel_03-imap_teamspeak_down.gif");
		TopPanel_03_imap_teamspeak_sel_item_teamspeak = newImage("designs/Discovery/images/TopPanel_03-imap_teamspeak_sel_item_teamspeak.gif");
		TopPanel_04_imap_einstellungen_over = newImage("designs/Discovery/images/TopPanel_04-imap_einstellungen_over.gif");
		TopPanel_04_imap_einstellungen_down = newImage("designs/Discovery/images/TopPanel_04-imap_einstellungen_down.gif");
		TopPanel_04_imap_einstellungen_sel_item_einstellungen = newImage("designs/Discovery/images/TopPanel_04-imap_einstellungen_sel_item_einstellungen.gif");
		TopPanel_04_imap_regeln_over = newImage("designs/Discovery/images/TopPanel_04-imap_regeln_over.gif");
		TopPanel_04_imap_regeln_down = newImage("designs/Discovery/images/TopPanel_04-imap_regeln_down.gif");
		TopPanel_04_imap_regeln_sel_item_regeln = newImage("designs/Discovery/images/TopPanel_04-imap_regeln_sel_item_regeln.gif");
		TopPanel_04_imap_teamspeak_over = newImage("designs/Discovery/images/TopPanel_04-imap_teamspeak_over.gif");
		TopPanel_04_imap_teamspeak_down = newImage("designs/Discovery/images/TopPanel_04-imap_teamspeak_down.gif");
		TopPanel_04_imap_teamspeak_sel_item_teamspeak = newImage("designs/Discovery/images/TopPanel_04-imap_teamspeak_sel_item_teamspeak.gif");
		TopPanel_05_imap_helpcenter_over = newImage("designs/Discovery/images/TopPanel_05-imap_helpcenter_over.gif");
		TopPanel_05_imap_helpcenter_down = newImage("designs/Discovery/images/TopPanel_05-imap_helpcenter_down.gif");
		TopPanel_05_imap_helpcenter_sel_item_helpcenter = newImage("designs/Discovery/images/TopPanel_05-imap_helpcenter_sel_item_helpcenter.gif");
		TopPanel_05_imap_logout_over = newImage("designs/Discovery/images/TopPanel_05-imap_logout_over.gif");
		TopPanel_05_imap_logout_down = newImage("designs/Discovery/images/TopPanel_05-imap_logout_down.gif");
		TopPanel_05_imap_logout_up = newImage("designs/Discovery/images/TopPanel_05-imap_logout_up.gif");
		TopPanel_05_imap_regeln_over = newImage("designs/Discovery/images/TopPanel_05-imap_regeln_over.gif");
		TopPanel_05_imap_regeln_down = newImage("designs/Discovery/images/TopPanel_05-imap_regeln_down.gif");
		TopPanel_05_imap_regeln_sel_item_regeln = newImage("designs/Discovery/images/TopPanel_05-imap_regeln_sel_item_regeln.gif");
		TopPanel_06_imap_forum_over = newImage("designs/Discovery/images/TopPanel_06-imap_forum_over.gif");
		TopPanel_06_imap_forum_down = newImage("designs/Discovery/images/TopPanel_06-imap_forum_down.gif");
		TopPanel_06_imap_forum_sel_item_forum = newImage("designs/Discovery/images/TopPanel_06-imap_forum_sel_item_forum.gif");
		TopPanel_06_imap_statistiken_over = newImage("designs/Discovery/images/TopPanel_06-imap_statistiken_over.gif");
		TopPanel_06_imap_statistiken_down = newImage("designs/Discovery/images/TopPanel_06-imap_statistiken_down.gif");
		TopPanel_06_imap_statistiken_sel_item_statistiken = newImage("designs/Discovery/images/TopPanel_06-imap_statistiken_sel_item_statistiken.gif");
		TopPanel_07_imap_chat_over = newImage("designs/Discovery/images/TopPanel_07-imap_chat_over.gif");
		TopPanel_07_imap_chat_down = newImage("designs/Discovery/images/TopPanel_07-imap_chat_down.gif");
		TopPanel_07_imap_chat_sel_item_chat = newImage("designs/Discovery/images/TopPanel_07-imap_chat_sel_item_chat.gif");
		TopPanel_07_imap_forum_over = newImage("designs/Discovery/images/TopPanel_07-imap_forum_over.gif");
		TopPanel_07_imap_forum_down = newImage("designs/Discovery/images/TopPanel_07-imap_forum_down.gif");
		TopPanel_07_imap_forum_sel_item_forum = newImage("designs/Discovery/images/TopPanel_07-imap_forum_sel_item_forum.gif");
		TopPanel_07_imap_teamspeak_over = newImage("designs/Discovery/images/TopPanel_07-imap_teamspeak_over.gif");
		TopPanel_07_imap_teamspeak_down = newImage("designs/Discovery/images/TopPanel_07-imap_teamspeak_down.gif");
		TopPanel_07_imap_teamspeak_sel_item_teamspeak = newImage("designs/Discovery/images/TopPanel_07-imap_teamspeak_sel_item_teamspeak.gif");
		TopPanel_08_imap_einstellungen_over = newImage("designs/Discovery/images/TopPanel_08-imap_einstellungen_over.gif");
		TopPanel_08_imap_einstellungen_down = newImage("designs/Discovery/images/TopPanel_08-imap_einstellungen_down.gif");
		TopPanel_08_imap_einstellungen_sel_item_einstellungen = newImage("designs/Discovery/images/TopPanel_08-imap_einstellungen_sel_item_einstellungen.gif");
		TopPanel_08_imap_regeln_over = newImage("designs/Discovery/images/TopPanel_08-imap_regeln_over.gif");
		TopPanel_08_imap_regeln_down = newImage("designs/Discovery/images/TopPanel_08-imap_regeln_down.gif");
		TopPanel_08_imap_regeln_sel_item_regeln = newImage("designs/Discovery/images/TopPanel_08-imap_regeln_sel_item_regeln.gif");
		TopPanel_08_imap_teamspeak_over = newImage("designs/Discovery/images/TopPanel_08-imap_teamspeak_over.gif");
		TopPanel_08_imap_teamspeak_down = newImage("designs/Discovery/images/TopPanel_08-imap_teamspeak_down.gif");
		TopPanel_08_imap_teamspeak_sel_item_teamspeak = newImage("designs/Discovery/images/TopPanel_08-imap_teamspeak_sel_item_teamspeak.gif");
		TopPanel_09_imap_helpcenter_over = newImage("designs/Discovery/images/TopPanel_09-imap_helpcenter_over.gif");
		TopPanel_09_imap_helpcenter_down = newImage("designs/Discovery/images/TopPanel_09-imap_helpcenter_down.gif");
		TopPanel_09_imap_helpcenter_sel_item_helpcenter = newImage("designs/Discovery/images/TopPanel_09-imap_helpcenter_sel_item_helpcenter.gif");
		TopPanel_09_imap_regeln_over = newImage("designs/Discovery/images/TopPanel_09-imap_regeln_over.gif");
		TopPanel_09_imap_regeln_down = newImage("designs/Discovery/images/TopPanel_09-imap_regeln_down.gif");
		TopPanel_09_imap_regeln_sel_item_regeln = newImage("designs/Discovery/images/TopPanel_09-imap_regeln_sel_item_regeln.gif");
		TopPanel_09_imap_logout_over = newImage("designs/Discovery/images/TopPanel_09-imap_logout_over.gif");
		TopPanel_09_imap_logout_down = newImage("designs/Discovery/images/TopPanel_09-imap_logout_down.gif");



		pb_info_sp_pb_info_over = newImage("designs/Discovery/images/pb_info-sp_pb_info_over.gif");
		pb_info_sp_pb_info_down = newImage("designs/Discovery/images/pb_info-sp_pb_info_down.gif");
		pb_info_sp_pb_info_sel_item_info = newImage("designs/Discovery/images/pb_info-sp_pb_info_sel_item.gif");
		sp_pb_overview_over = newImage("designs/Discovery/images/sp_pb_overview-over.gif");
		sp_pb_overview_down = newImage("designs/Discovery/images/sp_pb_overview-down.gif");
		sp_pb_overview_sel_item_overview = newImage("designs/Discovery/images/sp_pb_overview-sel_item_ove.gif");
		pb_previousplanet_sp_pb_previousplanet_over = newImage("designs/Discovery/images/pb_previousplanet-sp_pb_pre.gif");
		pb_previousplanet_sp_pb_previousplanet_down = newImage("designs/Discovery/images/pb_previousplanet-sp_pb_-38.gif");
		pb_ddplanets_sp_pb_nextplanet_over = newImage("designs/Discovery/images/pb_ddplanets-sp_pb_nextplan.gif");
		Menueleiste_23_sp_pb_nextplanet_down = newImage("designs/Discovery/images/pb_ddplanets-sp_pb_nextp-44.gif");
		pb_nextplanet_sp_pb_ddplanets_over = newImage("designs/Discovery/images/pb_nextplanet-sp_pb_ddplane.gif");
		pb_nextplanet_sp_pb_ddplanets_down = newImage("designs/Discovery/images/pb_nextplanet-sp_pb_ddpl-49.gif");
		nb_raumkarte_over = newImage("designs/Discovery/images/nb_raumkarte-over.gif");
		nb_raumkarte_sel_item_space = newImage("designs/Discovery/images/nb_raumkarte-sel_item_space.gif");
		nb_raumkarte_down = newImage("designs/Discovery/images/nb_raumkarte-down.gif");
		nb_flotten_over = newImage("designs/Discovery/images/nb_flotten-over.gif");
		nb_flotten_down = newImage("designs/Discovery/images/nb_flotten-down.gif");
		nb_flotten_sel_item_fleets = newImage("designs/Discovery/images/nb_flotten-sel_item_fleets.gif");
		nb_favoriten_over = newImage("designs/Discovery/images/nb_favoriten-over.gif");
		nb_favoriten_down = newImage("designs/Discovery/images/nb_favoriten-down.gif");
		nb_favoriten_sel_item_bookmarks = newImage("designs/Discovery/images/nb_favoriten-sel_item_bookm.gif");
		nb_allianz_over = newImage("designs/Discovery/images/nb_allianz-over.gif");
		nb_allianz_down = newImage("designs/Discovery/images/nb_allianz-down.gif");
		nb_allianz_sel_item_alliance = newImage("designs/Discovery/images/nb_allianz-sel_item_allianc.gif");
		nb_rathaus_over = newImage("designs/Discovery/images/nb_rathaus-over.gif");
		nb_rathaus_down = newImage("designs/Discovery/images/nb_rathaus-down.gif");
		nb_rathaus_sel_item_alliance_news = newImage("designs/Discovery/images/nb_rathaus-sel_item_allianc.gif");
		nb_bevoelkerung_over = newImage("designs/Discovery/images/nb_bevoelkerung-over.gif");
		nb_bevoelkerung_down = newImage("designs/Discovery/images/nb_bevoelkerung-down.gif");
		nb_bevoelkerung_sel_item_population = newImage("designs/Discovery/images/nb_bevoelkerung-sel_item_po.gif");
		nb_informationen_over = newImage("designs/Discovery/images/nb_informationen-over.gif");
		nb_informationen_down = newImage("designs/Discovery/images/nb_informationen-down.gif");
		nb_informationen_sel_item_planetoverview = newImage("designs/Discovery/images/nb_informationen-sel_item_p.gif");
		nb_schiffshafen_over = newImage("designs/Discovery/images/nb_schiffshafen-over.gif");
		nb_schiffshafen_down = newImage("designs/Discovery/images/nb_schiffshafen-down.gif");
		nb_schiffshafen_sel_item_haven = newImage("designs/Discovery/images/nb_schiffshafen-sel_item_ha.gif");
		nb_technikbuam_over = newImage("designs/Discovery/images/nb_technikbuam-over.gif");
		nb_technikbuam_down = newImage("designs/Discovery/images/nb_technikbuam-down.gif");
		nb_technikbuam_sel_item_techtree = newImage("designs/Discovery/images/nb_technikbuam-sel_item_tec.gif");
		nb_wirtschaft_down = newImage("designs/Discovery/images/nb_wirtschaft-down.gif");
		nb_wirtschaft_over = newImage("designs/Discovery/images/nb_wirtschaft-over.gif");
		nb_wirtschaft_sel_item_ressources = newImage("designs/Discovery/images/nb_wirtschaft-sel_item_ress.gif");
		nb_bauhof_over = newImage("designs/Discovery/images/nb_bauhof-over.gif");
		nb_bauhof_down = newImage("designs/Discovery/images/nb_bauhof-down.gif");
		nb_bauhof_sel_item_buildings = newImage("designs/Discovery/images/nb_bauhof-sel_item_building.gif");
		nb_schiffswerft_over = newImage("designs/Discovery/images/nb_schiffswerft-over.gif");
		nb_schiffswerft_down = newImage("designs/Discovery/images/nb_schiffswerft-down.gif");
		nb_schiffswerft_sel_item_shipyard = newImage("designs/Discovery/images/nb_schiffswerft-sel_item_sh.gif");
		nb_verteidigung_over = newImage("designs/Discovery/images/nb_verteidigung-over.gif");
		nb_verteidigung_down = newImage("designs/Discovery/images/nb_verteidigung-down.gif");
		nb_verteidigung_sel_item_defense = newImage("designs/Discovery/images/nb_verteidigung-sel_item_de.gif");
		nb_forschung_over = newImage("designs/Discovery/images/nb_forschung-over.gif");
		nb_forschung_down = newImage("designs/Discovery/images/nb_forschung-down.gif");
		nb_forschung_sel_item_research = newImage("designs/Discovery/images/nb_forschung-sel_item_resea.gif");
		nb_marktplatz_over = newImage("designs/Discovery/images/nb_marktplatz-over.gif");
		nb_marktplatz_down = newImage("designs/Discovery/images/nb_marktplatz-down.gif");
		nb_marktplatz_sel_item_market = newImage("designs/Discovery/images/nb_marktplatz-sel_item_mark.gif");
		nb_recycling_over = newImage("designs/Discovery/images/nb_recycling-over.gif");
		nb_recycling_down = newImage("designs/Discovery/images/nb_recycling-down.gif");
		nb_recycling_sel_item_recycle = newImage("designs/Discovery/images/nb_recycling-sel_item_recyc.gif");
		nb_buddylist_over = newImage("designs/Discovery/images/nb_buddylist-over.gif");
		nb_buddylist_down = newImage("designs/Discovery/images/nb_buddylist-down.gif");
		nb_buddylist_sel_item_buddylist = newImage("designs/Discovery/images/nb_buddylist-sel_item_buddy.gif");
		pb_post_sp_pb_post_over = newImage("designs/Discovery/images/pb_post-sp_pb_post_over.gif");
		pb_post_sp_pb_post_down = newImage("designs/Discovery/images/pb_post-sp_pb_post_down.gif");
		pb_post_sp_pb_post_sel_item_post = newImage("designs/Discovery/images/pb_post-sp_pb_post_sel_item.gif");
		pb_post_sp_pb_post_postthere = newImage("designs/Discovery/images/pb_post-sp_pb_post_postther.gif");
		alert_attacked = newImage("designs/Discovery/images/alert-attacked.gif");
		pb_notes_sp_pb_notes_over = newImage("designs/Discovery/images/pb_notes-sp_pb_notes_over.gif");
		pb_notes_sp_pb_notes_down = newImage("designs/Discovery/images/pb_notes-sp_pb_notes_down.gif");
		pb_notes_sp_pb_notes_sel_item_notes = newImage("designs/Discovery/images/pb_notes-sp_pb_notes_sel_it.gif");
		preloadFlag = true;
	}
}

function ShowState_sel_item_space() {
	changeImages('nb_space', 'nb_raumkarte_sel_item_space');
}

function ShowState_sel_item_fleets() {
	changeImages('nb_flotten', 'nb_flotten_sel_item_fleets');
}

function ShowState_sel_item_bookmarks() {
	changeImages('nb_favoriten', 'nb_favoriten_sel_item_bookmarks');
}

function ShowState_sel_item_alliance() {
	changeImages('nb_allianz', 'nb_allianz_sel_item_alliance');
}

function ShowState_sel_item_alliance_news() {
	changeImages('nb_rathaus', 'nb_rathaus_sel_item_alliance_news');
}

function ShowState_sel_item_population() {
	changeImages('nb_bevoelkerung', 'nb_bevoelkerung_sel_item_population');
}

function ShowState_sel_item_planetoverview() {
	changeImages('nb_informationen', 'nb_informationen_sel_item_planetoverview');
}

function ShowState_sel_item_haven() {
	changeImages('nb_schiffshafen', 'nb_schiffshafen_sel_item_haven');
}

function ShowState_sel_item_techtree() {
	changeImages('nb_technikbuam', 'nb_technikbuam_sel_item_techtree');
}

function ShowState_sel_item_ressources() {
	changeImages('nb_wirtschaft', 'nb_wirtschaft_sel_item_ressources');
}

function ShowState_sel_item_buildings() {
	changeImages('nb_bauhof', 'nb_bauhof_sel_item_buildings');
}

function ShowState_sel_item_shipyard() {
	changeImages('nb_schiffswerft', 'nb_schiffswerft_sel_item_shipyard');
}

function ShowState_sel_item_defense() {
	changeImages('nb_verteidigung', 'nb_verteidigung_sel_item_defense');
}

function ShowState_sel_item_research() {
	changeImages('nb_forschung', 'nb_forschung_sel_item_research');
}

function ShowState_sel_item_market() {
	changeImages('nb_marktplatz', 'nb_marktplatz_sel_item_market');
}

function ShowState_sel_item_recycle() {
	changeImages('nb_recycling', 'nb_recycling_sel_item_recycle');
}

function ShowState_sel_item_buddylist() {
	changeImages('nb_buddylist', 'nb_buddylist_sel_item_buddylist');
}

function ShowState_Postthere() {

	$mcnt = check_new_messages($_SESSION[ROUNDID]['user']['id']);
	if ($mcnt>0)
		changeImages('pb_post', 'designs/Discovery/images/pb_post-sp_pb_post_postther.gif');
	else
		changeImages('pb_post', 'designs/Discovery/images/pb_post.gif');
	return true
}

function ShowState_attacked() {
	changeImages('alert', 'alert_attacked');
}




function PlanetDropDown(enable)
{
	//if (document.getElementById('planetDropDown').style.visibility=='visible')
	if (enable)
		document.getElementById('planetDropDown').style.visibility='visible';
	else
		document.getElementById('planetDropDown').style.visibility='hidden';


}