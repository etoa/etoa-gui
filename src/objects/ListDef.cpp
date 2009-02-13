
#include "ListDef.h"
	
	double ListDef::getWfMetal() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		Data::Data *data = DataHandler.getDefById(this->getTypeId());
		
		this->rebuildCount = floor((this->initCount - this->count)*this->rebuild);
		int defCount = ceil((this->initCount - (this->count+this->rebuildCount))*config.nget("def_wf_percent",0));
		return (defCount * data->getCostsMetal());
	}
	
	double ListDef::getWfCrystal() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		Data::Data *data = DataHandler.getDefById(this->getTypeId());
		
		this->rebuildCount = floor((this->initCount - this->count)*this->rebuild);
		int defCount = ceil((this->initCount - (this->count+this->rebuildCount))*config.nget("def_wf_percent",0));
		return (defCount * data->getCostsCrystal());
	}
	
	double ListDef::getWfPlastic() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		Data::Data *data = DataHandler.getDefById(this->getTypeId());
		
		this->rebuildCount = floor((this->initCount - this->count)*this->rebuild);
		int defCount = ceil((this->initCount - (this->count+this->rebuildCount))*config.nget("def_wf_percent",0));
		return (defCount * data->getCostsPlastic());
	}
	
	void ListDef::save() {
		if (this->isChanged) {

			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
			mysqlpp::Query query = con->query();

			query << "UPDATE ";
			query << "	deflist ";
			query << "SET ";
			query << "	deflist_count='" << this->getCount() + this->getRebuildCount() << "' ";
			query << "WHERE ";
			query << "	deflist_id='" << this->getId() << "' ";
			query << "	AND deflist_user_id='" << this->getUserId() << "' ";
			query << "LIMIT 1;";
			query.store();
			query.reset();
		}
	}
