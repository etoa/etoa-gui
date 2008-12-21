
#include "Wormhole.h"
	
	void Wormhole::loadData() {
		this->resMetal = 0;
		this->resCrystal = 0;
		this->resPlastic = 0;
		this->resFuel = 0;
		this->resFood = 0;
		this->resPower = 0;
		
		this->dataLoaded = true;
	}
	
	void Wormhole::saveData() {
		this->changedData = false;
	}
