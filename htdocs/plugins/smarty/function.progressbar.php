<?PHP
function smarty_function_progressbar($params, $template)
{
	if (isset($params['id'])) {

		$val = isset($params['value']) ? intval($params['value']) : 0;
		$max = isset($params['max']) ? intval($params['max']) : 100;
	
		return '<div class="progressbarContainer" id="'.$params['id'].'">
					<progress max="'.$max.'" value="'.$val.'"></progress>
					<div class="progressbarLabel">'.round($val/$max*100).'%</div>
				</div>';
	}
}
?>