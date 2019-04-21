
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

	void Entity::addMessageUser(Message* message) {
		message->addUserId(this->getUserId());
		std::string done = this->entityUser->getUserNick();
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			std::size_t found;
			for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
				std::string key = (*it)->fleetUser->getUserNick();
				found=done.rfind(key);
				if (found==std::string::npos) {
					(*it)->addMessageUser(message);
					done += ", "
							+ key;
				}
			}
		}
	}

	std::string Entity::getCoords() {
		if (!this->dataLoaded)
			this->loadData();
		if (!this->coordsLoaded)
			this->loadCoords();

		return this->coordsString;
	}

	int Entity::getAbsX() {
		if (!this->coordsLoaded)
			this->loadCoords();
		return (10 * (this->sx - 1) + this->cx);
	}

	int Entity::getAbsY() {
		if (!this->coordsLoaded)
			this->loadCoords();
		return (10 * (this->sy - 1) + this->cy);
	}

	void Entity::setAction(std::string actionName) {
		this->actionName = actionName;
	}

	double Entity::getResMetal(double percent, int spy) {
		if (!this->dataLoaded)
			this->loadData();
		
		if (percent!=1)
			return max(0.0,min(this->resMetal-this->bunkerMetal,this->resMetal*percent));
        
        if (spy==1 && percent==1)
            return max(0.0,this->resMetal-this->bunkerMetal);
		return this->resMetal;
	}

	double Entity::getResCrystal(double percent, int spy) {
		if (!this->dataLoaded)
			this->loadData();
		
		if (percent!=1)
			return max(0.0,min(this->resCrystal-this->bunkerCrystal,this->resCrystal*percent));
        if (spy==1 && percent==1)
            return max(0.0,this->resCrystal-this->bunkerCrystal);
        
		return this->resCrystal;
	}

	double Entity::getResPlastic(double percent, int spy) {
		if (!this->dataLoaded)
			this->loadData();
		
		if (percent!=1)
			return max(0.0,min(this->resPlastic-this->bunkerPlastic,this->resPlastic*percent));
        if (spy==1 && percent==1)
            return max(0.0,this->resPlastic-this->bunkerPlastic);
        
		return this->resPlastic;
	}

	double Entity::getResFuel(double percent, int spy) {
		if (!this->dataLoaded)
			this->loadData();
		
		if (percent!=1)
			return max(0.0,min(this->resFuel-this->bunkerFuel,this->resFuel*percent));
        if (spy==1 && percent==1)
            return max(0.0,this->resFuel-this->bunkerFuel);
        
		return this->resFuel;
	}

	double Entity::getResFood(double percent, int spy) {
		if (!this->dataLoaded)
			this->loadData();
		
		if (percent!=1)
			return max(0.0,min(this->resFood-this->bunkerFood,this->resFood*percent));
        
        if (spy==1 && percent==1)
            return max(0.0,this->resFood-this->bunkerFood);
        
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
		
		return this->getResMetal() + this->getResCrystal() + this->getResPlastic() + this->getResFuel() + this->getResFood();
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

	double Entity::removeResMetal(double metal, bool steal) {
		if (!this->dataLoaded)
			this->loadData();

		this->changedData = true;
		if (steal)
			metal = max(0.0,min(metal,this->resMetal-this->bunkerMetal));
		else
			metal = min(metal,this->resMetal);
		this->resMetal -= metal;
		return metal;
	}

	double Entity::removeResCrystal(double crystal, bool steal) {
		if (!this->dataLoaded)
			this->loadData();

		this->changedData = true;
		if (steal)
			crystal = max(0.0,min(crystal,this->resCrystal-this->bunkerCrystal));
		else
			crystal = min(crystal,this->resCrystal);
		this->resCrystal -= crystal;
		return crystal;
	}

	double Entity::removeResPlastic(double plastic, bool steal) {
		if (!this->dataLoaded)
			this->loadData();

		this->changedData = true;
		if (steal)
			plastic = max(0.0,min(plastic,this->resPlastic-this->bunkerPlastic));
		else
			plastic = min(plastic,this->resPlastic);
		this->resPlastic -= plastic;
		return plastic;
	}

	double Entity::removeResFuel(double fuel, bool steal) {
		if (!this->dataLoaded)
			this->loadData();

		this->changedData = true;
		if (steal)
			fuel = max(0.0,min(fuel,this->resFuel-this->bunkerFuel));
		else
			fuel = min(fuel,this->resFuel);
		this->resFuel -= fuel;
		return fuel;
	}

	double Entity::removeResFood(double food, bool steal) {
		if (!this->dataLoaded)
			this->loadData();

		this->changedData = true;
		if (steal)
			food = max(0.0,min(food,this->resFood-this->bunkerFood));
		else
			food = min(food,this->resFood);
		this->resFood -= food;
		return food;
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
		if (people) {
			double peopleAtWork = this->loadPeopleAtWork();
			this->changedData = true;
			if (people<=this->resPeople-peopleAtWork) {
				this->resPeople -= people;
				return people;
			}
			else {
				people = this->resPeople - peopleAtWork;
				this->resPeople = 0 + peopleAtWork;
				return people;
			}
		}
		return 0;
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

	double Entity::getObjectWfMetal(bool total) {
		double wfMetal = 0;
		std::vector<Object*>::iterator ot;
		for (ot=def.begin() ; ot < def.end(); ot++)
			wfMetal += (*ot)->getWfMetal();
		for (ot=objects.begin() ; ot < objects.end(); ot++)
			wfMetal += (*ot)->getWfMetal();
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				wfMetal += (*it)->getWfMetal();
		}

		return wfMetal;
	}
	double Entity::getObjectWfCrystal(bool total) {
		double wfCrystal = 0;
		std::vector<Object*>::iterator ot;
		for (ot=objects.begin() ; ot < objects.end(); ot++)
			wfCrystal += (*ot)->getWfCrystal();
		for (ot=def.begin() ; ot < def.end(); ot++)
			wfCrystal += (*ot)->getWfCrystal();
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				wfCrystal += (*it)->getWfCrystal();
		}

		return wfCrystal;
	}
	double Entity::getObjectWfPlastic(bool total) {
		double wfPlastic = 0;
		std::vector<Object*>::iterator ot;
		for (ot=objects.begin() ; ot < objects.end(); ot++)
			wfPlastic += (*ot)->getWfPlastic();
		for (ot=def.begin() ; ot < def.end(); ot++)
			wfPlastic += (*ot)->getWfPlastic();
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				wfPlastic += (*it)->getWfPlastic();
		}

		return wfPlastic;
	}

	double Entity::getAddedWfMetal() {
		return (this->wfMetal - this->initWfMetal);
	}

	double Entity::getAddedWfCrystal() {
		return (this->wfCrystal - this->initWfCrystal);
	}

	double Entity::getAddedWfPlastic() {
		return (this->wfPlastic - this->initWfPlastic);
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
					+ etoa::nf(etoa::d2s(this->getResMetal()))
					+ "\n"
					+ "Silizium: "
					+ etoa::nf(etoa::d2s(this->getResCrystal()))
					+ "\n"
					+ "PVC: "
					+ etoa::nf(etoa::d2s(this->getResPlastic()))
					+ "\n"
					+ "Tritium: "
					+ etoa::nf(etoa::d2s(this->getResFuel()))
					+ "\n"
					+ "Nahrung: "
					+ etoa::nf(etoa::d2s(this->getResFood()))
					+ "\n"
					+ "Bewohner: "
					+ etoa::nf(etoa::d2s(this->getResPeople()))
					+ "\n";
		return resString;
	}

	void Entity::invadeEntity(int userId) {
		this->changedData = true;
		std::time_t time = std::time(0);
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		mysqlpp::Query query = con_->query();
		
		//Log
		std::string log = "[URL=?page=user&sub=edit&user_id=";
		log += etoa::toString(userId);
		log += "][B]";
		log += etoa::get_user_nick(userId);
		log += "[/B][/URL] hat den Planeten ";
		log += this->getCoords();
		log += " von [URL=?page=user&sub=edit&user_id=";
		log += etoa::toString(this->userId);
		log += "][B]";
		log += etoa::get_user_nick(this->userId);
		log += "[/B][/URL] übernommen.";
		etoa::add_log(13,log,time);

        // Planet übernehmen
		this->lastUserId = this->userId;
		this->userId = userId;
		this->codeName = "Unbenannt";
		this->userChanged = time;
		
        // Gebäude übernehmen
        query << "UPDATE ";
		query << "	buildlist ";
		query << "	SET ";
		query << "	buildlist_user_id='" << this->userId << "' ";
		query << "WHERE ";
		query << "	buildlist_entity_id='" << this->id << "'; ";
		query.store();
		query.reset();

		// Bestehende Schiffs-Einträge löschen
		query << "DELETE FROM ";
		query << "	shiplist ";
		query << "WHERE ";
		query << "	shiplist_entity_id='" << this->id << "';";
		query.store();
		query.reset();

		query << "DELETE FROM ";
		query << "	ship_queue ";
		query << "WHERE ";
		query << "	queue_entity_id='" << this->id << "';";
		query.store();
		query.reset();

		// Bestehende Verteidigungs-Einträge löschen
		query << "DELETE FROM ";
		query << "	deflist ";
		query << "WHERE ";
		query << "	deflist_entity_id='" << this->id << "';";
		query.store(),
		query.reset();

		query << "DELETE FROM ";
		query << "	def_queue ";
		query << "WHERE ";
		query << "	queue_entity_id='" << this->id << "';";
		query.store();
		query.reset();
	}

	void Entity::resetEntity(int userId) {
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();

		this->userId = userId;
		this->codeName = "";
		this->userMain = false;
		this->resMetal = 0;
		this->resCrystal = 0;
		this->resPlastic = 0;
		this->resFuel = 0;
		this->resFood = 0;
		this->resPower = 0;
		this->resPeople = 0;

		mysqlpp::Query query = con_->query();
		query << "DELETE FROM ";
		query << "	shiplist ";
		query << "WHERE ";
		query << "	shiplist_entity_id='" << this->id << "';";
		query.store();
		query.reset();

		query << "DELETE FROM ";
		query << "	ship_queue ";
		query << "WHERE ";
		query << "	queue_entity_id='" << this->id << "';";
		query.store();
		query.reset();

		query << "DELETE FROM ";
		query << "	buildlist ";
		query << "WHERE ";
		query << "	buildlist_entity_id='" << this->id << "';";
		query.store();
		query.reset();

		query << "DELETE FROM ";
		query << "	deflist ";
		query << "WHERE ";
		query << "	deflist_entity_id='" << this->id << "';";
		query.store();
		query.reset();

		query << "DELETE FROM ";
		query << "	def_queue ";
		query << "WHERE ";
		query << "	queue_entity_id='" << this->id << "';";
		query.store();
		query.reset();
	}

	double Entity::getWeapon(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
		if (!this->defLoaded)
			this->loadDef();
		if (!this->techsAdded)
			this->addTechs();
		double weapon = this->weapon;
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				weapon += (*it)->getWeapon(total);
		}
		return weapon;
	}

	double Entity::getShield(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
		if (!this->defLoaded)
			this->loadDef();
		if (!this->techsAdded)
			this->addTechs();
		double shield = this->shield;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				shield += (*it)->getShield(total);
		}
		return shield;
	}

	double Entity::getStructure(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
		if (!this->defLoaded)
			this->loadDef();
		if (!this->techsAdded)
			this->addTechs();
		double structure = this->structure;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				structure += (*it)->getStructure(total);
		}
		return structure;
	}

	double Entity::getStructShield(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
		if (!this->defLoaded)
			this->loadDef();
		if (!this->techsAdded)
			this->addTechs();
		double structShield = this->getStructure() + this->getShield();

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				structShield += (*it)->getStructShield(total);
		}
		return structShield;
	}

	double Entity::getHeal(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
		if (!this->defLoaded)
			this->loadDef();
		if (!this->techsAdded)
			this->addTechs();
        double heal = this->heal;
        
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
            for ( it=fleets.begin() ; it < fleets.end(); it++ )
				heal += (*it)->getHeal(total);
		}
		return heal;
	}

	double Entity::getInitCount(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		double initCount = this->initCount;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				initCount += (*it)->getInitCount(total);
		}
		return initCount;
	}

	double Entity::getCount(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
		if (!this->defLoaded)
			this->loadDef();
		if (!this->techsAdded)
			this->addTechs();
		double count = this->count;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				count += (*it)->getCount(total);
		}
		return count;
	}

	double Entity::getInitDefCount() {
		if (!this->defLoaded)
			this->loadDef();

		return this->initDefCount;
	}

	double Entity::getDefCount() {
		if (this->shipsChanged)
			this->recalcShips();
		if (!this->defLoaded)
			this->loadDef();

		return this->defCount;
	}

	double Entity::getSpyCount() {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
		return this->spyCount;
	}

	double Entity::getHealCount(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
		if (!this->defLoaded)
			this->loadDef();
		if (!this->techsAdded)
			this->addTechs();
		double healCount = this->healCount;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				healCount += (*it)->getHealCount(total);
		}
		return healCount;
	}

	void Entity::setPercentSurvive(double percentage, bool total) {
		percentage = std::max(percentage,0.0);

		// if the changes affect more then one fleet
		if (total && fleets.size()) {
			
			// initialize the object conter for every fleet and a counter for the entire fleet
			std::map<unsigned int, unsigned int> fleetObjCounter;
			std::map<unsigned int, unsigned int>::iterator oct;
			std::vector<Object*>::iterator ot;
			std::vector<Fleet*>::iterator it;
			std::pair<std::map<unsigned int,unsigned int>::iterator,bool> ret;
			
			// clean the counter
			this->objCounter.clear();
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				(*it)->objCounter.clear();
			
			// calculations for the main fleet
			for ( ot = this->objects.begin() ; ot != this->objects.end(); ot++ ) {
				this->objCounter.insert ( std::pair<unsigned int, unsigned int>((*ot)->getTypeId(),(*ot)->getInitCount()) );
				fleetObjCounter.insert ( std::pair<unsigned int, unsigned int>((*ot)->getTypeId(),(*ot)->getInitCount()) );				
			}
			
			// calculations for all the support fleets
			for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
				for ( ot = (*it)->objects.begin() ; ot != (*it)->objects.end(); ot++ ) {
					(*it)->objCounter.insert ( std::pair<unsigned int, unsigned int>((*ot)->getTypeId(),(*ot)->getInitCount()) );
					ret = fleetObjCounter.insert ( std::pair<unsigned int, unsigned int>((*ot)->getTypeId(),(*ot)->getInitCount()) );
					if (ret.second==false)
						fleetObjCounter[(*ot)->getTypeId()] += (*ot)->getInitCount();			
				}
			}
			
			// calc the losts in the entire fleet
			for ( oct = fleetObjCounter.begin() ; oct != fleetObjCounter.end(); oct++ ) {
				(*oct).second = (*oct).second - ceil(percentage*(*oct).second);
			}
			
			// calc the new count of each fleet
			for ( oct = this->objCounter.begin() ; oct != this->objCounter.end(); oct++ ) {
				if (fleetObjCounter[(*oct).first]>0) {
					unsigned int losts = (*oct).second - round((*oct).second*percentage);
					losts = std::min(losts,fleetObjCounter[(*oct).first]);
					(*oct).second -= losts;
					fleetObjCounter[(*oct).first] -= losts;
				}
			}
			
			for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
				for ( oct = (*it)->objCounter.begin() ; oct != (*it)->objCounter.end(); oct++ ) {
					if (fleetObjCounter[(*oct).first]>0) {
						unsigned int losts = (*oct).second - floor((*oct).second*percentage);
						losts = std::min(losts,fleetObjCounter[(*oct).first]);
						(*oct).second -= losts;
						fleetObjCounter[(*oct).first] -= losts;
					}
				}
			}
			
			// apply the changes to the objects
			for (ot = this->objects.begin() ; ot < this->objects.end(); ot++)
				(*ot)->setPercentSurvive(percentage, this->objCounter[(*ot)->getTypeId()]);
			if (total && fleets.size()) {
				std::vector<Fleet*>::iterator it;
				for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
					(*it)->setShipsChanged();
					for (ot = (*it)->objects.begin() ; ot < (*it)->objects.end(); ot++)
						(*ot)->setPercentSurvive(percentage, (*it)->objCounter[(*ot)->getTypeId()]);	
				}
			}
			for (ot = this->def.begin() ; ot < this->def.end(); ot++)
				(*ot)->setPercentSurvive(percentage);
		}
		// if there is just one fleet affected set percentage without the counter
		else {
			std::vector<Object*>::iterator ot;
			for (ot = this->objects.begin() ; ot < this->objects.end(); ot++)
				(*ot)->setPercentSurvive(percentage);
			for (ot = this->def.begin() ; ot < this->def.end(); ot++)
				(*ot)->setPercentSurvive(percentage);
		}
		
		// set flag to update the values
		this->shipsChanged = true;
	}

	// TODO: Is double reasonable? Added conversion to int in line 777
	void Entity::addExp(double exp)
	{
		this->shipsSave = true;
		int counter = 0;
		std::vector<Object*>::iterator ot;
		for (ot = this->specialObjects.begin() ; ot < this->specialObjects.end(); ot++) {
			counter++;
			(*ot)->addExp(exp);
		}
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				counter += (int)(*it)->addExp(exp);
		}
		
		if (counter)
			this->exp = exp;
		else
			this->exp = -1;
	}

	double Entity::getExp()
	{
		double exp = 0;

		DataHandler &DataHandler = DataHandler::instance();

		std::vector<Object*>::iterator ot;
		for (ot = this->objects.begin() ; ot < this->objects.end(); ot++) {
			ShipData *data = DataHandler.getShipById((*ot)->getTypeId());
			if((data)->getCatId() != 2) {
				if((data)->getCatId() != 7) {
					exp += ((*ot)->getInitCount() - (*ot)->getCount()) * data->getCosts();
				}	
			}	
		}
		for (ot = this->def.begin() ; ot < this->def.end(); ot++) {
			DefData *data = DataHandler.getDefById((*ot)->getTypeId());
			exp += ((*ot)->getInitCount() - (*ot)->getCount()) * data->getCosts();
		}
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				exp += (*it)->getExp();
		}
		return exp;
	}

	double Entity::getAddedExp() {
		return this->exp;
	}

	std::string Entity::getUserNicks() {
		if (!this->shipsLoaded)
			this->loadShips();
		
		std::string nicks = this->entityUser->getUserNick();
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			std::size_t found;
			for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
				std::string key = (*it)->fleetUser->getUserNick();
				found=nicks.rfind(key);
				if (found==std::string::npos)
					nicks += ", "
							+ key;
			}
		}
		return nicks;
	}

	std::string Entity::getUserIds() {
		if (!this->shipsLoaded)
			this->loadShips();
			
		std::string ids = "," + etoa::d2s(this->getUserId()) + ",";
		if (this->fleets.size()) {
			std::vector<Fleet*>::iterator it;
			std::size_t found;
			for ( it = this->fleets.begin() ; it < this->fleets.end(); it++ ) {
				std::string key = etoa::d2s((*it)->getUserId()) + ",";
				found=ids.find(key);
				if (found==std::string::npos)
					ids += key;
			}
		}
		return ids;
	}

	short Entity::getShieldTech() {
		if (!this->allianceTechsLoaded)
			this->loadAllianceTechs();
		
		int counter = 1;
		double shieldTech = this->getShieldBonus();
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
				counter++;
				shieldTech += (*it)->getShieldBonus();
			}
		}
		
		shieldTech = round(shieldTech*100/counter);
		
		return shieldTech;
	}

	short Entity::getStructureTech() {
		if (!this->allianceTechsLoaded)
			this->loadAllianceTechs();
		
		int counter = 1;
		double structureTech = this->getStructureBonus();
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
				counter++;
				structureTech += (*it)->getStructureBonus();
			}
		}
		
		structureTech = round(structureTech*100/counter);

		return structureTech;
	}

	short Entity::getWeaponTech() {
		if (!this->allianceTechsLoaded)
			this->loadAllianceTechs();
		
		int counter = 1;
		double weaponTech = this->getWeaponBonus();
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
				counter++;
				weaponTech += (*it)->getWeaponBonus();
			}
		}
		
		weaponTech = round(weaponTech*100/counter);
		
		return weaponTech;
	}

	std::string Entity::getShipString(bool total)
	{
		if (!this->shipsLoaded)
			this->loadShips();
		std::map<int,int> ships;
		std::map<int,int> specialShips;

		std::vector<Object*>::iterator ot;
		for (ot = this->objects.begin() ; ot < this->objects.end(); ot++) {
			if ((*ot)->getSpecial())
				specialShips[(*ot)->getTypeId()] += (*ot)->getCount();
			else
				ships[(*ot)->getTypeId()] += (*ot)->getCount();
		}

		if (fleets.size() && total) {
			std::vector<Fleet*>::iterator it;

			for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
				for (ot = (*it)->objects.begin() ; ot < (*it)->objects.end(); ot++) {
					if ((*ot)->getSpecial())
						specialShips[(*ot)->getTypeId()] += (*ot)->getCount();
					else
						ships[(*ot)->getTypeId()] += (*ot)->getCount();
				}
			}
		}
		std::string shipString = "";
		
		std::map<int,int>::iterator st;
		for ( st=specialShips.begin() ; st != specialShips.end(); st++ ) {
			shipString += etoa::d2s((*st).first)
						+ ":"
						+ etoa::d2s((*st).second)
						+ ",";
		}
		for ( st=ships.begin() ; st != ships.end(); st++ ) {
			shipString += etoa::d2s((*st).first)
						+ ":"
						+ etoa::d2s((*st).second)
						+ ",";
		}
		if (shipString.length()<1)
			shipString = "0";
		
		return shipString;
	}

	std::string Entity::getDefString(bool rebuild) {
		this->loadDef();
		std::string defString = "";
		
		std::vector<Object*>::iterator ot;
		for (ot = this->def.begin() ; ot < this->def.end(); ot++) {
			defString += etoa::d2s((*ot)->getTypeId())
						+ ":"
						+ etoa::d2s((*ot)->getCount());
			if (rebuild)
				defString += ":"
						+ etoa::d2s((*ot)->getRebuildCount());

			defString += ",";
		}
		if (defString.length()<1)
			defString = "0";
		
		return defString;
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
		RESULT_TYPE cRes = query.store();
		query.reset();

		if (cRes) {
			int cSize = cRes.size();

			if (cSize>0) {
				mysqlpp::Row cRow = cRes.at(0);

				this->sx = (int)cRow["sx"];
				this->sy = (int)cRow["sy"];
				this->cx = (int)cRow["cx"];
				this->cy = (int)cRow["cy"];

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
								+ etoa::d2s(this->pos)
								+ ")";
			}
		}
	}

	void Entity::loadAdditionalFleets() {
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();

		mysqlpp::Query query = con->query();
		query << "SELECT ";
		query << " * ";
		query << "FROM ";
		query << " fleet ";
		query << "WHERE ";
		query << "	entity_to='" << this->getId() << "' ";
		query << "	AND action='support' ";
		query << "	AND status='3';";
		RESULT_TYPE fRes = query.store();
		query.reset();

		if (fRes) {
			int fSize = fRes.size();

			if (fSize>0) {
				mysqlpp::Row fRow;
				Fleet* additionalFleet;
				for (int i=0; i<fSize; i++) {
					fRow = fRes.at(i);
					additionalFleet = new Fleet(fRow);
					fleets.push_back(additionalFleet);
				}
			}
		}
	}

	void Entity::loadShips() {
		if (!this->shipsLoaded) {
			this->loadAdditionalFleets();

			My &my = My::instance();
			mysqlpp::Connection *con = my.get();

			mysqlpp::Query query = con->query();
			query << "SELECT ";
			query << "	* ";
			query << "FROM ";
			query << "	shiplist ";
			query << "WHERE ";
			query << "	shiplist_entity_id='" << this->getId() << "' ";
			query << "	AND shiplist_count>'0' ";
			query << "	AND shiplist_user_id='" << this->getUserId() << "';";
			RESULT_TYPE slRes = query.store();
			query.reset();

			if (slRes) {
				int slSize = slRes.size();
				this->shipsLoaded = true;

				if (slSize>0) {
					this->logEntityShipStart = "";

					DataHandler &DataHandler = DataHandler::instance();
					mysqlpp::Row slRow;

					for (int i=0; i<slSize; i++) {
						slRow = slRes.at(i);

						Object* object = ObjectFactory::createObject(slRow, 's');
						ShipData *data = DataHandler.getShipById(object->getTypeId());

						this->count += object->getCount();
						this->weapon += object->getCount() * data->getWeapon();
						this->shield += object->getCount() * data->getShield();
						this->structure += object->getCount() * data->getStructure();
						this->heal += object->getCount() * data->getHeal();
						if (data->getHeal()>0)
							this->healCount += object->getCount();

						this->logEntityShipStart += etoa::d2s(object->getTypeId())
												+ ":"
												+ etoa::d2s(object->getCount())
												+ ",";
						if (data->getActions("spy"))
							this->spyCount += object->getCount();

						this->objects.push_back(object);
						
						if (data->getSpecial())
							this->specialObjects.push_back(object);
					}
				}
			}
			if (fleets.size()) {
				std::vector<Fleet*>::iterator it;
				for ( it=fleets.begin() ; it < fleets.end(); it++ )
					(*it)->loadShips();
			}
		}
		this->loadDef();
	}

	void Entity::recalcShips() {
		if (this->shipsChanged) {
			this->shipsChanged = false;
			this->count = 0;
			this->healCount = 0;
			this->weapon = 0;
			this->shield = 0;
			this->structure = 0;
			this->heal = 0;

			this->techsAdded = false;

			DataHandler &DataHandler = DataHandler::instance();

			std::vector<Object*>::iterator it;
			for (it=objects.begin() ; it < objects.end(); it++) {
				ShipData *data = DataHandler.getShipById((*it)->getTypeId());

				this->count += (*it)->getCount();
				this->weapon += (*it)->getCount() * data->getWeapon();
				this->shield += (*it)->getCount() * data->getShield();
				this->structure += (*it)->getCount() * data->getStructure();
				this->heal += (*it)->getCount() * data->getHeal();
				if (data->getHeal()>0)
					this->healCount += (*it)->getCount();
			}
			if (fleets.size()) {
				std::vector<Fleet*>::iterator it;
				for ( it=fleets.begin() ; it < fleets.end(); it++ )
					(*it)->recalcShips();
			}
			this->recalcDef();
		}
	}

	void Entity::loadDef() {
		if (!this->defLoaded) {
			My &my = My::instance();
			mysqlpp::Connection *con = my.get();

			mysqlpp::Query query = con->query();
			query << "SELECT ";
			query << "	* ";
			query << "FROM ";
			query << "	deflist ";
			query << "WHERE ";
			query << "	deflist_entity_id='" << this->getId() << "' ";
			query << "	AND deflist_count>'0' ";
			query << "	AND deflist_user_id='" << this->getUserId() << "';";
			RESULT_TYPE dlRes = query.store();
			query.reset();

			if (dlRes) {
				int dlSize = dlRes.size();
				this->defLoaded = true;
				this->initDefCount = 0;

				if (dlSize>0) {
					this->logEntityDefStart = "";


					DataHandler &DataHandler = DataHandler::instance();
					mysqlpp::Row dlRow;

					for (int i=0; i<dlSize; i++) {
						dlRow = dlRes.at(i);

						Object* object = ObjectFactory::createObject(dlRow, 'd', this->getUser()->getSpecialist()->getSpecialistDefRepair());
						DefData *data = DataHandler.getDefById(object->getTypeId());

						this->count += object->getCount();
						this->defCount += object->getCount();
						this->weapon += object->getCount() * data->getWeapon();
						this->shield += object->getCount() * data->getShield();
						this->structure += object->getCount() * data->getStructure();
						this->heal += object->getCount() * data->getHeal();
						if (data->getHeal()>0)
							this->healCount += object->getCount();

						this->logEntityDefStart += etoa::d2s(object->getTypeId())
												+ ":"
												+ etoa::d2s(object->getCount())
												+ ",";

						def.push_back(object);
					}
					this->initDefCount = this->defCount;
				}
			}
		}
	}

	void Entity::recalcDef() {
		DataHandler &DataHandler = DataHandler::instance();

		this->defCount = 0;

		std::vector<Object*>::iterator it;
		for (it=def.begin() ; it < def.end(); it++) {
			DefData *data = DataHandler.getDefById((*it)->getTypeId());

			this->count += (*it)->getCount();
			this->defCount += (*it)->getCount();
			this->weapon += (*it)->getCount() * data->getWeapon();
			this->shield += (*it)->getCount() * data->getShield();
			this->structure += (*it)->getCount() * data->getStructure();
			this->heal += (*it)->getCount() * data->getHeal();
			if (data->getHeal()>0)
				this->healCount += (*it)->getCount();
		}
	}

	void Entity::addTechs() {
		if (!this->techsAdded) {
			if (!this->allianceTechsLoaded)
				this->loadAllianceTechs();
			this->techsAdded = true;
			this->weapon *= this->getWeaponBonus();
			this->shield *= this->getShieldBonus();
			this->structure *= this->getStructureBonus();
			this->heal *= this->getHealBonus();
		}
		if (this->initWeapon<0)
			this->initWeapon = this->weapon;
		if (this->initShield<0)
			this->initShield = this->shield;
		if (this->initStructure<0)
			this->initStructure = this->structure;
		if (this->initStructShield<0)
			this->initStructShield = this->initStructure + this->initShield;
		if (this->initHeal<0)
			this->initHeal = this->heal;
		if (this->initCount<0)
			this->initCount = this->count;
	}

	void Entity::loadAllianceTechs() {
		if (this->entityUser->getAllianceId()!=0) {
			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
			mysqlpp::Query query = con->query();
			query << "SELECT "
				<< "	alliance_techlist_tech_id, "
				<< "	alliance_techlist_current_level "
				<< "FROM "
				<< "	alliance_techlist "
				<< "WHERE "
				<< "	alliance_techlist_alliance_id='" << this->entityUser->getAllianceId() << "';";
			RESULT_TYPE aRes = query.store();
			query.reset();
			
			if (aRes) {
				Config &config = Config::instance();
				int aSize = aRes.size();
				
				std::string users = this->getUserNicks();
				size_t found;
				int userCount = 0;
				found=users.find_first_of(",");
				while (found!=std::string::npos)
				{
					userCount++;
					found=users.find_first_of(",",found+1);
				}
				
				if (aSize>0) {
					mysqlpp::Row aRow;
					for (int i=0; i<aSize; i++) {
						aRow = aRes.at(i);
						if ((int)aRow["alliance_techlist_tech_id"]==5)
							this->allianceWeapon = (int)config.nget("alliance_tech_bonus",0) * (int)aRow["alliance_techlist_current_level"]
								+ (int)config.nget("alliance_tech_bonus",1) * userCount;
						if ((int)aRow["alliance_techlist_tech_id"]==6)
							this->allianceShield = (int)config.nget("alliance_tech_bonus",0) * (int)aRow["alliance_techlist_current_level"]
								+ (int)config.nget("alliance_tech_bonus",1) * userCount;
						if ((int)aRow["alliance_techlist_tech_id"]==7)
							this->allianceStructure = (int)config.nget("alliance_tech_bonus",0) * (int)aRow["alliance_techlist_current_level"]
								+ (int)config.nget("alliance_tech_bonus",1) * userCount;
					}
					if (fleets.size()) {
						std::vector<Fleet*>::iterator it;
						for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
							(*it)->setAllianceWeapon(this->allianceWeapon);
							(*it)->setAllianceShield(this->allianceShield);
							(*it)->setAllianceStructure(this->allianceStructure);
						}
					}
				}
			}
		}
		this->allianceTechsLoaded = true;
	}

	double Entity::getWeaponBonus() {
		double bonus = 1;
		if (specialObjects.size()) {
			std::vector<Object*>::iterator it;
			DataHandler &DataHandler = DataHandler::instance();
			for ( it=specialObjects.begin() ; it < specialObjects.end(); it++ ) {
				ShipData *data = DataHandler.getShipById((*it)->getTypeId());
				bonus += (*it)->getSBonusWeapon() * data->getBonusWeapon();
			}
		}
		bonus += this->entityUser->getTechBonus((unsigned int)Config::instance().idget("WEAPON_TECH_ID"));
		return bonus;
	}

	double Entity::getShieldBonus() {
		double bonus = 1;
		if (specialObjects.size()) {
			std::vector<Object*>::iterator it;
			DataHandler &DataHandler = DataHandler::instance();
			for ( it=specialObjects.begin() ; it < specialObjects.end(); it++ ) {
				ShipData *data = DataHandler.getShipById((*it)->getTypeId());
				bonus += (*it)->getSBonusShield() * data->getBonusShield();
			}
		}
		bonus += this->entityUser->getTechBonus((unsigned int)Config::instance().idget("SHIELD_TECH_ID"));
		return bonus;
	}

	double Entity::getStructureBonus() {
		double bonus = 1;
		if (specialObjects.size()) {
			std::vector<Object*>::iterator it;
			DataHandler &DataHandler = DataHandler::instance();
			for ( it=specialObjects.begin() ; it < specialObjects.end(); it++ ) {
				ShipData *data = DataHandler.getShipById((*it)->getTypeId());
				bonus += (*it)->getSBonusStructure() * data->getBonusStructure();
			}
		}
		bonus += this->entityUser->getTechBonus((unsigned int)Config::instance().idget("STRUCTURE_TECH_ID"));
		return bonus;
	}

	double Entity::getHealBonus() {
		double bonus = 1;
		if (specialObjects.size()) {
			std::vector<Object*>::iterator it;
			DataHandler &DataHandler = DataHandler::instance();
			for ( it=specialObjects.begin() ; it < specialObjects.end(); it++ ) {
				ShipData *data = DataHandler.getShipById((*it)->getTypeId());
				bonus += (*it)->getSBonusHeal() * data->getBonusHeal();
			}
		}
		bonus += this->entityUser->getTechBonus((unsigned int)Config::instance().idget("REGENA_TECH_ID"));
		return bonus;
	}

	void Entity::loadBuildings() {
		if (!this->buildingsLoaded) {
			My &my = My::instance();
			mysqlpp::Connection *con = my.get();

			mysqlpp::Query query = con->query();
			query << "SELECT "
				<< "	buildlist_building_id, "
				<< "	buildlist_current_level, "
				<< "	buildlist_build_type "
				<< "FROM "
				<< "	buildlist "
				<< "WHERE "
				<< "	buildlist_entity_id='" << this->id << "' "
				<< "	AND buildlist_current_level>'0' "
				<< "	AND buildlist_user_id='" << this->userId << "';";
			RESULT_TYPE bRes = query.store();
			query.reset();

			if (bRes) {
				int bSize = bRes.size();
				this->buildingsLoaded = true;

				if (bSize > 0) {
					mysqlpp::Row bRow;
					for (int i=0; i<bSize; i++) {
						bRow = bRes.at(i);
						buildings[(int)bRow["buildlist_building_id"]] = (int)bRow["buildlist_current_level"];
						if ((int)bRow["buildlist_build_type"]>0)
							this->buildingAtWork = (int)bRow["buildlist_building_id"];
					}
				}
			}
		}
	}
	
	double Entity::loadPeopleAtWork() {
				
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		mysqlpp::Query query = con->query();
		query << "SELECT "
        	<< "	SUM(buildlist_people_working) AS atWork "
        	<< "FROM "
        	<< "	buildlist "
        	<< "WHERE "
        	<< "	buildlist_entity_id='" << this->id << "'";
        
		RESULT_TYPE pRes = query.store();
		query.reset();
		
		if (pRes) {
			int pSize = pRes.size();
			
			if (pSize > 0) {
				mysqlpp::Row pRow = pRes.at(0);
				
				//TODO: Nur temporÃ¤rer Fix, da wenn buildlist_people_working_status=0 NULL als Resultat geliefert wird
				if (pRow["atWork"]!="NULL")
				return (double)pRow["atWork"];
			}
		}
		
		return 0;
	}

	std::string	Entity::bombBuilding(int level) {
		if (!this->buildingsLoaded)
			this->loadBuildings();

		if (this->buildings.size()) {
			int building = rand() % this->buildings.size();

			std::map<int,int>::iterator it;
			for ( it=buildings.begin() ; it != buildings.end(); it++ ) {
				if (!building) {
					My &my = My::instance();
					mysqlpp::Connection *con_ = my.get();

					level = std::max(0,(*it).second-level);

					mysqlpp::Query query = con_->query();
					query << "UPDATE ";
					query << "	buildlist ";
					query << "SET ";
					query << "	buildlist_current_level='" << level  << "' ";
					query << "WHERE ";
					query << "	buildlist_user_id='" << this->userId << "' ";
					query << "	AND buildlist_building_id='" << (*it).first << "' ";
					query << "	AND buildlist_entity_id='" << this->id << "' ";
					query << "LIMIT 1;";
					query.store();
					query.reset();

					return (etoa::d2s((*it).first) + ":" + etoa::d2s(level) + ":"  + etoa::d2s((*it).second));
				}
				building--;
			}
		}

		return "";
	}

	std::string Entity::empBuilding(int h) {
		std::time_t time = std::time(0);

		Config &config = Config::instance();

		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		mysqlpp::Query query = con_->query();
		// Load a building by random
		query << "SELECT ";
		query << "	buildlist_deactivated, ";
		query << "	buildlist_building_id ";
		query << "FROM ";
		query << "buildlist ";
		query << "WHERE ";
		query << "	buildlist_entity_id='" << this->id << "' ";
		query << "	AND buildlist_current_level >= 0 ";
		query << "	AND (";
		query << "		buildlist_building_id='" << config.idget("FLEET_CONTROL_ID") << "' ";
		query << "		OR buildlist_building_id='" << config.idget("FACTORY_ID") << "' ";
		query << "		OR buildlist_building_id='" << config.idget("SHIPYARD_ID") << "' ";
		query << "		OR buildlist_building_id='" << config.idget("MARKET_ID") << "' ";
		query << "		)";
		query << "ORDER BY ";
		query << "	RAND() ";
		query << "LIMIT 1;";
		RESULT_TYPE bRes = query.store();
		query.reset();

		if (bRes) {
			int bSize = bRes.size();

			if (bSize > 0) {
				mysqlpp::Row bRow = bRes.at(0);
				// Calculate the time, while the building is deactivated

				int ctime = std::max((int)time,(int)bRow["buildlist_deactivated"]);

				ctime += h*3600;

				// Update the deactivated building
				query << "UPDATE ";
				query << "	buildlist ";
				query << "SET ";
				query << "	buildlist_deactivated='" << ctime << "' ";
				query << "WHERE ";
				query << "	buildlist_entity_id='" << this->id << "' ";
				query << "	AND buildlist_building_id='" << bRow["buildlist_building_id"] << "' ";
				query << "LIMIT 1";
				query.store();
				query.reset();
				
				return (etoa::d2s((int)bRow["buildlist_building_id"]) + ":" + etoa::d2s(h));
			}
		}
		return "";
	}

	std::string Entity::getBuildingString() {
		if (!this->buildingsLoaded)
			this->loadBuildings();
		
		std::string buildingString = "";
		
		if (buildings.size()) {
			std::map<int,int>::iterator it;
				for ( it=buildings.begin() ; it != buildings.end(); it++ )
					buildingString +=  etoa::d2s((*it).first)
								+ ":"
								+ etoa::d2s((*it).second)
								+ ",";
		}
		else
			buildingString += "0";

		return buildingString;
	}


	std::string Entity::getLogResStart() {
		if (this->dataLoaded) {
			std::string log = ""
							+ etoa::d2s(this->initResMetal)
							+ ":"
							+ etoa::d2s(this->initResCrystal)
							+ ":"
							+ etoa::d2s(this->initResPlastic)
							+ ":"
							+ etoa::d2s(this->initResFuel)
							+ ":"
							+ etoa::d2s(this->initResFood)
							+ ":"
							+ etoa::d2s(this->initResPeople)
							+ ":"
							+ etoa::d2s(this->initResPower)
							+ ",w,"
							+ etoa::d2s(this->initWfMetal)
							+ ":"
							+ etoa::d2s(this->initWfCrystal)
							+ ":"
							+ etoa::d2s(this->initWfPlastic);
			return log;
		}
		else
			return "0";
	}

	std::string Entity::getLogResEnd() {
		if (this->dataLoaded) {
			std::string log = ""
							+ etoa::d2s(this->resMetal)
							+ ":"
							+ etoa::d2s(this->resCrystal)
							+ ":"
							+ etoa::d2s(this->resPlastic)
							+ ":"
							+ etoa::d2s(this->resFuel)
							+ ":"
							+ etoa::d2s(this->resFood)
							+ ":"
							+ etoa::d2s(this->resPeople)
							+ ":"
							+ etoa::d2s(this->resPower)
							+ ",w,"
							+ etoa::d2s(this->wfMetal)
							+ ":"
							+ etoa::d2s(this->wfCrystal)
							+ ":"
							+ etoa::d2s(this->wfPlastic);
			return log;
		}
		else
			return "0";
	}

	std::string Entity::getLogShipsStart() {
		return this->logEntityShipStart;
	}

	std::string Entity::getLogShipsEnd() {
		return "0";
	}

