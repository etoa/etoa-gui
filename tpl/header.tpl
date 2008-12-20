<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">	
	<head>
		<title>{$gameTitle}</title>

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

		<!-- CSS Stylesheets -->
		<link rel="stylesheet" type="text/css" href="css/general.css" />
		
		<!-- CSS Hacks for stuipd Internet Explorer -->
		{literal}<!--[if IE]>
		<style type="text/css">
		#dropdown ul {display:inline-block;}
		#dropdown ul {display:inline;}
		#dropdown ul li {float:left;}
		#dropdown {text-align:center;}
		</style>
		<![endif]-->
		<!-- PNG Hack for stupid Internet Explorer -->
		<!--[if IE 6]>
		img, div { 
			behavior: url('css/iepngfix.htc') 
		}
		<script src="js/DD_belatedPNG.js"></script>
		<script>
		    DD_belatedPNG.fix('.png_bg'); /* EXAMPLE */
		    /* string argument can be any CSS selector */
		    /* using .png_bg example is unnecessary */
		    /* change it to what suits you! */
		</script>
		<![endif]-->{/literal}
		
		<!-- General JavaScript -->
		<script type="text/javascript" src="js/main.js"></script>
		<script type="text/javascript" src="js/tooltip.js"></script>

		<!-- XAJAX -->
		{$xajaxJS}
		
		<!-- Scriptaculous -->					
		<script type="text/javascript" src="js/prototype.js"></script>
		<script type="text/javascript" src="js/scriptaculous/scriptaculous.js"></script>

		<!-- Design related -->
		<link rel="stylesheet" type="text/css" href="{$templateDir}/style.css" />
		<script type="text/javascript" src="{$templateDir}/scripts.js"></script>

	</head> 		
	<body>
		{$bodyTopStuff}
