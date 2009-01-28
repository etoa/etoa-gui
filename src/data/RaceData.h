
#ifndef __RACEDATA__
#define __RACEDATA__

#include <mysql++/mysql++.h>

#include "Data.h"

/**
* RaceData class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class RaceData : public Data {
public:
	RaceData(mysqlpp::Row object) : Data(object, false) {
		this->raceId = (short)object["race_id"];
		this->raceResearchtime = (double)object["race_f_researchtime"];
		this->raceBuildtime = (double)object["race_f_buildtime"];
		this->raceFleettime = (double)object["race_f_fleettime"];
		this->raceMetal = (double)object["race_f_metal"];
		this->raceCrystal = (double)object["race_f_crystal"];
		this->racePlastic = (double)object["race_f_plastic"];
		this->raceFuel = (double)object["race_f_fuel"];
		this->raceFood = (double)object["race_f_food"];
		this->racePower = (double)object["race_f_power"];
		this->racePopulation = (double)object["race_f_population"];
	}
	
	short getRaceId();
	double getRaceResearchtime();
	double getRaceBuildtime();
	double getRaceFleettime();
	double getRaceMetal();
	double getRaceCrystal();
	double getRacePlastic();
	double getRaceFuel();
	double getRaceFood();
	double getRacePower();
	double getRacePopulation();
	
private:
	short raceId;
	double raceResearchtime, raceBuildtime, raceFleettime;
	double raceMetal, raceCrystal, racePlastic, raceFuel, raceFood, racePower, racePopulation;
};

#endif
