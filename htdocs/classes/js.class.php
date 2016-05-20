<?PHP

/*
 * Let us introduce the new javascript class
 * add here every init function including jQuery init
 *
 * function names starting with jq using jQuery, starting with js only using js
 *
 * @features
 * -okMsg
 * -errorMsg
 */

	class JS
	{
		static public function jqRemovable($class='remove', $value='')
		{
			return '<script type="text/javascript">
						$(document).ready(function(){
							$(".'.$class.'").each(function(){
								setRemovable(this, \'buddy\', \''.$value.'\', null);
							})
						});
					</script>';
		}

		static public function jqClickable($class='clickable')
		{
			return '<script type="text/javascript">
						$(document).ready(function(){
							$(".'.$class.'").each(function(i){
								setClickable(this, i);
							})
						});
					</script>';
		}
	}

?>