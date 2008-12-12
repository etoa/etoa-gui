
#include <mysql++/mysql++.h>

#include "SupportHandler.h"
#include "../../MysqlHandler.h"
#include "../../functions/Functions.h"
#include "../../config/ConfigHandler.h"

namespace support
{
	void SupportHandler::update()
	{
	
		/**
		* Fleet-Action: Position
		*/
		
		Config &config = Config::instance();
		
		//Support beenden und Flotte nach Hause schicken
		if (this->f->getStatus()==3) {
			mysqlpp::Query query = con_->query();
			query << "UPDATE ";
			query << "	fleet ";
			query << "SET ";
			query << "	entity_to=next_id, ";
			query << "	next_id=0, ";
			query << "	landtime=launchtime+nextactiontime, ";
			query << "	launchtime=landtime, ";
			query << "	nextactiontime='0', ";
			query << "	res_fuel='0', ";
			query << "	status='1' ";
			query << "WHERE ";
			query << "	id='" << this->f->getId() << "';";
			query.store();
			query.reset();
			
			//Nachricht senden Flotteninhaber
			this->msg = "[b]SUPPORT BEENDET[/b]\n\nEine eurer Flotten hat hat ihr Ziel verlassen und macht sich nun auf den Rückweg!\n\n[b]Zielplanet:[/b] ";
			this->msg += this->f->getEntityToString(0);
			this->msg += "\n[b]Startplanet:[/b] ";
			this->msg += this->f->getEntityFromString(0);
			this->msg += "\n[b]Zeit:[/b] ";
			this->msg += this->f->getLandtimeString();
			this->msg += "\n[b]Auftrag:[/b] ";
			this->msg += this->f->getActionString();
			
			functions::sendMsg(this->f->getUserId(),(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Supportflotte Rückflug",this->msg);
							
			if (this->f->getEntityToUserId() != this->f->getUserId()) {
				//Nachricht senden Flotteninhaber
				this->msg = "[b]SUPPORT BEENDET[/b]\n\nEine Flotte hat hat ihr Ziel verlassen und macht isch nun auf den Rückweg!\n\n[b]Zielplanet:[/b] ";
				this->msg += this->f->getEntityToString(0);
				this->msg += "\n[b]Startplanet:[/b] ";
				this->msg += this->f->getEntityFromString(0);
				this->msg += "\n[b]Zeit:[/b] ";
				this->msg += this->f->getLandtimeString();
				this->msg += "\n[b]Auftrag:[/b] ";
				this->msg += this->f->getActionString();
				this->msg += "\n[b]User:[/b] ";
				this->msg += this->f->getEntityToUserId();
				functions::sendMsg(this->f->getEntityToUserId(),(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Supportflotte Rückflug",this->msg);
			}
		}
		
		else {
			//Support beginnen
			this->flyingHomeTime = this->f->getLandtime() - this->f->getLaunchtime();;
		
			// Precheck action==possible?
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "	owner.user_alliance_id AS oAId, ";
			query << "	owner.user_id, ";
			query << "	owner.user_nick, ";
			query << "	fleet.user_alliance_id AS fAId ";
			query << "FROM ";
			query << "	users AS owner ";
			query << "INNER JOIN ";
			query << "	planets ";
			query << "ON ";
			query << "	owner.user_id = planets.planet_user_id ";
			query << "	AND planets.id='" << this->f->getEntityTo() << "' ";
			query << "LEFT JOIN ";
			query << "	users AS fleet ";
			query << "ON ";
			query << "	fleet.user_id ='" << this->f->getUserId() << "';";
			mysqlpp::Result checkRes = query.store();
			query.reset();
		
			if (checkRes) {
				int checkSize = checkRes.size();
				
				if (checkSize > 0) {
					mysqlpp::Row checkRow = checkRes.at(0);
				
					if ((int)checkRow["oAId"] == (int)checkRow["fAId"] && (int)checkRow["fAId"] > 0) {

						this->landtime = this->f->getLandtime() + this->f->getNextactiontime();
							
						query << "UPDATE ";
						query << "	fleet ";
						query << "SET ";
						query << "	next_id=entity_from, ";
						query << "	entity_from=entity_to, ";
						query << "	nextactiontime='" << this->flyingHomeTime << "', ";
						query << "	launchtime=landtime, ";
						query << "	landtime='" << this->landtime << "', ";
						query << "	status='3' ";
						query << "WHERE ";
						query << "	id='" << this->f->getId() << "';";
						query.store();
						query.reset();

						//Nachricht senden Flotteninhaber
						this->msg = "[b]SUPPORTFLOTTE ANGEKOMMEN[/b]\n\nEine eurer Flotten hat hat ihr Ziel erreicht!\n\n[b]Zielplanet:[/b] ";
						this->msg += this->f->getEntityToString(0);
						this->msg += "\n[b]Startplanet:[/b] ";
						this->msg += this->f->getEntityFromString(0);
						this->msg += "\n[b]Zeit:[/b] ";
						this->msg += this->f->getLandtimeString();
						this->msg += "\n[b]Auftrag:[/b] ";
						this->msg += this->f->getActionString();
						this->msg += "\n[b]Voraussichtliches Ende:[/b] ";
						this->msg += functions::formatTime(this->flyingHomeTime);
						
						functions::sendMsg(this->f->getUserId(),(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Supportflotte angekommen",this->msg);
						
						if ((int)checkRow["user_id"] != this->f->getUserId()) {
							//Nachricht senden Planeteninhaber
							this->msg = "[b]SUPPORTFLOTTE ANGEKOMMEN[/b]\n\nEine Flotte hat hat ihr Ziel erreicht!\n\n[b]Zielplanet:[/b] ";
							this->msg += this->f->getEntityToString(0);
							this->msg += "\n[b]Startplanet:[/b] ";
							this->msg += this->f->getEntityFromString(0);
							this->msg += "\n[b]Zeit:[/b] ";
							this->msg += this->f->getLandtimeString();
							this->msg += "\n[b]Auftrag:[/b] ";
							this->msg += this->f->getActionString();
							this->msg += "\n[b]Voraussichtliches Ende:[/b] ";
							this->msg += functions::formatTime(this->flyingHomeTime);
							this->msg += "\n[b]User:[/b] ";
							this->msg += std::string(checkRow["user_nick"]);
							functions::sendMsg((int)checkRow["user_nick"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Supportflotte angekommen",this->msg);
						}
					}
					
					else {
						//Nachricht senden Flotteninhaber
						this->msg = "[b]FLOTTE LANDEN FEHLGESCHLAGEN[/b]\n\nEine eurer Flotten konnte nicht auf ihrem Ziel landen!\n\n[b]Zielplanet:[/b] ";
							this->msg += this->f->getEntityToString(0);
							this->msg += "\n[b]Startplanet:[/b] ";
							this->msg += this->f->getEntityFromString(0);
							this->msg += "\n[b]Zeit:[/b] ";
							this->msg += this->f->getLandtimeString();
							this->msg += "\n[b]Auftrag:[/b] ";
							this->msg += this->f->getActionString();
			
						functions::sendMsg(this->f->getUserId(),(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Supportflug fehlgeschlagen",this->msg);
					
					}
				}
			}
		}
	}
}
