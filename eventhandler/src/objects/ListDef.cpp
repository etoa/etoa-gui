
#include "ListDef.h"

	ListDef::ListDef(mysqlpp::Row &oRow,double rebuild) : Object(oRow) {
		this->id = (int)oRow["deflist_id"];
		this->typeId = (short)oRow["deflist_def_id"];
		this->entityId = (int)oRow["deflist_entity_id"];
		this->userId = (int)oRow["deflist_user_id"];
		this->count = (int)oRow["deflist_count"];
		this->initCount = this->count;
		this->rebuildCount = 0;
		
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
	
	int ListDef::getDefCnt()
	{
		this->rebuildCount = (int)round((this->initCount - this->count)*this->rebuild);
		return (int)ceil((this->initCount - (this->count+this->rebuildCount))*Config::instance().nget("def_wf_percent",0));
	}
	
	double ListDef::getWfMetal() {
		return (getDefCnt() * DataHandler::instance().getDefById(this->getTypeId())->getCostsMetal());
	}
	
	double ListDef::getWfCrystal() {
		return (getDefCnt() * DataHandler::instance().getDefById(this->getTypeId())->getCostsCrystal());
	}
	
	double ListDef::getWfPlastic() {
		return (getDefCnt() * DataHandler::instance().getDefById(this->getTypeId())->getCostsPlastic());
	}
