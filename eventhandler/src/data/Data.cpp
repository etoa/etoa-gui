
#include "Data.h"

	int Data::getId() {
		return this->id;
	}

	std::string Data::getName() {
		return this->name;
	}

	std::string Data::getShortComment() {
		return this->shortComment;
	}

	std::string Data::getLongComment() {
		return this->longComment;
	}

	double Data::getCosts() {
		return (this->costsMetal + this->costsCrystal + this->costsPlastic + this->costsFuel + this->costsFood);
	}

	double Data::getCostsMetal() {
		return this->costsMetal;
	}

	double Data::getCostsCrystal() {
		return this->costsCrystal;
	}

	double Data::getCostsPlastic() {
		return this->costsPlastic;
	}

	double Data::getCostsFuel() {
		return this->costsFuel;
	}

	double Data::getCostsFood() {
		return this->costsFood;
	}

	double Data::getCostsPower() {
		return this->costsPower;
	}
