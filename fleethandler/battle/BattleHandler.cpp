#include <mysql++/mysql++.h>
#include <vector>
#include <math.h>
#include <ctime>
#include <algorithm>
#include "../config/ConfigHandler.h"
#include "../functions/Functions.h"

#include "BattleHandler.h"

#include "ObjectHandler.h"
#include "UserHandler.h"


	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: battle.php													//
	// Topic: Kampfscript			 									//
	// Version: 0.1																	//
	// Letzte Änderung: 21.05.2005									//
	//////////////////////////////////////////////////

void BattleHandler::loadSpecial()
{
	// Lädt die Bonis der Spezialschiffe und summiert sie
	mysqlpp::Query query = con_->query();
	query << "SELECT ";
	query << "	s.special_ship_bonus_antrax, ";
    query << "	s.special_ship_bonus_forsteal, ";
	query << "	s.special_ship_bonus_build_destroy, ";
	query << "	s.special_ship_bonus_antrax_food, ";
	query << "	s.special_ship_bonus_deactivade, ";

	query << "	fs.fs_ship_cnt, ";

	query << "	sl.shiplist_special_ship_bonus_antrax, ";
	query << "	sl.shiplist_special_ship_bonus_forsteal, ";
	query << "	sl.shiplist_special_ship_bonus_build_destroy, ";
	query << "	sl.shiplist_special_ship_bonus_antrax_food, ";
	query << "	sl.shiplist_special_ship_bonus_deactivade ";
	query << "FROM  ";
	query << "(";
	query << "	(";
	query << "	fleet_ships AS fs ";
	query << "		INNER JOIN ";
	query << "			fleet AS f ";
	query << "		ON fs.fs_fleet_id = f.fleet_id ";
	query << "	) ";
	query << "	INNER JOIN ";
	query << "		ships AS s ";
	query << "	ON fs.fs_ship_id = s.ship_id ";
	query << ") ";
	query << "INNER JOIN ";
	query << "	shiplist AS sl ";
	query << "ON sl.shiplist_planet_id = f.fleet_entity_from ";
	query << "AND sl.shiplist_user_id = f.fleet_user_id ";
	query << "AND s.ship_id = sl.shiplist_ship_id ";
	query << "AND f.fleet_id='" << fleet_["fleet_id"] << "' ";
	query << "AND s.special_ship='1';";
	mysqlpp::Result specialBoniRes = query.store();
	query.reset();
	
	if (specialBoniRes)
	{
		int specialBoniSize = specialBoniRes.size();
		mysqlpp::Row specialBoniRow;
		
		if (specialBoniSize > 0)
		{
			for (mysqlpp::Row::size_type i = 0; i<specialBoniSize; i++) 
			{
				specialBoniRow = specialBoniRes.at(i);
	    			
            	specialShipBonusAntrax			+= (float)specialBoniRow["special_ship_bonus_antrax"]			* (float)specialBoniRow["shiplist_special_ship_bonus_antrax"];
            	specialShipBonusForsteal		+= (float)specialBoniRow["special_ship_bonus_forsteal"]		* (float)specialBoniRow["shiplist_special_ship_bonus_forsteal"];
            	specialShipBonusBuildDestroy	+= (float)specialBoniRow["special_ship_bonus_build_destroy"]	* (float)specialBoniRow["shiplist_special_ship_bonus_build_destroy"];
            	specialShipBonusAntraxFood		+= (float)specialBoniRow["special_ship_bonus_antrax_food"]		* (float)specialBoniRow["shiplist_special_ship_bonus_antrax_food"];
            	specialShipBonusDeactivade		+= (float)specialBoniRow["special_ship_bonus_deactivade"]		* (float)specialBoniRow["shiplist_special_ship_bonus_deactivade"];
            }

         }
	}
}
		
		 
		 
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
	UserHandler *attacker = new UserHandler((int)fleet_["fleet_user_id"]);	
	UserHandler *defender = new UserHandler(functions::getUserIdByPlanet((int)fleet_["fleet_entity_to"]));

   	// Kampf abbrechen falls User gleich
    if (attacker->userId==defender->userId)
    {
	    msgFight = "[b]KAMPFBERICHT[/b]\nvom Planeten ";
		msgFight += functions::formatCoords((int)fleet_["fleet_entity_to"],0);
		msgFight += "\n[b]Zeit:[/b] ";
		msgFight += functions::formatTime((int)fleet_["fleet_landtime"]);
		msgFight += "\n\n";
	    msgFight += "[b]Angreifer:[/b] ";
		msgFight += functions::getUserNick(attacker->userId);
		msgFight += "\n";
	    msgFight += "[b]Verteidiger:[/b] ";
		msgFight += functions::getUserNick(defender->userId);
		msgFight += "\n\n";
    	msgFight += "Der Kampf wurde abgebrochen da Angreifer und Verteidiger demselben Imperium angehören!";
    	
			returnV = 4;
			bstat = "Unentschieden";
			bstat2 = "Unentschieden";
			returnFleet = true;
  	} 
  	
  	// Kampf abbrechen und Flotte zum Startplanet schicken wenn Kampfsperre aktiv ist
  	else if ((int)config.nget("battleban",0)!=0 && (int)config.nget("battleban",1)<=time && (int)config.nget("battleban",2)>time)
	{
  		msgFight = "[b]KAMPFBERICHT[/b]\nvom Planeten ";
		msgFight += functions::formatCoords((int)fleet_["fleet_entity_to"],0);
		msgFight += "\n[b]Zeit:[/b] ";
		msgFight += functions::formatTime((int)fleet_["fleet_landtime"]);
		msgFight += "\n\n";
	    msgFight += "[b]Angreifer:[/b] ";
		msgFight += functions::getUserNick(attacker->userId);
		msgFight += "\n";
	    msgFight += "[b]Verteidiger:[/b] ";
		msgFight += functions::getUserNick(defender->userId);
		msgFight += "\n\n";
    	msg += config.get("battleban_arrival_text",1);
    	
			returnV = 4;
			bstat = "Unentschieden";
			bstat2 = "Unentschieden";
			returnFleet =true;
	}
	else
	{

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
		msg += functions::formatCoords((int)fleet_["fleet_entity_to"],0);
		msg += "\n[b]Zeit:[/b] ";
		msg += functions::formatTime((int)fleet_["fleet_landtime"]);
		msg += "\n\n";
		msg += "[b]Angreifer:[/b] ";
		msg += functions::getUserNick(attacker->userId);
		msg += "\n";
		msg += "[b]Verteidiger:[/b] ";
		msg += functions::getUserNick(defender->userId);
		msg += "\n\n";
		msg += "[b]ANGREIFENDE FLOTTE:[/b]\n";

		//
		// Flotten Daten (att)
		//

        // Daten der angreifenden Flotte laden
        query << "SELECT ";
		query << "	s.ship_id, ";
		query << "	s.ship_name, ";
		query << "	s.ship_structure, ";
		query << "	s.ship_shield, ";
		query << "	s.ship_weapon, ";
		query << "	s.ship_heal, ";
		query << "	s.ship_costs_metal, ";
		query << "	s.ship_costs_crystal, ";
		query << "	s.ship_costs_plastic, ";
		query << "	s.ship_costs_fuel, ";
		query << "	s.ship_costs_food, ";
		query << "	s.ship_capacity, ";
		query << "	s.ship_steal, ";
		query << "	s.special_ship, ";
		query << "	s.special_ship_need_exp, ";
		query << "	s.special_ship_exp_factor, ";
		query << "	s.special_ship_bonus_weapon, ";
		query << "	s.special_ship_bonus_structure, ";
		query << "	s.special_ship_bonus_shield, ";
		query << "	s.special_ship_bonus_heal, ";
		query << "	s.special_ship_bonus_capacity, ";

		query << "	fs.fs_ship_cnt, ";
		query << "	fs.fs_ship_id, ";
		query << "	fs.fs_special_ship, ";
		query << "	fs.fs_special_ship_level, ";
		query << "	fs.fs_special_ship_exp, ";
		query << "	fs.fs_special_ship_bonus_weapon, ";
		query << "	fs.fs_special_ship_bonus_structure, ";
		query << "	fs.fs_special_ship_bonus_shield, ";
		query << "	fs.fs_special_ship_bonus_heal, ";
		query << "	fs.fs_special_ship_bonus_capacity, ";
		query << "	fs.fs_special_ship_bonus_speed, ";
		query << "	fs.fs_special_ship_bonus_pilots, ";
		query << "	fs.fs_special_ship_bonus_tarn, ";
		query << "	fs.fs_special_ship_bonus_antrax, ";
		query << "	fs.fs_special_ship_bonus_forsteal, ";
		query << "	fs.fs_special_ship_bonus_build_destroy, ";
		query << "	fs.fs_special_ship_bonus_antrax_food, ";
		query << "	fs.fs_special_ship_bonus_deactivade ";
		query << "FROM ";
		query << "	fleet_ships AS fs ";
		query << "		INNER JOIN ";
		query << "			fleet AS f ";
		query << "			ON fs.fs_fleet_id = f.fleet_id ";
		query << "			AND f.fleet_id='" << fleet_["fleet_id"] << "' ";
		query << "		INNER JOIN ";
		query << "		ships AS s ";
		query << "		ON fs.fs_ship_id = s.ship_id ";
		query << "ORDER BY ";
		query << "	s.special_ship DESC, ";
		query << "	s.ship_name;";
		mysqlpp::Result fsRes = query.store();
		query.reset();
		
		if (fsRes)
		{
			int fsSize = fsRes.size();
			
			if (fsSize > 0)
			{
				mysqlpp::Row fsRow;
	    		for (mysqlpp::Row::size_type i = 0; i<fsSize; i++) 
				{
	    			fsRow = fsRes.at(i);
                
					if ((int)fsRow["ship_steal"]!=50)
					{
						shipSteal = std::max((int)fsRow["ship_steal"], shipSteal);
					}
					else
					{
						dontSteal = 1;
					}


					ObjectHandler att(fsRow,2);
					
					attacker->objects.push_back(att);
					
					if((int)fsRow["special_ship"]==1)
					{
						
						msg += "[B]";
						msg += std::string(fsRow["ship_name"]);
						msg += "[/B] ";
						msg += functions::nf(std::string(fsRow["fs_ship_cnt"]));
						msg += "\n";
						
						//bonus von spezialschiffe dazu rechnen
						attacker->weaponTech += (double)fsRow["bonus_weapon"] * (double)fsRow["ship_bonus_weapon"];
						attacker->structureTech += (double)fsRow["bonus_structure"] * (double)fsRow["ship_bonus_structure"];
						attacker->shieldTech += (double)fsRow["bonus_shield"] * (double)fsRow["ship_bonus_shield"];
						attacker->healTech += (double)fsRow["bonus_heal"] * (double)fsRow["ship_bonus_heal"];
					}
					else
					{
						msg += std::string(fsRow["ship_name"]);
						msg += " ";
						msg += functions::nf(std::string(fsRow["fs_ship_cnt"]));
						msg += "\n";					
					}
						
					attacker->structure += (double)fsRow["ship_structure"] * (double)fsRow["fs_ship_cnt"];
					attacker->shield += (double)fsRow["ship_shield"] * (double)fsRow["fs_ship_cnt"];
					attacker->weapon += (double)fsRow["ship_weapon"] * (double)fsRow["fs_ship_cnt"];
					attacker->count += (double)fsRow["fs_ship_cnt"];

				}
            }
        }

		//
		// Flotten & Def Daten (def)
		//

        msg += "\n[b]VERTEIDIGENDE FLOTTE:[/b]\n";

        // Daten der Verteidigung und der Flotte auf dem Planeten laden
        query << "SELECT ";
		query << "	s.ship_id, ";
		query << "	s.ship_name, ";
		query << "	s.ship_structure, ";
		query << "	s.ship_shield, ";
		query << "	s.ship_weapon, ";
		query << "	s.ship_heal, ";
		query << "	s.ship_costs_metal, ";
		query << "	s.ship_costs_crystal, ";
		query << "	s.ship_costs_plastic, ";
		query << "	s.ship_costs_fuel, ";
		query << "	s.ship_costs_food, ";
		query << "	s.ship_capacity, ";
		query << "	s.ship_steal, ";
		query << "	s.special_ship, ";
		query << "	s.special_ship_need_exp, ";
		query << "	s.special_ship_exp_factor, ";
		query << "	s.special_ship_bonus_weapon, ";
		query << "	s.special_ship_bonus_structure, ";
		query << "	s.special_ship_bonus_shield, ";
		query << "	s.special_ship_bonus_heal, ";
		query << "	s.special_ship_bonus_capacity, ";

		query << "	sl.shiplist_count, ";
		query << "	sl.shiplist_special_ship, ";
		query << "	sl.shiplist_special_ship_level, ";
		query << "	sl.shiplist_special_ship_exp, ";
		query << "	sl.shiplist_special_ship_bonus_weapon, ";
		query << "	sl.shiplist_special_ship_bonus_structure, ";
		query << "	sl.shiplist_special_ship_bonus_shield, ";
		query << "	sl.shiplist_special_ship_bonus_heal, ";
		query << "	sl.shiplist_special_ship_bonus_capacity, ";
		query << "	sl.shiplist_special_ship_bonus_speed, ";
		query << "	sl.shiplist_special_ship_bonus_pilots, ";
		query << "	sl.shiplist_special_ship_bonus_tarn, ";
		query << "	sl.shiplist_special_ship_bonus_antrax, ";
		query << "	sl.shiplist_special_ship_bonus_forsteal, ";
		query << "	sl.shiplist_special_ship_bonus_build_destroy, ";
		query << "	sl.shiplist_special_ship_bonus_antrax_food, ";
		query << "	sl.shiplist_special_ship_bonus_deactivade ";
		query << "	FROM ";
		query << "		shiplist AS sl ";
		query << "	INNER JOIN  ";
		query << "		ships AS s ";
		query << "	ON sl.shiplist_ship_id = s.ship_id ";
		query << "	AND sl.shiplist_planet_id='" << fleet_["fleet_entity_to"] << "' ";
		query << "	AND sl.shiplist_user_id='" << defender->userId << "' ";
		query << "	AND sl.shiplist_count>'0' ";
		query << "ORDER BY ";
		query << "	s.special_ship DESC, ";
		query << "	s.ship_name;";
		mysqlpp::Result psRes = query.store();
		query.reset();

		if (psRes)
		{
			int psSize = psRes.size();
			
			if (psSize > 0)
			{
				mysqlpp::Row psRow;
				
				for (mysqlpp::Row::size_type i = 0; i<psSize; i++) 
				{
	    			psRow = psRes.at(i);
					
					ObjectHandler def(psRow,1);
					defender->objects.push_back(def);
					
					//Spezialschiffe (def)
					if((int)psRow["special_ship"]==1)
					{
						msg += "[B]";
						msg += std::string(psRow["ship_name"]);
						msg += "[/B] ";
						msg += functions::nf(std::string(psRow["shiplist_count"]));
						msg += "\n";

				        //bonus von spezialschiffe dazu rechnen
						defender->weaponTech += (double)psRow["bonus_weapon"] * (double)psRow["ship_bonus_weapon"];
						defender->structureTech += (double)psRow["bonus_structure"] * (double)psRow["ship_bonus_structure"];
						defender->shieldTech += (double)psRow["bonus_shield"] * (double)psRow["ship_bonus_shield"];
						defender->healTech += (double)psRow["bonus_heal"] * (double)psRow["ship_bonus_heal"];
					}
					else
					{
						msg += std::string(psRow["ship_name"]);
						msg += " ";
						msg += functions::nf(std::string(psRow["shiplist_count"]));
						msg += "\n";					
					}
					
					defender->structure += (double)psRow["ship_structure"] * (double)psRow["shiplist_count"];
					defender->shield += (double)psRow["ship_shield"] * (double)psRow["shiplist_count"];
					defender->weapon += (double)psRow["ship_weapon"] * (double)psRow["shiplist_count"];
					defender->count += (double)psRow["shiplist_count"];
				}
            }
			else
			{
				msg += "[i]Nichts vorhanden![/i]\n";
			}
		}


		msg += "\n[b]PLANETARE VERTEIDIGUNG:[/b]\n";
		query << "SELECT ";
		query << "	d.def_id, ";
		query << "	d.def_name, ";
		query << "	dl.deflist_count, ";
		query << "	d.def_structure, ";
		query << "	d.def_shield, ";
		query << "	d.def_weapon, ";
		query << "	d.def_heal, ";
		query << "	d.def_costs_metal, ";
		query << "	d.def_costs_crystal, ";
		query << "	d.def_costs_plastic, ";
		query << "	d.def_costs_fuel, ";
		query << "d.def_costs_food ";
		query << "FROM ";
		query << "	deflist AS dl ";
		query << "	INNER JOIN  ";
		query << "		defense AS d ";
		query << "	ON dl.deflist_def_id = d.def_id ";
		query << "	AND dl.deflist_planet_id='" << fleet_["fleet_entity_to"] << "' ";
		query << "	AND dl.deflist_user_id='" << defender->userId << "' ";
		query << "	AND dl.deflist_count>'0';";
		mysqlpp::Result pdRes = query.store();
		query.reset();

		if (pdRes)
		{
			int pdSize = pdRes.size();
			
			if (pdSize > 0)
			{
				mysqlpp::Row pdRow;
			
				for (mysqlpp::Row::size_type i = 0; i<pdSize; i++) 
				{
					pdRow = pdRes.at(i);		
			
					msg += "";
					msg += std::string(pdRow["def_name"]);
					msg += " ";
					msg += functions::nf(std::string(pdRow["deflist_count"]));
					msg += "\n";
					
					ObjectHandler def(pdRow,0);
					defender->defObjects.push_back(def);
					
					
					defender->structure += (double)pdRow["def_structure"] * (double)pdRow["deflist_count"];
					defender->shield += (double)pdRow["def_shield"] * (double)pdRow["deflist_count"];
					defender->weapon += (double)pdRow["def_weapon"] * (double)pdRow["deflist_count"];
					defender->count += (double)pdRow["deflist_count"];
				}
			}
			else
			{
				msg += "[i]Nichts vorhanden![/i]\n";
			}
		}
		
		msg += "\n";

		//
		//Technologie Daten laden (att)
		//

        //Liest Level der Waffen-,Schild-,Panzerungs-,Regena Tech aus Datenbank (att)
        query << "SELECT ";
		query << "	techlist_tech_id, ";
		query << "	techlist_current_level ";
		query << "FROM ";
		query << "	techlist ";
		query << "WHERE ";
		query << "	techlist_user_id='" << attacker->userId << "' ";
		query << "	AND ";
		query << "	(";
		query << "		techlist_tech_id='" << config.idget("STRUCTURE_TECH_ID") << "' ";
		query << "		OR techlist_tech_id='" << config.idget("SHIELD_TECH_ID") <<  "' ";
		query << "		OR techlist_tech_id='" << config.idget("WEAPON_TECH_ID") << "' ";
		query << "		OR techlist_tech_id='" << config.idget("REGENA_TECH_ID") << "' ";
		query << "	);";
		mysqlpp::Result techResA = query.store();
		query.reset();

		if (techResA)
		{
			int techSizeA = techResA.size();
			
			if (techSizeA > 0)
			{
				mysqlpp::Row techRowA;
				
				for (mysqlpp::Row::size_type i = 0; i<techSizeA; i++) 
				{
					techRowA = techResA.at(i);
					
					if ((int)techRowA["techlist_tech_id"]==config.idget("SHIELD_TECH_ID"))
						attacker->shieldTech += ((float)techRowA["techlist_current_level"]/10);

					if ((int)techRowA["techlist_tech_id"]==config.idget("STRUCTURE_TECH_ID"))
						attacker->structureTech += ((float)techRowA["techlist_current_level"]/10);

					if ((int)techRowA["techlist_tech_id"]==config.idget("WEAPON_TECH_ID"))
						attacker->weaponTech += ((float)techRowA["echlist_current_level"]/10);

					if ((int)techRowA["techlist_tech_id"]==config.idget("REGENA_TECH_ID"))
						attacker->healTech += ((float)techRowA["techlist_current_level"]/10);
				}
			}
		}

		//
		//Technologie Daten laden (def)
		//


        //Liest Level der Waffen-,Schild-,Panzerungs-,Regena Tech aus Datenbank (def)
        query << "SELECT ";
		query << "	techlist_tech_id, ";
		query << "	techlist_current_level ";
		query << "FROM ";
		query << "	techlist ";
		query << "WHERE ";
		query << "	techlist_user_id='" << defender->userId << "' ";
		query << "	AND ";
		query << "	( ";
		query << "		techlist_tech_id='" << config.idget("STRUCTURE_TECH_ID") << "' ";
		query << "		OR techlist_tech_id='" << config.idget("SHIELD_TECH_ID") << "' ";
		query << "		OR techlist_tech_id='" << config.idget("WEAPON_TECH_ID") << "' ";
		query << "		OR techlist_tech_id='" << config.idget("REGENA_TECH_ID") << "' ";
		query << "	);";
		mysqlpp::Result techResD = query.store();
		query.reset();

		if (techResD)
		{
			int techSizeD = techResD.size();

			if (techSizeD > 0)
			{
				mysqlpp::Row techRowD;
				
				for (mysqlpp::Row::size_type i = 0; i<techSizeD; i++) 
				{
					techRowD = techResD.at(i);
					
					if ((int)techRowD["techlist_tech_id"]==config.idget("SHIELD_TECH_ID"))
						defender->shieldTech += ((float)techRowD["techlist_current_level"]/10);

					if ((int)techRowD["techlist_tech_id"]==config.idget("STRUCTURE_TECH_ID"))
						defender->structureTech += ((float)techRowD["techlist_current_level"]/10);

					if ((int)techRowD["techlist_tech_id"]==config.idget("WEAPON_TECH_ID"))
						defender->weaponTech += ((float)techRowD["echlist_current_level"]/10);

					if ((int)techRowD["techlist_tech_id"]==config.idget("REGENA_TECH_ID"))
						defender->healTech += ((float)techRowD["techlist_current_level"]/10);
				}
			}
		}

		//
		//Kampf Daten errechnen
		//
		//init... = wert vor dem kampf (wird nicht verändert) und c... aktueller Wert

		//Anzahl Schiffe
        attacker->cCount = std::max(attacker->count,(double)0);
        defender->cCount = std::max(defender->count,(double)0);

        //Schildfstärke
        attacker->initShield = std::max((attacker->shieldTech * attacker->shield),(double)0);
        defender->initShield = std::max((defender->shieldTech * defender->shield),(double)0);

		//Strukturstärke
        attacker->initStructure = std::max((attacker->structureTech * attacker->structure),(double)0);
        defender->initStructure = std::max((defender->structureTech * defender->structure),(double)0);

		//Waffenstärke
        attacker->initWeapon = std::max((attacker->weapon * attacker->weaponTech),(double)0);
        defender->initWeapon = std::max((defender->weapon * defender->weaponTech),(double)0);
		
		attacker->cWeapon = attacker->initWeapon;
		defender->cWeapon = defender->initWeapon;

		//Schild + Strukturstärke
        attacker->initStructureShield = std::max((attacker->initShield + attacker->initStructure),(double)0);
        defender->initStructureShield = std::max((defender->initShield + defender->initStructure),(double)0);
		
		attacker->cStructureShield = attacker->initStructureShield;
		defender->cStructureShield = defender->initStructureShield;

        msg += "[b]DATEN DES ANGREIFERS[/b]\n[b]Schild (";
		msg += functions::nf(functions::d2s(attacker->shieldTech * 100));
		msg += "%):[/b] ";
		msg += functions::nf(functions::d2s(attacker->initShield));
		msg += "\n[b]Struktur (";
		msg += functions::nf(functions::d2s(attacker->structureTech * 100));
		msg += "%):[/b] ";
		msg += functions::nf(functions::d2s(attacker->initStructure));
		msg += "\n[b]Waffen (";
		msg += functions::nf(functions::d2s(attacker->weaponTech * 100));
		msg += "%):[/b] ";
		msg += functions::nf(functions::d2s(attacker->initWeapon));
		msg += "\n[b]Einheiten:[/b] ";
		msg += functions::nf(functions::d2s(attacker->count));
		msg +="\n\n[b]DATEN DES VERTEIDIGERS[/b]\n[b]Schild (";
		msg += functions::nf(functions::d2s(defender->shieldTech * 100));
		msg += "%):[/b] ";
		msg += functions::nf(functions::d2s(defender->initShield));
		msg += "\n[b]Struktur (";
		msg += functions::nf(functions::d2s(defender->structureTech * 100));
		msg += "%):[/b] ";
		msg += functions::nf(functions::d2s(defender->initStructure));
		msg += "\n[b]Waffen (";
		msg += functions::nf(functions::d2s(defender->weaponTech * 100));
		msg += "%):[/b] ";
		msg += functions::nf(functions::d2s(defender->initWeapon));
		msg += "\n[b]Einheiten:[/b] ";
		msg += functions::nf(functions::d2s(defender->count));
        msg +="\n\n";

		//
		//Der Kampf!
		//
        for (int bx = 0; bx < config.nget("battle_rounds",0); bx++)
        {

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
			
			
            if (attacker->cHealCount > 0 && attacker->cCount > 0)
            {
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

            if (defender->cHealCount > 0 && defender->cCount > 0)
            {
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
		query << "	id='" << fleet_["fleet_entity_to"] << "';";
		query.store();
		query.reset(),

        //Löscht die flotte und setzt alle schiffe & def zurück (es wird wieder eingetragen!)
        query << "UPDATE ";
		query << "	deflist ";
		query << "SET ";
		query << "	deflist_count='0' ";
		query << "WHERE ";
		query << "	deflist_planet_id='" << fleet_["fleet_entity_to"] << "';";
		query.store();
		query.reset();
		
        query << "UPDATE ";
		query << "	shiplist ";
		query << "SET ";
		query << "	shiplist_count='0' ";
		query << "WHERE ";
		query << "	shiplist_planet_id='" << fleet_["fleet_entity_to"] << "';";
		query.store();
		query.reset();
		
        query << "DELETE FROM ";
		query << "	fleet_ships ";
		query << "WHERE ";
		query << "	fs_fleet_id='" << fleet_["fleet_id"] << "';";
		query.store();
		query.reset();


		//
		//Auswertung
		//
		//Schiffe/Def wiederherstellen
		//
		std::vector< ObjectHandler>::iterator it;
		
		std::string attMsg = "[b]Zustand nach dem Kampf:[/b]\n\n";
		attMsg += "[b]ANGREIFENDE FLOTTE:[/b]\n";
		
		specialShipBonusCapacity = 1;
		
		bool special;
		
		for (it=attacker->objects.begin() ; it<attacker->objects.end(); it++)
		{
			if (it->newCnt > 0 && it->special)
			{
				it->shipExp += attacker->newExpInit;
				specialShipBonusCapacity += it->bonusCapacity * it->shipBonusCapacity;
				special = true;
				
			    query << "INSERT INTO ";
				query << "	fleet_ships ";
				query << "(";
				query << "	fs_fleet_id, ";
				query << "	fs_ship_id, ";
				query << "	fs_ship_cnt, ";
				query << "	fs_special_ship, ";
				query << "	fs_special_ship_level, ";
				query << "	fs_special_ship_exp, ";
				query << "	fs_special_ship_bonus_weapon, ";
				query << "	fs_special_ship_bonus_structure, ";
				query << "	fs_special_ship_bonus_shield, ";
				query << "	fs_special_ship_bonus_heal, ";
				query << "	fs_special_ship_bonus_capacity, ";
				query << "	fs_special_ship_bonus_speed, ";
				query << "	fs_special_ship_bonus_pilots, ";
				query << "	fs_special_ship_bonus_tarn, ";
				query << "	fs_special_ship_bonus_antrax, ";
				query << "	fs_special_ship_bonus_forsteal, ";
				query << "	fs_special_ship_bonus_build_destroy, ";
				query << "	fs_special_ship_bonus_antrax_food, ";
				query << "	fs_special_ship_bonus_deactivade ";
				query << ") ";
				query << "VALUES ";
				query << "(";
				query << "	'" << fleet_["fleet_id"] << "', ";
				query << "	'" << it->sid << "', ";
				query << "	'" << it->newCnt << "', ";
				query << "	'1', ";
				query << "	'" << it->shipLevel << "', ";
				query << "	'" << it->shipExp << "', ";
				query << "	'" << it->shipsBonusWeapon << "', ";
				query << "	'" << it->shipBonusStructure << "', ";
				query << "	'" << it->shipsBonusShield << "', ";
				query << "	'" << it->shipBonusHeal << "', ";
				query << "	'" << it->shipBonusCapacity << "', ";
				query << "	'" << it->shipsBonusSpeed << "', ";
				query << "	'" << it->shipsBonusPilots << "', ";
				query << "	'" << it->shipsBonusTarn << "', ";
				query << "	'" << it->shipBonusAntrax << "', ";
				query << "	'" << it->shipsBonusForsteal << "',";
				query << "	'" << it->shipsBonusDestroy << "', ";
				query << "	'" << it->shipsBonusAntraxFood << "', ";
				query << "	'" << it->shipBonusDeactivade << "' ";
				query << ");";
				query.store();
				query.reset();
				}
		}
		
		for ( it = attacker->objects.begin() ; it < attacker->objects.end(); it++ )
		{
			if (it->newCnt > 0)
			{
				query << "INSERT INTO ";
				query << "	fleet_ships ";
				query << "(";
				query << "	fs_fleet_id, ";
				query << "	fs_ship_id, ";
				query << "	fs_ship_cnt ";
				query << ")";
				query << "VALUES ";
				query << " (";
				query << "	'" << fleet_["fleet_id"] << "', ";
				query << "	'" << it->sid << "', ";
				query << "	'" << it->newCnt << "' ";
				query << ");";
				query.store();
				query.reset();
				
				
				//Kapazität der überlebenden Schiffe rechnen
				capa += it->capacity * it->newCnt;
				
			}
			
			attMsg += it->name;
			attMsg += " ";
			attMsg += functions::d2s(it->newCnt);
			attMsg += "\n";
		}

	
		if (special)
		{
			attMsg += "\nDie Spezialschiffe von [B]";
			attMsg += functions::getUserNick(attacker->userId);
			attMsg += "[/B] erhalten ";
			attMsg += functions::nf(functions::d2s(attacker->newExpInit));
			attMsg += " EXP!\n\n";
			attMsg += "\nGewonnene EXP: ";
			attMsg += functions::nf(functions::d2s(attacker->newExpInit));
			attMsg += "\n\n";
		}
		

		//Stellt die Verteidigung wieder her
		special = false;
		
		std::string defMsg = "\n[b]VERTEIDIGENDE FLOTTE:[/b]\n";
		
		for ( it = defender->objects.begin() ; it < defender->objects.end(); it++ )
		{
			if (it->newCnt > 0 && it->special == 1)
			{
				it->shipExp += defender->newExpInit;
				special = true;

                query << "SELECT ";
				query << "	shiplist_ship_id ";
				query << "FROM ";
				query << "	shiplist ";
				query << "WHERE ";
				query << "	shiplist_planet_id='" << fleet_["fleet_entity_to"] << "' ";
				query << "	AND shiplist_ship_id='" << it->sid << "';";
				mysqlpp::Result slRes = query.store();
				query.reset();
				
				if (slRes)
				{
					int slSize = slRes.size();
					
					if (slSize > 0)
					{
						query << "UPDATE ";
						query << "	shiplist ";
						query << "SET ";
						query << "	shiplist_count='" << it->newCnt << "', ";
						query << "	shiplist_special_ship='1', ";
						query << "	shiplist_special_ship_level='" << it->shipLevel << "', ";
						query << "	shiplist_special_ship_exp='" << it->shipExp << "', ";
						query << "	shiplist_special_ship_bonus_weapon='" << it->shipsBonusWeapon << "', ";
						query << "	shiplist_special_ship_bonus_structure='" <<it->shipBonusStructure << "', ";
						query << "	shiplist_special_ship_bonus_shield='" <<it->shipsBonusShield << "', ";
						query << "	shiplist_special_ship_bonus_heal='" << it->shipBonusHeal << "', ";
						query << "	shiplist_special_ship_bonus_capacity='" << it->shipBonusCapacity << "', ";
						query << "	shiplist_special_ship_bonus_speed='" << it->shipsBonusSpeed << "', ";
						query << "	shiplist_special_ship_bonus_pilots='" << it->shipsBonusPilots << "', ";
						query << "	shiplist_special_ship_bonus_tarn='" << it->shipsBonusTarn << "', ";
						query << "	shiplist_special_ship_bonus_antrax='" << it->shipBonusAntrax << "', ";
						query << "	shiplist_special_ship_bonus_forsteal='" << it->shipsBonusForsteal << "', ";
						query << "	shiplist_special_ship_bonus_build_destroy='" << it->shipsBonusDestroy << "', ";
						query << "	shiplist_special_ship_bonus_antrax_food='" << it->shipsBonusAntraxFood << "', ";
						query << "	shiplist_special_ship_bonus_deactivade='" << it->shipBonusDeactivade << "' ";
						query << "WHERE ";
						query << "	shiplist_planet_id='" << fleet_["fleet_entity_to"] << "' ";
						query << "	AND shiplist_ship_id='" << it->sid << "';";
						query.store(),
						query.reset();
						
					}
					else
					{
						query << "INSERT INTO ";
						query << "	shiplist ";
						query << "(";
						query << "	shiplist_user_id, ";
						query << "	shiplist_planet_id, ";
						query << "	shiplist_ship_id, ";
						query << "	shiplist_count,";
						query << "	shiplist_special_ship, ";
						query << "	shiplist_special_ship_level, ";
						query << "	shiplist_special_ship_exp, ";
						query << "	shiplist_special_ship_bonus_weapon, ";
						query << "	shiplist_special_ship_bonus_structure, ";
						query << "	shiplist_special_ship_bonus_shield, ";
						query << "	shiplist_special_ship_bonus_heal, ";
						query << "	shiplist_special_ship_bonus_capacity, ";
						query << "	shiplist_special_ship_bonus_speed, ";
						query << "	shiplist_special_ship_bonus_pilots, ";
						query << "	shiplist_special_ship_bonus_tarn, ";
						query << "	shiplist_special_ship_bonus_antrax, ";
						query << "	shiplist_special_ship_bonus_forsteal, ";
						query << "	shiplist_special_ship_bonus_build_destroy, ";
						query << "	shiplist_special_ship_bonus_antrax_food, ";
						query << "	shiplist_special_ship_bonus_deactivade ";
						query << ") ";
						query << "VALUES ";
						query << "(";
						query << "	'" << defender->userId << "', ";
						query << "	'" << fleet_["fleet_entity_to"] << "', ";
						query << "	'" << it->sid << "', ";
						query << "	'" << it->newCnt << "', ";
						query << "	'1', ";
						query << "	'" << it->shipLevel << "', ";
						query << "	'" << it->shipExp << "', ";
						query << "	'" << it->shipsBonusWeapon << "', ";
						query << "	'" << it->shipBonusStructure << "', ";
						query << "	'" << it->shipsBonusShield << "', ";
						query << "	'" << it->shipBonusHeal << "', ";
						query << "	'" << it->shipBonusCapacity << "', ";
						query << "	'" << it->shipsBonusSpeed << "', ";
						query << "	'" << it->shipsBonusPilots << "', ";
						query << "	'" << it->shipsBonusTarn << "', ";
						query << "	'" << it->shipBonusAntrax << "', ";
						query << "	'" << it->shipsBonusForsteal << "',";
						query << "	'" << it->shipsBonusDestroy << "', ";
						query << "	'" << it->shipsBonusAntraxFood << "', ";
						query << "	'" << it->shipBonusDeactivade << "' ";
						query << ");";
					}
				}
			}
		}
		if (special)
		{
			defMsg += "\nDie Spezialschiffe von [B]";
			defMsg += functions::getUserNick(defender->userId);
			defMsg += "[/B] erhalten ";
			defMsg += functions::nf(functions::d2s(defender->newExpInit));
			defMsg += " EXP!\n\n";
			defMsg += "\nGewonnene EXP: ";
			defMsg += functions::nf(functions::d2s(defender->newExpInit));
			defMsg += "\n\n";
		}
	
		for ( it = defender->objects.begin() ; it < defender->objects.end(); it++ )
		{
			if (it->newCnt > 0)
			{
				query << "SELECT ";
				query << "	shiplist_ship_id ";
				query << "FROM ";
				query << "	shiplist ";
				query << "WHERE ";
				query << "	shiplist_planet_id='" << fleet_["fleet_entity_to"] << "' ";
				query << "	AND shiplist_ship_id='" << it->sid << "';";
				mysqlpp::Result slRes = query.store();
				query.reset();
				
				if (slRes)
				{
					int slSize = slRes.size();
					
					if (slSize > 0)
					{
						query << "UPDATE ";
						query << "	shiplist ";
						query << "SET ";
						query << "	shiplist_count='" << it->newCnt << "' ";
						query << "WHERE ";
						query << "	shiplist_planet_id='" << fleet_["fleet_entity_to"] << "' ";
						query << "	AND shiplist_ship_id='" << it->sid << "';";
						query.store();
						query.reset();
					}
					else
					{
						query << "INSERT INTO ";
						query << "	shiplist ";
						query << "(";
						query << "	shiplist_user_id, ";
						query << "	shiplist_planet_id, ";
						query << "	shiplist_ship_id, ";
						query << "	shiplist_count ";
						query << ") ";
						query << "VALUES ";
						query << "( ";
						query << "	'" << defender->userId << "', ";
						query << "	'" << fleet_["fleet_entity_to"] << "', ";
						query << "	'" << it->sid << "', ";
						query << "	'" << it->newCnt << "' ";
						query << ");";
						query.store();
						query.reset();
					}
				}
			}
			defMsg += it->name;
			defMsg += " ";
			defMsg += functions::d2s(it->newCnt);
			defMsg += "\n";
		}
		if (defender->objects.size() == 0)
	    {
            defMsg += "[i]Nichts vorhanden![/i]\n";
        }
	
		defMsg += "\n[b]VERTEIDIGUNG:[/b]\n";
		for ( it = defender->defObjects.begin() ; it < defender->defObjects.end(); it++ )
		{
			if (it->newCnt > 0)
			{
				query << "SELECT ";
				query << "	deflist_def_id ";
				query << "FROM ";
				query << " deflist ";
				query << "WHERE ";
				query << "	deflist_planet_id='" << fleet_["fleet_entity_to"] << "' ";
				query << "	AND deflist_def_id='" << it->sid << "';";
				mysqlpp::Result dlRes = query.store();
				query.reset();
			
				if (dlRes)
				{
					int dlSize = dlRes.size();
				
					if (dlSize > 0)
					{
						query << "UPDATE ";
						query << "	deflist ";
						query << "SET ";
						query << "	deflist_count='" << it->newCnt << "' ";
						query << "WHERE ";
						query << "	deflist_planet_id='" << fleet_["fleet_entity_to"] << "' ";
						query << "	AND deflist_def_id='" << it->sid << ";";
						query.store();
						query.reset();
					}
					else
					{
						query << "INSERT INTO ";
						query << "	deflist ";
						query << "(";
						query << "	deflist_user_id, ";
						query << "	deflist_planet_id, ";
						query << "	deflist_def_id, ";
						query << "	deflist_count ";
						query << ")";
						query << "VALUES ";
						query << "(";
						query << "	'" << defender->userId << "', ";
						query << "	'" << fleet_["fleet_entity_to"] << "', ";
						query << "	'" << it->sid << "', ";
						query << "	'" << it->newCnt << "'";
						query << ");";
						query.store();
						query.reset();
					}
				}
			}
			defMsg += it->name;
			defMsg += " ";
			defMsg += functions::d2s(it->newCnt);
			defMsg += "\n";
		}
		if (defender->defObjects.size() == 0)
	    {
            defMsg += "[i]Nichts vorhanden![/i]\n";
        }
		
		if (defender->cCount == 0)
		{
			for ( it = defender->objects.begin() ; it < defender->objects.end(); it++ )
			{
				query << "UPDATE ";
				query << "	shiplist ";
				query << "SET ";
				query << "	shiplist_special_ship_level='0', ";
				query << "	shiplist_special_ship_exp='0', ";
				query << "	shiplist_special_ship_bonus_weapon='0', ";
				query << "	shiplist_special_ship_bonus_structure='0', ";
				query << "	shiplist_special_ship_bonus_shield='0', ";
				query << "	shiplist_special_ship_bonus_heal='0', ";
				query << "	shiplist_special_ship_bonus_capacity='0', ";
				query << "	shiplist_special_ship_bonus_speed='0', ";
				query << "	shiplist_special_ship_bonus_pilots='0', ";
				query << "	shiplist_special_ship_bonus_tarn='0', ";
				query << "	shiplist_special_ship_bonus_antrax='0', ";
				query << "	shiplist_special_ship_bonus_forsteal='0', ";
				query << "	shiplist_special_ship_bonus_build_destroy='0', ";
				query << "	shiplist_special_ship_bonus_antrax_food='0', ";
				query << "	shiplist_special_ship_bonus_deactivade='0' ";
				query << "WHERE ";
				query << "	shiplist_user_id='" << defender->userId << "' ";
				query << "	AND shiplist_ship_id='" << it->sid << "';";
				query.store(),
				query.reset();
			}
		}
		

		//
		//Der Angreifer hat gewonnen!
		//

		if (defender->cCount == 0 && attacker->cCount > 0)
		{
			returnV = 1;
			
			raidR.resize(5);
			raidRToShip.resize(5);

			msg += "Der Angreifer hat den Kampf gewonnen!\n\n";

            //Kapazität der überlebenden Schiffe rechnen
            capa *= specialShipBonusCapacity;

            resRaidFactor = 0.5;
            if (dontSteal != 1 && shipSteal!=50)
                resRaidFactor = shipSteal;

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
			query << "	id='" << fleet_["fleet_entity_to"] << "';";
			mysqlpp::Result rpRes = query.store();
			query.reset();
			
			if (rpRes)
			{
				int rpSize = rpRes.size();
				
				if (rpSize > 0)
				{
					mysqlpp::Row rpRow = rpRes.at(0);
					

					raidR[0] = rpRow["planet_res_metal"]	* resRaidFactor;
					raidR[1] = rpRow["planet_res_crystal"]	* resRaidFactor;
					raidR[2] = rpRow["planet_res_plastic"]	* resRaidFactor;
					raidR[3] = rpRow["planet_res_fuel"]		* resRaidFactor;
					raidR[4] = rpRow["planet_res_food"]		* resRaidFactor;
				}
			}
		
			double sum = raidR[0] + raidR[1] + raidR[2] + raidR[3] + raidR[4];
			
            for (int rcnt = 0; rcnt < raidR.size(); rcnt++)
            {
                if (capa <= sum)
                    raidRToShip[rcnt] += round(raidR[rcnt] * capa / sum);
                else
                    raidRToShip[rcnt] += round(raidR[rcnt]);
            }

            query << "UPDATE ";
			query << "	fleet ";
			query << "SET ";
			query << "	fleet_res_metal=fleet_res_metal+'" << raidRToShip[0] << "', ";
			query << "	fleet_res_crystal=fleet_res_crystal+'" << raidRToShip[1] << "', ";
			query << "	fleet_res_plastic=fleet_res_plastic+'" << raidRToShip[2] << "', ";
			query << "	fleet_res_fuel=fleet_res_fuel+'" << raidRToShip[3] << "', ";
			query << "	fleet_res_food=fleet_res_food+'" << raidRToShip[4] << "' ";
			query << "WHERE ";
			query << "	fleet_id='" << fleet_["fleet_id"] << "';";	
			query.store();
			query.reset();

            query << "UPDATE ";
			query << "	planets ";
			query << "SET ";
			query << "	planet_res_metal=planet_res_metal-'" << raidRToShip[0] << "', ";
			query << "	planet_res_crystal=planet_res_crystal-'" << raidRToShip[1] << "', ";
			query << "	planet_res_plastic=planet_res_plastic-'" << raidRToShip[2] << "', ";
			query << "	planet_res_fuel=planet_res_fuel-'" << raidRToShip[3] << "', ";
			query << "	planet_res_food=planet_res_food-'" << raidRToShip[4] << "' ";
			query << "WHERE ";
			query << "	id='" << fleet_["fleet_id"] << "';";
			query.store();
			query.reset();

            //Erbeutete Rohstoffsumme speichern
            query << "UPDATE ";
			query << "	users ";
			query << "SET ";
			query << "	user_res_from_raid=user_res_from_raid+'" << sum << "' ";
			query << "WHERE ";
			query << "	user_id='" << attacker->userId << "';";
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
		else if (attacker->cCount==0 && defender->cCount>0)
		{
			returnV = 2;
			msg += "Der Verteidiger hat den Kampf gewonnen!\n\n";

			//löscht die angreiffende flotte
			query << "DELETE FROM ";
			query << "	fleet ";
			query << "WHERE ";
			query << "	fleet_id='" << fleet_["fleet_id"] << "';";
			query.store();
			query.reset();
		}

		//
		//Der Kampf endete unentschieden
		//
		else
		{

			//
			//	Unentschieden, beide Flotten wurden zerstört
			//
			if (attacker->cCount==0 && defender->cCount==0)
			{
        		returnV = 3;
				msg += "Der Kampf endete unentschieden, da sowohl die Einheiten des Angreifes als auch die Einheiten des Verteidigers alle zerstört wurden!\n\n";
            
				//löscht die angreiffende flotte
				query << "DELETE FROM ";
				query << "	fleet ";
				query << "WHERE ";
				query << "	fleet_id='" << fleet_["fleet_id"] << "';";
				query.store();
				query.reset();
			}

			//
			//	Unentschieden, beide Flotten haben überlebt
			//
			else
			{
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
		msg += defMsg;
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

		delete attacker, defender;
	}
}
