<?PHP

use EtoA\Message\ReportRepository;
use EtoA\Message\ReportSearch;

$xajax->register(XAJAX_FUNCTION, 'reportSetRead');
$xajax->register(XAJAX_FUNCTION, 'reportSetDeleted');
$xajax->register(XAJAX_FUNCTION, 'reportSelectAll');

function reportSetRead($id)
{
    global $app, $cu;

    /** @var ReportRepository $reportRepository */
    $reportRepository = $app[ReportRepository::class];
    $reportRepository->markAsRead($cu->getId(), [$id]);

    $or = new xajaxResponse();
    $or->assign("repimg" . $id, "src", "images/pm_normal.gif");
    return $or;
}

function reportSetDeleted($id)
{
    global $app, $cu;

    /** @var ReportRepository $reportRepository */
    $reportRepository = $app[ReportRepository::class];

    $or = new xajaxResponse();
    $r = Report::createFactory($id);
    $reportRepository->delete($cu->getId(), (bool) $r->archived, [$id]);
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
