<?php

use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use EtoA\Help\TicketSystem\Ticket;
use EtoA\Help\TicketSystem\TicketMessageRepository;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\User\UserRepository;

/**
 * @var TicketRepository
 */
$ticketRepo = $app['etoa.help.ticket.repository'];

/**
 * @var TicketMessageRepository
 */
$ticketMessageRepo = $app['etoa.help.ticket.message.repository'];

/**
 * @var AdminUserRepository
 */
$adminUserRepo = $app['etoa.admin.user.repository'];

/**
 * @var UserRepository
 */
$userRepo = $app['etoa.user.repository'];

$twig->addGlobal("title", "Support-Tickets");

if ($cu->hasRole("master,super-admin,game-admin,trial-game-admin")) {
	ticketNavigation();

	if (isset($_GET['edit']) && $_GET['edit'] > 0) {
		editTicket($ticketRepo, $adminUserRepo, $userRepo);
	} elseif (isset($_GET['id']) && $_GET['id'] > 0) {
		ticketDetails($ticketRepo, $ticketMessageRepo, $adminUserRepo, $userRepo, $cu);
	} elseif (isset($_GET['action']) && $_GET['action'] == "new") {
		createNewTicketForm($ticketRepo);
	} elseif (isset($_GET['action']) && $_GET['action'] == "closed") {
		closedTickets($ticketRepo, $ticketMessageRepo, $adminUserRepo, $userRepo);
	} else {
		activeTickets($ticketRepo, $ticketMessageRepo, $adminUserRepo, $userRepo);
	}
} else {
	$twig->addGlobal("errorMessage", "Nicht erlaubt!");
}

function ticketNavigation()
{
	global $page;
	echo '<div>[ <a href="?page=' . $page . '">Aktive Tickets</a> |
	<a href="?page=' . $page . '&amp;action=new">Neues Ticket erstellen</a> |
	<a href="?page=' . $page . '&amp;action=closed">Bearbeitete Tickets</a> ]</div>';
}

function editTicket(
	TicketRepository $ticketRepo,
	AdminUserRepository $adminUserRepo,
	UserRepository $userRepo
) {
	global $page;

	echo "<h2>Ticket bearbeiten</h2>";

	$ticket = $ticketRepo->find($_GET['edit']);

	echo '<form action="?page=' . $page . '&amp;id=' . $ticket->id . '" method="post">';
	echo '<h3>Ticket ' . $ticket->getIdString() . '</h3>';
	tableStart();
	echo '<tr><th>Kategorie:</th><td colspan="3">';
	htmlSelect("cat_id", $ticketRepo->findAllCategoriesAsMap(), $ticket->catId);
	echo '</td></tr>';
	echo '<tr><th>User:</th><td>';
	$userNick = $userRepo->getNick($ticket->userId);
	echo '<a href=\"javascript:;\" ' . cTT($userNick, "ttuser") . '>' . $userNick . '</a>';
	echo '</td>';
	echo '<th>Zugeteilter Admin:</th><td>';
	$admins = $adminUserRepo->findAllAsList();
	$admins[0] = "(Niemand)";
	htmlSelect("admin_id", $admins, $ticket->adminId);
	echo '</td></tr>';
	echo '<tr><th>Status:</th><td>';
	htmlSelect("status", Ticket::STATUS_ITEMS, $ticket->status);
	echo '</td>';
	echo '<th>Lösung:</th><td>';
	htmlSelect("solution", Ticket::SOLUTION_ITEMS, $ticket->solution);
	echo '</td></tr>';
	echo '<tr><th>Admin-Kommentar:</th><td colspan="3">';
	echo '<textarea name="admin_comment" rows="5" cols="60">' . $ticket->adminComment . '</textarea>';
	echo '</td></tr>';
	tableEnd();
	echo '<p><input type="submit" name="submit" value="Änderungen übernehmen" /> &nbsp;
		' . button("Abbrechen", "?page=$page&amp;id=" . $ticket->id . "") . ' &nbsp;</p>';
	echo "</form>";
}

function ticketDetails(
	TicketRepository $ticketRepo,
	TicketMessageRepository $ticketMessageRepo,
	AdminUserRepository $adminUserRepo,
	UserRepository $userRepo,
	AdminUser $cu
) {
	global $page;

	echo "<h2>Ticket-Details</h2>";

	$ticket = $ticketRepo->find($_GET['id']);

	if (isset($_POST['submit'])) {
		$ticket->status = $_POST['status'];
		$ticket->solution = $_POST['solution'];
		$ticket->catId = $_POST['cat_id'];
		$ticket->adminId = $_POST['admin_id'];
		$ticket->adminComment = $_POST['admin_comment'];

		if ($ticketRepo->persist($ticket)) {
			success_msg("Ticket aktualisiert!");
		}
	}
	if (isset($_POST['submit_assign'])) {
		if ($ticketRepo->assign($ticket, $cu->id)) {
			success_msg("Ticket aktualisiert!");
		}
	}
	if (isset($_POST['submit_reopen'])) {
		if ($ticketRepo->reopen($ticket)) {
			success_msg("Ticket aktualisiert!");
		}
	}

	if (isset($_POST['submit_new_post'])) {
		$ticketRepo->addMessage($ticket, $_POST['message'], 0, $cu->id, !isset($_POST['should_close']));
		success_msg("Nachricht hinzugefügt!");

		if (isset($_POST['should_close'])) {
			$ticketRepo->close($ticket, $_POST['close_solution']);
		}

		if (isset($_POST['admin_comment'])) {
			$ticket->adminComment = $_POST['admin_comment'];
			$ticketRepo->persist($ticket);
		}
	}
	if (isset($_POST['submit_admin_comment'])) {
		$ticket->adminComment = $_POST['admin_comment'];
		$ticketRepo->persist($ticket);
	}

	echo "<div id=\"ttuser\" style=\"display:none;\">
	" . openerLink("page=user&sub=edit&id=" . $ticket->userId, "Daten anzeigen") . "<br/>
	" . popupLink("sendmessage", "Nachricht senden", "", "id=" . $ticket->userId) . "<br/>
	</div>";

	echo '<form action="?page=' . $page . '&amp;id=' . $_GET['id'] . '" method="post">';

	echo "<h3>Ticket " . $ticket->getIdString() . "</h3>";
	tableStart();
	echo '<tr><th style="width:150px">Kategorie:</th><td>';
	echo $ticketRepo->getCategoryName($ticket->catId);
	echo '</td></tr>';
	echo '<th>Status:</th><td>';
	echo $ticket->getStatusName();
	echo '</td></tr>';
	echo '<tr><th>User:</th><td>';
	$userNick = $userRepo->getNick($ticket->userId);
	echo '<a href="javascript:;" ' . cTT($userNick, "ttuser") . '>' . $userNick . '</a>';
	echo '</td></tr>';
	if ($ticket->adminId > 0) {
		echo '<th>Zugeteilter Admin:</th><td>';
		echo $adminUserRepo->getNick($ticket->adminId);
		echo '</td></tr>';
	}
	echo '<tr><th>Letzte Änderung:</th><td>';
	echo df($ticket->timestamp);
	echo '</td></tr>';
	echo '<tr><th>Admin-Kommentar:</th><td colspan="3">';
	echo '<textarea name="admin_comment" style="color:#00008B" rows="4" cols="60">' . $ticket->adminComment . '</textarea>
	<input type="submit" name="submit_admin_comment" value="Speichern" /> (wird auch beim Senden einer neuen Nachricht gespeichert)';
	echo '</td></tr>';
	tableEnd();

	echo "<h3>Nachrichten</h3>";
	tableStart("");
	echo "<tr>
	<th style=\"width:120px\">Datum</th>
	<th style=\"width:130px\">Autor</th>
	<th>Nachricht</th></tr>";
	foreach ($ticketRepo->getMessages($ticket) as $message) {
		echo "<tr>
		<td>" . df($message->timestamp) . "</td>
		<td>" . $ticketMessageRepo->getAuthorNick($message) . "</td>
		<td>" . text2html($message->message) . "</td>
		</tr>";
	}
	tableEnd();

	if ($ticket->status == "assigned") {
		echo "<h3>Neue Nachricht</h3>";
		tableStart("");
		echo '<tr><th>Absender:</th><td>';
		echo $cu->nick . " (Admin)";
		echo '</td></tr>';
		echo '<tr><th>Nachricht:</th><td>';
		echo '<textarea name="message" rows="8" cols="60"></textarea>';
		echo '</td></tr>';
		tableEnd();
		echo '<p><input type="submit" name="submit_new_post" value="Senden" /> &nbsp; ';
		echo ' <input type="checkbox" name="should_close" id="should_close" value="1" />
			<label for="should_close">Ticket abschliessen als</label> ';
		htmlSelect("close_solution", Ticket::SOLUTION_ITEMS, "solved");
		echo '</p>';
	}

	echo '<p>';
	echo button("Zur Übersicht", "?page=$page") . ' &nbsp; ';
	if ($ticket->status == "new") {
		echo '<input type="submit" name="submit_assign" value="Ticket mir zuweisen" /> &nbsp; ';
	}
	if ($ticket->status == "closed") {
		echo '<input type="submit" name="submit_reopen" value="Ticket wieder eröffnen" /> &nbsp; ';
	}
	if ($ticket->status == "assigned") {
		echo '<input type="submit" name="submit_reopen" value="Zuweisung widerrufen" /> &nbsp; ';
	}

	echo button("Ticketdetails bearbeiten", "?page=$page&amp;edit=" . $ticket->id . "") . ' &nbsp;
	</p>';
	echo "</form><br/>";
}

function createNewTicketForm(TicketRepository $ticketRepo)
{
	global $page;

	echo "<h2>Ticket erstellen</h2>";
	echo '<form action="?page=' . $page . '" method="post">';
	tableStart();
	echo '<tr><th>User:</th><td>';
	htmlSelect("user_id", Users::getArray());
	echo '</td></tr>';
	echo '<tr><th>Kategorie:</th><td>';
	htmlSelect("cat_id", $ticketRepo->findAllCategoriesAsMap());
	echo '</td></tr>';
	echo '<tr><th>Problembeschreibung:</th><td>';
	echo '<textarea name="message" rows="8" cols="60"></textarea>';
	echo '</td></tr>';
	tableEnd();
	echo '<p><input type="submit" name="submit_new" value="Speichern" /></p></form>';
}

function closedTickets(
	TicketRepository $ticketRepo,
	TicketMessageRepository $ticketMessageRepo,
	AdminUserRepository $adminUserRepo,
	UserRepository $userRepo
) {
	global $page;

	echo '<h2>Bearbeitete Tickets</h2>';

	$tickets = $ticketRepo->findBy(['status' => 'closed']);
	if (count($tickets) > 0) {
		tableStart('Abgeschlossen', '100%');
		echo "<tr><th>ID</th>
			<th>Status</th>
			<th>Kategorie</th>
			<th>User</th>
			<th>Admin</th>
			<th>Nachrichten</th>
			<th>Letzte Änderung</th></tr>";
		foreach ($tickets as $ticket) {
			echo "<tr>
			<td><a href=\"?page=$page&amp;id=" . $ticket->id . "\">" . $ticket->getIdString() . "</a></td>
			<td>" . $ticket->getStatusName() . "</td>
			<td>" . $ticketRepo->getCategoryName($ticket->catId) . "</td>
			<td>" . $userRepo->getNick($ticket->userId) . "</td>
			<td>" . ($adminUserRepo->getNick($ticket->adminId) ?? '?') . "</td>
			<td>" . $ticketMessageRepo->count($ticket->id) . "</td>
			<td>" . df($ticket->time) . "</td>
			</tr>";
		}
		tableEnd();
	} else {
		echo '<i>Keine aktiven Tickets vorhanden!</i>';
	}
}

function activeTickets(
	TicketRepository $ticketRepo,
	TicketMessageRepository $ticketMessageRepo,
	AdminUserRepository $adminUserRepo,
	UserRepository $userRepo
) {
	global $page;

	echo '<h2>Aktive Tickets</h2>';

	if (isset($_POST['submit_new'])) {
		$ticketRepo->create((int) $_POST['user_id'], (int) $_POST['cat_id'], $_POST['message']);
		success_msg("Das Ticket wurde erstellt!");
	}

	$cnt = 0;

	$newTickets = $ticketRepo->findBy(['status' => 'new']);
	if (count($newTickets) > 0) {
		echo "<h3>Neu</h3>";
		tableStart('', '100%');
		echo "<tr><th>ID</th><th>Status</th><th>Kategorie</th><th>User</th><th>Letzte Änderung</th></tr>";
		foreach ($newTickets as $ticket) {
			echo "<div id=\"tt" . $ticket->id . "\" style=\"display:none;\">
			" . openerLink("page=user&sub=edit&id=" . $ticket->userId, "Daten anzeigen") . "<br/>
			" . popupLink("sendmessage", "Nachricht senden", "", "id=" . $ticket->userId) . "<br/>
			</div>";

			$userNick = $userRepo->getNick($ticket->userId);
			echo "<tr>
			<td><a href=\"?page=$page&amp;id=" . $ticket->id . "\">" . $ticket->getIdString() . "</a></td>
			<td>" . $ticket->getStatusName() . "</td>
			<td>" . $ticketRepo->getCategoryName($ticket->catId) . "</td>
			<td><a href=\"javascript:;\" " . cTT($userNick, "tt" . $ticket->id) . ">" . $userNick . "</a></td>
			<td>" . df($ticket->timestamp) . "</td>
			</tr>";
			$cnt++;
		}
		tableEnd();
	}

	$assignedTickets = $ticketRepo->findBy(['status' => 'assigned']);
	if (count($assignedTickets) > 0) {
		echo "<h3>Zugeteilt</h3>";
		tableStart('', '100%');
		echo "<tr><th>ID</th>
			<th>Status</th>
			<th>Kategorie</th>
			<th>User</th>
			<th>Nachrichten</th>
			<th>Letzte Änderung</th></tr>";
		foreach ($assignedTickets as $ticket) {
			$userNick = $userRepo->getNick($ticket->userId);
			$adminNick = $adminUserRepo->getNick($ticket->adminId);
			echo "<tr>
				<td><a href=\"?page=$page&amp;id=" . $ticket->id . "\">" . $ticket->getIdString() . "</a></td>
				<td>" . $ticket->getStatusName() . ": <b>" . $adminNick . "</b></td>
				<td>" . $ticketRepo->getCategoryName($ticket->catId) . "</td>
				<td>" . $userNick . "</td>
				<td>" . $ticketMessageRepo->count($ticket->id) . "</td>
				<td>" . df($ticket->timestamp) . "</td>
				</tr>";
			$cnt++;
		}
		tableEnd();
	}

	if ($cnt == 0) {
		echo '<i>Keine aktiven Tickets vorhanden!</i>';
	}
}
