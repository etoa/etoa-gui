<?PHP

use EtoA\Notepad\NotepadRepository;
use EtoA\Support\StringUtils;
use Symfony\Component\HttpFoundation\Request;

/** @var NotepadRepository $notepadRepository */
$notepadRepository = $app[NotepadRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

echo "<h1>Notizen</h1>";

//
// Neue Notiz
//
if ($request->query->has('action') && $request->query->get('action') == "new") {
    echo "<form action=\"?page=$page\" method=\"post\">";
    tableStart("Neue Notiz");
    echo "<tr><th>Titel:</th>
        <td><input type=\"text\" name=\"note_subject\" value=\"\" size=\"40\" /></td></tr>";
    echo '<tr><th>Text:</th><td><textarea name="note_text" cols="50" rows="10"></textarea><br/>' . helpLink('textformat', 'Hilfe zur Formatierung') . '</td></tr>';
    tableEnd();
    echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_new\" > &nbsp; ";
    echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /> &nbsp; ";
    echo "</form><br/>";
}

//
// Notiz bearbeiten
//
elseif ($request->query->has('action') && $request->query->get('action') == "edit" && $request->query->getInt('id') > 0) {
    $note = $notepadRepository->find($request->query->getInt('id'), $cu->id);
    if ($note !== null) {
        echo "<form action=\"?page=$page\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"note_id\" value=\"" . $note->id . "\" />";
        tableStart("Notiz bearbeiten");
        echo "<tr><th>Titel:</th><td><input type=\"text\" name=\"note_subject\" value=\"" . $note->subject . "\" size=\"40\" /></td></tr>";
        echo '<tr><th>Text:</th><td><textarea name="note_text" cols="50" rows="10">' . $note->text . '</textarea><br/>' . helpLink('textformat', 'Hilfe zur Formatierung') . '</td></tr>';
        tableEnd();
        echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_edit\" > &nbsp; ";
        echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /> &nbsp; ";
        echo "</form><br/>";
    } else {
        error_msg("Notiz nicht vorhanden!");
    }
}

//
// Übersicht
//
else {

    // Änderungen speichern
    if ($request->request->has('submit_new') && $request->request->get('note_subject') != "") {
        $notepadRepository->add($request->request->get('note_subject'), $request->request->get('note_text'), $cu->id);
    }
    // Änderungen speichern
    if ($request->request->has('submit_edit') && $request->request->getInt('note_id') > 0 && $request->request->get('note_subject') != "") {
        $notepadRepository->update($request->request->getInt('note_id'), $cu->id, $request->request->get('note_subject'), $request->request->get('note_text'));
    }
    // Notiz löschen
    elseif ($request->query->has('action') && $request->query->get('action') == "delete" && $request->query->getInt('id') > 0) {
        $notepadRepository->delete($request->query->getInt('id'), $cu->id);
    }

    if ($notepadRepository->count($cu->id) > 0) {
        tableStart("Meine Notizen");
        foreach ($notepadRepository->findAll($cu->id) as $note) {
            echo "<tr><td width=\"120px\"><b>" . $note->subject . "</b>
                <br/>" . StringUtils::formatDate($note->timestamp) . "</td>";
            echo "<td>" . text2html($note->text) . "</td>";
            echo "<td style=\"width:130px;\"><a href=\"?page=$page&amp;action=edit&amp;id=" . $note->id . "\">Bearbeiten</a> &nbsp; ";
            echo "<a href=\"?page=$page&amp;action=delete&amp;id=" . $note->id . "\" onclick=\"return confirm('Soll die Notiz " . $note->subject . " wirklich gel&ouml;scht werden?');\">L&ouml;schen</a></td></tr>";
        }
        tableEnd();
    } else {
        info_msg("Keine Notizen vorhanden!");
    }

    echo "<input type=\"button\" value=\"Neue Notiz\" onclick=\"document.location='?page=$page&amp;action=new'\" /> &nbsp; ";
}
