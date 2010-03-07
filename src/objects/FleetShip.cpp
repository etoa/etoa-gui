
#include "FleetShip.h"

	FleetShip::FleetShip(mysqlpp::Row &oRow) : Object(oRow) {
		this->id = (int)oRow["fs_id"];
		this->typeId = (short)oRow["fs_ship_id"];
		this->userId = 0;
		this->fleetId = (int)oRow["fs_fleet_id"];
		this->count = (double)oRow["fs_ship_cnt"];
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
	
	FleetShip::~FleetShip() {
		if (this->isChanged || this->getCount()!=this->initCount) {

			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
			mysqlpp::Query query = con->query();

			if (this->getCount() > 0) {
				query << "UPDATE "
					<< "	fleet_ships "
					<< "SET "
					<< "	fs_ship_cnt='" << this->getCount() << "', "
					<< "	fs_special_ship_exp= '" << this->getSExp() << "' "
					<< "WHERE "
					<< "	fs_id='" << this->getId() << "' "
					<< "LIMIT 1;";
				query.store();
				query.reset();
			}
			else {
				query << "DELETE FROM "
					<< " fleet_ships "
					<< "WHERE "
					<< "	fs_id='" << this->getId() << "' "
					<< "LIMIT 1;";
				query.store();
				query.reset();
			}
		}
	}

	double FleetShip::getWfMetal() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		ShipData::ShipData *data = DataHandler.getShipById(this->getTypeId());
		double structure = data->getStructure() + data->getShield();
		if (!structure) this->count = 0;
		int shipCnt = (int)ceil((this->initCount - this->count) * config.nget("ship_wf_percent",0));
		
		this->rebuildIsCalced = true;
		
		return (shipCnt * data->getCostsMetal());
	}
	
	double FleetShip::getWfCrystal() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		ShipData::ShipData *data = DataHandler.getShipById(this->getTypeId());
		double structure = data->getStructure() + data->getShield();
		if (!structure) this->count = 0;
		int shipCnt = (int)ceil((this->initCount - this->count)*config.nget("ship_wf_percent",0));
		
		this->rebuildIsCalced = true;
		
		return (shipCnt * data->getCostsCrystal());
	}
	
	double FleetShip::getWfPlastic() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		ShipData::ShipData *data = DataHandler.getShipById(this->getTypeId());
		double structure = data->getStructure() + data->getShield();
		if (!structure) this->count = 0;
		int shipCnt = (int)ceil((this->initCount - this->count)*config.nget("ship_wf_percent",0));
		
		this->rebuildIsCalced = true;
		
		return (shipCnt * data->getCostsPlastic());
	}
