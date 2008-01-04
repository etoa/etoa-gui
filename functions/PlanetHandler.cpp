
#include "PlanetHandler.h"

namespace planet
{
	void changePlanet::changePlanetUserId(mysqlpp::Connection* con, mysqlpp::Row fleet_row)
	{
		std::cout << "->changing Planet_User_Id\n";
		
		mysqlpp::Query query = con->query();
		query << "UPDATE ";
			query << "planets ";
		query << "SET ";
			query << "planet_user_id = " << fleet_row["fleet_user_id"];			
		query << " WHERE ";
			query << "planet_id = " << fleet_row["fleet_planet_to"] << ";";
		query.store();
		query.reset();
	}

	int changePlanet::countUserPlanets(mysqlpp::Connection* con, mysqlpp::Row fleet_row)
	{
		std:: cout << "->counting User Planets: ";
		// Lade Planeten des Users
		mysqlpp::Query query = con->query();
		query << "SELECT ";
			query << "COUNT(planet_user_id) ";
		query << "FROM ";
			query << "planets ";
		query << "WHERE ";
			query << "planet_user_id = " << fleet_row["fleet_user_id"] << ";";

		mysqlpp::Result planet_res = query.store();
		query.reset();
		
		int resSize = planet_res.size();
		
		std::cout << resSize << "\n";
		
		return resSize;
	}
	
	void changePlanet::colonizePlanet(mysqlpp::Connection* con, mysqlpp::Row fleet_row)
	{
		
		std::cout << "->colonize Planet\n";
		
		mysqlpp::Query query = con->query();
		query << "UPDATE ";
			query << "planets ";
		query << "SET ";
			query << "planet_user_id = " << fleet_row["fleet_user_id"] << ", ";
			query << "planet_name='',";
			query << "planet_user_main=0, ";
			query << "planet_fields_used=0, ";
			query << "planet_fields_extra=0, ";
			query << "planet_res_metal=0, ";
			query << "planet_res_crystal=0, ";
			query << "planet_res_fuel=0, ";
			query << "planet_res_plastic=0, ";
			query << "planet_res_food=0, ";
			query << "planet_use_power=0, ";
			query << "planet_last_updated=0, ";
			query << "planet_prod_metal=0, ";
			query << "planet_prod_crystal=0, ";
			query << "planet_prod_plastic=0, ";
			query << "planet_prod_fuel=0, ";
			query << "planet_prod_food=0, ";
			query << "planet_prod_power=0, ";
			query << "planet_store_metal=0, ";
			query << "planet_store_crystal=0, ";
			query << "planet_store_plastic=0, ";
			query << "planet_store_fuel=0, ";
			query << "planet_store_food=0, ";
			query << "planet_people=1, ";
			query << "planet_people_place=0, ";
			query << "planet_desc='' ";
		query << "WHERE ";
			query << "planet_id = " << fleet_row["fleet_planet_to"] << ";";

		query.store();
		query.reset();			

		query << "DELETE FROM ";
			query << "shiplist ";
		query <<  "WHERE ";
			query << "shiplist_planet_id = " << fleet_row["fleet_planet_to"] << ";";
		query.store();
		query.reset();
		
		query << "DELETE FROM ";
			query << "buildlist ";
		query << "WHERE ";
			query << "buildlist_planet_id = " << fleet_row["fleet_planet_to"] << ";";
		query.store();
		query.reset();
		
		query << "DELETE FROM ";
			query << "deflist ";
		query << "WHERE ";
			query << "deflist_planet_id = " << fleet_row["fleet_planet_to"] << ";";
		
	}
	
}
