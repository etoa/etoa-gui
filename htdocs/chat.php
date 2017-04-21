<?PHP
	/* fastchat von river */

	define('RELATIVE_ROOT','');
	include_once(RELATIVE_ROOT.'inc/bootstrap.inc.php');

	$login = false;
	if (isset($_SESSION['user_id']))
	{
		$login = true;
	
		$res = dbquery("
		SELECT * FROM
			chat_banns
		WHERE
			user_id=".$_SESSION['user_id'].";");
		if (!isset($res))
		{
			$tpl->assign('errmsg', "Irgend etwas lief schief. Versuche den Chat neu zu laden.");
		}
		elseif (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);
			$tpl->assign('errmsg', "Du wurdest vom Chat gebannt!<br/><br/><b>Grund:</b> ".$arr['reason']);
		}
		else
		{
			$cu = new CurrentUser($_SESSION['user_id']);
			$_SESSION['ccolor'] = $cu->properties->chatColor;
			$tpl->assign('chatColor', $cu->properties->chatColor);
		}
        $tpl->assign('login', $login);
	}

	// Select design
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
	
	// Chat design css
	if (file_exists(CSS_STYLE."/chat.css")) {
		$chatCss = CSS_STYLE."/chat.css";
	} else {
		$chatCss = 'web/css/chat.css';
	}
	$tpl->assign('chatCss', $chatCss);
	
	$tpl->display("tpl/layouts/chat.html");
	
	dbclose();
	
?>