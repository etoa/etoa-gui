<?php
function smarty_block_infobox($params, $content, &$smarty, &$repeat)
{
	$title = isset($params['title']) ? $params['title'] : '';

	if (!isset($content)) {
		return '<div class="boxLayout">
		<div class="infoboxtitle">'.$title.'</div>
		<div class="infoboxcontent">';
	} else {
		return $content.'</div>';
	}		
}
?>