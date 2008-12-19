
#ifndef __BASE__
#define __BASE__

#include <mysql++/mysql++.h>
#include "MysqlHandler.h"
#include <string>

/**
* Base class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Base : public Entity {
	public: 
		Base(char code, mysqlpp::Row &eRow=NULL) {
			this->codeName = "Allianzbasis";
			this->showCoords = false;
		}

};


#endif
