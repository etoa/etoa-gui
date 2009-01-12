
#include "PlanetManager.h"

namespace planet
{
	PlanetManager::PlanetManager(std::vector<int>* planetIds)
	{
		std::cout << "Updating " << planetIds->size() << " Planet(s)...\n";
		while (!planetIds->empty()) {
			this->planet_ = new PlanetEntity(planetIds->back());
			planetIds->pop_back();
			delete this->planet_;
		}
	}
	
	void PlanetManager::updateUserPlanets()
	{
		std::time_t ptime = std::time(0) - 300;
		My &my = My::instance();
		mysqlpp::Connection* con_ = my.get();
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "	planets.id, "
			<< "	planets.planet_user_main, "
			<< "	planets.planet_last_updated, "
			<< "	planets.planet_res_metal, "
			<< "	planets.planet_res_crystal, "
			<< "	planets.planet_res_plastic, "
			<< "	planets.planet_res_fuel, "
			<< "	planets.planet_res_food, "
			<< "	planets.planet_prod_metal, "
			<< "	planets.planet_prod_crystal, "
			<< "	planets.planet_prod_plastic, "
			<< "	planets.planet_prod_fuel, "
			<< "	planets.planet_prod_food, "
			<< "	planets.planet_store_metal, "
			<< "	planets.planet_store_crystal, "
			<< "	planets.planet_store_plastic, "
			<< "	planets.planet_store_fuel, "
			<< "	planets.planet_store_food, "
			<< "	planets.planet_people, "
			<< "	planets.planet_people_place, "
			<< "	planets.planet_type_id, "
			<< "	users.user_id, "
			<< "	users.user_race_id, "
			<< "	users.user_specialist_id, "
			<< "	users.user_specialist_time, "
			<< "	stars.type_id "
			<< "FROM  "
			<< "  ( "
			<< "  	( "
			<< "		("
			<< "		entities "
			<< "			INNER JOIN "
			<< "				planets "
			<< "			ON planets.id = entities.id "
			<< "			AND planets.planet_last_updated<'"<< ptime << "' "
			<< "		) "
			<< "		INNER JOIN  "
			<< "			entities AS e "
			<< "		ON e.cell_id=entities.cell_id AND e.pos=0 "
			<< "	) "
			<< "	INNER JOIN  	 "
			<< "		stars "
			<< "	ON stars.id=e.id "
			<< " )"
			<< " INNER JOIN  "
			<< "	users  "
			<< " ON planets.planet_user_id = users.user_id;";
		mysqlpp::Result res = query.store();			
		query.reset();
		
		if (res) {
			int resSize = res.size();
			
			if (resSize) {
				mysqlpp::Row row;
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) { 
					row = res.at(i);
					this->planet_ = new PlanetEntity(row);
					delete this->planet_;
				}
			}
			
			std::cout << "Updated " << resSize << " Userplanets\n";
		}
	}
}
