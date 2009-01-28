
#ifndef __TECHDATA__
#define __TECHDATA__

#include <mysql++/mysql++.h>
#include <string>

#include "Data.h"

/**
* TechData class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class TechData : public Data	
{
public:
	TechData(mysqlpp::Row object) : Data(object) {
		this->typeId = (short)object["tech_type_id"];
		this->buildCostFactor = (double)object["tech_build_costs_factor"];
		this->lastLevel = (short)object["tech_last_level"];
		this->show = (bool)object["tech_show"];
		this->order = (short)object["tech_order"];
		this->stealable = (bool)object["tech_stealable"];
	}
	
	short getTypeId();
	double getBuildCostFactor();
	short getLastLevel();
	bool getShow();
	short getOrder();
	bool getStealable();
	
private:

	short typeId;
	double buildCostFactor;
	short lastLevel;
	bool show;
	short order;
	bool stealable;
};

#endif
