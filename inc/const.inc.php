<?php

	// OS-Version feststellen
	if (defined('POSIX_F_OK'))
	{
		define('UNIX',true);
		define('WINDOWS',false);
		define('UNIX_USER',"etoa");
		define('UNIX_GROUP',"apache");
	}
	else
	{
		define('UNIX',false);
		define('WINDOWS',true);
	}

	// Path to the relative root of the game
	if (!defined('RELATIVE_ROOT'))
		define('RELATIVE_ROOT','');

	// Cache directory
	if (!defined('CACHE_ROOT'))
		define('CACHE_ROOT',RELATIVE_ROOT.'cache');

	// Class directory
	if (!defined('CLASS_ROOT'))
		define('CLASS_ROOT',RELATIVE_ROOT.'classes');

	// Data file directory
	if (!defined('DATA_DIR'))
		define('DATA_DIR',RELATIVE_ROOT."data");

	// Image directory
	if (!defined('IMAGE_DIR'))
		define('IMAGE_DIR',RELATIVE_ROOT."images");


	if (!defined('ADMIN_MODE'))
		define('ADMIN_MODE',false);

	if (!defined('USE_HTML'));
		define('USE_HTML',true);


	define('ERROR_LOGFILE',CACHE_ROOT."/errors.txt");
	define('DBERROR_LOGFILE',CACHE_ROOT."/dberrors.txt");
	
?>
