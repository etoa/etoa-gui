
#include "PlanetManager.h"
#include "../util/Debug.h"

namespace planet
{
	PlanetManager::PlanetManager() {
	}
	
	PlanetManager::~PlanetManager() {
	}
	
	void PlanetManager::updatePlanet(int planetId)
	{
    DEBUG("Update planet " << planetId);
		PlanetEntity p = PlanetEntity(planetId);
		p.updateResources();
    p.updateProduction();
    p.save();
	}

	void PlanetManager::updatePlanets(std::vector<int>* planetIds)
	{
    for (unsigned int x=0; x < planetIds->size(); x++)
    {
			updatePlanet((*planetIds)[x]);
    }  
	}
	
	std::vector<int> PlanetManager::getUpdateableUserPlanets()
	{
		std::time_t ptime = std::time(0) - PLANETMANAGER_UPDATE_INTERVAL;
		My &my = My::instance();
		mysqlpp::Connection* con_ = my.get();
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "	id "
			<< "FROM "
			<< "  planets "
			<< "WHERE planet_last_updated<'" << ptime << "' "
			<< "  AND planet_user_id > 0 ";
    //std::cout << query.str() << std::endl;
		RESULT_TYPE res = query.store();
		query.reset();
		
    std::vector<int> vec;
    
		if (res) {
			unsigned int resSize = res.size();
			if (resSize) {
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) { 
          mysqlpp::Row row = res.at(i);
          vec.push_back((int)row["id"]);
				}
			}
		}
    return vec;
	}
}
