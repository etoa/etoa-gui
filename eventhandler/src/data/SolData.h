
#ifndef __SOLDATA__
#define __SOLDATA__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "Data.h"

/**
* SolData class
*
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class SolData : public Data {
public:
	SolData(mysqlpp::Row object) : Data(object,false) {
		this->typeId = (short)object["sol_type_id"];
		this->typeMetal = (double)object["sol_type_f_metal"];
		this->typeCrystal = (double)object["sol_type_f_crystal"];
		this->typePlastic = (double)object["sol_type_f_plastic"];
		this->typeFuel = (double)object["sol_type_f_fuel"];
		this->typeFood = (double)object["sol_type_f_food"];
		this->typePower = (double)object["sol_type_f_power"];
		this->typePopulation = (double)object["sol_type_f_population"];
		this->typeResearchtime = (double)object["sol_type_f_researchtime"];
		this->typeBuildtime = (double)object["sol_type_f_buildtime"];
	}

	short getTypeId();
	double getTypeMetal();
	double getTypeCrystal();
	double getTypePlastic();
	double getTypeFuel();
	double getTypeFood();
	double getTypePower();
	double getTypePopulation();
	double getTypeResearchtime();
	double getTypeBuildtime();

private:
	short typeId;
	double typeMetal, typeCrystal, typePlastic, typeFuel, typeFood, typePower;
	double typePopulation, typeResearchtime, typeBuildtime;
};

#endif
