
#ifndef __MARKET__
#define __MARKET__

#include <string>
#include <mysql++/mysql++.h>

#include "Entity.h"

/**
* Market class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Market : public Entity {
public: 
	Market(char code, mysqlpp::Row &eRow) : Entity(code, eRow) {
		this->codeName = "Marktplatz";
		this->showCoords = false;
	}
	
	~Market() {
		this->saveData();
	}
	
	void saveData();
	
protected:
	void loadData();
};

#endif
