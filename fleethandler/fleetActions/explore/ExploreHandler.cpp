#include <iostream>
#include <ctime>
#include <math.h>
#include <stdlib.h>

#include <mysql++/mysql++.h>

#include "ExploreHandler.h"
#include "../../MysqlHandler.h"
#include "../../config/ConfigHandler.h"
#include "../../functions/Functions.h"

namespace explore
{
	void ExploreHandler::update()
	{
	
		/**
		* Fleet-Action: Explore the univserse
		*/

		//Init
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);
		std::string action = "explore";

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
				query << "SELECT ";
				query << "	sx, ";
				query << "	sy, ";
				query << "	cx, ";
				query << "	cy, ";
				query << "	code, ";
				query << "	lastvisited ";
				query << "FROM ";
				query << "	entities ";
				query << "INNER JOIN ";
				query << "	cells ";
				query << "ON entities.cell_id=cells.id ";
				query << "AND entities.id='" << fleet_["entity_to"] << "';";
				mysqlpp::Result cellRes = query.store();
				query.reset();

				//the mask
				char mask[1000] = "";

				if (cellRes)
				{
					int cellSize = cellRes.size();

					if (cellSize > 0)
					{
						mysqlpp::Row cellRow = cellRes.at(0);

						query << "SELECT ";
						query << "	discoverymask ";
						query << "FROM ";
						query << "	users ";
						query << "WHERE ";
						query << "	user_id='" << fleet_["user_id"] << "';";
						mysqlpp::Result maskRes = query.store();
						query.reset();
						
						if (maskRes)
						{
							int maskSize = maskRes.size();
							
							if (maskSize > 0)
							{
								mysqlpp::Row maskRow = maskRes.at(0);
								strcpy( mask, maskRow["discoverymask"]);
							}
						}

						this->absX = 10 * ((int)cellRow["sx"] - 1) + (int)cellRow["cx"];
						this->absY = 10 * ((int)cellRow["sy"] - 1) + (int)cellRow["cy"];

						this->sxNum = config.nget("num_of_sectors",1);
						this->cxNum = config.nget("num_of_cells",1);
						this->syNum = config.nget("num_of_sectors",2);
						this->cyNum = config.nget("num_of_cells",2);

						for (int x = this->absX - 1; x <= this->absX + 1; x++)
						{
							for (int y = this->absY - 1; y <= this->absY + 1; y++)
							{
								this->pos = x + (this->cyNum * this->syNum) * (y - 1) - 1;
								if (this->pos >= 0 && this->pos <= this->sxNum * this->syNum * this->cxNum * this->cyNum)
								{
									mask[this->pos] = '1';				
								}
							}
						}	

						query << "UPDATE ";
						query << "	users ";
						query << "SET ";
						query << " discoverymask='" << mask << "' ";
						query << "WHERE ";
						query << "	user_id='" << fleet_["user_id"] << "';";
						query.store();
						query.reset();
						
						std::string text = "Eine Flotte vom Planeten ";
						text += functions::formatCoords((int)fleet_["entity_from"],0);
						text += " hat das Ziel ";
						text += functions::formatCoords((int)fleet_["entity_to"],2);
						text += " erkundet.";
							
						functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Erkundung",text);
				
						fleetReturn(1);
					}			
				}	

			}
			else
			{
				std::string text = "\n\nEine Flotte vom Planeten ";
				text += functions::formatCoords((int)fleet_["entity_from"],0);
				text += " versuchte, das Ziel zu erkunden. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
				functions::sendMsg((int)fleet_["user_id"],(int)config.idget("SHIP_MISC_MSG_CAT_ID"),"Erkundung gescheitert",text);
				
				fleetReturn(1);
			}
		}
	}
	
	std::string ExploreHandler::event()
	{
	
		/*Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);
		
		this->days = (time - lastvisited)/3600;
		
		this->one = rand() % 101;
		
		if (code=='p' || code=='s')
		{
			this-> = std::min(days*3.30),5);
		}
		else
		{
			this->two = std::min(days*5,50),80);
		}
		
		if (this->one < this->two)
		{
		 
		}
		//Sonst keine Aktion
		else
		{
		  
		} */
	}
}
