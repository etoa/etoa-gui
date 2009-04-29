<?PHP
	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	Dateiname: main_i.php
	// 	Topic: Alternative Include-Seite
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 07.03.2006
	// 	Kommentar:
	//

	/**
	* Alternative main file for out of game viewing of specific pages
	*
	* @author MrCage mrcage@etoa.ch
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
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

	$tpl->assign("gameTitle",$cfg->game_name->v." ".$cfg->game_name->p1.(isset($indexpage[$index])?' - '.$indexpage[$index]['label']:''));
	$tpl->assign("templateDir",CSS_STYLE);

	// Xajax header
	ob_start();
	echo $xajax->printJavascript(XAJAX_DIR);
	$tpl->assign("xajaxJS",ob_get_clean());

	// Tooltip init
	ob_start();
	initTT();
	$tpl->assign("bodyTopStuff",ob_get_clean());

	$tpl->assign("topmenu",$indexpage);
	$tpl->assign("loginurl",LOGINSERVER_URL);
	$tpl->assign("roundname",ROUNDID);

	//
	// Page content
	//

	ob_start();

	echo '<div id="outGameContent">';
			
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
			if (!eregi("^[a-z\_]+$",$index) || strlen($index)>50)
			{
				die("<h1>Fehler</h1>Der Seitenname <b>".$index."</b> enth&auml;lt unerlaubte Zeichen!<br/><br/>
				<a href=\"javascript:window.close();\">Schliessen</a><br/><br/>");
			}
			if (file_exists($sub.$index.".php"))
			{
				$popup = true;
				include ($sub.$index.".php");
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
			if (!eregi("^[a-z\_]+$",$page) || strlen($page)>50)
			{
				die("<h1>Fehler</h1>Der Seitenname <b>".$page."</b> enth&auml;lt unerlaubte Zeichen!<br/><br/>
				<a href=\"javascript:window.close();\">Schliessen</a><br/><br/>");
			}
			if (file_exists($sub.$page.".php"))
			{
				$popup = true;
				include ($sub.$page.".php");
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
	echo '</div>';

	$content = ob_get_clean();

	// Display header
	if ($loggedIn && $page!=DEFAULT_PAGE)
		$tpl->display(getcwd()."/tpl/header.html");
	else
		$tpl->display(getcwd()."/tpl/headerext.html");

	echo $content;

	// Page footer
	$tpl->display(getcwd()."/tpl/footer.html");

?>
