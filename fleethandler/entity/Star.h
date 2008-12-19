
#ifndef __STAR__
#define __STAR__

#include <mysql++/mysql++.h>
#include "MysqlHandler.h"
#include <string>

/**
* Star class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Star : public Entity {
	public: 
		Star(char code, mysqlpp::Row &eRow=NULL) {
			this->codeName = "Stern";
			this->showCoords = true;
		}

};


#endif
