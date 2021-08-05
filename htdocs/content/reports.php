<?php

use EtoA\Message\ReportRepository;

/** @var ReportRepository $reportRepository */
$reportRepository = $app[ReportRepository::class];

define("REPORT_LIMIT", 20);

echo "<h1>Berichte</h1>";

// Show navigation
$tabitems = array("all" => "Neuste Berichte");
foreach (Report::$types as $k => $v) {
    $tabitems[$k] = $v;
}
$tabitems["archiv"] = "Archiv";
show_tab_menu("type", $tabitems);

// Detect report type
$type = isset($_GET['type']) ? $_GET['type'] : 'all';

// Selektiere archivieren
if (isset($_POST['submitarchivselection'])  && checker_verify()) {

    if (count($_POST['delreport']) > 0) {

        $ids = array();
        foreach ($_POST['delreport'] as $id => $val)
            array_push($ids, intval($id));

        $reportRepository->archive($cu->getId(), $ids);

        if (count($_POST['delreport']) == 1) {
            success_msg("Bericht wurde archiviert!");
        } else {
            success_msg("Berichte wurden archiviert!");
        }
    }
}

// Selektiere löschen
if (isset($_POST['submitdeleteselection'])  && checker_verify()) {
    if (isset($_POST['delreport']) && count($_POST['delreport']) > 0) {

        $ids = array();
        foreach ($_POST['delreport'] as $id => $val)
            array_push($ids, intval($id));

        $reportRepository->delete($cu->getId(), $type === "archiv", $ids);

        if (count($_POST['delreport']) == 1) {
            success_msg("Bericht wurde gel&ouml;scht!");
        } else {
            success_msg("Berichte wurden gel&ouml;scht!");
        }
    }
}
// Alle Nachrichten löschen
elseif (isset($_POST['submitdeleteall']) && checker_verify()) {
    $deleteType = in_array($type, ['archiv', 'all'], true) ? null : $type;
    $reportRepository->delete($cu->getId(), $type === "archiv", null, $deleteType);

    success_msg("Alle Berichte wurden gel&ouml;scht!");
}

// Limit for pagination
$limit =  (isset($_GET['limit'])) ? intval($_GET['limit']) : 0;
$limit -= $limit % REPORT_LIMIT;
$limitstr = $limit . "," . REPORT_LIMIT;

// Load all reports
if ($type == "all") {
    $reports = Report::find(array("user_id" => $cu->id), "timestamp DESC", $limitstr);
    $totalReports = Report::find(array("user_id" => $cu->id), "timestamp DESC", "", 1);
} elseif ($type == "archiv") {
    $reports = Report::find(array("user_id" => $cu->id, "archived" => true), "timestamp DESC", $limitstr);
    $totalReports = Report::find(array("user_id" => $cu->id, "archived" => true), "timestamp DESC", "", 1);
} else {
    $reports = Report::find(array("type" => $type, "user_id" => $cu->id), "timestamp DESC", $limitstr);
    $totalReports = Report::find(array("type" => $type, "user_id" => $cu->id), "timestamp DESC", "", 1);
}

// Check if reports available
if (count($reports) > 0) {
    echo "<form action=\"?page=$page&amp;type=" . $type . "\" method=\"post\"><div>";
    $cstr = checker_init();
    // Table title
    if ($type == "all")
        tableStart("Neueste Berichte");
    elseif ($type == "archiv")
        tableStart("Archiv");
    else
        tableStart(Report::$types[$type] . "berichte");

    // Pagination navigation
    echo "<tr><th colspan=\"5\">";
    echo "<div style=\"float:right;\">";
    if ($limit > 0) {
        echo "<input type=\"button\" value=\"&lt;&lt;\" onclick=\"document.location='?page=$page&amp;type=$type&amp;limit=0'\" /> ";
        echo "<input type=\"button\" value=\"&lt;\" onclick=\"document.location='?page=$page&amp;type=$type&amp;limit=" . ($limit - REPORT_LIMIT) . "'\" /> ";
    }
    $totalReportsCount = (int) $totalReports;
    echo " " . $limit . "-" . min($limit + REPORT_LIMIT, $totalReportsCount) . " ";
    if ($limit + REPORT_LIMIT < $totalReportsCount) {
        echo "<input type=\"button\" value=\"&gt;\" onclick=\"document.location='?page=$page&amp;type=$type&amp;limit=" . ($limit + REPORT_LIMIT) . "'\" /> ";
        echo "<input type=\"button\" value=\"&gt;&gt;\" onclick=\"document.location='?page=$page&amp;type=$type&amp;limit=" . ($totalReportsCount - ($totalReportsCount % REPORT_LIMIT)) . "'\" /> ";
        echo "</div></th></tr>";
    }

    $ccnt = count($reports);
    // Table header
    echo "<tr>
        <th colspan=\"2\">Nachricht:</th>";
    if ($type == "all")
        echo "<th style=\"width:100px;\">Kategorie:</th>";
    echo "<th style=\"width:150px\">Datum:</th>
        <th style=\"text-align:center;\"><input type=\"button\" id=\"selectBtn\" value=\"X\" onclick=\"xajax_reportSelectAll(" . $ccnt . ",this.value)\"/></td>
        </tr>";

    $cnt = 0;
    // Iterate through each report
    foreach ($reports as $rid => $r) {
        if (!$r->read) {
            $im_path = "images/pm_new.gif";
        } else {
            $im_path = "images/pm_normal.gif";
        }
        echo "<tr>
            <td style=\"width:16px\"><img src=\"" . $im_path . "\" alt=\"Mail\" id=\"repimg" . $rid . "\" /></td>
            <td id=\"header" . $rid . "\"><a href=\"javascript:;\" onclick=\"toggleBox('report" . $rid . "');xajax_reportSetRead(" . $rid . ")\" >" . $r->subject . "</a></td>";
        if ($type == "all")
            echo "<td><b>" . $r->typeName() . "</b></td>";
        echo "<td>" . df($r->timestamp) . "</td>";
        echo "<td id=\"del" . $rid . "\" style=\"width:2%;text-align:center;padding:0px;vertical-align:middle;\">
                            <input id=\"delreport[" . $cnt . "]\" type=\"checkbox\" name=\"delreport[" . $rid . "]\" value=\"1\" title=\"Report zum L&ouml;schen markieren\" /></td></tr>";
        echo "<tr><td colspan=\"5\" style=\"padding:10px;display:none;\" id=\"report" . $rid . "\">";
        echo $r;
        echo "<br /><br />";
        /*$msgadd = "&amp;message_text=".base64_encode($r);
            if(substr($r->subject,0,3) == "Fw:")
                $subject = base64_encode($r->subject);
            else
                $subject = base64_encode("Fw: ".$r->subject);

            echo "<input type=\"button\" value=\"Weiterleiten\" onclick=\"document.location='?page=messages&mode=new&amp;message_subject=".$subject."".$msgadd."'\" name=\"remit\" />&nbsp;*/
        echo "<input type=\"button\" value=\"L&ouml;schen\" onclick=\"toggleBox('report" . $rid . "');xajax_reportSetDeleted(" . $rid . ");\" />&nbsp;";
        ticket_button('8', "Regelverstoss melden");
        echo "</td>";
        echo "</tr>";
        $cnt++;
    }
    tableEnd();
    echo "<input type=\"submit\" name=\"submitdeleteselection\" value=\"Markierte l&ouml;schen\" />&nbsp;
                <input type=\"submit\" name=\"submitdeleteall\" value=\"Alle l&ouml;schen\" onclick=\"return confirm('Wirklich alle Berichte in dieser Kategorie löschen?');\" />&nbsp;&nbsp;";
    if ($type != "archiv")
        echo "<input type=\"submit\" name=\"submitarchivselection\" value=\"Markierte archivieren\" />";

    echo "</div></form>";
} else {
    error_msg("Keine Berichte vorhanden!", 1);
}
