<?PHP
function smarty_function_helpimagelink($params, $template)
{
	$alt = isset($params['alt']) ? $params['alt'] : 'Link';
	$css = isset($params['css']) ? $params['css'] : '';
	
	return helpImageLink($params['url'], $params['img'], $alt, $css);	
}
?>