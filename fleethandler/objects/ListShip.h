
#ifndef __LISTSHIP__
#define __LISTSHIP__

#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "Object.h"

/**
* ListShip class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class ListShip : public Object {
public: 
	ListShip(mysqlpp::Row &oRow) : Object(oRow) {
		std::cout << "two\n";
		this->id = (int)oRow["shiplist_id"];
		this->typeId = (short)oRow["shiplist_ship_id"];
		this->userId = (int)oRow["shiplist_user_id"];
		this->entityId = (int)oRow["shiplist_entity_id"];
		this->count = (int)oRow["shiplist_count"];
		this->initCount = this->count;
		
		this->special = (bool)oRow["shiplist_special_ship"];
		if (this->special) {
			this->sLevel = (short)oRow["shiplist_special_ship_level"];
			this->sExp = (int)oRow["shiplist_special_ship_exp"];
			this->sBonusWeapon = (short)oRow["shiplist_special_ship_bonus_weapon"];
			this->sBonusStructure = (short)oRow["shiplist_special_ship_bonus_structure"];
			this->sBonusShield = (short)oRow["shiplist_special_ship_bonus_shield"];
			this->sBonusHeal = (short)oRow["shiplist_special_ship_bonus_heal"];
			this->sBonusCapacity = (short)oRow["shiplist_special_ship_bonus_capacity"];
			this->sBonusSpeed = (short)oRow["shiplist_special_ship_bonus_speed"];
			this->sBonusPilots = (short)oRow["shiplist_special_ship_bonus_pilots"];
			this->sBonusTarn = (short)oRow["shiplist_special_ship_bonus_tarn"];
			this->sBonusAntrax = (short)oRow["shiplist_special_ship_bonus_antrax"];
			this->sBonusForsteal = (short)oRow["shiplist_special_ship_bonus_forsteal"];
			this->sBonusBuildDestroy = (short)oRow["shiplist_special_ship_bonus_build_destroy"];
			this->sBonusAntraxFood = (short)oRow["shiplist_special_ship_bonus_antrax_food"];
			this->sBonusDeactivade = (short)oRow["shiplist_special_ship_bonus_deactivade"];
		}
	}
	
	~ListShip() {
		this->save();
	}
	
	double getWfMetal();
	double getWfCrystal();
	double getWfPlastic();
	
	void save();
	
};

#endif
