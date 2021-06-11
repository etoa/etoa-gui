
#ifndef __BASE__
#define __BASE__

#include <string>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "Entity.h"

/**
* Base class
*
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Base : public Entity {
public:
	Base(char code, mysqlpp::Row &eRow) : Entity(code, eRow) {
		this->codeName = "Allianzbasis";
		this->showCoords = false;
	}

	~Base() {
		this->saveData();
	}

	void saveData();

protected:
	void loadData();

};

#endif
