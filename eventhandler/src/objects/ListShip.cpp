
#include "ListShip.h"

	ListShip::ListShip(mysqlpp::Row &oRow) : Object(oRow) {
		this->id = (int)oRow["shiplist_id"];
		this->typeId = (short)oRow["shiplist_ship_id"];
		this->userId = (int)oRow["shiplist_user_id"];
		this->entityId = (int)oRow["shiplist_entity_id"];
		this->count = (int)oRow["shiplist_count"];
		this->initCount = this->count;
		
		this->special = (bool)oRow["shiplist_special_ship"];
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
	
	ListShip::~ListShip() {
		if (this->isChanged || this->getCount()!=this->initCount) {
			
			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
			mysqlpp::Query query = con->query();

			query << "UPDATE "
				<< "	shiplist "
				<< "SET "
				<< "	shiplist_count='" << this->getCount() << "', "
				<< "	shiplist_special_ship_exp = '" << this->getSExp() << "' "
				<< "WHERE "
				<< "	shiplist_id='" << this->getId() << "' "
				<< "	AND shiplist_user_id='" << this->getUserId() << "' "
				<< "LIMIT 1;";
			query.store();
			query.reset();
		}
	}
	
	double ListShip::getWfMetal() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		ShipData *data = DataHandler.getShipById(this->getTypeId());
		int shipCnt = (int)ceil((this->initCount - this->count)*config.nget("ship_wf_percent",0));
		
		this->rebuildIsCalced = true;
		
		return (shipCnt * data->getCostsMetal());
	}
	
	double ListShip::getWfCrystal() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		ShipData *data = DataHandler.getShipById(this->getTypeId());
		int shipCnt = (int)ceil((this->initCount - this->count)*config.nget("ship_wf_percent",0));
		
		this->rebuildIsCalced = true;
		
		return (shipCnt * data->getCostsCrystal());
	}
	
	double ListShip::getWfPlastic() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		ShipData *data = DataHandler.getShipById(this->getTypeId());
		int shipCnt = (int)ceil((this->initCount - this->count)*config.nget("ship_wf_percent",0));
		
		this->rebuildIsCalced = true;
		
		return (shipCnt * data->getCostsPlastic());
	}
