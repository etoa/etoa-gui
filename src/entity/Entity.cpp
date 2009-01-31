
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
		
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				(*it)->addMessageUser(message);
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
					+ etoa::nf(etoa::d2s(this->getResMetal()))
					+ "\n";
		return resString;
	}
	
	void Entity::invadeEntity(int userId) {
		this->changedData = true;
		std::time_t time = std::time(0);
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		mysqlpp::Query query = con_->query();

        // Planet übernehmen
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
		return count;
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
		this->shipsChanged = true;
		percentage = std::max(percentage,0.0);
		std::vector<Object*>::iterator ot;
		for (ot = this->objects.begin() ; ot < this->objects.end(); ot++)
			(*ot)->setPercentSurvive(percentage);
		for (ot = this->def.begin() ; ot < this->def.end(); ot++)
			(*ot)->setPercentSurvive(percentage);
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				(*it)->setPercentSurvive(percentage);
		}
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
			ShipData::ShipData *data = DataHandler.getShipById((*ot)->getTypeId());
			exp += ((*ot)->getInitCount() - (*ot)->getCount()) * data->getCosts();
		}
		for (ot = this->def.begin() ; ot < this->def.end(); ot++) {
			DefData::DefData *data = DataHandler.getDefById((*ot)->getTypeId());
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
	std::string Entity::getShieldString(bool small) {
		std::string shieldString = "";
		if (!small) {
			int counter = 1;
			double shieldTech = this->getShieldBonus();
			shieldString += "[b]Schild (";
			if (fleets.size()) {
				shieldString += "~";
				std::vector<Fleet*>::iterator it;
				for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
					counter++;
					shieldTech += (*it)->getShieldBonus();
				}
			}
		
			shieldString += etoa::d2s(round(shieldTech*100/counter));
			shieldString += "%):[/b] ";
		}
		shieldString += etoa::nf(etoa::d2s(this->getShield(true)));
		
		return shieldString;
	}
	
	std::string Entity::getStructureString(bool small) {
		std::string structureString = "";
		if (!small) {
			int counter = 1;
			double structureTech = this->getStructureBonus();
			structureString += "[b]Struktur (";
			if (fleets.size()) {
				structureString += "~";
				std::vector<Fleet*>::iterator it;
				for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
					counter++;
					structureTech += (*it)->getStructureBonus();
				}
			}
			
			structureString += etoa::d2s(round(structureTech*100/counter));
			structureString += "%):[/b] ";
		}
		structureString += etoa::nf(etoa::d2s(this->getStructure(true)));
		
		return structureString;
	}
	
	std::string Entity::getStructureShieldString() {
		return etoa::nf(etoa::d2s(getStructShield(true)));
	}
	
	std::string Entity::getWeaponString(bool small) {
		std::string weaponString = "";
		if (!small) {
			int counter = 1;
			double weaponTech = this->getWeaponBonus();
			weaponString += "[b]Waffen (";
			if (fleets.size()) {
				weaponString += "~";
				std::vector<Fleet*>::iterator it;
				for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
					counter++;
					weaponTech += (*it)->getWeaponBonus();
				}
			}
			
			weaponString += etoa::d2s(round(weaponTech*100/counter));
			weaponString += "%):[/b] ";
		}
		weaponString += etoa::nf(etoa::d2s(this->getWeapon(true)));
		return weaponString;
	}
	
	// TODO: What is this good for? Unused variable count!
	std::string Entity::getCountString(bool small) 
	{
		std::string countString = "";
		if (!small) 
		{
			double count = this->getWeaponBonus();
			countString += "[b]Einheiten:[/b] ";
		}
		countString += etoa::nf(etoa::d2s(this->getCount(true)));
		return countString;
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
			std::size_t found;
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
		
		DataHandler &DataHandler = DataHandler::instance();
		std::map<int,int>::iterator st;
		for ( st=specialShips.begin() ; st != specialShips.end(); st++ ) {
			ShipData::ShipData *data = DataHandler.getShipById((*st).first);	
			shipString += "[tr][td]"
						+ data->getName()
						+ "[/td][td]"
						+ etoa::nf(etoa::d2s((*st).second))
						+ "[/td][/tr]";
		}
		for ( st=ships.begin() ; st != ships.end(); st++ ) {
			ShipData::ShipData *data = DataHandler.getShipById((*st).first);	
			shipString += "[tr][td]"
						+ data->getName()
						+ "[/td][td]"
						+ etoa::nf(etoa::d2s((*st).second))
						+ "[/td][/tr]";
		}
		if (shipString.length()<1)
			shipString = "[i]Nichts vorhanden![/i]\n";
		else 
			shipString = "[table]" + shipString + "[/table]";
		return shipString;
	}
	
	std::string Entity::getDefString(bool rebuild) {
		std::string defString = "";
		DataHandler &DataHandler = DataHandler::instance();
		std::vector<Object*>::iterator ot;
		for (ot = this->def.begin() ; ot < this->def.end(); ot++) {
			DefData::DefData *data = DataHandler.getDefById((*ot)->getTypeId());	
			defString += "[tr][td]"
						+ data->getName()
						+ "[/td][td] "
						+ etoa::nf(etoa::d2s((*ot)->getCount()));
			if (rebuild)
				defString += " (+"
							+ etoa::nf(etoa::d2s((*ot)->getRebuildCount()))
							+ ")";
							
			defString += "[/td][/tr]";
		}
		if (defString.length()<1)
			defString = "[i]Nichts vorhanden![/i]\n";
		else 
			defString = "[table]" + defString + "[/table]";
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
		mysqlpp::Result cRes = query.store();
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
		
		std::time_t time = std::time(0);
		
		mysqlpp::Query query = con->query();
		query << "SELECT ";
		query << " * ";
		query << "FROM ";
		query << " fleet ";
		query << "WHERE ";
		query << "	entity_to='" << this->getId() << "' ";
		query << "	AND action='support' ";
		query << "	AND status='3';";
		mysqlpp::Result fRes = query.store();
		query.reset();
		
		if (fRes) {
			int fSize = fRes.size();
			
			if (fSize>0) {
				mysqlpp::Row fRow;
				Fleet* additionalFleet;
				for (int i=0; i<fSize; i++) {
					fRow = fRes.at(0);
					additionalFleet = new Fleet(fRow);
					fleets.push_back(additionalFleet);
				}
			}
		}
	}
	
	void Entity::loadShips() {
		if (!this->shipsLoaded) {
			this->loadAdditionalFleets();
			
			Config &config = Config::instance();
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
			mysqlpp::Result slRes = query.store();
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
						ShipData::ShipData *data = DataHandler.getShipById(object->getTypeId());
						
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
							
						objects.push_back(object);
						
						if (object->getSpecial())
							specialObjects.push_back(object);
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
				ShipData::ShipData *data = DataHandler.getShipById((*it)->getTypeId());
				
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
			Config &config = Config::instance();
			
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
			mysqlpp::Result dlRes = query.store();
			query.reset();
			
			if (dlRes) {
				int dlSize = dlRes.size();
				this->defLoaded = true;
				
				if (dlSize>0) {
					this->logEntityDefStart = "";
					
					DataHandler &DataHandler = DataHandler::instance();
					mysqlpp::Row dlRow;
					
					for (int i=0; i<dlSize; i++) {
						dlRow = dlRes.at(i);
						
						Object* object = ObjectFactory::createObject(dlRow, 'd'); 
						DefData::DefData *data = DataHandler.getDefById(object->getTypeId());
						
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
				}
			}
		}
	}
	
	void Entity::recalcDef() {
		DataHandler &DataHandler = DataHandler::instance();
		
		this->defCount = 0;
		
		std::vector<Object*>::iterator it;
		for (it=def.begin() ; it < def.end(); it++) {
			DefData::DefData *data = DataHandler.getDefById((*it)->getTypeId());
			
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
	
	double Entity::getWeaponBonus() {
		double bonus = 1;
		if (specialObjects.size()) {
			std::vector<Object*>::iterator it;
			DataHandler &DataHandler = DataHandler::instance();
			for ( it=specialObjects.begin() ; it < specialObjects.end(); it++ ) {
				ShipData::ShipData *data = DataHandler.getShipById((*it)->getTypeId());
				bonus += (*it)->getSBonusWeapon() * data->getBonusWeapon();
			}
		}
		bonus += this->entityUser->getTechBonus("Waffentechnik");
		return bonus;
	}
	
	double Entity::getShieldBonus() {
		double bonus = 1;
		if (specialObjects.size()) {
			std::vector<Object*>::iterator it;
			DataHandler &DataHandler = DataHandler::instance();
			for ( it=specialObjects.begin() ; it < specialObjects.end(); it++ ) {
				ShipData::ShipData *data = DataHandler.getShipById((*it)->getTypeId());
				bonus += (*it)->getSBonusShield() * data->getBonusShield();
			}
		}
		bonus += this->entityUser->getTechBonus("Schutzschilder");
		return bonus;
	}
	
	double Entity::getStructureBonus() {
		double bonus = 1;
		if (specialObjects.size()) {
			std::vector<Object*>::iterator it;
			DataHandler &DataHandler = DataHandler::instance();
			for ( it=specialObjects.begin() ; it < specialObjects.end(); it++ ) {
				ShipData::ShipData *data = DataHandler.getShipById((*it)->getTypeId());
				bonus += (*it)->getSBonusStructure() * data->getBonusStructure();
			}
		}
		bonus += this->entityUser->getTechBonus("Panzerung");
		return bonus;
	}
	
	double Entity::getHealBonus() {
		double bonus = 1;
		if (specialObjects.size()) {
			std::vector<Object*>::iterator it;
			DataHandler &DataHandler = DataHandler::instance();
			for ( it=specialObjects.begin() ; it < specialObjects.end(); it++ ) {
				ShipData::ShipData *data = DataHandler.getShipById((*it)->getTypeId());
				bonus += (*it)->getSBonusHeal() * data->getBonusHeal();
			}
		}
		bonus += this->entityUser->getTechBonus("Regenatechnik");
		return bonus;
	}
	
	void Entity::loadBuildings() {
		if (!this->buildingsLoaded) {
			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
			
			mysqlpp::Query query = con->query();
			query << "SELECT ";
			query << "	buildlist_building_id, ";
			query << "	buildlist_current_level, ";
			query << "	buildlist_build_type ";
			query << "FROM ";
			query << "	buildlist ";
			query << "WHERE ";
			query << "	buildlist_entity_id='" << this->id << "' ";
			query << "	AND buildlist_current_level>'0' ";
			query << "	AND buildlist_user_id='" << this->userId << "';";
			mysqlpp::Result bRes = query.store();
			query.reset();
			
			if (bRes) {
				int bSize = bRes.size();
				this->buildingsLoaded = true;
				
				if (bSize > 0) {
					mysqlpp::Row bRow;
					DataHandler &DataHandler = DataHandler::instance();
					for (int i=0; i<bSize; i++) {
						bRow = bRes.at(i);
						BuildingData::BuildingData *data = DataHandler.getBuildingById((int)bRow["buildlist_building_id"]);
						buildings[data->getName()] = (int)bRow["buildlist_current_level"];
						if ((int)bRow["buildlist_build_type"]>0)
							this->buildingAtWork = data->getName();
					}
				}
			}
		}
	}
	
	std::string	Entity::bombBuilding(int level) {
		if (!this->buildingsLoaded)
			this->loadBuildings();
		
		if (this->buildings.size()) {
			int building = rand() % this->buildings.size();
			
			std::map<std::string,int>::iterator it;
			for ( it=buildings.begin() ; it != buildings.end(); it++ ) {
				if (!building) {
					DataHandler &DataHandler = DataHandler::instance();
					BuildingData::BuildingData *data = DataHandler.getBuildingByName((*it).first);
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
					query << "	AND buildlist_building_id='" << data->getId() << "' ";
					query << "LIMIT 1;";
					query.store();
					query.reset();
					
					return ("[/b]hat das Gebäude " + (*it).first + " des Planeten [b]" + this->getCoords() + "[/b] um ein Level auf Stuffe " + etoa::d2s((*it).second) + " zurück gesetzt.");
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
		query << "	AND buildlist_current_level > 0 ";
		query << "	AND (";
		query << "		buildlist_building_id='" << config.idget("FLEET_CONTROL_ID") << "' ";
		query << "		OR buildlist_building_id='" << config.idget("FACTORY_ID") << "' ";
		query << "		OR buildlist_building_id='" << config.idget("SHIPYARD_ID") << "' ";
		query << "		OR buildlist_building_id='" << config.idget("BUILD_MISSILE_ID") << "' ";
		query << "		)";
		query << "ORDER BY ";
		query << "	RAND() ";
		query << "LIMIT 1;";
		mysqlpp::Result bRes = query.store();
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
				
				DataHandler &DataHandler = DataHandler::instance();
				BuildingData::BuildingData *data = DataHandler.getBuildingById((int)bRow["buildlist_building_id"]);
				
				return ("[/b]hat das Gebäude " + data->getName() + " des Planeten [b]" + this->getCoords() + "[/b] für " + etoa::d2s(h) + "h deaktiviert.");
			}
		}
		return "";
	}
	
	std::string Entity::getBuildingString() {
		if (!this->buildingsLoaded)
			this->loadBuildings();
		std::string buildingString = "[b]GEBÄUDE:[/B]\n";
		
		if (buildings.size()) {
			buildingString += "[table]";
			std::map<std::string,int>::iterator it;
				for ( it=buildings.begin() ; it != buildings.end(); it++ )
					buildingString += "[tr][td]" + (*it).first + "[/td][td]" + etoa::d2s((*it).second) + "[/td][/tr]";
			
			buildingString += "[/table]";
		}
		else
			buildingString += "[i]Nichts vorhanden[/i]\n";
		
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

