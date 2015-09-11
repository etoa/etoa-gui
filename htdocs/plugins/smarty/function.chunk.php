<?php
function smarty_function_chunk($params, &$smarty)
{
	if (isset($params['name']) && trim($params['name']) != "")
	{	
		echo $smarty->fetch('chunks/'.$params['name'].'.html');
	}
}
?>