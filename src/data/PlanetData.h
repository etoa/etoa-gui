
#ifndef __PLANETDATA__
#define __PLANETDATA__

#include <mysql++/mysql++.h>

#include "Data.h"

/**
* PlanetData class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class PlanetData : public Data {
public:
	PlanetData(mysqlpp::Row object) : Data(object,false) {
		this->typeId = (short)object["type_id"];
		this->typeHabitable = (bool)object["type_habitable"];
		this->typeMetal = (double)object["type_f_metal"];
		this->typeCrystal = (double)object["type_f_crystal"];
		this->typePlastic = (double)object["type_f_plastic"];
		this->typeFuel = (double)object["type_f_fuel"];
		this->typeFood = (double)object["type_f_food"];
		this->typePower = (double)object["type_f_power"];
		this->typePopulation = (double)object["type_f_population"];
		this->typeResearchtime = (double)object["type_f_researchtime"];
		this->typeBuildtime = (double)object["type_f_buildtime"];
		this->typeCollectGas = (bool)object["type_collect_gas"];
	}
	
	short getTypeId();
	bool getTypeHabitable();
	double getTypeMetal();
	double getTypeCrystal();
	double getTypePlastic();
	double getTypeFuel();
	double getTypeFood();
	double getTypePower();
	double getTypePopulation();
	double getTypeResearchtime();
	double getTypeBuildtime();
	bool getTypeCollectGas();
		
private:
	short typeId;
	bool typeHabitable;
	double typeMetal, typeCrystal, typePlastic, typeFuel, typeFood, typePower;
	double typePopulation, typeResearchtime, typeBuildtime;
	bool typeCollectGas;
};

#endif
