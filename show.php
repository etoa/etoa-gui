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

	define("IMAGE_PATH",$cfg->get('default_image_path'));
	define("IMAGE_EXT","png");
	define('CSS_STYLE',DESIGN_DIRECTORY."/".$cfg->value('default_css_style'));

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
			
	if ($show)
	{
		if ($page!="" && $page=="help")
		{
			$index = "help";
			$page = $index;
			$sub="content/";
		}
		elseif ($page!="" && $page=="contact")
		{
			$index = "contact";
			$page = $index;
			$sub="index/";
		}
		elseif ($index!="")
		{
			$index = ($index=="stats") ? "ladder" : $index;
			$page = $index;
			$sub="index/";
		}
		elseif ($info!="")
		{
			$page=$info;
			$sub="info/";
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
			exit;
		}


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
	$tpl->display(getcwd()."/tpl/headerext.html");

	echo $content;

	// Page footer
	$tpl->display(getcwd()."/tpl/footer.tpl");

?>
