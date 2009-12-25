
#include "ShipData.h"
	
	short ShipData::getTypeId() {
		return this->typeId;
	}
	
	int ShipData::getPowerUse() {
		return this->powerUse;
	}
	
	int ShipData::getFuelUse() {
		return this->fuelUse;
	}
	
	int ShipData::getFuelUseLaunch() {
		return this->fuelUseLaunch;
	}
	
	int ShipData::getFuelUseLanding() {
		return this->fuelUseLanding;
	}
	
	int ShipData::getProdPower() {
		return this->prodPower;
	}
	
	double ShipData::getCapacity() {
		return this->capacity;
	}
	
	double ShipData::getPeopleCapacity() {
		return this->peopleCapacity;
	}
	
	short ShipData::getPilots() {
		return this->pilots;
	}
	
	int ShipData::getSpeed() {
		return this->speed;
	}
	
	int ShipData::getTime2Start() {
		return this->time2Start;
	}
	
	int ShipData::getTime2Land() {
		return this->time2Land;
	}
	
	bool ShipData::getShow() {
		return this->show;
	}
	
	bool ShipData::getBuildable() {
		return this->buildable;
	}
	
	short ShipData::getOrder() {
		return this->order;
	}
	
	bool ShipData::getActions(std::string action) {
		size_t found=this->actions.find(action);
		if (found!=std::string::npos) 
			return true;
		else 
			return false;
	}
	
	double ShipData::getBountyBonus() {
		return this->bountyBonus;
	}
	
	double ShipData::getHeal() {
		return this->heal;
	}
	
	double ShipData::getStructure() {
		return this->structure;
	}
	
	double ShipData::getShield() {
		return this->shield;
	}
	
	double ShipData::getWeapon() {
		return this->weapon;
	}
	
	short ShipData::getRaceId() {
		return this->raceId;
	}
	
	bool ShipData::getLaunchable() {
		return this->launchable;
	}
	
	short ShipData::getFieldsProvide() {
		return this->fieldsProvide;
	}
	
	short ShipData::getCatId() {
		return this->catId;
	}
	
	bool ShipData::getFakeable() {
		return this->fakeable;
	}
	
	bool ShipData::getSpecial() {
		return this->special;
	}
	
	int ShipData::getMaxCount() {
		return this->maxCount;
	}
	
	short ShipData::getMaxLevel() {
		return this->maxLevel;
	}
	
	int ShipData::getNeedExp() {
		return this->needExp;
	}
	
	double ShipData::getExpFactor() {
		return this->expFactor;
	}
	
	double ShipData::getBonusWeapon() {
		return this->bonusWeapon;
	}
	
	double ShipData::getBonusStructure() {
		return this->bonusStructure;
	}
	
	double ShipData::getBonusShield() {
		return this->bonusShield;
	}
	
	double ShipData::getBonusHeal() {
		return this->bonusHeal;
	}
	
	double ShipData::getBonusCapacity() {
		return this->bonusCapacity;
	}
	
	double ShipData::getBonusSpeed() {
		return this->bonusSpeed;
	}
	
	double ShipData::getBonusPilots() {
		return this->bonusPilots;
	}
	
	double ShipData::getBonusTarn() {
		return this->bonusTarn;
	}
	
	double ShipData::getBonusAntrax() {
		return this->bonusAntrax;
	}
	
	double ShipData::getBonusForsteal() {
		return this->bonusForsteal;
	}
	
	double ShipData::getBonusBuildDestroy(){
		return this->bonusBuildDestroy;
	}
	
	double ShipData::getBonusAntraxFood() {
		return this->bonusAntraxFood;
	}
	
	double ShipData::getBonusDeactivade() {
		return this->bonusDeactivade;
	}
	
	double ShipData::getPoints() {
		return this->points;
	}
	
	short ShipData::getAllianceBuildingLevel() {
		return this->allianceBuildingLevel;
	}
	
	short ShipData::getAllianceCosts() {
		return this->allianceCosts;
	}
