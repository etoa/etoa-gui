
#ifndef __BUILDINGDATA__
#define __BUILDINGDATA__

#include <mysql++/mysql++.h>
#include <string>

#include "Data.h"

/**
* BuildingData class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class BuildingData : public Data {
public:
	BuildingData(mysqlpp::Row object) : Data(object) {
		this->typeId = (short)object["building_type_id"];	
		this->buildCostsFactor = (double)object["building_build_costs_factor"];
		this->demolishCostsFactor = (double)object["building_demolish_costs_factor"];
		this->powerUse = (int)object["building_power_use"];
		this->powerReq = (int)object["building_power_req"];
		this->fuelUse = (int)object["building_fuel_use"];
		this->prodMetal = (int)object["building_prod_metal"];
		this->prodCrystal = (int)object["building_prod_crystal"];
		this->prodPlastic = (int)object["building_prod_plastic"];
		this->prodFuel = (int)object["building_prod_fuel"];
		this->prodFood = (int)object["building_prod_food"];
		this->prodPower = (int)object["building_prod_power"];
		this->productionFactor = (double)object["building_production_factor"];
		this->storeMetal = (int)object["building_store_metal"];
		this->storeCrystal = (int)object["building_store_crystal"];
		this->storePlastic = (int)object["building_store_plastic"];
		this->storeFuel = (int)object["building_store_fuel"];
		this->storeFood = (int)object["building_store_food"];
		this->storeFactor = (double)object["building_store_factor"];
		this->peoplePlace = (int)object["building_people_place"];
		this->lastLevel = (short)object["building_last_level"];
		this->fields = (short)object["building_fields"];
		this->show = (bool)object["building_show"];
		this->order = (short)object["building_order"];
		this->fieldsprovide = (short)object["building_fieldsprovide"];
		this->workplace = (bool)object["building_workplace"];
		this->bunkerRes = (unsigned int)object["building_bunker_res"];
		this->bunkerFleetCount = (unsigned int)object["building_bunker_fleet_count"];
		this->bunkerFleetSpace = (unsigned int)object["building_bunker_fleet_space"];
	}
	
	short getTypeId();
	double getBuildCostsFactor();
	double getDemolishCostsFactor();
	int getPowerUse();
	int getPowerReq();
	int getFuelUse();
	int getProdMetal();
	int getProdCrystal();
	int getProdPlastic();
	int getProdFuel();
	int getProdFood();
	int getProdPower();
	double getProductionFactor();
	int getStoreMetal();
	int getStoreCrystal();
	int getStorePlastic();
	int getStoreFuel();
	int getStoreFood();
	double getStoreFactor();
	int getPeoplePlace();
	short getLastLevel();
	short getFields();
	bool getShow();
	short getOrder();
	short getFieldsprovide();
	bool getWorkplace();
	unsigned int getBunkerRes();
	unsigned int getBunkerFleetCount();
	unsigned int getBunkerFleetSpace();
			
private:
	short typeId;
	double buildCostsFactor;
	double demolishCostsFactor;
	int powerUse, powerReq;
	int fuelUse;
	int prodMetal, prodCrystal, prodPlastic, prodFuel, prodFood, prodPower;
	double productionFactor;
	int storeMetal, storeCrystal, storePlastic, storeFuel, storeFood;
	double storeFactor;
	int peoplePlace;
	short lastLevel;
	short fields;
	bool show;
	short order;
	short fieldsprovide;
	bool workplace;
	unsigned int bunkerRes;
	unsigned int bunkerFleetCount;
	unsigned int bunkerFleetSpace;
};

#endif
