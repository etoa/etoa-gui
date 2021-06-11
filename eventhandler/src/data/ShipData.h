
#ifndef __SHIPDATA__
#define __SHIPDATA__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>
#include <string>

#include "Data.h"

/**
* ShipData class
*
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class ShipData : public Data
{
public:
	ShipData(mysqlpp::Row object) : Data(object){
		this->typeId = (short)object["ship_type_id"];
		this->powerUse = (int)object["ship_power_use"];
		this->fuelUse = (int)object["ship_fuel_use"];
		this->show = (bool)object["ship_show"];
		this->buildable = (bool)object["ship_buildable"];
		this->order = (short)object["ship_order"];
		this->heal = (int)object["ship_heal"];
		this->structure = (double)object["ship_structure"];
		this->shield = (double)object["ship_shield"];
		this->weapon = (double)object["ship_weapon"];
		this->bountyBonus = (double)object["ship_bounty_bonus"];
		this->raceId = (short)object["ship_race_id"];
		this->catId = (short)object["ship_cat_id"];
		this->maxCount = (double)object["ship_max_count"];
		this->points = (double)object["ship_points"];
		this->fuelUseLanding = (int)object["ship_fuel_use_launch"];
		this->fuelUseLaunch = (int)object["ship_fuel_use_landing"];
		this->prodPower = (int)object["ship_prod_power"];
		this->capacity = (double)object["ship_capacity"];
		this->peopleCapacity = (double)object["ship_people_capacity"];
		this->pilots = (short)object["ship_pilots"];
		this->speed = (int)object["ship_speed"];
		this->time2Start = (int)object["ship_time2start"];
		this->time2Land = (int)object["ship_time2land"];
		this->actions = std::string(object["ship_actions"]);
		this->launchable = (bool)object["ship_launchable"];
		this->fieldsProvide = (short)object["ship_fieldsprovide"];
		this->fakeable = (bool)object["ship_fakeable"];
		this->special = (bool)object["special_ship"];
		this->maxLevel = (short)object["special_ship_max_level"];
		this->needExp = (int)object["special_ship_need_exp"];
		this->expFactor = (double)object["special_ship_exp_factor"];
		this->bonusWeapon = (double)object["special_ship_bonus_weapon"];
		this->bonusStructure = (double)object["special_ship_bonus_structure"];
		this->bonusShield = (double)object["special_ship_bonus_shield"];
		this->bonusHeal = (double)object["special_ship_bonus_heal"];
		this->bonusCapacity = (double)object["special_ship_bonus_capacity"];
		this->bonusSpeed = (double)object["special_ship_bonus_speed"];
		this->bonusPilots = (double)object["special_ship_bonus_pilots"];
		this->bonusTarn = (double)object["special_ship_bonus_tarn"];
		this->bonusAntrax = (double)object["special_ship_bonus_antrax"];
		this->bonusForsteal = (double)object["special_ship_bonus_forsteal"];
		this->bonusBuildDestroy = (double)object["special_ship_bonus_build_destroy"];
		this->bonusAntraxFood = (double)object["special_ship_bonus_antrax_food"];
		this->bonusDeactivade = (double)object["special_ship_bonus_deactivade"];
		this->allianceBuildingLevel = (short)object["ship_alliance_shipyard_level"];
		this->allianceCosts = (short)object["ship_alliance_costs"];
	}

	short getTypeId();
	int getPowerUse();
	int getFuelUse();
	int getFuelUseLaunch();
	int getFuelUseLanding();
	int getProdPower();
	double getCapacity();
	double getPeopleCapacity();
	short getPilots();
	int getSpeed();
	int getTime2Start();
	int getTime2Land();
	bool getShow();
	bool getBuildable();
	short getOrder();
	bool getActions(std::string action);
	double getBountyBonus();
	double getHeal();
	double getStructure();
	double getShield();
	double getWeapon();
	short getRaceId();
	bool getLaunchable();
	short getFieldsProvide();
	short getCatId();
	bool getFakeable();
	bool getSpecial();
	double getMaxCount();
	short getMaxLevel();
	int getNeedExp();
	double getExpFactor();
	double getBonusWeapon();
	double getBonusStructure();
	double getBonusShield();
	double getBonusHeal();
	double getBonusCapacity();
	double getBonusSpeed();
	double getBonusPilots();
	double getBonusTarn();
	double getBonusAntrax();
	double getBonusForsteal();
	double getBonusBuildDestroy();
	double getBonusAntraxFood();
	double getBonusDeactivade();
	double getPoints();
	short getAllianceBuildingLevel();
	short getAllianceCosts();
	bool isCivilShip();

private:

	short typeId;
	int powerUse;
	int fuelUse;
	int fuelUseLaunch;
	int fuelUseLanding;
	int prodPower;
	double capacity;
	double peopleCapacity;
	short pilots;
	int speed;
	int time2Start;
	int time2Land;
	bool show;
	bool buildable;
	short order;
	std::string actions;
	double bountyBonus;
	double heal;
	double structure;
	double shield;
	double weapon;
	short raceId;
	bool launchable;
	short fieldsProvide;
	short catId;
	bool fakeable;
	bool special;
	double maxCount;
	short maxLevel;
	int needExp;
	double expFactor;
	double bonusWeapon;
	double bonusStructure;
	double bonusShield;
	double bonusHeal;
	double bonusCapacity;
	double bonusSpeed;
	double bonusPilots;
	double bonusTarn;
	double bonusAntrax;
	double bonusForsteal;
	double bonusBuildDestroy;
	double bonusAntraxFood;
	double bonusDeactivade;
	double points;
	short allianceBuildingLevel;
	short allianceCosts;
};

#endif
