
#include "ListShip.h"

	
	double ListShip::getWfMetal() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		ShipData::ShipData *data = DataHandler.getShipById(this->getTypeId());
		int shipCnt = ceil((this->initCount - this->count)*config.nget("ship_wf_percent",0));
		
		this->rebuildIsCalced = true;
		
		return (shipCnt * data->getCostsMetal());
	}
	
	double ListShip::getWfCrystal() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		ShipData::ShipData *data = DataHandler.getShipById(this->getTypeId());
		int shipCnt = ceil((this->initCount - this->count)*config.nget("ship_wf_percent",0));
		
		this->rebuildIsCalced = true;
		
		return (shipCnt * data->getCostsCrystal());
	}
	
	double ListShip::getWfPlastic() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		ShipData::ShipData *data = DataHandler.getShipById(this->getTypeId());
		int shipCnt = ceil((this->initCount - this->count)*config.nget("ship_wf_percent",0));
		
		this->rebuildIsCalced = true;
		
		return (shipCnt * data->getCostsPlastic());
	}
	
	void ListShip::save() {
		if (this->isChanged) {

			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
			mysqlpp::Query query = con->query();

			if (this->getCount() > 0) {
				query << "UPDATE ";
				query << "	shilist ";
				query << "SET ";
				query << "	shiplist_count='" << this->getCount() << "', ";
				query << "	shiplist_special_ship_exp = '" << this->getSExp() << "' ";
				query << "WHERE ";
				query << "	shiplist_id='" << this->getId() << "' ";
				query << "	AND shiplist_user_id='" << this->getUserId() << "' ";
				query << "LIMIT 1;";
				query.store();
				query.reset();
			}
			else {
				query << "DELETE FROM ";
				query << " shiplist ";
				query << "WHERE ";
				query << "	shiplist_id='" << this->getId() << "' ";
				query << "	AND shiplist_user_id='" << this->getUserId() << "' ";
				query << "LIMIT 1;";
				query.store();
				query.reset();
			}
		}
	}
