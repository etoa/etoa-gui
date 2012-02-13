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
	// 	Dateiname: events.php	
	// 	Topic: Verwaltung der Events 
	// 	Autor: Nicolas Perrenoud alias MrCage							
	// 	Erstellt: 26.04.2006
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 26.04.2006
	// 	Kommentar: 	
	//
	
	echo "<h1>Zufallsereignisse</h1>";
	
		echo "<h2>Planeten</h2>";
		if (isset($_POST['submitforceid']))	
		{
			
			if (PlanetEventHandler::doEvent(1,1,$_POST['eventid']))
			{
				echo "Das Ereignis ".$_POST['eventid']." wurde ausgelöst!<br/><br/>";
			}
			else
			{
				echo "Leider wurde kein bewhonter Planet ausgewählt, deshalb wurde kein Ereignis ausgelöst!<br/><br/>";
			}			
		}	
		
		if ($_GET['doit']==1)	
		{
			
			if (PlanetEventHandler::doEvent(1,intval($_GET['force'])))
			{
				echo "Es wurde ein Ereignis ausgelöst!<br/><br/>";
			}
			else
			{
				echo "Leider wurde kein bewhonter Planet ausgewählt, deshalb wurde kein Ereignis ausgelöst!<br/><br/>";
			}			
		}
		echo "<h3>Manuell auslösen:</h3>
		<form action=\"?page=$page\" method=\"post\">
		<input type=\"button\" value=\"Zufälliger Planet\" onclick=\"document.location='?page=$page&amp;doit=1'\" /> <br/><br/>
		<input type=\"button\" value=\"Bewohnter Planet\" onclick=\"document.location='?page=$page&amp;doit=1&amp;force=1'\" /> <br/><br/>";
		echo "<select name=\"eventid\">";
		$evts = PlanetEventHandler::getEventList();
		foreach ($evts as $e)
		{
			echo "<option value=\"".$e."\">".$e."</option>";
		}
		echo "</select> <input type=\"submit\" value=\"Ereignis forcieren\" name=\"submitforceid\" /></form>";

		$cfg = Config::getInstance();
		echo "<h3>Statistik</h3>
		Bei jedem regulären Update werden ".RANDOM_EVENTS_PER_UPDATE." Events ausgelöst.<br/>
		Hits: ".$cfg->get("random_event_hits")."<br/>
		Misses: ".$cfg->get("random_event_misses")."<br/>";		
	
	
?>