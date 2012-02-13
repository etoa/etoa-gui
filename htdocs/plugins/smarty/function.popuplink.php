<?PHP
function smarty_function_popuplink($params, $template)
{
	return popupLink($params["type"], $params["title"], isset($params['class']) ? $params['class'] : null, isset($params['params']) ? $params['css'] : null);
}
?>