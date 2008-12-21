
#ifndef __PLANET__
#define __PLANET__

#include <mysql++/mysql++.h>

#include "Entity.h"
#include "../MysqlHandler.h"

/**
* Planet class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Planet : public Entity {
public: 
	Planet(char code, mysqlpp::Row &eRow) : Entity(code, eRow) {
		this->codeName = "Planet";
		this->showCoords = true;
	}
	
	void saveData();
	
	double getWfMetal();
	double getWfCrystal();
	double getWfPlastic();
	double getWfsum();
	
	double removeWfMetal(double metal);
	double removeWfCrystal(double crystal);
	double removeWfPlastic(double plastic);
	
	double getResPeople();
	double removeResPeople(double people);
	
	bool getPlanetUserMain();
	
	short getPlanetType();
	
private:
	void loadData();
	
	bool planetUserMain;
	short planetType;
	double wfMetal, wfCrystal, wfPlastic;
	double resPeople;

};

#endif
