<?PHP

$xajax->register(XAJAX_FUNCTION, 'designInfo');

function designInfo($did)
{
    $ajax = new xajaxResponse();
    $designs = get_designs();
    if ($did && isset($designs[$did])) {
        $cd = $designs[$did];
        $out = "
        <b>Version:</b> " . $cd['version'] . "<br/>
        <b>Geändert:</b> " . $cd['changed'] . "<br/>
        <b>Autor:</b> <a href=\"mailto:" . $cd['email'] . "\">" . $cd['author'] . "</a><br/>
        <b>Beschreibung:</b> " . $cd['description'] . "";
    } else {
        $out = '';
    }
    $ajax->assign("designInfo", "innerHTML", $out);
    return $ajax;
}
