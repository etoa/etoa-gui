<?PHP
	require_once __DIR__ . '/inc/bootstrap.inc.php';
	$app = require __DIR__ . '/../src/app.php';


	$tpl->assign("gameTitle", getGameIdentifier());
	$tpl->assign("gameIdentifier", getGameIdentifier());
	$tpl->assign("loginurl", getLoginUrl());
	$tpl->assign("roundname",Config::getInstance()->roundname->v);

	$loggedIn = false;
	if ($s->validate(0))
	{
		$cu = new CurrentUser($s->user_id);
		if ($cu->isValid)
		{
			$loggedIn = true;
		}
	}

	$design = DESIGN_DIRECTORY."/official/".$cfg->value('default_css_style');
	if (isset($cu) && $cu->properties->cssStyle !='')
	{
		if (is_dir(DESIGN_DIRECTORY."/custom/".$cu->properties->cssStyle))
		{
			$design = DESIGN_DIRECTORY."/custom/".$cu->properties->cssStyle;
		}
		else if (is_dir(DESIGN_DIRECTORY."/official/".$cu->properties->cssStyle))
		{
			$design = DESIGN_DIRECTORY."/official/".$cu->properties->cssStyle;
		}
	}
	define('CSS_STYLE', $design);
	$tpl->assign("templateDir", CSS_STYLE);
	if (isset($cu) && $cu->properties->imageUrl != '' && $cu->properties->imageExt != '')
	{
		define('IMAGE_PATH',$cu->properties->imageUrl);
		define('IMAGE_EXT',$cu->properties->imageExt);
	}
	else
	{
		define("IMAGE_PATH",$cfg->default_image_path->v);
		define("IMAGE_EXT","png");
	}

	// Xajax header
	$tpl->assign("xajaxJS", $xajax->getJavascript(XAJAX_DIR));

	// Tooltip init
	$tpl->assign("bodyTopStuff", getInitTT());

	try {

		ob_start();

		if ($loggedIn)
		{
			if ($page!="" && $page!=DEFAULT_PAGE)
			{
				$popup = true;
				require __DIR__ . '/inc/content.inc.php';
			}
		}
		else
		{
			error_msg("Du bist nicht eingeloggt!");
		}

		$tpl->assign("content_for_layout", ob_get_clean());

	} catch (DBException $ex) {
		ob_clean();
		$tpl->assign("content_for_layout", $ex);
	}

	$tpl->display("tpl/layouts/popup.html");

	dbclose();
