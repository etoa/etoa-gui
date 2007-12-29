
#include "ResHandler.h"

namespace res
{
	void addRes::add_fleet_res_to_planet_res(mysqlpp::Connection* con, mysqlpp::Row fleet_row)
	{
		mysqlpp::Query query = con->query();
		query << "UPDATE ";
			query << "planets ";
		query << "SET ";
			query << "planet_res_metal = planet_res_metal +" << fleet_row["fleet_res_metal"] << ", ";
			query << "planet_res_crystal = planet_res_crystal + " << fleet_row["fleet_res_crystal"] << ", ";
			query << "planet_res_plastic = planet_res_plastic + " << fleet_row["fleet_res_plastic"] << ", ";
			query << "planet_res_fuel = planet_res_fuel + " << fleet_row["fleet_res_fuel"] << ", ";
			query << "planet_res_food = planet_res_food + " << fleet_row["fleet_res_food"] << ", ";
			query << "planet_people = planet_people + " << fleet_row["fleet_res_people"];			
		query << " WHERE ";
			query << "planet_id = " << fleet_row["fleet_planet_to"] << ";";
		query.store();
		query.reset();
	}
	}
