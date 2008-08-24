
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
		
		this->oId = (int)object["id"];
		this->type = type;
		this->cnt = (int)object["cnt"];
		this->userId = (int)object["user_id"];
		
		if (type==1) {
			this->fleetId = (int)object["fleet_id"];
			this->planetId = 0;
		}
		else {
			this->fleetId = 0;
			this->planetId = (int)object["planet_id"];
		}

		if (type>0)
		{
			this->type=1;
			this->shipExp = (double)object["special_ship_exp"];
			this->shipLevel = (double)object["special_ship_level"];
			this->shipsBonusWeapon = (double)object["special_ship_bonus_weapon"];
			this->shipsBonusStructure = (double)object["special_ship_bonus_structure"];
			this->shipsBonusShield = (double)object["special_ship_bonus_shield"];
			this->shipsBonusWeapon = (double)object["special_ship_bonus_weapon"];
			this->shipsBonusHeal = (double)object["special_ship_bonus_heal"];
			this->shipsBonusCapacity = (double)object["special_ship_bonus_capacity"];
			this->shipsBonusSpeed = (double)object["special_ship_bonus_speed"];
			this->shipsBonusPilots = (double)object["special_ship_bonus_pilots"];
			this->shipsBonusTarn = (double)object["special_ship_bonus_tarn"];
			this->shipsBonusAntrax = (double)object["special_ship_bonus_antrax"];
			this->shipsBonusForsteal = (double)object["special_ship_bonus_forsteal"];
			this->shipsBonusDestroy = (double)object["special_ship_bonus_build_destroy"];
			this->shipsBonusAntraxFood = (double)object["special_ship_bonus_antrax_food"];
			this->shipsBonusDeactivade = (double)object["special_ship_bonus_deactivade"];
		}
		else
		{
			this->shipExp = 0;
			this->shipLevel = 0;
			this->shipsBonusWeapon = 0;
			this->shipsBonusStructure = 0;
			this->shipsBonusShield = 0;
			this->shipsBonusWeapon = 0;
			this->shipsBonusHeal = 0;
			this->shipsBonusCapacity = 0;
			this->shipsBonusSpeed = 0;
			this->shipsBonusPilots = 0;
			this->shipsBonusTarn = 0;
			this->shipsBonusAntrax = 0;
			this->shipsBonusForsteal = 0;
			this->shipsBonusDestroy = 0;
			this->shipsBonusAntraxFood = 0;
			this->shipsBonusDeactivade = 0;
		}

	};
	
	int oId;
	int userId;
	int fleetId;
	int planetId;
	short type;
	double cnt, newCnt, repairCnt;
	double shipExp, shipLevel, shipsBonusWeapon, shipsBonusStructure, shipsBonusShield, shipsBonusHeal;
	double shipsBonusCapacity, shipsBonusSpeed, shipsBonusPilots;
	double shipsBonusTarn, shipsBonusAntrax, shipsBonusForsteal, shipsBonusDestroy, shipsBonusAntraxFood, shipsBonusDeactivade;

private:
	mysqlpp::Row object;
	
};

#endif
