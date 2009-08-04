
#include "BattleHandler.h"

void BattleHandler::battle(Fleet* fleet, Entity* entity, Log* log)
{
	Config &config = Config::instance();
	std::time_t time = std::time(0);

	message->addType((int)config.idget("SHIP_WAR_MSG_CAT_ID"));

    // BEGIN SKRIPT //
	alliancesHaveWar = 0;

   	// Kampf abbrechen falls User gleich 0
	if (entity->getUserId()==0) {
		message->addText("[b]KAMPFBERICHT[/b]",1);
		message->addText("vom Planeten ",0);
		message->addText(entity->getCoords(),1);
		message->addText("[b]Zeit:[/b] ");
		message->addText(fleet->getLandtimeString(),2);
		message->addText("[b]Angreifer:[/b] ");
		message->addText(fleet->getUserNicks(),1);
		message->addText("[b]Verteidiger:[b] ");
		message->addText("Niemand");
		message->addText("Der Kampf wurde abgebrochen da der Verteidiger nicht mehr existiert!");
		
		message->addSubject("Kampfbericht (Unentschieden)");
		
		this->returnV = 4;
		this->returnFleet = true;
		
		fleet->addMessageUser(message);
		
		log->addText("Action failed: Opponent error");
	}
    else if (fleet->getLeaderId() > 0 && (fleet->fleetUser->getAllianceId()==entity->getUser()->getAllianceId() &&fleet->fleetUser->getAllianceId()!=0)) {
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
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();

		mysqlpp::Query query = con_->query();

		// Prüft, ob Krieg herrscht
		this->alliancesHaveWar = false;
		if(fleet->fleetUser->getAllianceId()!=0 && entity->getUser()->getAllianceId()!=0)
		{
			query << "SELECT ";
			query << "	alliance_bnd_id ";
			query << "FROM ";
			query << "	alliance_bnd ";
			query << "WHERE ";
			query << "	(alliance_bnd_alliance_id1='" << fleet->fleetUser->getAllianceId() << "' ";
			query << "	AND alliance_bnd_alliance_id2='" << entity->getUser()->getAllianceId() << "') ";
			query << "OR ";
			query << "	(alliance_bnd_alliance_id1='" << entity->getUser()->getAllianceId() << "' ";
			query << "	AND alliance_bnd_alliance_id2='" << fleet->fleetUser->getAllianceId() << "') ";
			query << "	AND alliance_bnd_level='3';";
			mysqlpp::Result warCheckRes = query.store();
			query.reset();

			if (warCheckRes)
			{
				int warCheckSize = warCheckRes.size();

				if (warCheckSize > 0)
				{
					this->alliancesHaveWar = true;
				}
			}
		}
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
		double initAttWeapon = fleet->getWeapon(true);
		double initDefWeapon = entity->getWeapon(true);

		double initAttStructure = fleet->getStructure(true);
		double initDefStructure = entity->getStructure(true);

		double initAttShield = fleet->getShield(true);
		double initDefShield = entity->getShield(true);

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

			attPercent = (cAttStructureShield==0) ? 0 : cAttStructureShield/initAttStructureShield;
			defPercent = (cDefStructureShield==0) ? 0 : cDefStructureShield/initDefStructureShield;

			message->addText(etoa::d2s(runde));
			message->addText(": ");
			message->addText(fleet->getCountString());
			message->addText(" Einheiten des Angreifes schiessen mit einer St&auml;rke von ");
			message->addText(fleet->getWeaponString());
			message->addText(" auf den Verteidiger. Der Verteidiger hat danach noch ");
			message->addText(etoa::nf(etoa::d2s(cDefStructureShield)));
			message->addText(" Struktur- und Schildpunkte",2);

			message->addText(etoa::d2s(this->runde));
			message->addText(": ");
			message->addText(entity->getCountString());
			message->addText(" Einheiten des Verteidigers schiessen mit einer St&auml;rke von ");
			message->addText(entity->getWeaponString());
			message->addText(" auf den Angreifer. Der Angreifer hat danach noch ");
			message->addText(etoa::nf(etoa::d2s(cAttStructureShield)));
			message->addText(" Struktur- und Schildpunkte",2);

			fleet->setPercentSurvive(attPercent,true);
			entity->setPercentSurvive(defPercent,true);

            if (fleet->getHeal() > 0) {
                cAttStructureShield += fleet->getHeal();
                if (cAttStructureShield > initAttStructureShield)
                    cAttStructureShield = initAttStructureShield;

				message->addText(etoa::d2s(runde));
				message->addText(": ");
				message->addText(etoa::d2s(fleet->getHealCount()));
				message->addText(" Einheiten des Angreifes heilen ");
				message->addText(etoa::d2s(fleet->getHeal()));
				message->addText(" Struktur- und Schildpunkte. Der Angreifer hat danach wieder ");

				fleet->setPercentSurvive(cAttStructureShield/initAttStructureShield,true);

				message->addText(entity->getStructureShieldString());
				message->addText(" Struktur- und Schildpunkte",1);
            }

            if (entity->getHeal() > 0) {
                cDefStructureShield += entity->getHeal();
                if (cDefStructureShield > initDefStructureShield)
                    cDefStructureShield = initDefStructureShield;

				message->addText(etoa::d2s(runde));
				message->addText(": ");
				message->addText(etoa::nf(etoa::d2s(entity->getHealCount())));
				message->addText(" Einheiten des Verteidigers heilen ");
				message->addText(etoa::nf(etoa::d2s(entity->getHeal())));
				message->addText(" Struktur- und Schildpunkte. Der Verteidiger hat danach wieder ");

				entity->setPercentSurvive(cDefStructureShield/initDefStructureShield,true);

				message->addText(entity->getStructureShieldString());
				message->addText(" Struktur- und Schildpunkte",1);
            }

			message->addText("",1);
            if (cAttStructureShield <= 0 || cDefStructureShield <= 0)
                break;
        }

		message->addText("Der Kampf dauerte ");
		message->addText(etoa::d2s(runde));
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
		std::vector<double> raid (5);
		raid[0] = 0;
		raid[1] = 0;
		raid[2] = 0;
		raid[3] = 0;
		raid[4] = 0;

		if (cDefStructureShield == 0 && cAttStructureShield > 0) {
			this->returnV = 1;

			message->addText("Der Angreifer hat den Kampf gewonnen!");

			double percent = std::min(fleet->getBountyBonus(),(fleet->getCapacity(true) / entity->getResSum()));
			raid[0] = entity->removeResMetal(fleet->addMetal(entity->getResMetal(percent),true));
			raid[1] = entity->removeResCrystal(fleet->addCrystal(entity->getResCrystal(percent),true));
			raid[2] = entity->removeResPlastic(fleet->addPlastic(entity->getResPlastic(percent),true));
			raid[3] = entity->removeResFuel(fleet->addFuel(entity->getResFuel(percent),true));
			raid[4] = entity->removeResFood(fleet->addFood(entity->getResFood(percent),true));

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
		message->addText(etoa::nf(etoa::d2s(entity->getAddedWfMetal())),1);
		message->addText("Silizium: ");
		message->addText(etoa::nf(etoa::d2s(entity->getAddedWfCrystal())),1);
		message->addText("PVC: ");
		message->addText(etoa::nf(etoa::d2s(entity->getAddedWfPlastic())),3);

		//
		//Auswertung
		//

		message->addText("[b]Zustand nach dem Kampf:[/b]",2);
		message->addText("[b]ANGREIFENDE FLOTTE:[/b]",1);
		message->addText(fleet->getShipString(),1);
		if (fleet->getAddedExp()>=0) {
			message->addText("Gewonnene EXP: ");
			message->addText(etoa::nf(etoa::d2s(fleet->getAddedExp())),2);
		}

		message->addText("",1);
		message->addText("[b]VERTEIDIGENDE FLOTTE:[/b]",1);
		message->addText(entity->getShipString(),1);
		if (entity->getAddedExp()>=0) {
			message->addText("Gewonnene EXP: ");
			message->addText(etoa::nf(etoa::d2s(entity->getAddedExp())),2);
		}

		message->addText("[b]VERTEIDIGUNG:[/b]",1);
		message->addText(entity->getDefString(true),1);

        message->addText(etoa::d2s(round((config.nget("def_restore_percent",0) + entity->getUser()->getSpecialist()->getSpecialistDefRepair() - 1)*100)));
		message->addText("% der Verteidigungsanlagen werden repariert!");

        //Log schreiben
        query << "INSERT INTO "
			<< "	logs_battle "
			<< "("
			<< "	fleet_id, "
			<< "	user_id, "
			<< "	entity_user_id, "
			<< "	user_alliance_id, "
			<< "	entity_user_alliance_id, "
			<< "	war, "
			<< "	entity_id, "
			<< "	action, "
			<< "	result, "
			<< "	fleet_ships_cnt, "
			<< "	entity_ships_cnt, "
			<< "	entity_defs_cnt, "
			<< "	fleet_weapon, "
			<< "	fleet_shield, "
			<< "	fleet_structure, "
			<< "	fleet_weapon_bonus, "
			<< "	fleet_shield_bonus, "
			<< "	fleet_structure_bonus, "
			<< "	entity_weapon, "
			<< "	entity_shield, "
			<< "	entity_structure, "
			<< "	entity_weapon_bonus, "
			<< "	entity_shield_bonus, "
			<< "	entity_structure_bonus, "
			<< "	fleet_win_exp, "
			<< "	entity_win_exp, "
			<< "	win_metal, "
			<< "	win_crystal, "
			<< "	win_pvc, "
			<< "	win_tritium, "
			<< "	win_food, "
			<< "	tf_metal, "
			<< "	tf_crystal, "
			<< "	tf_pvc, "
			<< "	timestamp, "
			<< "	landtime "
			<< ")"
			<< "VALUES"
			<< "("
			<< "	'" << fleet->getId() << "', "
			<< "	'" << fleet->getUserIds() << "', "
			<< "	'" << entity->getUserIds() << "', "
			<< "	'" << fleet->fleetUser->getAllianceId() << "', "
			<< "	'" << entity->getUser()->getAllianceId() << "', "
			<< "	'" << this->alliancesHaveWar << "', "
			<< "	'" << entity->getId() << "', "
			<< "	'" << fleet->getAction(true) << "', "
			<< "	'" << returnV << "', "
			<< "	'" << fleet->getInitCount(true) << "', "
			<< "	'" << entity->getInitCount(true) - entity->getInitDefCount() << "', "
			<< "	'" << entity->getInitDefCount() << "', "
			<< "	'" << initAttWeapon << "', "
			<< "	'" << initAttShield << "', "
			<< "	'" << initAttStructure << "', "
			<< "	'" << fleet->getWeaponBonus() * 100 << "', "
			<< "	'" << fleet->getShieldBonus() * 100 << "', "
			<< "	'" << fleet->getStructureBonus() * 100 << "', "
			<< "	'" << initDefWeapon << "', "
			<< "	'" << initDefShield <<"', "
			<< "	'" << initDefStructure << "', "
			<< "	'" << entity->getWeaponBonus() * 100 << "', "
			<< "	'" << entity->getShieldBonus() * 100 << "', "
			<< "	'" << entity->getStructureBonus() * 100 << "', "
			<< "	'" << fleet->getAddedExp() << "', "
			<< "	'" << entity->getAddedExp() << "', "
			<< "	'" << raid[0] << "', "
			<< "	'" << raid[1] << "', "
			<< "	'" << raid[2] << "', "
			<< "	'" << raid[3] << "', "
			<< "	'" << raid[4] << "', "
			<< "	'" << entity->getAddedWfMetal() << "', "
			<< "	'" << entity->getAddedWfCrystal() << "', "
			<< "	'" << entity->getAddedWfPlastic() << "', "
			<< "	'" << std::time(0) << "', "
			<< "	'" << fleet->getLandtime() << "');";
		query.store();
		query.reset();

		log->addText(("Battle id: " + etoa::d2s(con_->insert_id())));

		switch (returnV)
		{
			case 1:	//angreifer hat gewonnen
				this->bstat = "Gewonnen";
				this->bstat2 = "Verloren";
				this->returnFleet = true;
				this->attPoints = 3;
				this->defPoints = 0;
				this->attResult = 2;
				this->defResult = 0;
				break;
			case 2:	//agreifer hat verloren
				this->bstat = "Verloren";
				this->bstat2 = "Gewonnen";
				this->returnFleet = false;
				this->attPoints = 1;
				this->defPoints = 2;
				this->attResult = 0;
				this->defResult = 2;
				break;
			case 3:	//beide flotten sind kaputt
				this->bstat = "Unentschieden";
				this->bstat2 = "Unentschieden";
				this->returnFleet = false;
				this->attPoints = 1;
				this->defPoints = 1;
				this->attResult = 1;
				this->defResult = 1;
				break;
			case 4: //beide flotten haben überlebt
				this->bstat = "Unentschieden";
				this->bstat2 = "Unentschieden";
				this->returnFleet = true;
				this->attPoints = 1;
				this->defPoints = 1;
				this->attResult = 1;
				this->defResult = 1;
				break;
		}

		size_t found;
		int user;
		std::string attReason = "Angriff gegen" + entity->getUserNicks();
		std::string defReason = "Verteidigung gegen" + fleet->getUserNicks();
		
		if (initAttWeapon>0) {
			std::string users = fleet->getUserIds();
			found=users.find_first_of(",");
			while (found!=std::string::npos) {
				users = users.substr(found+1);
				found=users.find_first_of(",");
				user = (int)etoa::s2d(users.substr(0,found));
				etoa::addBattlePoints(user,this->attPoints,this->attResult,attReason);
			}
			
			users = entity->getUserIds();
			found=users.find_first_of(",");
			while (found!=std::string::npos) {
				users = users.substr(found+1);
				found=users.find_first_of(",");
				user = (int)etoa::s2d(users.substr(0,found));
				etoa::addBattlePoints(user,this->defPoints,this->defResult,attReason);
			}
		}


		Message *defender = new Message(message);

		fleet->addMessageUser(message);
		entity->addMessageUser(defender);

		defender->addSubject("Kampfbericht (" + this->bstat2 + ")");
		delete defender;

		message->addSubject("Kampfbericht(" + this->bstat + ")");

		fleet->addRaidedRes();
	}
	delete message;
}
