
#ifndef __ASTEROID__
#define __ASTEROID__

#include <string>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "Entity.h"

/**
* Asteroid class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Asteroid : public Entity {
public: 
	Asteroid(char code, mysqlpp::Row &eRow) : Entity(code, eRow) {
		this->codeName = "Asteroidenfeld";
		this->showCoords = true;
	}
	
	~Asteroid() {
		this->saveData();
	}
	
	void saveData();
	
protected:
	void loadData();

};

#endif
