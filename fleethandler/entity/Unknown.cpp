
#include "Unknown.h"
	
	void Unknown::loadData() {
		this->resMetal = 0;
		this->resCrystal = 0;
		this->resPlastic = 0;
		this->resFuel = 0;
		this->resFood = 0;
		this->resPower = 0;
		
		this->dataLoaded = true;
	}
	
	void Unknown::saveData() {
		this->changedData = false;
	}
