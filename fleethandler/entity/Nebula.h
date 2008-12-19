
#ifndef __NEBULA__
#define __NEBULA__

#include <string>
#include "Entity.h"

/**
* Nebula class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Nebula : public Entity {
	public: 
		Nebula(char code, mysqlpp::Row &eRow=NULL) {
			this->codeName = "Interstellarer Gasnebel";
			this->showCoords = true;
		}

};

#endif
