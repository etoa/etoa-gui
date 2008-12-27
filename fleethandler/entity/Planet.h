
#ifndef __PLANET__
#define __PLANET__

#include <string>
#include <mysql++/mysql++.h>

#include "Entity.h"
#include "../MysqlHandler.h"

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

};

#endif
