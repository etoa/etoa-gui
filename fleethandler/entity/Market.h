
#ifndef __MARKET__
#define __MARKET__

#include <mysql++/mysql++.h>
#include "MysqlHandler.h"
#include <string>

/**
* Market class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Market : public Entity {
	public: 
		Market(char code, mysqlpp::Row &eRow=NULL) {
			this->codeName = "Marktplatz";
			this->showCoords = false;
		}

};


#endif
