#include <iostream>

#include <mysql++/mysql++.h>

#include "GasHandler.h"
#include "../../MysqlHandler.H"
#include "../../functions/Functions.h"

namespace gas
{
	void GasHandler::update()
	{
	
		/**
		* Fleet-Action: Gas collect on gas planet
		*/
		mysqlpp::Query query = con_->query();
		destroyedShips = "";
		destroy = 0;
		if (mt_rand(0,100)>80)	// 20 % Chance dass Schiffe überhaupt zerstört werden //ToDO + config + time init
		{
			destroy=mt_rand(0,10);		// 0 <= X <= 10 Prozent an Schiffen werden Zerstört
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
						int shipDestroy = floor((int)cntRow["fs_ship_cnt"]*destroy/100);
						
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
							destroyedShips += cntRow["ship_name"];
							destroyedShips += "\n";
						}
					}
				}
			}
                
			if(shipDestroy > 0)
			{
				destroyedShipsMsg = "\n\nAufgrund starker Wasserstoffexplosionen sind einige deiner Schiffe zerst&ouml;rt worden:\n\n";
				destroyedShipsMsg += $destroyed_ships;
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
		query << "id='" << fleet_["fleet_target_to"] << "';";
		mysqlpp::Result fuelRes = query.store();
		query.reset();
		
		fuelTotal = fleet_["fleet_res_fuel"];
		
		if (fuelRes)
		{
			int fuelSize = fuelRes.size();
			
			if (fuelSize > 0)
			{
			
				mysqlpp::Row fuelRow = fuelRes.at(0);

				// Anzahl gesammelter Rohstoffe berechen
				int capa = min((double)fleet_["fleet_capacity_nebula"],(double)fleet_["fleet_capacity"]);
				fuel = mt_rand(1000,capa); //ToDo
		
				fuel = min(fuel, fuelRow["planet_res_fuel"]);
			
				//Tritium nach dem Saugen berechnen und speichern
				newFuel = fuelRow["planet_res_fuel"] - fuel;
				query << "UPDATE ";
				query << "	planets ";
				query << "SET ";
				query << "	planet_res_fuel='" << newFuel << "' ";
				query << "WHERE ";
				query << "	id='" << fleet_["fleet_planet_to"] << "';";
				query.store();
				query.reset();

				//Smmiert erhaltenes Tritium zu der Ladung der Flotte
				fuelTotal = fuel + fleet_["fleet_res_fuel"];
			}
		}
		
		double capacity = (double)fleet_["fleet_capacity"] - fuel;
		// Flotte zurückschicken
		fleetReturn("gr","","","",fuelTotal,cpacity);

		//Nachricht senden
		std::string msg = "[b]GASSAUGER-RAPPORT[/b]\n\nEine Flotte vom Planeten \n[b]";
		msg += functions::formatCoords((int)fleet_["fleet_target_from"]);
		msg += "[/b]\nhat [b]";
		msg += functions::formatCoords((int)fleet_["fleet_target_to"]);
		msg += "[/b]\num [b]";
		msg += functions::formatTime((int)fleet_["fleet_landtime"]);
		msg += "[/b]\n erreicht und Gas gesaugt\n";
			
		std::string msgRes = "\n[b]ROHSTOFFE:[/b]\n\nTritium: ";
		msgRes += functions::nf(fuel);
		msgRes += destroyedShipsMsg;
		
		msg += msgRes;
		
		functions::sendMsg((int)fleet_["fleet_user_id"],SHIP_MISC_MSG_CAT_ID,"Gas gesaugt",msg);

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
		log += functions::getUserNick(fleet_["fleet_user_id"]);
		log += "[/B] vom Planeten [b]";
		log += functions::formatCoords(fleet_["fleet_target_from"]);
		log += "[/b] hat den Gasplaneten [b]";
		log += functions::formatCoords(fleet_["fleet_target_to"]);
		log += "[/b] um [b]";
		log += functions::formatTime((int)fleet_["fleet_landtime"]);
		log += "[/b] erreicht und Gas gesaugt.\n";
		log += msgRes;
		functions::addLog(13,log,(int)time);

	}
}

