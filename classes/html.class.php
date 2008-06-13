<?PHP
 	
 	class Html
 	{
 		static function header()
 		{
 			global $xajax;
 			$cfg = Config::getInstance();
 			
			echo '<?xml version="1.0" encoding="UTF-8"?>
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
			';?>
			<html xmlns="http://www.w3.org/1999/xhtml">	
				<head>
					<meta name="author" content="EtoA Gaming" />
					<meta name="keywords" content="Escape to Andromeda, Browsergame, Strategie, Simulation, Andromeda, MMPOG, RPG" />
					<meta name="robots" content="nofollow" />
					<meta name="language" content="de" />
					<meta name="distribution" content="global" />
					<meta name="audience" content="all" />
					<meta name="author-mail" content="mail@etoa.de" />
					<meta name="publisher" content="EtoA Gaming" />
					<meta name="copyright" content="(c) 2007 by EtoA Gaming" />
			
					<meta http-equiv="expires" content="0" />
					<meta http-equiv="pragma" content="no-cache" />
				 	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
					<meta http-equiv="content-script-type" content="text/javascript" />
					<meta http-equiv="content-style-type" content="text/css" />
					<meta http-equiv="content-language" content="de" />
			
					<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
					<title><?PHP echo $cfg->value('game_name')." ".$cfg->param1('game_name');	?></title>
			
					<?PHP 
						echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".CSS_STYLE."/style.css\" />\n";
					?>
					<link rel="stylesheet" href="css/general.css" type="text/css" />
					<link rel="stylesheet" href="css/dropdown.css" type="text/css" />
					<?PHP include("inc/csshacks.inc.php"); ?>
			
					<script src="js/main.js" type="text/javascript"></script>
					<?PHP
						echo $xajax->printJavascript(XAJAX_DIR);
						echo file_exists(CSS_STYLE."/scripts.js") ? "<script src=\"".CSS_STYLE."/scripts.js\" type=\"text/javascript\"></script>" : ''; 
					?>					
					
					<!--<script type="text/javascript" src="js/range.js"></script> -->
					<!--<script type="text/javascript" src="js/timer.js"></script> -->
					<!--<script type="text/javascript" src="js/slider.js"></script>-->
					
					<!--<link type="text/css" rel="StyleSheet" href="css/slider.css" />					-->

				</head> 		
			<?PHP	
 		}
 		
 		static function footer()
 		{
 			?>
		</body>
	</html>
 			<?PHP
 		}
 		
 	}


?>