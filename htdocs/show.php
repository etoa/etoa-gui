<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

/**
* Alternative main file for out of game viewing of specific pages
*
* @author MrCage mrcage@etoa.ch
* @copyright Copyright (c) 2004 EtoA Gaming, www.etoa.ch
*/

	$indexpage = [
		'login' => [
			'url'=>'?index=login',
			'label'=>'Einloggen'
		],
		'register' => [
			'url'=>'?index=register',
			'label'=>'Registrieren'
		],
		'pwforgot' => [
			'url'=>'?index=pwforgot',
			'label'=>'Passwort'
		],
		'ladder' => [
			'url'=>'?index=ladder',
			'label'=>'Rangliste'
		],
		'gamestats' => [
			'url'=>'?index=gamestats',
			'label'=>'Server'
		],
		'pillory' => [
			'url'=>'?index=pillory',
			'label'=>'Pranger'
		],
		'help' => [
			'url'=>'?index=help',
			'label'=>'Hilfe'
		],
		'contact' => [
			'url'=>'?index=contact',
			'label'=>'Kontakt'
		]
	];

	require_once("inc/bootstrap.inc.php");

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

	//
	// Page header
	//

	$tpl->assign("gameTitle", getGameIdentifier().(isset($indexpage[$index]) ? ' - '.$indexpage[$index]['label'] : ''));
	$tpl->assign("templateDir",CSS_STYLE);
	$tpl->assign("additional_css", array('web/css/external.css'));

	// Xajax header
	$tpl->assign("xajaxJS", $xajax->getJavascript(XAJAX_DIR));

	// Tooltip init
	$tpl->assign("bodyTopStuff", getInitTT());

	$tpl->assign("topmenu",$indexpage);
	$tpl->assign("loginurl", getLoginUrl());
	$tpl->assign("roundname",Config::getInstance()->roundname->v);

	//
	// Page content
	//
	try {

	ob_start();
			
	$show = true;
	// Handle case if outgame key is set
	if ($cfg->register_key->v!="")
	{
		if (isset($_POST['reg_key_auth_submit']))
		{
			if ($_POST['reg_key_auth_value']==$cfg->register_key->v)
			{
				$s->reg_key_auth = $cfg->register_key->v;
			}
			else
			{
				echo "Falscher Schlüssel!<br/><br/>";
			}
		}

		if ($s->reg_key_auth != $cfg->register_key->v)
		{
			$show = false;
		}
	}
	if ($loggedIn) {
		$show = true;
	}

	$tpl->assign("logged_in", ($loggedIn && $page!=DEFAULT_PAGE));
			
	if ($show)
	{
		if ($index!="")
		{
			$index = ($index=="stats") ? "ladder" : $index;
			$sub="index/";
			if (!preg_match('^[a-z\_]+$^',$index) || strlen($index)>50)
			{
				echo "<h1>Fehler</h1>Der Seitenname enth&auml;lt unerlaubte Zeichen!<br/><br/>
				<a href=\"javascript:window.close();\">Schliessen</a><br/><br/>";
			} else {
				if (file_exists($sub.$index.".php"))
				{
					$popup = true;
					include ($sub.$index.".php");
					logAccess($index,"public");

					echo "<br/><br/>";
				}
				else
				{
					echo "<h1>Fehler</h1> Die Seite <b>".$index."</b> existiert nicht!<br/><br/>";
				}
			}
		}
		elseif ($page!="" && $loggedIn  && $page!=DEFAULT_PAGE)
		{
			$popup = true;
			require("inc/content.inc.php");
			echo "<br/><br/>";
		}
		else
		{
			echo '<h1>Öffentliche Seiten</h1>';
			echo '<p>Bitte wähle eine Seite aus:</p><br/><ul>';
			foreach ($indexpage as $k => $v)
			{
				echo '<li><a href="'.$v['url'].'">'.$v['label'].'</a></li>';
			}
			echo '</ul>';
		}

		dbclose();
	}
	else
	{
		echo "<h1>Zugang erfordert Schlüssel</h1>";
		echo "<form action=\"?index=".$_GET['index']."\" method=\"post\">
		Bitte Schlüssel eingeben: <input type=\"text\" value=\"\" name=\"reg_key_auth_value\" /> &nbsp;
		<input type=\"submit\" value=\"Prüfen\" name=\"reg_key_auth_submit\" />
		</form>";
	}		

	$tpl->assign("content_for_layout", ob_get_clean());
	
	} catch (DBException $ex) {
		ob_clean();
		$tpl->assign("content_for_layout", $ex);
	}

	$tpl->display("tpl/layouts/external.html");

?>
