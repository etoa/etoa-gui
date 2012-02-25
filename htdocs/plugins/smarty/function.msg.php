<?php
function smarty_function_msg($params, &$smarty)
{
	if (isset($params['type']) && $params['type']=="saved")
	{	
		return MessageBox::saved();
	}
	
	if (isset($params['type']) && isset($params['text']))
	{
		switch ($params['type'])
		{
			case "success":		
				return MessageBox::ok($params['text']);
				break;			
			case "ok":		
				return MessageBox::ok($params['text']);
				break;
			case "info":		
				return MessageBox::info($params['text']);
				break;
			case "error":		
				return MessageBox::error($params['text']);
				break;
			case "err":		
				return MessageBox::error($params['text']);
				break;			
			case "warn":		
				return MessageBox::warning($params['text']);
				break;
			case "wargning":		
				return MessageBox::warning($params['text']);
				break;			
			case "validation":		
				return MessageBox::validation($params['text']);
				break;				
			default:
				return MessageBox::error('Invalid type!');
		}
	}
	return MessageBox::error("Invalid or missing type and text for message!");
}
?>