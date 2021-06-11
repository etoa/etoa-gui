
#ifndef __EMPTY__
#define __EMPTY__

#include <string>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "Entity.h"

/**
* Entity class
*
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Empty : public Entity {
public:
	Empty(char code, mysqlpp::Row &eRow) : Entity(code, eRow) {
		this->codeName = "Leerer Raum";
		this->showCoords = true;
	}

	~Empty() {
		this->saveData();
	}

	void saveData();

protected:
	void loadData();
};

#endif
