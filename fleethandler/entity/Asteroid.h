
#ifndef __ASTEROID__
#define __ASTEROID__

#include <mysql++/mysql++.h>
#include "MysqlHandler.h"
#include <string>

/**
* Asteroid class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Asteroid : public Entity {
	public: 
		Asteroid(char code, mysqlpp::Row &eRow=NULL) {
			this->codeName = "Asteroidenfeld";
			this->showCoords = true;
		}

};


#endif
