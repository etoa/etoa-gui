
#ifndef __OBJECTHANDLER__
#define __OBJECTHANDLER__

#include <mysql++/mysql++.h>
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
	* Eventhandler constructor for all handler classes.
	* Sets the internal MySQL connection pointer
	*/
	ObjectHandler(mysqlpp::Row object, short type) {
		this->object=object;
		
		if (type == 0)
		{
			sid = (int)object["def_id"];
			cnt = (int)object["deflist_count"];
			newCnt = 0;
			name = std::string(object["def_name"]);
			structure = (double)object["def_structure"];
			shield = (double)object["def_shield"];
			weapon = (double)object["def_weapon"];
			heal = (double)object["def_heal"];
			metal = (double)object["def_costs_metal"];
			crystal = (double)object["def_costs_crystal"];
			plastic = (double)object["def_costs_plastic"];
			fuel = (double)object["def_costs_fuel"];
			food = (double)object["def_costs_food"];
		}
		else
		{

			sid = (int)object["ship_id"];
			
			if (type == 2)
			{
				cnt = (int)object["fs_ship_cnt"];
			}
			else
			{
				cnt = (int)object["shiplist_count"];
			}
			newCnt = 0;
			name = std::string(object["ship_name"]);
			structure = (double)object["ship_structure"];
			shield = (double)object["ship_shield"];
			weapon = (double)object["ship_weapon"];
			heal = (double)object["ship_heal"];
			metal = (double)object["ship_costs_metal"];
			crystal = (double)object["ship_costs_crystal"];
			plastic = (double)object["ship_costs_plastic"];
			fuel = (double)object["ship_costs_fuel"];
			food = (double)object["ship_costs_food"];
			capacity = (double)object["ship_capacity"];
			special = (bool)object["special_ship"];
		
			needExp = (double)object["special_ship_need_exp"];
			expFactor = (double)object["special_ship_exp_factor"];
			bonusWeapon = (double)object["special_ship_bonus_weapon"];
			bonusStructure = (double)object["special_ship_bonus_structure"];
			bonusShield = (double)object["special_ship_bonus_shield"];
			bonusHeal = (double)object["special_ship_bonus_heal"];
			bonusCapacity = (double)object["special_ship_bonus_capacity"];
		
			if (type == 2)
			{
				shipExp = (double)object["fs_special_ship_exp"];
				shipLevel = (double)object["fs_special_ship_level"];
				shipsBonusWeapon = (double)object["fs_special_ship_bonus_weapon"];
				shipBonusStructure = (double)object["fs_special_ship_bonus_structure"];
				shipsBonusShield = (double)object["fs_special_ship_bonus_shield"];
				shipsBonusWeapon = (double)object["fs_special_ship_bonus_weapon"];
				shipBonusHeal = (double)object["fs_special_ship_bonus_heal"];
				shipBonusCapacity = (double)object["fs_special_ship_bonus_capacity"];
				shipsBonusSpeed = (double)object["fs_special_ship_bonus_speed"];
				shipsBonusPilots = (double)object["fs_special_ship_bonus_pilots"];
				shipsBonusTarn = (double)object["fs_special_ship_bonus_tarn"];
				shipBonusAntrax = (double)object["fs_special_ship_bonus_antrax"];
				shipsBonusForsteal = (double)object["fs_special_ship_bonus_forsteal"];
				shipsBonusDestroy = (double)object["fs_special_ship_bonus_build_destroy"];
				shipsBonusAntraxFood = (double)object["fs_special_ship_bonus_antrax_food"];
				shipBonusDeactivade = (double)object["fs_special_ship_bonus_deactivade"];
			}
			else
			{
				shipExp =				(double)object["shiplist_special_ship_exp"];
				shipLevel =				(double)object["shiplist_special_ship_level"];
				shipsBonusWeapon =		(double)object["shiplist_special_ship_bonus_weapon"];
				shipBonusStructure =	(double)object["shiplist_special_ship_bonus_structure"];
				shipsBonusShield =		(double)object["shiplist_special_ship_bonus_shield"];
				shipsBonusWeapon =		(double)object["shiplist_special_ship_bonus_weapon"];
				shipBonusHeal =			(double)object["shiplist_special_ship_bonus_heal"];
				shipBonusCapacity =		(double)object["shiplist_special_ship_bonus_capacity"];
				shipsBonusSpeed =		(double)object["shiplist_special_ship_bonus_speed"];
				shipsBonusPilots =		(double)object["shiplist_special_ship_bonus_pilots"];
				shipsBonusTarn =		(double)object["shiplist_special_ship_bonus_tarn"];
				shipBonusAntrax =		(double)object["shiplist_special_ship_bonus_antrax"];
				shipsBonusForsteal =	(double)object["shiplist_special_ship_bonus_forsteal"];
				shipsBonusDestroy =		(double)object["shiplist_special_ship_bonus_build_destroy"];
				shipsBonusAntraxFood =	(double)object["shiplist_special_ship_bonus_antrax_food"];
				shipBonusDeactivade =	(double)object["shiplist_special_ship_bonus_deactivade"];
			}
		}
	};
	
	int sid;
	double cnt, newCnt, repairCnt;
	std::string name;
	bool special;
	double structure, shield, weapon, heal;
	double metal, crystal, plastic, fuel, food, capacity;
	double needExp, expFactor, bonusWeapon, bonusStructure, bonusShield, bonusHeal, bonusCapacity;
	double shipExp, shipLevel, shipsBonusWeapon, shipBonusStructure, shipsBonusShield, shipBonusHeal;
	double shipBonusCapacity, shipsBonusSpeed, shipsBonusPilots;
	double shipsBonusTarn, shipBonusAntrax, shipsBonusForsteal, shipsBonusDestroy, shipsBonusAntraxFood, shipBonusDeactivade;


private:
	mysqlpp::Row object;
	

};

#endif
