<?PHP

use EtoA\Admin\AdminRoleManager;
use EtoA\Admin\AdminUserRepository;

/** @var AdminUserRepository $adminUserRepo */
$adminUserRepo = $app[AdminUserRepository::class];

/** @var AdminRoleManager $roleManager */
$roleManager = $app[AdminRoleManager::class];

adminListIndex($adminUserRepo, $roleManager);

function adminListIndex(AdminUserRepository $adminUserRepo, AdminRoleManager $roleManager): void
{
    echo "<h1>Admin-Liste</h1>";

    echo "<table class=\"tb\">
    <tr>
        <th>Nick</th>
        <th>Name</th>
        <th>E-Mail</th>
        <th>Zwei-Faktor-Authentifizierung</th>
        <th>Gruppe</th>
        <th>Foren-Profil</th>
    </tr>";
    foreach ($adminUserRepo->findAll() as $admin) {
        echo "<tr>
            <td>" . $admin->nick . "</td>
            <td>" . $admin->name . "</td>
            <td><a href=\"mailto:" . $admin->email . "\">" . $admin->email . "</a></td>
            <td>" . ($admin->tfaSecret !== '' ? "Aktiv" : "Nicht aktiviert") . "</td>
            <td>" . $roleManager->getRolesStr($admin) . "</td>
            <td>" . ($admin->boardUrl !== '' ? "<a href=\"" . $admin->boardUrl . "\" target=\"_blank\">Profil</a>" : "") . "</td>
        </tr>";
    }
    echo "</table><br/> ";
}
