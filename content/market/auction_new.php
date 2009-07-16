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

	$offeredRes = array(
	$_POST['auction_sell_metal']*MARKET_TAX,
	$_POST['auction_sell_crystal']*MARKET_TAX,
	$_POST['auction_sell_plastic']*MARKET_TAX,
	$_POST['auction_sell_fuel']*MARKET_TAX,
	$_POST['auction_sell_food']*MARKET_TAX,
	);

	// Prüft ob Rohstoffe noch vorhanden sind (eventueller verlust durch Kampf?)
	if ($cp->checkRes($offeredRes))
	{

        // Rohstoffe + Taxe vom Planetenkonto abziehen
		$cp->subRes($offeredRes);

        // Angebot speichern
        dbquery("
        INSERT INTO
			market_auction
            (
			user_id,
            entity_id,
            date_start,
            date_end,
            sell_0,
            sell_1,
            sell_2,
            sell_3,
            sell_4,
            `text`,
            currency_0,
            currency_1,
            currency_2,
            currency_3,
            currency_4,
            buyable)
        VALUES
            ('".$cu->id."',
            '".$cp->id()."',
            '".time()."',
            '".$auction_end_time."',
            '".$_POST['auction_sell_metal']."',
            '".$_POST['auction_sell_crystal']."',
            '".$_POST['auction_sell_plastic']."',
            '".$_POST['auction_sell_fuel']."',
            '".$_POST['auction_sell_food']."',
            '".addslashes($_POST['auction_text'])."',
            '".(isset($_POST['auction_buy_metal'])?$_POST['auction_buy_metal']:'')."',
            '".(isset($_POST['auction_buy_crystal'])?$_POST['auction_buy_crystal']:'')."',
            '".(isset($_POST['auction_buy_plastic'])?$_POST['auction_buy_plastic']:'')."',
            '".(isset($_POST['auction_buy_fuel'])?$_POST['auction_buy_fuel']:'')."',
            '".(isset($_POST['auction_buy_food'])?$_POST['auction_buy_food']:'')."',
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
