
#include "ListDef.h"

	ListDef::ListDef(mysqlpp::Row &oRow,double rebuild) : Object(oRow) {
		this->id = (int)oRow["deflist_id"];
		this->typeId = (short)oRow["deflist_def_id"];
		this->entityId = (int)oRow["deflist_entity_id"];
		this->userId = (int)oRow["deflist_user_id"];
		this->count = (int)oRow["deflist_count"];
		this->initCount = this->count;
		this->rebuildCount = -1;
		
		Config &config = Config::instance();
		this->rebuild = rebuild + config.nget("def_restore_percent",0) - 1;
	}
	
	ListDef::~ListDef() {
		if (this->isChanged) {

			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
			mysqlpp::Query query = con->query();

			query << "UPDATE "
				<< "	deflist "
				<< "SET "
				<< "	deflist_count='" << this->getCount() + this->getRebuildCount() << "' "
				<< "WHERE "
				<< "	deflist_id='" << this->getId() << "' "
				<< "	AND deflist_user_id='" << this->getUserId() << "' "
				<< "LIMIT 1;";
			query.store();
			query.reset();
		}
	}
	
	double ListDef::getWfMetal() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		Data *data = DataHandler.getDefById(this->getTypeId());
		
		this->rebuildCount = (int)round((this->initCount - this->count)*this->rebuild);
		int defCount = (int)ceil((this->initCount - (this->count+this->rebuildCount))*config.nget("def_wf_percent",0));
		return (defCount * data->getCostsMetal());
	}
	
	double ListDef::getWfCrystal() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		Data *data = DataHandler.getDefById(this->getTypeId());
		
		this->rebuildCount = (int)round((this->initCount - this->count)*this->rebuild);
		int defCount = (int)ceil((this->initCount - (this->count+this->rebuildCount))*config.nget("def_wf_percent",0));
		return (defCount * data->getCostsCrystal());
	}
	
	double ListDef::getWfPlastic() {
		Config &config = Config::instance();
		
		DataHandler &DataHandler = DataHandler::instance();
		Data *data = DataHandler.getDefById(this->getTypeId());
		
		this->rebuildCount = (int)round((this->initCount - this->count)*this->rebuild);
		int defCount = (int)ceil((this->initCount - (this->count+this->rebuildCount))*config.nget("def_wf_percent",0));
		return (defCount * data->getCostsPlastic());
	}
