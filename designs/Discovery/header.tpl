
{*-----------------------------------------------------------------------------------------------------------------------------------
//--Beschreibung       :         Discovery-Menuedesign Version
//--Version            :         1.6
//--Datum              :         14. Oktober 2006
//--Autor              :         Michael van Ingen
//--ToDo               :         Postthere & Attacked-States (in script.js)
//--                             Ingame-Buttonstyle wie im Menue anpassen (button1, button2 ? )
//--                             Preload nach dem Logon 1x Ausführen, damit er aus dem Body herausgenommen werden kann
//---------------------------------------------------------------------------------------------------------------------------------*}



{* ----------------------------------- Linker Menueleiste ----------------------------------- *}

<div class="Left_Panel">

	<div class="Menueleiste-03_">
		<img name="Menueleiste_03" src="{$templateDir}/images/Menueleiste_03.gif"  align="top"></div>
	<div class="Menueleiste-08_">
		<img src="{$templateDir}/images/Menueleiste_08.gif" /></div>
	<div class="sp-pb-overview_">
		<img name="sp_pb_overview" src="{$templateDir}/images/sp_pb_overview.gif" alt="Ueberblick" border="0" usemap="#sp_pb_overview_Map"></div>
	<div class="Menueleiste-11_">
		<img src="{$templateDir}/images/Menueleiste_11.gif" /></div>
	<div class="Menueleiste-14_">
		<img src="{$templateDir}/images/Menueleiste_14.gif" /></div>
	<div class="platzhalter-serverzeit_">
		<td colspan="2" bgcolor="#000000" class="Servertime">
			{$serverTime}
		</td>
	</div>
	<div class="Menueleiste-12_">
		<img src="{$templateDir}/images/Menueleiste_12.gif" /></div>
	<div class="pb-info_">
		<img name="pb_info" src="{$templateDir}/images/pb_info.gif" width="56px" height="60px" alt="Info" border="0" usemap="#pb_info_Map"></div>
	<div class="Menueleiste-15_">
		<img src="{$templateDir}/images/Menueleiste_15.gif" /></div>
	<div class="Menueleiste-16_">
		<img src="{$templateDir}/images/Menueleiste_16.gif" /></div>
	<div class="Menueleiste-18_">
		<img src="{$templateDir}/images/Menueleiste_18.gif" /></div>
	<div class="PlanetName">
		<td colspan="11" align="left" valign="middle">
			{$currentPlanetName}
		</td>
	</div>
	<div class="Menueleiste-20_">
		<img src="{$templateDir}/images/Menueleiste_20.gif" /></div>
	<div class="pb-previousplanet_">
		<img name="pb_previousplanet" src="{$templateDir}/images/pb_previousplanet.gif" alt="PrevPlanet" border="0" usemap="#pb_previousplanet_Map"></a></div>
	<div class="pb-nextplanet_">
		<img name="pb_ddplanets" src="{$templateDir}/images/pb_ddplanets.gif" alt="NextPlanet" border="0" usemap="#pb_ddplanets_Map"></div>
	<div class="Menueleiste_">
		<img src="{$templateDir}/images/Menueleiste.gif" /></div>
	<div class="Menueleiste-27_">
		<img src="{$templateDir}/images/Menueleiste_27.gif" /></div>
	<div class="pb-planetdropdown_">
		<img name="pb_nextplanet" src="{$templateDir}/images/pb_nextplanet.gif" alt="Planeten" border="0" alt="" usemap="#pb_nextplanet_Map"></div>
	<div class="Menueleiste-23_">
		<img src="{$templateDir}/images/Menueleiste_23.gif" /></div>
	<div class="Menueleiste-25_">
		<img src="{$templateDir}/images/Menueleiste_25.gif" /></div>
	<div class="Menueleiste-26_">
		<img src="{$templateDir}/images/Menueleiste_26.gif" /></div>
	<div class="nb-raumkarte_">
		<a href="?page=space"
			onmouseover="changeImages('nb_space', '{$templateDir}/images/nb_raumkarte-over.gif'); return true;"
			onmouseout="changeImages('nb_space', '{$templateDir}/images/nb_raumkarte.gif'); return true;"
			onmousedown="changeImages('nb_space', '{$templateDir}/images/nb_raumkarte-down.gif'); return true;"
			onmouseup="changeImages('nb_space', '{$templateDir}/images/nb_raumkarte-over.gif'); return true;">
			<img id="nb_space" src="{$templateDir}/images/nb_raumkarte.gif" alt="Raumkarte" border="0" /></a></div>
	<div class="nb-flotten_">
		<a href="?page=fleets"
			onmouseover="changeImages('nb_flotten', '{$templateDir}/images/nb_flotten-over.gif'); return true;"
			onmouseout="changeImages('nb_flotten', '{$templateDir}/images/nb_flotten.gif'); return true;"
			onmousedown="changeImages('nb_flotten', '{$templateDir}/images/nb_flotten-down.gif'); return true;"
			onmouseup="changeImages('nb_flotten', '{$templateDir}/images/nb_flotten-over.gif'); return true;">
			<img id="nb_flotten" src="{$templateDir}/images/nb_flotten.gif" alt="Flotten" border="0" /></a></div>
	<div class="nb-favoriten_">
		<a href="?page=bookmarks"
			onmouseover="changeImages('nb_favoriten', '{$templateDir}/images/nb_favoriten-over.gif'); return true;"
			onmouseout="changeImages('nb_favoriten', '{$templateDir}/images/nb_favoriten.gif'); return true;"
			onmousedown="changeImages('nb_favoriten', '{$templateDir}/images/nb_favoriten-down.gif'); return true;"
			onmouseup="changeImages('nb_favoriten', '{$templateDir}/images/nb_favoriten-over.gif'); return true;">
			<img name="nb_favoriten" src="{$templateDir}/images/nb_favoriten.gif" alt="Favoriten" border="0" /></a></div>
	<div class="nb-allianz_">
		<a href="?page=alliance"
			onmouseover="changeImages('nb_allianz', '{$templateDir}/images/nb_allianz-over.gif'); return true;"
			onmouseout="changeImages('nb_allianz', '{$templateDir}/images/nb_allianz.gif'); return true;"
			onmousedown="changeImages('nb_allianz', '{$templateDir}/images/nb_allianz-down.gif'); return true;"
			onmouseup="changeImages('nb_allianz', '{$templateDir}/images/nb_allianz-over.gif'); return true;">
			<img name="nb_allianz" src="{$templateDir}/images/nb_allianz.gif" alt="Allianz" border="0" /></a></div>
	<div class="nb-rathaus_">
		<a href="?page=townhall"
			onmouseover="changeImages('nb_rathaus', '{$templateDir}/images/nb_rathaus-over.gif'); return true;"
			onmouseout="changeImages('nb_rathaus', '{$templateDir}/images/nb_rathaus.gif'); return true;"
			onmousedown="changeImages('nb_rathaus', '{$templateDir}/images/nb_rathaus-down.gif'); return true;"
			onmouseup="changeImages('nb_rathaus', '{$templateDir}/images/nb_rathaus-over.gif'); return true;">
			<img name="nb_rathaus" src="{$templateDir}/images/nb_rathaus.gif" alt="Rathaus" border="0" /></a></div>
	<div class="Menueleiste-41_">
		<img src="{$templateDir}/images/Menueleiste_41.gif" /></div>
	<div class="nb-bev-lkerung_">
		<a href="?page=population"
			onmouseover="changeImages('nb_bevoelkerung', '{$templateDir}/images/nb_bevoelkerung-over.gif'); return true;"
			onmouseout="changeImages('nb_bevoelkerung', '{$templateDir}/images/nb_bevoelkerung.gif'); return true;"
			onmousedown="changeImages('nb_bevoelkerung', '{$templateDir}/images/nb_bevoelkerung-down.gif'); return true;"
			onmouseup="changeImages('nb_bevoelkerung', '{$templateDir}/images/nb_bevoelkerung-over.gif'); return true;">
			<img name="nb_bevoelkerung" src="{$templateDir}/images/nb_bevoelkerung.gif" alt="Bevoelkerung" border="0" /></a></div>
	<div class="nb-informationen_">
		<a href="?page=planetoverview"
			onmouseover="changeImages('nb_informationen', '{$templateDir}/images/nb_informationen-over.gif'); return true;"
			onmouseout="changeImages('nb_informationen', '{$templateDir}/images/nb_informationen.gif'); return true;"
			onmousedown="changeImages('nb_informationen', '{$templateDir}/images/nb_informationen-down.gif'); return true;"
			onmouseup="changeImages('nb_informationen', '{$templateDir}/images/nb_informationen-over.gif'); return true;">
			<img name="nb_informationen" src="{$templateDir}/images/nb_informationen.gif" alt="Info" border="0" /></a></div>
	<div class="nb-schiffshafen_">
		<a href="?page=haven"
			onmouseover="changeImages('nb_schiffshafen', '{$templateDir}/images/nb_schiffshafen-over.gif'); return true;"
			onmouseout="changeImages('nb_schiffshafen', '{$templateDir}/images/nb_schiffshafen.gif'); return true;"
			onmousedown="changeImages('nb_schiffshafen', '{$templateDir}/images/nb_schiffshafen-down.gif'); return true;"
			onmouseup="changeImages('nb_schiffshafen', '{$templateDir}/images/nb_schiffshafen-over.gif'); return true;">
			<img name="nb_schiffshafen" src="{$templateDir}/images/nb_schiffshafen.gif" alt="Raumschiffhafen" border="0" /></a></div>
	<div class="nb-technikbuam_">
		<a href="?page=techtree"
			onmouseover="changeImages('nb_technikbuam', '{$templateDir}/images/nb_technikbuam-over.gif'); return true;"
			onmouseout="changeImages('nb_technikbuam', '{$templateDir}/images/nb_technikbuam.gif'); return true;"
			onmousedown="changeImages('nb_technikbuam', '{$templateDir}/images/nb_technikbuam-down.gif'); return true;"
			onmouseup="changeImages('nb_technikbuam', '{$templateDir}/images/nb_technikbuam-over.gif'); return true;">
			<img name="nb_technikbuam" src="{$templateDir}/images/nb_technikbuam.gif" alt="Technikbaum" border="0" /></a></div>
	<div class="nb-wirtschaft_">
		<a href="?page=economy"
			onmouseover="changeImages('nb_wirtschaft', '{$templateDir}/images/nb_wirtschaft-over.gif'); return true;"
			onmouseout="changeImages('nb_wirtschaft', '{$templateDir}/images/nb_wirtschaft.gif'); return true;"
			onmousedown="changeImages('nb_wirtschaft', '{$templateDir}/images/nb_wirtschaft-down.gif'); return true;"
			onmouseup="changeImages('nb_wirtschaft', '{$templateDir}/images/nb_wirtschaft-over.gif'); return true;">
			<img name="nb_wirtschaft" src="{$templateDir}/images/nb_wirtschaft.gif" alt="Wirtschaft" border="0" /></a></div>
	<div class="nb-kryptocenter_">
		<a href="?page=crypto"
			onmouseover="changeImages('nb_kryptocenter', '{$templateDir}/images/nb_kryptocenter-over.gif'); return true;"
			onmouseout="changeImages('nb_kryptocenter', '{$templateDir}/images/nb_kryptocenter.gif'); return true;"
			onmousedown="changeImages('nb_kryptocenter', '{$templateDir}/images/nb_kryptocenter-down.gif'); return true;"
			onmouseup="changeImages('nb_kryptocenter', '{$templateDir}/images/nb_kryptocenter-over.gif'); return true;">
			<img name="nb_kryptocenter" src="{$templateDir}/images/nb_kryptocenter.gif" alt="Kryptocenter" border="0" /></a></div>

	<div class="Menueleiste-47_">
		<img img name="Menueleiste-47_" src="{$templateDir}/images/Menueleiste_47.gif" /></div>
	<div class="nb-bauhof_">
		<a href="?page=buildings"
			onmouseover="changeImages('nb_bauhof', '{$templateDir}/images/nb_bauhof-over.gif'); return true;"
			onmouseout="changeImages('nb_bauhof', '{$templateDir}/images/nb_bauhof.gif'); return true;"
			onmousedown="changeImages('nb_bauhof', '{$templateDir}/images/nb_bauhof-down.gif'); return true;"
			onmouseup="changeImages('nb_bauhof', '{$templateDir}/images/nb_bauhof-over.gif'); return true;">
			<img name="nb_bauhof" src="{$templateDir}/images/nb_bauhof.gif" alt="Bauhof" border="0" /></a></div>
	<div class="nb-schiffswerft_">
		<a href="?page=shipyard"
			onmouseover="changeImages('nb_schiffswerft', '{$templateDir}/images/nb_schiffswerft-over.gif'); return true;"
			onmouseout="changeImages('nb_schiffswerft', '{$templateDir}/images/nb_schiffswerft.gif'); return true;"
			onmousedown="changeImages('nb_schiffswerft', '{$templateDir}/images/nb_schiffswerft-down.gif'); return true;"
			onmouseup="changeImages('nb_schiffswerft', '{$templateDir}/images/nb_schiffswerft-over.gif'); return true;">
			<img name="nb_schiffswerft" src="{$templateDir}/images/nb_schiffswerft.gif" alt="Raumschiffwerft" border="0" /></a></div>
	<div class="nb-verteidigung_">
		<a href="?page=defense"
			onmouseover="changeImages('nb_verteidigung', '{$templateDir}/images/nb_verteidigung-over.gif'); return true;"
			onmouseout="changeImages('nb_verteidigung', '{$templateDir}/images/nb_verteidigung.gif'); return true;"
			onmousedown="changeImages('nb_verteidigung', '{$templateDir}/images/nb_verteidigung-down.gif'); return true;"
			onmouseup="changeImages('nb_verteidigung', '{$templateDir}/images/nb_verteidigung-over.gif'); return true;">
			<img id="nb_verteidigung" src="{$templateDir}/images/nb_verteidigung.gif" alt="Verteidigungsanlagen" border="0" /></a></div>
	<div class="nb-forschung_">
		<a href="?page=research"
			onmouseover="changeImages('nb_forschung', '{$templateDir}/images/nb_forschung-over.gif'); return true;"
			onmouseout="changeImages('nb_forschung', '{$templateDir}/images/nb_forschung.gif'); return true;"
			onmousedown="changeImages('nb_forschung', '{$templateDir}/images/nb_forschung-down.gif'); return true;"
			onmouseup="changeImages('nb_forschung', '{$templateDir}/images/nb_forschung-over.gif'); return true;">
			<img id="nb_forschung" src="{$templateDir}/images/nb_forschung.gif" alt="Forschung" border="0" /></a></div>
	<div class="nb-raketen_">
		<a href="?page=missiles"
			onmouseover="changeImages('nb_raketen', '{$templateDir}/images/nb_raketen-over.gif'); return true;"
			onmouseout="changeImages('nb_raketen', '{$templateDir}/images/nb_raketen.gif'); return true;"
			onmousedown="changeImages('nb_raketen', '{$templateDir}/images/nb_raketen-down.gif'); return true;"
			onmouseup="changeImages('nb_raketen', '{$templateDir}/images/nb_raketen-over.gif'); return true;">
			<img id="nb_raketen" src="{$templateDir}/images/nb_raketen.gif" alt="raketen" border="0" /></a></div>			
	<div class="nb-marktplatz_">
		<a href="?page=market"
			onmouseover="changeImages('nb_marktplatz', '{$templateDir}/images/nb_marktplatz-over.gif'); return true;"
			onmouseout="changeImages('nb_marktplatz', '{$templateDir}/images/nb_marktplatz.gif'); return true;"
			onmousedown="changeImages('nb_marktplatz', '{$templateDir}/images/nb_marktplatz-down.gif'); return true;"
			onmouseup="changeImages('nb_marktplatz', '{$templateDir}/images/nb_marktplatz-over.gif'); return true;">
			<img id="nb_marktplatz" src="{$templateDir}/images/nb_marktplatz.gif" alt="Marktplatz" border="0" /></a></div>
	<div class="nb-recycling_">
		<a href="?page=recycle"
			onmouseover="changeImages('nb_recycling', '{$templateDir}/images/nb_recycling-over.gif'); return true;"
			onmouseout="changeImages('nb_recycling', '{$templateDir}/images/nb_recycling.gif'); return true;"
			onmousedown="changeImages('nb_recycling', '{$templateDir}/images/nb_recycling-down.gif'); return true;"
			onmouseup="changeImages('nb_recycling', '{$templateDir}/images/nb_recycling-over.gif'); return true;">
			<img id="nb_recycling" src="{$templateDir}/images/nb_recycling.gif" alt="Recycling" border="0" /></a></div>
	<div class="Menueleiste-57_">
		<img src="{$templateDir}/images/Menueleiste_57.gif" /></div>
	<div class="nb-buddylist_">
		<a href="?page=buddylist"
			onmouseover="changeImages('nb_buddylist', '{$templateDir}/images/nb_buddylist-over.gif'); return true;"
			onmouseout="changeImages('nb_buddylist', '{if $buddys == true}{$templateDir}/images/nb_buddylist-sel.gif{else}{$templateDir}/images/nb_buddylist.gif{/if}');"
			onmousedown="changeImages('nb_buddylist', '{$templateDir}/images/nb_buddylist-down.gif'); return true;"
			onmouseup="changeImages('nb_buddylist', '{$templateDir}/images/nb_buddylist-over.gif'); return true;">
			<img id="nb_buddylist" src="{if $buddys == true}{$templateDir}/images/nb_buddylist-sel.gif{else}{$templateDir}/images/nb_buddylist.gif{/if}" alt="Buddyliste" border="0" /></a></div>
	<div class="Menueleiste-33_">
		<img src="{$templateDir}/images/Menueleiste_33.gif" /></div>
	<div class="Menueleiste-51_">
		<img src="{$templateDir}/images/Menueleiste_51.gif" /></div>
	<div class="Menueleiste-59_">
		<img src="{$templateDir}/images/Menueleiste_59.gif" /></div>
	<div class="Menueleiste-62_">
		<img src="{$templateDir}/images/Menueleiste_62.gif" /></div>
	<div class="pb-post_">
		<img name="pb_post" id="pb_post" src="{if $messages > 0}{$templateDir}/images/pb_post-sp_pb_post_postther.gif{else}{$templateDir}/images/pb_post.gif{/if}" border="0" usemap="#pb_post_Map" /></div>
	<div class="Menueleiste-63_">
		<img src="{$templateDir}/images/Menueleiste_63.gif" /></div>
	<div class="alert_"><a href="?page=fleets"><img id="alert" src="{if $fleetAttack > 0}{$templateDir}/images/alert-attacked.gif{else}{$templateDir}/images/alert.gif{/if}" alt="Alarm" border="0"/></a></div>
	<div class="pb-notes_">
		<img name="pb_notes" id="pb_notes" src="{$templateDir}/images/pb_notes.gif" alt="Notitzen" border="0" usemap="#pb_notes_Map" /></div>
	<div class="Menueleiste-66_">
		<img src="{$templateDir}/images/Menueleiste_66.gif" /></div>
	<div class="Menueleiste-68_">
		<img src="{$templateDir}/images/Menueleiste_68.gif" /></div>


</div>

{* -----------------------------------  Obere Menueleiste ----------------------------------- *}

<div class="Toppanel">
	<div class="EtoA-Logo_">
		<img name="EtoA_Logo" id="EtoA_Logo" src="{$templateDir}/images/EtoA_Logo.gif" width="289px" height="62px" alt="" />
	</div>
	<div class="TopPanel-02_">
		<img name="TopPanel_02" id="TopPanel_02" src="{$templateDir}/images/TopPanel_02.gif" width="156px" height="42px" border="0" alt="" usemap="#TopPanel_02_Map" />
	</div>
	<div class="TopPanel-03_">
		<img name="TopPanel_03" id="TopPanel_03" src="{$templateDir}/images/TopPanel_03.gif" width="178px" height="42px" border="0" alt="" usemap="#TopPanel_03_Map" />
	</div>
	<div class="TopPanel-04_">
		<img name="TopPanel_04" id="TopPanel_04" src="{$templateDir}/images/TopPanel_04.gif" width="179px" height="42px" border="0" alt="" usemap="#TopPanel_04_Map" />
	</div>
	<div class="TopPanel-05_">
		<img name="TopPanel_05" id="TopPanel_05" src="{$templateDir}/images/TopPanel_05.gif" width="182px" height="42px" border="0" alt="" usemap="#TopPanel_05_Map" />
	</div>
	<div class="TopPanel-06_">
		<img name="TopPanel_06" id="TopPanel_06" src="{$templateDir}/images/TopPanel_06.gif" width="156px" height="22px" border="0" alt="" usemap="#TopPanel_06_Map" />
	</div>
	<div class="TopPanel-07_">
		<img name="TopPanel_07" id="TopPanel_07" src="{$templateDir}/images/TopPanel_07.gif" width="178px" height="22px" border="0" alt="" usemap="#TopPanel_07_Map" />
	</div>
	<div class="TopPanel-08_">
		<img name="TopPanel_08" id="TopPanel_08" src="{$templateDir}/images/TopPanel_08.gif" width="179px" height="22px" border="0" alt="" usemap="#TopPanel_08_Map" />
	</div>
	<div class="TopPanel-09_">
		<img name="TopPanel_09" id="TopPanel_09" src="{$templateDir}/images/TopPanel_09.gif" width="182px" height="22px" border="0" alt="" usemap="#TopPanel_09_Map" />
	</div>
</div>

{* -----------------------------------  Game-Area ----------------------------------- *}


{if $adds == true }
	<div style="position:absolute;left:1000px;top:100px;">
		{$addBanner}
	</div>
{/if}


<div class="Spielbereich_">
