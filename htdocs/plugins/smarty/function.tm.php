<?PHP
function smarty_function_tm($params, $template)
{
	if (isset($params['title']) && isset($params['text'])) {

		return tm($params['title'], $params['text']);
	}
}
?>