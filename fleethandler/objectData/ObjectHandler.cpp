#include <iostream>
#include <set>
#include <vector>
#include "../MysqlHandler.h"

#include <mysql++/mysql++.h>

#include "ObjectHandler.h"

	int ObjectHandler::id() { 
		return this->objectId;
	}
	
	short ObjectHandler::type() {
		return this->objectType;
	}
	
	std::string ObjectHandler::name() {
		return this->objectName;
	}
	
	short ObjectHandler::typeId() {
		return this->objectTypeId;
	}
	
	int ObjectHandler::costs() {
		return (this->objectCostsMetal + this->objectCostsCrystal + this->objectCostsPlastic + this->objectCostsFuel + this->objectCostsFood);
	}
	
	double ObjectHandler::costsMetal() {
		return this->objectCostsMetal;
	}
	
	double ObjectHandler::costsCrystal() {
		return this->objectCostsCrystal;
	}
	
	double ObjectHandler::costsPlastic() {
		return this->objectCostsPlastic;
	}
	
	double ObjectHandler::costsFuel() {
		return this->objectCostsFuel;
	}
	
	double ObjectHandler::costsFood() {
		return this->objectCostsFood;
	}
	
	int ObjectHandler::costsPower() {
		return this->objectCostsPower;
	}
	
	int ObjectHandler::powerUse() {
		return this->objectPowerUse;
	}
	
	int ObjectHandler::fuelUse() {
		return this->objectFuelUse;
	}
	
	int ObjectHandler::fuelUseLaunch() {
		return this->objectFuelUseLaunch;
	}
	
	int ObjectHandler::fuelUseLanding() {
		return this->objectFuelUseLanding;
	}
	
	int ObjectHandler::prodPower() {
		return this->objectProdPower;
	}
	
	int ObjectHandler::capacity() {
		return this->objectCapacity;
	}
	
	int ObjectHandler::peopleCapacity() {
		return this->objectPeopleCapacity;
	}
	
	short ObjectHandler::pilots() {
		return this->objectPilots;
	}
	
	int ObjectHandler::speed() {
		return this->objectSpeed;
	}
	
	int ObjectHandler::time2Start() {
		return this->objectTime2Start;
	}
	
	int ObjectHandler::time2Land() {
		return this->objectTime2Land;
	}
	
	bool ObjectHandler::show() {
		return this->objectShow;
	}
	
	bool ObjectHandler::buildable() {
		return this->objectBuildable;
	}
	
	short ObjectHandler::order() {
		return this->objectOrder;
	}
	
	bool ObjectHandler::actions(std::string action) {
		size_t found=this->objectActions.find(action);
		if (found!=std::string::npos) 
			return true;
		else 
			return false;
	}
	
	int ObjectHandler::heal() {
		return this->objectHeal;
	}
	
	int ObjectHandler::structure() {
		return this->objectStructure;
	}
	
	int ObjectHandler::shield() {
		return this->objectShield;
	}
	
	int ObjectHandler::weapon() {
		return this->objectWeapon;
	}
	
	short ObjectHandler::raceId() {
		return this->objectRaceId;
	}
	
	bool ObjectHandler::launchable() {
		return this->objectLaunchable;
	}
	
	short ObjectHandler::fieldsProvide() {
		return this->objectFieldsProvide;
	}
	
	short ObjectHandler::catId() {
		return this->objectCatId;
	}
	
	bool ObjectHandler::fakeable() {
		return this->objectFakeable;
	}
	
	bool ObjectHandler::special() {
		return this->objectSpecial;
	}
	
	int ObjectHandler::maxCount() {
		return this->objectMaxCount;
	}
	
	short ObjectHandler::maxLevel() {
		return this->objectMaxLevel;
	}
	
	int ObjectHandler::needExp() {
		return this->objectNeedExp;
	}
	
	float ObjectHandler::expFactor() {
		return this->objectExpFactor;
	}
	
	float ObjectHandler::bonusWeapon() {
		return this->objectBonusWeapon;
	}
	
	float ObjectHandler::bonusStructure() {
		return this->objectBonusStructure;
	}
	
	float ObjectHandler::bonusShield() {
		return this->objectBonusShield;
	}
	
	float ObjectHandler::bonusHeal() {
		return this->objectBonusHeal;
	}
	
	float ObjectHandler::bonusCapacity() {
		return this->objectBonusCapacity;
	}
	
	float ObjectHandler::bonusSpeed() {
		return this->objectBonusSpeed;
	}
	
	float ObjectHandler::bonusPilots() {
		return this->objectBonusPilots;
	}
	
	float ObjectHandler::bonusTarn() {
		return this->objectBonusTarn;
	}
	
	float ObjectHandler::bonusAntrax() {
		return this->objectBonusAntrax;
	}
	
	float ObjectHandler::bonusForsteal() {
		return this->objectBonusForsteal;
	}
	
	float ObjectHandler::bonusBuildDestroy(){
		return this->objectBonusBuildDestroy;
	}
	
	float ObjectHandler::bonusAntraxFood() {
		return this->objectBonusAntraxFood;
	}
	
	float ObjectHandler::bonusDeactivade() {
		return this->objectBonusDeactivade;
	}
	
	float ObjectHandler::points() {
		return this->objectPoints;
	}
	
	short ObjectHandler::allianceBuildingLevel() {
		return this->objectAllianceBuildingLevel;
	}
	
	short ObjectHandler::allianceCosts() {
		return this->objectAllianceCosts;
	}
	
	short ObjectHandler::fields() {
		return this->objectFields;
	}
	
	short ObjectHandler::jam() {
		return this->objectJam;
	}