
#ifndef __WORMHOLE__
#define __WORMHOLE__

#include <mysql++/mysql++.h>
#include "MysqlHandler.h"
#include <string>

/**
* Wormhole class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Wormhole : public Entity {
	public: 
		Wormhole(char code, mysqlpp::Row &eRow=NULL) {
			this->codeName = "Wurmloch";
			this->showCoords = true;
		}

};


#endif
