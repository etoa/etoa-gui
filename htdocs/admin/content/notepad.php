<?PHP

use EtoA\Admin\AdminNotesRepository;

$adminUserId = $cu->id;
if ($adminUserId > 0) {

	/** @var AdminNotesRepository */
	$notesRepo = $app['etoa.admin.notes.repository'];

	if (isset($_GET['chk']) && $_GET['chk'] == 'new') {
		createNote();
	} elseif (isset($_GET['chk']) && $_GET['chk'] == 'edit') {
		editNote($notesRepo, $adminUserId);
	} else {
		echo "<h1>Notizen</h1>";

		if (isset($_GET['func']) && $_GET['func'] == 'new') {
			storeNote($notesRepo, $adminUserId);
		}

		if (isset($_GET['func']) && $_GET['func'] == 'editieren') {
			updateNote($notesRepo);
		}

		if (isset($_GET['chk']) && $_GET['chk'] == 'del') {
			destroyNote($notesRepo);
		}

		noteIndex($notesRepo, $adminUserId);
	}
} else {
	echo "Ungültige ID";
}

function createNote()
{
	echo "<h1>Notizen</h1>";
	echo "<form action=\"?page=notepad&amp;func=new\" method=\"post\">";
	echo "<input type=\"Text\" name=\"Titel\" size=\"50\" /><br><br>";
	echo "<textarea name=\"Text\" cols=\"80\" rows=\"20\"></textarea><br><br>";
	echo "<input type=\"Submit\" name=\"Einfügen\" value=\"Einfügen\"></input>";
	echo "</form>";
}

function storeNote(AdminNotesRepository $notesRepo, int $adminUserId)
{
	$notesRepo->create($_POST['Titel'], $_POST['Text'], $adminUserId);
}

function editNote(AdminNotesRepository $notesRepo, int $adminUserId)
{
	$note = $notesRepo->findForAdmin($_GET['pid'], $adminUserId);

	echo "<h1>Notizen</h1>";
	echo "<form action=\"?page=notepad&amp;func=editieren\" method=\"post\">";
	echo "<input type=\"hidden\" name=\"pid\" value=\"" . $note['notes_id'] . "\">
	<input type=\"Text\" name=\"Titel\" value=\"" . $note['titel'] . "\" size=\"50\"/><br><br>";
	echo "<textarea name=\"Text\" cols=\"80\" rows=\"20\">" . $note['text'] . "</textarea><br><br>";
	echo "<input type=\"Submit\" name=\"Ändern\" value=\"Ändern\"></input>";
	echo "</form>";
}

function updateNote(AdminNotesRepository $notesRepo)
{
	$notesRepo->update($_POST['pid'], $_POST['Titel'], $_POST['Text']);
}

function destroyNote(AdminNotesRepository $notesRepo)
{
	$notesRepo->remove($_GET['pid']);
}

function noteIndex(AdminNotesRepository $notesRepo, int $adminUserId)
{
	global $page;

	$notes = $notesRepo->findAllForAdmin($adminUserId);
	if (count($notes) == 0) {
		echo "Keine Notiz vorhanden";

		echo "<form action=\"?page=notepad&amp;chk=new\" method=\"post\">";
		echo "<br><input type=\"Submit\" name=\"Neue Notiz\" value=\"Neue Notiz\"></input>";
		echo "</form>";
	} else {
		echo "<br>";
		tableStart("Notizübersicht", "95%");
		foreach ($notes as $note) {
			$datum = date("d.m.Y", $note['date']);
			$uhrzeit = date("H:i", $note['date']);

			echo "<tr>
			<td width=\"120\"><b>" . text2html($note['titel']) . "</b><br/>" . $datum . " " . $uhrzeit . "</td>";
			echo "<td width=\"350\">" . text2html($note['text']) . "</td>";
			echo "<td>
			<a href=\"?page=$page&amp;chk=edit&pid=" . $note['notes_id'] . "\">Bearbeiten</a><br>
			<a href=\"?page=$page&amp;chk=del&pid=" . $note['notes_id'] . "\" onclick=\"return confirm('Eintrag löschen?')\">Löschen</a>
			</td></tr>";
		}
		echo "</table>";
		echo "<form action=\"?page=notepad&amp;chk=new\" method=\"post\">";
		echo "<br><input type=\"Submit\" name=\"Neue Notiz\" value=\"Neue Notiz\"></input>";
		echo "</form>";
	}
}
