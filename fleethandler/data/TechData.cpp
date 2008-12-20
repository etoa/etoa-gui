
#include "TechData.h"
	
	short TechData::getTypeId() {
		return this->typeId;
	}
	
	double TechData::getBuildCostFactor() {
		return this->buildCostFactor;
	}
	
	short TechData::getLastLevel() {
		return this->lastLevel;
	}
	
	bool TechData::getShow() {
		return this->show;
	}
	
	short TechData::getOrder() {
		return this->order;
	}
	
	bool TechData::getStealable() {
		return this->stealable;
	}
