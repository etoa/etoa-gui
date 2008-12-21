
#ifndef __WORMHOLE__
#define __WORMHOLE__

#include <mysql++/mysql++.h>

#include "Entity.h"

/**
* Wormhole class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Wormhole : public Entity {
public: 
	Wormhole(char code, mysqlpp::Row &eRow) : Entity(code, eRow) {
		this->codeName = "Wurmloch";
		this->showCoords = true;
	}
	
	void saveData();
	
private:
	void loadData();

};

#endif
