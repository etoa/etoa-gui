
#include "Entity.h"

	int Entity::getId() {
		return this->id;
	}
	
	char Entity::getCode() {
		return this->code;
	}
	
	int Entity::getUserId() {
		if (!this->dataLoaded)
			this->loadData();
			
		return this->userId;
	}
	
	User* Entity::getUser() {
		if (!this->dataLoaded)
			this->loadData();
		
		return entityUser;
	}
	
	short Entity::getTypeId() {
		if (!this->dataLoaded)
			this->loadData();
			
		return this->typeId;
	}
	
	bool Entity::getIsUserMain() {
		if (!this->dataLoaded)
			this->loadData();
			
		return this->userMain;
	}
	
	std::string Entity::getCoords() {
		if (!this->dataLoaded)		
			this->loadData();
		if (!this->coordsLoaded)
			this->loadCoords();
		
		return this->coordsString;
	}
	
	void Entity::setAction(std::string actionName) {
		this->actionName = actionName;
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
	
	double Entity::getResPeople() {
		if (!this->dataLoaded)
			this->loadData();
		
		return this->resPeople;
	}
	
	double Entity::getResSum() {
		if (!this->dataLoaded)
			this->loadData();
		
		return this->resMetal + this->resCrystal + this->resPlastic + this->resFuel + this->resFood;
	}
	
	void Entity::addResMetal(double metal) {
		if (!this->dataLoaded)
			this->loadData();
		
		this->changedData = true;
		this->resMetal += metal;
	}
	
	void Entity::addResCrystal(double crystal) {
		if (!this->dataLoaded)
			this->loadData();
		
		this->changedData = true;
		this->resCrystal += crystal;
	}
	
	void Entity::addResPlastic(double plastic) {
		if (!this->dataLoaded)
			this->loadData();
		
		this->changedData = true;
		this->resPlastic += plastic;
	}
	
	void Entity::addResFuel(double fuel) {
		if (!this->dataLoaded)
			this->loadData();
		
		this->changedData = true;
		this->resFuel += fuel;
	}
	
	void Entity::addResFood(double food) {
		if (!this->dataLoaded)
			this->loadData();
		
		this->changedData = true;
		this->resFood += food;
	}
	
	void Entity::addResPower(double power) {
		if (!this->dataLoaded)
			this->loadData();
		
		this->changedData = true;
		this->resPower += power;
	}
	
	void Entity::addResPeople(double people) {
		if (!this->dataLoaded)
			this->loadData();
		
		this->changedData = true;
		this->resPeople += people;
	}
	
	double Entity::removeResMetal(double metal) {
		if (!this->dataLoaded)
			this->loadData();
		
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
		if (!this->dataLoaded)
			this->loadData();
		
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
		if (!this->dataLoaded)
			this->loadData();
		
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
		if (!this->dataLoaded)
			this->loadData();
		
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
		if (!this->dataLoaded)
			this->loadData();
		
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
		if (!this->dataLoaded)
			this->loadData();
		
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
	
	double Entity::removeResPeople(double people) {
		if (!this->dataLoaded)
			this->loadData();
		
		this->changedData = true;
		if (people<=this->resPeople) {
			this->resPeople -= people;
			return people;
		}
		else {
			people = this->resPeople;
			this->resPeople = 0;
			return people;
		}
	}
	
	double Entity::getWfMetal() {
		if (!this->dataLoaded)
			this->loadData();
			
		return this->wfMetal;
	}
	
	double Entity::getWfCrystal() {
		if (!this->dataLoaded)
			this->loadData();
			
		return this->wfCrystal;
	}
	
	double Entity::getWfPlastic() {
		if (!this->dataLoaded)
			this->loadData();
			
		return this->wfPlastic;
	}
	
	double Entity::getWfSum() {
		if (!this->dataLoaded)
			this->loadData();
		
		return this->getWfMetal() + this->getWfCrystal() + this->getWfPlastic();
	}
	
	void Entity::addWfMetal(double metal) {
		if (!this->dataLoaded)
			this->loadData();
		
		this->changedData = true;
		this->wfMetal += metal;
	}
	
	void Entity::addWfCrystal(double crystal) {
		if (!this->dataLoaded)
			this->loadData();
		
		this->changedData = true;
		this->wfCrystal += crystal;
	}
	
	void Entity::addWfPlastic(double plastic) {
		if (!this->dataLoaded)
			this->loadData();
		
		this->changedData = true;
		this->wfPlastic += plastic;
	}
	
	double Entity::removeWfMetal(double metal) {
		if (!this->dataLoaded)
			this->loadData();
			
		this->changedData = true;
		if (metal<=this->wfMetal) {
			this->wfMetal -= metal;
			return metal;
		}
		else {
			metal = this->wfMetal;
			this->wfMetal = 0;
			return metal;
		}
	}
	
	double Entity::removeWfCrystal(double crystal) {
		if (!this->dataLoaded)
			this->loadData();
			
		this->changedData = true;
		if (crystal<=this->wfCrystal) {
			this->wfCrystal -= crystal;
			return crystal;
		}
		else {
			crystal = this->wfCrystal;
			this->wfCrystal = 0;
			return crystal;
		}
	}
	
	double Entity::removeWfPlastic(double plastic) {
		if (!this->dataLoaded)
			this->loadData();
			
		this->changedData = true;
		if (plastic<=this->wfPlastic) {
			this->wfPlastic -= plastic;
			return plastic;
		}
		else {
			plastic = this->wfPlastic;
			this->wfPlastic = 0;
			return plastic;
		}
	}
	
	std::string Entity::getResString() {
		if (!this->dataLoaded)
			this->loadData();
		
		std::string resString = "";
		
		 resString += "[b]Rohstoffe:[/b]\n\nTitan: "
					+ functions::nf(functions::d2s(this->getResMetal()))
					+ "\n"
					+ "Silizium: "
					+ functions::nf(functions::d2s(this->getResCrystal()))
					+ "\n"
					+ "PVC: "
					+ functions::nf(functions::d2s(this->getResPlastic()))
					+ "\n"
					+ "Tritium: "
					+ functions::nf(functions::d2s(this->getResFuel()))
					+ "\n"
					+ "Nahrung: "
					+ functions::nf(functions::d2s(this->getResFood()))
					+ "\n"
					+ "Bewohner: "
					+ functions::nf(functions::d2s(this->getResMetal()))
					+ "\n";
		return resString;
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
	
	std::string Entity::getLogResStart() {
		if (this->dataLoaded) {
			std::string log = ""
							+ functions::d2s(this->initResMetal)
							+ ":"
							+ functions::d2s(this->initResCrystal)
							+ ":"
							+ functions::d2s(this->initResPlastic)
							+ ":"
							+ functions::d2s(this->initResFuel)
							+ ":"
							+ functions::d2s(this->initResFood)
							+ ":"
							+ functions::d2s(this->initResPeople)
							+ ":"
							+ functions::d2s(this->initResPower)
							+ ",w,"
							+ functions::d2s(this->initWfMetal)
							+ ":"
							+ functions::d2s(this->initWfCrystal)
							+ ":"
							+ functions::d2s(this->initWfPlastic);
			return log;
		}
		else
			return "0";
	}
	
	std::string Entity::getLogResEnd() {
		if (this->dataLoaded) {
			std::string log = ""
							+ functions::d2s(this->resMetal)
							+ ":"
							+ functions::d2s(this->resCrystal)
							+ ":"
							+ functions::d2s(this->resPlastic)
							+ ":"
							+ functions::d2s(this->resFuel)
							+ ":"
							+ functions::d2s(this->resFood)
							+ ":"
							+ functions::d2s(this->resPeople)
							+ ":"
							+ functions::d2s(this->resPower)
							+ ",w,"
							+ functions::d2s(this->wfMetal)
							+ ":"
							+ functions::d2s(this->wfCrystal)
							+ ":"
							+ functions::d2s(this->wfPlastic);
			return log;
		}
		else
			return "0";
	}
	
	std::string Entity::getLogShipsStart() {
		return "0";
	}
	
	std::string Entity::getLogShipsEnd() {
		return "0";
	}

