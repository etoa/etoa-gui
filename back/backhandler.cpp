#include <iostream>

#include <mysql++/mysql++.h>

#include "BackHandler.h"
#include "../functions/ResHandler.h"
#include "../functions/ShipHandler.h"
#include "../functions/PlanetHandler.h"
#include "../functions/MessageHandler.h"


int check_user(int, int);
namespace back
{
	void BackHandler::update(mysqlpp::Row fleet_row)
	{
		
		//std::string coords1 = message::formatCoords::format_coords(this->con_, fleet_row);
		//std::cout << coords1;
		mysqlpp::Row planet_row;
		
		std:: cout << "->loading Planetdata...\n";
		//Load Planetdata
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
	
		std::string res_message, ship_message, time_message;

		
		if (checked_user == 2)
		{
			
			if ((std::string)fleet_row["fleet_action"] == "ko")
			{
				if ((std::string)planet_row["planet_user_id"] == "0")
				{	
					int one = planet::changePlanet::countUserPlanets(this->con_, fleet_row);
					planet::changePlanet::colonizePlanet(this->con_, fleet_row);
					res_message = res::addRes::add_fleet_res_to_planet_res(this->con_, fleet_row);
					ship_message = ships::addShips::add_fleet_ships_to_planet(this->con_, fleet_row);
					//add_message_to_db(fleet_id, fleet_planet_id, fleet_cat, fleet_action);
					//ships::deleteFleet::delete_fleet(this->con_, fleet_row);
				}
				else
				{
					std::cout << "->colonoize failed\n";
					//send_fleet_home
				}

			}
			else
			{
				std::cout << "Überprüfen ob Transport oder falsche Eingabe//Kolonisieren -> zu main_planet senden\n";	
			}		
		}
		else
		{
			time_message = message::formatMessage::format_time();
			//Nachricht senden
			std::string msg = "'[b]FLOTTE GELANDET[/b]\n\nEine eurer Flotten hat ihr Ziel erreicht!\n[b]Zielplanet:[/b] ";
			msg += message::formatMessage::format_coords(this->con_, (std::string)fleet_row["fleet_planet_to"]);
			msg += "\n[b]Startplanet:[/b] ";
			msg +=  message::formatMessage::format_coords(this->con_, (std::string)fleet_row["fleet_planet_from"]);
			msg += "\n[b]Zeit:[/b] ";
			msg += time_message;
			msg += "[b]Auftrag:[/b] ";
			msg += (std::string)fleet_row["fleet_action"];
			msg += "\n\n";
			res_message = res::addRes::add_fleet_res_to_planet_res(this->con_, fleet_row);
			ship_message = ships::addShips::add_fleet_ships_to_planet(this->con_, fleet_row);
			msg += res_message;
			msg += ship_message;
			msg += "'";
			std::string subject = "'TRANSPORT ANGEKOMMEN'";
			int user_id = 415;
			int cat = 1;
			message::addMessage::send_message(this->con_, user_id, cat, subject, msg);
			//ships::deleteFleet::delete_fleet(this->con_, fleet_row);
			
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