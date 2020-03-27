<?php

$twig->addGlobal("title", "Support-Tickets");

if ($cu->hasRole("master,super-admin,game-admin,trial-game-admin")) {

echo '<div>[ <a href="?page='.$page.'">Aktive Tickets</a> |
<a href="?page='.$page.'&amp;action=new">Neues Ticket erstellen</a> |
<a href="?page='.$page.'&amp;action=closed">Bearbeitete Tickets</a> ]</div>' ;



if (isset($_GET['edit']) && $_GET['edit']>0)
{
	echo "<h2>Ticket bearbeiten</h2>";
	$ti = new Ticket($_GET['edit']);

	echo '<form action="?page='.$page.'&amp;id='.$ti->id.'" method="post">';
	tableStart("Ticket ".$ti->idString);
	echo '<tr><th>Kategorie:</th><td colspan="3">';
	htmlSelect("cat_id",Ticket::getCategories(),$ti->catId);
	echo '</td></tr>';
	echo '<tr><th>User:</th><td>';
	echo '<a href=\"javascript:;\" '.cTT($ti->userNick,"ttuser").'>'.$ti->userNick.'</a>';
	echo '</td>';
	echo '<th>Zugeteilter Admin:</th><td>';
	$sdata = AdminUser::getArray();
	$sdata[0] = "(Niemand)";
	htmlSelect("admin_id",$sdata,$ti->adminId);
	echo '</td></tr>';
	echo '<tr><th>Status:</th><td>';
	htmlSelect("status",Ticket::$statusItems,$ti->status);
	echo '</td>';
	echo '<th>Lösung:</th><td>';
	htmlSelect("solution",Ticket::$solutionItems,$ti->solution);
	echo '</td></tr>';
	echo '<tr><th>Admin-Kommentar:</th><td colspan="3">';
	echo '<textarea name="admin_comment" rows="5" cols="60">'.$ti->adminComment.'</textarea>';
	echo '</td></tr>';
	tableEnd();
	echo '<input type="submit" name="submit" value="Änderungen übernehmen" /> &nbsp;
	'.button("Abbrechen","?page=$page&amp;id=".$ti->id."").' &nbsp;';
	echo "</form>";


}

//
// Display ticket details
//
elseif (isset($_GET['id']) && $_GET['id']>0)
{
	echo "<h2>Ticket-Details</h2>";
	$ti = new Ticket($_GET['id']);

	if (isset($_POST['submit']))
	{
		$ti->status = $_POST['status'];
		$ti->solution = $_POST['solution'];
		$ti->catId = $_POST['cat_id'];
		$ti->adminId = $_POST['admin_id'];
		$ti->adminComment = $_POST['admin_comment'];

		if ($ti->changed)
			success_msg("Ticket aktualisiert!");
	}
	if (isset($_POST['submit_assign']))
	{
		$ti->assign($cu->id);
		if ($ti->changed)
			success_msg("Ticket aktualisiert!");
	}
	if (isset($_POST['submit_reopen']))
	{
		$ti->reopen();
		if ($ti->changed)
			success_msg("Ticket aktualisiert!");
	}

	if (isset($_POST['submit_new_post']))
	{
		// Do not inform the user with pm, because the close function does this already
		// BUGFIX by river: But *only* if it actually *is* closed! wtf.
		if (
				$ti->addMessage(
					array("admin_id"=>$cu->id,"message"=>$_POST['message']),
					(isset($_POST['checkclose'])?0:1)
				)
			)
		{
			success_msg("Nachricht hinzugefügt!");
			if (isset($_POST['checkclose']))
			{
				$ti->close($_POST['solutionclose']);
			}
		}

		if (isset($_POST['admin_comment']))
		{
			$ti->adminComment = $_POST['admin_comment'];
		}
	}
	if (isset($_POST['submit_admin_comment']))
	{
		$ti->adminComment = $_POST['admin_comment'];
	}


	echo "<div id=\"ttuser\" style=\"display:none;\">
	".openerLink("page=user&sub=edit&id=".$ti->userId,"Daten anzeigen")."<br/>
	".popupLink("sendmessage","Nachricht senden","","id=".$ti->userId)."<br/>
	</div>";


	echo '<form action="?page='.$page.'&amp;id='.$_GET['id'].'" method="post">';
	tableStart("Ticket ".$ti->idString);
	echo '<tr><th style="width:150px">Kategorie:</th><td>';
	echo $ti->catName;
	echo '</td></tr>';
	echo '<th>Status:</th><td>';
	echo $ti->statusName;
	echo '</td></tr>';
	echo '<tr><th>User:</th><td>';
	echo '<a href="javascript:;" '.cTT($ti->userNick,"ttuser").'>'.$ti->userNick.'</a>';
	echo '</td></tr>';
	if ($ti->adminId > 0)
	{
		echo '<th>Zugeteilter Admin:</th><td>';
		echo $ti->adminNick;
		echo '</td></tr>';
	}
	echo '<tr><th>Letzte Änderung:</th><td>';
	echo df($ti->time);
	echo '</td></tr>';
	echo '<tr><th>Admin-Kommentar:</th><td colspan="3">';
	echo '<textarea name="admin_comment" style="color:#00008B" rows="4" cols="60">'.$ti->adminComment.'</textarea>
	<input type="submit" name="submit_admin_comment" value="Speichern" /> (wird auch beim Senden einer neuen Nachricht gespeichert)';
	echo '</td></tr>';
	tableEnd();


	tableStart("Nachrichten");
	echo "<tr>
	<th style=\"width:120px\">Datum</th>
	<th style=\"width:130px\">Autor</th>
	<th>Nachricht</th></tr>";
	foreach ($ti->getMessages() as $mi)
	{
		echo "<tr>
		<td>".df($mi->timestamp)."</td>
		<td>".$mi->authorNick."</td>
		<td>".text2html($mi->message)."</td>
		</tr>";
	}
	tableEnd();

	if ($ti->status=="assigned")
	{
		tableStart("Neue Nachricht");
		echo '<tr><th>Absender:</th><td>';
		echo $cu->nick." (Admin)";
		echo '</td></tr>';
		echo '<tr><th>Nachricht:</th><td>';
		echo '<textarea name="message" rows="8" cols="60"></textarea>';
		echo '</td></tr>';
		tableEnd();
		echo '<input type="submit" name="submit_new_post" value="Senden" /> &nbsp; ';

		echo ' <input type="checkbox" name="checkclose" value="1" /> Ticket abschliessen als ';
		htmlSelect("solutionclose",Ticket::$solutionItems,"solved");

	}

	echo '<p>';
	echo button("Zur Übersicht","?page=$page").' &nbsp; ';
	if ($ti->status=="new")
		echo '<input type="submit" name="submit_assign" value="Ticket mir zuweisen" /> &nbsp; ';
	if ($ti->status=="closed")
		echo '<input type="submit" name="submit_reopen" value="Ticket wieder eröffnen" /> &nbsp; ';
	if ($ti->status=="assigned")
		echo '<input type="submit" name="submit_reopen" value="Zuweisung widerrufen" /> &nbsp; ';

	echo button("Ticketdetails bearbeiten","?page=$page&amp;edit=".$ti->id."").' &nbsp;
	</p>';
	echo "</form><br/>";



}

//
// Create new ticket
//
elseif (isset($_GET['action']) && $_GET['action']=="new")
{
	//
	// Create ticket form
	//
	echo "<h2>Ticket erstellen</h2>";
	echo '<form action="?page='.$page.'" method="post">';
	tableStart();
	echo '<tr><th>User:</th><td>';
	htmlSelect("user_id",Users::getArray());
	echo '</td></tr>';
	/*
	echo '<tr><th>Zugeteilter Admin:</th><td>';
	$sdata = AdminUser::getArray();
	$sdata[0] = "(Niemand)";
	htmlSelect("admin_id",$sdata);
	echo '</td></tr>'; */
	echo '<tr><th>Kategorie:</th><td>';
	htmlSelect("cat_id",Ticket::getCategories());
	echo '</td></tr>';
	echo '<tr><th>Problembeschreibung:</th><td>';
	echo '<textarea name="message" rows="8" cols="60"></textarea>';
	echo '</td></tr>';
	tableEnd();
	echo '<input type="submit" name="submit_new" value="Speichern" />
	</form>';
}

//
// Closed tickets
//
elseif (isset($_GET['action']) && $_GET['action']=="closed")
{

	echo '<h2>Bearbeitete Tickets</h2>';

	$cnt=0;
	$tlist = Ticket::find(array('status'=>'closed'));
	if (count($tlist)>0)
	{
		tableStart('Abgeschlossen','100%');
		echo "<tr><th>ID</th>
<th>Status</th>
<th>Kategorie</th>
<th>User</th>
<th>Admin</th>
<th>Nachrichten</th>
<th>Letzte Änderung</th></tr>";
		foreach ($tlist as $tid => &$ti)
		{
			echo "<tr>
			<td><a href=\"?page=$page&amp;id=".$tid."\">".$ti->idString."</a></td>
			<td>".$ti->statusName."</td>
			<td>".$ti->catName."</td>
			<td>".$ti->userNick."</td>
			<td>".$ti->adminNick."</td>
			<td>".$ti->countMessages()."</td>
			<td>".df($ti->time)."</td>
			</tr>";
			$cnt++;
		}
		tableEnd();
	}

	if ($cnt==0)
	{
		echo '<i>Keine aktiven Tickets vorhanden!</i>';
	}
}

//
// Active tickets
//
else
{
	echo '<h2>Aktive Tickets</h2>';

	if (isset($_POST['submit_new']))
	{
		if (Ticket::create($_POST))
		{
			success_msg("Das Ticket wurde erstellt!");
		}
	}


	$cnt=0;

	$tlist = Ticket::find(array('status'=>'new'));
	if (count($tlist)>0)
	{
		tableStart('Neu','100%');
		echo "<tr><th>ID</th><th>Status</th><th>Kategorie</th><th>User</th><th>Letzte Änderung</th></tr>";
		foreach ($tlist as $tid => &$ti)
		{
			echo "<div id=\"tt".$ti->id."\" style=\"display:none;\">
			".openerLink("page=user&sub=edit&id=".$ti->userId,"Daten anzeigen")."<br/>
			".popupLink("sendmessage","Nachricht senden","","id=".$ti->userId)."<br/>
			</div>";

			echo "<tr>
			<td><a href=\"?page=$page&amp;id=".$tid."\">".$ti->idString."</a></td>
			<td>".$ti->statusName."</td>
			<td>".$ti->catName."</td>
			<td><a href=\"javascript:;\" ".cTT($ti->userNick,"tt".$ti->id).">".$ti->userNick."</a></td>
			<td>".df($ti->time)."</td>
			</tr>";
			$cnt++;
		}
		tableEnd();
	}
	$tlist = Ticket::find(array('status'=>'assigned'));
	if (count($tlist)>0)
	{
		tableStart('Zugeteilt','100%');
		echo "<tr><th>ID</th>
<th>Status</th>
<th>Kategorie</th>
<th>User</th>
<th>Nachrichten</th>
<th>Letzte Änderung</th></tr>";
		foreach ($tlist as $tid => &$ti)
		{
			echo "<tr>
			<td><a href=\"?page=$page&amp;id=".$tid."\">".$ti->idString."</a></td>
			<td>".$ti->statusName.": <b>".$ti->adminNick."</b></td>
			<td>".$ti->catName."</td>
			<td>".$ti->userNick."</td>
			<td>".$ti->countMessages()."</td>
			<td>".df($ti->time)."</td>
			</tr>";
			$cnt++;
		}
		tableEnd();
	}

	if ($cnt==0)
	{
		echo '<i>Keine aktiven Tickets vorhanden!</i>';
	}
}
} else {
	$twig->addGlobal("errorMessage", "Nicht erlaubt!");
}


?>
