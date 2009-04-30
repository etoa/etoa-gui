
#include "FleetShip.h"

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
	
	void FleetShip::save() {
		if (this->isChanged || this->getCount()!=this->initCount) {

			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
			mysqlpp::Query query = con->query();

			if (this->getCount() > 0) {
				query << "UPDATE ";
				query << "	fleet_ships ";
				query << "SET ";
				query << "	fs_ship_cnt='" << this->getCount() << "', ";
				query << "	fs_special_ship_exp= '" << this->getSExp() << "' ";
				query << "WHERE ";
				query << "	fs_id='" << this->getId() << "' ";
				query << "LIMIT 1;";
				query.store();
				query.reset();
			}
			else {
				query << "DELETE FROM ";
				query << " fleet_ships ";
				query << "WHERE ";
				query << "	fs_id='" << this->getId() << "' ";
				query << "LIMIT 1;";
				query.store();
				query.reset();
			}
		}
	}

