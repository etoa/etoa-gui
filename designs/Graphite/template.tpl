	<div id="sidebar">
		
		<a href="?page=overview" id="logo"></a>
		
		<div id="navcontainer">
			<a href="?page=planetoverview" id="navplanetoverview"></a>
			<a href="?page=economy" id="naveconomy"></a>
			<a href="?page=population" id="navpopulation"></a>
			<a href="?page=haven" id="navhaven"></a>
			<a href="?page=market" id="navmarket"></a>
			<a href="?page=crypto" id="navcrypto"></a>
			<a href="?page=recycle" id="navrecycle"></a>
            <a href="?page=bunker" id="navbunker"></a>
			<a></a>
			<a href="?page=buildings" id="navbuildings"></a>
			<a href="?page=research" id="navresearch"></a>
			<a href="?page=shipyard" id="navshipyard"></a>
			<a href="?page=defense" id="navdefense"></a>
			<a href="?page=missiles" id="navmissiles"></a>
		</div>
		<div id="planetimage">
			<a href="?page=planetoverview">
				<img src="{$currentPlanetImage}" alt="Planet" style="width:100px;height:100px;" />
			</a>
		</div>
		<a id="prevEntity" href="?page={$page}&change_entity={$prevPlanetId}"></a>
		<a id="nextEntity" href="?page={$page}&change_entity={$nextPlanetId}"></a>
		<div id="planetname">
			{$selectField}
		</div>		
	</div>
	<div id="hbar">
		<a href="?page=stats" id="navstats" onmouseover="hideAllHbarMenus();"></a>
		{literal}<a href="?page=cell" id="navmap" onmouseover="		
				if (document.getElementById('hbarmapslide').style.display=='none') { document.getElementById('hbarmapslide').style.display='';	}
				document.getElementById('hbarallianceslide').style.display='none';
				document.getElementById('hbarsettingsslide').style.display='none';
				document.getElementById('hbarhelpslide').style.display='none';
				document.getElementById('hbarlogoutslide').style.display='none';
				return false;"></a>{/literal}		
		{if $fleetAttack > 0}
			<a href="?page=fleets" id="navfleetred" onmouseover="hideAllHbarMenus();"></a>
			<script type="text/javascript">
				{literal}	Effect.Pulsate('navfleetred',{duration:180,pulses:120,from:0.0}); {/literal}
			</script>	
		{/if}
		<a href="?page=fleets" id="navfleet" onmouseover="hideAllHbarMenus();"></a>
		{if $messages > 0}
			<a href="?page=messages" id="navmessagesgreen" onmouseover="hideAllHbarMenus();"></a>
			<script type="text/javascript">
				{literal}	Effect.Pulsate('navmessagesgreen',{duration:180,pulses:120,from:0.0}); {/literal}
			</script>	
		{/if}
		<a href="?page=messages" id="navmessages" onmouseover="hideAllHbarMenus();"></a>
		{literal}<a href="?page=alliance" id="navalliance" onmouseover="
				if (document.getElementById('hbarallianceslide').style.display=='none') { document.getElementById('hbarallianceslide').style.display='' } 
				document.getElementById('hbarmapslide').style.display='none';
				document.getElementById('hbarsettingsslide').style.display='none';
				document.getElementById('hbarhelpslide').style.display='none';
				document.getElementById('hbarlogoutslide').style.display='none';
				return false;"></a>{/literal}		
		{if $buddyreq>0}<a href="?page=buddylist" id="navbuddylistred" onmouseover="hideAllHbarMenus();"></a>
		{elseif $buddys > 0}<a href="?page=buddylist" id="navbuddylistgreen" onmouseover="hideAllHbarMenus();"></a>
		{else}<a href="?page=buddylist" id="navbuddylist" onmouseover="hideAllHbarMenus();"></a>{/if}
		{literal}<a href="?page=userconfig" id="navuserconfig" onmouseover="
				if (document.getElementById('hbarsettingsslide').style.display=='none') { document.getElementById('hbarsettingsslide').style.display=''; }
				document.getElementById('hbarallianceslide').style.display='none';
				document.getElementById('hbarmapslide').style.display='none';
				document.getElementById('hbarhelpslide').style.display='none';
				document.getElementById('hbarlogoutslide').style.display='none';
				return false;"></a>{/literal}
		{literal}<a href="?page=help" id="navhelp" onmouseover="		
				if (document.getElementById('hbarhelpslide').style.display=='none'){ document.getElementById('hbarhelpslide').style.display=''; }
				document.getElementById('hbarsettingsslide').style.display='none';
				document.getElementById('hbarallianceslide').style.display='none';
				document.getElementById('hbarmapslide').style.display='none';
				document.getElementById('hbarlogoutslide').style.display='none';
				return false;"></a>{/literal}		
		{literal}<a href="?logout=1" id="navlogout" onmouseover="		
				if (document.getElementById('hbarlogoutslide').style.display=='none'){ document.getElementById('hbarlogoutslide').style.display='' }
				document.getElementById('hbarhelpslide').style.display='none';
				document.getElementById('hbarsettingsslide').style.display='none';
				document.getElementById('hbarallianceslide').style.display='none';
				document.getElementById('hbarmapslide').style.display=='none'; 
				return false;"></a>{/literal}
	</div>
	<div id="hbarmap">
		<div id="hbarmapslide" style="display:none;">
			<div>
				<a href="?page=cell">Sonnensystem</a>		
				<a href="?page=sector">Sektor</a>
				<a href="?page=galaxy">Galaxie</a>
			</div>
		</div>
	</div>	
	<div id="hbaralliance">
		<div id="hbarallianceslide" style="display:none;">
			<div>
				<a href="?page=alliance">Allianz</a>
				<a href="?page=allianceboard">Allianzforum</a>		
				<a href="?page=alliance&amp;action=base">Allianzbasis</a>		
				<a href="?page=townhall">Rathaus</a>
			</div>
		</div>
	</div>
	<div id="hbarsettings">
		<div id="hbarsettingsslide" style="display:none;">
			<div>
				<a href="?page=userconfig">Einstellungen</a>
				<a href="?page=bookmarks">Favoriten</a>
				<a href="?page=userinfo">Profil</a>
				<a href="?page=notepad">Notizen</a>		
			</div>
		</div>
	</div>
	<div id="hbarhelp">
		<div id="hbarhelpslide" style="display:none;">
			<div>
				<a href="?page=help">Hilfe</a>
				<a href="?page=techtree">Technikbaum</a>
				<a href="?page=ticket">Ticketsystem</a>
				<a href="#" onclick="window.open('{$bugreportUrl}');">Fehler melden</a>
				<a href="#" onclick="{$helpcenterOnclick}">Häufige Fragen</a>
				<a href="?page=contact">Über EtoA</a>
			</div>
		</div>
	</div>
	<div id="hbarlogout">
		<div id="hbarlogoutslide" style="display:none;">
			<div>
				<a href="?logout=1">Logout</a>
				<a href="#" onclick="window.open('{$urlForum}')">Forum</a>
				<a href="#" onclick="{$chatOnclick}">Chat</a>
				<a href="#" onclick="{$teamspeakOnclick}">Teamspeak</a>
				<a href="#" onclick="{$rulesOnclick}">Regeln</a>
			</div>
		</div>
	</div>	
	
	<div id="servertime">
		{$serverTime}
	</div>
	<script type="text/javascript">
		document.onload = time({$serverTimeUnix},'servertime',1);
		
	</script>
	
	<div id="contentcontainer">
		{$content}
	</div>