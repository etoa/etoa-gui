
#include "ShipHandler.h"
#include "MessageHandler.h"
#include <string>
#include <sstream>

namespace ships
{
	std::string addShips::add_fleet_ships_to_planet(mysqlpp::Connection* con, mysqlpp::Row fleet_row)
	{
		std::string msg = "";
		
		std::cout << "->adding Ships to PlanetShips...\n";
		mysqlpp::Query query = con->query();
		
		query << "SELECT ";
			query << "* ";
		query << "FROM ";
			query << "fleet_ships AS fs "; 
		query << "INNER JOIN "; 
			query << "ships AS s ON fs.fs_ship_id = s.ship_id ";
			query << "AND fs.fs_fleet_id = '" << fleet_row["fleet_id"] << "' ";
			query << "AND fs.fs_ship_id != '16' "; //Handelsschiff wird nicht überprüft
			query << "AND fs.fs_ship_faked = '0';";
	
		
		mysqlpp::Result fleet_ships_res = query.store();
		query.reset();
		
		if (fleet_ships_res) 
		{
			
			int resSize = fleet_ships_res.size();
			
			if (resSize>0)
			{
			
				std::string msg;
				
				mysqlpp::Row fleet_ships_row;
				int lastId = 0;
				
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
				{
					fleet_ships_row = fleet_ships_res.at(i);

					//Option für Kolonisieren und Invasieren
                	int fleet_ships_cnt = fleet_ships_row["fs_ship_cnt"];
					

                    // Koloschiff wegpurzelnlassen
                   /*if (fleet_ships_row["ship_colonialize"]==1 && already_colonialized==0 && fleet_row["fleet_action"]=="onewayticket_colonize") //Oder so ähnlich
                    {
                        fleet_ships_cnt--;
                        already_colonialized=1;
                    }*/


                    //Eintrag in shiplist vorhanden?
                    query << "SELECT ";
						query << "shiplist_id ";
					query << "FROM ";
						query << "shiplist ";
					query << "WHERE ";
						query << "shiplist_ship_id = " << fleet_ships_row["fs_ship_id"] << " ";
						query << "AND shiplist_planet_id = " << fleet_row["fleet_planet_to"] << ";";
					mysqlpp::Result shiplist_res = query.store();
					query.reset();
				
					
					if (shiplist_res.size()) //shiplist_res genügt nicht, weil, dies immer gleich 1 wenn query korreckt
					{
					
						mysqlpp::Row shiplist_row;
						shiplist_row = shiplist_res.at(0);
					

                        query << "UPDATE ";
							query << "shiplist ";
						query << "SET ";
							query << "shiplist_count = shiplist_count + " << fleet_ships_cnt << ", ";
							query << "shiplist_special_ship = " << fleet_ships_row["fs_special_ship"] << ", ";
							query << "shiplist_special_ship_level = " << fleet_ships_row["fs_special_ship_level"] << ", ";
							query << "shiplist_special_ship_exp = " << fleet_ships_row["fs_special_ship_exp"] << ", ";
							query << "shiplist_special_ship_bonus_weapon = " << fleet_ships_row["fs_special_ship_bonus_weapon"] << ", ";
							query << "shiplist_special_ship_bonus_structure = " << fleet_ships_row["fs_special_ship_bonus_structure"] << ", ";
							query << "shiplist_special_ship_bonus_shield = " << fleet_ships_row["fs_special_ship_bonus_shield"] << ", ";
							query << "shiplist_special_ship_bonus_heal = " << fleet_ships_row["fs_special_ship_bonus_heal"] << ", ";
							query << "shiplist_special_ship_bonus_capacity = " << fleet_ships_row["fs_special_ship_bonus_capacity"] << ", ";
							query << "shiplist_special_ship_bonus_speed = " << fleet_ships_row["fs_special_ship_bonus_speed"] << ", ";
							query << "shiplist_special_ship_bonus_pilots = " << fleet_ships_row["fs_special_ship_bonus_pilots"] << ", ";
							query << "shiplist_special_ship_bonus_tarn = " << fleet_ships_row["fs_special_ship_bonus_tarn"] << ", ";
							query << "shiplist_special_ship_bonus_antrax = " << fleet_ships_row["fs_special_ship_bonus_antrax"] << ", ";
							query << "shiplist_special_ship_bonus_forsteal = " << fleet_ships_row["fs_special_ship_bonus_forsteal"] << ", ";
							query << "shiplist_special_ship_bonus_build_destroy = " << fleet_ships_row["fs_special_ship_bonus_build_destroy"] << ", ";
							query << "shiplist_special_ship_bonus_antrax_food = " << fleet_ships_row["fs_special_ship_bonus_antrax_food"] << ", ";
							query << "shiplist_special_ship_bonus_deactivade = " << fleet_ships_row["fs_special_ship_bonus_deactivade"] << " ";
						query << "WHERE ";
							query << "shiplist_id = " << shiplist_row["shiplist_id"] << ";";
						query.store();
						query.reset();
                    }
                    //Kein Datensatz vorhanden
                    else
                    {
						query << "INSERT INTO ";
							query << "shiplist ("; 
								query << "shiplist_user_id, ";
								query << "shiplist_ship_id, ";
								query << "shiplist_planet_id, ";
								query << "shiplist_count, ";
								query << "shiplist_special_ship, ";
								query << "shiplist_special_ship_level, ",
								query << "shiplist_special_ship_exp, ",
								query << "shiplist_special_ship_bonus_weapon, ";
								query << "shiplist_special_ship_bonus_structure, ";
								query << "shiplist_special_ship_bonus_shield, ";
								query << "shiplist_special_ship_bonus_heal, ";
								query << "shiplist_special_ship_bonus_capacity, ";
								query << "shiplist_special_ship_bonus_speed, ";
								query << "shiplist_special_ship_bonus_pilots, ";
								query << "shiplist_special_ship_bonus_tarn, ";
								query << "shiplist_special_ship_bonus_antrax, ";
								query << "shiplist_special_ship_bonus_forsteal, ";
								query << "shiplist_special_ship_bonus_build_destroy, ";
								query << "shiplist_special_ship_bonus_antrax_food, ";
								query << "shiplist_special_ship_bonus_deactivade";
							query << ") ";
						query << "VALUES (";
								query << fleet_row["fleet_user_id"] << ", ";
								query << fleet_ships_row["fs_ship_id"] << ", ";
								query << fleet_row["fleet_planet_to"] << ", ";
								query << fleet_ships_cnt << ", ";
								query << fleet_ships_row["fs_special_ship"] << ", ";
								query << fleet_ships_row["fs_special_ship_level"] << ", ";
								query << fleet_ships_row["fs_special_ship_exp"] << ", ";
								query << fleet_ships_row["fs_special_ship_bonus_weapon"] << ", ";
								query << fleet_ships_row["fs_special_ship_bonus_structure"] << ", ";
								query << fleet_ships_row["fs_special_ship_bonus_shield"] << ", ";
								query << fleet_ships_row["fs_special_ship_bonus_heal"] << ", ";
								query << fleet_ships_row["fs_special_ship_bonus_capacity"] << ", ";
								query << fleet_ships_row["fs_special_ship_bonus_speed"] << ", ";
								query << fleet_ships_row["fs_special_ship_bonus_pilots"] << ", ";
								query << fleet_ships_row["fs_special_ship_bonus_tarn"] << ", ";
								query << fleet_ships_row["fs_special_ship_bonus_antrax"] << ", ";
								query << fleet_ships_row["fs_special_ship_bonus_forsteal"] << ", ";
								query << fleet_ships_row["fs_special_ship_bonus_build_destroy"] << ", ";
								query << fleet_ships_row["fs_special_ship_bonus_antrax_food"] << ", ";
								query << fleet_ships_row["fs_special_ship_bonus_deactivade"];
							query << ");";
						query.store();
						query.reset();
                    }

					if (fleet_ships_cnt>0)
                    {
						std::stringstream tmp;
						std::string number;
						tmp << fleet_ships_cnt;
						tmp >> number;
                    	msg += "\n[b]";
						msg += (std::string)fleet_ships_row["ship_name"];
						msg += ":[/b] ";
						msg += message::formatMessage::format_number(number);
                    }
					
				}

				if (msg.length() == 0)
				{
					msg = "\n\n[b]SCHIFFE[/b]\n[i]Keine weiteren Schiffe in der Flotte![/i]";
				}
				else
				{
					std::string tmp = "\n\n[b]SCHIFFE[/b]\n";
					tmp += msg;
					msg = tmp;
				}
				return msg;
				std::cout << msg << "\n";
			}
		}
	}
	
	// Flotte löschen
	
	void deleteFleet::delete_fleet(mysqlpp::Connection* con,  mysqlpp::Row fleet_row)
	{
	
		std::cout << "deleting Fleet...\n";
		mysqlpp::Query query = con->query();
		
		query << "DELETE FROM ";
			query << "fleet_ships ";
		query << "WHERE "; 
			query << "fs_fleet_id = " << fleet_row["fleet_id"] << ";";
		query.store();
		query.reset();
		
		query << "DELETE FROM "; 
			query << "fleet ";
		query << "WHERE "; 
			query << "fleet_id = " << fleet_row["fleet_id"] << ";";
		query.store();
		query.reset();
	}
	
}
