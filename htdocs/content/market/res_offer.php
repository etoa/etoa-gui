<?php
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
	// $Author$
	// $Date$
	// $Rev$
	//
	
			// Alliance checks
			if(!isset($_POST['ressource_for_alliance']))
			{
				$_POST['ressource_for_alliance']=0;
				$for_alliance="";
			}
			elseif ($alliance_market_level>0 && !$cd_enabled)
			{
				$_POST['ressource_for_alliance']=$cu->allianceId;
				$for_alliance="f&uuml;r ein Allianzmitglied ";

				// Set cooldown
				$cd = time()+$cooldown;
				dbquery("
						UPDATE
							alliance_buildlist
						SET
							alliance_buildlist_cooldown=".$cd."
						WHERE
							alliance_buildlist_alliance_id='".$cu->allianceId."'
							AND alliance_buildlist_building_id='".ALLIANCE_MARKET_ID."';");

				$cu->alliance->buildlist->setCooldown(ALLIANCE_MARKET_ID,$cd);
			}
			else
			{
				$_POST['ressource_for_alliance']=0;
				$for_alliance="";
			}

			$ok = true;	// Checker for valid resources
			$subtracted = array(); // Resource to be subtracted from planet
			$marr = array('factor'=>MARKET_TAX); // Market report data
			$sf = "";
			$sv = "";
			foreach ($resNames as $rk => $rn)
			{
				// Convert formatted number back to integer
				$_POST['res_sell_'.$rk] = nf_back($_POST['res_sell_'.$rk]);
				if (isset($_POST['res_buy_'.$rk]))
					$_POST['res_buy_'.$rk] = nf_back($_POST['res_buy_'.$rk]);

				// Prüft ob noch immer genug Rohstoffe auf dem Planeten sind (eventueller verlust durch Kampf?)
				if (isset($_POST['res_sell_'.$rk]) && $_POST['res_sell_'.$rk] * MARKET_TAX > $cp->resources[$rk])
				{
					$ok = false;
					break;
				}

				// Save resource to be subtracted from the planet
				$subtracted[$rk] = $_POST['res_sell_'.$rk] * MARKET_TAX;

				// Build query
				$sf.= ",sell_".$rk;
				$sv.= ",'".$_POST['res_sell_'.$rk]."'";

				if (isset($_POST['res_buy_'.$rk]))
				{
					$sf.= ",buy_".$rk;
					$sv.= ",'".$_POST['res_buy_'.$rk]."'";
				}

				// Report data
				if ($_POST['res_sell_'.$rk]>0)
					$marr['sell_'.$rk]=$_POST['res_sell_'.$rk];
				if (isset($_POST['res_buy_'.$rk]) && $_POST['res_buy_'.$rk]>0)
					$marr['buy_'.$rk]=$_POST['res_buy_'.$rk];
			}

  		if($ok)
  		{
		      // Rohstoffe vom Planet abziehen
				if ($cp->subRes($subtracted))
				{

					// Angebot speichern
					$sql = "
					INSERT INTO
					market_ressource
							(user_id,
							entity_id
							".$sf.",
							for_alliance,
							`text`,
							datum)
					VALUES
							('".$cu->id."',
							'".$cp->id()."'
							".$sv.",
							'".$_POST['ressource_for_alliance']."',
							'".addslashes($_POST['ressource_text'])."',
							'".time()."');";

					if (dbquery($sql))
					{
						MarketReport::add(array(
							'user_id'=>$cu->id,
							'entity1_id'=>$cp->id,
							'content'=>$_POST['ressource_text']
							), "resadd", mysql_insert_id(), $marr);

						ok_msg("Angebot erfolgreich aufgegeben");
						return_btn();
					}
				}
				else
				{
					error_msg("Es gab ein Problem beim Reservieren der Rohstoffe!");
					return_btn();
				}
    	}
    	else
    	{
 	      error_msg("Es sind nicht mehr genügend Rohstoffe vorhanden!");
	      return_btn();
    	}

?>
