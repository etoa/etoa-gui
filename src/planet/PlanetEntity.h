
#ifndef __PLANETENTITY__
#define __PLANETENTITY__

#define MYSQLPP_MYSQL_HEADERS_BURIED
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
		PlanetEntity(int entityId);
		PlanetEntity(mysqlpp::Row& planet);
		~PlanetEntity();
	
	private:
		bool smallUpdate;
		int entityId;
		int fieldsUsed, fieldsExtra, solarPowerBonus;
		int bunkerRes, bunkered;
		double solarFuelBonus;
		int raceId, userId, planetType, solType, speicalistId;
		int t;
		double birthRate;
		bool isMain, isUmod;
		
		std::vector<double> store;
		std::vector<double> cnt;
		std::vector<double> ressource;
		std::vector<double> bunker;
		
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
