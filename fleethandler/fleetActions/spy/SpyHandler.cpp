#include <iostream>
#include <vector>
#include <math.h>
#include <time.h>

#include <mysql++/mysql++.h>

#include "SpyHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"
#include "../../objectData/ObjectHandler.h"
#include "../../objectData/ObjectDataHandler.h"

namespace spy
{
	void SpyHandler::update()
	{
	
		/**
		* Fleet-Action: Spy
		*/
		
		/** Init Data **/
		Config &config = Config::instance();
		objectData &objectData = objectData::instance();
		this->action = std::string(this->fleet_["action"]);	
	
		this->userToId = functions::getUserIdByPlanet((int)fleet_["entity_to"]);
		std::string coordsBlank = functions::formatCoords((int)fleet_["entity_to"],1);
		std::string coordsTarget = functions::formatCoords((int)fleet_["entity_to"],0);
		std::string coordsFrom = functions::formatCoords((int)fleet_["entity_from"],0);

		/** Load tech levels first agressor, needs a value higher then 0 for one of them, cause /0 **/
		this->spyLevelAtt = 1e-2;
		this->tarnLevelAtt = 0;
		
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	techlist_current_level, ";
		query << "	techlist_tech_id ";
		query << "FROM ";
		query << "	techlist ";
		query << "WHERE ";
		query << "	techlist_user_id='" << fleet_["user_id"] << "' ";
		query << "	AND (techlist_tech_id='" << config.idget("SPY_TECH_ID") << "' ";
		query << "	OR techlist_tech_id='" << config.idget("TARN_TECH_ID") << "');";
		mysqlpp::Result levelRes = query.store();
		query.reset();
		
		if (levelRes) {
			int levelSize = levelRes.size();
			
			if (levelSize > 0) {
		
				mysqlpp::Row levelRow = levelRes.at(0);
				
	    		for (mysqlpp::Row::size_type i = 0; i<levelSize; i++) {
	    			levelRow = levelRes.at(i);

					if ((int)levelRow["techlist_tech_id"] == config.idget("SPY_TECH_ID")) {
						this->spyLevelAtt = (int)levelRow["techlist_current_level"];
					}
					else if((int)levelRow["techlist_tech_id"] == config.idget("TARN_TECH_ID")) {
						this->tarnLevelAtt = (int)levelRow["techlist_current_level"];
					}
				}
			}
		}

		/** Then load the tech levels of the victim **/
		this->spyLevelDef = 0;
		this->tarnLevelDef = 0;
		
		query << "SELECT ";
		query << "	techlist_current_level, ";
		query << "	techlist_tech_id ";
		query << "FROM ";
		query << "	techlist ";
		query << "WHERE ";
		query << "	techlist_user_id='" << userToId << "' ";
		query << "	AND (techlist_tech_id='" << config.idget("SPY_TECH_ID") << "' ";
		query << "	OR techlist_tech_id='" << config.idget("TARN_TECH_ID") << "');";
		levelRes = query.store();
		query.reset();
		
		if (levelRes) {
			int levelSize = levelRes.size();
			
			if (levelSize > 0) {
		
				mysqlpp::Row levelRow = levelRes.at(0);
				
	    		for (mysqlpp::Row::size_type i = 0; i<levelSize; i++) {
	    			levelRow = levelRes.at(i);

					if ((int)levelRow["techlist_tech_id"] == config.idget("SPY_TECH_ID")) {
						this->spyLevelDef = (int)levelRow["techlist_current_level"];
					}
					else if ((int)levelRow["techlist_tech_id"] == config.idget("TARN_TECH_ID")) {
						this->tarnLevelDef = (int)levelRow["techlist_current_level"];
					}
				}
			}
		}

		/** Load spy ships agressor **/
		this->spyShipsAtt = 0;
		
		query << "SELECT ";
		query << "	SUM(fs_ship_cnt) as cnt ";
		query << "FROM ";
		query << "	fleet_ships ";
		query << "INNER JOIN ";
		query << "	ships ";
		query << "	ON fs_ship_id=ship_id ";
		query << "	AND fs_fleet_id=" << fleet_["id"] << " ";
		query << "	AND ship_actions LIKE '%" << this->action << "%'";
		query << "GROUP BY ";
		query << "	fs_ship_cnt;";
		mysqlpp::Result spyRes = query.store();
		query.reset();
		
		if (spyRes)
		{
			int spySize = spyRes.size();
			
			if (spySize > 0)
			{
				mysqlpp::Row spyRow = spyRes.at(0);
				this->spyShipsAtt = spyRow["cnt"];
			}
		}

		/** Load spy ships defender or sometimes victim **/
		this->spyShipsDef = 0;
		
		query << "SELECT ";
		query << "	SUM(shiplist_count) AS cnt ";
		query << "FROM ";
		query << "	shiplist ";
		query << "INNER JOIN ";
		query << "	ships ";
		query << "	ON shiplist_ship_id=ship_id ";
		query << "	AND shiplist_entity_id=" << fleet_["entity_to"] << " ";
		query << "	AND ship_actions LIKE '%" << this->action << "%'";
		query << "GROUP BY ";
		query << "	shiplist_count;";
		spyRes = query.store();
		query.reset();

		if (spyRes) {
			int spySize = spyRes.size();

			if (spySize > 0) {

				mysqlpp::Row spyRow = spyRes.at(0);

				this->spyShipsDef = (int)spyRow["cnt"];
			}
		}
		
		/** Add the support ships to the defender **/
		query << "SELECT ";
		query << "	SUM(fleet_ships.fs_ship_cnt) AS cnt ";
		query << "FROM ";
		query << "	fleet_ships ";
		query << "INNER JOIN ";
		query << "	fleet ";
		query << "	ON fleet.id=fleet_ships.fs_fleet_id ";
		query << "INNER JOIN ";
		query << "	ships ";
		query << "	ON fleet_ships.fs_ship_id=ships.ship_id ";
		query << "	AND ships.ship_actions LIKE '%" << this->action << "%'";
		query << "	AND fleet.action='support' ";
		query << "	AND fleet.entity_from='" << fleet_["entity_to"] << "' ";
		query << "	AND fleet.entity_to='" << fleet_["entity_to"] << "' ";
		query << "GROUP BY ";
		query << "	fleet_ships.fs_ship_cnt;";
		spyRes = query.store();
		query.reset();

		if (spyRes) {
			int spySize = spyRes.size();

			if (spySize > 0) {

				mysqlpp::Row spyRow = spyRes.at(0);

				this->spyShipsDef = (int)spyRow["cnt"];
			}
		}	
		/** If there are some spy ships in the fleet **/
		if (spyShipsAtt > 0) {
			/** Calculate the defense **/
			this->spyDefense1 = std::max(0.0,(this->spyLevelDef / (this->spyLevelAtt + this->tarnLevelAtt) * config.idget("SPY_DEFENSE_FACTOR_TECH")));
			this->spyDefense2 = std::max(0.0,((this->spyShipsDef / this->spyShipsAtt) * config.idget("SPY_DEFENSE_FACTOR_SHIPS")));
			this->spyDefense = std::min(this->spyDefense1 + this->spyDefense2,config.idget("SPY_DEFENSE_MAX"));
			this->defended = false;
			this->roll = rand() % 101;
		
			if (this->roll <= this->spyDefense) {
				this->defended = true;
			}
			
			if (!this->defended) {
				/** Calculate stealth bonus **/
				this->tarnDefense = std::max(0.0,std::min((this->tarnLevelDef / this->spyLevelAtt * config.idget("SPY_DEFENSE_FACTOR_TARN")),config.idget("SPY_DEFENSE_MAX")));
					
				/** Prepare the message header **/
				std::string topText = "[b]Planet:[/b] ";
				topText += coordsTarget;
				topText += "\n[b]Besitzer:[/b] ";
				topText += functions::getUserNick(this->userToId);
				topText += "\n";
				
				std::string text = "";

				/** If the spy tech level is high enough show the buildings **/
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_BUILDINGS") && (rand() % 101) > this->tarnDefense) {
					// Lädt Gebäudedaten
					query << "SELECT ";
					query << "	b.building_name, ";
					query << "	bl.buildlist_current_level ";
					query << "FROM ";
					query << "	buildings AS b ";
					query << "INNER JOIN ";
					query << "	buildlist AS bl ";
					query << "	ON bl.buildlist_building_id=b.building_id ";
					query << "	AND bl.buildlist_entity_id='" << fleet_["entity_to"] << "' ";
					query << "	AND bl.buildlist_user_id='" << this->userToId << "' ";
					query << "	AND buildlist_current_level>0 ";
					query << "ORDER BY ";
					query << "	b.building_name;";
					mysqlpp::Result bRes = query.store();
					query.reset();

					if (bRes) {
						int bSize = bRes.size();
						text += "\n[b]GEBÄUDE:[/b]\n";
						
						if (bSize > 0) {
							mysqlpp::Row bRow;
							text += "[table]";

							for (mysqlpp::Row::size_type i = 0; i<bSize; i++) {
								bRow = bRes.at(i);
								
								text += "[tr][td]";
								text += std::string(bRow["building_name"]);
								text += "[/td][td]";
								text += std::string(bRow["buildlist_current_level"]);
								text += "[/td][/tr]";
							}
							text += "[/table]";
						}
						else {
							text += "[i]Nichts vorhanden[/i]\n";
						}
					}
				}

				/** Same with the technologies **/
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_RESEARCH") && (rand() % 101) > this->tarnDefense) {
					query << "SELECT ";
					query << "	t.tech_name, ";
					query << "	tl.techlist_current_level ";
					query << "FROM ";
					query << "	technologies AS t ";
					query << "INNER JOIN ";
					query << "	techlist AS tl ";
					query << "	ON tl.techlist_tech_id=t.tech_id ";
					query << "	AND tl.techlist_user_id='" << this->userToId << "' ";
					query << "	AND techlist_current_level>0 ";
					query << "ORDER BY";
					query << "	t.tech_name;";
					mysqlpp::Result tRes = query.store();
					query.reset();
					
					if (tRes) {
						int tSize = tRes.size();
						text += "\n[b]TECHNOLOGIEN[/b]:\n";
						
						if (tSize > 0) {	
							mysqlpp::Row tRow;
							text += "[table]";
							
							for (mysqlpp::Row::size_type i = 0; i<tSize; i++) {
								tRow = tRes.at(i);
								
								text += "[tr][td]";
								text += std::string(tRow["tech_name"]);
								text += "[/td][td]";
								text += std::string(tRow["techlist_current_level"]);
								text += "[/td][/tr]";
							}
							text += "[/table]";
						}
						else {
							text += "[i]Nichts vorhanden[/i]\n";
						}
					}
				}
		
				/** Next to go are the ships **/
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_SHIPS") && (rand() % 101) > this->tarnDefense) {
					query << "SELECT ";
					query << "	shiplist_ship_id, ";
					query << "	shiplist_count ";
					query << "FROM ";
					query << "	shiplist ";
					query << "WHERE ";
					query << "	shiplist_entity_id='" << fleet_["entity_to"] <<  "' ";
					query << "	AND shiplist_count>'0';";
					mysqlpp::Result sRes = query.store();
					query.reset();
					
					if (sRes) {
						int sSize = sRes.size();
						text += "\n[b]SCHIFFE[/b]:\n";
						
						if (sSize > 0) {
							mysqlpp::Row sRow;
							text += "[table]";
	
							for (mysqlpp::Row::size_type i = 0; i<sSize; i++) {
								sRow = sRes.at(i);
								ObjectHandler::ObjectHandler object = objectData.get((int)sRow["shiplist_ship_id"],1);
								
								text += "[tr][td]";
								text += object.name();
								text +="[/td][td]";
								text += functions::nf(std::string(sRow["shiplist_count"]));
								text += "[/td][/tr]";
							}
							
							text += "[/table]";
						}
						else {
							text += "[i]Nichts vorhanden[/i]\n";
						}
					}
				}
		
				/** .., the defense, ... **/
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_DEFENSE") && (rand() % 101) > this->tarnDefense) {
					query << "SELECT ";
					query << "	deflist_def_id, ";
					query << "	deflist_count ";
					query << "FROM ";
					query << "	deflist ";
					query << "WHERE ";
					query << "	deflist_entity_id='" << fleet_["entity_to"] << "' ";
					query << "	AND deflist_count>0;";
					mysqlpp::Result dRes = query.store();
					query.reset();
					  
					if (dRes) {
						int dSize = dRes.size();
						text += "\n[b]VERTEIDIGUNG[/b]:\n";
							
						if (dSize > 0) {
							mysqlpp::Row dRow;
							text += "[table]";
	
							for (mysqlpp::Row::size_type i = 0; i<dSize; i++) {
								dRow = dRes.at(i);
								ObjectHandler::ObjectHandler object = objectData.get((int)dRow["deflist_def_id"],0);
								
								text += "[tr][td]";
								text += object.name();
								text +="[/td][td]";
								text += functions::nf(std::string(dRow["deflist_count"]));
								text += "[/td][/tr]";
							}
							
							text += "[/table]";
						}
						else {
							text += "[i]Nichts vorhanden[/i]\n";
						}
					}
				}
		
				/** and at last the resources on the planet **/
				if (this->spyLevelAtt >= config.idget("SPY_ATTACK_SHOW_RESSOURCEN") && (rand() % 101) > this->tarnDefense) {
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
					mysqlpp::Result pRes = query.store();
					query.reset();
						
					if (pRes) {
						int pSize = pRes.size();
						text += "\n[b]RESSOURCEN:[/b]\n";
						text += "[table]";
							
						if (pSize > 0) {
							mysqlpp::Row pRow = pRes.at(0);
					
							text += "[tr][td]Titan[/td][td]";
							text += functions::nf(std::string(pRow["planet_res_metal"]));
							text += "[/td][/tr]";
							
							text += "[tr][td]Silizium[/td][td]";
							text += functions::nf(std::string(pRow["planet_res_crystal"]));
							text += "[/td][/tr]";
								
							text += "[tr][td]PVC[/td][td]";
							text += functions::nf(std::string(pRow["planet_res_plastic"]));
							text += "[/td][/tr]";
								
							text += "[tr][td]Tritium[/td][td]";
							text += functions::nf(std::string(pRow["planet_res_fuel"]));
							text += "[/td][/tr]";
								
							text += "[tr][td]Nahrung[/td][td]";
							text += functions::nf(std::string(pRow["planet_res_food"]));
							text += "[/td][/tr]";
						}
							
						text += "[/table]";
					}
				}
				
				/** Finish the spy message **/
				if (text!="") {
					topText += text;
					topText += "\n\n[b]Spionageabwehr:[/b] ";
					topText += functions::d2s(round(this->spyDefense));
					topText += "%\n[b]Tarnung:[/b] ";
					topText += functions::d2s(round(this->tarnDefense));
					topText += "%";
				}
				else {
					topText += "\nDu konntest leider nichts über den Planeten herausfinden da deine Spionagetechnologie zu wenig weit entwickelt oder der Gegner zu gut getarnt ist!\n\n[b]Spionageabwehr:[/b] ";
					topText += functions::d2s(round(this->spyDefense));
					topText += "%\n[b]Tarnung:[/b] ";
					topText += functions::d2s(this->tarnDefense);
					topText += "%";
				}
		
				/** Send the spy message to the fleet user **/
				std::string subject = "Spionagebericht ";
				subject += coordsBlank;
				functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_SPY_MSG_CAT_ID"),subject,topText);
				
				/** drop a note to the victim, that someone dudeling around the planet **/
				std::string text2 = "Eine fremde Flotte vom Planeten ";
				text2 += coordsFrom;
				text2 += " wurde in der Nähe deines Planeten ";
				text2 += coordsTarget;
				text2 += " gesichtet!\n\n[b]Spionageabwehr:[/b] ";
				text2 += functions::d2s(round(this->spyDefense));
				text2 += "%";
				
				functions::sendMsg(userToId,(int)config.idget("SHIP_MONITOR_MSG_CAT_ID"),"Raumüberwachung",text2);
			}
			/** if the mission failed **/
			else {
				/** Send a message to the fleet user **/
				std::string text = "Dein Versuch, den Planeten ";
				text += coordsTarget;
				text += " auszuspionieren schlug fehl, da du entdeckt wurdest. Deine Sonden kehren ohne Ergebniss zurück!\n\n[b]Spionageabwehr:[/b] ";
				text += functions::d2s(round(this->spyDefense));
				text += "%";
				
				std::string subject = "Spionage fehlgeschlagen auf ";
				subject += coordsBlank;
				functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_SPY_MSG_CAT_ID"),subject,text);
		
				/** send a note to the planet user **/
				std::string text2 = "Auf deinem Planeten ";
				text2 += coordsTarget;
				text2 += " wurde ein Spionageversuch vom Planeten ";
				text2 += coordsFrom;
				text2 += " erfolgreich verhindert!\n\n[b]Spionageabwehr:[/b] ";
				text2 += functions::d2s(round(this->spyDefense));
				text2 += "%";
				
				functions::sendMsg(this->userToId,(int)config.idget("SHIP_MONITOR_MSG_CAT_ID"),"Raumüberwachung",text2);
			}
		}
		/** if there was no spy ship in the fleet **/
		else {
			/** Only send a message to the fleet user **/
			std::string text = "Dein Versuch, den Planeten ";
			text += coordsTarget;
			text += " auszuspionieren schlug fehl, da du keine Spionagesonden mitgeschickt hast!";
			
			std::string subject = "Spionage fehlgeschlagen auf ";
			subject += coordsBlank;
			functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_SPY_MSG_CAT_ID"),subject,text);
		}
	
		/** Send fleet back home again **/
		fleetReturn(1);
	}
}
