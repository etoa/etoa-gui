<?PHP

$xajax->register(XAJAX_FUNCTION, 'reportSetRead');
$xajax->register(XAJAX_FUNCTION, 'reportSetDeleted');
$xajax->register(XAJAX_FUNCTION, 'reportSelectAll');

function reportSetRead($id)
{
    $or = new xajaxResponse();
    $r = Report::createFactory($id);
    $r->read = true;
    $or->assign("repimg" . $id, "src", "images/pm_normal.gif");
    return $or;
}

function reportSetDeleted($id)
{
    $or = new xajaxResponse();
    $r = Report::createFactory($id);
    $r->deleted = true;
    $or->assign("header" . $id, 'innerHTML', "<i>" . $r->subject . " (gel&ouml;scht)</i>");
    $or->assign("del" . $id, 'innerHTML', "");
    $or->assign("repimg" . $id, "src", "images/delete.gif");
    return $or;
}

function reportSelectAll($cnt, $bv)
{
    $objResponse = new xajaxResponse();

    if ($bv == "-") {
        for ($x = 0; $x < $cnt; $x++) {
            $objResponse->assign("delreport[" . $x . "]", "checked", "");
        }
        $objResponse->assign("selectBtn", "value", "X");
    } else {
        for ($x = 0; $x < $cnt; $x++) {
            $objResponse->assign("delreport[" . $x . "]", "checked", "true");
        }
        $objResponse->assign("selectBtn", "value", "-");
    }
    return $objResponse;
}
