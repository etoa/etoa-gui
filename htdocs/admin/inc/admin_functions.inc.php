<?PHP

use Doctrine\DBAL\Query\QueryBuilder;

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
        return "<a href=\"$url\" onclick=\"$ocl\"><img src=\"/images/icons/copy.png\" alt=\"Kopieren\" style=\"width:16px;height:18px;border:none;\" title=\"Kopieren\" /></a>";
    else
        return "<a href=\"$url\"><img src=\"/images/icons/copy.png\" alt=\"Kopieren\" style=\"width:16px;height:18px;border:none;\" title=\"Kopieren\" /></a>";
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
        return "<a href=\"$url\" onclick=\"$ocl\"><img src=\"/images/icons/delete.png\" alt=\"Löschen\" style=\"width:16px;height:15px;border:none;\" title=\"Löschen\" /></a>";
    else
        return "<a href=\"$url\"><img src=\"/images/icons/delete.png\" alt=\"Löschen\" style=\"width:18px;height:15px;border:none;\" title=\"Löschen\" /></a>";
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

/**
 * Shows a datepicker
 */
function showDatepicker(string $element_name, int $timestamp, bool $time = false, bool $seconds = false): void
{
    if ($time) {
        echo '<input type="datetime-local" name="' . $element_name . '" value="' . date('Y-m-d\TH:i' . ($seconds ? ':s' : ''), $timestamp) . '" />';
    } else {
        echo '<input type="date" name="' . $element_name . '" value="' . date('Y-m-d', $timestamp) . '" />';
    }
}

/**
 * Parse value submitted by datepicker field
 */
function parseDatePicker(string $value): int
{
    try {
        $dt = new DateTime($value);
        return $dt->getTimestamp();
    } catch (Exception) {
        return 0;
    }
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
