
#include "PlanetManager.h"
#include "../util/Debug.h"

namespace planet
{
	PlanetManager::PlanetManager() {
	}

	PlanetManager::~PlanetManager() {
	}

	std::vector<int>* PlanetManager::getUpdateableUserPlanets()
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

    std::vector<int>* vec = new std::vector<int>();

		if (res) {
			unsigned int resSize = res.size();
			if (resSize) {
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
          mysqlpp::Row row = res.at(i);
          vec->push_back((int)row["id"]);
				}
			}
		}
    return vec;
	}

	std::vector<int>* PlanetManager::getUserPlanets(int userId)
	{
		My &my = My::instance();
		mysqlpp::Connection* con_ = my.get();
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "	id "
			<< "FROM "
			<< "  planets "
			<< "WHERE planet_user_id = '" << userId << "';";
		RESULT_TYPE res = query.store();
		query.reset();
		std::vector<int>* vec = new std::vector<int>();
		if (res) {
			unsigned int resSize = res.size();
			if (resSize) {
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
					mysqlpp::Row row = res.at(i);
					vec->push_back((int)row["id"]);
				}
			}
		}
		return vec;
	}

  void PlanetManager::markForUpdate(int planetId) {
    planetsMarkedForUpdate.push_back(planetId);
  }

  void PlanetManager::markForUpdate(std::vector<int>* planetIds) {
    planetsMarkedForUpdate.reserve(planetsMarkedForUpdate.size() + planetIds->size());
    planetsMarkedForUpdate.insert(planetsMarkedForUpdate.end(), planetIds->begin(), planetIds->end());
  }

  	void PlanetManager::markUserUpdate(int userId) {
		std::vector<int>* up = getUserPlanets(userId);
		markForUpdate(up);
		delete up;
	}

    void PlanetManager::markUsersForUpdate(std::vector<int>* userIds) {
		for(std::vector<int>::iterator it = userIds->begin(); it != userIds->end(); ++it) {
			markUserUpdate(*it);
		}
	}

	void PlanetManager::updatePlanets()
	{
    std::vector<int>* up = getUpdateableUserPlanets();
    markForUpdate(up);
    delete up;

    updatePlanets(&planetsMarkedForUpdate);
    planetsMarkedForUpdate.clear();
  }

	void PlanetManager::updatePlanets(std::vector<int>* planetIds)
	{
    // Remove duplicates
    sort(planetIds->begin(), planetIds->end());
    planetIds->erase(unique(planetIds->begin(), planetIds->end() ), planetIds->end());

    for (unsigned int x=0; x < planetIds->size(); x++)
    {
			updatePlanet((*planetIds)[x]);
    }
    DEBUG("Planets: " << planetIds->size() << " updated");
  }

	void PlanetManager::updatePlanet(int planetId)
	{
    DEBUG("  Processing planet " << planetId);
		PlanetEntity p = PlanetEntity(planetId);
		p.updateResources();
    p.updateProduction();
    p.save();
	}
}
