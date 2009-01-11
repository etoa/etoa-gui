
#ifndef __STAR__
#define __STAR__

#include <string>
#include <mysql++/mysql++.h>

#include "Entity.h"

/**
* Star class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Star : public Entity {
public: 
	Star(char code, mysqlpp::Row &eRow) : Entity(code, eRow) {
		this->codeName = "Stern";
		this->showCoords = true;
	}
	
	~Star() {
		this->saveData();
	}
	
	void saveData();
	
protected:
	void loadData();

};

#endif
