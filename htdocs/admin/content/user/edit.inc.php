<?php

if (isset($_GET['id']))
    $id = $_GET['id'];
elseif (isset($_GET['user_id']))
    $id = $_GET['user_id'];
else
    $id = 0;

// Geänderte Daten speichern
if (isset($_POST['save'])) {
    // TODO
}

// User löschen
if (isset($_POST['delete_user'])) {
// TODO
}

// Löschantrag speichern
if (isset($_POST['requestdelete'])) {
// TODO
}

// Löschantrag aufheben
if (isset($_POST['canceldelete'])) {
// TODO
}

// Fetch all data
$user = $userRepository->getUserAdminView($id);
if ($user !== null) {


}