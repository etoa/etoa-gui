<?php

	// Load smarty template engine
	require(SMARTY_DIR.'/Smarty.class.php');

	// Create template object
	$tpl = new Smarty;
	$tpl->template_dir = SMARTY_TEMPLATE_DIR;
	$tpl->compile_dir = SMARTY_COMPILE_DIR;

?>
