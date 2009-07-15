
#ifndef __ListDef__
#define __ListDef__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "Object.h"

/**
* DefList class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class ListDef : public Object {
	private:
		double rebuild;
public: 
	ListDef(mysqlpp::Row &oRow,double rebuild=1.0) : Object(oRow) {
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
	
	~ListDef() {
		this->save();
	}
	
	double getWfMetal();
	double getWfCrystal();
	double getWfPlastic();
	
	void save();
	
};

#endif
