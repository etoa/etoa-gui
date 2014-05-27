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
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	//

	/**
	* Sends ships on their flights
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	// BEGIN SKRIPT //

	if ($cp)
	{

		echo '<h1>Raumschiffhafen des Planeten '.$cp->name.'</h1>';
		$cp->resBox($cu->properties->smallResBox);
	
		//
		// Kampfsperre prüfen
		//
		if ($cfg->get("battleban")!=0 && $cfg->param1("battleban_time")<=time() && $cfg->param2("battleban_time")>time())
		{
			iBoxStart("Kampfsperre");
			echo 'Es ist momentan nicht m&ouml;glich andere Spieler anzugreifen. Grund: '.text2html($cfg->param1("battleban")).'<br />Die Sperre dauert vom '.date("d.m.Y",$cfg->param1("battleban_time")).' um '.date("H:i",$cfg->param1("battleban_time")).' Uhr bis am '.date("d.m.Y",$cfg->param2("battleban_time"))." um ".date("H:i",$cfg->param2("battleban_time")).' Uhr!';
			iBoxEnd();
		}
		
			if (isset($_GET['target']) && intval($_GET['target'])>0)
			{
				$_SESSION['haven']['targetId']=intval($_GET['target']);
			}
			elseif (isset($_GET['cellTarget']) && intval($_GET['cellTarget'])>0)
			{
				$_SESSION['haven']['cellTargetId']=intval($_GET['cellTarget']);
			}


			// Fleet object
			$fleet = new FleetLaunch($cp,$cu);

			$fleet->checkHaven();

				// Set vars for xajax
				$_SESSION['haven'] = Null;
				$_SESSION['haven']['fleetObj']=serialize($fleet);
				
				echo '<div id="havenContent">
				<div id="havenContentShips" style="">
				<div style="padding:20px"><img src="images/loading.gif" alt="Loading" /> Lade Daten...</div>
				</div>
				<div id="havenContentTarget" style="display:none;"></div>
				<div id="havenContentWormhole" style="display:none;"></div>
				<div id="havenContentAction" style="display:none;"></div>
				</div>';
				echo '<script type="text/javascript">xajax_havenShowShips();</script>';	
	}
?>
