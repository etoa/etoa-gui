
#include "Empty.h"

	void Empty::loadData() {

		this->initResMetal = this->resMetal;
		this->initResCrystal = this->resCrystal;
		this->initResPlastic = this->resPlastic;
		this->initResFuel = this->resFuel;
		this->initResFood = this->resFood;
		this->initResPower = this->resPower;

		this->initWfMetal = this->resMetal;
		this->initWfCrystal = this->wfCrystal;
		this->initWfPlastic = this->wfPlastic;

		this->entityUser = new User(this->userId);

		this->dataLoaded = true;
	}

	void Empty::saveData() {

		this->changedData = false;
	}
