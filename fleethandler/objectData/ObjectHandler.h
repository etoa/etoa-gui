
#ifndef __OBJECTHANDLER__
#define __OBJECTHANDLER__

#include <mysql++/mysql++.h>
#include <string>
#include "../MysqlHandler.h"

/**
* ObjectType class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class ObjectHandler	
{
public:
	/**
	* Object Class
	* 
	*/
	ObjectHandler(mysqlpp::Row object, short type) {
		this->objectId = (int)object["id"];
		this->objectType = type;
		this->objectName = std::string(object["name"]);
		this->objectCostsMetal = (int)object["costs_metal"];
		this->objectCostsCrystal = (int)object["costs_crystal"];
		this->objectCostsPlastic = (int)object["costs_plastic"];
		this->objectCostsFuel  = (int)object["costs_fuel"];
		this->objectCostsFood  = (int)object["costs_fuel"];
		this->objectCostsPower  = (int)object["costs_power"];
		this->objectPowerUse = (int)object["power_use"];
		this->objectFuelUse = (int)object["fuel_use"];
		this->objectShow = (bool)object["showable"];
		this->objectBuildable = (bool)object["buildable"];
		this->objectOrder = (short)object["ordered"];
		this->objectHeal = (int)object["heal"];
		this->objectStructure = (int)object["structure"];
		this->objectShield = (int)object["shield"];
		this->objectWeapon = (int)object["weapon"];
		this->objectRaceId = (short)object["race_id"];
		this->objectCatId = (short)object["cat_id"];
		this->objectMaxCount = (int)object["max_count"];
		this->objectPoints = (double)object["points"];

		
		if (type>0) {
			this->objectTypeId = object["type_id"];
			this->objectFuelUseLanding = (int)object["fuel_use_landing"];
			this->objectFuelUseLaunch = (int)object["fuel_use_launch"];
			this->objectFuelUseEconomize = (int)object["fuel_use_economize"];
			this->objectProdPower = (int)object["prod_power"];
			this->objectCapacity = (int)object["capacity"];
			this->objectPeopleCapacity = (int)object["people_capacity"];
			this->objectPilots = (short)object["pilots"];
			this->objectSpeed = (int)object["speed"];
			this->objectTime2Start = (int)object["time2start"];
			this->objectTime2Land = (int)object["time2start"];
			this->objectActions = std::string(object["actions"]);
			this->objectLaunchable = (bool)object["launchable"];
			this->objectFieldsProvide = (short)object["fieldsprovide"];
			this->objectFakeable = (bool)object["fakeable"];
			this->objectSpecial = (bool)object["special"];
			this->objectMaxLevel = (short)object["max_level"];
			this->objectNeedExp = (int)object["need_exp"];
			this->objectExpFactor = (float)object["exp_factor"];
			this->objectBonusWeapon = (float)object["bonus_weapon"];
			this->objectBonusStructure = (float)object["bonus_structure"];
			this->objectBonusShield = (float)object["bonus_shield"];
			this->objectBonusHeal = (float)object["bonus_heal"];
			this->objectBonusCapacity = (float)object["bonus_capacity"];
			this->objectBonusSpeed = (float)object["bonus_speed"];
			this->objectBonusPilots = (float)object["bonus_pilots"];
			this->objectBonusTarn = (float)object["bonus_tarn"];
			this->objectBonusAntrax = (float)object["bonus_antrax"];
			this->objectBonusForsteal = (float)object["bonus_forsteal"];
			this->objectBonusBuildDestroy = (float)object["bonus_build_destroy"];
			this->objectBonusAntraxFood = (float)object["bonus_antrax_food"];
			this->objectBonusDeactivade = (float)object["bonus_deactivade"];
			this->objectAllianceBuildingLevel = (short)object["alliance_building_level"];
			this->objectAllianceCosts = (short)object["alliance_costs"];

			this->objectFields = 0;
			this->objectJam = 0;
		}
		else {
			this->objectTypeId=0;
			this->objectFuelUseLaunch = 0;
			this->objectFuelUseLanding = 0;
			this->objectFuelUseEconomize = 0;
			this->objectProdPower = 0;
			this->objectCapacity = 0;
			this->objectPeopleCapacity = 0;
			this->objectPilots = 0;
			this->objectSpeed = 0;
			this->objectTime2Start = 0;
			this->objectTime2Land = 0;
			this->objectLaunchable = 0;
			this->objectFieldsProvide = 0;
			this->objectFakeable = 0;
			this->objectSpecial = 0;
			this->objectMaxLevel = 0;
			this->objectNeedExp = 0;
			this->objectExpFactor = 0;
			this->objectBonusWeapon = 0;
			this->objectBonusStructure = 0;
			this->objectBonusShield = 0;
			this->objectBonusHeal = 0;
			this->objectBonusCapacity = 0;
			this->objectBonusSpeed = 0;
			this->objectBonusPilots = 0;
			this->objectBonusTarn = 0;
			this->objectBonusAntrax = 0;
			this->objectBonusForsteal = 0;
			this->objectBonusBuildDestroy = 0;
			this->objectBonusAntraxFood = 0;
			this->objectBonusDeactivade = 0;
			this->objectAllianceBuildingLevel = 0;
			this->objectAllianceCosts = 0;
			
			this->objectFields = (short)object["field"];
			this->objectJam = (short)object["jam"];
		}

	};
		
	int id();
	short type();
	std::string name();
	short typeId();
	int costs();
	int costsMetal();
	int costsCrystal();
	int costsPlastic();
	int costsFuel();
	int costsFood();
	int costsPower();
	int powerUse();
	int fuelUse();
	int fuelUseLaunch();
	int fuelUseLanding();
	int fuelUseEconomize();
	int prodPower();
	int capacity();
	int peopleCapacity();
	short pilots();
	int speed();
	int time2Start();
	int time2Land();
	bool show();
	bool buildable();
	short order();
	bool actions(std::string action);
	int heal();
	int structure();
	int shield();
	int weapon();
	short raceId();
	bool launchable();
	short fieldsProvide();
	short catId();
	bool fakeable();
	bool special();
	int maxCount();
	short maxLevel();
	int needExp();
	float expFactor();
	float bonusWeapon();
	float bonusStructure();
	float bonusShield();
	float bonusHeal();
	float bonusCapacity();
	float bonusSpeed();
	float bonusPilots();
	float bonusTarn();
	float bonusAntrax();
	float bonusForsteal();
	float bonusBuildDestroy();
	float bonusAntraxFood();
	float bonusDeactivade();
	float points();
	short allianceBuildingLevel();
	short allianceCosts();
	short fields();
	short jam();
	
private:
	
	int objectId;
	short objectType;
	std::string objectName;
	short objectTypeId;
	int objectCostsMetal;
	int objectCostsCrystal;
	int objectCostsPlastic;
	int objectCostsFuel;
	int objectCostsFood;
	int objectCostsPower;
	int objectPowerUse;
	int objectFuelUse;
	int objectFuelUseLaunch;
	int objectFuelUseLanding;
	int objectFuelUseEconomize;
	int objectProdPower;
	int objectCapacity;
	int objectPeopleCapacity;
	short objectPilots;
	int objectSpeed;
	int objectTime2Start;
	int objectTime2Land;
	bool objectShow;
	bool objectBuildable;
	short objectOrder;
	std::string objectActions;
	int objectHeal;
	int objectStructure;
	int objectShield;
	int objectWeapon;
	short objectRaceId;
	bool objectLaunchable;
	short objectFieldsProvide;
	short objectCatId;
	bool objectFakeable;
	bool objectSpecial;
	int objectMaxCount;
	short objectMaxLevel;
	int objectNeedExp;
	float objectExpFactor;
	float objectBonusWeapon;
	float objectBonusStructure;
	float objectBonusShield;
	float objectBonusHeal;
	float objectBonusCapacity;
	float objectBonusSpeed;
	float objectBonusPilots;
	float objectBonusTarn;
	float objectBonusAntrax;
	float objectBonusForsteal;
	float objectBonusBuildDestroy;
	float objectBonusAntraxFood;
	float objectBonusDeactivade;
	float objectPoints;
	short objectAllianceBuildingLevel;
	short objectAllianceCosts;
	short objectFields;
	short objectJam;

};

#endif
