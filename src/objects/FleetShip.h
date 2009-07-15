
#ifndef __FLEETSHIP__
#define __FLEETSHIP__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "Object.h"

/**
* FleetShip class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class FleetShip : public Object {
public: 
	FleetShip(mysqlpp::Row &oRow) : Object(oRow) {
		this->id = (int)oRow["fs_id"];
		this->typeId = (short)oRow["fs_ship_id"];
		this->userId = 0;
		this->fleetId = (int)oRow["fs_fleet_id"];
		this->count = (int)oRow["fs_ship_cnt"];
		this->initCount = this->count;
		this->isFaked = (bool)oRow["fs_ship_faked"];
		
		this->special = (bool)oRow["fs_special_ship"];
		this->sLevel = (short)oRow["fs_special_ship_level"];
		this->sExp = (double)oRow["fs_special_ship_exp"];
		this->sBonusWeapon = (short)oRow["fs_special_ship_bonus_weapon"];
		this->sBonusStructure = (short)oRow["fs_special_ship_bonus_structure"];
		this->sBonusShield = (short)oRow["fs_special_ship_bonus_shield"];
		this->sBonusHeal = (short)oRow["fs_special_ship_bonus_heal"];
		this->sBonusCapacity = (short)oRow["fs_special_ship_bonus_capacity"];
		this->sBonusSpeed = (short)oRow["fs_special_ship_bonus_speed"];
		this->sBonusPilots = (short)oRow["fs_special_ship_bonus_pilots"];
		this->sBonusTarn = (short)oRow["fs_special_ship_bonus_tarn"];
		this->sBonusAntrax = (short)oRow["fs_special_ship_bonus_antrax"];
		this->sBonusForsteal = (short)oRow["fs_special_ship_bonus_forsteal"];
		this->sBonusBuildDestroy = (short)oRow["fs_special_ship_bonus_build_destroy"];
		this->sBonusAntraxFood = (short)oRow["fs_special_ship_bonus_antrax_food"];
		this->sBonusDeactivade = (short)oRow["fs_special_ship_bonus_deactivade"];
	}
	
	~FleetShip() {
		this->save();
	}
	
	double getWfMetal();
	double getWfCrystal();
	double getWfPlastic();
	
	void save();
	
};

#endif
