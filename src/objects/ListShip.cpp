
#include "ListShip.h"


	
	double ListShip::getWfMetal() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		ShipData::ShipData *data = DataHandler.getShipById(this->getTypeId());
		int shipCnt = (int)ceil((this->initCount - this->count)*config.nget("ship_wf_percent",0));
		
		this->rebuildIsCalced = true;
		
		return (shipCnt * data->getCostsMetal());
	}
	
	double ListShip::getWfCrystal() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		ShipData::ShipData *data = DataHandler.getShipById(this->getTypeId());
		int shipCnt = (int)ceil((this->initCount - this->count)*config.nget("ship_wf_percent",0));
		
		this->rebuildIsCalced = true;
		
		return (shipCnt * data->getCostsCrystal());
	}
	
	double ListShip::getWfPlastic() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		ShipData::ShipData *data = DataHandler.getShipById(this->getTypeId());
		int shipCnt = (int)ceil((this->initCount - this->count)*config.nget("ship_wf_percent",0));
		
		this->rebuildIsCalced = true;
		
		return (shipCnt * data->getCostsPlastic());
	}
	
	void ListShip::save() {
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
