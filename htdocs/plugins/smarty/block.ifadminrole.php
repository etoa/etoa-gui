<?php
function smarty_block_ifadminrole($params, $content, &$smarty, &$repeat)
{
	if (isset($params['required']) && isset($params['provided']))
	{	
		$rm = new AdminRoleManager();
		if (isset($content) && $rm->checkAllowed($params['required'], $params['provided'])) {
			return $content;
		}
	}
}
?>