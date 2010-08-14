<?PHP
	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	//
	
	/**
	* Manage buddys
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	


	echo '<h1>Buddylist</h1>';
?>


<script type="text/javascript">
function setClickable(obj, i) {
	$(obj).click(function() {
		$(obj).parent().addClass("editing");
		var textarea = '<div><textarea style="width:100%;height:100%"">'+$(this).html()+'</textarea></div>';
		var button	 = '<div><a href="javascript:;" class="saveButton" title="Speichern"><img alt="check" src="images/icons/check.png"></a><a href="javascript:;" class="cancelButton" title="Abbrechen"><img alt="delete" src="images/icons/delete.png"></a></div>';
		var revert = $(obj).html();
		var iconsRevert = $(obj).parent().parent().find(".icons").parent().html();
		$(obj).parent().parent().find(".icons").after(button).remove();
		
		$(obj).after(textarea).remove();
		$('.saveButton').click(function(){saveChanges(this, iconsRevert, false, i);});
		$('.cancelButton').click(function(){saveChanges(this, iconsRevert, revert, i);});
	});
}//end of function setClickable

function saveChanges(obj, actions, cancel, n) {
	if(!cancel) {
		var t = $(obj).parent().parent().parent().find(".editing").find("textarea").val();
		xajax_saveEdit(t,t);

	}
	else {
		var t = cancel;
	}
	if(t=='') t='(click to add text)';
	$(obj).parent().parent().parent().find(".editing").find("div").after('<p class="editable">'+t+'</p>').remove();
	$(obj).parent().parent().parent().find(".editing").removeClass("editing");
	$(obj).parent().after(actions).remove();
	
	setClickable($("p").get(n), n);
}

function setRemovable(obj, page, action, callback) {
	$(obj).click(function() {
		var id = $(obj).parent().find('input').val();
		var tr = $(obj).parent().parent().parent();
		var tbody = tr.parent();

		if (action == "declineBuddy") {
			xajax_declineBuddy(id);
		}
		else {
			xajax_acceptBuddy(id,1);
		}
		
		tr.remove();
		if ( !tbody.find("td").length > 0 )
		{
			tbody.parent().remove();
		}
		
		if (callback)
		{
			var buddylist = $("buddylist").find("TBODY");

		}
		return false;
	});
}

$(function() {
        $("a.msg").click(function(e) {
            e.preventDefault();
            console.log($(this).parent().parent().parent().attr('id'));
            $.fancybox({
		'hideOnContentClick': false,
                'href': 'jQuery/msg.php',
                'type': 'ajax',
                'ajax': { type : 'POST',
                          data : 'userId=1&preview=true' }
            });
        });

	var cache = {};
	function split(val) {
		return val.split(/,\s*/);
	}
	function extractLast(term) {
		return split(term).pop();
	}
	
	$("#usernick").autocomplete({
		delay: 500,
		source: function(request, response) {
			request.term = extractLast(request.term);
			if ( request.term in cache ) {
				response( cache[ request.term ] );
				return;
			}
			
			$.ajax({
				url: "search/user.php",
				dataType: "json",
				data: request,
				success: function( data ) {
					cache[ request.term ] = data;
					response( data );
				}
			});
		},
		search: function() {
			// custom minLength
			var term = extractLast(this.value);
			if (term.length < 2) {
				return false;
			}
		},
		focus: function() {
			// prevent value inserted on focus
			return false;
		},
		select: function(event, ui) {
			var terms = split( this.value );
			// remove the current input
			terms.pop();
			// add the selected item
			terms.push( ui.item.value );
			// check that every element is just once in the list
			terms = $.unique(terms);
			terms = $.unique(terms);
			// add placeholder to get the comma-and-space at the end
			terms.push("");
			this.value = terms.join(", ");
			return false;
		}
	});
});
</script>
<?PHP
	//
	// Erlaubnis erteilen
	//
	if (isset($_GET['allow']) && $_GET['allow']>0)
	{
		if (true)
			ok_msg("Erlaubnis erteilt!");
		else
			err_msg("Die Erlaubnis kann nicht erteilt werden weil die Anfrage gel&ouml;scht wurde!");
	}

	//
	// Erlaubnis verweigern
	//
	if (isset($_GET['deny']) && $_GET['deny']>0)
	{
		if (TRUE)
			ok_msg("Die Anfrage wurde gel&ouml;scht!");
		else
			err_msg("Die Anfrage konnte nicht gel&ouml;scht werden weil sie nicht mehr existiert!");
	}

	//
	// Freund hinzufügen
	//
	if ((isset($_POST['usernick']) && $_POST['usernick']!="" && $_POST['submit_buddy']!="") || (isset($_GET['add_id']) && $_GET['add_id']>0))
	{
		if (TRUE)
		{
			ok_msg("[b]".$arr['user_nick']."[/b] wurde zu deiner Liste hinzugef&uuml;gt und ihm wurde eine Best&auml;tigungsnachricht gesendet!");
			send_msg($arr['user_id'],5,"Buddylist-Anfrage von ".$cu->nick,"Der Spieler will dich zu seiner Freundesliste hinzuf&uuml;gen.\n\n[url ?page=buddylist]Anfrage bearbeiten[/url]");
		}
		else
			err_msg("Dieser Eintrag ist schon vorhanden!");
			err_msg("Du kannst nicht dich selbst zur Buddyliste hinzuf&uuml;gen!");
			err_msg("Der Spieler [b]".$_POST['buddy_nick']."[/b] konnte nicht gefunden werden!");
	}

	//
	// Entfernen
	//
	if (isset($_GET['remove']) && $_GET['remove']>0)
	{
		ok_msg("Der Spieler wurde von der Freundesliste entfern!");
	}

	//
	// In einer anderen Liste entfernen
	//
	if (isset($_GET['removeremote']) && $_GET['removeremote']>0)
	{
		dbquery("DELETE FROM buddylist WHERE bl_user_id='".$_GET['removeremote']."' AND bl_buddy_id='".$cu->id."';");
	}
	
	// start new script
	                    
	$buddys = $cu->buddylist->getIterator();
	
	if ($cu->buddylist->buddyCount)
	{
		$store = array();
		Design::tableStart('Meine Freunde', 'buddylist');
		echo '<tr>
				<th class="tbltitle">Nick</th>
				<th class="tbltitle">Punkte</th>
				<th class="tbltitle">Hauptplanet</th>
				<th class="tbltitle">Online</th>
				<th class="tbltitle">Kommentar</th>
				<th class="tbltitle" style="width:95px">Aktion</th>
		</tr>';

		// loop through every buddy
		while( $buddys->valid() )
		{
			$store = $buddys->current();
			$allow = $store['allow'];
			$comment = $store['comment'];
			$buddy = $store['user'];

			echo createBuddyRow($buddy, $allow, $comment);

			$buddys->next();
		}
		
		// create the add alliance member button if the user is in an alliance
		if ( $cu->allianceId() )
		{
			$abutton = '<input type="submit" name="submit_buddy" value="&nbsp;A&nbsp;" title="Allianzmitglieder hinzufügen" onClick="xajax_addAllianceMembers(\''.$cu->allianceId().'\');" )/>';
		}
		else
		{
			$abutton = '';
		}
		Design::tableEnd('<input type="submit" name="submit_buddy" value="&nbsp;+&nbsp;" title="Neuen Freund hinzufügen" onclick="toggleBox(\'addbuddy\');" />'.$abutton, 'button');
	}


	
	echo '<form id="buddyform" onsubmit="xajax_addBuddy(xajax.getFormValues(\'buddyform\'));return false;">';
	Design::tableStart('Freund hinzufügen', 'addbuddy', '', 'nondisplay');
	echo '<tr>
			<td colspan="4" class="tbldata">F&uuml;ge Freunde zu deiner Buddylist hinzu um auf einen Blick zu sehen wer alles online ist:</td>
		</tr>
			<th class="tbltitle"><label for="usernick">Nick: </label></th>
			<td class="tbldata">
				<input type="text" name="usernick" id="usernick" maxlength="20" size="20" autocomplete="off" value="">
			</td>
			<td class="tbldata">
				<input type="text" name="comment" id="comment" maxlength="20" size="20" autocomplete="off" value="Kommentar hinzufügen">
			</td>
			<td class="tbldata">
				<input type="submit" name="submit_buddy" value="Freund hinzuf&uuml;gen" />
			</td>
		</tr>';
	Design::tableEnd();
	echo '	</form>';


	$buddys = $cu->buddylist->getIterator('requests');

	if ($cu->buddylist->requestCount)
	{
		$store = array();
		tableStart('Offene Anfragen');
		echo '<tr>
				<th class="tbltitle">Nick</th>
				<th class="tbltitle">Punkte</th>
				<th class="tbltitle">Kommentar</th>
				<th class="tbltitle" style="width:95px">Aktion</th>
		</tr>';
		

		// loop through every buddy
		while( $buddys->valid() )
		{
			$store = $buddys->current();
			$buddy = $store['user'];
			$comment = $store['comment'];

			// check several options to choose the right font-color (banned, admin, ...
			$class = getFormatingColorByUser($buddy);

			// create the row
			echo '<tr id="'.$buddy->id.'">
					<td class="tbldata '.$class.'" >
						<div id="ttuser'.$buddy->id.'" style="display:none;">
							'.popUp('Profil anzeigen','page=userinfo&id='.$buddy->id).'<br/>
							'.popUp('Punkteverlauf','page=$page&amp;mode=$mode&amp;userdetail='.$buddy->id).'<br/>
							<a href="?page=messages&mode=new&message_user_to='.$buddy->id.'">Nachricht senden</a>
						</div>
						<a class="'.$class.'" href="#" '.cTT($buddy,"ttuser".$buddy->id).'>'.$buddy.'</a>
					</td>
					<td class="tbldata '.$class.'">'.nf($buddy->points).'</td>
					<td class="tbldata '.$class.'">'.$comment.'</td>';
			
			// and the last part, the icons
			echo '<td>
					<p class="icons">
						<input id="id['.$buddy->id.']" type="hidden" value="'.$buddy->id.'">
						<a class="msg"href="?page=messages&amp;mode=new&amp;message_user_to='.$arr['user_id'].'" title="Nachricht senden">'.icon('mail').'</a>
						<a href="?page=userinfo&amp;id='.$arr['user_id'].'" title="Profil anzeigen">'.icon('profil').'</a>
						<a id="accept['.$buddy->id.']" class="accept" href="#" title="Annehmen">'.icon('check').'</a>
						<a class="deny" href="#" title="Zurückweisen">'.icon('delete').'</a>
					</p>
				</td>
			</tr>';
			$buddys->next();
		}
		tableEnd();
	}

	echo JS::jqClickable('editable');
	echo JS::jqRemovable('deny', 'declineBuddy');
	echo JS::jqRemovable('accept', 'acceptBuddy');

?>
