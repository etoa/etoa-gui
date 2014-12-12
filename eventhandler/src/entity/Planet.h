
#ifndef __PLANET__
#define __PLANET__

#include <string>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "Entity.h"
#include "../MysqlHandler.h"
#include "../config/ConfigHandler.h"

/**
* Planet class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Planet : public Entity {
public: 
	Planet(char code, mysqlpp::Row &eRow) : Entity(code, eRow) {
		this->codeName = "Planet";
		this->showCoords = true;
	}
	
	~Planet() {
		this->saveData();
	}
	
	void saveData();
	
protected:
	void loadData();
	void updateGasPlanet();
	
	int fields;
	int lastUpdated;

};

#endif
