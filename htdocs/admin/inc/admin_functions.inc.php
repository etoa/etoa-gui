<?PHP

use Doctrine\DBAL\Query\QueryBuilder;
use EtoA\Admin\AdminUserRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Building\BuildingDataRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\HostCache\NetworkNameService;
use EtoA\Log\BattleLogRepository;
use EtoA\Log\BattleLogSearch;
use EtoA\Log\DebrisLogRepository;
use EtoA\Log\DebrisLogSearch;
use EtoA\Log\FleetLogFacility;
use EtoA\Log\FleetLogRepository;
use EtoA\Log\FleetLogSearch;
use EtoA\Log\GameLogFacility;
use EtoA\Log\GameLogRepository;
use EtoA\Log\GameLogSearch;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSearch;
use EtoA\Log\LogSeverity;
use EtoA\Ship\ShipDataRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserRepository;

/**
 * Displays a clickable edit button
 *
 * @param string $url Url of the link
 * @param string $ocl Optional onclick value
 */
function edit_button($url, $ocl = "")
{
    if ($ocl != "")
        return "<a href=\"$url\" onclick=\"$ocl\"><img src=\"/images/icons/edit.png\" alt=\"Bearbeiten\" style=\"width:16px;height:18px;border:none;\" title=\"Bearbeiten\" /></a>";
    else
        return "<a href=\"$url\"><img src=\"/images/icons/edit.png\" alt=\"Bearbeiten\" style=\"width:16px;height:18px;border:none;\" title=\"Bearbeiten\" /></a>";
}

/**
 * Displays a clickable copy button
 *
 * @param string $url Url of the link
 * @param string $ocl Optional onclick value
 */
function copy_button($url, $ocl = "")
{
    if ($ocl != "")
        return "<a href=\"$url\" onclick=\"$ocl\"><img src=\"../images/icons/copy.png\" alt=\"Kopieren\" style=\"width:16px;height:18px;border:none;\" title=\"Kopieren\" /></a>";
    else
        return "<a href=\"$url\"><img src=\"../images/icons/copy.png\" alt=\"Kopieren\" style=\"width:16px;height:18px;border:none;\" title=\"Kopieren\" /></a>";
}

/**
 * Displays a clickable delete button
 *
 * @param string $url Url of the link
 * @param string $ocl Optional onclick value
 */
function del_button($url, $ocl = "")
{
    if ($ocl != "")
        return "<a href=\"$url\" onclick=\"$ocl\"><img src=\"../images/icons/delete.png\" alt=\"Löschen\" style=\"width:16px;height:15px;border:none;\" title=\"Löschen\" /></a>";
    else
        return "<a href=\"$url\"><img src=\"../images/icons/delete.png\" alt=\"Löschen\" style=\"width:18px;height:15px;border:none;\" title=\"Löschen\" /></a>";
}

function fieldComparisonSelectBox(string $name): string
{
    $options = [
        'like_wildcard' => 'enthält',
        'like' => 'ist gleich',
        'not_like_wildcard' => 'enthält nicht',
        'not_like' => 'ist ungleich',
        'lt' => 'ist kleiner',
        'gt' => 'ist grösser',
    ];
    $str = "<select name=\"comparisonMode[$name]\">";
    foreach ($options as $key => $value) {
        $str .= '<option value="' . $key . '">' . $value . '</option>';
    }
    $str .= "</select>";
    return $str;
}

function fieldComparisonQuery(QueryBuilder $qry, array $formData, string $column, string $formKey): QueryBuilder
{
    $value = $formData[$formKey];
    switch ($formData['comparisonMode'][$formKey]) {
        case 'like_wildcard':
            $comparator = 'LIKE';
            $value = "%$value%";
            break;
        case 'like':
            $comparator = 'LIKE';
            break;
        case 'not_like_wildcard':
            $comparator = 'NOT LIKE';
            $value = "%$value%";
            break;
        case 'not_like':
            $comparator = 'NOT LIKE';
            break;
        case 'lt':
            $comparator = '<';
            break;
        case 'gt':
            $comparator = '>';
            break;
        default:
            $comparator = '=';
    }
    $qry->andWhere("$column $comparator :$column")
        ->setParameter($column, $value);
    return $qry;
}

//DEPRECATED
function searchQuery($data)
{
    $str = null;
    foreach ($data as $k => $v) {
        if (!isset($str)) {
            $str = base64_encode($k) . ":" . base64_encode($v);
        } else {
            $str .= ";" . base64_encode($k) . ":" . base64_encode($v);
        }
    }
    return base64_encode($str);
}

// DEPRECATED
function searchQueryDecode($query)
{
    $str = explode(";", base64_decode($query, true));
    $res = array();
    foreach ($str as $s) {
        $t = explode(":", $s);
        $res[base64_decode($t[0], true)] = base64_decode($t[1], true);
    }
    return $res;
}

function searchQueryUrl($str)
{
    return "&amp;sq=" . base64_encode($str);
}

/**
 * Builds a search query and sort array
 * based on GET,POST or SESSION data.
 *
 * @param array $arr Pointer to query array
 * @param array $oarr Pointer to order/limit array
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
function searchQueryArray(&$arr, &$oarr)
{
    $arr = array();
    $oarr = array();

    if (isset($_GET['newsearch'])) {
        searchQueryReset();
        return false;
    }

    if (isset($_GET['sq'])) {
        $sq = base64_decode($_GET['sq'], true);
        $ob = explode(";", $sq);
        foreach ($ob as $o) {
            $oe = explode(":", $o);
            $arr[$oe[0]] = array($oe[1], $oe[2]);
        }
        if (!isset($oarr['limit']))
            $oarr['limit'] = 100;
        return true;
    } elseif (isset($_POST['search_submit'])) {
        foreach ($_POST as $k => $v) {
            if (substr($k, 0, 7) == "search_" && $k != "search_submit" && $v != "") {
                $fname = substr($k, 7);
                if ($fname == "order") {
                    if (stristr($v, ":")) {
                        $chk = explode(":", $v);
                        if ($chk[1] == "d")
                            $oarr[$chk[0]] = "d";
                        else
                            $oarr[$chk[0]] = "a";
                    } else
                        $oarr[$v] = "a";
                    continue;
                }
                if ($fname == "limit") {
                    $oarr['limit'] = min(max(1, intval($v)), 5000);
                    continue;
                }

                if (isset($_POST['qmode'][$fname])) {
                    $arr[$fname] = array($_POST['qmode'][$fname], $v);
                } elseif (isset($_POST['qmode'][$k])) {
                    $arr[$fname] = array($_POST['qmode'][$k], $v);
                } elseif (is_numeric($v)) {
                    $arr[$fname] = array("=", $v);
                } else {
                    $arr[$fname] = array("%", $v);
                }
            }
        }
        if (!isset($oarr['limit']))
            $oarr['limit'] = 100;
        return true;
    } elseif (isset($_SESSION['search']['query'])) {
        $arr = $_SESSION['search']['query'];
        $oarr = $_SESSION['search']['order'];
        if (isset($_POST['search_resubmit'])) {
            if (isset($_POST['search_order'])) {
                if (stristr($_POST['search_order'], ":")) {
                    $chk = explode(":", $_POST['search_order']);
                    if ($chk[1] == "d")
                        $oarr[$chk[0]] = "d";
                    else
                        $oarr[$chk[0]] = "a";
                } else
                    $oarr[$_POST['search_order']] = "a";
            }
            if (isset($_POST['search_limit'])) {
                $oarr['limit'] = min(max(1, intval($_POST['search_limit'])), 5000);
            }
        }
        return true;
    }
    return false;
}

function searchQuerySave(&$sa, &$so)
{
    $_SESSION['search']['query'] = $sa;
    $_SESSION['search']['order'] = $so;
}

function searchQueryReset()
{
    unset($_SESSION['search']);
}


/**
 * Displays a select box for choosing the search method
 * for varchar/text mysql table fields ('contains', 'part of'
 * and negotiations of those two)
 *
 * @param string $name Field name
 */
function searchFieldTextOptions($name)
{
    ob_start();
    echo "<select name=\"qmode[$name]\">";
    echo "<option value=\"%\">enth&auml;lt</option>";
    echo "<option value=\"!%\">enth&auml;lt nicht</option>";
    echo "<option value=\"=\">ist gleich</option>";
    echo "<option value=\"!=\">ist ungleich</option>";
    echo "</select>";
    $res = ob_get_contents();
    ob_end_clean();
    return $res;
}

/**
 * Resolves the name of a given search operator
 *
 * @return string Operator name
 */
function searchFieldOptionsName($operator = '')
{
    switch ($operator) {
        case "=":
            return "gleich";
        case "!=":
            return "ungleich";
        case "%":
            return "enthält";
        case "!%":
            return "enthält nicht";
        case "<":
            return "kleiner als";
        case "<=":
            return "kleiner gleich";
        case ">":
            return "grösser als";
        case ">=":
            return "grösser gleich";
        default:
            return "gleich";
    }
}

function drawTechTreeForSingleItem(string $type, \EtoA\Requirement\RequirementsCollection $requirements, int $objectId, array $technologyNames, array $buildingNames)
{
    $objectRequirements = $requirements->getAll($objectId);
    if (count($objectRequirements) > 0) {
        foreach ($objectRequirements as $requirement) {
            if ($requirement->requiredBuildingId > 0) {
                $name = $buildingNames[$requirement->requiredBuildingId];
                $pn = "b:" . $requirement->requiredBuildingId;
            } elseif ($requirement->requiredTechnologyId > 0) {
                $name = $technologyNames[$requirement->requiredTechnologyId];
                $pn = "t:" . $requirement->requiredTechnologyId;
            } else {
                $name = "INVALID";
                $pn = '';
            }

            echo "<a href=\"javascript:;\" onclick=\"var nlvl = prompt('Level für " . $name . " ändern:','" . $requirement->requiredLevel . "'); if (nlvl != '' && nlvl != null) xajax_addToTechTree('" . $type . "'," . $objectId . ",'" . $pn . "',nlvl);\">";
            echo $name . " <b>" . $requirement->requiredLevel . "</b></a>";
            echo " &nbsp; <a href=\"javascript:;\" onclick=\"if (confirm('Anforderung löschen?')) xajax_removeFromTechTree('" . $type . "'," . $objectId . "," . $requirement->id . ")\">" . icon("delete") . "</a>";
            echo "<br/>";
        }
    } else {
        echo "<i>Keine Anforderungen</i>";
    }
}

/**
 * Shows a datepicker
 */
function showDatepicker($element_name, $def_val, $time = false, $seconds = false)
{
    echo "<input type=\"text\" name=\"" . $element_name . "\" value=\"" . date('d.m.Y', $def_val) . "\" class=\"datepicker\" size=\"10\" />";
    if ($time) {
        if ($seconds) {
            echo ":<input type=\"text\" name=\"" . $element_name . "_time\" value=\"" . date('H:i:s', $def_val) . "\" size=\"7\" />";
        } else {
            echo ":<input type=\"text\" name=\"" . $element_name . "_time\" value=\"" . date('H:i', $def_val) . "\" size=\"5\" />";
        }
    }
}

/**
 * Parse value submitted by datepicker field
 */
function parseDatePicker($element_name, $data)
{

    $str = $data[$element_name];
    if (isset($data[$element_name . "_time"])) {
        $str .= " " . $data[$element_name . "_time"];
    }
    return strtotime($str);
}

/**
 * Create file downlad link
 */
function createDownloadLink($file)
{

    $encodedName = base64_encode($file);
    if (!isset($_SESSION['filedownload'][$encodedName])) {
        $_SESSION['filedownload'][$encodedName] = uniqid('', true);
    }
    return "/admin/dl/?path=" . $encodedName . "&hash=" . sha1($encodedName . $_SESSION['filedownload'][$encodedName]);
}

/**
 * $Parse file downlad link
 */
function parseDownloadLink($arr)
{

    if (isset($arr['path']) && $arr['path'] != "" && isset($arr['hash']) && $arr['hash'] != "") {
        $encodedName = $arr['path'];
        $file = base64_decode($encodedName, true);
        if (isset($_SESSION['filedownload'][$encodedName]) && $arr['hash'] == sha1($encodedName . $_SESSION['filedownload'][$encodedName])) {
            unset($_SESSION['filedownload'][$encodedName]);
            return $file;
        }
    }
    return false;
}
