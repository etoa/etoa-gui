
#ifndef __ListDef__
#define __ListDef__

#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "Object.h"

/**
* DefList class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class ListDef : public Object {
public: 
	ListDef(mysqlpp::Row &oRow) : Object(oRow) {
		this->id = (int)oRow["deflist_id"];
		this->typeId = (short)oRow["deflist_def_id"];
		this->entityId = (int)oRow["deflist_entity_id"];
		this->userId = (int)oRow["deflist_user_id"];
		this->count = (int)oRow["deflsit_count"];
		this->initCount = this->count;
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
