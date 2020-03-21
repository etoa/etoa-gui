<?PHP

$successMessage = null;
$errorMessage = null;
if (isset($_POST['submitpw'])) {
    if ($cu->checkEqualPassword($_POST['user_password_old'])) {
        if ($_POST['user_password'] == $_POST['user_password2'] && $_POST['user_password_old'] != $_POST['user_password']) {
            if (strlen($_POST['user_password']) >= PASSWORD_MINLENGHT) {
                $cu->setPassword($_POST['user_password']);
                $successMessage = 'Das Passwort wurde geändert!';
                add_log(8, $cu->id . " ändert sein Passwort", time());
            } else {
                $errorMessage = 'Das Passwort ist zu kurz! Es muss mindestens ' . PASSWORD_MINLENGHT . ' Zeichen lang sein!';
            }
        } else {
            $errorMessage = 'Die Kennwortwiederholung stimmt nicht oder das alte und das neue Passwort sind gleich!';
        }
    } else {
        $errorMessage = 'Das alte Passwort stimmt nicht mit dem gespeicherten Wert überein!';
    }
}

if (isset($_POST['submitdata'])) {
    $cu->name = $_POST['user_name'];
    $cu->email = $_POST['user_email'];
    $cu->boardUrl = $_POST['user_board_url'];
    $cu->userTheme = $_POST['user_theme'] ?? '';
    $cu->ticketEmail = $_POST['ticketmail'];
    $cu->playerId = $_POST['player_id'];
    $cu->save();

    if ($cu->playerId != $_POST['player_id']) {
        dbquery("UPDATE
                users
            SET
                user_ghost='0'
            WHERE
                user_id='".$arr['player_id']."';");

        dbquery("UPDATE
                users
            SET
                user_ghost='1'
            WHERE
                user_id='".$_POST['player_id']."';");
    }

    $successMessage = 'Die Daten wurden geändert!';
    add_log(8,$cu->nick." ändert seine Daten");
}

echo $twig->render('admin/profile/profile.html.twig', [
    'user' => $cu,
    'users' => Users::getArray(),
    'errMsg' => $errorMessage,
    'successMessage' => $successMessage,
]);
exit();
