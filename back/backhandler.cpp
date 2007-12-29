#include <iostream>

#include <mysql++/mysql++.h>

#include "backhandler.h"
#include "../functions/ResHandler.h"
#include "../functions/ShipHandler.h"

int check_user(int, int);


namespace back
{
	void BackHandler::update(mysqlpp::Row fleet_row)
	{
		
		//std::cout << fleet_row["fleet_user_id"];
		mysqlpp::Row planet_row;
		
		//std:: cout << fleet_id << fleet_user_id << fleet_planet_id << fleet_action << fleet_cat;
		//Planetendaten laden
		mysqlpp::Query query = con_->query();
		 query << "SELECT ";
			query << "* ";
		 query << "FROM ";
			query << "planets ";
		query << "WHERE ";
			query << "planet_id =  " << fleet_row["fleet_planet_to"] << ";";
		mysqlpp::Result planet_res = query.store();
		query.reset();
		
		planet_row = planet_res.at(0);
		

		
		//planet_user_id=!fleet_user_id=2; planet_user_id=fleet_user_id=0; market=1;
		int checked_user;
		checked_user = check_user(fleet_row["fleet_user_id"], planet_row["planet_user_id"]);
		
		
		if (checked_user == 2)
		{
			std::cout << "Überprüfen ob Transport oder falsche Eingabe//Kolonisieren -> zu main_planet senden";
		}
		else
		{
			res::addRes::add_fleet_res_to_planet_res(this->con_, fleet_row);
			ships::addShips::add_fleet_ships_to_planet(this->con_, fleet_row);
			//add_message_to_db(fleet_id, fleet_planet_id, fleet_cat, fleet_action);
			ships::deleteFleet::delete_fleet(this->con_, fleet_row);
		}
		
	}
	

}

			

		int check_user(int fleet, int planet)
		{
			if (fleet == planet)
			{
				return 0;
			}
			else if (fleet == '0') //ID des Marktplatzes :D
			{
				return 1;
			}
			else
			{
				return 2;
			}
		}
