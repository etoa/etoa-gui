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

	$indexpage = array();
	$indexpage['login']=array('url'=>'?index=login','label'=>'Einloggen');
	$indexpage['register']=array('url'=>'?index=register','label'=>'Registrieren');
	$indexpage['pwforgot']=array('url'=>'?index=pwforgot','label'=>'Passwort');
	$indexpage['ladder']=array('url'=>'?index=ladder','label'=>'Rangliste');
	$indexpage['gamestats']=array('url'=>'?index=gamestats','label'=>'Server');
	$indexpage['pillory']=array('url'=>'?index=pillory','label'=>'Pranger');
	$indexpage['help']=array('url'=>'?index=help','label'=>'Hilfe');
	$indexpage['contact']=array('url'=>'?index=contact','label'=>'Kontakt');

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

	if (isset($cu) && $cu->properties->cssStyle !='')
	{
		define('CSS_STYLE',DESIGN_DIRECTORY."/".$cu->properties->cssStyle);
	}
	else
	{
		define('CSS_STYLE',DESIGN_DIRECTORY."/".$cfg->value('default_css_style'));
	}
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
	$tpl->assign("additional_css", array('web/css/outgame.css'));

	// Xajax header
	ob_start();
	echo $xajax->printJavascript(XAJAX_DIR);
	$tpl->assign("xajaxJS",ob_get_clean());

	// Tooltip init
	ob_start();
	initTT();
	$tpl->assign("bodyTopStuff",ob_get_clean());

	$tpl->assign("topmenu",$indexpage);
	$tpl->assign("loginurl",Config::getInstance()->loginurl->v);
	$tpl->assign("roundname",Config::getInstance()->roundname->v);

	//
	// Page content
	//

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
	if ($loggedIn)
		$show = true;
			
	if ($show)
	{
		if ($index!="")
		{
			$index = ($index=="stats") ? "ladder" : $index;
			$sub="index/";
			if (!preg_match('^[a-z\_]+$^',$index) || strlen($index)>50)
			{
				die("<h1>Fehler</h1>Der Seitenname <b>".$index."</b> enth&auml;lt unerlaubte Zeichen!<br/><br/>
				<a href=\"javascript:window.close();\">Schliessen</a><br/><br/>");
			}
			if (file_exists($sub.$index.".php"))
			{
				$popup = true;
				include ($sub.$index.".php");
				logAccess($index,"public");

				echo "<br/><br/>";
			}
			else
			{
				echo "<h1>Fehler:</h1> Die Seite <b>".$index."</b> existiert nicht!<br/><br/>";
			}
		}
		elseif ($page!="" && $loggedIn  && $page!=DEFAULT_PAGE)
		{
			$sub="content/";
			if (!preg_match('/^[a-z\_]+$/',$page) || strlen($page)>50)
			{
				die("<h1>Fehler</h1>Der Seitenname <b>".$page."</b> enth&auml;lt unerlaubte Zeichen!<br/><br/>
				<a href=\"javascript:window.close();\">Schliessen</a><br/><br/>");
			}
			if (file_exists($sub.$page.".php"))
			{
				$popup = true;
				include ($sub.$page.".php");

				if (isset($_GET['sub']))
	                                $lasub = $_GET['sub'];
                                elseif (isset($_GET['action']))
                                     $lasub = $_GET['action'];
                                elseif (isset($_GET['site']))
                                        $lasub = $_GET['site'];
                                else
                                     $lasub="";

				logAccess($page,"ingame",$lasub);

				echo "<br/><br/>";
			}
			else
			{
				echo "<h1>Fehler:</h1> Die Seite <b>".$page."</b> existiert nicht!<br/><br/>";
			}
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

	$tpl->assign("logged_in", ($loggedIn && $page!=DEFAULT_PAGE));

	$tpl->assign("content_for_layout", ob_get_clean());
	
	$layoutTemplate = "/tpl/layouts/game/external.html";
	$tpl->display(getcwd().'/'.$layoutTemplate);

?>
