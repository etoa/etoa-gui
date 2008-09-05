#include <mysql++/mysql++.h>
#include <vector>
#include <math.h>
#include <ctime>
#include <algorithm>
#include "../config/ConfigHandler.h"
#include "../functions/Functions.h"

#include "BattleHandler.h"

#include "DivisionHandler.h"
#include "FightObjectHandler.h"
#include "UserHandler.h"
		 
void BattleHandler::battle()
{
	Config &config = Config::instance();
	mysqlpp::Query query = con_->query();
	
	std::time_t time = std::time(0);
	
    // BEGIN SKRIPT //
	alliancesHaveWar = 0;
	specialShipBonusAntrax = 0;
	specialShipBonusForsteal = 0;
	specialShipBonusBuildDestroy = 0;
	specialShipBonusAntraxFood = 0;
	specialShipBonusDeactivade = 0;

	//Erzeugen Object für User
	DivisionHandler *attacker = new DivisionHandler();	
	DivisionHandler *defender = new DivisionHandler();

	//
	// Flotten Daten
	attacker->loadFleet((int)fleet_["id"]);
	attacker->initValues();

	//Vertidiung Daten
	defender->loadShips((int)fleet_["entity_to"]);
	defender->loadSupport((int)fleet_["entity_to"]);
	defender->loadDefense((int)fleet_["entity_to"]);
	defender->initValues();

   	// Kampf abbrechen falls User gleich
    if (attacker->allianceId==defender->allianceId && attacker->allianceId!=0) {
	    msgFight = "[b]KAMPFBERICHT[/b]\nvom Planeten ";
		msgFight += functions::formatCoords((int)fleet_["entity_to"],0);
		msgFight += "\n[b]Zeit:[/b] ";
		msgFight += functions::formatTime((int)fleet_["landtime"]);
		msgFight += "\n\n";
	    msgFight += "[b]Angreifer:[/b] ";
		msgFight += attacker->getNicks();
		msgFight += "\n";
	    msgFight += "[b]Verteidiger:[/b] ";
		msgFight += defender->getNicks((int)fleet_["entity_to"]);
		msgFight += "\n\n";
    	msgFight += "Der Kampf wurde abgebrochen da Angreifer und Verteidiger demselben Imperium angehören!";
    	
			returnV = 4;
			bstat = "Unentschieden";
			bstat2 = "Unentschieden";
			returnFleet = true;
  	} 
  	
  	// Kampf abbrechen und Flotte zum Startplanet schicken wenn Kampfsperre aktiv ist
  	else if ((int)config.nget("battleban",0)!=0 && (int)config.nget("battleban",1)<=time && (int)config.nget("battleban",2)>time) {
  		msgFight = "[b]KAMPFBERICHT[/b]\nvom Planeten ";
		msgFight += functions::formatCoords((int)fleet_["entity_to"],0);
		msgFight += "\n[b]Zeit:[/b] ";
		msgFight += functions::formatTime((int)fleet_["landtime"]);
		msgFight += "\n\n";
	    msgFight += "[b]Angreifer:[/b] ";
		msgFight += attacker->getNicks();
		msgFight += "\n";
	    msgFight += "[b]Verteidiger:[/b] ";
		msgFight += defender->getNicks((int)fleet_["entity_to"]);
		msgFight += "\n\n";
    	msg += config.get("battleban_arrival_text",1);
    	
			returnV = 4;
			bstat = "Unentschieden";
			bstat2 = "Unentschieden";
			returnFleet =true;
	}
	else {

		// Prüft, ob Krieg herrscht
		if(attacker->allianceId!=0 && defender->allianceId!=0)
		{
			query << "SELECT ";
			query << "	alliance_bnd_id ";
			query << "FROM ";
			query << "	alliance_bnd ";
			query << "WHERE ";
			query << "	(alliance_bnd_alliance_id1='" << attacker->allianceId << "' ";
			query << "	AND alliance_bnd_alliance_id2='" << defender->allianceId << "') ";
			query << "OR ";
			query << "	(alliance_bnd_alliance_id1='" << defender->allianceId << "' ";
			query << "	AND alliance_bnd_alliance_id2='" << attacker->allianceId << "') ";
			query << "	AND alliance_bnd_level='3';";
			mysqlpp::Result warCheckRes = query.store();
			query.reset();

			if (warCheckRes)
			{
				int warCheckSize = warCheckRes.size();
				
				if (warCheckSize > 0)
				{
					alliancesHaveWar = 1;
				}
			}
		}

		msg = "[b]KAMPFBERICHT[/b]\nvom Planeten ";
		msg += functions::formatCoords((int)fleet_["entity_to"],0);
		msg += "\n[b]Zeit:[/b] ";
		msg += functions::formatTime((int)fleet_["landtime"]);
		msg += "\n\n";
		msg += "[b]Angreifer:[/b] ";
		msg += attacker->getNicks();
		msg += "\n";
		msg += "[b]Verteidiger:[/b] ";
		msg += defender->getNicks((int)fleet_["entity_to"]);
		msg += "\n\n";
		msg += "[b]ANGREIFENDE FLOTTE:[/b]\n";
		msg += attacker->getObjects(1);
		msg += "[b]VERTEIDIGENDE FLOTTE:[/b]\n";
		msg += defender->getObjects(1);
		msg += "[b]VERTEIDIGUNG:[/b]\n";
		msg += defender->getObjects(0);
        msg += "[b]DATEN DES ANGREIFERS[/b]\n[b]Schild (";
		msg += functions::nf(functions::d2s(std::max(1.0,attacker->initShield/attacker->shield) * 100));
		msg += "%):[/b] ";
		msg += functions::nf(functions::d2s(attacker->initShield));
		msg += "\n[b]Struktur (";
		msg += functions::nf(functions::d2s(std::max(1.0,attacker->initStructure/attacker->structure) * 100));
		msg += "%):[/b] ";
		msg += functions::nf(functions::d2s(attacker->initStructure));
		msg += "\n[b]Waffen (";
		msg += functions::nf(functions::d2s(std::max(1.0,attacker->initWeapon/attacker->weapon) * 100));
		msg += "%):[/b] ";
		msg += functions::nf(functions::d2s(attacker->initWeapon));
		msg += "\n[b]Einheiten:[/b] ";
		msg += functions::nf(functions::d2s(attacker->initCount));
		msg +="\n\n[b]DATEN DES VERTEIDIGERS[/b]\n[b]Schild (";
		msg += functions::nf(functions::d2s(std::max(1.0,defender->initShield/defender->shield) * 100));
		msg += "%):[/b] ";
		msg += functions::nf(functions::d2s(defender->initShield));
		msg += "\n[b]Struktur (";
		msg += functions::nf(functions::d2s(std::max(1.0,defender->initStructure/defender->structure) * 100));
		msg += "%):[/b] ";
		msg += functions::nf(functions::d2s(defender->initStructure));
		msg += "\n[b]Waffen (";
		msg += functions::nf(functions::d2s(std::max(1.0,defender->initWeapon/defender->weapon) * 100));
		msg += "%):[/b] ";
		msg += functions::nf(functions::d2s(defender->initWeapon));
		msg += "\n[b]Einheiten:[/b] ";
		msg += functions::nf(functions::d2s(defender->initCount));
        msg +="\n\n";





		//
		//Kampf Daten errechnen
		//
		//init... = wert vor dem kampf (wird nicht verändert) und c... aktueller Wert

		//Anzahl Schiffe
        attacker->cCount = std::max(attacker->initCount,(double)0);
        defender->cCount = std::max(defender->initCount,(double)0);
		
		attacker->cWeapon = attacker->initWeapon;
		defender->cWeapon = defender->initWeapon;

		//Schild + Strukturstärke
        attacker->initStructureShield = std::max((attacker->initShield + attacker->initStructure),(double)0);
        defender->initStructureShield = std::max((defender->initShield + defender->initStructure),(double)0);
		
		attacker->cStructureShield = attacker->initStructureShield;
		defender->cStructureShield = defender->initStructureShield;

		//
		//Der Kampf!
		//
        for (int bx = 0; bx < config.nget("battle_rounds",0); bx++) {

            runde = bx + 1;
			
			
            attacker->cStructureShield -= defender->cWeapon;
			attacker->cStructureShield = std::max((double)0,attacker->cStructureShield);
			defender->cStructureShield -= attacker->cWeapon;
			defender->cStructureShield = std::max((double)0,defender->cStructureShield);
			attacker->percentage = attacker->cStructureShield / attacker->initStructureShield;
			defender->percentage = defender->cStructureShield / defender->initStructureShield;
			
			
            msg += "\n";
			msg += functions::d2s(runde);
			msg += ": ";
			msg += functions::d2s(attacker->cCount);
			msg += " Einheiten des Angreifes schiessen mit einer St&auml;rke von ";
			msg += functions::nf(functions::d2s(attacker->cWeapon));
			msg += " auf den Verteidiger. Der Verteidiger hat danach noch ";
			msg += functions::nf(functions::d2s(defender->cStructureShield));
			msg += " Struktur- und Schildpunkte\n" ;
			
            msg += "\n";
			msg += functions::d2s(runde);
			msg += ": ";
			msg += functions::d2s(defender->cCount);
			msg += " Einheiten des Verteidigers schiessen mit einer St&auml;rke von ";
			msg += functions::nf(functions::d2s(defender->cWeapon));
			msg += " auf den Angreifer. Der Angreifer hat danach noch ";
			msg += functions::nf(functions::d2s(attacker->cStructureShield));
			msg += " Struktur- und Schildpunkte\n" ;


			
			attacker->updateValues();
			defender->updateValues();
			
			
            if (attacker->cHealCount > 0 && attacker->cCount > 0) {
                attacker->cStructureShield += attacker->cHealPoints;
                if (attacker->cStructureShield > attacker->initStructureShield)
                    attacker->cStructureShield = attacker->initStructureShield;

                msg += "\n";
				msg += functions::d2s(runde);
				msg += ": ";
				msg += functions::d2s(attacker->cHealCount),
				msg += " Einheiten des Angreifes heilen ";
				msg += functions::nf(functions::d2s(attacker->cHealPoints));
				msg += " Struktur- und Schildpunkte. Der Angreifer hat danach wieder ";
				msg += functions::nf(functions::d2s(attacker->cStructureShield));
				msg += " Struktur- und Schildpunkte\n" ;
				
				attacker->updateValues();
            }

            if (defender->cHealCount > 0 && defender->cCount > 0) {
                defender->cStructureShield += defender->cHealPoints;
                if (defender->cStructureShield > defender->initStructureShield)
                    defender->cStructureShield = defender->initStructureShield;
					
                msg += "\n";
				msg += functions::d2s(runde);
				msg += ": ";
				msg += functions::d2s(defender->cHealCount);
				msg += " Einheiten des Verteidigers heilen ";
				msg += functions::nf(functions::d2s(defender->cHealPoints));
				msg += " Struktur- und Schildpunkte. Der Verteidiger hat danach wieder ";
				msg += functions::nf(functions::d2s(defender->cStructureShield));
				msg += " Struktur- und Schildpunkte\n";
				
				defender->updateValues();
            }
            msg += "\n";
			
            if (attacker->cStructureShield <= 0 || defender->cStructureShield <= 0)
                break;
        }

        msg += "Der Kampf dauerte ";
		msg += functions::d2s(runde);
		msg += " Runden!\n\n";

		//
		//Daten nach dem Kampf
		//

		//
		//überlebende Schiffe errechnen
		//
		attacker->loseFleet.resize(5);
		defender->loseFleet.resize(5);
		
		wf.resize(3);

		attacker->updateValuesEnd(wf);
		defender->updateValuesEnd(wf);
		

		//Erfahrung für die Spezialschiffe errechnen
        attacker->newExpInit = round((defender->loseFleet[0] + defender->loseFleet[1] + defender->loseFleet[2] + defender->loseFleet[3] + defender->loseFleet[4]) / 100000);
		defender->newExpInit = round((attacker->loseFleet[0] + attacker->loseFleet[1] + attacker->loseFleet[2] + attacker->loseFleet[3] + attacker->loseFleet[4]) / 100000);


		//Das entstandene Trümmerfeld erstellen/hochladen
        query << "UPDATE ";
		query << "	planets ";
		query << "SET ";
		query << "	planet_wf_metal=planet_wf_metal+'" << abs(wf[0]) << "', ";
		query << "	planet_wf_crystal=planet_wf_crystal+'" << abs(wf[1]) << "', ";
		query << "	planet_wf_plastic=planet_wf_plastic+'" << abs(wf[2]) << "' ";
		query << "WHERE ";
		query << "	id='" << fleet_["entity_to"] << "';";
		query.store();
		query.reset(),

        //Löscht die flotte und setzt alle schiffe & def zurück (es wird wieder eingetragen!)
        query << "UPDATE ";
		query << "	deflist ";
		query << "SET ";
		query << "	deflist_count='0' ";
		query << "WHERE ";
		query << "	deflist_entity_id='" << fleet_["entity_to"] << "';";
		query.store();
		query.reset();
		
        query << "UPDATE ";
		query << "	shiplist ";
		query << "SET ";
		query << "	shiplist_count='0' ";
		query << "WHERE ";
		query << "	shiplist_entity_id='" << fleet_["entity_to"] << "';";
		query.store();
		query.reset();
		
        query << "DELETE FROM ";
		query << "	fleet_ships ";
		query << "WHERE ";
		query << "	fs_fleet_id='" << fleet_["id"] << "';";
		query.store();
		query.reset();
		
		query << "SELECT ";
		query << "	fleet.id ";
		query << "FROM ";
		query << "	fleet_ships ";
		query << "INNER JOIN ";
		query << "	fleet ";
		query << "ON ";
		query << "	fs_fleet_id=fleet.id ";
		query << "	AND fleet.leader_id='" << fleet_["id"] << "';";
		mysqlpp::Result fsRes = query.store();
		query.reset();
		
		if (fsRes) {
			int fsSize = fsRes.size();
			
			if (fsSize > 0) {
				mysqlpp::Row fsRow;
				
				for (mysqlpp::Row::size_type i = 0; i<fsSize; i++) {
					fsRow = fsRes.at(i);
						
					query << "DELETE FROM ";
					query << "	fleet_ships ";
					query << "WHERE ";
					query << "	fs_fleet_id='" << fsRow["id"] << "';";
					query.store();
					query.reset();
				}
			}
		}

		//
		//Auswertung
		//
		//Schiffe/Def wiederherstellen
		//
		std::vector< FightObjectHandler >::iterator it;
		
		std::string attMsg = "[b]Zustand nach dem Kampf:[/b]\n\n";
		attMsg += "[b]ANGREIFENDE FLOTTE:[/b]\n";
		attMsg += attacker->getObjects(1);
		if (attacker->saveObjects()) {
			attMsg += "Gewonnene EXP: ";
			attMsg += functions::nf(functions::d2s(attacker->newExpInit));
			attMsg += "\n\n";
		}
		attMsg += "[b]VERTEIDIGENDE FLOTTE:[/b]\n";
		attMsg += defender->getObjects(1);
		if (defender->saveObjects()) {
			attMsg += "\nGewonnene EXP: ";
			attMsg += functions::nf(functions::d2s(defender->newExpInit));
			attMsg += "\n\n";
		}
		attMsg += "[b]VERTEIDIGUNG:[/b]\n";
		attMsg += defender->getObjects(0,true);

		//
		//Der Angreifer hat gewonnen!
		//

		if (defender->cCount == 0 && attacker->cCount > 0) {
			returnV = 1;
			raidR.resize(5);
			raidRToShip.resize(5);

			msg += "Der Angreifer hat den Kampf gewonnen!\n\n";

            //Kapazität der überlebenden Schiffe rechnen

            resRaidFactor = 0.5;
          /*  if (dontSteal != 1 && shipSteal!=50)
                resRaidFactor = shipSteal;*/

			//Rohstoffe vom gegnerischen Planeten abfragen
			query << "SELECT ";
			query << "	planet_res_metal, ";
			query << "	planet_res_crystal, ";
			query << "	planet_res_plastic, ";
			query << "	planet_res_fuel, ";
			query << "	planet_res_food ";
			query << "FROM ";
			query << "	planets ";
			query << "WHERE ";
			query << "	id='" << fleet_["entity_to"] << "';";
			mysqlpp::Result rpRes = query.store();
			query.reset();
			
			if (rpRes) {
				int rpSize = rpRes.size();
				
				if (rpSize > 0) {
					mysqlpp::Row rpRow = rpRes.at(0);
					
					raidR[0] = rpRow["planet_res_metal"]	* resRaidFactor;
					raidR[1] = rpRow["planet_res_crystal"]	* resRaidFactor;
					raidR[2] = rpRow["planet_res_plastic"]	* resRaidFactor;
					raidR[3] = rpRow["planet_res_fuel"]		* resRaidFactor;
					raidR[4] = rpRow["planet_res_food"]		* resRaidFactor;
				}
			}

			double sum = raidR[0] + raidR[1] + raidR[2] + raidR[3] + raidR[4];
			
            for (int rcnt = 0; rcnt < raidR.size(); rcnt++) {
                if (attacker->capa <= sum)
                    raidRToShip[rcnt] += round(raidR[rcnt] * attacker->capa / sum);
                else
                    raidRToShip[rcnt] += round(raidR[rcnt]);
            }

			std::map< int,UserHandler>::iterator at;
			std::map< int,double >::iterator ft;
			for ( at = attacker->users.begin() ; at != attacker->users.end(); at++ ) {
				for (ft = (*at).second.fleetCapa.begin(); ft != (*at).second.fleetCapa.end(); ft++) {
					double percent = (*ft).second / attacker->capa;
					query << "UPDATE ";
					query << "	fleet ";
					query << "SET ";
					query << "	res_metal=res_metal+'" << floor(raidRToShip[0] * percent)  << "', ";
					query << "	res_crystal=res_crystal+'" << floor(raidRToShip[1] * percent) << "', ";
					query << "	res_plastic=res_plastic+'" << floor(raidRToShip[2] * percent) << "', ";
					query << "	res_fuel=res_fuel+'" << floor(raidRToShip[3] * percent) << "', ";
					query << "	res_food=res_food+'" << floor(raidRToShip[4] * percent) << "' ";
					query << "WHERE ";
					query << "	id='" <<(*ft).first << "';";	
					query.store();
					query.reset();
				}

				double percent2 = (*at).second.capa / attacker->capa;
				//Erbeutete Rohstoffsumme speichern
				query << "UPDATE ";
				query << "	users ";
				query << "SET ";
				query << "	user_res_from_raid=user_res_from_raid+'" << sum * percent2 << "' ";
				query << "WHERE ";
				query << "	user_id='" << (*at).second.userId << "';";
				query.store();
				query.reset();			
			}

			query << "UPDATE ";
			query << "	planets ";
			query << "SET ";
			query << "	planet_res_metal=planet_res_metal-'" << raidRToShip[0] << "', ";
			query << "	planet_res_crystal=planet_res_crystal-'" << raidRToShip[1] << "', ";
			query << "	planet_res_plastic=planet_res_plastic-'" << raidRToShip[2] << "', ";
			query << "	planet_res_fuel=planet_res_fuel-'" << raidRToShip[3] << "', ";
			query << "	planet_res_food=planet_res_food-'" << raidRToShip[4] << "' ";
			query << "WHERE ";
			query << "	id='" << fleet_["entity_to"] << "';";
			query.store();
			query.reset();

            msg += "[b]BEUTE:[/b]\n";
            msg += "Titan: ";
			msg += functions::nf(functions::d2s(raidRToShip[0]));
			msg += "\nSilizium: ";
			msg += functions::nf(functions::d2s(raidRToShip[1]));
            msg += "\nPVC: ";
			msg += functions::nf(functions::d2s(raidRToShip[2]));
			msg += "\nTritium: ";
			msg += functions::nf(functions::d2s(raidRToShip[3]));
            msg += "\nNahrung: ";
			msg += functions::nf(functions::d2s(raidRToShip[4]));
            msg += "\n\n\n";
		}
	
				


		//
		//Der Verteidiger hat gewonnen
		//
		else if (attacker->cCount==0 && defender->cCount>0) {
			returnV = 2;
			msg += "Der Verteidiger hat den Kampf gewonnen!\n\n";

			//löscht die angreiffende flotte
			query << "DELETE FROM ";
			query << "	fleet ";
			query << "WHERE ";
			query << "	id='" << fleet_["id"] << "' ";
			query << "	OR leader_id='" << fleet_["id"] << "';";
			query.store();
			query.reset();
		}

		//
		//Der Kampf endete unentschieden
		//
		else {

			//
			//	Unentschieden, beide Flotten wurden zerstört
			//
			if (attacker->cCount==0 && defender->cCount==0) {
        		returnV = 3;
				msg += "Der Kampf endete unentschieden, da sowohl die Einheiten des Angreifes als auch die Einheiten des Verteidigers alle zerstört wurden!\n\n";
            
				//löscht die angreiffende flotte
				query << "DELETE FROM ";
				query << "	fleet ";
				query << "WHERE ";
				query << "	id='" << fleet_["id"] << "' ";
				query << "	OR leader_id='" << fleet_["id"] << "';";
				query.store();
				query.reset();
			}

			//
			//	Unentschieden, beide Flotten haben überlebt
			//
			else {
				returnV = 4;

				msg += "Der Kampf endete unentschieden und die Flotten zogen sich zurück!\n\n";
			}
		}

		msg += "[b]TR&Uuml;MMERFELD:[/b]\n";
		msg += "Titan: ";
		msg += functions::nf(functions::d2s(abs(wf[0])));
		msg += "\nSilizium: ";
		msg += functions::nf(functions::d2s(abs(wf[1])));
		msg += "\nPVC: ";
		msg += functions::nf(functions::d2s(abs(wf[2])));
		msg += "\n\n\n";
		
		msg += attMsg;

        int perc =  config.nget("def_restore_percent",0)*100;
        msg += "\n";
		msg += functions::d2s(perc);
		msg += "% der Verteidigungsanlagen werden repariert!";
/*
        //Log schreiben
        query << "INSERT INTO ";
		query << "	logs_battle ";
		query << "(";
		query << "	logs_battle_user1_id, ";
		query << "	logs_battle_user2_id, ";
		query << "	logs_battle_user1_alliance_id, ";
		query << "	logs_battle_user1_alliance_tag, ";
		query << "	logs_battle_user1_alliance_name, ";
		query << "	logs_battle_user2_alliance_id, ";
		query << "	logs_battle_user2_alliance_tag, ";
		query << "	logs_battle_user2_alliance_name, ";
		query << "	logs_battle_alliances_have_war, ";
		query << "	logs_battle_planet_id, ";
		query << "	logs_battle_fleet_action, ";
		query << "	logs_battle_result, ";
		query << "	logs_battle_user1_ships_cnt, ";
		query << "	logs_battle_user2_ships_cnt, ";
		query << "	logs_battle_user2_defs_cnt, ";
		query << "	logs_battle_user1_weapon, ";
		query << "	logs_battle_user1_shield, ";
		query << "	logs_battle_user1_structure, ";
		query << "	logs_battle_user1_weapon_bonus, ";
		query << "	logs_battle_user1_shield_bonus, ";
		query << "	logs_battle_user1_structure_bonus, ";
		query << "	logs_battle_user2_weapon, ";
		query << "	logs_battle_user2_shield, ";
		query << "	logs_battle_user2_structure, ";
		query << "	logs_battle_user2_weapon_bonus, ";
		query << "	logs_battle_user2_shield_bonus, ";
		query << "	logs_battle_user2_structure_bonus, ";
		query << "	logs_battle_user1_win_exp, ";
		query << "	logs_battle_user2_win_exp, ";
		query << "	logs_battle_user1_win_metal, ";
		query << "	logs_battle_user1_win_crystal, ";
		query << "	logs_battle_user1_win_pvc, ";
		query << "	logs_battle_user1_win_tritium, ";
		query << "	logs_battle_user1_win_food, ";
		query << "	logs_battle_tf_metal, ";
		query << "	logs_battle_tf_crystal, ";
		query << "	logs_battle_tf_pvc, ";
		query << "	logs_battle_fight, ";
		query << "	logs_battle_time, ";
		query << "	logs_battle_fleet_landtime ";
		query << ")";
		query << "VALUES";
		query << "(";
		query << "	'" << attacker->userId << "', ";
		query << "	'" << defender->userId << "', ";
		query << "	'" << attacker->allianceId << "', ";
		query << "	'" << attacker->allianceTag << "', ";
		query << "	'" << attacker->allianceName << "', ";
		query << "	'" << defender->allianceId << "', ";
		query << "	'" << defender->allianceTag << "', ";
		query << "	'" << defender->allianceName <<"', ";
		query << "	'" << alliancesHaveWar << "', ";
		query << "	'" << fleet_["fleet_entity_to"] << "', ";
		query << "	'" << fleet_["fleet_action"] << "', ";
		query << "	'" << returnV << "', ";
		query << "	'" << attacker->count << "', ";
		query << "	'" << defender->count << "', ";
		query << "	'" << count_dd << "', ";
		query << "	'" << attacker->initWeapon << "', ";
		query << "	'" << $shield_a << "', ";
		query << "	'" << structure_a << "', ";
		query << "	'" << (attacker->weaponTech * 100) << "', ";
		query << "	'" << (attacker->shieldTech * 100) << "', ";
		query << "	'" << (attacker->structureTech * 100) << "', ";
		query << "	'" << defender->initWeapon << "', ";
		query << "	'" << shield_d <<"', ";
		query << "	'" << structure_d << "', ";
		query << "	'" << (defender->weaponTech * 100) << "', ";
		query << "	'" << (defender->shieldTech * 100) << "', ";
		query << "	'" << (defender->structureTech * 100) << "', ";
		query << "	'" << attacker->newExpInit << "', ";
		query << "	'" << defender->newExpInit << "', ";
		query << "	'" << raidRToShip[0] << "', ";
		query << "	'" << raidRToShip[1] << "', ";
		query << "	'" << raidRToShip[2] << "', ";
		query << "	'" << raidRToShip[3] << "', ";
		query << "	'" << raidRToShip[4] << "', ";
		query << "	'" << wf[0] << "', ";
		query << "	'" << wf[1] << "', ";
		query << "	'" << wf[2] << "', ";
		query << "	'" << msg << "', ";
		query << "	'" << std::time(0) << "', ";
		query << "	'" << fleet_["fleet_landtime"] << "');";
		query.store();
		query.reset();*/

		switch (returnV)
		{
			case 1:	//angreifer hat gewonnen
				bstat = "Gewonnen";
				bstat2 = "Verloren";
				returnFleet = true;
				//Ranking::addBattlePoints($user_a_id,BATTLE_POINTS_A_W,"Angriff gegen ".$user_d_id);
				//Ranking::addBattlePoints($user_d_id,BATTLE_POINTS_D_L,"Verteidigung gegen ".$user_a_id);
				break;
			case 2:	//agreifer hat verloren
				bstat = "Verloren";
				bstat2 = "Gewonnen";
				returnFleet = false;
				//Ranking::addBattlePoints($user_a_id,BATTLE_POINTS_A_L,"Angriff gegen ".$user_d_id);
				//Ranking::addBattlePoints($user_d_id,BATTLE_POINTS_D_W,"Verteidigung gegen ".$user_a_id);
				break;
			case 3:	//beide flotten sind kaputt
				bstat = "Unentschieden";
				bstat2 = "Unentschieden";
				returnFleet = false;
				//Ranking::addBattlePoints($user_a_id,BATTLE_POINTS_A_D,"Angriff gegen ".$user_d_id);
				//Ranking::addBattlePoints($user_d_id,BATTLE_POINTS_D_D,"Verteidigung gegen ".$user_a_id);
				break;
			case 4: //beide flotten haben überlebt
				bstat = "Unentschieden";
				bstat2 = "Unentschieden";
				returnFleet = true;
				//Ranking::addBattlePoints($user_a_id,BATTLE_POINTS_A_D,"Angriff gegen ".$user_d_id);
				//Ranking::addBattlePoints($user_d_id,BATTLE_POINTS_D_D,"Verteidigung gegen ".$user_a_id);
				break;
		}
		
		/** Send fight message to the fighter **/
		std::string subject1 = "Kampfbericht (";
		subject1 += bstat;
		subject1 += ")";
		std::string subject2 = "Kampfbericht (";
		subject2 += bstat2;
		subject2 += ")";
		std::map< int,UserHandler>::iterator at;
		for ( at = attacker->users.begin() ; at != attacker->users.end(); at++ ) {
			functions::sendMsg((*at).second.userId,config.idget("SHIP_WAR_MSG_CAT_ID"),subject1,msg);
		}
		if (defender->users.size()>0) {
			for ( at = defender->users.begin() ; at != defender->users.end(); at++ ) {
				functions::sendMsg((*at).second.userId,config.idget("SHIP_WAR_MSG_CAT_ID"),subject2,msg);
			}		
		}
		else {
			int userToId = functions::getUserIdByPlanet((int)fleet_["entity_to"]);
			functions::sendMsg(userToId,config.idget("SHIP_WAR_MSG_CAT_ID"),subject2,msg);
		}

		/** Add log **/
		functions::addLog(1,msg,(int)fleet_["landtime"]);

		delete attacker, defender;
	}
}
