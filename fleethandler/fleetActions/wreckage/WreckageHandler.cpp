#include <iostream>
#include <math.h>

#include <time.h>
#include <mysql++/mysql++.h>

#include "WreckageHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

namespace wreckage
{
	void WreckageHandler::update()
	{
	
		/**
		* Fleet-Action: Collect wreckage/debris field
		*/ 
		
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		std::string action = "collectdebris";
		
		// Precheck action==possible?
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	ship_id ";
		query << "FROM ";
		query << "	fleet_ships ";
		query << "INNER JOIN ";
		query << "	ships ON fs_ship_id = ship_id ";
		query << "	AND fs_fleet_id='" << fleet_["id"] << "' ";
		query << "	AND fs_ship_faked='0' ";
		query << "	AND (";
		query << "		ship_actions LIKE '%," << action << "'";
		query << "		OR ship_actions LIKE '" << action << ",%'";
		query << "		OR ship_actions LIKE '%," << action << ",%'";
		query << "		OR ship_actions LIKE '" << action << "');";
		mysqlpp::Result fsRes = query.store();
		query.reset();
		
					
		if (fsRes)
		{
			int fsSize = fsRes.size();
			
			if (fsSize > 0)
			{
				//Lade Kapazität
				query << "SELECT ";
				query << "	SUM(fs_ship_cnt*ship_capacity) as capa ";
				query << "FROM ";
				query << "	fleet_ships ";
				query << "INNER JOIN ";
				query << "	ships ON fs_ship_id = ship_id ";
				query << "	AND fs_fleet_id='" << fleet_["id"] << "' ";
				query << "	AND fs_ship_faked='0';";
				mysqlpp::Result capaRes = query.store();
				query.reset();
				
				if (capaRes)
				{
					int capaSize = capaRes.size();
					
					if (capaSize > 0)
					{
						mysqlpp::Row capaRow = capaRes.at(0);
						
						this->capa = (double)capaRow["capa"] - (double)fleet_["res_metal"] - (double)fleet_["res_crystal"] - (double)fleet_["res_plastic"] -(double)fleet_["res_fuel"] - (double)fleet_["res_food"];
					}
				}
				
				//Lädt Trümmerfeld Rohstoffe
				query << std::setprecision(18);
				query << "SELECT ";
				query << "	planet_wf_metal, ";
				query << "	planet_wf_crystal, ";
				query << "	planet_wf_plastic ";
				query << "FROM ";
				query << "	planets ";
				query << "WHERE ";
				query << "	id='" << fleet_["entity_to"] << "';";
				mysqlpp::Result wfRes = query.store();
				query.reset();
		
				if (wfRes)
				{
					int wfSize = wfRes.size();
			
					if (wfSize > 0)
					{
						mysqlpp::Row wfRow = wfRes.at(0);
						this->metal = (double)wfRow["planet_wf_metal"];
						this->crystal = (double)wfRow["planet_wf_crystal"];
						this->plastic = (double)wfRow["planet_wf_plastic"];
						this->sum = this->metal + this->crystal + this->plastic;
					}
				}

				// Prüfen ob TF nicht leer
				if (this->sum>0)
				{
					//Rohstoffe prozentual aufteilen, wenn die Kapazität nicht für das ganze TF reicht
					if (this->capa <= this->sum)
					{
						this->percent = this->capa / this->sum;
						this->metal = round(this->metal * this->percent);
						this->crystal = round(this->crystal * this->percent);
						this->plastic = round(this->plastic * this->percent);
						this->sum = this->metal + this->crystal + this->plastic;
					}
					else
					{
						this->metal = round(this->metal);
						this->crystal = round(this->crystal);
						this->plastic = round(this->plastic);
					}
	
					// Rohstoffe vom Planeten abziehen
					query << "UPDATE ";
					query << "	planets ";
					query << "SET ";
					query << "	planet_wf_metal=planet_wf_metal-'" << this->metal << "', ";
					query << "	planet_wf_crystal=planet_wf_crystal-'" << this->crystal << "', ";
					query << "	planet_wf_plastic=planet_wf_plastic-'" << this->plastic << "' ";
					query << "WHERE ";
					query << "	id='" << fleet_["entity_to"] << "';";
					query.store();
					query.reset();
		
					//Summiert erhaltene Rohstoffe vom TF zu des Ladung
					this->metal += (double)fleet_["res_metal"];
					this->crystal += (double)fleet_["res_crystal"];
					this->plastic += (double)fleet_["res_plastic"];
	
					// Flotte zurückschicken mit Ress von TF und bestehenden ress
					fleetReturn(1,this->metal,this->crystal,this->plastic,-1,-1,-1);
	
					// Nachricht senden
					std::string msg = "[b]TR&Uuml;MMERSAMMLER-RAPPORT[/b]\n\nEine Flotte vom Planeten \n[b]";
					msg += functions::formatCoords((int)fleet_["entity_from"],0);
					msg += "[/b]\nhat das Tr&uuml;mmerfeld bei \n[b]";
					msg += functions::formatCoords((int)fleet_["entity_to"],0);
					msg += "[/b]\num [b]";
					msg += functions::formatTime((int)fleet_["landtime"]);
					msg += "[/b]\n erreicht und Tr&uuml;mmer gesammelt.\n";
		
					msgRes = "\n[b]ROHSTOFFE:[/b]\n\nTitan: ";
					msgRes += functions::nf(functions::d2s(this->metal));
					msgRes += "\nSilitium: ";
					msgRes += functions::nf(functions::d2s(this->crystal));
					msgRes += "\nPVC: ";
					msgRes += functions::nf(functions::d2s(this->plastic));
					msg += msgRes;
		
					functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Tr&uuml;mmer gesammelt",msg);	
	
					//Erbeutete Rohstoffsumme speichern
					query << "UPDATE ";
					query << "	users ";
					query << "SET ";
					query << "	user_res_from_tf=user_res_from_tf+'" << this->sum << "' ";
					query << "WHERE ";
					query << "	user_id='" << fleet_["user_id"] << "';";
					query.store();
					query.reset();  
	
					//Log schreiben
					std::string log = "Eine Flotte des Spielers [B]";
					log += functions::getUserNick((int)fleet_["user_id"]);
					log += "[/B] vom Planeten [b]";
					log += functions::formatCoords((int)fleet_["entity_from"],0);
					log += "[/b] hat das Tr&uuml;mmerfeld bei [b]";
					log += functions::formatCoords((int)fleet_["entity_to"],0);
					log += "[/b] um [b]";
					log += functions::formatTime((int)fleet_["landtime"]);
					log += "[/b] erreicht und Tr&uuml;mmer gesammelt.\n";
					log += msgRes;
					functions::addLog(13,log,(int)time);
				}
				// TF ist leer...
				else
				{
					// Flotte zurückschicken 
					fleetReturn(1);
	
					// Nachricht senden
					std::string msg = "[b]TRÜMMERSAMMLER-RAPPORT[/b]\n\nEine Flotte vom Planeten \n[b]";
					msg += functions::formatCoords((int)fleet_["entity_from"],0);
					msg += "[/b]\nhat das Tr&uuml;mmerfeld bei \n[b]";
					msg += functions::formatCoords((int)fleet_["entity_to"],0);
					msg += "[/b]\num [b]";
					msg += functions::formatTime((int)fleet_["landtime"]);
					msg += "[/b]\n erreicht.\n\n";
					msgRes = "Es wurden aber leider keine brauchbaren Trümmerteile mehr gefunden so dass die Flotte unverrichteter Dinge zurückkehren musste.";
					msg += msgRes;
				
					functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Tr&uuml;mmer gesammelt",msg);
				}
			}
			else
			{
				std::string text = "Eine Flotte vom Planeten ";
				text += functions::formatCoords((int)fleet_["entity_from"],0);
				text += " versuchte, Trümmer zu sammeln. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
				functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Trümmer gescheitert",text);
				
				fleetReturn(1);
			}
		}
	}
}
