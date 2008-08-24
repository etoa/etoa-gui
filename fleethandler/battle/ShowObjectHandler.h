
#ifndef __SHOWOBJECTHANDLER__
#define __SHOWOBJECTHANDLER__

#include <mysql++/mysql++.h>
#include "../MysqlHandler.h"

/**
* ObjectType class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class ShowObjectHandler	
{
public:
	/**
	* Eventhandler constructor for all handler classes.
	* Sets the internal MySQL connection pointer
	*/
	ShowObjectHandler(mysqlpp::Row object, short type) {
		this->object=object;
		this->special=0;
		this->cnt=0;
		this->repairCnt=0;
		
		this->oid = (int)object["id"];
		this->type = type;
		this->name = std::string(object["name"]);
		this->structure = (double)object["structure"];
		this->shield = (double)object["shield"];
		this->weapon = (double)object["weapon"];
		this->heal = (double)object["heal"];
		this->metal = (double)object["costs_metal"];
		this->crystal = (double)object["costs_crystal"];
		this->plastic = (double)object["costs_plastic"];
		this->fuel = (double)object["costs_fuel"];
		this->food = (double)object["costs_food"];
		
		if (type>0)
		{
			this->capacity = (double)object["ship_capacity"];
			this->special = (bool)object["special_ship"];
		
			this->needExp = (double)object["special_ship_need_exp"];
			this->expFactor = (double)object["special_ship_exp_factor"];
			this->bonusWeapon = (double)object["special_ship_bonus_weapon"];
			this->bonusStructure = (double)object["special_ship_bonus_structure"];
			this->bonusShield = (double)object["special_ship_bonus_shield"];
			this->bonusHeal = (double)object["special_ship_bonus_heal"];
			this->bonusCapacity = (double)object["special_ship_bonus_capacity"];
		}

	};
	
	int oid;
	short type;
	double cnt, repairCnt;
	std::string name;
	bool special;
	double structure, shield, weapon, heal;
	double metal, crystal, plastic, fuel, food, capacity;
	double needExp, expFactor, bonusWeapon, bonusStructure, bonusShield, bonusHeal, bonusCapacity;

private:
	mysqlpp::Row object;

};

#endif
