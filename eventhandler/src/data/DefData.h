
#ifndef __DEFDATA__
#define __DEFDATA__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>
#include <string>

#include "Data.h"

/**
* DefData class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class DefData : public Data	
{
public:
	/**
	* DefData Class
	* 
	*/
	 	 	 	 	 	 	 	 	 	 	 	 	 	 	
	DefData(mysqlpp::Row object) : Data(object) {
		this->powerUse = (int)object["def_power_use"];
		this->fuelUse = (int)object["def_fuel_use"];
		this->fields = (short)object["def_fields"];
		this->show = (bool)object["def_show"];
		this->buildable = (bool)object["def_buildable"];
		this->order = (short)object["def_order"];
		this->heal = (double)object["def_heal"];
		this->structure = (double)object["def_structure"];
		this->shield = (double)object["def_shield"];
		this->weapon = (double)object["def_weapon"];
		this->jam = (short)object["def_jam"];
		this->raceId = (short)object["def_race_id"];
		this->catId = (short)object["def_cat_id"];
		this->maxCount = (int)object["def_max_count"];
		this->points = (double)object["def_points"];
	}
	
	int getPowerUse();
	int getFuelUse();
	short getFields();
	bool getShow();
	bool getBuildable();
	short getOrder();
	double getHeal();
	double getStructure();
	double getShield();
	double getWeapon();
	short getJam();
	short getRaceId();
	short getCatId();
	int getMaxCount();
	double getPoints();
	
private:

	int powerUse;
	int fuelUse;
	short fields;
	bool show;
	bool buildable;
	short order;
	double heal;
	double structure;
	double shield;
	double weapon;
	short jam;
	short raceId;
	short catId;
	int maxCount;
	double points;
};

#endif
