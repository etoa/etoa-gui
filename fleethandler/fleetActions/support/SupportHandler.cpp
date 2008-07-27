#include <iostream>

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
		
		this->action =std::string(fleet_["action"]);
		
		//Support beenden und Flotte nach Hause schicken
		if ((int)fleet_["status"]==3)
		{
			mysqlpp::Query query = con_->query();
			query << "UPDATE ";
			query << "	fleet ";
			query << "SET ";
			query << "	entity_to=entity_next, ";
			query << "	entity_next=0, ";
			query << "	landtime=startime+nextactiontime, ";
			query << "	starttime=landtime, ";
			query << "	nextactiontime='0', ";
			query << "	res_fuel='0', ";
			query << "	status='1';";
			query.store();
			query.reset();
			
			int uId = functions::getUserIdByPlanet((int)fleet_["entity_to"]);
			
			
			//Nachricht senden Flotteninhaber
			this->msg = "[b]SUPPORT BEENDET[/b]\n\nEine eurer Flotten hat hat ihr Ziel verlassen und macht sich nun auf den Rückweg!\n\n[b]Zielplanet:[/b] ";
			this->msg += functions::formatCoords((int)fleet_["entity_to"],0);
			this->msg += "\n[b]Startplanet:[/b] ";
			this->msg += functions::formatCoords((int)fleet_["entity_from"],0);
			this->msg += "\n[b]Zeit:[/b] ";
			this->msg += functions::formatTime((int)fleet_["landtime"]);
			this->msg += "\n[b]Auftrag:[/b] ";
			this->msg += functions::fa(this->action);
			
			functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Supportflotte Rückflug",this->msg);
							
			if (uId != (int)fleet_["user_id"])
			{
				//Nachricht senden Flotteninhaber
				this->msg = "[b]SUPPORT BEENDET[/b]\n\nEine Flotte hat hat ihr Ziel verlassen und macht isch nun auf den Rückweg!\n\n[b]Zielplanet:[/b] ";
				this->msg += functions::formatCoords((int)fleet_["entity_to"],0);
				this->msg += "\n[b]Startplanet:[/b] ";
				this->msg += functions::formatCoords((int)fleet_["entity_from"],0);
				this->msg += "\n[b]Zeit:[/b] ";
				this->msg += functions::formatTime((int)fleet_["landtime"]);
				this->msg += "\n[b]Auftrag:[/b] ";
				this->msg += functions::fa(this->action);
				this->msg += "\n[b]User:[/b] ";
				this->msg += functions::getUserNick(uId);
				functions::sendMsg(uId,(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Supportflotte Rückflug",this->msg);
			}
		}
		else
		{
			//Support beginnen
			this->flyingHomeTime = (int)fleet_["landtime"] - (int)fleet_["starttime"];
			this->entity = (int)fleet_["entity_from"];
		
			// Precheck action==possible?
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "	owner.user_alliance_id AS oAId, ";
			query << "	owner.user_id, ";
			query << "	owner.user_nick, ";
			query << "	fleet.user_alliance_id AS fAId";
			query << "FROM ";
			query << "	users AS owner ";
			query << "INNER JOIN ";
			query << "	planets ";
			query << "ON ";
			query << "	owner.user_id = planets.planet_user_id ";
			query << "	AND planets.id='" << (int)fleet_["entity_to"] << "' ";
			query << "LEFT JOIN ";
			query << "	users AS fleet ";
			query << "ON ";
			query << "	fleet.user_id ='" << (int)fleet_["user_id"] << "';";
			mysqlpp::Result checkRes = query.store();
			query.reset();
		
			if (checkRes)
			{
				int checkSize = checkRes.size();
				
				if (checkSize > 0)
				{
					mysqlpp::Row checkRow = checkRes.at(0);
				
					if ((int)checkRow["oAId"] == (int)checkRow["fAId"] && (int)checkRow["fAId"] > 0)
					{
						query << "SELECT ";
						query << " SUM(ship_fuel_use_economize*fs_ship_cnt) AS fuel";
						query << "FROM ";
						query << "	fleet_ships ";
						query << "INNER JOIN ";
						query << "	ships ";
						query << "ON ";
						query << "	fleet_ships.fs_ship_id = ships.ship_id ";
						query << "	AND fleet_ships.fs_fleet_id='" << fleet_["id"] << "';";
						mysqlpp::Result fuelRes = query.store();
						query.reset();
					
						if (fuelRes)
						{
							int fuelSize = fuelRes.size();
						
							if (fuelSize > 0)
							{
								mysqlpp::Row fuelRow = fuelRes.at(0);
								this->fuelPerMinute = (int)fuelRow["fuel"];
							
								this->actionTime = ((int)fleet_["res_fuel"] / this->fuelPerMinute) * 60;
								this->landtime = (int)fleet_["landtime"] + this->actionTime;
							
								query << "UPDATE ";
								query << "	fleet ";
								query << "SET ";
								query << "	entity_next=entity_from, ";
								query << "	entity_from=entity_to, ";
								query << "	nextactiontime='" << this->flyingHomeTime << "', ";
								query << "	starttime=landtime, ";
								query << "	landtime='" << landtime << "'";
								query << "	stauts='4'";
								query.store();
								query.reset();

								//Nachricht senden Flotteninhaber
								this->msg = "[b]FLOTTE GELANDET[/b]\n\nEine eurer Flotten hat hat ihr Ziel erreicht!\n\n[b]Zielplanet:[/b] ";
								this->msg += functions::formatCoords((int)fleet_["entity_to"],0);
								this->msg += "\n[b]Startplanet:[/b] ";
								this->msg += functions::formatCoords((int)fleet_["entity_from"],0);
								this->msg += "\n[b]Zeit:[/b] ";
								this->msg += functions::formatTime((int)fleet_["landtime"]);
								this->msg += "\n[b]Auftrag:[/b] ";
								this->msg += functions::fa(this->action);
								this->msg += "\n[b]Voraussichtliche Dauer:[/b] ";
								this->msg += functions::formatTime(this->actionTime);
			
								functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Supportflotte angekommen",this->msg);
							
								if ((int)checkRow["user_id"] != (int)fleet_["user_id"])
								{
									//Nachricht senden Planeteninhaber
									this->msg = "[b]FLOTTE GELANDET[/b]\n\nEine Flotte hat hat ihr Ziel erreicht!\n\n[b]Zielplanet:[/b] ";
									this->msg += functions::formatCoords((int)fleet_["entity_to"],0);
									this->msg += "\n[b]Startplanet:[/b] ";
									this->msg += functions::formatCoords((int)fleet_["entity_from"],0);
									this->msg += "\n[b]Zeit:[/b] ";
									this->msg += functions::formatTime((int)fleet_["landtime"]);
									this->msg += "\n[b]Auftrag:[/b] ";
									this->msg += functions::fa(this->action);
									this->msg += "\n[b]Voraussichtliche Dauer:[/b] ";
									this->msg += functions::formatTime(this->actionTime);
									this->msg += "\n[b]User:[/b] ";
									this->msg += std::string(checkRow["user_nick"]);
									functions::sendMsg((int)checkRow["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Supportflotte angekommen",this->msg);
								}
							}
						}
					}
					else
					{
						//Nachricht senden Flotteninhaber
						this->msg = "[b]FLOTTE LANDEN FEHLGESCHLAGEN[/b]\n\nEine eurer Flotten konnte nicht auf ihrem Ziel landen!\n\n[b]Zielplanet:[/b] ";
						this->msg += functions::formatCoords((int)fleet_["entity_to"],0);
						this->msg += "\n[b]Startplanet:[/b] ";
						this->msg += functions::formatCoords((int)fleet_["entity_from"],0);
						this->msg += "\n[b]Zeit:[/b] ";
						this->msg += functions::formatTime((int)fleet_["landtime"]);
						this->msg += "\n[b]Auftrag:[/b] ";
						this->msg += functions::fa(this->action);
			
						functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Supportflug fehlgeschlagen",this->msg);
					
					}
				}
			}
		}
	}
}
