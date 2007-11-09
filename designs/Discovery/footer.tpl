<br/><br/>

</div>

{*---------------------------------------------- Dropdown für Linkes Menue ---------------------------------------------------*}

<div id="planetDropDown" onmouseover="PlanetDropDown(true);return true;"  onmouseout="PlanetDropDown(false);return true;">
{$planetList}
</div>




{*---------------------------------------------- Imagemaps linker Menueleiste ---------------------------------------------------*}


<map name="pb_info_Map">
<area shape="circle" alt="" coords="28,31,18" 
	{if $helpBox eq "true"}
		href="javascript:;" onclick="window.open('show.php?page=help','help','status=no,width=800,height=600,scrollbars=yes');"
	{else}
		href="?page=help"
	{/if}
	onmouseover="changeImages('pb_info', '{$templateDir}/images/pb_info-sp_pb_info_over.gif'); return true;"
	onmouseout="changeImages('pb_info', '{$templateDir}/images/pb_info.gif'); return true;"
	onmousedown="changeImages('pb_info', '{$templateDir}/images/pb_info-sp_pb_info_down.gif'); return true;"
	onmouseup="changeImages('pb_info', '{$templateDir}/images/pb_info-sp_pb_info_over.gif'); return true;">
</map>
<map name="sp_pb_overview_Map">
<area shape="circle" alt="" coords="36,36,27" href="?page=overview"
	onmouseover="changeImages('sp_pb_overview', '{$templateDir}/images/sp_pb_overview-over.gif'); return true;"
	onmouseout="changeImages('sp_pb_overview', '{$templateDir}/images/sp_pb_overview.gif'); return true;"
	onmousedown="changeImages('sp_pb_overview', '{$templateDir}/images/sp_pb_overview-down.gif'); return true;"
	onmouseup="changeImages('sp_pb_overview', '{$templateDir}/images/sp_pb_overview-over.gif'); return true;">
</map>
<map name="pb_previousplanet_Map">
<area shape="poly" alt="" coords="31,5, 31,29, 12,16"
	onmouseover="changeImages('pb_previousplanet', '{$templateDir}/images/pb_previousplanet-sp_pb_pre.gif'); return true;"
	onmouseout="changeImages('pb_previousplanet', '{$templateDir}/images/pb_previousplanet.gif'); return true;"
	onmousedown="changeImages('pb_previousplanet', '{$templateDir}/images/pb_previousplanet-sp_pb_-38.gif'); return true;"
	onmouseup="changeImages('pb_previousplanet', '{$templateDir}/images/pb_previousplanet.gif'); document.location='?page={$page}&planet_id={$prevPlanetId}'; return true;">
</map>
<map name="pb_ddplanets_Map">
<area shape="poly" alt="" coords="4,5, 23,16, 4,27"
	onmouseover="changeImages('pb_ddplanets', '{$templateDir}/images/pb_ddplanets-sp_pb_nextplan.gif'); return true;"
	onmouseout="changeImages('pb_ddplanets', '{$templateDir}/images/pb_ddplanets.gif'); return true;"
	onmousedown="changeImages('pb_ddplanets', '{$templateDir}/images/pb_ddplanets-sp_pb_nextp-44.gif'); return true;"
	onmouseup="changeImages('pb_ddplanets', '{$templateDir}/images/pb_ddplanets.gif'); document.location='?page={$page}&planet_id={$nextPlanetId}'; return true;">
</map>
<map name="pb_nextplanet_Map">
<area shape="poly" alt="" coords="14,7, 14,26,  26,26, 38,7 "
	onmouseover="changeImages('pb_nextplanet', '{$templateDir}/images/pb_nextplanet-sp_pb_ddplane.gif'); PlanetDropDown(false); return true;"
	onmouseout="changeImages('pb_nextplanet', '{$templateDir}/images/pb_nextplanet.gif'); return true;"
	onmousedown="changeImages('pb_nextplanet', '{$templateDir}/images/pb_nextplanet-sp_pb_ddpl-49.gif'); PlanetDropDown(true); return true;"
	onmouseup="changeImages('pb_nextplanet', '{$templateDir}/images/pb_nextplanet.gif'); return true;">
</map>
<map name="pb_post_Map">
<area shape="circle" alt="" coords="49,37,27" href="?page=messages"
	onmouseover="changeImages('pb_post', '{$templateDir}/images/pb_post-sp_pb_post_over.gif'); return true;"
	onmouseout="changeImages('pb_post', '{if $messages > 0}{$templateDir}/images/pb_post-sp_pb_post_postther.gif{else}{$templateDir}/images/pb_post.gif{/if}');"
	onmousedown="changeImages('pb_post', '{$templateDir}/images/pb_post-sp_pb_post_down.gif'); return true;"
	onmouseup="changeImages('pb_post', '{$templateDir}/images/pb_post-sp_pb_post_over.gif'); return true;">
</map>
<map name="pb_notes_Map">
<area shape="circle" alt="" coords="23,32,18" 
	{if $noteBox == true}
		href="javascript:;" onclick="window.open('show.php?page=notepad','notes','status=no,width=800,height=600,scrollbars=yes');"
	{else}
		href="?page=notepad"
	{/if}
	onmouseover="changeImages('pb_notes', '{$templateDir}/images/pb_notes-sp_pb_notes_over.gif'); return true;"
	onmouseout="changeImages('pb_notes', '{$templateDir}/images/pb_notes.gif'); return true;"
	onmousedown="changeImages('pb_notes', '{$templateDir}/images/pb_notes-sp_pb_notes_down.gif'); return true;"
	onmouseup="changeImages('pb_notes', '{$templateDir}/images/pb_notes-sp_pb_notes_over.gif'); return true;">
</map>


{*---------------------------------------------- Imagemaps obere Menueleiste --------------------------------------------------*}
<map name="TopPanel_02_Map" id="TopPanel_02_Map">
<area shape="poly" alt="" coords="116,14, 207,14, 195,53, 103,53" href="{$urlForum}" target="_Blank"
	onmouseover="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_forum_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07.gif'); return true;"
	onmousedown="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_forum_down.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_forum_down.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_forum_down.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_forum_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_forum_over.gif'); return true;" />
<area shape="poly" alt="" coords="26,14, 117,14, 105,53, 13,53" href="?page=stats"
	onmouseover="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_statistiken_over.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_statistiken_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06.gif'); return true;"
	onmousedown="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_statistiken_down.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_statistiken_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_statistiken_over.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_statistiken_over.gif'); return true;" />
</map>
<map name="TopPanel_03_Map" id="TopPanel_03_Map">
<area shape="poly" alt="" coords="139,14, 230,14, 218,53, 126,53" href="{$urlTeamspeak}" target="_Blank"
	onmouseover="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_teamspeak_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08.gif'); return true;"
	onmousedown="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_teamspeak_down.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_teamspeak_down.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_teamspeak_down.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_teamspeak_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_teamspeak_over.gif'); return true;" />
<area shape="poly" alt="" coords="49,14, 140,14, 128,53, 36,53" href="javascript:;" onclick="{$chatString}"
	onmouseover="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_chat_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_chat_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07.gif'); return true;"
	onmousedown="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_chat_down.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_chat_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_chat_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_chat_over.gif'); return true;" />
<area shape="poly" alt="" coords="-40,14, 51,14, 39,53, -53,53" href="{$urlForum}" target="_Blank"
	onmouseover="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_forum_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07.gif'); return true;"
	onmousedown="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_forum_down.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_forum_down.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_forum_down.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_forum_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_forum_over.gif'); return true;" />
</map>
<map name="TopPanel_04_Map" id="TopPanel_04_Map">
<area shape="poly" alt="" coords="140,14, 231,14, 219,53, 127,53" href="{$urlRules}" target="_Blank"
	onmouseover="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_regeln_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_regeln_down.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_regeln_down.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_regeln_down.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_regeln_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_regeln_over.gif'); return true;" />
<area shape="poly" alt="" coords="50,14, 141,14, 129,53, 37,53" href="?page=userconfig"
	onmouseover="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_einstellungen_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_einstellungen_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08.gif'); return true;"
	onmousedown="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_einstellungen_down.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_einstellungen_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_einstellungen_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_einstellungen_over.gif'); return true;" />
<area shape="poly" alt="" coords="-39,14, 52,14, 40,53, -52,53" href="{$urlTeamspeak}" target="_Blank"
	onmouseover="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_teamspeak_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08.gif'); return true;"
	onmousedown="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_teamspeak_down.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_teamspeak_down.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_teamspeak_down.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_teamspeak_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_teamspeak_over.gif'); return true;" />
</map>
<map name="TopPanel_05_Map" id="TopPanel_05_Map">
<area shape="poly" alt="" coords="152,0, 135,48, 155,48, 162,44, 167,37, 180,-1" href="?logout=1"
	onmouseover="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_logout_over.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_logout_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_logout_down.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_logout_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_logout_up.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_logout_over.gif'); return true;" />
<area shape="poly" alt="" coords="50,14, 141,14, 129,53, 37,53" href="{$urlHelpcenter}" target="_Blank"
	onmouseover="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_helpcenter_over.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_helpcenter_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_helpcenter_down.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_helpcenter_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_helpcenter_over.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_helpcenter_over.gif'); return true;" />
<area shape="poly" alt="" coords="-39,14, 52,14, 40,53, -52,53" href="{$urlRules}"
	onmouseover="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_regeln_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_regeln_down.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_regeln_down.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_regeln_down.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_regeln_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_regeln_over.gif'); return true;" />
</map>
<map name="TopPanel_06_Map" id="TopPanel_06_Map">
<area shape="poly" alt="" coords="116,-28, 207,-28, 195,11, 103,11" href="{$urlForum}" target="_blank"
	onmouseover="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_forum_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07.gif'); return true;"
	onmousedown="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_forum_down.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_forum_down.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_forum_down.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_forum_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_forum_over.gif'); return true;" />
<area shape="poly" alt="" coords="26,-28, 117,-28, 105,11, 13,11" href="?page=stats"
	onmouseover="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_statistiken_over.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_statistiken_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06.gif'); return true;"
	onmousedown="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_statistiken_down.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_statistiken_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_statistiken_over.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_statistiken_over.gif'); return true;" />
</map>
<map name="TopPanel_07_Map" id="TopPanel_07_Map">
<area shape="poly" alt="" coords="139,-28, 230,-28, 218,11, 126,11" href="{$urlTeamspeak}" target="_blank"
	onmouseover="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_teamspeak_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08.gif'); return true;"
	onmousedown="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_teamspeak_down.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_teamspeak_down.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_teamspeak_down.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_teamspeak_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_teamspeak_over.gif'); return true;" />
<area shape="poly" alt="" coords="49,-28, 140,-28, 128,11, 36,11" href="javascript:;" onclick="{$chatString}"
	onmouseover="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_chat_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_chat_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07.gif'); return true;"
	onmousedown="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_chat_down.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_chat_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_chat_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_chat_over.gif'); return true;" />
<area shape="poly" alt="" coords="-40,-28, 51,-28, 39,11, -53,11" href="{$urlForum}" target="_Blank"
	onmouseover="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_forum_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07.gif'); return true;"
	onmousedown="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_forum_down.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_forum_down.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_forum_down.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_forum_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_02', '{$templateDir}/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '{$templateDir}/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_forum_over.gif'); return true;" />
</map>
<map name="TopPanel_08_Map" id="TopPanel_08_Map">
<area shape="poly" alt="" coords="140,-28, 231,-28, 219,11, 127,11" href="{$urlRules}" target="_blank"
	onmouseover="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_regeln_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_regeln_down.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_regeln_down.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_regeln_down.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_regeln_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_regeln_over.gif'); return true;" />
<area shape="poly" alt="" coords="50,-28, 141,-28, 129,11, 37,11" href="?page=userconfig"
	onmouseover="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_einstellungen_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_einstellungen_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08.gif'); return true;"
	onmousedown="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_einstellungen_down.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_einstellungen_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_einstellungen_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_einstellungen_over.gif'); return true;" />
<area shape="poly" alt="" coords="-39,-28, 52,-28, 40,11, -52,11" href="{$urlTeamspeak}" target="_Blank"
	onmouseover="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_teamspeak_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08.gif'); return true;"
	onmousedown="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_teamspeak_down.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_teamspeak_down.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_teamspeak_down.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_teamspeak_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_03', '{$templateDir}/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '{$templateDir}/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_teamspeak_over.gif'); return true;" />
</map>
<map name="TopPanel_09_Map" id="TopPanel_09_Map">
<area shape="poly" alt="" coords="152,-42, 135,6, 155,6, 162,2, 167,-5, 180,-43" href="?logout=1"
	onmouseover="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_logout_over.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_logout_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_logout_down.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_logout_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_logout_up.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_logout_over.gif'); return true;" />
<area shape="poly" alt="" coords="50,-28, 141,-28, 129,11, 37,11" href="{$urlHelpcenter}" target="_Blank"
	onmouseover="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_helpcenter_over.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_helpcenter_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_helpcenter_down.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_helpcenter_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_helpcenter_over.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_helpcenter_over.gif'); return true;" />
<area shape="poly" alt="" coords="-39,-28, 52,-28, 40,11, -52,11" href="{$urlRules}"
	onmouseover="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_regeln_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_regeln_down.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_regeln_down.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_regeln_down.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_regeln_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_04', '{$templateDir}/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '{$templateDir}/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '{$templateDir}/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '{$templateDir}/images/TopPanel_09-imap_regeln_over.gif'); return true;" />
</map>
