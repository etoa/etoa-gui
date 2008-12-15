	<div id="sidebar">
		
		<a href="?page=overview" id="logo"></a>
		
		<div id="navcontainer">
			<a href="?page=planetoverview" id="navplanetoverview"></a>
			<a href="?page=economy" id="naveconomy"></a>
			<a href="?page=population" id="navpopulation"></a>
			<a href="?page=haven" id="navhaven"></a>
			<a href="?page=buildings" id="navbuildings"></a>
			<a href="?page=research" id="navresearch"></a>
			<a href="?page=shipyard" id="navshipyard"></a>
			<a href="?page=defense" id="navdefense"></a>
			<a href="?page=market" id="navmarket"></a>
			<a href="?page=crypto" id="navcrypto"></a>
			<a href="?page=recycle" id="navrecycle"></a>
		</div>
		<div id="planetimage">
			<img src="{$currentPlanetImage}" alt="Planet" style="width:100px;height:100px;" />
		</div>
		{literal}
		<div id="planetname" 
			onmouseover="if (document.getElementById('planetlist').style.display=='none') Effect.Appear('planetlist',{duration:0.1});" 
			onmouseout="document.getElementById('planetlist').style.display='none'">
		{/literal}
			{$currentPlanetName}
		</div>	
		{literal}
		<div id="planetlist" style="display:none;" 
			onmouseover="this.style.display='block'" 
			onmouseout="this.style.display='none'" 
			>	
		{/literal}
				<div>
					{$planetListImages}
				</div>
		</div>
	</div>
	
	{literal}
	<script type="text/javascript">
		var scptEffectRunning = 0;
		function hideAllHbarMenus()
		{		
			if (scptEffectRunning==0)
			{
				if (document.getElementById('hbarmapslide').style.display=='') Effect.SlideUp('hbarmapslide',{duration:0.1}); 
				if (document.getElementById('hbarallianceslide').style.display=='') Effect.SlideUp('hbarallianceslide',{duration:0.1}); 
				if (document.getElementById('hbarsettingsslide').style.display=='') Effect.SlideUp('hbarsettingsslide',{duration:0.1}); 
				if (document.getElementById('hbarhelpslide').style.display=='') Effect.SlideUp('hbarhelpslide',{duration:0.1}); 
			}
		}
	</script>
	{/literal}
		
	<div id="hbar">
		<a href="?page=stats" id="navstats" onmouseover="hideAllHbarMenus();"></a>
		<a href="?page=cell" id="navmap" onmouseover="
		{literal}
				if (document.getElementById('hbarmapslide').style.display=='none' && scptEffectRunning==0) { scptEffectRunning=1; Effect.SlideDown('hbarmapslide',{duration:0.2,afterFinish:function(){scptEffectRunning=0;}}); }
				if (document.getElementById('hbarallianceslide').style.display=='') Effect.SlideUp('hbarallianceslide',{duration:0.1}); 
				if (document.getElementById('hbarsettingsslide').style.display=='') Effect.SlideUp('hbarsettingsslide',{duration:0.1}); 
				if (document.getElementById('hbarhelpslide').style.display=='') Effect.SlideUp('hbarhelpslide',{duration:0.1}); 
				return false;"></a>
		{/literal}
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
		<a href="?page=alliance" id="navalliance" onmouseover="
		{literal}
				if (document.getElementById('hbarallianceslide').style.display=='none' && scptEffectRunning==0) { scptEffectRunning=1; Effect.SlideDown('hbarallianceslide',{duration:0.2,afterFinish:function(){scptEffectRunning=0;}}); } 
				if (document.getElementById('hbarmapslide').style.display=='') Effect.SlideUp('hbarmapslide',{duration:0.1}); 
				if (document.getElementById('hbarsettingsslide').style.display=='') Effect.SlideUp('hbarsettingsslide',{duration:0.1}); 
				if (document.getElementById('hbarhelpslide').style.display=='') Effect.SlideUp('hbarhelpslide',{duration:0.1}); 
				return false;"></a>
		{/literal}
		
		{if $buddyreq>0}
				<a href="?page=buddylist" id="navbuddylistred" onmouseover="hideAllHbarMenus();"></a>
		{elseif $buddys > 0}
				<a href="?page=buddylist" id="navbuddylistgreen" onmouseover="hideAllHbarMenus();"></a>
		{else}
				<a href="?page=buddylist" id="navbuddylist" onmouseover="hideAllHbarMenus();"></a>
		{/if}
		<a href="?page=userconfig" id="navuserconfig" onmouseover="
		{literal}
				if (document.getElementById('hbarsettingsslide').style.display=='none' && scptEffectRunning==0) { scptEffectRunning=1; Effect.SlideDown('hbarsettingsslide',{duration:0.2,afterFinish:function(){scptEffectRunning=0;}}); }
				if (document.getElementById('hbarallianceslide').style.display=='') Effect.SlideUp('hbarallianceslide',{duration:0.1}); 
				if (document.getElementById('hbarmapslide').style.display=='') Effect.SlideUp('hbarmapslide',{duration:0.1}); 
				if (document.getElementById('hbarhelpslide').style.display=='') Effect.SlideUp('hbarhelpslide',{duration:0.1}); 
				return false;"></a>
		{/literal}
		<a href="?page=help" id="navhelp" onmouseover="
		{literal}
				if (document.getElementById('hbarhelpslide').style.display=='none' && scptEffectRunning==0){ scptEffectRunning=1;  Effect.SlideDown('hbarhelpslide',{duration:0.2,afterFinish:function(){scptEffectRunning=0;}}); }
				if (document.getElementById('hbarsettingsslide').style.display=='') Effect.SlideUp('hbarsettingsslide',{duration:0.1}); 
				if (document.getElementById('hbarallianceslide').style.display=='') Effect.SlideUp('hbarallianceslide',{duration:0.1}); 
				if (document.getElementById('hbarmapslide').style.display=='') Effect.SlideUp('hbarmapslide',{duration:0.1}); 
				return false;"></a>
		{/literal}
		<a href="?logout=1" id="navlogout" onmouseover="hideAllHbarMenus();"></a>
	</div>


	<div id="hbarmap">
		<div id="hbarmapslide" style="display:none;">
			<div>
				<a href="?page=cell">Sonnensystem</a>		
				<a href="?page=map">Sektor</a>
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
				<a href="?page=contact">Über EtoA</a>
			</div>
		</div>
	</div>
	
	<div id="servertime">
		{$serverTime}
	</div>
	
	<div id="contentcontainer">