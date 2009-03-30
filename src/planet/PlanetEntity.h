
#ifndef __PLANETENTITY__
#define __PLANETENTITY__

#include <mysql++/mysql++.h>

#include <ctime>
#include <cmath> 
#include <vector>

#include "../data/DataHandler.h"
#include "../config/ConfigHandler.h"
#include "../util/Functions.h"

namespace planet
{
	
	class PlanetEntity
	{
	public:
		PlanetEntity(int entityId) {
			this->smallUpdate = false;
			
			Config &config = Config::instance();
					
			this->entityId = entityId;
			
			this->store.resize(6);
			this->cnt.resize(8);
			this->ressource.resize(7);
			this->bunker.resize(5);
			
			this->loadData();
			
			DataHandler &DataHandler = DataHandler::instance();
			
			this->race_ = DataHandler.getRaceById(this->raceId);
			this->sol_ = DataHandler.getSolById(this->solType);
			this->planet_ = DataHandler.getPlanetById(this->planetType);
			this->specialist_ = DataHandler.getSpecialistById(this->speicalistId);
			
			this->updateValues();
			
			this->fieldsUsed = 0;
			this->fieldsExtra = 0;
			
			this->bunkerRes = 0;
			
			this->store[0] = config.nget("def_store_capacity", 0);
			this->store[1] = config.nget("def_store_capacity", 0);
			this->store[2] = config.nget("def_store_capacity", 0);
			this->store[3] = config.nget("def_store_capacity", 0);
			this->store[4] = config.nget("def_store_capacity", 0);
			this->store[5] = config.nget("user_start_people", 1);
			
			this->cnt[0] = 0;
			this->cnt[1] = 0;
			this->cnt[2] = 0;
			this->cnt[3] = 0;
			this->cnt[4] = 0;
			this->cnt[5] = 0;
			this->cnt[6] = 0;
			this->cnt[7] = 0;
			
			this->loadBuildlist();
			this->loadShiplist();
			this->loadDeflist();
			this->addBoni();
		}
		
		PlanetEntity(mysqlpp::Row& planet) {
			this->entityId = (int)planet["id"];
			
			this->t = time(0) - (int)planet["planet_last_updated"];
			
			this->smallUpdate = true;
			
			this->store.resize(6);
			this->cnt.resize(8);
			this->ressource.resize(7);
			this->bunker.resize(5);
			
			this->ressource[0] = (double)planet["planet_res_metal"];
			this->ressource[1] = (double)planet["planet_res_crystal"];
			this->ressource[2] = (double)planet["planet_res_plastic"];
			this->ressource[3] = (double)planet["planet_res_fuel"];
			this->ressource[4] = (double)planet["planet_res_food"];
			this->ressource[5] = (double)planet["planet_people"];
			this->ressource[6] = 0;
			
			this->store[0] = (double)planet["planet_store_metal"];
			this->store[1] = (double)planet["planet_store_crystal"];
			this->store[2] = (double)planet["planet_store_plastic"];
			this->store[3] = (double)planet["planet_store_fuel"];
			this->store[4] = (double)planet["planet_store_food"];
			this->store[5] = (double)planet["planet_people_place"];
			
			this->cnt[0] = (double)planet["planet_prod_metal"];
			this->cnt[1] = (double)planet["planet_prod_crystal"];
			this->cnt[2] = (double)planet["planet_prod_plastic"];
			this->cnt[3] = (double)planet["planet_prod_fuel"];
			this->cnt[4] = (double)planet["planet_prod_food"];
			this->cnt[5] = 0;
			this->cnt[6] = 0;
			this->cnt[7] = 0;
			
			this->bunker[0] = (unsigned int)planet["planet_bunker_metal"];
			this->bunker[1] = (unsigned int)planet["planet_bunker_crystal"];
			this->bunker[2] = (unsigned int)planet["planet_bunker_plastic"];
			this->bunker[3] = (unsigned int)planet["planet_bunker_fuel"];
			this->bunker[4] = (unsigned int)planet["planet_bunker_food"];
			
			this->raceId = (int)planet["user_race_id"];
			this->userId = (int)planet["user_id"];
			this->solType = (int)planet["type_id"];
			this->planetType = (int)planet["planet_type_id"];
			
			this->isMain = (bool)planet["planet_user_main"];
			
			this->speicalistId = (int)planet["user_specialist_time"] < time(0) ? 0 : (int)planet["user_specialist_id"];
			
			DataHandler &DataHandler = DataHandler::instance();
			
			this->race_ = DataHandler.getRaceById(this->raceId);
			this->sol_ = DataHandler.getSolById(this->solType);
			this->planet_ = DataHandler.getPlanetById(this->planetType);
			this->specialist_ = DataHandler.getSpecialistById(this->speicalistId);
			
			this->updateValues();
		}
		
		~PlanetEntity() {
			if (smallUpdate)
				this->saveRes();
			else
				this->save();
		}
	
	private:
		bool smallUpdate;
		int entityId;
		int fieldsUsed, fieldsExtra, solarPowerBonus;
		int bunkerRes, bunkered;
		double solarFuelBonus;
		int raceId, userId, planetType, solType, speicalistId;
		int t;
		double birthRate;
		bool isMain;
		
		std::vector<double> store;
		std::vector<double> cnt;
		std::vector<double> ressource;
		std::vector<unsigned int> bunker;
		
		RaceData* race_;
		SolData* sol_;
		PlanetData* planet_;
		SpecialistData* specialist_;
		BuildingData* building_;
		ShipData* ship_;
		DefData* def_;
		
		void updateValues();
		void loadData();
		void loadBuildlist();
		void loadShiplist();
		void loadDeflist();
		void addBoni();
		
		void save();
		void saveRes();
		
	};	
}

#endif
