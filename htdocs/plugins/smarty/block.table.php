<?php
function smarty_block_table($params, $content, &$smarty, &$repeat)
{
	$title = isset($params['title']) ? $params['title'] : '';
	$layout = isset($params['layout']) ? $params['layout'] : '';
	$id = isset($params['id']) ? $params['id'] : '';
	$width = isset($params['width']) ? intval($params['width']) : 0;
	
	if (!isset($content)) {
		return tableStart($title, $width, $layout, $id, true);
	} else {
		return $content."</table>";
	}		
}
?>