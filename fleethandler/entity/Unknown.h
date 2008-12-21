
#ifndef __UNKNOWN__
#define __UNKNOWN__

#include <mysql++/mysql++.h>

#include "Entity.h"

/**
* Unknown Entity class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Unknown : public Entity {
public: 
	Unknown(char code, mysqlpp::Row &eRow) : Entity(code, eRow) {
		this->codeName = "Unerforschte Raumzelle!";
		this->showCoords = true;
	}
	
	void saveData();
	
private:
	void loadData();

};

#endif
