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

	 		$ship_update=0;
	 		$ress_update=0;

			$_POST['auction_sell_metal'] = nf_back($_POST['auction_sell_metal']);
			$_POST['auction_sell_crystal'] = nf_back($_POST['auction_sell_crystal']);
			$_POST['auction_sell_plastic'] = nf_back($_POST['auction_sell_plastic']);
			$_POST['auction_sell_fuel'] = nf_back($_POST['auction_sell_fuel']);
			$_POST['auction_sell_food'] = nf_back($_POST['auction_sell_food']);

			// Prüft ob Rohstoffe noch vorhanden sind (eventueller verlust durch Kampf?)
      if (($_POST['auction_sell_metal']*MARKET_TAX)<=$cp->resMetal
          && ($_POST['auction_sell_crystal']*MARKET_TAX)<=$cp->resCrystal
          && ($_POST['auction_sell_plastic']*MARKET_TAX)<=$cp->resPlastic
          && ($_POST['auction_sell_fuel']*MARKET_TAX)<=$cp->resFuel
          && ($_POST['auction_sell_food']*MARKET_TAX)<=$cp->resFood)
      {

        // Rohstoffe + Taxe vom Planetenkonto abziehen
                	// TODO: use planet class
        dbquery("
        UPDATE
            planets
        SET
            planet_res_metal=planet_res_metal-".($_POST['auction_sell_metal']*MARKET_TAX).",
            planet_res_crystal=planet_res_crystal-".($_POST['auction_sell_crystal']*MARKET_TAX).",
            planet_res_plastic=planet_res_plastic-".($_POST['auction_sell_plastic']*MARKET_TAX).",
            planet_res_fuel=planet_res_fuel-".($_POST['auction_sell_fuel']*MARKET_TAX).",
            planet_res_food=planet_res_food-".($_POST['auction_sell_food']*MARKET_TAX)."
        WHERE
            id=".$cp->id()."
            AND planet_user_id=".$cu->id."");

        // Angebot speichern
        dbquery("
        INSERT INTO market_auction
            (auction_user_id,
            auction_planet_id,
            auction_cell_id,
            auction_start,
            auction_end,
            auction_sell_metal,
            auction_sell_crystal,
            auction_sell_plastic,
            auction_sell_fuel,
            auction_sell_food,
            auction_text,
            auction_currency_metal,
            auction_currency_crystal,
            auction_currency_plastic,
            auction_currency_fuel,
            auction_currency_food,
            auction_buyable)
        VALUES
            ('".$cu->id."',
            '".$cp->id()."',
            '".$cp->cellId()."',
            '".time()."',
            '".$auction_end_time."',
            '".$_POST['auction_sell_metal']."',
            '".$_POST['auction_sell_crystal']."',
            '".$_POST['auction_sell_plastic']."',
            '".$_POST['auction_sell_fuel']."',
            '".$_POST['auction_sell_food']."',
            '".addslashes($_POST['auction_text'])."',
            '".$_POST['auction_buy_metal']."',
            '".$_POST['auction_buy_crystal']."',
            '".$_POST['auction_buy_plastic']."',
            '".$_POST['auction_buy_fuel']."',
            '".$_POST['auction_buy_food']."',
            '1')");


        //Nachricht senden
        $msg = "Du hast folgende Rohstoffe zur versteigerung angeboten:\n\n";

        $msg .= "".RES_METAL.": ".nf($_POST['auction_sell_metal'])."\n";
        $msg .= "".RES_CRYSTAL.": ".nf($_POST['auction_sell_crystal'])."\n";
        $msg .= "".RES_PLASTIC.": ".nf($_POST['auction_sell_plastic'])."\n";
        $msg .= "".RES_FUEL.": ".nf($_POST['auction_sell_fuel'])."\n";
        $msg .= "".RES_FOOD.": ".nf($_POST['auction_sell_food'])."\n\n";

        $msg .= "Die Auktion endet am ".date("d.m.Y",$auction_end_time)." um ".date("H:i",$auction_end_time)." Uhr.\n\n";

        $msg .= "Das Handelsministerium";
        send_msg($cu->id,SHIP_MISC_MSG_CAT_ID,"Auktion eingetragen",$msg);

        add_log(LOG_CAT,"Der Spieler ".$cu->nick." hat folgende Rohstoffe zur versteigerung angeboten:\n\n".RES_METAL.": ".nf($_POST['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($_POST['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($_POST['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($_POST['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($_POST['auction_sell_food'])."\n\nAuktionsende: ".date("d.m.Y H:i",$auction_end_time)."",time());

        ok_msg("Auktion erfolgreich lanciert");
        return_btn();

      }
      else
      {
          error_msg("Die angegebenen Rohstoffe sind nicht mehr verfügbar!");
          return_btn();
      }

?>
