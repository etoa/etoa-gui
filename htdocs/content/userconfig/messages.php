<?PHP

use EtoA\User\UserPropertiesRepository;

/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];

$properties = $userPropertiesRepository->getOrCreateProperties($cu->id);

// Datenänderung übernehmen
if (isset($_POST['data_submit']) && checker_verify()) {
    $properties->msgSignature = filled($_POST['msgsignature']) ? $_POST['msgsignature'] : null;
    $properties->msgPreview = $_POST['msg_preview'] == 1;
    $properties->msgCreationPreview = $_POST['msgcreation_preview'] == 1;
    $properties->msgBlink = $_POST['msg_blink'] == 1;
    $properties->msgCopy = $_POST['msg_copy'] == 1;
    $properties->fleetRtnMsg = $_POST['fleet_rtn_msg'] == 1;
    $userPropertiesRepository->storeProperties($cu->id, $properties);

    success_msg("Nachrichten-Einstellungen wurden ge&auml;ndert!");
}

echo "<form action=\"?page=$page&mode=messages\" method=\"post\" enctype=\"multipart/form-data\">";
$cstr = checker_init();
tableStart("Nachrichtenoptionen");

echo "<tr>
        <th width=\"35%\">Nachrichten-Signatur:</th>
        <td>
            <textarea name=\"msgsignature\" cols=\"50\" rows=\"4\" width=\"65%\">" . $properties->msgSignature . "</textarea></td>
    </tr>";
//Nachrichtenvorschau (Neue/Archiv) (An/Aus)
echo "<tr>
                     <th width=\"36%\">Nachrichtenvorschau (Neue/Archiv):</th>
                <td width=\"16%\">
            <input type=\"radio\" name=\"msg_preview\" value=\"1\" ";
if ($properties->msgPreview) echo " checked=\"checked\"";
echo "/> Aktiviert
            <input type=\"radio\" name=\"msg_preview\" value=\"0\" ";
if (!$properties->msgPreview) echo " checked=\"checked\"";
echo "/> Deaktiviert
               </td>
        </tr>";

//Nachrichtenvorschau (Erstellen) (An/Aus)
echo "<tr>
                  <th width=\"36%\">Nachrichtenvorschau (Erstellen):</th>
              <td width=\"16%\">
              <input type=\"radio\" name=\"msgcreation_preview\" value=\"1\" ";
if ($properties->msgCreationPreview)
    echo " checked=\"checked\"";
echo "/> Aktiviert
              <input type=\"radio\" name=\"msgcreation_preview\" value=\"0\" ";
if (!$properties->msgCreationPreview)
    echo " checked=\"checked\"";
echo "/> Deaktiviert
          </td>
       </tr>";

// Blinkendes Nachrichtensymbol (An/Aus)
echo "<tr>
                  <th width=\"36%\">Blinkendes Nachrichtensymbol:</th>
              <td width=\"16%\">
              <input type=\"radio\" name=\"msg_blink\" value=\"1\" ";
if ($properties->msgBlink)
    echo " checked=\"checked\"";
echo "/> Aktiviert
              <input type=\"radio\" name=\"msg_blink\" value=\"0\" ";
if (!$properties->msgBlink)
    echo " checked=\"checked\"";
echo "/> Deaktiviert
          </td>
       </tr>";

// Text kopieren (An/Aus)
echo "<tr>
                  <th width=\"36%\">Text bei Antwort/Weiterleiten kopieren:</th>
              <td width=\"16%\">
              <input type=\"radio\" name=\"msg_copy\" value=\"1\" ";
if ($properties->msgCopy)
    echo " checked=\"checked\"";
echo "/> Aktiviert
              <input type=\"radio\" name=\"msg_copy\" value=\"0\" ";
if (!$properties->msgCopy)
    echo " checked=\"checked\"";
echo "/> Deaktiviert
          </td>
       </tr>";

// Rückflug-Benachrichtingung für Flotten
echo "<tr>
                <th width=\"36%\">Nachricht bei Transport-/Spionagerückkehr:</th>
                <td width=\"16%\">
              <input type=\"radio\" name=\"fleet_rtn_msg\" value=\"1\" ";
if ($properties->fleetRtnMsg) echo " checked=\"checked\"";
echo "/> Aktiviert &nbsp;

              <input type=\"radio\" name=\"fleet_rtn_msg\" value=\"0\" ";
if (!$properties->fleetRtnMsg) echo " checked=\"checked\"";
echo "/> Deaktiviert
            </td>
          </tr>";

tableEnd();
echo "<input type=\"submit\" name=\"data_submit\" value=\"&Uuml;bernehmen\"/>";
echo "</form><br/><br/>";
