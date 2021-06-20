<?PHP

use EtoA\Admin\AdminRoleManager;
use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

/** @var AdminUserRepository */
$adminUserRepo = $app['etoa.admin.user.repository'];

/** @var AdminRoleManager */
$rm = $app['etoa.admin.role.manager'];

/** @var Request */
$request = Request::createFromGlobals();

$twig->addGlobal("title", "Admin-Management");

if ($request->query->has('new')) {
    createUser($rm);
} elseif ($request->query->has('edit') && $request->query->getInt('edit') > 0) {
    editUser($request, $adminUserRepo, $rm);
} else {
    listUsers($request, $adminUserRepo, $cu, $twig);
}

function createUser(AdminRoleManager $rm)
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
    foreach ($rm->getRoles() as $k => $v) {
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

function editUser(Request $request, AdminUserRepository $adminUserRepo, AdminRoleManager $rm)
{
    global $page;
    global $sub;

    echo "<h2>Bearbeiten</h2>";
    $au = $adminUserRepo->find($request->query->getInt('edit'));
    if ($au != null) {
        echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
            <input type=\"hidden\" name=\"user_id\" value=\"" . $au->id . "\" />";
        echo "<table class=\"tb\" style=\"width:auto;\">";
        echo "<tr>
                <th>Realer Name:</th>
                <td><input type=\"text\" name=\"user_name\" value=\"" . $au->name . "\" /></td>
            </tr>";
        echo "<tr>
                <th>E-Mail:</th>
                <td><input type=\"text\" name=\"user_email\" value=\"" . $au->email . "\" /></td>
            </tr>";
        echo "<tr>
                <th>Nickname:</th>
                <td><input type=\"text\" name=\"user_nick\" value=\"" . $au->nick . "\"  autocomplete=\"off\" /></td>
            </tr>";
        echo "<tr>
                <th>Neues Passwort:</th>
                <td><input type=\"password\" name=\"user_password\" autocomplete=\"off\" /></td>
            </tr>";
        if (!empty($au->tfaSecret)) {
            echo "<tr>
                    <th>Zwei-Faktor-Authentifizierung:</th>
                    <td><input type=\"checkbox\" name=\"tfa_remove\" id=\"tfa_remove\" value=\"1\" /> <label for=\"tfa_remove\">Zwei-Faktor-Authentifizierung deaktivieren</label></td>
                </tr>";
        }
        echo "<tr>
                <th>Rollen:</th>
                <td>";
        foreach ($rm->getRoles() as $k => $v) {
            echo '<input type="checkbox" name="roles[]" value="' . $k . '" id="role_' . $k . '"';
            if (in_array($k, $au->roles)) {
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
        if ($au->locked) {
            echo " checked=\"checked\"";
        }
        echo "/> Ja
                    <input type=\"radio\" name=\"user_locked\" value=\"0\" ";
        if (!$au->locked) {
            echo " checked=\"checked\"";
        }
        echo "/> Nein
                </td>
            </tr>";
        echo "<tr>
                <th>Kontakt anzeigen:</th>
                <td>
                    <input type=\"radio\" name=\"is_contact\" value=\"1\" ";
        if ($au->isContact) {
            echo " checked=\"checked\"";
        }
        echo "/> Ja
                    <input type=\"radio\" name=\"is_contact\" value=\"0\" ";
        if (!$au->isContact) {
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
            Log::add(8, Log::INFO, "Der Administrator " . $cu->nick . " erstellt einen neuen Administrator: " . $admin->nick . "(" . $admin->id . ").");

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
            $au = $adminUserRepo->find($request->request->getInt('user_id'));
            $pw = '';
            if ($request->request->get('user_password') != "") {
                $adminUserRepo->setPassword($au, $request->request->get('user_password'));
                Log::add(8, Log::INFO, "Der Administrator " . $cu->nick . " ändert das Passwort des Administrators " . $au->nick . "(" . $au->id . ").");
            }

            $au->nick = $request->request->get('user_nick');
            $au->name = $request->request->get('user_name');
            $au->email = $request->request->get('user_email');
            if ($request->request->has('tfa_remove')) {
                $au->tfaSecret = "";
                Log::add(8, Log::INFO, "Der Administrator " . $cu->nick . " deaktiviert die Zwei-Faktor-Authentifizierung des Administrators " . $au->nick . "(" . $au->id . ").");
            }
            $au->locked = $request->request->getBoolean('user_locked');
            $au->isContact = $request->request->getBoolean('is_contact');
            $au->roles = $request->request->has('roles') ? $request->request->get('roles') : [];

            $adminUserRepo->save($au);

            $twig->addGlobal('successMessage', "Gespeichert!");
            Log::add(8, Log::INFO, "Der Administrator " . $cu->nick . " ändert die Daten des Administrators " . $au->nick . " (ID: " . $au->id . ").");
        } else {
            echo "Nick nicht angegeben!<br/><br/>";
        }
    }

    if ($request->query->has('del') && $request->query->getInt('del') > 0 && $request->query->getInt('del') != $cu->id) {
        $au = $adminUserRepo->find($request->query->getInt('del'));
        if ($au != null && $adminUserRepo->remove($au)) {
            Log::add(8, Log::INFO, "Der Administrator " . $cu->nick . " löscht den Administrator " . $au->nick . " (ID: " . $au->id . ").");
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
    foreach ($adminUserRepo->findAll() as $arr) {
        echo "<tr>
                <td>" . $arr->nick . "</td>
                <td>" . $arr->name . "</td>
                <td><a href=\"mailto:" . $arr->email . "\">" . $arr->email . "</a></td>
                <td>" . ($arr->tfaSecret ? "Aktiv" : "Nicht aktiviert") . "</td>
                <td>" . $arr->getRolesStr() . "</td>
                <td>" . ($arr->locked == 1 ? "<span style=\"color:red\">Ja</span>" : "Nein") . "</td>
                <td style=\"width:40px;\">" . edit_button("?page=$page&amp;sub=$sub&amp;edit=" . $arr->id . "") . " ";
        if ($arr->id != $cu->id) {
            echo del_button("?page=$page&amp;sub=$sub&amp;del=" . $arr->id, "return confirm('Soll der Benutzer wirklich gelöscht werden?')");
        }
        echo "</td>
            </tr>";
    }
    echo "</table><br/> ";
    echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;new=1'\" value=\"Neuer Benutzer\" />";
}
