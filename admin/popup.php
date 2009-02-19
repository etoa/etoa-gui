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
	// 	Dateiname: notepad.php
	// 	Topic: Admin Notepad
	// 	Autor: Yanneck Boss alias Yanneck
	// 	Erstellt: 27.12.2007
	// 	Bearbeitet von: Yanneck Boss alias Yanneck
	// 	Bearbeitet am: 01.01.2008
	// 	Kommentar: 	Notepad für den Admin Modus
	//
	
	require("inc/includer.inc.php");
	
	adminHtmlHeader($s['theme']);
	
	if (eregi("^[a-z\_]+$",$page)  && strlen($page)<=50)
	{
		if (!include("content/".$page.".php"))
			cms_err_msg("Die Seite $page wurde nicht gefunden!");
	}
	else
		echo "<h1>Fehler</h1>Der Seitenname <b>".$page."</b> enth&auml;lt unerlaubte Zeichen!<br><br><a href=\"javascript:history.back();\">Zur&uuml;ck</a>";
	
	
	adminHtmlFooter();
	
	require("inc/footer.inc.php");
?>