<?php
function smarty_function_msg($params, &$smarty)
{
	if (isset($params['type']) && $params['type']=="saved")
	{	
		return MessageBox::saved();
	}
	
	if (isset($params['type']) && isset($params['text']))
	{
		return MessageBox::get($params['type'], isset($params['title']) ? $params['title'] : '', $params['text']);
	}
	return MessageBox::error("Smarty Plugin Error", "Invalid or missing 'type' and 'text' for msg plugin!");
}
?>