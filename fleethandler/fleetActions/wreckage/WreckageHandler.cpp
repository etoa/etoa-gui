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
		
		// Precheck action==possible?
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	ship_id ";
		query << "FROM ";
		query << "	fleet_ships ";
		query << "INNER JOIN ";
		query << "	ships ON fs_ship_id = ship_id ";
		query << "	AND fs_fleet_id='" << f->getId() << "' ";
		query << "	AND fs_ship_faked='0' ";
		query << "	AND (";
		query << "		ship_actions LIKE '%," << f->getAction() << "'";
		query << "		OR ship_actions LIKE '" << f->getAction() << ",%'";
		query << "		OR ship_actions LIKE '%," << f->getAction() << ",%'";
		query << "		OR ship_actions LIKE '" << f->getAction() << "');";
		mysqlpp::Result fsRes = query.store();
		query.reset();
					
		if (fsRes) {
			int fsSize = fsRes.size();
			
			if (fsSize > 0) {
				// Calculate the fleet capacity
				query << "SELECT ";
				query << "	SUM(fs_ship_cnt*ship_capacity) as capa ";
				query << "FROM ";
				query << "	fleet_ships ";
				query << "INNER JOIN ";
				query << "	ships ON fs_ship_id = ship_id ";
				query << "	AND fs_fleet_id='" << f->getId() << "' ";
				query << "	AND fs_ship_faked='0';";
				mysqlpp::Result capaRes = query.store();
				query.reset();
				
				if (capaRes) {
					int capaSize = capaRes.size();
					
					if (capaSize > 0) {
						mysqlpp::Row capaRow = capaRes.at(0);
						
						this->capa = (double)capaRow["capa"] - f->getResLoaded();
					}
				}
				
				// Load the wreckage field
				query << std::setprecision(18);
				query << "SELECT ";
				query << "	planet_wf_metal, ";
				query << "	planet_wf_crystal, ";
				query << "	planet_wf_plastic ";
				query << "FROM ";
				query << "	planets ";
				query << "WHERE ";
				query << "	id='" << f->getEntityTo() << "';";
				mysqlpp::Result wfRes = query.store();
				query.reset();
		
				if (wfRes) {
					int wfSize = wfRes.size();
			
					if (wfSize > 0) {
						mysqlpp::Row wfRow = wfRes.at(0);
						this->metal = (double)wfRow["planet_wf_metal"];
						this->crystal = (double)wfRow["planet_wf_crystal"];
						this->plastic = (double)wfRow["planet_wf_plastic"];
						this->sum = this->metal + this->crystal + this->plastic;
					}
				}

				// Check if there is a field
				if (this->sum>0) {
					// Calculate the collected resources
					if (this->capa <= this->sum) {
						this->percent = this->capa / this->sum;
						this->metal = round(this->metal * this->percent);
						this->crystal = round(this->crystal * this->percent);
						this->plastic = round(this->plastic * this->percent);
						this->sum = this->metal + this->crystal + this->plastic;
					}
					else {
						this->metal = round(this->metal);
						this->crystal = round(this->crystal);
						this->plastic = round(this->plastic);
					}
	
					// Update the field
					query << "UPDATE ";
					query << "	planets ";
					query << "SET ";
					query << "	planet_wf_metal=planet_wf_metal-'" << this->metal << "', ";
					query << "	planet_wf_crystal=planet_wf_crystal-'" << this->crystal << "', ";
					query << "	planet_wf_plastic=planet_wf_plastic-'" << this->plastic << "' ";
					query << "WHERE ";
					query << "	id='" << f->getEntityTo() << "';";
					query.store();
					query.reset();
		
					// Add collected resources to the fleet
					this->metal += (double)fleet_["res_metal"];
					this->crystal += (double)fleet_["res_crystal"];
					this->plastic += (double)fleet_["res_plastic"];
	
					// Send fleet back home again
					fleetReturn(1,this->metal,this->crystal,this->plastic,-1,-1,-1);
	
					// Send a message to the user
					std::string msg = "[b]TR&Uuml;MMERSAMMLER-RAPPORT[/b]\n\nEine Flotte vom Planeten \n[b]";
					msg += f->getEntityFromString();
					msg += "[/b]\nhat das Tr&uuml;mmerfeld bei \n[b]";
					msg += f->getEntityToString();
					msg += "[/b]\num [b]";
					msg += f->getLandtimeString();
					msg += "[/b]\n erreicht und Tr&uuml;mmer gesammelt.\n";
		
					msgRes = "\n[b]ROHSTOFFE:[/b]\n\nTitan: ";
					msgRes += functions::nf(functions::d2s(this->metal));
					msgRes += "\nSilitium: ";
					msgRes += functions::nf(functions::d2s(this->crystal));
					msgRes += "\nPVC: ";
					msgRes += functions::nf(functions::d2s(this->plastic));
					msg += msgRes;
		
					functions::sendMsg(f->getUserId(),(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Tr&uuml;mmer gesammelt",msg);	
	
					// Update collected resources for the userstatistic
					query << "UPDATE ";
					query << "	users ";
					query << "SET ";
					query << "	user_res_from_tf=user_res_from_tf+'" << this->sum << "' ";
					query << "WHERE ";
					query << "	user_id='" << f->getUserId() << "';";
					query.store();
					query.reset();  
	
					// Add a log
					std::string log = "Eine Flotte des Spielers [B]";
					log += functions::getUserNick(f->getUserId());
					log += "[/B] vom Planeten [b]";
					log += f->getEntityFromString();
					log += "[/b] hat das Tr&uuml;mmerfeld bei [b]";
					log += f->getEntityToString();
					log += "[/b] um [b]";
					log += f->getLandtimeString();
					log += "[/b] erreicht und Tr&uuml;mmer gesammelt.\n";
					log += msgRes;
					functions::addLog(13,log,(int)time);
				}
				
				// If the field is empty
				else {
					// Send fleet back home again
					fleetReturn(1);
	
					// Send a message to the user
					std::string msg = "[b]TRÜMMERSAMMLER-RAPPORT[/b]\n\nEine Flotte vom Planeten \n[b]";
					msg += f->getEntityFromString();
					msg += "[/b]\nhat das Tr&uuml;mmerfeld bei \n[b]";
					msg += f->getEntityToString();
					msg += "[/b]\num [b]";
					msg += f->getLandtimeString();
					msg += "[/b]\n erreicht.\n\n";
					msgRes = "Es wurden aber leider keine brauchbaren Trümmerteile mehr gefunden so dass die Flotte unverrichteter Dinge zurückkehren musste.";
					msg += msgRes;
				
					functions::sendMsg(f->getUserId(),(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Tr&uuml;mmer gesammelt",msg);
				}
			}
			
			// If there is no wreckage collecter in the fleet
			else {
				std::string text = "Eine Flotte vom Planeten ";
				text += f->getEntityFromString();
				text += " versuchte, Trümmer zu sammeln. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
				functions::sendMsg(f->getUserId(),(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Trümmersammeln gescheitert",text);
				
				fleetReturn(1);
			}
		}
	}
}
