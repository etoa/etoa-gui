#include <iostream>
#include <ctime>
#include <math.h>
#include <stdlib.h>

#include <mysql++/mysql++.h>

#include "GasHandler.h"
#include "../../MysqlHandler.H"
#include "../../config/ConfigHandler.h"
#include "../../functions/Functions.h"

namespace gas
{
	void GasHandler::update()
	{
	
		/**
		* Fleet-Action: Gas collect on gas planet
		*/
		
		//Init
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		srand (time);
		
		//Precheck action==possible?
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	ship_id ";
		query << "FROM ";
		query << "	fleet_ships ";
		query << "INNER JOIN ";
		query << "	ships ON fs_ship_id = ship_id ";
		query << "	AND fs_fleet_id='" << fleet_["fleet_id"] << "' ";
		query << "	AND fs_ship_faked='0' ";
		query << "	AND ship_nebula='1';";
		mysqlpp::Result fsRes = query.store();
		query.reset();
		
					
		if (fsRes)
		{
			int fsSize = fsRes.size();
			
			if (fsSize > 0)
			{
				mysqlpp::Query query = con_->query();  //destroy fault!!
				query << std::setprecision(18);
				destroyedShips = "";
				destroy = 0;
				int one = rand() % 101;
				int two = config.nget("gascollect_action",0) * 100;
				if (one  < two)	// 20 % Chance dass Schiffe überhaupt zerstört werden
				{
					destroy = rand() % (int)(config.nget("gascollect_action",1) * 100);		// 0 <= X <= 10 Prozent an Schiffen werden Zerstört
					
				}
				
				if(destroy>0)
				{
					query << "SELECT ";
					query << "	s.ship_name, ";
					query << "	fs.fs_ship_id, ";
					query << "	fs.fs_ship_cnt ";
					query << "FROM ";
					query << "(";
					query << "	fleet_ships AS fs ";
					query << "INNER JOIN ";
					query << "	fleet AS f ";
					query << "	ON fs.fs_fleet_id = f.fleet_id ";
					query << ")"; 
					query << "INNER JOIN ";
					query << "	ships AS s ";
					query << "	ON fs.fs_ship_id = s.ship_id ";
					query << "	AND f.fleet_id='" << fleet_["fleet_id"] << "' ";
					query << "GROUP BY ";
					query << "fs.fs_ship_id;";
					mysqlpp::Result cntRes = query.store();
					query.reset();
			
					if (cntRes)
					{
						int cntSize = cntRes.size();
				
						if (cntSize > 0)
						{
							mysqlpp::Row cntRow = cntRes.at(0);
					
							for (mysqlpp::Row::size_type i = 0; i<cntSize; i++) 
							{
								cntRow = cntRes.at(i);
			
								//Berechnet wie viele Schiffe von jedem Typ zerstört werden
								shipDestroy = (int)floor((int)cntRow["fs_ship_cnt"] * destroy / 100);
						
								if(shipDestroy>0)
								{
									// "Zerstörte" Schiffe aus der Flotte löschen
									query << "UPDATE ";
									query << "	fleet_ships ";
									query << "SET ";
									query << "	fs_ship_cnt=fs_ship_cnt-'" << shipDestroy << "' ";
									query << "WHERE ";
									query << "	fs_fleet_id='" << fleet_["fleet_id"] << "' ";
									query << "	AND fs_ship_id='" << cntRow["fs_ship_id"] << "';";
									query.store();
									query.reset();
									destroyedShips += shipDestroy;
									destroyedShips += " ";
									destroyedShips += std::string(cntRow["ship_name"]);
									destroyedShips += "\n";
								}
							}
						}
					}
                
					if(shipDestroy > 0)
					{
						destroyedShipsMsg = "\n\nAufgrund starker Wasserstoffexplosionen sind einige deiner Schiffe zerst&ouml;rt worden:\n\n";
						destroyedShipsMsg += destroyedShips;
					}
				}
				else
				{
					destroyedShipsMsg = "";
				}
		
				//Laden der Tritiummenge auf dem Planeten
				query << "SELECT ";
				query << "	planet_res_fuel ";
				query << "FROM ";
				query << "	planets ";
				query << "WHERE ";
				query << "id='" << fleet_["fleet_entity_to"] << "' ";
				query << "	AND planet_type_id='" << config.get("gasplanet",0) << "';";
				mysqlpp::Result fuelRes = query.store();
				query.reset();
		
				fuelTotal = fleet_["fleet_res_fuel"];
				
				std::cout << "fuelTotal -> " << fuelTotal;

				if (fuelRes)
				{
					int fuelSize = fuelRes.size();
			
					if (fuelSize > 0)
					{
			
						mysqlpp::Row fuelRow = fuelRes.at(0);

						// Anzahl gesammelter Rohstoffe berechen
						int capa = std::min((double)fleet_["fleet_capacity_nebula"],(double)fleet_["fleet_capacity"]);
						fuel = 1000 + (rand() % (int)(capa - 999));
		
						fuel = std::min(fuel, (double)fuelRow["planet_res_fuel"]);
			
						//Tritium nach dem Saugen berechnen und speichern
						double newFuel = fuelRow["planet_res_fuel"] - fuel;
						query << "UPDATE ";
						query << "	planets ";
						query << "SET ";
						query << "	planet_res_fuel='" << newFuel << "' ";
						query << "WHERE ";
						query << "	id='" << fleet_["fleet_entity_to"] << "';";
						query.store();
						query.reset();

						//Smmiert erhaltenes Tritium zu der Ladung der Flotte
						fuelTotal = fuel + fleet_["fleet_res_fuel"];
					}
				}

				double capacity = (double)fleet_["fleet_capacity"] - fuel;
				// Flotte zurückschicken
				fleetReturn("gr",-1,-1,-1,fuelTotal,-1,-1,capacity);

				//Nachricht senden
				std::string msg = "[b]GASSAUGER-RAPPORT[/b]\n\nEine Flotte vom Planeten \n[b]";
				msg += functions::formatCoords((int)fleet_["fleet_entity_from"],0);
				msg += "[/b]\nhat [b]";
				msg += functions::formatCoords((int)fleet_["fleet_entity_to"],0);
				msg += "[/b]\num [b]";
				msg += functions::formatTime((int)fleet_["fleet_landtime"]);
				msg += "[/b]\n erreicht und Gas gesaugt\n";
			
				std::string msgRes = "\n[b]ROHSTOFFE:[/b]\n\nTritium: ";
				msgRes += functions::nf(functions::d2s(fuel));
				msgRes += destroyedShipsMsg;
		
				msg += msgRes;
		
				functions::sendMsg((int)fleet_["fleet_user_id"],config.idget("SHIP_MISC_MSG_CAT_ID"),"Gas gesaugt",msg);

				//Erbeutete Rohstoffsumme speichern
				query << "UPDATE ";
				query << "	users ";
				query << "SET ";
				query << "	user_res_from_nebula=user_res_from_nebula+'" << fuel << "' ";
				query << "WHERE ";
				query << "	user_id='" << fleet_["fleet_user_id"] << "';";
				query.store();
				query.reset();  

				//Log schreiben
				std::string log = "Eine Flotte des Spielers [B]";
				log += functions::getUserNick((int)fleet_["fleet_user_id"]);
				log += "[/B] vom Planeten [b]";
				log += functions::formatCoords((int)fleet_["fleet_entity_from"],0);
				log += "[/b] hat den Gasplaneten [b]";
				log += functions::formatCoords((int)fleet_["fleet_entity_to"],0);
				log += "[/b] um [b]";
				log += functions::formatTime((int)fleet_["fleet_landtime"]);
				log += "[/b] erreicht und Gas gesaugt.\n";
				log += msgRes;
				functions::addLog(13,log,(int)time);
			}
			else
			{
				std::string text = "\n\nEine Flotte vom Planeten ";
				text += functions::formatCoords((int)fleet_["fleet_entity_from"],0);
				text += " versuchte, das Ziel zu übernehmen. Leider war kein Schiff mehr in der Flotte, welches die Aktion ausführen konnte, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
							
				functions::sendMsg((int)fleet_["fleet_user_id"],config.idget("SHIP_MISC_MSG_CAT_ID"),"Gassaugen gescheitert",text);
				
				fleetReturn("gr");
			}
		}
	}
}

