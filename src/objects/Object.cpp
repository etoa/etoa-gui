
#include "Object.h"

	int Object::getId() {
		return this->id;
	}
	
	short Object::getTypeId() {
		return this->typeId;
	}
	
	int Object::getUserId() {
		return this->userId;
	}
	
	double Object::getCount() {
		return this->count;
	}
	
	double Object::getInitCount() {
		return this->initCount;
	}
	
	double Object::getRebuildCount() {
		return std::max(0.0,this->rebuildCount);
	}
	
	int Object::getEntityId() {
		return this->entityId;
	}
	
	int Object::getFleetId() {
		return this->fleetId;
	}
	
	bool Object::getIsFaked() {
		return this->isFaked;
	}
		
	bool Object::getSpecial() {
		return this->special;
	}
	
	short Object::getSLevel() {
		return this->sLevel;
	}
	
	double Object::getSExp() {
		return this->sExp;
	}
	
	short Object::getSBonusWeapon() {
		return this->sBonusWeapon;
	}
	
	short Object::getSBonusStructure() {
		return this->sBonusStructure;
	}
	short Object::getSBonusShield() {
		return this->sBonusShield;
	}
	
	short Object::getSBonusHeal() {
		return this->sBonusHeal;
	}
	
	short Object::getSBonusCapacity() {
		return this->sBonusCapacity;
	}
	
	short Object::getSBonusSpeed() {
		return this->sBonusSpeed;
	}
	
	short Object::getSBonusPilots() {
		return this->sBonusPilots;
	}
	
	short Object::getSBonusTarn() {
		return this->sBonusTarn;
	}
	
	short Object::getSBonusAntrax() {
		return this->sBonusAntrax;
	}
	
	short Object::getSBonusForsteal() {
		return this->sBonusForsteal;
	}
	
	short Object::getSBonusBuildDestroy() {
		return this->sBonusBuildDestroy;
	}
	
	short Object::getSBonusAntraxFood() {
		return this->sBonusAntraxFood;
	}
	
	short Object::getSBonusDeactivade() {
		return this->sBonusDeactivade;
	}
	
	void Object::setPercentSurvive(double percentage) {
		this->isChanged = true;
		percentage = std::min(1.0,percentage);
		this->count = (int)ceil(this->initCount * percentage);
	}
	
	int Object::removeObjects(int count) {
		this->isChanged = true;
		if (count>this->count) {
			this->count = 0;
			return this->count - count;
		}
		else
			this->count -= count;
		return 0;
	}
	
	void Object::addExp(double exp) {
		this->sExp += exp;
		this->isChanged = true;
	}


