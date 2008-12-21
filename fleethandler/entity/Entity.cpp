
#include "Entity.h"

	void Entity::setAction(std::string actionName) {
		this->actionName = actionName;
	}

	std::string Entity::getCoords() {
		if (!this->coordsLoaded)
			this->loadCoords();
		
		return this->coordsString;
	}
	
	int Entity::getUserId() {
		if (!this->dataLoaded)
			this->loadData();
			
		return this->userId;
	}
	
	int Entity::getId() {
		return this->id;
	}
	
	void Entity::loadCoords() {
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		mysqlpp::Query query = con->query();
		query << "SELECT ";
		query << "	* ";
		query << "FROM ";
		query << "	cells ";
		query << "WHERE ";
		query << "	id='" << this->cellId << "' ";
		query << "LIMIT 1;";
		mysqlpp::Result cRes = query.store();
		query.reset();
			
		if (cRes) {
			int cSize = cRes.size();
			
			if (cSize>0) {
				mysqlpp::Row cRow = cRes.at(0);
				
				this->sx = (short)cRow["sx"];
				this->sy = (short)cRow["sy"];
				
				this->coordsString = this->codeName
								+ " ("
								+ std::string(cRow["sx"])
								+ "/"
								+ std::string(cRow["sy"])
								+ " : "
								+ std::string(cRow["cx"])
								+ "/"
								+ std::string(cRow["cy"])
								+ " : "
								+ functions::d2s(this->pos)
								+ ")";
			}
		}
	}
	
	double Entity::getResMetal() {
		if (!this->dataLoaded)
			this->loadData();
		
		return this->resMetal;
	}
	
	double Entity::getResCrystal() {
		if (!this->dataLoaded)
			this->loadData();
		
		return this->resCrystal;
	}
	
	double Entity::getResPlastic() {
		if (!this->dataLoaded)
			this->loadData();
		
		return this->resPlastic;
	}
	
	double Entity::getResFuel() {
		if (!this->dataLoaded)
			this->loadData();
		
		return this->resFuel;
	}
	
	double Entity::getResFood() {
		if (!this->dataLoaded)
			this->loadData();
		
		return this->resFood;
	}
	
	double Entity::getResPower() {
		if (!this->dataLoaded)
			this->loadData();
		
		return this->resPower;
	}
	
	double Entity::getResSum() {
		if (!this->dataLoaded)
			this->loadData();
		
		return this->resMetal + this->resCrystal + this->resPlastic + this->resFuel + this->resFood + this->resPower;
	}
	
	double Entity::removeResMetal(double metal) {
		this->changedData = true;
		if (metal<=this->resMetal) {
			this->resMetal -= metal;
			return metal;
		}
		else {
			metal = this->resMetal;
			this->resMetal = 0;
			return metal;
		}
	}

	double Entity::removeResCrystal(double crystal) {
		this->changedData = true;
		if (crystal<=this->resCrystal) {
			this->resCrystal -= crystal;
			return crystal;
		}
		else {
			crystal = this->resCrystal;
			this->resCrystal = 0;
			return crystal;
		}
	}

	double Entity::removeResPlastic(double plastic) {
		this->changedData = true;
		if (plastic<=this->resPlastic) {
			this->resPlastic -= plastic;
			return plastic;
		}
		else {
			plastic = this->resPlastic;
			this->resPlastic = 0;
			return plastic;
		}
	}

	double Entity::removeResFuel(double fuel) {
		this->changedData = true;
		if (fuel<=this->resFuel) {
			this->resFuel -= fuel;
			return fuel;
		}
		else {
			fuel = this->resFuel;
			this->resFuel = 0;
			return fuel;
		}
	}

	double Entity::removeResFood(double food) {
		this->changedData = true;
		if (food<=this->resFood) {
			this->resFood -= food;
			return food;
		}
		else {
			food = this->resFood;
			this->resFood = 0;
			return food;
		}
	}
	
	double Entity::removeResPower(double power) {
		this->changedData = true;
		if (power<=this->resPower) {
			this->resPower -= power;
			return power;
		}
		else {
			power = this->resPower;
			this->resPower = 0;
			return power;
		}
	}
