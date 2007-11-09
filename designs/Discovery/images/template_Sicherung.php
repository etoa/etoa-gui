<?php

//--------------------------------------------------------------------------------------------------------------------------------->
//--Beschreibung       :         Discovery-Menuedesign Version
//--Version            :         1.6
//--Datum              :         14. Oktober 2006
//--Autor              :         Michael van Ingen
//--ToDo               :         Postthere & Attacked-States (in script.js)
//--                             Ingame-Buttonstyle wie im Menue anpassen (button1, button2 ? )
//--                             Preload nach dem Logon 1x Ausführen, damit er aus dem Body herausgenommen werden kann
//--------------------------------------------------------------------------------------------------------------------------------->

	// überprüft ob es neue nachrichten hat und wechselt dementsprechend den button
	$mcnt = check_new_messages($_SESSION[ROUNDID]['user']['id']);
	if ($mcnt>0)
	{
		$messages = "".CSS_STYLE."/images/pb_post-sp_pb_post_postther.gif";
	}
	else
	{
		$messages = "".CSS_STYLE."/images/pb_post.gif";
	}


	// überprüft ob fremde flotten zu einem eigenen planet unterwegs sind und wechselt dementsprechend den button
	if (check_fleet_incomming($_SESSION[ROUNDID]['user']['id']))
	{
		$fleet_attack = "".CSS_STYLE."/images/alert-attacked.gif";
	}
	else
	{
		$fleet_attack = "".CSS_STYLE."/images/alert.gif";
	}


	if(check_buddys_online($_SESSION[ROUNDID]['user']['id']))
	{
		$buddys = check_buddys_online($_SESSION[ROUNDID]['user']['id']);
		$buddy_pic = "".CSS_STYLE."/images/nb_buddylist-sel.gif";
		//$buddy_pic = "".CSS_STYLE."/images/buddy_pic.gif";
	}
	else
	{
		$buddys = "";
		$buddy_pic = "".CSS_STYLE."/images/nb_buddylist.gif";
		//$buddy_pic = "".CSS_STYLE."/images/buddy_pic.gif";
	}

//---------------------------------------------- Linker Menueleiste --------------------------------------------------->
echo "
<div class=\"Left_Panel\">

	<div class=\"Menueleiste-03_\">
		<img name=\"Menueleiste_03\" src=\"".CSS_STYLE."/images/Menueleiste_03.gif\" /></div>
	<div class=\"Menueleiste-08_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_08.gif\" /></div>
	<div class=\"sp-pb-overview_\">
		<img name=\"sp_pb_overview\" src=\"".CSS_STYLE."/images/sp_pb_overview.gif\" border=\"0\" usemap=\"#sp_pb_overview_Map\"></div>
	<div class=\"Menueleiste-11_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_11.gif\" /></div>
	<div class=\"Menueleiste-14_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_14.gif\" /></div>
	<div class=\"platzhalter-serverzeit_\">
		<td colspan=\"2\" bgcolor=\"#000000\" class=\"Servertime\">
";

				serverTime();
echo "
		</td>
	</div>
	<div class=\"Menueleiste-12_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_12.gif\" /></div>
	<div class=\"pb-info_\">
		<img name=\"pb_info\" src=\"".CSS_STYLE."/images/pb_info.gif\" width=\"56\" height=\"60\" border=\"0\" usemap=\"#pb_info_Map\"></div>
	<div class=\"Menueleiste-15_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_15.gif\" /></div>
	<div class=\"Menueleiste-16_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_16.gif\" /></div>
	<div class=\"Menueleiste-18_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_18.gif\" /></div>
	<div class=\"PlanetName\">
		<td colspan=\"11\" align=\"left\" valign=\"middle\">
";

				$planets->current->toString();

echo "
		</td>
	</div>
	<div class=\"Menueleiste-20_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_20.gif\" /></div>
	<div class=\"pb-previousplanet_\">
		<img name=\"pb_previousplanet\" src=\"".CSS_STYLE."/images/pb_previousplanet.gif\" border=\"0\" usemap=\"#pb_previousplanet_Map\"></a></div>
	<div class=\"pb-nextplanet_\">
		<img name=\"pb_ddplanets\" src=\"".CSS_STYLE."/images/pb_ddplanets.gif\" border=\"0\" usemap=\"#pb_ddplanets_Map\"></div>
	<div class=\"Menueleiste_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste.gif\" /></div>
	<div class=\"Menueleiste-27_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_27.gif\" /></div>
	<div class=\"pb-planetdropdown_\">
		<img name=\"pb_nextplanet\" src=\"".CSS_STYLE."/images/pb_nextplanet.gif\" border=\"0\" alt=\"\" usemap=\"#pb_nextplanet_Map\"></div>
	<div class=\"Menueleiste-23_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_23.gif\" /></div>
	<div class=\"Menueleiste-25_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_25.gif\" /></div>
	<div class=\"Menueleiste-26_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_26.gif\" /></div>
	<div class=\"nb-raumkarte_\">
		<a href=\"?page=space\"
			onmouseover=\"changeImages('nb_space', '".CSS_STYLE."/images/nb_raumkarte-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_space', '".CSS_STYLE."/images/nb_raumkarte.gif'); return true;\"
			onmousedown=\"changeImages('nb_space', '".CSS_STYLE."/images/nb_raumkarte-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_space', '".CSS_STYLE."/images/nb_raumkarte-over.gif'); return true;\">
			<img id=\"nb_space\" src=\"".CSS_STYLE."/images/nb_raumkarte.gif\" border=\"0\" /></a></div>
	<div class=\"nb-flotten_\">
		<a href=\"?page=fleets\"
			onmouseover=\"changeImages('nb_flotten', '".CSS_STYLE."/images/nb_flotten-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_flotten', '".CSS_STYLE."/images/nb_flotten.gif'); return true;\"
			onmousedown=\"changeImages('nb_flotten', '".CSS_STYLE."/images/nb_flotten-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_flotten', '".CSS_STYLE."/images/nb_flotten-over.gif'); return true;\">
			<img id=\"nb_flotten\" src=\"".CSS_STYLE."/images/nb_flotten.gif\" border=\"0\" /></a></div>
	<div class=\"nb-favoriten_\">
		<a href=\"?page=bookmarks\"
			onmouseover=\"changeImages('nb_favoriten', '".CSS_STYLE."/images/nb_favoriten-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_favoriten', '".CSS_STYLE."/images/nb_favoriten.gif'); return true;\"
			onmousedown=\"changeImages('nb_favoriten', '".CSS_STYLE."/images/nb_favoriten-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_favoriten', '".CSS_STYLE."/images/nb_favoriten-over.gif'); return true;\">
			<img name=\"nb_favoriten\" src=\"".CSS_STYLE."/images/nb_favoriten.gif\" border=\"0\" /></a></div>
	<div class=\"nb-allianz_\">
		<a href=\"?page=alliance\"
			onmouseover=\"changeImages('nb_allianz', '".CSS_STYLE."/images/nb_allianz-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_allianz', '".CSS_STYLE."/images/nb_allianz.gif'); return true;\"
			onmousedown=\"changeImages('nb_allianz', '".CSS_STYLE."/images/nb_allianz-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_allianz', '".CSS_STYLE."/images/nb_allianz-over.gif'); return true;\">
			<img name=\"nb_allianz\" src=\"".CSS_STYLE."/images/nb_allianz.gif\" border=\"0\" /></a></div>
	<div class=\"nb-rathaus_\">
		<a href=\"?page=townhall\"
			onmouseover=\"changeImages('nb_rathaus', '".CSS_STYLE."/images/nb_rathaus-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_rathaus', '".CSS_STYLE."/images/nb_rathaus.gif'); return true;\"
			onmousedown=\"changeImages('nb_rathaus', '".CSS_STYLE."/images/nb_rathaus-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_rathaus', '".CSS_STYLE."/images/nb_rathaus-over.gif'); return true;\">
			<img name=\"nb_rathaus\" src=\"".CSS_STYLE."/images/nb_rathaus.gif\" border=\"0\" /></a></div>
	<div class=\"Menueleiste-41_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_41.gif\" /></div>
	<div class=\"nb-bev-lkerung_\">
		<a href=\"?page=population\"
			onmouseover=\"changeImages('nb_bevoelkerung', '".CSS_STYLE."/images/nb_bevoelkerung-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_bevoelkerung', '".CSS_STYLE."/images/nb_bevoelkerung.gif'); return true;\"
			onmousedown=\"changeImages('nb_bevoelkerung', '".CSS_STYLE."/images/nb_bevoelkerung-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_bevoelkerung', '".CSS_STYLE."/images/nb_bevoelkerung-over.gif'); return true;\">
			<img name=\"nb_bevoelkerung\" src=\"".CSS_STYLE."/images/nb_bevoelkerung.gif\" border=\"0\" /></a></div>
	<div class=\"nb-informationen_\">
		<a href=\"?page=planetoverview\"
			onmouseover=\"changeImages('nb_informationen', '".CSS_STYLE."/images/nb_informationen-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_informationen', '".CSS_STYLE."/images/nb_informationen.gif'); return true;\"
			onmousedown=\"changeImages('nb_informationen', '".CSS_STYLE."/images/nb_informationen-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_informationen', '".CSS_STYLE."/images/nb_informationen-over.gif'); return true;\">
			<img name=\"nb_informationen\" src=\"".CSS_STYLE."/images/nb_informationen.gif\" border=\"0\" /></a></div>
	<div class=\"nb-schiffshafen_\">
		<a href=\"?page=haven\"
			onmouseover=\"changeImages('nb_schiffshafen', '".CSS_STYLE."/images/nb_schiffshafen-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_schiffshafen', '".CSS_STYLE."/images/nb_schiffshafen.gif'); return true;\"
			onmousedown=\"changeImages('nb_schiffshafen', '".CSS_STYLE."/images/nb_schiffshafen-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_schiffshafen', '".CSS_STYLE."/images/nb_schiffshafen-over.gif'); return true;\">
			<img name=\"nb_schiffshafen\" src=\"".CSS_STYLE."/images/nb_schiffshafen.gif\" border=\"0\" /></a></div>
	<div class=\"nb-technikbuam_\">
		<a href=\"?page=techtree\"
			onmouseover=\"changeImages('nb_technikbuam', '".CSS_STYLE."/images/nb_technikbuam-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_technikbuam', '".CSS_STYLE."/images/nb_technikbuam.gif'); return true;\"
			onmousedown=\"changeImages('nb_technikbuam', '".CSS_STYLE."/images/nb_technikbuam-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_technikbuam', '".CSS_STYLE."/images/nb_technikbuam-over.gif'); return true;\">
			<img name=\"nb_technikbuam\" src=\"".CSS_STYLE."/images/nb_technikbuam.gif\" border=\"0\" /></a></div>
	<div class=\"nb-wirtschaft_\">
		<a href=\"?page=ressources\"
			onmouseover=\"changeImages('nb_wirtschaft', '".CSS_STYLE."/images/nb_wirtschaft-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_wirtschaft', '".CSS_STYLE."/images/nb_wirtschaft.gif'); return true;\"
			onmousedown=\"changeImages('nb_wirtschaft', '".CSS_STYLE."/images/nb_wirtschaft-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_wirtschaft', '".CSS_STYLE."/images/nb_wirtschaft-over.gif'); return true;\">
			<img name=\"nb_wirtschaft\" src=\"".CSS_STYLE."/images/nb_wirtschaft.gif\" border=\"0\" /></a></div>
	<div class=\"Menueleiste-47_\">
		<img img name=\"Menueleiste-47_\" src=\"".CSS_STYLE."/images/Menueleiste_47.gif\" /></div>
	<div class=\"nb-bauhof_\">
		<a href=\"?page=buildings\"
			onmouseover=\"changeImages('nb_bauhof', '".CSS_STYLE."/images/nb_bauhof-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_bauhof', '".CSS_STYLE."/images/nb_bauhof.gif'); return true;\"
			onmousedown=\"changeImages('nb_bauhof', '".CSS_STYLE."/images/nb_bauhof-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_bauhof', '".CSS_STYLE."/images/nb_bauhof-over.gif'); return true;\">
			<img name=\"nb_bauhof\" src=\"".CSS_STYLE."/images/nb_bauhof.gif\" border=\"0\" /></a></div>
	<div class=\"nb-schiffswerft_\">
		<a href=\"?page=shipyard\"
			onmouseover=\"changeImages('nb_schiffswerft', '".CSS_STYLE."/images/nb_schiffswerft-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_schiffswerft', '".CSS_STYLE."/images/nb_schiffswerft.gif'); return true;\"
			onmousedown=\"changeImages('nb_schiffswerft', '".CSS_STYLE."/images/nb_schiffswerft-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_schiffswerft', '".CSS_STYLE."/images/nb_schiffswerft-over.gif'); return true;\">
			<img name=\"nb_schiffswerft\" src=\"".CSS_STYLE."/images/nb_schiffswerft.gif\" border=\"0\" /></a></div>
	<div class=\"nb-verteidigung_\">
		<a href=\"?page=defense\"
			onmouseover=\"changeImages('nb_verteidigung', '".CSS_STYLE."/images/nb_verteidigung-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_verteidigung', '".CSS_STYLE."/images/nb_verteidigung.gif'); return true;\"
			onmousedown=\"changeImages('nb_verteidigung', '".CSS_STYLE."/images/nb_verteidigung-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_verteidigung', '".CSS_STYLE."/images/nb_verteidigung-over.gif'); return true;\">
			<img id=\"nb_verteidigung\" src=\"".CSS_STYLE."/images/nb_verteidigung.gif\" border=\"0\" /></a></div>
	<div class=\"nb-forschung_\">
		<a href=\"?page=research\"
			onmouseover=\"changeImages('nb_forschung', '".CSS_STYLE."/images/nb_forschung-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_forschung', '".CSS_STYLE."/images/nb_forschung.gif'); return true;\"
			onmousedown=\"changeImages('nb_forschung', '".CSS_STYLE."/images/nb_forschung-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_forschung', '".CSS_STYLE."/images/nb_forschung-over.gif'); return true;\">
			<img id=\"nb_forschung\" src=\"".CSS_STYLE."/images/nb_forschung.gif\" border=\"0\" /></a></div>
	<div class=\"nb-marktplatz_\">
		<a href=\"?page=market\"
			onmouseover=\"changeImages('nb_marktplatz', '".CSS_STYLE."/images/nb_marktplatz-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_marktplatz', '".CSS_STYLE."/images/nb_marktplatz.gif'); return true;\"
			onmousedown=\"changeImages('nb_marktplatz', '".CSS_STYLE."/images/nb_marktplatz-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_marktplatz', '".CSS_STYLE."/images/nb_marktplatz-over.gif'); return true;\">
			<img id=\"nb_marktplatz\" src=\"".CSS_STYLE."/images/nb_marktplatz.gif\" border=\"0\" /></a></div>
	<div class=\"nb-recycling_\">
		<a href=\"?page=recycle\"
			onmouseover=\"changeImages('nb_recycling', '".CSS_STYLE."/images/nb_recycling-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_recycling', '".CSS_STYLE."/images/nb_recycling.gif'); return true;\"
			onmousedown=\"changeImages('nb_recycling', '".CSS_STYLE."/images/nb_recycling-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_recycling', '".CSS_STYLE."/images/nb_recycling-over.gif'); return true;\">
			<img id=\"nb_recycling\" src=\"".CSS_STYLE."/images/nb_recycling.gif\" border=\"0\" /></a></div>
	<div class=\"Menueleiste-57_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_57.gif\" /></div>
	<div class=\"nb-buddylist_\">
		<a href=\"?page=buddylist\"
			onmouseover=\"changeImages('nb_buddylist', '".CSS_STYLE."/images/nb_buddylist-over.gif'); return true;\"
			onmouseout=\"changeImages('nb_buddylist', '$buddy_pic');\"
			onmousedown=\"changeImages('nb_buddylist', '".CSS_STYLE."/images/nb_buddylist-down.gif'); return true;\"
			onmouseup=\"changeImages('nb_buddylist', '".CSS_STYLE."/images/nb_buddylist-over.gif'); return true;\">
			<img id=\"nb_buddylist\" src=\"$buddy_pic\" border=\"0\" /></a></div>
	<div class=\"Menueleiste-33_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_33.gif\" /></div>
	<div class=\"Menueleiste-51_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_51.gif\" /></div>
	<div class=\"Menueleiste-59_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_59.gif\" /></div>
	<div class=\"Menueleiste-62_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_62.gif\" /></div>
	<div class=\"pb-post_\">
		<img name=\"pb_post\" id=\"pb_post\" src=\"$messages\" border=\"0\" usemap=\"#pb_post_Map\" /></div>
	<div class=\"Menueleiste-63_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_63.gif\" /></div>
	<div class=\"alert_\"><a href=\"?page=overview\"><img id=\"alert\" src=\"$fleet_attack\" border=\"0\"/></a></div>
	<div class=\"pb-notes_\">
		<img name=\"pb_notes\" id=\"pb_notes\" src=\"".CSS_STYLE."/images/pb_notes.gif\" border=\"0\" usemap=\"#pb_notes_Map\" /></div>
	<div class=\"Menueleiste-66_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_66.gif\" /></div>
	<div class=\"Menueleiste-68_\">
		<img src=\"".CSS_STYLE."/images/Menueleiste_68.gif\" /></div>


</div>
";
//	<div class=\"test_grafik_\">
		//<a href=\"?page=buddylist\"><img src=\"$buddy_pic\" border=\"0\"/></a></div>

//---------------------------------------------- Obere Menueleiste ---------------------------------------------------

echo "
<div class=\"Toppanel\">
	<div class=\"EtoA-Logo_\">
		<img name=\"EtoA_Logo\" id=\"EtoA_Logo\" src=\"".CSS_STYLE."/images/EtoA_Logo.gif\" width=\"329\" height=\"64\" alt=\"\" />
	</div>
	<div class=\"TopPanel-02_\">
		<img name=\"TopPanel_02\" id=\"TopPanel_02\" src=\"".CSS_STYLE."/images/TopPanel_02.gif\" width=\"156\" height=\"42\" border=\"0\" alt=\"\" usemap=\"#TopPanel_02_Map\" />
	</div>
	<div class=\"TopPanel-03_\">
		<img name=\"TopPanel_03\" id=\"TopPanel_03\" src=\"".CSS_STYLE."/images/TopPanel_03.gif\" width=\"178\" height=\"42\" border=\"0\" alt=\"\" usemap=\"#TopPanel_03_Map\" />
	</div>
	<div class=\"TopPanel-04_\">
		<img name=\"TopPanel_04\" id=\"TopPanel_04\" src=\"".CSS_STYLE."/images/TopPanel_04.gif\" width=\"179\" height=\"42\" border=\"0\" alt=\"\" usemap=\"#TopPanel_04_Map\" />
	</div>
	<div class=\"TopPanel-05_\">
		<img name=\"TopPanel_05\" id=\"TopPanel_05\" src=\"".CSS_STYLE."/images/TopPanel_05.gif\" width=\"182\" height=\"42\" border=\"0\" alt=\"\" usemap=\"#TopPanel_05_Map\" />
	</div>
	<div class=\"TopPanel-06_\">
		<img name=\"TopPanel_06\" id=\"TopPanel_06\" src=\"".CSS_STYLE."/images/TopPanel_06.gif\" width=\"156\" height=\"22\" border=\"0\" alt=\"\" usemap=\"#TopPanel_06_Map\" />
	</div>
	<div class=\"TopPanel-07_\">
		<img name=\"TopPanel_07\" id=\"TopPanel_07\" src=\"".CSS_STYLE."/images/TopPanel_07.gif\" width=\"178\" height=\"22\" border=\"0\" alt=\"\" usemap=\"#TopPanel_07_Map\" />
	</div>
	<div class=\"TopPanel-08_\">
		<img name=\"TopPanel_08\" id=\"TopPanel_08\" src=\"".CSS_STYLE."/images/TopPanel_08.gif\" width=\"179\" height=\"22\" border=\"0\" alt=\"\" usemap=\"#TopPanel_08_Map\" />
	</div>
	<div class=\"TopPanel-09_\">
		<img name=\"TopPanel_09\" id=\"TopPanel_09\" src=\"".CSS_STYLE."/images/TopPanel_09.gif\" width=\"182\" height=\"22\" border=\"0\" alt=\"\" usemap=\"#TopPanel_09_Map\" />
	</div>
</div>
";





//---------------------------------------------- Game-Area --------------------------------------------------->

echo "
<div class=\"Spielbereich_\">
";


	// Auf Sperrung oder Urlaub prüfen
	$uarr = mysql_fetch_array(dbquery("SELECT user_race_id,user_blocked_from,user_blocked_to,user_ban_reason,user_hmode_from,user_hmode_to, user_points FROM ".$db_table['users']." WHERE user_id='".$_SESSION[ROUNDID]['user']['id']."';"));
	if ($uarr['user_blocked_from']>0 && $uarr['user_blocked_from']<time() && $uarr['user_blocked_to']>time())
	{
		echo "<p>Dein Account ist gesperrt! Grund: ".$uarr['user_ban_reason'].". Dauer der Sperre: ".date("d.m.Y H:i",$uarr['user_blocked_from'])." bis ".date("d.m.Y H:i",$uarr['user_blocked_to'])."</p>";
	}
	elseif ($uarr['user_hmode_from']>0 && $_GET['page']!="userconfig")
	{
		echo "Du befindest dich im Urlaubsmodus. Dieser dauert mindestens noch bis zu folgendem Zeitpunkt:<br/> ".date("d.m.Y H:i",$uarr['user_hmode_to'])."!<br>";
	    if($uarr['user_hmode_to']<time())
        {
            echo "Die Minimaldauer ist abgelaufen. Um wieder Zugriff zum Spiel zu bekommen geh zu den <a href=\"?page=userconfig\">Einstellungen</a> und deaktiviere den Urlaubsmodus!<br>";
        }   	
	}
	else
	{
		$_SESSION[ROUNDID]['user']['points']=$uarr['user_points'];

		// Seite anzeigen
		if ($_GET['page']!="" && !stristr($_GET['page'],"/")) $page = $_GET['page']; else $page = DEFAULT_PAGE;
		if (!@include ("content/$page.php"))
			echo "<h1>Fehler</h1>Die Seite <b>".$page."</b> existiert nicht!<br><br><a href=\"javascript:history.back();\">Zurück</a>";
	}


echo "</div>";

//---------------------------------------------- Dropdown für Linkes Menue --------------------------------------------------->

echo "
<div id=\"planetDropDown\" onmouseover=\"PlanetDropDown(true);return true;\"  onmouseout=\"PlanetDropDown(false);return true;\">
";

		$planets->toLinkList();

echo "
</div>
";




//---------------------------------------------- Imagemaps linker Menueleiste --------------------------------------------------->

echo "
<map name=\"pb_info_Map\">
<area shape=\"circle\" alt=\"\" coords=\"28,31,18\" href=\"?page=help\"
	onmouseover=\"changeImages('pb_info', '".CSS_STYLE."/images/pb_info-sp_pb_info_over.gif'); return true;\"
	onmouseout=\"changeImages('pb_info', '".CSS_STYLE."/images/pb_info.gif'); return true;\"
	onmousedown=\"changeImages('pb_info', '".CSS_STYLE."/images/pb_info-sp_pb_info_down.gif'); return true;\"
	onmouseup=\"changeImages('pb_info', '".CSS_STYLE."/images/pb_info-sp_pb_info_over.gif'); return true;\">
</map>
<map name=\"sp_pb_overview_Map\">
<area shape=\"circle\" alt=\"\" coords=\"36,36,27\" href=\"?page=overview\"
	onmouseover=\"changeImages('sp_pb_overview', '".CSS_STYLE."/images/sp_pb_overview-over.gif'); return true;\"
	onmouseout=\"changeImages('sp_pb_overview', '".CSS_STYLE."/images/sp_pb_overview.gif'); return true;\"
	onmousedown=\"changeImages('sp_pb_overview', '".CSS_STYLE."/images/sp_pb_overview-down.gif'); return true;\"
	onmouseup=\"changeImages('sp_pb_overview', '".CSS_STYLE."/images/sp_pb_overview-over.gif'); return true;\">
</map>
<map name=\"pb_previousplanet_Map\">
<area shape=\"poly\" alt=\"\" coords=\"31,5, 31,29, 12,16\"
	onmouseover=\"changeImages('pb_previousplanet', '".CSS_STYLE."/images/pb_previousplanet-sp_pb_pre.gif'); return true;\"
	onmouseout=\"changeImages('pb_previousplanet', '".CSS_STYLE."/images/pb_previousplanet.gif'); return true;\"
	onmousedown=\"changeImages('pb_previousplanet', '".CSS_STYLE."/images/pb_previousplanet-sp_pb_-38.gif'); return true;\"
	onmouseup=\"changeImages('pb_previousplanet', '".CSS_STYLE."/images/pb_previousplanet.gif'); document.location='?page=$page&planet_id=".$planets->prevId.KJ."'; return true;\">
</map>
<map name=\"pb_ddplanets_Map\">
<area shape=\"poly\" alt=\"\" coords=\"4,5, 23,16, 4,27\"
	onmouseover=\"changeImages('pb_ddplanets', '".CSS_STYLE."/images/pb_ddplanets-sp_pb_nextplan.gif'); return true;\"
	onmouseout=\"changeImages('pb_ddplanets', '".CSS_STYLE."/images/pb_ddplanets.gif'); return true;\"
	onmousedown=\"changeImages('pb_ddplanets', '".CSS_STYLE."/images/pb_ddplanets-sp_pb_nextp-44.gif'); return true;\"
	onmouseup=\"changeImages('pb_ddplanets', '".CSS_STYLE."/images/pb_ddplanets.gif'); document.location='?page=$page&planet_id=".$planets->nextId.KJ."'; return true;\">
</map>
<map name=\"pb_nextplanet_Map\">
<area shape=\"poly\" alt=\"\" coords=\"14,7, 14,26,  26,26, 38,7 \"
	onmouseover=\"changeImages('pb_nextplanet', '".CSS_STYLE."/images/pb_nextplanet-sp_pb_ddplane.gif'); PlanetDropDown(false); return true;\"
	onmouseout=\"changeImages('pb_nextplanet', '".CSS_STYLE."/images/pb_nextplanet.gif'); return true;\"
	onmousedown=\"changeImages('pb_nextplanet', '".CSS_STYLE."/images/pb_nextplanet-sp_pb_ddpl-49.gif'); PlanetDropDown(true); return true;\"
	onmouseup=\"changeImages('pb_nextplanet', '".CSS_STYLE."/images/pb_nextplanet.gif'); return true;\">
</map>
<map name=\"pb_post_Map\">
<area shape=\"circle\" alt=\"\" coords=\"49,37,27\" href=\"?page=messages\"
	onmouseover=\"changeImages('pb_post', '".CSS_STYLE."/images/pb_post-sp_pb_post_over.gif'); return true;\"
	onmouseout=\"changeImages('pb_post', '$messages');\"
	onmousedown=\"changeImages('pb_post', '".CSS_STYLE."/images/pb_post-sp_pb_post_down.gif'); return true;\"
	onmouseup=\"changeImages('pb_post', '".CSS_STYLE."/images/pb_post-sp_pb_post_over.gif'); return true;\">
</map>
<map name=\"pb_notes_Map\">
<area shape=\"circle\" alt=\"\" coords=\"23,32,18\" href=\"?page=notepad\"
	onmouseover=\"changeImages('pb_notes', '".CSS_STYLE."/images/pb_notes-sp_pb_notes_over.gif'); return true;\"
	onmouseout=\"changeImages('pb_notes', '".CSS_STYLE."/images/pb_notes.gif'); return true;\"
	onmousedown=\"changeImages('pb_notes', '".CSS_STYLE."/images/pb_notes-sp_pb_notes_down.gif'); return true;\"
	onmouseup=\"changeImages('pb_notes', '".CSS_STYLE."/images/pb_notes-sp_pb_notes_over.gif'); return true;\">
</map>
";


//---------------------------------------------- Imagemaps obere Menueleiste --------------------------------------------------->
echo "
<map name=\"TopPanel_02_Map\" id=\"TopPanel_02_Map\">
<area shape=\"poly\" alt=\"\" coords=\"116,14, 207,14, 195,53, 103,53\" href=\"http://forum.etoa.ch/\" target=\"_Blank\"
	onmouseover=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_forum_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_forum_down.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_forum_down.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_forum_down.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_forum_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_forum_over.gif'); return true;\" />
<area shape=\"poly\" alt=\"\" coords=\"26,14, 117,14, 105,53, 13,53\" href=\"?page=stats\"
	onmouseover=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_statistiken_over.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_statistiken_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_statistiken_down.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_statistiken_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_statistiken_over.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_statistiken_over.gif'); return true;\" />
</map>
<map name=\"TopPanel_03_Map\" id=\"TopPanel_03_Map\">
<area shape=\"poly\" alt=\"\" coords=\"139,14, 230,14, 218,53, 126,53\" href=\"teamspeak://84.19.184.30:9275/\" target=\"_Blank\"
	onmouseover=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_teamspeak_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_teamspeak_down.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_teamspeak_down.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_teamspeak_down.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_teamspeak_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_teamspeak_over.gif'); return true;\" />
<area shape=\"poly\" alt=\"\" coords=\"49,14, 140,14, 128,53, 36,53\" href=\"http://www.etoa.ch/chat\"  target=\"_Blank\"
	onmouseover=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_chat_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_chat_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_chat_down.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_chat_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_chat_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_chat_over.gif'); return true;\" />
<area shape=\"poly\" alt=\"\" coords=\"-40,14, 51,14, 39,53, -53,53\" href=\"http://forum.etoa.ch/\" target=\"_Blank\"
	onmouseover=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_forum_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_forum_down.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_forum_down.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_forum_down.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_forum_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_forum_over.gif'); return true;\" />
</map>
<map name=\"TopPanel_04_Map\" id=\"TopPanel_04_Map\">
<area shape=\"poly\" alt=\"\" coords=\"140,14, 231,14, 219,53, 127,53\" href=\"?page=rules\"
	onmouseover=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_regeln_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_regeln_down.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_regeln_down.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_regeln_down.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_regeln_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_regeln_over.gif'); return true;\" />
<area shape=\"poly\" alt=\"\" coords=\"50,14, 141,14, 129,53, 37,53\" href=\"?page=userconfig\"
	onmouseover=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_einstellungen_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_einstellungen_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_einstellungen_down.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_einstellungen_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_einstellungen_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_einstellungen_over.gif'); return true;\" />
<area shape=\"poly\" alt=\"\" coords=\"-39,14, 52,14, 40,53, -52,53\" href=\"teamspeak://84.19.184.30:9275/\" target=\"_Blank\"
	onmouseover=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_teamspeak_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_teamspeak_down.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_teamspeak_down.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_teamspeak_down.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_teamspeak_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_teamspeak_over.gif'); return true;\" />
</map>
<map name=\"TopPanel_05_Map\" id=\"TopPanel_05_Map\">
<area shape=\"poly\" alt=\"\" coords=\"152,0, 135,48, 155,48, 162,44, 167,37, 180,-1\" href=\"?logout=1\"
	onmouseover=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_logout_over.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_logout_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_logout_down.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_logout_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_logout_up.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_logout_over.gif'); return true;\" />
<area shape=\"poly\" alt=\"\" coords=\"50,14, 141,14, 129,53, 37,53\" href=\"http://www.etoa.ch/wiki\" target=\"_Blank\"
	onmouseover=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_helpcenter_over.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_helpcenter_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_helpcenter_down.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_helpcenter_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_helpcenter_over.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_helpcenter_over.gif'); return true;\" />
<area shape=\"poly\" alt=\"\" coords=\"-39,14, 52,14, 40,53, -52,53\" href=\"?page=rules\"
	onmouseover=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_regeln_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_regeln_down.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_regeln_down.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_regeln_down.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_regeln_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_regeln_over.gif'); return true;\" />
</map>
<map name=\"TopPanel_06_Map\" id=\"TopPanel_06_Map\">
<area shape=\"poly\" alt=\"\" coords=\"116,-28, 207,-28, 195,11, 103,11\" href=\"http://forum.etoa.ch/\" target=\"_Blank\"
	onmouseover=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_forum_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_forum_down.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_forum_down.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_forum_down.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_forum_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_forum_over.gif'); return true;\" />
<area shape=\"poly\" alt=\"\" coords=\"26,-28, 117,-28, 105,11, 13,11\" href=\"?page=stats\"
	onmouseover=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_statistiken_over.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_statistiken_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_statistiken_down.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_statistiken_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_statistiken_over.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_statistiken_over.gif'); return true;\" />
</map>
<map name=\"TopPanel_07_Map\" id=\"TopPanel_07_Map\">
<area shape=\"poly\" alt=\"\" coords=\"139,-28, 230,-28, 218,11, 126,11\" href=\"teamspeak://84.19.184.30:9275/\" target=\"_Blank\"
	onmouseover=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_teamspeak_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_teamspeak_down.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_teamspeak_down.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_teamspeak_down.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_teamspeak_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_teamspeak_over.gif'); return true;\" />
<area shape=\"poly\" alt=\"\" coords=\"49,-28, 140,-28, 128,11, 36,11\" href=\"http://www.etoa.ch/chat\"  target=\"_Blank\"
	onmouseover=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_chat_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_chat_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_chat_down.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_chat_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_chat_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_chat_over.gif'); return true;\" />
<area shape=\"poly\" alt=\"\" coords=\"-40,-28, 51,-28, 39,11, -53,11\" href=\"http://forum.etoa.ch/\" target=\"_Blank\"
	onmouseover=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_forum_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_forum_down.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_forum_down.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_forum_down.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_forum_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_02', '".CSS_STYLE."/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '".CSS_STYLE."/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_forum_over.gif'); return true;\" />
</map>
<map name=\"TopPanel_08_Map\" id=\"TopPanel_08_Map\">
<area shape=\"poly\" alt=\"\" coords=\"140,-28, 231,-28, 219,11, 127,11\" href=\"?page=rules\"
	onmouseover=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_regeln_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_regeln_down.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_regeln_down.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_regeln_down.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_regeln_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_regeln_over.gif'); return true;\" />
<area shape=\"poly\" alt=\"\" coords=\"50,-28, 141,-28, 129,11, 37,11\" href=\"?page=userconfig\"
	onmouseover=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_einstellungen_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_einstellungen_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_einstellungen_down.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_einstellungen_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_einstellungen_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_einstellungen_over.gif'); return true;\" />
<area shape=\"poly\" alt=\"\" coords=\"-39,-28, 52,-28, 40,11, -52,11\" href=\"teamspeak://84.19.184.30:9275/\" target=\"_Blank\"
	onmouseover=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_teamspeak_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_teamspeak_down.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_teamspeak_down.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_teamspeak_down.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_teamspeak_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_03', '".CSS_STYLE."/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '".CSS_STYLE."/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_teamspeak_over.gif'); return true;\" />
</map>
<map name=\"TopPanel_09_Map\" id=\"TopPanel_09_Map\">
<area shape=\"poly\" alt=\"\" coords=\"152,-42, 135,6, 155,6, 162,2, 167,-5, 180,-43\" href=\"?logout=1\"
	onmouseover=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_logout_over.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_logout_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_logout_down.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_logout_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_logout_up.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_logout_over.gif'); return true;\" />
<area shape=\"poly\" alt=\"\" coords=\"50,-28, 141,-28, 129,11, 37,11\" href=\"http://www.etoa.ch/wiki\" target=\"_Blank\"
	onmouseover=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_helpcenter_over.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_helpcenter_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_helpcenter_down.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_helpcenter_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_helpcenter_over.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_helpcenter_over.gif'); return true;\" />
<area shape=\"poly\" alt=\"\" coords=\"-39,-28, 52,-28, 40,11, -52,11\" href=\"?page=rules\"
	onmouseover=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_regeln_over.gif'); return true;\"
	onmouseout=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09.gif'); return true;\"
	onmousedown=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_regeln_down.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_regeln_down.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_regeln_down.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_regeln_down.gif'); return true;\"
	onmouseup=\"changeImages('TopPanel_04', '".CSS_STYLE."/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '".CSS_STYLE."/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '".CSS_STYLE."/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '".CSS_STYLE."/images/TopPanel_09-imap_regeln_over.gif'); return true;\" />
</map>
";

?>
