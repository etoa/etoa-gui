	<table id="layoutbox" width="{$gameWidth}%">
		<tr>
			<td id="minibar">				
			</td>
			<td id="topbar" colspan="2">
				{section name=id loop=$topNav}
				{strip}
					{if $topNav[id].onclick neq ""}
						<a href="javascript:;" onclick="{$topNav[id].onclick}">{$topNav[id].name}</a> | 
					{else}
						<a href="{$topNav[id].url}" target="_blank">{$topNav[id].name}</a> | 
					{/if}
				{/strip}
				{/section}
				<a href="?logout=1">Logout</a>
			</td>
		</tr>
		<tr>
			<td id="logo">&nbsp;</td>
			<td id="banner" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td id="menu1">
				{$selectField}
				
				{section name=id loop=$gameNav}
				{strip}
					<div>{$gameNav[id].cat}</div>
					{section name=iid loop=$gameNav[id].items}
					{strip}
					
						{if $gameNav[id].items[iid].name == "Buddylist" && $buddys > 0}
							<a href="{$gameNav[id].items[iid].url}" style="color:#0f0">
						{elseif $gameNav[id].items[iid].name == "Flotten" && $fleetAttack > 0}
							<a href="{$gameNav[id].items[iid].url}" style="color:#f00">
						{elseif $gameNav[id].items[iid].name == "Nachrichten" && $messages > 0}
							<a href="{$gameNav[id].items[iid].url}" style="color:#0f0">
						{elseif $gameNav[id].items[iid].name == "Notizen" && $noteBox == true}
							<a href="javascript:;" onclick="window.open('show.php?page=notepad','notes','status=no,width=800,height=600,scrollbars=yes,resizable=yes');">
						{elseif $gameNav[id].items[iid].name == "Hilfe" && $helpBox == true}
							<a href="javascript:;" onclick="window.open('show.php?page=help','help','status=no,width=800,height=600,scrollbars=yes,resizable=yes');">
						{else}
							<a href="{$gameNav[id].items[iid].url}">
						{/if}
	
						{$gameNav[id].items[iid].name}
						
						{if $notes > 0 && $gameNav[id].items[iid].name == "Notizen"}
							&nbsp;({$notes} vorhanden)</a>
						{elseif $gameNav[id].items[iid].name == "Flotten" && $fleetAttack > 0}
							&nbsp;({$fleetAttack} fremde)</a>														
						{elseif $buddys > 0 && $gameNav[id].items[iid].name == "Buddylist"}
							({$buddys} online)
						{elseif $gameNav[id].items[iid].name == "Nachrichten" && $messages > 0}		
							{if $blinkMessages == true}
								&nbsp;<blink>({$messages} neu)</blink>
							{else}
								&nbsp;({$messages} neu)
							{/if}							
						{/if}
						</a>							
					{/strip}
					{/section}
				{/strip}
				{/section}				
			</td>
			<td id="content">