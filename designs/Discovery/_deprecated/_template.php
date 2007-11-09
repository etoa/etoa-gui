<div class="Tabelle_01">
	<div class="EtoA-Logo_">
		<img name="EtoA_Logo" id="EtoA_Logo" src="css_style/Andromeda/images/EtoA_Logo.png" width="340" height="64" alt="" />
	</div>
	<div class="toppanel-02_">
		<img name="toppanel_02" id="toppanel_02" src="css_style/Andromeda/images/toppanel_02.gif" width="653" height="8" alt="" />
	</div>
	<div class="toppanel-03_">
		<img name="toppanel_03" id="toppanel_03" src="css_style/Andromeda/images/toppanel_03.png" width="31" height="64" alt="" />
	</div>
	<div class="toppanel-06_">
		<img name="toppanel_06" id="toppanel_06" src="css_style/Andromeda/images/toppanel_06.png" width="653" height="51" border="0" alt="" usemap="#toppanel_06_Map" />
	</div>
	<div class="Menueleiste-03_">
		<img id="Menueleiste_03" src="css_style/Andromeda/images/Menueleiste_03.png" width="207" height="9" alt="" />
	</div>
	<div class="pb-info_">
		<a href="?page=help"
			onmouseover="changeImages('pb_info', 'css_style/Andromeda/images/pb_info-over.png'); return true;"
			onmouseout="changeImages('pb_info', 'css_style/Andromeda/images/pb_info.png'); return true;"
			onmousedown="changeImages('pb_info', 'css_style/Andromeda/images/pb_info-down.png'); return true;"
			onmouseup="changeImages('pb_info', 'css_style/Andromeda/images/pb_info-over.png'); return true;">
			<img name="pb_info" id="pb_info" src="css_style/Andromeda/images/pb_info.png" width="56" height="60" border="0" alt="" /></a>
	</div>
	<div class="Menueleiste_">
		<img id="Menueleiste" src="css_style/Andromeda/images/Menueleiste.png" width="15" height="154" alt="" />
	</div>
	<div class="Spielbereich_ Stil1 Stil2">


	<?php
		// Auf Sperrung oder Urlaub prüfen
		$uarr = mysql_fetch_array(dbquery("SELECT user_race_id,user_blocked_from,user_blocked_to,user_ban_reason,user_hmode_from,user_hmode_to FROM ".$db_table['users']." WHERE user_id='".$_SESSION[ROUNDID]['user']['id']."';"));
		if ($uarr['user_blocked_from']>0 && $uarr['user_blocked_from']<time() && $uarr['user_blocked_to']>time())
		{
			echo "<p>Dein Account ist gesperrt! Grund: ".$uarr['user_ban_reason'].". Dauer der Sperre: ".date("d.m.Y H:i",$uarr['user_blocked_from'])." bis ".date("d.m.Y H:i",$uarr['user_blocked_to'])."</p>";
		}
		elseif ($uarr['user_hmode_from']>0 && $_GET['page']!="userconfig")
		{
			echo "<p>Du befindest dich im Urlaubsmodus. Dieser dauert mindestens noch bis zu folgendem Zeitpunkt:<br/> ".date("d.m.Y H:i",$uarr['user_hmode_to'])."!</p>";
		}
		else
		{
			// Seite anzeigen
			if ($_GET['page']!="" && !stristr($_GET['page'],"/")) $page = $_GET['page']; else $page = DEFAULT_PAGE;
			if (!@include ("content/$page.php"))
				echo "<h1>Fehler</h1>Die Seite <b>".$page."</b> existiert nicht!<br><br><a href=\"javascript:history.back();\">Zurück</a>";
		}
  
	?>



	</div>
	<div class="Menueleiste-08_">
	</div>
	<div class="Menueleiste-09_">
		<img id="Menueleiste_09" src="css_style/Andromeda/images/Menueleiste_09.png" width="10" height="145" alt="" />
	</div>
	<div class="sp-pb-overview_">
		<a href="?"
			onmouseover="changeImages('sp_pb_overview', 'css_style/Andromeda/images/sp_pb_overview-over.png'); return true;"
			onmouseout="changeImages('sp_pb_overview', 'css_style/Andromeda/images/sp_pb_overview.png'); return true;"
			onmousedown="changeImages('sp_pb_overview', 'css_style/Andromeda/images/sp_pb_overview-down.png'); return true;"
			onmouseup="changeImages('sp_pb_overview', 'css_style/Andromeda/images/sp_pb_overview-over.png'); return true;">
			<img name="sp_pb_overview" id="sp_pb_overview" src="css_style/Andromeda/images/sp_pb_overview.png" width="71" height="68" border="0" alt="" /></a>
	</div>
	<div class="Menueleiste-11_">
		<img id="Menueleiste_11" src="css_style/Andromeda/images/Menueleiste_11.gif" width="120" height="29" alt="" />
	</div>
	<div class="Menueleiste-12_">
		<img id="Menueleiste_12" src="css_style/Andromeda/images/Menueleiste_12.gif" width="53" height="45" alt="" />
	</div>
	<div class="platzhalter-serverzeit_ Stil1">
		<?PHP 
			serverTime();
		?>
	</div>
	<div class="Menueleiste-14_">
		<img id="Menueleiste_14" src="css_style/Andromeda/images/Menueleiste_14.gif" width="5" height="45" alt="" />
	</div>
	<div class="Menueleiste-15_">
		<img id="Menueleiste_15" src="css_style/Andromeda/images/Menueleiste_15.gif" width="62" height="27" alt="" />
	</div>
	<div class="Menueleiste-16_">
		<img id="Menueleiste_16" src="css_style/Andromeda/images/Menueleiste_16.gif" width="56" height="23" alt="" />
	</div>
	<div class="Menueleiste-17_">
		<img id="Menueleiste_17" src="css_style/Andromeda/images/Menueleiste_17.gif" width="71" height="6" alt="" />
	</div>
	<div class="Menueleiste-18_">
		<img id="Menueleiste_18" src="css_style/Andromeda/images/Menueleiste_18.gif" width="12" height="23" alt="" />
	</div>
	<div class="platzhalter-planeten_ Stil1  Stil6">
		<?PHP
			$planets->getCurrentData()->toString();
		?>
	</div>
	<div class="Menueleiste-20_">
		<img id="Menueleiste_20" src="css_style/Andromeda/images/Menueleiste_20.gif" width="17" height="23" alt="" />
	</div>
	<div class="pb-previousplanet_">
		<a href="PreviousPlanet()"
			onmouseover="changeImages('pb_previousplanet', 'css_style/Andromeda/images/pb_previousplanet-over.gif'); return true;"
			onmouseout="changeImages('pb_previousplanet', 'css_style/Andromeda/images/pb_previousplanet.gif'); return true;"
			onmousedown="changeImages('pb_previousplanet', 'css_style/Andromeda/images/pb_previousplanet-down.gif'); return true;"
			onmouseup="changeImages('pb_previousplanet', 'css_style/Andromeda/images/pb_previousplanet.gif'); return true;">
			<img name="pb_previousplanet" id="pb_previousplanet" src="css_style/Andromeda/images/pb_previousplanet.gif" width="37" height="35" border="0" alt="" /></a>
	</div>
	<div class="pb-dropdownplanets_">
		<a href="NextPlanet()"
			onmouseover="changeImages('pb_dropdownplanets', 'css_style/Andromeda/images/pb_dropdownplanets-over.gif'); return true;"
			onmouseout="changeImages('pb_dropdownplanets', 'css_style/Andromeda/images/pb_dropdownplanets.gif'); return true;"
			onmousedown="changeImages('pb_dropdownplanets', 'css_style/Andromeda/images/pb_dropdownplanets-down.gif'); return true;"
			onmouseup="changeImages('pb_dropdownplanets', 'css_style/Andromeda/images/pb_dropdownplanets.gif'); return true;">
			<img name="pb_dropdownplanets" id="pb_dropdownplanets" src="css_style/Andromeda/images/pb_dropdownplanets.gif" width="34" height="35" border="0" alt="" /></a>
	</div>
	<div class="Menueleiste-23_">
		<img id="Menueleiste_23" src="css_style/Andromeda/images/Menueleiste_23.gif" width="121" height="48" alt="" />
	</div>
	<div class="pb-nextplanet_" onmouseover="PlanetDropDown(true);return true;"  onmouseout="PlanetDropDown(false);return true;">
		<a href="javascript:;"
			onmouseover="changeImages('pb_nextplanet', 'css_style/Andromeda/images/pb_nextplanet-over.gif'); return true;"
			onmouseout="changeImages('pb_nextplanet', 'css_style/Andromeda/images/pb_nextplanet.gif'); return true;"
			onmousedown="changeImages('pb_nextplanet', 'css_style/Andromeda/images/pb_nextplanet-down.gif'); return true;"
			onmouseup="changeImages('pb_nextplanet', 'css_style/Andromeda/images/pb_nextplanet.gif'); return true;">
			<img name="pb_nextplanet" id="pb_nextplanet" src="css_style/Andromeda/images/pb_nextplanet.gif" width="43" height="32" border="0" alt="" /></a>
	</div>
	<div class="Menueleiste-25_">
		<img id="Menueleiste_25" src="css_style/Andromeda/images/Menueleiste_25.png" width="12" height="58" alt="" />
	</div>
	<div class="Menueleiste-26_">
		<img id="Menueleiste_26" src="css_style/Andromeda/images/Menueleiste_26.png" width="43" height="26" alt="" />
	</div>
	<div class="Menueleiste-27_">
		<img id="Menueleiste_27" src="css_style/Andromeda/images/Menueleiste_27.gif" width="71" height="13" alt="" />
	</div>
	<div class="Menueleiste-28_">
	</div>
	<div class="Menueleiste-29_">
		<img id="Menueleiste_29" src="css_style/Andromeda/images/Menueleiste_29.gif" width="30" height="48" alt="" />
	</div>
	<div class="nb-raumkarte_">
		<a href="?page=space" target="_self"
			onmouseover="changeImages('nb_raumkarte', 'css_style/Andromeda/images/nb_raumkarte-over.gif'); return true;"
			onmouseout="changeImages('nb_raumkarte', 'css_style/Andromeda/images/nb_raumkarte.gif'); return true;"
			onmousedown="changeImages('nb_raumkarte', 'css_style/Andromeda/images/nb_raumkarte-down.gif'); return true;"
			onmouseup="changeImages('nb_raumkarte', 'css_style/Andromeda/images/nb_raumkarte-over.gif'); return true;">
			<img name="nb_raumkarte" id="nb_raumkarte" src="css_style/Andromeda/images/nb_raumkarte.gif" width="164" height="21" border="0" alt="" /></a>
	</div>
	<div class="Menueleiste-31_">
	</div>
	<div class="Menueleiste-33_">
		<img id="Menueleiste_33" src="css_style/Andromeda/images/Menueleiste_33.png" width="32" height="443" alt="" />
	</div>
	<div class="Menueleiste-33_">
	</div>
	<div class="nb-flotten_">
		<a href="?page=fleets"
			onmouseover="changeImages('nb_flotten', 'css_style/Andromeda/images/nb_flotten-over.gif'); return true;"
			onmouseout="changeImages('nb_flotten', 'css_style/Andromeda/images/nb_flotten.gif'); return true;"
			onmousedown="changeImages('nb_flotten', 'css_style/Andromeda/images/nb_flotten-down.gif'); return true;"
			onmouseup="changeImages('nb_flotten', 'css_style/Andromeda/images/nb_flotten-over.gif'); return true;">
			<img name="nb_flotten" id="nb_flotten" src="css_style/Andromeda/images/nb_flotten.gif" width="164" height="20" border="0" alt="" /></a>
	</div>
	<div class="nb-favoriten_">
		<a href="?page=bookmarks"
			onmouseover="changeImages('nb_favoriten', 'css_style/Andromeda/images/nb_favoriten-over.gif'); return true;"
			onmouseout="changeImages('nb_favoriten', 'css_style/Andromeda/images/nb_favoriten.gif'); return true;"
			onmousedown="changeImages('nb_favoriten', 'css_style/Andromeda/images/nb_favoriten-down.gif'); return true;"
			onmouseup="changeImages('nb_favoriten', 'css_style/Andromeda/images/nb_favoriten-over.gif'); return true;">
			<img name="nb_favoriten" id="nb_favoriten" src="css_style/Andromeda/images/nb_favoriten.gif" width="164" height="21" border="0" alt="" /></a>
	</div>
	<div class="Menueleiste-37_">
		<img id="Menueleiste_37" src="css_style/Andromeda/images/Menueleiste_37.png" width="30" height="36" alt="" />
	</div>
	<div class="nb-allianz_">
		<a href="?page=alliance"
			onmouseover="changeImages('nb_allianz', 'css_style/Andromeda/images/nb_allianz-over.gif'); return true;"
			onmouseout="changeImages('nb_allianz', 'css_style/Andromeda/images/nb_allianz.gif'); return true;"
			onmousedown="changeImages('nb_allianz', 'css_style/Andromeda/images/nb_allianz-down.gif'); return true;"
			onmouseup="changeImages('nb_allianz', 'css_style/Andromeda/images/nb_allianz-over.gif'); return true;">
			<img name="nb_allianz" id="nb_allianz" src="css_style/Andromeda/images/nb_allianz.gif" width="164" height="21" border="0" alt="" /></a>
	</div>
	<div class="nb-rathaus_">
		<a href="?page=alliance_news"
			onmouseover="changeImages('nb_rathaus', 'css_style/Andromeda/images/nb_rathaus-over.gif'); return true;"
			onmouseout="changeImages('nb_rathaus', 'css_style/Andromeda/images/nb_rathaus.gif'); return true;"
			onmousedown="changeImages('nb_rathaus', 'css_style/Andromeda/images/nb_rathaus-down.gif'); return true;"
			onmouseup="changeImages('nb_rathaus', 'css_style/Andromeda/images/nb_rathaus-over.gif'); return true;">
			<img name="nb_rathaus" id="nb_rathaus" src="css_style/Andromeda/images/nb_rathaus.gif" width="164" height="21" border="0" alt="" /></a>
	</div>
	<div class="Menueleiste-40_">
		<img id="Menueleiste_40" src="css_style/Andromeda/images/Menueleiste_40.gif" width="30" height="176" alt="" />
	</div>
	<div class="Menueleiste-41_">
		<img id="Menueleiste_41" src="css_style/Andromeda/images/Menueleiste_41.gif" width="164" height="23" alt="" />
	</div>
	<div class="nb-bev-lkerung_">
		<a href="?page=population"
			onmouseover="changeImages('nb_bev_lkerung', 'css_style/Andromeda/images/nb_bev%f6lkerung-over.gif'); return true;"
			onmouseout="changeImages('nb_bev_lkerung', 'css_style/Andromeda/images/nb_bev%f6lkerung.gif'); return true;"
			onmousedown="changeImages('nb_bev_lkerung', 'css_style/Andromeda/images/nb_bev%f6lkerung-down.gif'); return true;"
			onmouseup="changeImages('nb_bev_lkerung', 'css_style/Andromeda/images/nb_bev%f6lkerung-over.gif'); return true;">
			<img name="nb_bev_lkerung" id="nb_bev_lkerung" src="css_style/Andromeda/images/nb_bev%f6lkerung.gif" width="164" height="21" border="0" alt="" /></a>
	</div>
	<div class="nb-informationen_">
		<a href="?page=planetoverview"
			onmouseover="changeImages('nb_informationen', 'css_style/Andromeda/images/nb_informationen-over.gif'); return true;"
			onmouseout="changeImages('nb_informationen', 'css_style/Andromeda/images/nb_informationen.gif'); return true;"
			onmousedown="changeImages('nb_informationen', 'css_style/Andromeda/images/nb_informationen-down.gif'); return true;"
			onmouseup="changeImages('nb_informationen', 'css_style/Andromeda/images/nb_informationen-over.gif'); return true;">
			<img name="nb_informationen" id="nb_informationen" src="css_style/Andromeda/images/nb_informationen.gif" width="164" height="21" border="0" alt="" /></a>
	</div>
	<div class="nb-schiffshafen_">
		<a href="?page=haven"
			onmouseover="changeImages('nb_schiffshafen', 'css_style/Andromeda/images/nb_schiffshafen-over.gif'); return true;"
			onmouseout="changeImages('nb_schiffshafen', 'css_style/Andromeda/images/nb_schiffshafen.gif'); return true;"
			onmousedown="changeImages('nb_schiffshafen', 'css_style/Andromeda/images/nb_schiffshafen-down.gif'); return true;"
			onmouseup="changeImages('nb_schiffshafen', 'css_style/Andromeda/images/nb_schiffshafen-over.gif'); return true;">
			<img name="nb_schiffshafen" id="nb_schiffshafen" src="css_style/Andromeda/images/nb_schiffshafen.gif" width="164" height="21" border="0" alt="" /></a>
	</div>
	<div class="nb-technikbuam_">
		<a href="?page=techtree"
			onmouseover="changeImages('nb_technikbuam', 'css_style/Andromeda/images/nb_technikbuam-over.gif'); return true;"
			onmouseout="changeImages('nb_technikbuam', 'css_style/Andromeda/images/nb_technikbuam.gif'); return true;"
			onmousedown="changeImages('nb_technikbuam', 'css_style/Andromeda/images/nb_technikbuam-down.gif'); return true;"
			onmouseup="changeImages('nb_technikbuam', 'css_style/Andromeda/images/nb_technikbuam-over.gif'); return true;">
			<img name="nb_technikbuam" id="nb_technikbuam" src="css_style/Andromeda/images/nb_technikbuam.gif" width="164" height="21" border="0" alt="" /></a>
	</div>
	<div class="nb-wirtschaft_">
		<a href="?page=ressources"
			onmouseover="changeImages('nb_wirtschaft', 'css_style/Andromeda/images/nb_wirtschaft-over.gif'); return true;"
			onmouseout="changeImages('nb_wirtschaft', 'css_style/Andromeda/images/nb_wirtschaft.gif'); return true;"
			onmousedown="changeImages('nb_wirtschaft', 'css_style/Andromeda/images/nb_wirtschaft-down.gif'); return true;"
			onmouseup="changeImages('nb_wirtschaft', 'css_style/Andromeda/images/nb_wirtschaft-over.gif'); return true;">
			<img name="nb_wirtschaft" id="nb_wirtschaft" src="css_style/Andromeda/images/nb_wirtschaft.gif" width="164" height="21" border="0" alt="" /></a>
	</div>
	<div class="Menueleiste-47_">
		<img id="Menueleiste_47" src="css_style/Andromeda/images/Menueleiste_47.gif" width="164" height="22" alt="" />
	</div>
	<div class="nb-bauhof_">
		<a href="?page=buildings"
			onmouseover="changeImages('nb_bauhof', 'css_style/Andromeda/images/nb_bauhof-over.gif'); return true;"
			onmouseout="changeImages('nb_bauhof', 'css_style/Andromeda/images/nb_bauhof.gif'); return true;"
			onmousedown="changeImages('nb_bauhof', 'css_style/Andromeda/images/nb_bauhof-down.gif'); return true;"
			onmouseup="changeImages('nb_bauhof', 'css_style/Andromeda/images/nb_bauhof-over.gif'); return true;">
			<img name="nb_bauhof" id="nb_bauhof" src="css_style/Andromeda/images/nb_bauhof.gif" width="164" height="21" border="0" alt="" /></a>
	</div>
	<div class="Menueleiste-49_">
		<img id="Menueleiste_49" src="css_style/Andromeda/images/Menueleiste_49.png" width="30" height="30" alt="" />
	</div>
	<div class="nb-schiffswerft_">
		<a href="?page=shipyard"
			onmouseover="changeImages('nb_schiffswerft', 'css_style/Andromeda/images/nb_schiffswerft-over.gif'); return true;"
			onmouseout="changeImages('nb_schiffswerft', 'css_style/Andromeda/images/nb_schiffswerft.gif'); return true;"
			onmousedown="changeImages('nb_schiffswerft', 'css_style/Andromeda/images/nb_schiffswerft-down.gif'); return true;"
			onmouseup="changeImages('nb_schiffswerft', 'css_style/Andromeda/images/nb_schiffswerft-over.gif'); return true;">
			<img name="nb_schiffswerft" id="nb_schiffswerft" src="css_style/Andromeda/images/nb_schiffswerft.gif" width="164" height="21" border="0" alt="" /></a>
	</div>
	<div class="Menueleiste-51_">
		<img id="Menueleiste_51" src="css_style/Andromeda/images/Menueleiste_51.gif" width="30" height="147" alt="" />
	</div>
	<div class="nb-verteidigung_">
		<a href="?page=defense"
			onmouseover="changeImages('nb_verteidigung', 'css_style/Andromeda/images/nb_verteidigung-over.gif'); return true;"
			onmouseout="changeImages('nb_verteidigung', 'css_style/Andromeda/images/nb_verteidigung.gif'); return true;"
			onmousedown="changeImages('nb_verteidigung', 'css_style/Andromeda/images/nb_verteidigung-down.gif'); return true;"
			onmouseup="changeImages('nb_verteidigung', 'css_style/Andromeda/images/nb_verteidigung-over.gif'); return true;">
			<img name="nb_verteidigung" id="nb_verteidigung" src="css_style/Andromeda/images/nb_verteidigung.gif" width="164" height="19" border="0" alt="" /></a>
	</div>
	<div class="Menueleiste-54_">
		<img id="Menueleiste_54" src="css_style/Andromeda/images/Menueleiste_54.png" width="164" height="2" alt="" />
	</div>
	<div class="nb-forschung_">
		<a href="?page=research"
			onmouseover="changeImages('nb_forschung', 'css_style/Andromeda/images/nb_forschung-over.gif'); return true;"
			onmouseout="changeImages('nb_forschung', 'css_style/Andromeda/images/nb_forschung.gif'); return true;"
			onmousedown="changeImages('nb_forschung', 'css_style/Andromeda/images/nb_forschung-down.gif'); return true;"
			onmouseup="changeImages('nb_forschung', 'css_style/Andromeda/images/nb_forschung-over.gif'); return true;">
			<img name="nb_forschung" id="nb_forschung" src="css_style/Andromeda/images/nb_forschung.gif" width="164" height="21" border="0" alt="" /></a>
	</div>
	<div class="nb-marktplatz_">
		<a href="?page=market"
			onmouseover="changeImages('nb_marktplatz', 'css_style/Andromeda/images/nb_marktplatz-over.gif'); return true;"
			onmouseout="changeImages('nb_marktplatz', 'css_style/Andromeda/images/nb_marktplatz.gif'); return true;"
			onmousedown="changeImages('nb_marktplatz', 'css_style/Andromeda/images/nb_marktplatz-down.gif'); return true;"
			onmouseup="changeImages('nb_marktplatz', 'css_style/Andromeda/images/nb_marktplatz-over.gif'); return true;">
			<img name="nb_marktplatz" id="nb_marktplatz" src="css_style/Andromeda/images/nb_marktplatz.gif" width="164" height="21" border="0" alt="" /></a>
	</div>
	<div class="nb-recycling_">
		<a href="?page=recycle"
			onmouseover="changeImages('nb_recycling', 'css_style/Andromeda/images/nb_recycling-over.gif'); return true;"
			onmouseout="changeImages('nb_recycling', 'css_style/Andromeda/images/nb_recycling.gif'); return true;"
			onmousedown="changeImages('nb_recycling', 'css_style/Andromeda/images/nb_recycling-down.gif'); return true;"
			onmouseup="changeImages('nb_recycling', 'css_style/Andromeda/images/nb_recycling-over.gif'); return true;">
			<img name="nb_recycling" id="nb_recycling" src="css_style/Andromeda/images/nb_recycling.gif" width="164" height="21" border="0" alt="" /></a>
	</div>
	<div class="Menueleiste-57_">
		<img id="Menueleiste_57" src="css_style/Andromeda/images/Menueleiste_57.gif" width="164" height="21" alt="" />
	</div>
	<div class="nb-buddylist_">
		<a href="?page=buddylist"
			onmouseover="changeImages('nb_buddylist', 'css_style/Andromeda/images/nb_buddylist-over.gif'); return true;"
			onmouseout="changeImages('nb_buddylist', 'css_style/Andromeda/images/nb_buddylist.gif'); return true;"
			onmousedown="changeImages('nb_buddylist', 'css_style/Andromeda/images/nb_buddylist-down.gif'); return true;"
			onmouseup="changeImages('nb_buddylist', 'css_style/Andromeda/images/nb_buddylist-over.gif'); return true;">
			<img name="nb_buddylist" id="nb_buddylist" src="css_style/Andromeda/images/nb_buddylist.gif" width="164" height="21" border="0" alt="" /></a>
	</div>
	<div class="Menueleiste-59_">
		<img id="Menueleiste_59" src="css_style/Andromeda/images/Menueleiste_59.gif" width="164" height="15" alt="" />
	</div>
	<div class="Menueleiste-60_">
		<img id="Menueleiste_60" src="css_style/Andromeda/images/Menueleiste_60.png" width="16" height="71" alt="" />
	</div>
	<div class="pb-post_">
		<a href="?page=messages"
			onmouseover="changeImages('pb_post', 'css_style/Andromeda/images/pb_post-over.png'); return true;"
			onmouseout="changeImages('pb_post', 'css_style/Andromeda/images/pb_post.png'); return true;"
			onmousedown="changeImages('pb_post', 'css_style/Andromeda/images/pb_post-down.png'); return true;"
			onmouseup="changeImages('pb_post', 'css_style/Andromeda/images/pb_post-over.png'); return true;">
			<img name="pb_post" id="pb_post" src="css_style/Andromeda/images/pb_post.png" width="75" height="71" border="0" alt="" /></a>
	</div>
	<div class="Menueleiste-62_">
		<img id="Menueleiste_62" src="css_style/Andromeda/images/Menueleiste_62.gif" width="117" height="16" alt="" />
	</div>
	<div class="Menueleiste-63_">
		<img id="Menueleiste_63" src="css_style/Andromeda/images/Menueleiste_63.png" width="23" height="94" alt="" />
	</div>
	<div class="alert_">
		<img name="alert" id="alert" src="css_style/Andromeda/images/alert.gif" width="97" height="41" alt="" />
	</div>
	<div class="pb-notes_">
		<a href="?page=notepad"
			onmouseover="changeImages('pb_notes', 'css_style/Andromeda/images/pb_notes-over.png'); return true;"
			onmouseout="changeImages('pb_notes', 'css_style/Andromeda/images/pb_notes.png'); return true;"
			onmousedown="changeImages('pb_notes', 'css_style/Andromeda/images/pb_notes-down.png'); return true;"
			onmouseup="changeImages('pb_notes', 'css_style/Andromeda/images/pb_notes-over.png'); return true;">
			<img name="pb_notes" id="pb_notes" src="css_style/Andromeda/images/pb_notes.png" width="52" height="64" border="0" alt="" /></a>
	</div>
	<div class="Menueleiste-66_">
		<img id="Menueleiste_66" src="css_style/Andromeda/images/Menueleiste_66.png" width="97" height="56" alt="" />
	</div>
	<div class="Menueleiste-68_">
	</div>
	<div class="Menueleiste-68_">
		<img id="Menueleiste_68" src="css_style/Andromeda/images/Menueleiste_68.png" width="75" height="23" alt="" />
	</div>
	<div class="Menueleiste-69_">
		<img id="Menueleiste_69" src="css_style/Andromeda/images/Menueleiste_69.png" width="52" height="14" alt="" />
	</div>
	<div class="Menueleiste-69_">
	</div>
	<div class="Menueleiste-70_">
	</div>
</div>


<div id="planetDropDown" onmouseover="PlanetDropDown(true);return true;"  onmouseout="PlanetDropDown(false);return true;">
	<?PHP
		$planets->toLinkList();
	?>
</div>	

<map name="toppanel_06_Map" id="toppanel_06_Map">
<area shape="poly" alt="" coords="552,6, 643,6, 631,45, 539,45" href="http://www.etoa.ch/wiki"
	onmouseover="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_helpcenter_over.gif', 'toppanel_03', 'css_style/Andromeda/images/toppanel_03-imap_helpcenter_over.png', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_helpcenter_over.png'); return true;"
	onmouseout="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02.gif', 'toppanel_03', 'css_style/Andromeda/images/toppanel_03.png', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06.png'); return true;"
	onmousedown="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_helpcenter_down.gif', 'toppanel_03', 'css_style/Andromeda/images/toppanel_03-imap_helpcenter_down.png', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_helpcenter_down.png'); return true;"
	onmouseup="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_helpcenter_over.gif', 'toppanel_03', 'css_style/Andromeda/images/toppanel_03-imap_helpcenter_over.png', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_helpcenter_over.png'); return true;" />
<area shape="poly" alt="" coords="463,6, 554,6, 542,45, 450,45" href="http://www.etoa.ch/?page=rules"
	onmouseover="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_regeln_over.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_regeln_over.png'); return true;"
	onmouseout="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06.png'); return true;"
	onmousedown="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_regeln_down.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_regeln_down.png'); return true;"
	onmouseup="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_regeln_over.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_regeln_over.png'); return true;" />
<area shape="poly" alt="" coords="373,6, 464,6, 452,45, 360,45" href="?page=userconfig"
	onmouseover="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_einstellungen_over.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_einstellungen_over.png'); return true;"
	onmouseout="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06.png'); return true;"
	onmousedown="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_einstellungen_down.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_einstellungen_down.png'); return true;"
	onmouseup="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_einstellungen_over.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_einstellungen_over.png'); return true;" />
<area shape="poly" alt="" coords="284,6, 375,6, 363,45, 271,45" href="teamspeak://84.19.184.30:9275/"
	onmouseover="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_teamspeak_over.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_teamspeak_over.png'); return true;"
	onmouseout="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06.png'); return true;"
	onmousedown="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_teamspeak_down.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_teamspeak_down.png'); return true;"
	onmouseup="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_teamspeak_over.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_teamspeak_over.png'); return true;" />
<area shape="poly" alt="" coords="194,6, 285,6, 273,45, 181,45" href="http://www.etoa.ch/chat"
	onmouseover="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_chat_over.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_chat_over.png'); return true;"
	onmouseout="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06.png'); return true;"
	onmousedown="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_chat_down.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_chat_down.png'); return true;"
	onmouseup="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_chat_over.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_chat_over.png'); return true;" />
<area shape="poly" alt="" coords="105,6, 196,6, 184,45, 92,45" href="http://forum.etoa.ch/"
	onmouseover="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_forum_over.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_forum_over.png'); return true;"
	onmouseout="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06.png'); return true;"
	onmousedown="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_forum_down.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_forum_down.png'); return true;"
	onmouseup="changeImages('toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_forum_over.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_forum_over.png'); return true;" />
<area shape="poly" alt="" coords="15,6, 106,6, 94,45, 2,45" href="?page=stats"
	onmouseover="changeImages('EtoA_Logo', 'css_style/Andromeda/images/EtoA_Logo-imap_statistiken_over.png', 'toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_statistiken_over.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_statistiken_over.png'); return true;"
	onmouseout="changeImages('EtoA_Logo', 'css_style/Andromeda/images/EtoA_Logo.png', 'toppanel_02', 'css_style/Andromeda/images/toppanel_02.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06.png'); return true;"
	onmousedown="changeImages('EtoA_Logo', 'css_style/Andromeda/images/EtoA_Logo-imap_statistiken_down.png', 'toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_statistiken_down.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_statistiken_down.png'); return true;"
	onmouseup="changeImages('EtoA_Logo', 'css_style/Andromeda/images/EtoA_Logo-imap_statistiken_over.png', 'toppanel_02', 'css_style/Andromeda/images/toppanel_02-imap_statistiken_over.gif', 'toppanel_06', 'css_style/Andromeda/images/toppanel_06-imap_statistiken_over.png'); return true;" />
</map>