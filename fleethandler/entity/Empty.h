
#ifndef __EMPTY__
#define __EMPTY__

#include <string>
#include "Entity.h"

/**
* Entity class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Empty : public Entity {
	public: 
		Empty(char code, mysqlpp::Row &eRow=NULL) {
			this->codeName = "Leerer Raum";
			this->showCoords = true;
		}

};

#endif
