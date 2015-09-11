<?php
function smarty_function_cfg($params, &$smarty)
{
	if (isset($params['key']) && trim($params['key']) != "")
	{	
		if (isset(Config::getInstance()->$params['key'])) {
			if (isset($params['assign'])) {
				$smarty->assign($params['assign'], Config::getInstance()->$params['key']->v);
				return;
			} else {
				return Config::getInstance()->$params['key']->v;
			}
		}
		return MessageBox::error("Smarty Plugin Error", "The specified config key '".htmlentities($params['key'])."' does not exist!");
	}
	return MessageBox::error("Smarty Plugin Error", "Invalid or missing 'key' for cfg plugin!");
}
?>