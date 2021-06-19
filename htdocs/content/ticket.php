<?php

use EtoA\Admin\AdminUserRepository;
use EtoA\Help\TicketSystem\TicketMessageRepository;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\User\UserRepository;

/** @var TicketRepository */
$ticketRepo = $app['etoa.help.ticket.repository'];

/** @var TicketMessageRepository */
$ticketMessageRepo = $app['etoa.help.ticket.message.repository'];

/** @var AdminUserRepository */
$adminUserRepo = $app['etoa.admin.user.repository'];

/** @var UserRepository */
$userRepo = $app['etoa.user.repository'];

echo "<h1>Ticketsystem</h1>";

if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    viewTicket($ticketRepo, $ticketMessageRepo, $adminUserRepo, $userRepo);
} elseif (isset($_POST['ticket_submit']) && checker_verify()) {
    storeTicket($ticketRepo);
} else {
    listTickets($ticketRepo, $adminUserRepo);
}

function viewTicket(
    TicketRepository $ticketRepo,
    TicketMessageRepository $ticketMessageRepo,
    AdminUserRepository $adminUserRepo,
    UserRepository $userRepo
): void {
    global $cu;
    global $page;

    echo "<h2>Ticket-Details</h2>";

    $tickets = $ticketRepo->findBy([
        "user_id" => $cu->id,
        "id" => intval($_GET['id']),
    ]);
    if (count($tickets) == 0) {
        error_msg("Ticket nicht vorhanden!");
        return;
    }

    $ticket = array_shift($tickets);

    if (isset($_POST['submit_new_post'])) {
        $ticketRepo->addMessage($ticket, $_POST['message'], $cu->id);
        success_msg("Nachricht hinzugefügt!");
    }

    if (isset($_GET['reopen'])) {
        $ticketRepo->reopen($ticket);
    }

    tableStart("Ticket " . $ticket->getIdString());
    echo '<tr><th>Kategorie:</th><td colspan="3">';
    echo $ticketRepo->getCategoryName($ticket->catId);
    echo '</td></tr>';
    echo '<tr><th>User:</th><td>';
    echo '' . $userRepo->getNick($ticket->userId) . '';
    echo '</td></tr>';
    if ($ticket->adminId > 0) {
        echo '<tr><th>Zugeteilter Admin:</th><td>';
        echo $adminUserRepo->getNick($ticket->adminId);
        echo '</td></tr>';
    }
    echo '<tr><th>Status:</th><td colspan="3">';
    echo $ticket->getStatusName();
    echo '</td></tr>';
    tableEnd();

    tableStart("Nachrichten");
    echo "<tr><th style=\"width:120px;\">Datum</th><th style=\"width:150px;\">Autor</th><th>Nachricht</th></tr>";
    foreach ($ticketRepo->getMessages($ticket) as $message) {
        echo "<tr>
        <td>" . df($message->timestamp) . "</td>
        <td>" . $ticketMessageRepo->getAuthorNick($message) . "</td>
        <td>" . text2html($message->message) . "</td>
        </tr>";
    }
    tableEnd();

    if ($ticket->status == "closed") {
        echo '<p>' . button("Zur Übersicht", "?page=$page") . ' &nbsp;
            ' . button("Ticket wiedereröffnen", "?page=$page&amp;id=" . $ticket->id . "&amp;reopen=1") . '
            </p>';
        return;
    }

    echo '<form action="?page=' . $page . '&amp;id=' . $ticket->id . '" method="post">';
    tableStart("Neue Nachricht");
    echo '<tr><th>Absender:</th><td>';
    echo $cu->nick . "";
    echo '</td></tr>';
    echo '<tr><th>Nachricht:</th><td>';
    echo '<textarea name="message" rows="8" cols="60"></textarea>';
    echo '</td></tr>';
    tableEnd();
    echo '<input type="submit" name="submit_new_post" value="Senden" /> &nbsp;
        ' . button("Zur Übersicht", "?page=$page") . ' &nbsp;';
    echo "</form><br/>";
}

function storeTicket(TicketRepository $ticketRepo): void
{
    global $cu;

    $ticketRepo->create($cu->id, $_POST['cat_id'], $_POST['ticket_text']);
    echo "<br/>Vielen Dank, dein Text wurde gespeichert.<br/>Ein Game-Administrator wird sich dem Problem annehmen.<br/><br/>";
    echo "<input type=\"button\" onclick=\"document.location='?page=ticket'\" value=\"Weiter\" />";
}

function listTickets(
    TicketRepository $ticketRepo,
    AdminUserRepository $adminUserRepo
): void {
    global $page;
    global $cu;

    echo "Über unser Benachrichtigungssystem kannst du einen Game-Administrator informieren, falls
    du ein Problem mit dem Spiel hast oder einen Missbrauch der Spielregeln festgestellt hast. Bitte fülle folgendes Formular aus
    um dein Anliegen zu beschrieben; je mehr Infos du uns gibst, desto besser können wir dir helfen:<br/><br/>";
    echo "<form action=\"?page=$page\" method=\"post\">";
    checker_init();
    tableStart("Neues Ticket");
    echo "<tr>
        <th>Kategorie:</th>
        <td><select name=\"cat_id\">";
    foreach ($ticketRepo->findAllCategoriesAsMap() as $key => $value) {
        echo "<option value=\"" . $key . "\"";
        if (isset($_GET['cat']) && $_GET['cat'] == $key) {
            echo " selected=\"selected\"";
        }
        echo ">" . $value . "</option>";
    }
    echo "</select></td>
        </tr>
        <tr>
            <th>Beschreibung:</th>
            <td><textarea name=\"ticket_text\" id=\"ticket_text\" rows=\"10\" cols=\"60\"></textarea></td>
        </tr>";
    tableEnd();

    echo "<input type=\"submit\" name=\"ticket_submit\" value=\"Einsenden\" /><br/><br/>";
    echo "</form>";
    echo "<script type=\"text/javascript\">document.getElementById('ticket_text').focus()</script>";

    $tickets = $ticketRepo->findBy(['user_id' => $cu->id]);

    if (count($tickets) > 0) {
        tableStart("Vorhandene Tickets");
        echo "<tr>
            <th>ID</th>
            <th>Kategorie</th>
            <th>Status</th>
            <th>Admin</th>
            <th>Aktualisiert</th>
            <th>Optionen</th>
        </tr>";
        foreach ($tickets as $ticket) {
            echo "<tr>
                <td>" . $ticket->getIdString() . "</td>
                <td>" . $ticketRepo->getCategoryName($ticket->catId) . "</td>
                <td>" . $ticket->getStatusName() . "</td>
                <td><a href=\"?page=contact&rcpt=" . $ticket->adminId . "\">" . $adminUserRepo->getNick($ticket->adminId) . "</a></td>
                <td>" . df($ticket->timestamp) . "</td>
                <td>
                    <a href=\"?page=$page&amp;id=" . $ticket->id . "\">Anzeigen</a>
                </td>
            </tr>";
        }
        tableEnd();
    }
}
