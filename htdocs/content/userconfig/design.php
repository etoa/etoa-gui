<?PHP

$imagepacks = get_imagepacks();
$designs = get_designs();

//
// Daten werden gespeichert
//

if (isset($_POST['data_submit_design']) && $_POST['data_submit_design'] != "") {
    // Design
    $designChange = ($cu->properties->cssStyle != $_POST['css_style']);
    if (isset($_POST['css_style']) && $_POST['css_style']) {
        $cu->properties->cssStyle = $_POST['css_style'];
    } else {
        $cu->properties->cssStyle = null;
    }

    if ($cu->properties->smallResBox != $_POST['small_res_box']) {
        $cu->properties->smallResBox = $_POST['small_res_box'];
    }

    $cu->properties->planetCircleWidth = $_POST['planet_circle_width'];
    $cu->properties->itemShow = $_POST['item_show'];
    $cu->properties->imageFilter = $_POST['image_filter'];
    $cu->properties->helpBox = $_POST['helpbox'];
    $cu->properties->noteBox = $_POST['notebox'];
    $cu->properties->showAdds = $_POST['show_adds'];

    if (isset($_POST['image_ext']) && $_POST['image_ext'] && isset($_POST['image_url']) && $_POST['image_url']) {
        $cu->properties->imageUrl = htmlentities($_POST['image_url']);
        $cu->properties->imageExt = htmlentities($_POST['image_ext']);
    } else if (isset($_POST['image_select']) && isset($imagepacks[$_POST['image_select']])) {
        $imp = $imagepacks[$_POST['image_select']];
        $cu->properties->imageUrl = $imp['path'];
        if (isset($_POST['image_ext']) && in_array($_POST['image_ext'], $imp['extensions'], true)) {
            $cu->properties->imageExt = $_POST['image_ext'];
        } else {
            $cu->properties->imageExt = $imp['extensions'][0];
        }
    } else {
        $cu->properties->imageUrl = null;
        $cu->properties->imageExt = null;
    }

    success_msg("Design-Daten wurden geändert!");

    if ($designChange) {
        echo "<script type=\"text/javascript\">document.location='?page=userconfig&mode=design'</script>";
    }
}
if (isset($_GET['changes']) && $_GET['changes'] == 1) {
    success_msg("Design-Daten wurden geändert!");
}


//
//Formular
//

echo "<form action=\"?page=$page&mode=design\" method=\"post\">";
$cstr = checker_init();
tableStart("Designoptionen");

//Design wählen
echo "<tr>
        <th>Design w&auml;hlen:</th>
        <td width=\"64%\" colspan=\"4\">
                <select name=\"css_style\" id=\"designSelector\" onchange=\"xajax_designInfo(this.options[this.selectedIndex].value);\">";
echo '<option value="">(Standard)</option>';
foreach ($designs as $k => $v) {
    if (!$v['restricted'] || $cu->admin || $cu->developer) {
        echo "<option value=\"$k\"";
        if ($cu->properties->cssStyle == $k)
            echo " selected=\"selected\"";
        echo ">" . $v['name'] . "</option>";
    }
}
echo "</select>
                <div id=\"designInfo\"></div>";
echo "<script type=\"text/javascript;\">xajax_designInfo(document.getElementById('designSelector').options[document.getElementById('designSelector').selectedIndex].value);</script>";
echo "</tr>";

// Bildpacket wählen
echo "<tr>
            <th>Bildpaket w&auml;hlen:</th>
            <td width=\"64%\" colspan=\"4\">
                <select id=\"image_select\" name=\"image_select\" onchange=\"xajax_imagePackInfo(this.options[this.selectedIndex].value);\">";
echo "<option value=\"\">(Selbstdefiniert oder Standard)</option>";
foreach ($imagepacks as $k => $v) {
    echo "<option value=\"" . $k . "\"";
    if ($cu->properties->imageUrl == $v['path']) {
        echo " selected=\"selected\"";
    }
    echo ">" . $v['name'] . "</option>";
}
echo "</select> <span id=\"imagePackExtension\"></span><br/>
                <div id=\"imagePackInfo\"></div>";
echo "<script type=\"text/javascript;\">xajax_imagePackInfo(document.getElementById('image_select').options[document.getElementById('image_select').selectedIndex].value,'" . $cu->properties->imageExt . "','" . $cu->properties->imageUrl . "');</script>";
echo "</td>";
echo "</tr>";

//Planetkreisgrösse
echo "<tr>
            <th>Planetkreisgr&ouml;sse:</th>
            <td width=\"64%\" colspan=\"4\">
              <select name=\"planet_circle_width\">";
for ($x = 450; $x <= 700; $x += 50) {
    echo "<option value=\"$x\"";
    if ($cu->properties->planetCircleWidth == $x) echo " selected=\"selected\"";
    echo ">" . $x . "</option>";
}
echo "</select> <span " . tm("Info", "Mit dieser Option l&auml;sst sich die gr&ouml;sse des Planetkreises in der &Uuml;bersicht einstellen.<br>Je nach Aufl&ouml;sung die du verwendest ist es beispielsweise nicht m&ouml;glich eine Gr&ouml;sse von 700 Pixeln zu haben. Finde selber heraus welche Gr&ouml;sse am besten Aussieht.") . "><u>Info</u></span>
            </td>
        </tr>";

//Schiff/Def Ansicht (Einfach/Voll)
echo "<tr>
                <th>Schiff/Def Ansicht:</th>";
echo "<td>
                      <input type=\"radio\" name=\"item_show\" value=\"full\"";
if ($cu->properties->itemShow == 'full') echo " checked=\"checked\"";
echo " /> Volle Ansicht
                  </td>
                  <td width=\"48%\" colspan=\"3\">
                       <input type=\"radio\" name=\"item_show\" value=\"small\"";
if ($cu->properties->itemShow == 'small') echo " checked=\"checked\"";
echo " /> Einfache Ansicht
                   </td>";
echo "</tr>";


//Bildfilter (An/Aus)
echo "<tr>
                <th>Bildfilter:</th>";
echo "<td>
                      <input type=\"radio\" name=\"image_filter\" value=\"1\"";
if ($cu->properties->imageFilter == 1) echo " checked=\"checked\"";
echo "/> An
                  </td>
                  <td width=\"48%\" colspan=\"3\">
                      <input type=\"radio\" name=\"image_filter\" value=\"0\"";
if ($cu->properties->imageFilter == 0) echo " checked=\"checked\"";
echo "/> Aus
                  </td>";
echo "</tr>";

//Hilfefenster (Aktiviert/Deaktiviert)
echo "<tr>
                    <th>Separates Hilfefenster:</th>
                    <td>
                  <input type=\"radio\" name=\"helpbox\" value=\"1\" ";
if ($cu->properties->helpBox == 1) echo " checked=\"checked\"";
echo "/> Aktiviert
              </td>
              <td width=\"48%\" colspan=\"3\">
                  <input type=\"radio\" name=\"helpbox\" value=\"0\" ";
if ($cu->properties->helpBox == 0) echo " checked=\"checked\"";
echo "/> Deaktiviert
                </td>
              </tr>";

//Notizbox (Aktiviert/Deaktiviert)
echo "<tr>
                    <th>Separate Notizbox:</th>
                    <td>
                  <input type=\"radio\" name=\"notebox\" value=\"1\" ";
if ($cu->properties->noteBox == 1) echo " checked=\"checked\"";
echo "/> Aktiviert
              </td>
              <td width=\"48%\" colspan=\"3\">
                  <input type=\"radio\" name=\"notebox\" value=\"0\" ";
if ($cu->properties->noteBox == 0) echo " checked=\"checked\"";
echo "/> Deaktiviert
                </td>
              </tr>";


// Werbebanner
echo "<tr>
                    <th>Werbung anzeigen:</th>
                    <td>
                  <input type=\"radio\" name=\"show_adds\" value=\"1\" ";
if ($cu->properties->showAdds == 1) echo " checked=\"checked\"";
echo "/> Aktiviert
              </td>
              <td width=\"48%\" colspan=\"3\">
                  <input type=\"radio\" name=\"show_adds\" value=\"0\" ";
if ($cu->properties->showAdds == 0) echo " checked=\"checked\"";
echo "/> Deaktiviert
                </td>
              </tr>";

echo "<tr>
                    <th>Schlanke Ressourcenanzeige:</th>
                    <td>
                  <input type=\"radio\" name=\"small_res_box\" value=\"1\" ";
if ($cu->properties->smallResBox == 1) echo " checked=\"checked\"";
echo "/> Aktiviert
              </td>
              <td width=\"48%\" colspan=\"3\">
                  <input type=\"radio\" name=\"small_res_box\" value=\"0\" ";
if ($cu->properties->smallResBox == 0) echo " checked=\"checked\"";
echo "/> Deaktiviert
                </td>
              </tr>";

tableEnd();

echo "<input type=\"submit\" name=\"data_submit_design\" value=\"&Uuml;bernehmen\"></form><br/><br/>";


tableStart("Bildpakete herunterladen");
$cnt = 0;
echo '<tr>
        <th class="tbltitle">Bildpaket</th>
        <th class="tbltitle">Datei</th>
        <th class="tbltitle">Autor</th>
        <th class="tbltitle">Grösse</th>
        <th class="tbltitle">Letzte Änderung</th></tr>';
foreach ($imagepacks as $k => $v) {
    if (isset($v['files']) && count($v['files']) > 0) {
        foreach ($v['files'] as $file) {
            $path = IMAGEPACK_DOWNLOAD_DIRECTORY . "/" . $file;
            if (is_file($path)) {
                $cnt++;
                $fs = filesize($path);
                $t = filemtime($path);
                echo "<tr>
                <td>" . $v['name'] . "</td>
                <td><a href=\"" . $path . "\">" . $file . "</a></td>
                <td><a href=\"mailto:" . $v['email'] . "\">" . $v['author'] . "</a></td>
                <td>" . byte_format($fs) . "</td>
                <td>" . df($t) . "</td>
                </tr>";
            }
        }
    }
}
if ($cnt == 0) {
    echo '<tr><td colspan="5" class="tbldata"><i>Keine Downloads vorhanden!</i></tr>';
}
tableEnd();
