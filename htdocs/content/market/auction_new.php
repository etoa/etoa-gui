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

	// Berechnet Endzeit
	$auction_min_time = AUCTION_MIN_DURATION * 24 * 3600;
	$auction_time_days = $_POST['auction_time_days'];
	$auction_time_hours = $_POST['auction_time_hours'];
	$auction_end_time = time() + $auction_min_time + $auction_time_days * 24 * 3600 + $auction_time_hours * 3600;
	$marr = array('factor'=>MARKET_TAX,'timestamp2'=>$auction_end_time);
	
	$ok = true;
	$sf = "";
	$sv = "";
	foreach ($resNames as $rk => $rn)
	{
		// Convert formatted number back to integer
		$_POST['auction_sell_'.$rk] = nf_back($_POST['auction_sell_'.$rk]);

		// Prüft ob noch immer genug Rohstoffe auf dem Planeten sind (eventueller verlust durch Kampf?)
		if (isset($_POST['auction_sell_'.$rk]) && $_POST['auction_sell_'.$rk] * MARKET_TAX > $cp->resources[$rk])
		{
			$ok = false;
			break;
		}

		// Save resource to be subtracted from the planet
		$subtracted[$rk] = $_POST['auction_sell_'.$rk] * MARKET_TAX;

		// Build query
		$sf.= ",sell_".$rk;
		$sv.= ",'".$_POST['auction_sell_'.$rk]."'";

		$sf.= ",currency_".$rk;
		$sv.= ",'".(isset($_POST['auction_buy_'.$rk])?$_POST['auction_buy_'.$rk]:'')."'";

		// Report data
		if ($_POST['auction_sell_'.$rk]>0)
			$marr['sell_'.$rk]=$_POST['auction_sell_'.$rk];
		if (isset($_POST['res_buy_'.$rk]) && $_POST['res_buy_'.$rk]>0)
			$marr['buy_'.$rk]=$_POST['auction_buy_'.$rk];
	}
	
	$ship_update=0;
	$ress_update=0;
	
	// Prüft ob Rohstoffe noch vorhanden sind (eventueller verlust durch Kampf?)
	if ($ok && $cp->checkRes($subtracted))
	{

        // Rohstoffe + Taxe vom Planetenkonto abziehen
		$cp->subRes($subtracted);

        // Angebot speichern
        dbquery("
        INSERT INTO
			market_auction
            (
			user_id,
            entity_id,
            date_start,
            date_end,
			text
			".$sf.",
            buyable)
        VALUES
            ('".$cu->id."',
            '".$cp->id()."',
            '".time()."',
            '".$auction_end_time."',
            '".addslashes($_POST['auction_text'])."'
			".$sv.",
            '1')");


        //Nachricht senden
		MarketReport::add(array(
			'user_id'=>$cu->id,
			'entity1_id'=>$cp->id,
			'content'=> $_POST['auction_text']
			), "auctionadd", mysql_insert_id(), $marr);

        add_log(LOG_CAT,"Der Spieler ".$cu->nick." hat folgende Rohstoffe zur versteigerung angeboten:\n\n".RES_METAL.": ".nf($_POST['auction_sell_0'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_sell_1'])."\n".RES_PLASTIC.": ".nf($_POST['auction_sell_2'])."\n".RES_FUEL.": ".nf($_POST['auction_sell_3'])."\n".RES_FOOD.": ".nf($_POST['auction_sell_4'])."\n\nAuktionsende: ".date("d.m.Y H:i",$auction_end_time)."",time());

		// todo: report

        ok_msg("Auktion erfolgreich lanciert");
        return_btn();

	}
	else
	{
	  error_msg("Die angegebenen Rohstoffe sind nicht mehr verfügbar!");
	  return_btn();
	}

?>
