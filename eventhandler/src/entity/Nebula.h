
#ifndef __NEBULA__
#define __NEBULA__

#include <ctime>
#include <string>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "Entity.h"
#include "../MysqlHandler.h"
#include "../config/ConfigHandler.h"

/**
* Nebula class
*
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Nebula : public Entity {
public:
	Nebula(char code, mysqlpp::Row &eRow) : Entity(code, eRow) {
		this->codeName = "Interstellarer Gasnebel";
		this->showCoords = true;
	}

	~Nebula() {
		this->saveData();
	}

	void saveData();

protected:
	void loadData();

};

#endif
