<?php
function smarty_function_text($params, &$smarty)
{
	if (isset($params['key']) && trim($params['key']) != "")
	{	
		$tm = new TextManager();
		$text = $tm->getText($params['key']);
		if (text != null) {
			if ($text->enabled && !empty($text->content))
			{
				if (isset($params['assign'])) {
					$smarty->assign($params['assign'], $text->content);
					return;
				} else {
					return $text->content;
				}
			} else {
				return '';
			}
		} else {
			return MessageBox::error("Smarty Plugin Error", "The specified config key '".htmlentities($params['key'])."' does not exist!");
		}
	}
	return MessageBox::error("Smarty Plugin Error", "Invalid or missing 'key' for cfg plugin!");
}
?>