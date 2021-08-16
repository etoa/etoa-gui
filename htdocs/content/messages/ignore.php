<?php

use EtoA\Message\MessageIgnoreRepository;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var MessageIgnoreRepository $messageIgnoreRepository */
$messageIgnoreRepository = $app[MessageIgnoreRepository::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

// Ignorierung hinzufügen
if (($request->request->has('submit_ignore') && $request->request->getInt('target_id') > 0)
    || $request->query->getInt('add') > 0
) {
    if ($request->query->has('add')) {
        $request->request->set('target_id', $request->query->getInt('add'));
    }

    $messageIgnoreRepository->remove($cu->id, $request->request->getInt('target_id'));
    $messageIgnoreRepository->add($cu->id, $request->request->getInt('target_id'));

    success_msg("Spieler wurde ignoriert!");
}

// Ignorierung löschen
if ($request->query->getInt('remove') > 0) {
    $messageIgnoreRepository->remove($cu->id, $request->query->getInt('remove'));

    success_msg("Spieler wurde von der Liste entfernt!");
}

$users = $userRepository->searchUserNicknames();
unset($users[$cu->id]);

tableStart('Ignorierliste');
echo '<tr><th style="text-align:center;">Falls du von einem Benutzer belästigt wirst kannst du ihn hier ignorieren:</th></tr>';
if (count($users) > 0) {
    echo '<tr><td style="text-align:center;"><form action="?page=' . $page . '&amp;mode=' . $mode . '" method="post"><div>
    <select name="target_id"><option value="0">Spieler wählen...</option>';
    foreach ($users as $userId => $userNick) {
        echo '<option value="' . $userId . '">' . $userNick . '</option>';
    }
    echo '</select> <input type="submit" name="submit_ignore" value="Nachrichten dieses Spielers ignorieren" /></div></form></td>';
}
echo '</tr>';
tableEnd();

// Spieler die man ignoriert
$targets = $messageIgnoreRepository->findForOwner($cu->id);
if (count($targets) > 0) {
    tableStart();
    echo '<tr><th>Spieler</th><th>Aktionen</th></tr>';
    foreach ($targets as $target) {
        if (isset($users[$target])) {
            echo '<tr><td>' . $users[$target] . '</td>
            <td><a href="?page=' . $page . '&amp;mode=new&amp;message_user_to=' . $target . '">Nachricht</a>
            <a href="?page=userinfo&amp;id=' . $target . '">Profil</a>
            <a href="?page=' . $page . '&amp;mode=' . $mode . '&amp;remove=' . $target . '">Entfernen</a>
            </td></tr>';
        }
    }
    tableEnd();
} else {
    error_msg('Keine ignorierten Spieler vorhanden!', 1);
}

// Spieler bei denen man ignoriert ist
$owners = $messageIgnoreRepository->findForTarget($cu->id);
if (count($owners) > 0) {
    echo '<br/><br/>Du wirst von folgenden Spielern ignoriert:<br/><br/>';
    tableStart();
    echo '<tr><th>Spieler</th><th>Aktionen</th></tr>';
    foreach ($owners as $owner) {
        if (isset($users[$owner])) {
            echo '<tr><td>' . $users[$owner] . '</td>
            <td><a href="?page=userinfo&amp;id=' . $owner . '">Profil</a>
            <a href="?page=' . $page . '&amp;mode=' . $mode . '&amp;add=' . $owner . '">Ebenfalls ignorieren</a>
            </td></tr>';
        }
    }
    tableEnd();
}
