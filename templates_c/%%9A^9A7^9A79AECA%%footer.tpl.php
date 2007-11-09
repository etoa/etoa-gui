<?php /* Smarty version 2.6.14, created on 2007-09-27 17:02:17
         compiled from designs/Discovery/footer.tpl */ ?>
<br/><br/>

</div>


<div id="planetDropDown" onmouseover="PlanetDropDown(true);return true;"  onmouseout="PlanetDropDown(false);return true;">
<?php echo $this->_tpl_vars['planetList']; ?>

</div>






<map name="pb_info_Map">
<area shape="circle" alt="" coords="28,31,18" 
	<?php if ($this->_tpl_vars['helpBox'] == 'true'): ?>
		href="javascript:;" onclick="window.open('show.php?page=help','help','status=no,width=800,height=600,scrollbars=yes');"
	<?php else: ?>
		href="?page=help"
	<?php endif; ?>
	onmouseover="changeImages('pb_info', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_info-sp_pb_info_over.gif'); return true;"
	onmouseout="changeImages('pb_info', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_info.gif'); return true;"
	onmousedown="changeImages('pb_info', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_info-sp_pb_info_down.gif'); return true;"
	onmouseup="changeImages('pb_info', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_info-sp_pb_info_over.gif'); return true;">
</map>
<map name="sp_pb_overview_Map">
<area shape="circle" alt="" coords="36,36,27" href="?page=overview"
	onmouseover="changeImages('sp_pb_overview', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/sp_pb_overview-over.gif'); return true;"
	onmouseout="changeImages('sp_pb_overview', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/sp_pb_overview.gif'); return true;"
	onmousedown="changeImages('sp_pb_overview', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/sp_pb_overview-down.gif'); return true;"
	onmouseup="changeImages('sp_pb_overview', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/sp_pb_overview-over.gif'); return true;">
</map>
<map name="pb_previousplanet_Map">
<area shape="poly" alt="" coords="31,5, 31,29, 12,16"
	onmouseover="changeImages('pb_previousplanet', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_previousplanet-sp_pb_pre.gif'); return true;"
	onmouseout="changeImages('pb_previousplanet', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_previousplanet.gif'); return true;"
	onmousedown="changeImages('pb_previousplanet', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_previousplanet-sp_pb_-38.gif'); return true;"
	onmouseup="changeImages('pb_previousplanet', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_previousplanet.gif'); document.location='?page=<?php echo $this->_tpl_vars['page']; ?>
&planet_id=<?php echo $this->_tpl_vars['prevPlanetId']; ?>
'; return true;">
</map>
<map name="pb_ddplanets_Map">
<area shape="poly" alt="" coords="4,5, 23,16, 4,27"
	onmouseover="changeImages('pb_ddplanets', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_ddplanets-sp_pb_nextplan.gif'); return true;"
	onmouseout="changeImages('pb_ddplanets', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_ddplanets.gif'); return true;"
	onmousedown="changeImages('pb_ddplanets', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_ddplanets-sp_pb_nextp-44.gif'); return true;"
	onmouseup="changeImages('pb_ddplanets', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_ddplanets.gif'); document.location='?page=<?php echo $this->_tpl_vars['page']; ?>
&planet_id=<?php echo $this->_tpl_vars['nextPlanetId']; ?>
'; return true;">
</map>
<map name="pb_nextplanet_Map">
<area shape="poly" alt="" coords="14,7, 14,26,  26,26, 38,7 "
	onmouseover="changeImages('pb_nextplanet', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_nextplanet-sp_pb_ddplane.gif'); PlanetDropDown(false); return true;"
	onmouseout="changeImages('pb_nextplanet', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_nextplanet.gif'); return true;"
	onmousedown="changeImages('pb_nextplanet', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_nextplanet-sp_pb_ddpl-49.gif'); PlanetDropDown(true); return true;"
	onmouseup="changeImages('pb_nextplanet', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_nextplanet.gif'); return true;">
</map>
<map name="pb_post_Map">
<area shape="circle" alt="" coords="49,37,27" href="?page=messages"
	onmouseover="changeImages('pb_post', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_post-sp_pb_post_over.gif'); return true;"
	onmouseout="changeImages('pb_post', '<?php if ($this->_tpl_vars['messages'] > 0):  echo $this->_tpl_vars['templateDir']; ?>
/images/pb_post-sp_pb_post_postther.gif<?php else:  echo $this->_tpl_vars['templateDir']; ?>
/images/pb_post.gif<?php endif; ?>');"
	onmousedown="changeImages('pb_post', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_post-sp_pb_post_down.gif'); return true;"
	onmouseup="changeImages('pb_post', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_post-sp_pb_post_over.gif'); return true;">
</map>
<map name="pb_notes_Map">
<area shape="circle" alt="" coords="23,32,18" 
	<?php if ($this->_tpl_vars['noteBox'] == true): ?>
		href="javascript:;" onclick="window.open('show.php?page=notepad','notes','status=no,width=800,height=600,scrollbars=yes');"
	<?php else: ?>
		href="?page=notepad"
	<?php endif; ?>
	onmouseover="changeImages('pb_notes', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_notes-sp_pb_notes_over.gif'); return true;"
	onmouseout="changeImages('pb_notes', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_notes.gif'); return true;"
	onmousedown="changeImages('pb_notes', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_notes-sp_pb_notes_down.gif'); return true;"
	onmouseup="changeImages('pb_notes', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/pb_notes-sp_pb_notes_over.gif'); return true;">
</map>


<map name="TopPanel_02_Map" id="TopPanel_02_Map">
<area shape="poly" alt="" coords="116,14, 207,14, 195,53, 103,53" href="<?php echo $this->_tpl_vars['urlForum']; ?>
" target="_Blank"
	onmouseover="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_forum_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07.gif'); return true;"
	onmousedown="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_forum_down.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_forum_down.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_forum_down.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_forum_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_forum_over.gif'); return true;" />
<area shape="poly" alt="" coords="26,14, 117,14, 105,53, 13,53" href="?page=stats"
	onmouseover="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_statistiken_over.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_statistiken_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06.gif'); return true;"
	onmousedown="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_statistiken_down.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_statistiken_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_statistiken_over.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_statistiken_over.gif'); return true;" />
</map>
<map name="TopPanel_03_Map" id="TopPanel_03_Map">
<area shape="poly" alt="" coords="139,14, 230,14, 218,53, 126,53" href="<?php echo $this->_tpl_vars['urlTeamspeak']; ?>
" target="_Blank"
	onmouseover="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_teamspeak_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08.gif'); return true;"
	onmousedown="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_teamspeak_down.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_teamspeak_down.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_teamspeak_down.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_teamspeak_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_teamspeak_over.gif'); return true;" />
<area shape="poly" alt="" coords="49,14, 140,14, 128,53, 36,53" href="javascript:;" onclick="<?php echo $this->_tpl_vars['chatString']; ?>
"
	onmouseover="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_chat_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_chat_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07.gif'); return true;"
	onmousedown="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_chat_down.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_chat_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_chat_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_chat_over.gif'); return true;" />
<area shape="poly" alt="" coords="-40,14, 51,14, 39,53, -53,53" href="<?php echo $this->_tpl_vars['urlForum']; ?>
" target="_Blank"
	onmouseover="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_forum_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07.gif'); return true;"
	onmousedown="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_forum_down.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_forum_down.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_forum_down.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_forum_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_forum_over.gif'); return true;" />
</map>
<map name="TopPanel_04_Map" id="TopPanel_04_Map">
<area shape="poly" alt="" coords="140,14, 231,14, 219,53, 127,53" href="<?php echo $this->_tpl_vars['urlRules']; ?>
" target="_Blank"
	onmouseover="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_regeln_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_regeln_down.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_regeln_down.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_regeln_down.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_regeln_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_regeln_over.gif'); return true;" />
<area shape="poly" alt="" coords="50,14, 141,14, 129,53, 37,53" href="?page=userconfig"
	onmouseover="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_einstellungen_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_einstellungen_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08.gif'); return true;"
	onmousedown="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_einstellungen_down.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_einstellungen_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_einstellungen_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_einstellungen_over.gif'); return true;" />
<area shape="poly" alt="" coords="-39,14, 52,14, 40,53, -52,53" href="<?php echo $this->_tpl_vars['urlTeamspeak']; ?>
" target="_Blank"
	onmouseover="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_teamspeak_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08.gif'); return true;"
	onmousedown="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_teamspeak_down.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_teamspeak_down.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_teamspeak_down.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_teamspeak_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_teamspeak_over.gif'); return true;" />
</map>
<map name="TopPanel_05_Map" id="TopPanel_05_Map">
<area shape="poly" alt="" coords="152,0, 135,48, 155,48, 162,44, 167,37, 180,-1" href="?logout=1"
	onmouseover="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_logout_over.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_logout_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_logout_down.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_logout_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_logout_up.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_logout_over.gif'); return true;" />
<area shape="poly" alt="" coords="50,14, 141,14, 129,53, 37,53" href="<?php echo $this->_tpl_vars['urlHelpcenter']; ?>
" target="_Blank"
	onmouseover="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_helpcenter_over.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_helpcenter_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_helpcenter_down.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_helpcenter_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_helpcenter_over.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_helpcenter_over.gif'); return true;" />
<area shape="poly" alt="" coords="-39,14, 52,14, 40,53, -52,53" href="<?php echo $this->_tpl_vars['urlRules']; ?>
"
	onmouseover="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_regeln_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_regeln_down.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_regeln_down.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_regeln_down.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_regeln_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_regeln_over.gif'); return true;" />
</map>
<map name="TopPanel_06_Map" id="TopPanel_06_Map">
<area shape="poly" alt="" coords="116,-28, 207,-28, 195,11, 103,11" href="<?php echo $this->_tpl_vars['urlForum']; ?>
" target="_blank"
	onmouseover="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_forum_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07.gif'); return true;"
	onmousedown="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_forum_down.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_forum_down.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_forum_down.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_forum_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_forum_over.gif'); return true;" />
<area shape="poly" alt="" coords="26,-28, 117,-28, 105,11, 13,11" href="?page=stats"
	onmouseover="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_statistiken_over.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_statistiken_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06.gif'); return true;"
	onmousedown="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_statistiken_down.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_statistiken_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_statistiken_over.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_statistiken_over.gif'); return true;" />
</map>
<map name="TopPanel_07_Map" id="TopPanel_07_Map">
<area shape="poly" alt="" coords="139,-28, 230,-28, 218,11, 126,11" href="<?php echo $this->_tpl_vars['urlTeamspeak']; ?>
" target="_blank"
	onmouseover="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_teamspeak_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08.gif'); return true;"
	onmousedown="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_teamspeak_down.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_teamspeak_down.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_teamspeak_down.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_teamspeak_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_teamspeak_over.gif'); return true;" />
<area shape="poly" alt="" coords="49,-28, 140,-28, 128,11, 36,11" href="javascript:;" onclick="<?php echo $this->_tpl_vars['chatString']; ?>
"
	onmouseover="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_chat_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_chat_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07.gif'); return true;"
	onmousedown="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_chat_down.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_chat_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_chat_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_chat_over.gif'); return true;" />
<area shape="poly" alt="" coords="-40,-28, 51,-28, 39,11, -53,11" href="<?php echo $this->_tpl_vars['urlForum']; ?>
" target="_Blank"
	onmouseover="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_forum_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07.gif'); return true;"
	onmousedown="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_forum_down.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_forum_down.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_forum_down.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_forum_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_02', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_02-imap_forum_over.gif', 'TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_forum_over.gif', 'TopPanel_06', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_06-imap_forum_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_forum_over.gif'); return true;" />
</map>
<map name="TopPanel_08_Map" id="TopPanel_08_Map">
<area shape="poly" alt="" coords="140,-28, 231,-28, 219,11, 127,11" href="<?php echo $this->_tpl_vars['urlRules']; ?>
" target="_blank"
	onmouseover="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_regeln_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_regeln_down.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_regeln_down.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_regeln_down.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_regeln_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_regeln_over.gif'); return true;" />
<area shape="poly" alt="" coords="50,-28, 141,-28, 129,11, 37,11" href="?page=userconfig"
	onmouseover="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_einstellungen_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_einstellungen_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08.gif'); return true;"
	onmousedown="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_einstellungen_down.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_einstellungen_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_einstellungen_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_einstellungen_over.gif'); return true;" />
<area shape="poly" alt="" coords="-39,-28, 52,-28, 40,11, -52,11" href="<?php echo $this->_tpl_vars['urlTeamspeak']; ?>
" target="_Blank"
	onmouseover="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_teamspeak_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08.gif'); return true;"
	onmousedown="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_teamspeak_down.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_teamspeak_down.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_teamspeak_down.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_teamspeak_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_03', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_03-imap_teamspeak_over.gif', 'TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_teamspeak_over.gif', 'TopPanel_07', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_07-imap_teamspeak_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_teamspeak_over.gif'); return true;" />
</map>
<map name="TopPanel_09_Map" id="TopPanel_09_Map">
<area shape="poly" alt="" coords="152,-42, 135,6, 155,6, 162,2, 167,-5, 180,-43" href="?logout=1"
	onmouseover="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_logout_over.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_logout_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_logout_down.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_logout_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_logout_up.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_logout_over.gif'); return true;" />
<area shape="poly" alt="" coords="50,-28, 141,-28, 129,11, 37,11" href="<?php echo $this->_tpl_vars['urlHelpcenter']; ?>
" target="_Blank"
	onmouseover="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_helpcenter_over.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_helpcenter_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_helpcenter_down.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_helpcenter_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_helpcenter_over.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_helpcenter_over.gif'); return true;" />
<area shape="poly" alt="" coords="-39,-28, 52,-28, 40,11, -52,11" href="<?php echo $this->_tpl_vars['urlRules']; ?>
"
	onmouseover="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_regeln_over.gif'); return true;"
	onmouseout="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09.gif'); return true;"
	onmousedown="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_regeln_down.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_regeln_down.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_regeln_down.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_regeln_down.gif'); return true;"
	onmouseup="changeImages('TopPanel_04', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_04-imap_regeln_over.gif', 'TopPanel_05', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_05-imap_regeln_over.gif', 'TopPanel_08', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_08-imap_regeln_over.gif', 'TopPanel_09', '<?php echo $this->_tpl_vars['templateDir']; ?>
/images/TopPanel_09-imap_regeln_over.gif'); return true;" />
</map>