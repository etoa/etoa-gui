<?PHP

use EtoA\Admin\AdminRoleManager;
use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use EtoA\Log\LogFacility;
use EtoA\Log\LogSeverity;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

/** @var AdminUserRepository */
$adminUserRepo = $app[AdminUserRepository::class];

/** @var AdminRoleManager */
$roleManager = $app[AdminRoleManager::class];

/** @var Request */
$request = Request::createFromGlobals();

$twig->addGlobal("title", "Admin-Management");

if ($request->query->has('new')) {
    createUser($roleManager);
} elseif ($request->query->has('edit') && $request->query->getInt('edit') > 0) {
    editUser($request, $adminUserRepo, $roleManager);
} else {
    listUsers($request, $adminUserRepo, $roleManager, $cu, $twig);
}

function createUser(AdminRoleManager $roleManager)
{
    global $page;
    global $sub;

    echo "<h2>Neu</h2>";

    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
    echo "<table class=\"tb\" style=\"width:auto;\">";
    echo "<tr>
            <th>Realer Name:</th>
            <td><input type=\"text\" name=\"user_name\" value=\"\" /></td>
        </tr>";
    echo "<tr>
            <th>E-Mail:</th>
            <td><input type=\"text\" name=\"user_email\" value=\"\" /></td>
        </tr>";
    echo "<tr>
            <th>Nickname:</th>
            <td><input type=\"text\" name=\"user_nick\" value=\"\" autocomplete=\"off\" /></td>
        </tr>";
    echo "<tr>
            <th>Passwort (leerlassen generiert eins):</th>
            <td><input type=\"password\" name=\"user_password\" autocomplete=\"off\" /></td>
        </tr>";
    echo "<tr>
            <th>Rollen:</th>
            <td>";
    foreach ($roleManager->getRoles() as $k => $v) {
        echo '<input type="checkbox" name="roles[]" value="' . $k . '" id="role_' . $k . '"> <label for="role_' . $k . '">' . $v . '</label><br/>';
    }
    echo "</td>
        </tr>";
    echo "<tr>
            <th>Kontakt anzeigen:</th>
            <td>
                <input type=\"radio\" name=\"is_contact\" value=\"1\" ";
    echo " checked=\"checked\"";
    echo "/> Ja
                <input type=\"radio\" name=\"is_contact\" value=\"0\" ";
    echo "/> Nein
            </td>
        </tr>";
    echo "</table><br/>
        <input type=\"submit\" name=\"new_submit\" value=\"Speichern\" /> &nbsp;
        <input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Abbrechen\" />";
    echo "</form>";
}

function editUser(Request $request, AdminUserRepository $adminUserRepo, AdminRoleManager $roleManager)
{
    global $page;
    global $sub;

    echo "<h2>Bearbeiten</h2>";
    $adminUser = $adminUserRepo->find($request->query->getInt('edit'));
    if ($adminUser != null) {
        echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
            <input type=\"hidden\" name=\"user_id\" value=\"" . $adminUser->id . "\" />";
        echo "<table class=\"tb\" style=\"width:auto;\">";
        echo "<tr>
                <th>Realer Name:</th>
                <td><input type=\"text\" name=\"user_name\" value=\"" . $adminUser->name . "\" /></td>
            </tr>";
        echo "<tr>
                <th>E-Mail:</th>
                <td><input type=\"text\" name=\"user_email\" value=\"" . $adminUser->email . "\" /></td>
            </tr>";
        echo "<tr>
                <th>Nickname:</th>
                <td><input type=\"text\" name=\"user_nick\" value=\"" . $adminUser->nick . "\"  autocomplete=\"off\" /></td>
            </tr>";
        echo "<tr>
                <th>Neues Passwort:</th>
                <td><input type=\"password\" name=\"user_password\" autocomplete=\"off\" /></td>
            </tr>";
        if ($adminUser->tfaSecret !== '') {
            echo "<tr>
                    <th>Zwei-Faktor-Authentifizierung:</th>
                    <td><input type=\"checkbox\" name=\"tfa_remove\" id=\"tfa_remove\" value=\"1\" /> <label for=\"tfa_remove\">Zwei-Faktor-Authentifizierung deaktivieren</label></td>
                </tr>";
        }
        echo "<tr>
                <th>Rollen:</th>
                <td>";
        foreach ($roleManager->getRoles() as $k => $v) {
            echo '<input type="checkbox" name="roles[]" value="' . $k . '" id="role_' . $k . '"';
            if (in_array($k, $adminUser->roles, true)) {
                echo ' checked="checked"';
            }
            echo '> <label for="role_' . $k . '">' . $v . '</label><br/>';
        }
        echo "</td>
            </tr>";
        echo "<tr>
                <th>Gesperrt:</th>
                <td>
                    <input type=\"radio\" name=\"user_locked\" value=\"1\" ";
        if ($adminUser->locked) {
            echo " checked=\"checked\"";
        }
        echo "/> Ja
                    <input type=\"radio\" name=\"user_locked\" value=\"0\" ";
        if (!$adminUser->locked) {
            echo " checked=\"checked\"";
        }
        echo "/> Nein
                </td>
            </tr>";
        echo "<tr>
                <th>Kontakt anzeigen:</th>
                <td>
                    <input type=\"radio\" name=\"is_contact\" value=\"1\" ";
        if ($adminUser->isContact) {
            echo " checked=\"checked\"";
        }
        echo "/> Ja
                    <input type=\"radio\" name=\"is_contact\" value=\"0\" ";
        if (!$adminUser->isContact) {
            echo " checked=\"checked\"";
        }
        echo "/> Nein
                </td>
            </tr>";
        echo "</table><br/>
            <input type=\"submit\" name=\"edit_submit\" value=\"Speichern\" /> &nbsp;
            <input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Abbrechen\" />";
        echo "</form>";
    } else {
        echo "ID nicht vorhanden!";
    }
}
function listUsers(
    Request $request,
    AdminUserRepository $adminUserRepo,
    AdminRoleManager $roleManager,
    AdminUser $cu,
    Environment $twig
) {
    global $page;
    global $sub;

    echo "<h2>Übersicht</h2>";

    if ($request->request->has('new_submit')) {
        if ($request->request->get('user_nick') != "") {

            $admin = new AdminUser();
            $admin->nick = $request->request->get('user_nick');
            $admin->name = $request->request->get('user_name');
            $admin->email = $request->request->get('user_email');
            $admin->roles = $request->request->has('roles') ? $request->request->get('roles') : [];
            $admin->isContact = $request->request->getBoolean('is_contact');
            $adminUserRepo->save($admin);

            $twig->addGlobal('successMessage', "Gespeichert!");
            Log::add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $cu->nick . " erstellt einen neuen Administrator: " . $admin->nick . "(" . $admin->id . ").");

            if ($request->request->get('user_password') != "") {
                $password = $request->request->get('user_password');
            } else {
                $password = generatePasswort();
                echo "Das Passwort ist: $password<br/><br/>";
            }
            $adminUserRepo->setPassword($admin, $password);
        } else {
            echo "Nick nicht angegeben!<br/><br/>";
        }
    }

    if ($request->request->has('edit_submit')) {
        if ($request->request->get('user_nick') != "") {
            $adminUser = $adminUserRepo->find($request->request->getInt('user_id'));

            if ($request->request->get('user_password') != "") {
                $adminUserRepo->setPassword($adminUser, $request->request->get('user_password'));
                Log::add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $cu->nick . " ändert das Passwort des Administrators " . $adminUser->nick . "(" . $adminUser->id . ").");
            }

            $adminUser->nick = $request->request->get('user_nick');
            $adminUser->name = $request->request->get('user_name');
            $adminUser->email = $request->request->get('user_email');
            if ($request->request->has('tfa_remove')) {
                $adminUser->tfaSecret = "";
                Log::add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $cu->nick . " deaktiviert die Zwei-Faktor-Authentifizierung des Administrators " . $adminUser->nick . "(" . $adminUser->id . ").");
            }
            $adminUser->locked = $request->request->getBoolean('user_locked');
            $adminUser->isContact = $request->request->getBoolean('is_contact');
            $adminUser->roles = $request->request->has('roles') ? $request->request->get('roles') : [];

            $adminUserRepo->save($adminUser);

            $twig->addGlobal('successMessage', "Gespeichert!");
            Log::add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $cu->nick . " ändert die Daten des Administrators " . $adminUser->nick . " (ID: " . $adminUser->id . ").");
        } else {
            echo "Nick nicht angegeben!<br/><br/>";
        }
    }

    if ($request->query->has('del') && $request->query->getInt('del') > 0 && $request->query->getInt('del') != $cu->id) {
        $adminUser = $adminUserRepo->find($request->query->getInt('del'));
        if ($adminUser != null && $adminUserRepo->remove($adminUser)) {
            Log::add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $cu->nick . " löscht den Administrator " . $adminUser->nick . " (ID: " . $adminUser->id . ").");
            echo "Benutzer gelöscht!<br/><br/>";
        }
    }

    echo "<table class=\"tb\" style=\"width:auto;\">
        <tr>
            <th>Nick</th>
            <th>Name</th>
            <th>E-Mail</th>
            <th>Zwei-Faktor-Authentifizierung</th>
            <th>Rollen</th>
            <th>Gesperrt</th>
            <th></th>
        </tr>";
    foreach ($adminUserRepo->findAll() as $admin) {
        echo "<tr>
                <td>" . $admin->nick . "</td>
                <td>" . $admin->name . "</td>
                <td><a href=\"mailto:" . $admin->email . "\">" . $admin->email . "</a></td>
                <td>" . ($admin->tfaSecret !== '' ? "Aktiv" : "Nicht aktiviert") . "</td>
                <td>" . $roleManager->getRolesStr($admin) . "</td>
                <td>" . ($admin->locked == 1 ? "<span style=\"color:red\">Ja</span>" : "Nein") . "</td>
                <td style=\"width:40px;\">" . edit_button("?page=$page&amp;sub=$sub&amp;edit=" . $admin->id . "") . " ";
        if ($admin->id != $cu->id) {
            echo del_button("?page=$page&amp;sub=$sub&amp;del=" . $admin->id, "return confirm('Soll der Benutzer wirklich gelöscht werden?')");
        }
        echo "</td>
            </tr>";
    }
    echo "</table><br/> ";
    echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;new=1'\" value=\"Neuer Benutzer\" />";
}
