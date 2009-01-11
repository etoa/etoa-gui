#include <iomanip>
#include <iostream>

#include "BattleHandler.h"
		 
void BattleHandler::battle(Fleet* fleet, Entity* entity, Log* log)
{
	Config &config = Config::instance();
	std::time_t time = std::time(0);
	
	message->addType((int)config.idget("SHIP_WAR_MSG_CAT_ID"));

    // BEGIN SKRIPT //
	alliancesHaveWar = 0;

   	// Kampf abbrechen falls User gleich
    if (fleet->getLeaderId() > 0 && (fleet->fleetUser->getAllianceId()==entity->getUser()->getAllianceId() &&fleet->fleetUser->getAllianceId()!=0)) {
		message->addText("[b]KAMPFBERICHT[/b]",1);
		message->addText("vom Planeten ",0);
		message->addText(entity->getCoords(),1);
		message->addText("[b]Zeit:[/b] ");
		message->addText(fleet->getLandtimeString(),2);
		message->addText("[b]Angreifer:[/b] ");
		message->addText(fleet->getUserNicks(),1);
		message->addText("[b]Verteidiger:[b] ");
		message->addText(entity->getUserNicks(),2);
		message->addText("Der Kampf wurde abgebrochen da Angreifer und Verteidiger demselben Imperium angehören!");
		
		message->addSubject("Kampfbericht (Unentschieden)");
		
		this->returnV = 4;
		this->returnFleet = true;
		
		fleet->addMessageUser(message);
		entity->addMessageUser(message);
		
		log->addText("Action failed: Opponent error");
		
  	} 
	
   	// Kampf abbrechen falls User gleich
    else if (fleet->getLeaderId() == 0 && fleet->getUserId()==entity->getUserId()) {
		message->addText("[b]KAMPFBERICHT[/b]",1);
		message->addText("vom Planeten ",0);
		message->addText(entity->getCoords(),1);
		message->addText("[b]Zeit:[/b] ");
		message->addText(fleet->getLandtimeString(),2);
		message->addText("[b]Angreifer:[/b] ");
		message->addText(fleet->fleetUser->getUserNick(),1);
		message->addText("[b]Verteidiger:[b] ");
		message->addText(entity->getUser()->getUserNick(),2);
		message->addText("Der Kampf wurde abgebrochen da Angreifer und Verteidiger demselben Imperium angehören!");
		
		message->addSubject("Kampfbericht (Unentschieden)");
		
		message->addUserId(fleet->getUserId());
		
		this->returnV = 4;
		this->returnFleet = true;
		
		log->addText("Action failed: Opponent error");
  	} 
  	
  	// Kampf abbrechen und Flotte zum Startplanet schicken wenn Kampfsperre aktiv ist
  	else if ((int)config.nget("battleban",0)!=0 && (int)config.nget("battleban",1)<=time && (int)config.nget("battleban",2)>time) {
		message->addText("[b]KAMPFBERICHT[/b]",1);
		message->addText("vom Planeten ",0);
		message->addText(entity->getCoords(),1);
		message->addText("[b]Zeit:[/b] ");
		message->addText(fleet->getLandtimeString(),2);
		message->addText("[b]Angreifer:[/b] ");
		message->addText(fleet->getUserNicks(),1);
		message->addText("[b]Verteidiger:[b] ");
		message->addText(entity->getUserNicks(),2);
		message->addText(config.get("battleban_arrival_text",1));
		
		message->addSubject("Kampfbericht (Unentschieden)");
		
		this->returnV = 4;
		this->returnFleet =true;
		
		fleet->addMessageUser(message);
		entity->addMessageUser(message);
		
		log->addText("Action failed: Battleban error");
	}
	else {
	
		// Prüft, ob Krieg herrscht
		/*if(attacker->getAllianceId()!=0 && defender->getAllianceId()!=0)
		{
			query << "SELECT ";
			query << "	alliance_bnd_id ";
			query << "FROM ";
			query << "	alliance_bnd ";
			query << "WHERE ";
			query << "	(alliance_bnd_alliance_id1='" << attacker->getAllianceId() << "' ";
			query << "	AND alliance_bnd_alliance_id2='" << defender->getAllianceId() << "') ";
			query << "OR ";
			query << "	(alliance_bnd_alliance_id1='" << defender->getAllianceId() << "' ";
			query << "	AND alliance_bnd_alliance_id2='" << attacker->getAllianceId() << "') ";
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
		}*/
		message->addText("[b]KAMPFBERICHT[/b]",1);
		message->addText("vom Planeten ",0);
		message->addText(entity->getCoords(),1);
		message->addText("[b]Zeit:[/b] ");
		message->addText(fleet->getLandtimeString(),2);
		message->addText("[b]Angreifer:[/b] ");
		message->addText(fleet->getUserNicks(),1);
		message->addText("[b]Verteidiger:[/b] ");
		message->addText(entity->getUserNicks(),2);
		message->addText("[b]ANGREIFENDE FLOTTE:[/b] ",1);
		message->addText(fleet->getShipString(),1);
		message->addText("[b]VERTEIDIGENDE FLOTTE:[/b] ",1);
		message->addText(entity->getShipString(),1);
		message->addText("[b]VERTEIDIGUNG:[/b] ",1);
		message->addText(entity->getDefString(),1);
		message->addText("[b]DATEN DES ANGREIFERS:[/b] ",1);
		message->addText(fleet->getShieldString(false),1);
		message->addText(fleet->getStructureString(false),1);
		message->addText(fleet->getWeaponString(false),1);
		message->addText(fleet->getCountString(false),2);
		message->addText("[b]DATEN DES VERTEIDIGERS:[/b] ",1);
		message->addText(entity->getShieldString(false),1);
		message->addText(entity->getStructureString(false),1);
		message->addText(entity->getWeaponString(false),1);
		message->addText(entity->getCountString(false),3);
		
		//
		//Kampf Daten errechnen
		//
		//init... = wert vor dem kampf (wird nicht verändert) und c... aktueller Wert

		//Schild + Strukturstärke
        double initAttStructureShield = fleet->getStructShield(true);
        double initDefStructureShield = entity->getStructShield(true);
		
		double cAttStructureShield = initAttStructureShield;
		double cDefStructureShield = initDefStructureShield;
		
		//
		//Der Kampf!
		//
        for (int bx = 0; bx < config.nget("battle_rounds",0); bx++) {

           this->runde = bx + 1;
			
            cAttStructureShield -= entity->getWeapon(true);
			cDefStructureShield -= fleet->getWeapon(true);
			
			cAttStructureShield = std::max(0.0,cAttStructureShield);
			cDefStructureShield = std::max(0.0,cDefStructureShield);
			
			message->addText(functions::d2s(runde));
			message->addText(": ");
			message->addText(fleet->getCountString());
			message->addText(" Einheiten des Angreifes schiessen mit einer St&auml;rke von ");
			message->addText(fleet->getWeaponString());
			message->addText(" auf den Verteidiger. Der Verteidiger hat danach noch ");
			message->addText(functions::nf(functions::d2s(cDefStructureShield)));
			message->addText(" Struktur- und Schildpunkte",2);
			
			message->addText(functions::d2s(this->runde));
			message->addText(": ");
			message->addText(entity->getCountString());
			message->addText(" Einheiten des Verteidigers schiessen mit einer St&auml;rke von ");
			message->addText(entity->getWeaponString());
			message->addText(" auf den Angreifer. Der Angreifer hat danach noch ");
			message->addText(functions::nf(functions::d2s(cAttStructureShield)));
			message->addText(" Struktur- und Schildpunkte",2);
			
			fleet->setPercentSurvive(cAttStructureShield/initAttStructureShield,true);
			entity->setPercentSurvive(cDefStructureShield/initDefStructureShield,true);
						
            if (fleet->getHeal() > 0) {
                cAttStructureShield += fleet->getHeal();
                if (cAttStructureShield > initAttStructureShield)
                    cAttStructureShield = initAttStructureShield;

				message->addText(functions::d2s(runde));
				message->addText(": ");
				message->addText(functions::d2s(fleet->getHealCount()));
				message->addText(" Einheiten des Angreifes heilen ");
				message->addText(functions::d2s(fleet->getHeal()));
				message->addText(" Struktur- und Schildpunkte. Der Angreifer hat danach wieder ");
				
				fleet->setPercentSurvive(cAttStructureShield/initAttStructureShield,true);
				
				message->addText(entity->getStructureShieldString());
				message->addText(" Struktur- und Schildpunkte",1);
            }
			
            if (entity->getHeal() > 0) {
                cDefStructureShield += entity->getHeal();
                if (cDefStructureShield > initDefStructureShield)
                    cDefStructureShield = initDefStructureShield;

				message->addText(functions::d2s(runde));
				message->addText(": ");
				message->addText(functions::d2s(entity->getHealCount()));
				message->addText(" Einheiten des Verteidigers heilen ");
				message->addText(functions::d2s(entity->getHeal()));
				message->addText(" Struktur- und Schildpunkte. Der Angreifer hat danach wieder ");
				
				entity->setPercentSurvive(cDefStructureShield/initDefStructureShield,true);
				
				message->addText(entity->getStructureShieldString());
				message->addText(" Struktur- und Schildpunkte",1);
            }
			
			message->addText("",1);
            if (cAttStructureShield <= 0 || cDefStructureShield <= 0)
                break;
        }
		
		message->addText("Der Kampf dauerte ");
		message->addText(functions::d2s(runde));
		message->addText(" Runden!",2);
		
		
		//
		//Daten nach dem Kampf
		//

		//
		//überlebende Schiffe errechnen
		//
		
		//Erfahrung für die Spezialschiffe errechnen
        fleet->addExp(entity->getExp() / 100000);
        entity->addExp(fleet->getExp() / 100000);


		//Das entstandene Trümmerfeld erstellen/hochladen
		entity->addWfMetal(fleet->getWfMetal(true) + entity->getObjectWfMetal(true));
		entity->addWfCrystal(fleet->getWfCrystal(true) + entity->getObjectWfCrystal(true));
		entity->addWfPlastic(fleet->getWfPlastic(true) + entity->getObjectWfPlastic(true));

		//
		//Der Angreifer hat gewonnen!
		//
		
		if (cDefStructureShield == 0 && cAttStructureShield > 0) {
			this->returnV = 1;
			
			message->addText("Der Angreifer hat den Kampf gewonnen!");
			
			entity->removeResMetal(fleet->addMetal(entity->getResMetal()*fleet->getBountyBonus(),true));
			entity->removeResCrystal(fleet->addCrystal(entity->getResCrystal()*fleet->getBountyBonus(),true));
			entity->removeResPlastic(fleet->addPlastic(entity->getResPlastic()*fleet->getBountyBonus(),true));
			entity->removeResFuel(fleet->addFuel(entity->getResFuel()*fleet->getBountyBonus(),true));
			entity->removeResFood(fleet->addFood(entity->getResFood()*fleet->getBountyBonus(),true));

				/*double percent2 = (*at).second.capa / attacker->capa;
				//Erbeutete Rohstoffsumme speichern
				query << "UPDATE ";
				query << "	users ";
				query << "SET ";
				query << "	user_res_from_raid=user_res_from_raid+'" << sum * percent2 << "' ";
				query << "WHERE ";
				query << "	user_id='" << (*at).second.userId << "';";
				query.store();
				query.reset();*/
				
			message->addText(fleet->getResCollectedString(true,"Beute"),2);
		}
		
		
		//
		//Der Verteidiger hat gewonnen
		//
		else if (cAttStructureShield==0 && cDefStructureShield>0) {
			this->returnV = 2;
			message->addText("Der Verteidiger hat den Kampf gewonnen!",3);
		}

		//
		//Der Kampf endete unentschieden
		//
		else {

			//
			//	Unentschieden, beide Flotten wurden zerstört
			//
			if (cAttStructureShield==0 && cDefStructureShield==0) {
        		this->returnV = 3;
				message->addText("Der Kampf endete unentschieden, da sowohl die Einheiten des Angreifes als auch die Einheiten des Verteidigers alle zerstört wurden!",3);
			}

			//
			//	Unentschieden, beide Flotten haben überlebt
			//
			else {
				this->returnV = 4;

				message->addText("Der Kampf endete unentschieden und die Flotten zogen sich zurück!",1);
			}
		}

		message->addText("[b]TR&Uuml;MMERFELD:[/b]",1);
		message->addText("Titan: ");
		message->addText(functions::nf(functions::d2s(entity->getAddedWfMetal())),1);
		message->addText("Silizium: ");
		message->addText(functions::nf(functions::d2s(entity->getAddedWfCrystal())),1);
		message->addText("PVC: ");
		message->addText(functions::nf(functions::d2s(entity->getAddedWfPlastic())),3);
		
		//
		//Auswertung
		//
		
		message->addText("[b]Zustand nach dem Kampf:[/b]",2);
		message->addText("[b]ANGREIFENDE FLOTTE:[/b]",1);
		message->addText(fleet->getShipString(),1);
		if (fleet->getAddedExp()>=0) {
			message->addText("Gewonnene EXP: ");
			message->addText(functions::nf(functions::d2s(fleet->getAddedExp())),2);
		}
		
		message->addText("",1);
		message->addText("[b]VERTEIDIGENDE FLOTTE:[/b]",1);
		message->addText(entity->getShipString(),1);
		if (entity->getAddedExp()>=0) {
			message->addText("Gewonnene EXP: ");
			message->addText(functions::nf(functions::d2s(entity->getAddedExp())),2);
		}
		
		message->addText("[b]VERTEIDIGUNG:[/b]",1);
		message->addText(entity->getDefString(true),1);

        message->addText(functions::d2s(config.nget("def_restore_percent",0)*100));
		message->addText("% der Verteidigungsanlagen werden repariert!");

        /*/Log schreiben
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		mysqlpp::Query query = con_->query();
        query << "INSERT INTO ";
		query << "	logs_battle ";
		query << "(";
		query << "	logs_battle_fleet_id, ";
		query << "	logs_battle_user1_id, ";
		query << "	logs_battle_user2_id, ";
		query << "	logs_battle_user1_alliance_id, ";
		query << "	logs_battle_user2_alliance_id, ";
		query << "	logs_battle_alliances_have_war, ";
		query << "	logs_battle_entity_id, ";
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
		query << "	logs_battle_time, ";
		query << "	logs_battle_fleet_landtime ";
		query << ")";
		query << "VALUES";
		query << "(";
		query << "	'" << fleet->getId() << "', ";
		query << "	'" << fleet->getUserNicks() << "', ";
		query << "	'" << entity->getUserNicks() << "', ";
		query << "	'" << fleet->fleetUser->getAllianceId() << "', ";
		query << "	'" << entity->getUser->getAllianceId() << "', ";
		query << "	'" << alliancesHaveWar << "', ";
		query << "	'" << entity->getId() << "', ";
		query << "	'" << fleet->getActionString() << "', ";
		query << "	'" << returnV << "', ";
		query << "	'" << fleet->getCount(true) << "', ";
		query << "	'" << entity->getCount(true) - entity->getDefCount() << "', ";
		query << "	'" << entity->getDefCount() << "', ";
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
		query << "	'" << std::time(0) << "', ";
		query << "	'" << fleet_["fleet_landtime"] << "');";
		query.store();
		query.reset();
		
		log->addText("Battle id: " + con_->insert_id())*/

		switch (returnV)
		{
			case 1:	//angreifer hat gewonnen
				this->bstat = "Gewonnen";
				this->bstat2 = "Verloren";
				this->returnFleet = true;
				//Ranking::addBattlePoints($user_a_id,BATTLE_POINTS_A_W,"Angriff gegen ".$user_d_id);
				//Ranking::addBattlePoints($user_d_id,BATTLE_POINTS_D_L,"Verteidigung gegen ".$user_a_id);
				break;
			case 2:	//agreifer hat verloren
				this->bstat = "Verloren";
				this->bstat2 = "Gewonnen";
				this->returnFleet = false;
				//Ranking::addBattlePoints($user_a_id,BATTLE_POINTS_A_L,"Angriff gegen ".$user_d_id);
				//Ranking::addBattlePoints($user_d_id,BATTLE_POINTS_D_W,"Verteidigung gegen ".$user_a_id);
				break;
			case 3:	//beide flotten sind kaputt
				this->bstat = "Unentschieden";
				this->bstat2 = "Unentschieden";
				this->returnFleet = false;
				//Ranking::addBattlePoints($user_a_id,BATTLE_POINTS_A_D,"Angriff gegen ".$user_d_id);
				//Ranking::addBattlePoints($user_d_id,BATTLE_POINTS_D_D,"Verteidigung gegen ".$user_a_id);
				break;
			case 4: //beide flotten haben überlebt
				this->bstat = "Unentschieden";
				this->bstat2 = "Unentschieden";
				this->returnFleet = true;
				//Ranking::addBattlePoints($user_a_id,BATTLE_POINTS_A_D,"Angriff gegen ".$user_d_id);
				//Ranking::addBattlePoints($user_d_id,BATTLE_POINTS_D_D,"Verteidigung gegen ".$user_a_id);
				break;
		}
		
		Message *defender = new Message(message);
		
		fleet->addMessageUser(message);
		entity->addMessageUser(defender);
		
		defender->addSubject("Kampfbericht (" + this->bstat2 + ")");
		delete defender;
		
		message->addSubject("Kampfbericht(" + this->bstat + ")");
	}
	delete message;
}
