#include "Fleet.h"

	Fleet::Fleet(mysqlpp::Row &fleet) {
		this->action = std::string(fleet["action"]);
		this->fId = (int)fleet["id"];
		this->userId = (int)fleet["user_id"];
		this->leaderId = (int)fleet["leader_id"];
		this->entityFrom = (int)fleet["entity_from"];
		this->entityTo = (int)fleet["entity_to"];
		this->nextId = (int)fleet["next_id"];
		this->launchtime = (int)fleet["launchtime"];
		this->landtime = (int)fleet["landtime"];
		this->nextactiontime = (int)fleet["nextactiontime"];
		this->status = (short)fleet["status"];
		this->pilots = (double)fleet["pilots"];
		this->usageFuel = (int)fleet["usage_fuel"];
		this->usageFood = (int)fleet["usage_food"];
		this->usagePower = (int)fleet["usage_power"];
		this->supportUsageFuel = (int)fleet["support_usage_fuel"];
		this->supportUsageFood = (int)fleet["support_usage_food"];
		this->resMetal = (double)fleet["res_metal"];
		this->resCrystal = (double)fleet["res_crystal"];
		this->resPlastic = (double)fleet["res_plastic"];
		this->resFuel = (double)fleet["res_fuel"];
		this->resFood = (double)fleet["res_food"];
		this->resPower = (double)fleet["res_power"];
		this->resPeople = (double)fleet["res_people"];
		this->fetchMetal = (double)fleet["fetch_metal"];
		this->fetchCrystal = (double)fleet["fetch_crystal"];
		this->fetchPlastic = (double)fleet["fetch_plastic"];
		this->fetchFuel = (double)fleet["fetch_fuel"];
		this->fetchFood = (double)fleet["fetch_food"];
		this->fetchPower = (double)fleet["fetch_power"];
		this->fetchPeople = (double)fleet["fetch_people"];

		if (this->getLeaderId() == this->getId())
			this->loadAdditionalFleets();

		this->initResMetal = this->getResMetal();
		this->initResCrystal = this->getResCrystal();
		this->initResPlastic = this->getResPlastic();
		this->initResFuel = this->getResFuel();
		this->initResFood = this->getResFood();
		this->initResPower = this->getResPower();
		this->initResPeople = this->getResPeople();

		this->count = 0;
		this->weapon = 0;
		this->shield = 0;
		this->structure = 0;
		this->heal = 0;
		this->healCount = 0;
		this->actionCount = 0;

		this->initWeapon = -1;
		this->initShield = -1;
		this->initStructure = -1;
		this->initStructShield = -1;
		this->initHeal = -1;
		this->initCount = -1;
		
		this->allianceWeapon = 0;
		this->allianceStructure = 0;
		this->allianceShield = 0;

		this->exp = 0;

		this->antraxBonus = 0;
		this->antraxFoodBonus = 0;
		this->destroyBonus = 0;
		this->empBonus = 0;
		this->forstealBonus = 0;

		this->capacity = 0;
		this->peopleCapacity = 0;
		this->actionCapacity = 0;

		this->actionAllowed = false;
		this->shipsLoaded = false;
		this->entityLoaded = false;
		this->shipsChanged = false;

		this->techsAdded = false;
		this->allianceTechsLoaded = false;

		this->fleetUser = new User(this->getUserId());

		this->logFleetShipStart = "0";

		if (this->status==0) {
			this->usageFuel /= 2;
			this->usageFood /= 2;
			this->usagePower /= 2;
		}
		else if (this->status==3) {
			this->supportUsageFuel = 0;
			this->supportUsageFood = 0;
		}
		else {
			this->usageFuel = 0;
			this->usageFood = 0;
			this->usagePower = 0;
		}
	}

	int Fleet::getId() {
		return this->fId;
	}

	int Fleet::getUserId() {
		return this->userId;
	}

	int Fleet::getLeaderId() {
		return this->leaderId;
	}

	int Fleet::getEntityFrom() {
		return this->entityFrom;
	}

	int Fleet::getEntityTo() {
		return this->entityTo;
	}

	int Fleet::getNextId() {
		return this->nextId;
	}

	int Fleet::getLandtime() {
		return this->landtime;
	}

	int Fleet::getLaunchtime() {
		return this->launchtime;
	}

	int Fleet::getNextactiontime() {
		return this->nextactiontime;
	}

	std::string Fleet::getAction() {
		return this->action;
	}

	short Fleet::getStatus() {
		return this->status;
	}

	void Fleet::addMessageUser(Message* message) {
		message->addUserId(this->getUserId());
		std::string nicks = this->fleetUser->getUserNick();
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			std::size_t found;
			for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
				std::string key = (*it)->fleetUser->getUserNick();
				found=nicks.rfind(key);
				if (found==std::string::npos) {
					(*it)->addMessageUser(message);
					nicks += ", "
							+ key;
				}
			}
		}
	}

	double Fleet::getPilots(bool total) {
		double pilots = this->pilots;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				pilots += (*it)->getPilots(total);
		}
		return pilots;
	}

	double Fleet::getResMetal(bool total) {
		double resMetal = this->resMetal;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				resMetal += (*it)->getResMetal(total);
		}
		return resMetal;
	}

	double Fleet::getResCrystal(bool total) {
		double resCrystal = this->resCrystal;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				resCrystal += (*it)->getResCrystal(total);
		}
		return resCrystal;
	}

	double Fleet::getResPlastic(bool total) {
		double resPlastic = this->resPlastic;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				resPlastic += (*it)->getResPlastic(total);
		}
		return resPlastic;
	}

	double Fleet::getResFuel(bool total) {
		double resFuel = this->resFuel;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				resFuel += (*it)->getResFuel(total);
		}
		return resFuel;
	}

	double Fleet::getResFood(bool total) {
		double resFood = this->resFood;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				resFood += (*it)->getResFood(total);
		}

		return resFood;
	}

	double Fleet::getResPower(bool total) {
		double resPower = this->resPower;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				resPower += (*it)->getResPower(total);
		}
		return resPower;
	}

	double Fleet::getResPeople(bool total) {
		double resPeople = this->resPeople;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				resPeople += (*it)->getResPeople(total);
		}
		return resPeople;
	}

	double Fleet::getResLoaded(bool total) {
		return this->getResMetal(total)
				+ this->getResCrystal(total)
				+ this->getResPlastic(total)
				+ this->getResFuel(total)
				+ this->getResFood(total);
	}

	double Fleet::getInitResLoaded() {
		return this->initResMetal
				+ this->initResCrystal
				+ this->initResPlastic
				+ this->initResFuel
				+ this->initResFood;
	}

	double Fleet::getCapacity(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
		double capacity = this->capacity - this->getResLoaded() - this->usageFuel - this->usageFood - this->supportUsageFuel - this->supportUsageFood;
		
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				capacity += (*it)->getCapacity(total);
		}
		return capacity;
	}

	double Fleet::getCapa() {
		return this->capacity;
	}

	double Fleet::getActionCapacity(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
		double actionCapacity = std::min(this->actionCapacity,this->getCapacity());

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				actionCapacity += (*it)->getActionCapacity(total);
		}
		return actionCapacity;
	}

	double Fleet::getPeopleCapacity(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
		double peopleCapacity = 0;
		peopleCapacity += this->peopleCapacity;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				peopleCapacity += (*it)->getPeopleCapacity(total);
		}
		return peopleCapacity;
	}

	double Fleet::getBounty() {
		return this->bounty;
	}

	double Fleet::getBountyBonus(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
		double bounty = getBounty();
		double capacity = getCapa();

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
				capacity += (*it)->getCapa();
				bounty += (*it)->getBounty();
			}
		}
		return bounty/capacity;
	}

	void Fleet::addRaidedRes() {
		this->fleetUser->addRaidedRes(this->getResLoaded()-this->getInitResLoaded());

		if (this->fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=this->fleets.begin() ; it < this->fleets.end(); it++ )
				(*it)->fleetUser->addRaidedRes((*it)->getResLoaded()-(*it)->getInitResLoaded());
		}
	}

	double Fleet::getFetchMetal(bool total) {
		double fetchMetal = this->fetchMetal;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				fetchMetal += (*it)->getFetchMetal(total);
		}
		return fetchMetal;
	}

	double Fleet::getFetchCrystal(bool total) {
		double fetchCrystal = this->fetchCrystal;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				fetchCrystal += (*it)->getFetchCrystal(total);
		}
		return fetchCrystal;
	}

	double Fleet::getFetchPlastic(bool total) {
		double fetchPlastic = this->fetchPlastic;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				fetchPlastic += (*it)->getFetchPlastic(total);
		}
		return fetchPlastic;
	}

	double Fleet::getFetchFuel(bool total) {
		double fetchFuel = this->fetchFuel;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				fetchFuel += (*it)->getFetchFuel(total);
		}
		return fetchFuel;
	}

	double Fleet::getFetchFood(bool total) {
		double fetchFood = this->fetchFood;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				fetchFood += (*it)->getFetchFood(total);
		}
		return fetchFood;
	}

	double Fleet::getFetchPeople(bool total) {
		double fetchPeople = this->fetchPeople;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				fetchPeople += (*it)->getFetchPeople(total);
		}
		return fetchPeople;
	}

	double Fleet::getFetchSum(bool total) {
		return(this->getFetchMetal(total) + this->getFetchCrystal(total) + this->getFetchPlastic(total) + this->getFetchFuel(total) + this->getFetchFood(total));
	}

	double Fleet::addMetal(double metal, bool total) {
		this->changedData = true;
		double raidedMetal = 0;
		metal = round(metal);
		double initCapa = this->getCapacity(total);
		if (metal>=initCapa)
			metal = initCapa;
		
		if (metal*initCapa) 
			this->resMetal += raidedMetal = metal*this->getCapacity()/initCapa;
		
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				raidedMetal += (*it)->addMetal(metal*(*it)->getCapacity()/initCapa);
		}
		return raidedMetal;
	}

	double Fleet::addCrystal(double crystal, bool total) {
		this->changedData = true;
		double raidedCrystal = 0;
		crystal = round(crystal);
		double initCapa = this->getCapacity(total);
		if (crystal>=initCapa)
			crystal = initCapa;
		
		if (crystal*initCapa)
			this->resCrystal += raidedCrystal = crystal*this->getCapacity()/initCapa;
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				raidedCrystal += (*it)->addCrystal(crystal*(*it)->getCapacity()/initCapa);
		}
		return raidedCrystal;
	}

	double Fleet::addPlastic(double plastic, bool total) {
		this->changedData = true;
		double raidedPlastic = 0;
		plastic = round(plastic);
		double initCapa = this->getCapacity(total);
		if (plastic>=initCapa)
			plastic = initCapa;
		
		if (plastic*initCapa)
			this->resPlastic += raidedPlastic = plastic*this->getCapacity()/initCapa;
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				raidedPlastic += (*it)->addPlastic(plastic*(*it)->getCapacity()/initCapa);
		}
		return raidedPlastic;
	}

	double Fleet::addFuel(double fuel, bool total) {
		this->changedData = true;
		double raidedFuel = 0;
		fuel = round(fuel);
		double initCapa = this->getCapacity(total);
		if (fuel>=initCapa)
			fuel = initCapa;
		
		if (fuel*initCapa)
			this->resFuel += raidedFuel = fuel*this->getCapacity()/initCapa;
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				raidedFuel += (*it)->addFuel(fuel*(*it)->getCapacity()/initCapa);
		}
		return raidedFuel;
	}

	double Fleet::addFood(double food, bool total) {
		this->changedData = true;
		double raidedFood = 0;
		food = round(food);
		double initCapa = this->getCapacity(total);
		if (food>=initCapa)
			food = initCapa;
		
		if (food*this->getCapacity(total))
			this->resFood += raidedFood = food*this->getCapacity()/initCapa;
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				raidedFood += (*it)->addFood(food*(*it)->getCapacity()/initCapa);
		}
		return raidedFood;
	}

	double Fleet::addPower(double power, bool total) {
		power = round(power);
		this->changedData = true;
		if (power>=this->getCapacity())
			power = this->getCapacity();
		
		if (power*this->getCapacity(total))
			this->resPower += power*this->getCapacity()/this->getCapacity(total);
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				power += (*it)->addPower(power*(*it)->getCapacity()/this->getCapacity(total));
		}
		return power;
	}

	double Fleet::addPeople(double people, bool total) {
		people = round(people);
		this->changedData = true;
		if (people>=this->getPeopleCapacity())
			people = this->getPeopleCapacity();
		
		if (people*this->getPeopleCapacity(total))
			this->resPeople += people*this->getPeopleCapacity()/this->getPeopleCapacity(total);
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				people += (*it)->addPeople(people*(*it)->getPeopleCapacity()/this->getPeopleCapacity(total));
		}
		
		return people;
	}

	double Fleet::unloadResMetal() {
		this->changedData = true;
		double metal = this->resMetal;
		this->resMetal = 0;
		return metal;
	}

	double Fleet::unloadResCrystal() {
		this->changedData = true;
		double crystal = this->resCrystal;
		this->resCrystal = 0;
		return crystal;
	}

	double Fleet::unloadResPlastic() {
		this->changedData = true;
		double plastic = this->resPlastic;
		this->resPlastic = 0;
		return plastic;
	}

	double Fleet::unloadResFuel(bool land) {
		this->changedData = true;
		double fuel = this->resFuel;
		if (land) {
			fuel += this->usageFuel + this-> supportUsageFuel;
			this->usageFuel = 0;
			this->supportUsageFuel = 0;
		}
		this->resFuel = 0;
		return fuel;
	}

	double Fleet::unloadResFood(bool land) {
		this->changedData = true;
		double food = this->getResFood();
		if (land) {
			food += this->usageFood + this->supportUsageFood;
			this->usageFood = 0;
			this->supportUsageFood = 0;
		}
		this->resFood = 0;
		return food;
	}

	double Fleet::unloadResPower() {
		this->changedData = true;
		double power = this->resPower;
		this->resPower = 0;
		return power;
	}

	double Fleet::unloadResPeople(bool land) {
		this->changedData = true;
		double people = this->resPeople;
		if (land) {
			people += this->pilots;
			this->pilots = 0;
		}
		this->resPeople = 0;
		return people;
	}

	double Fleet::getWfMetal(bool total) {
		double wfMetal = 0;
		std::vector<Object*>::iterator ot;
		for (ot=objects.begin() ; ot < objects.end(); ot++)
			wfMetal += (*ot)->getWfMetal();
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				wfMetal += (*it)->getWfMetal();
		}

		return wfMetal;
	}

	double Fleet::getWfCrystal(bool total) {
		double wfCrystal = 0;
		std::vector<Object*>::iterator ot;
		for (ot=objects.begin() ; ot < objects.end(); ot++)
			wfCrystal += (*ot)->getWfCrystal();
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				wfCrystal += (*it)->getWfCrystal();
		}

		return wfCrystal;
	}

	double Fleet::getWfPlastic(bool total) {
		double wfPlastic = 0;
		std::vector<Object*>::iterator ot;
		for (ot=objects.begin() ; ot < objects.end(); ot++)
			wfPlastic += (*ot)->getWfPlastic();
		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				wfPlastic += (*it)->getWfPlastic();
		}

		return wfPlastic;
	}

	double Fleet::getWeapon(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
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

	double Fleet::getShield(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
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

	double Fleet::getStructure(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
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

	double Fleet::getStructShield(bool total) {
		double structShield = this->getStructure() + this->getShield();

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				structShield += (*it)->getStructShield(total);
		}
		return structShield;
	}

	double Fleet::getHeal(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
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

	double Fleet::getInitCount(bool total) {
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

	double Fleet::getCount(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
		double count = this->count;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				count += (*it)->getCount(total);
		}
		return count;
	}

	double Fleet::getHealCount(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
		double healCount = this->healCount;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				healCount += (*it)->getHealCount(total);
		}
		return healCount;
	}

	unsigned int Fleet::getActionCount(bool total) {
		if (!this->shipsLoaded)
			this->loadShips();
		if (this->shipsChanged)
			this->recalcShips();
		unsigned int actionCount = this->actionCount;

		if (total && fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				actionCount += (*it)->getActionCount(total);
		}
		return actionCount;
	}

	double Fleet::addExp(double exp) {
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
			this->exp += exp;
		else
			this->exp = -1;
			// TODO added this to overcome error, but which value should it be?
		return counter;
	}

	double Fleet::getExp() {
		double exp = 0;

		DataHandler &DataHandler = DataHandler::instance();

		std::vector<Object*>::iterator ot;
		for (ot = this->objects.begin() ; ot < this->objects.end(); ot++) {
			ShipData::ShipData *data = DataHandler.getShipById((*ot)->getTypeId());
			exp += ((*ot)->getInitCount() - (*ot)->getCount()) * data->getCosts();
		}
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				exp += (*it)->getExp();
		}
		
		return exp;
	}

	double Fleet::getAddedExp()
	{
		return this->exp;
	}

	double Fleet::getSpecialShipBonusAntrax() {
		double antraxBonus = this->antraxBonus;
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				antraxBonus += (*it)->getSpecialShipBonusAntrax();
		}
		return antraxBonus;
	}

	double Fleet::getSpecialShipBonusAntraxFood() {
		double antraxFoodBonus = this->antraxFoodBonus;
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				antraxFoodBonus += (*it)->getSpecialShipBonusAntraxFood();
		}
		return antraxFoodBonus;
	}

	double Fleet::getSpecialShipBonusBuildDestroy() {
		double destroyBonus = this->destroyBonus;
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				destroyBonus += (*it)->getSpecialShipBonusBuildDestroy();
		}
		return destroyBonus;
	}

	double Fleet::getSpecialShipBonusEMP() {
		double empBonus = this->empBonus;
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				empBonus += (*it)->getSpecialShipBonusEMP();
		}
		return empBonus;
	}

	double Fleet::getSpecialShipBonusForsteal() {
		double forstealBonus = this->forstealBonus;
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				forstealBonus += (*it)->getSpecialShipBonusForsteal();
		}
		return forstealBonus;
	}

	void Fleet::deleteActionShip(int count) {
		this->shipsChanged = true;
		std::vector<Object*>::iterator ot;
		for (ot = this->actionObjects.begin() ; ot < this->actionObjects.end(); ot++) {
			count = (*ot)->removeObjects(count);
			if (!count) break;
		}
	}

	void Fleet::setPercentSurvive(double percentage, bool total) {
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
		}
		// if there is just one fleet affected set percentage without the counter
		else {
			std::vector<Object*>::iterator ot;
			for (ot = this->objects.begin() ; ot < this->objects.end(); ot++)
				(*ot)->setPercentSurvive(percentage);
		}
		
		// set flag to update the values
		this->shipsChanged = true;
	}

	void Fleet::setReturn() {
		int entity = this->entityFrom;
		this->entityFrom = this->entityTo;
		int duration;

		if (this->getStatus() == 3 && (this->getNextactiontime() > 0 || this->getAction()=="support")) {
			duration = this->getNextactiontime();
			this->entityTo = this->getNextId();
		}
		else {
			duration = this->getLandtime() - this->getLaunchtime();
			this->entityTo = entity;
		}
		this->launchtime = this->getLandtime();
		this->landtime = this->getLaunchtime() + duration;

		this->status = 1;

		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			for ( it=fleets.begin() ; it < fleets.end(); it++ )
				(*it)->setReturn();
		}
	}

	void Fleet::setMain() {
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		mysqlpp::Query query = con->query();
		query << "SELECT "
			<< "	planets.id "
			<< "FROM "
			<< "	planets "
			<< "WHERE "
			<< "	planets.planet_user_id='" << this->getUserId() << "' "
			<< "	AND planets.planet_user_main='1' "
			<< "LIMIT 1;";
		RESULT_TYPE mainRes = query.store();
		query.reset();

		if (mainRes) {
			int mainSize = mainRes.size();

			if (mainSize > 0) {
				mysqlpp::Row mainRow = mainRes.at(0);

				int duration = this->getLandtime() - this->getLaunchtime();
				this->launchtime = this->getLandtime();
				this->landtime += duration;
				this->status = 2;
				this->entityFrom = this->entityTo;
				this->entityTo = (int)mainRow["id"];
			}
		}
	}

	void Fleet::setSupport() {
		int temp = this->getLandtime() - this->getLaunchtime();
		this->launchtime = this->getLandtime();
		this->landtime = this->getLandtime() + this->getNextactiontime();
		this->status = 3;
		this->nextactiontime = temp;

		this->nextId = this->entityFrom;
		this->entityFrom = this->entityTo;
	}

	std::string Fleet::getUserNicks() {
		std::string nicks = this->fleetUser->getUserNick();
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

	std::string Fleet::getUserIds() {
		std::string ids = "," + etoa::d2s(this->getUserId()) + ",";
		if (fleets.size()) {
			std::vector<Fleet*>::iterator it;
			std::size_t found;
			for ( it=fleets.begin() ; it < fleets.end(); it++ ) {
				std::string key = etoa::d2s((*it)->getUserId()) + ",";
				found=ids.find(key);
				if (found==std::string::npos)
					ids += key;
			}
		}
		return ids;
	}

	short Fleet::getShieldTech() {
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

		shieldTech =round(shieldTech*100/counter);
		
		return shieldTech;
	}

	short Fleet::getStructureTech() {
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

	short Fleet::getWeaponTech() {
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

	std::string Fleet::getDestroyedShipString() {
		std::string destroyedString = "";
		
		std::vector<Object*>::iterator it;
		for (it = this->objects.begin() ; it < this->objects.end(); it++) {
			if ((*it)->getCount() < (*it)->getInitCount()) {
				destroyedString +=  etoa::d2s((*it)->getTypeId())	+ ":" + etoa::d2s((*it)->getInitCount() - (*it)->getCount()) + ",";
			}
		}

		return destroyedString;
	}

	std::string Fleet::getShipString() {
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

		if (fleets.size()) {
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


	bool Fleet::actionIsAllowed() {
		if (!this->shipsLoaded)
			this->loadShips();
		else if (this->shipsChanged)
			this->recalcShips();
		return this->actionAllowed;
	}
	
	void Fleet::setShipsChanged() {
		this->shipsChanged = true;
	}

	void Fleet::loadAdditionalFleets() {
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();

		std::time_t time = std::time(0);

		mysqlpp::Query query = con->query();
		query << "SELECT "
			<< " * "
			<< "FROM "
			<< " fleet "
			<< "WHERE "
			<< "	leader_id='" << this->getLeaderId() << "' "
			<< "	AND landtime<='" << time << "' "
			<< "	AND status=3;";
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

	void Fleet::loadShips() {
		if (!this->shipsLoaded) {
			this->shipsLoaded = true;
			Config &config = Config::instance();
			My &my = My::instance();
			mysqlpp::Connection *con = my.get();

			mysqlpp::Query query = con->query();
			query << "SELECT "
				<< "	* "
				<< "FROM "
				<< "	fleet_ships "
				<< "WHERE "
				<< "	fs_fleet_id='" << this->getId() << "';";
			RESULT_TYPE fsRes = query.store();
			query.reset();

			if (fsRes) {
				int fsSize = fsRes.size();

				if (fsSize>0) {
					this->logFleetShipStart = "";

					DataHandler &DataHandler = DataHandler::instance();
					mysqlpp::Row fsRow;

					for (int i=0; i<fsSize; i++) {
						fsRow = fsRes.at(i);

						if (config.idget("MARKET_SHIP_ID")!=(int)fsRow["fs_ship_id"]) {
							Object* object = ObjectFactory::createObject(fsRow, 'f');
							ShipData::ShipData *data = DataHandler.getShipById(object->getTypeId());

							this->capacity += object->getCount() * data->getCapacity();
							this->peopleCapacity += object->getCount() * data->getPeopleCapacity();

							this->bounty += object->getCount() * data->getCapacity() * data->getBountyBonus();
							if (data->getActions(this->action)) {
								this->actionAllowed = true;
								this->actionCapacity += object->getCount() * data->getCapacity();
								this->actionCount += object->getCount();
								this->actionObjects.push_back(object);
							}

							this->count += object->getCount();
							this->weapon += object->getCount() * data->getWeapon();
							this->shield += object->getCount() * data->getShield();
							this->structure += object->getCount() * data->getStructure();
							this->heal += object->getCount() * data->getHeal();
							if (data->getHeal()>0)
								this->healCount += object->getCount();

							this->logFleetShipStart += etoa::d2s(object->getTypeId())
													+ ":"
													+ etoa::d2s(object->getCount())
													+ ",";

							this->objects.push_back(object);

							if (data->getSpecial()) {
								this->antraxBonus += object->getCount() * object->getSBonusAntrax() * data->getBonusAntrax();
								this->antraxFoodBonus += object->getCount() * object->getSBonusAntraxFood() * data->getBonusAntraxFood();
								this->destroyBonus += object->getCount() * object->getSBonusBuildDestroy() * data->getBonusBuildDestroy();
								this->empBonus += object->getCount() * object->getSBonusDeactivade() * data->getBonusDeactivade();
								this->forstealBonus += object->getCount() * object->getSBonusForsteal() * data->getBonusForsteal();
								this->specialObjects.push_back(object);
							}
						}
					}
				}
			}
			if (fleets.size()) {
				std::vector<Fleet*>::iterator it;
				for ( it=fleets.begin() ; it < fleets.end(); it++ )
					(*it)->loadShips();
			}
		}
	}

	void Fleet::recalcShips() {
		if (this->shipsChanged) {
			this->shipsChanged = false;
			this->actionCapacity = 0;
			this->capacity = 0;
			this->peopleCapacity = 0;

			this->count = 0;
			this->weapon = 0;
			this->shield = 0;
			this->structure = 0;
			this->heal = 0;
			this->bounty = 0;
			this->actionCount = 0;

				this->antraxBonus = 0;
				this->antraxFoodBonus = 0;
				this->destroyBonus = 0;
				this->empBonus = 0;
				this->forstealBonus = 0;

			this->techsAdded = false;

			this->actionAllowed = false;

			DataHandler &DataHandler = DataHandler::instance();

			std::vector<Object*>::iterator it;
			for (it=objects.begin() ; it < objects.end(); it++) {
				ShipData::ShipData *data = DataHandler.getShipById((*it)->getTypeId());

				this->capacity += (*it)->getCount() * data->getCapacity();
				this->peopleCapacity += (*it)->getCount() * data->getPeopleCapacity();

				this->bounty += (*it)->getCount() * data->getCapacity() * data->getBountyBonus();

				if (data->getActions(this->action)) {
					this->actionAllowed = true;
					this->actionCapacity += (*it)->getCount() * data->getCapacity();
					this->actionCount += (*it)->getCount();
				}
				/* BUGFIX: Wenn ein Schiff (z.B. Onefight) keine Struktur und kein Schild hat,
				 * sollten gar keine davon mehr uebrig bleiben. Sobald ein solches Schiff weniger als
				 * initCount in der Flotte ist, wird die Anzahl mit dieser Bedingung auf null gesetzt.
				 * Dies kann nicht in setPercentSurvive() oder im Object gemacht werden, weil nur
				 * an dieser Stelle im Code die Schiffdaten abgerufen werden.
				 * 
				 * Ein Hinzufuegen der Daten unten ist danach nicht mehr noetig, da getCount() sowieso
				 * immer null zurueckliefern wuerde.
				 * 
				 * Das fuehrt dazu, dass solche Schiffe bereits nach der ersten Runde eines Kampfes alle
				 * zerstoert sind.
				 * 
				 * Moeglicherweise muss diese Bedingung auch in Fleet::loadShips uebernommen werden.
				 * 
				 * TODO: Dieser Bugfix funktioniert nicht, wenn weniger als ein Schiff zerstoert wird.
				 * 
				 * Bugfix von river
				 */
				if(data->getStructure() == 0 && data->getShield() == 0 && (*it)->getCount() != (*it)->getInitCount())
				{
					(*it)->setPercentSurvive(0.0, 0);
				}
				else
				{
					this->count += (*it)->getCount();
					this->weapon += (*it)->getCount() * data->getWeapon();
					this->shield += (*it)->getCount() * data->getShield();
					this->structure += (*it)->getCount() * data->getStructure();
					this->heal += (*it)->getCount() * data->getHeal();

					if ((*it)->getSpecial())
						this->antraxBonus += (*it)->getCount() * (*it)->getSBonusAntrax() * data->getBonusAntrax();
						this->antraxFoodBonus += (*it)->getCount() * (*it)->getSBonusAntraxFood() * data->getBonusAntraxFood();
						this->destroyBonus += (*it)->getCount() * (*it)->getSBonusBuildDestroy() * data->getBonusBuildDestroy();
						this->empBonus += (*it)->getCount() * (*it)->getSBonusDeactivade() * data->getBonusDeactivade();
						this->forstealBonus += (*it)->getCount() * (*it)->getSBonusForsteal() * data->getBonusForsteal();
				}
			}

			if (fleets.size()) {
				std::vector<Fleet*>::iterator it;
				for ( it=fleets.begin() ; it < fleets.end(); it++ )
					(*it)->recalcShips();
			}
		}
	}

	void Fleet::addTechs() {
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
	
	void Fleet::loadAllianceTechs() {
		if (this->fleetUser->getAllianceId()!=0) {
			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
			mysqlpp::Query query = con->query();
			query << "SELECT "
				<< "	alliance_techlist_tech_id, "
				<< "	alliance_techlist_current_level "
				<< "FROM "
				<< "	alliance_techlist "
				<< "WHERE "
				<< "	alliance_techlist_alliance_id='" << this->fleetUser->getAllianceId() << "';";
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
							this->allianceWeapon = ((int)config.nget("alliance_tech_bonus",0) * (int)aRow["alliance_techlist_current_level"])
							+ (int)((int)config.nget("alliance_tech_bonus",1) * userCount);
						if ((int)aRow["alliance_techlist_tech_id"]==6)
							this->allianceShield = ((int)config.nget("alliance_tech_bonus",0) * (int)aRow["alliance_techlist_current_level"])
							+ (int)((int)config.nget("alliance_tech_bonus",1) * userCount);
						if ((int)aRow["alliance_techlist_tech_id"]==7)
							this->allianceStructure = ((int)config.nget("alliance_tech_bonus",0) * (int)aRow["alliance_techlist_current_level"])
							+ (int)((int)config.nget("alliance_tech_bonus",1) * userCount);
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
			

	double Fleet::getWeaponBonus() {
		double bonus = 1;
		if (specialObjects.size()) {
			std::vector<Object*>::iterator it;
			DataHandler &DataHandler = DataHandler::instance();
			for ( it=specialObjects.begin() ; it < specialObjects.end(); it++ ) {
				ShipData::ShipData *data = DataHandler.getShipById((*it)->getTypeId());
				bonus += (*it)->getSBonusWeapon() * data->getBonusWeapon();
			}
		}
		bonus += this->fleetUser->getTechBonus((unsigned int)Config::instance().idget("WEAPON_TECH_ID")) + this->allianceWeapon/100.0;
		return bonus;

	}

	double Fleet::getShieldBonus() {
		double bonus = 1;
		if (specialObjects.size()) {
			std::vector<Object*>::iterator it;
			DataHandler &DataHandler = DataHandler::instance();
			for ( it=specialObjects.begin() ; it < specialObjects.end(); it++ ) {
				ShipData::ShipData *data = DataHandler.getShipById((*it)->getTypeId());
				bonus += (*it)->getSBonusShield() * data->getBonusShield();
			}
		}
		bonus += this->fleetUser->getTechBonus((unsigned int)Config::instance().idget("SHIELD_TECH_ID")) + this->allianceShield/100.0;
		return bonus;
	}

	double Fleet::getStructureBonus() {
		double bonus = 1;
		if (specialObjects.size()) {
			std::vector<Object*>::iterator it;
			DataHandler &DataHandler = DataHandler::instance();
			for ( it=specialObjects.begin() ; it < specialObjects.end(); it++ ) {
				ShipData::ShipData *data = DataHandler.getShipById((*it)->getTypeId());
				bonus += (*it)->getSBonusStructure() * data->getBonusStructure();
			}
		}
		bonus += this->fleetUser->getTechBonus((unsigned int)Config::instance().idget("STRUCTURE_TECH_ID")) + this->allianceStructure/100.0;
		return bonus;
	}

	double Fleet::getHealBonus() {
		double bonus = 1;
		if (specialObjects.size()) {
			std::vector<Object*>::iterator it;
			DataHandler &DataHandler = DataHandler::instance();
			for ( it=specialObjects.begin() ; it < specialObjects.end(); it++ ) {
				ShipData::ShipData *data = DataHandler.getShipById((*it)->getTypeId());
				bonus += (*it)->getSBonusHeal() * data->getBonusHeal();
			}
		}
		bonus += this->fleetUser->getTechBonus((unsigned int)Config::instance().idget("REGENA_TECH_ID"));
		return bonus;
	}

	void Fleet::setAllianceWeapon(int weapon) {
		this->allianceWeapon = weapon;
		this->allianceTechsLoaded = true;
	}

	void Fleet::setAllianceStructure(int structure) {
		this->allianceStructure = structure;
		this->allianceTechsLoaded = true;
	}

	void Fleet::setAllianceShield(int shield) {
		this->allianceShield = shield;
		this->allianceTechsLoaded = true;
	}

	void Fleet::save() {
		int sum = 0;
		while (!objects.empty()) {
			Object* object = objects.back();
			sum += object->getCount();
			delete object;
			objects.pop_back();
		}

		while (!fleets.empty()) {
			Fleet* fleet = fleets.back();
			delete fleet;
			fleets.pop_back();
		}

		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		mysqlpp::Query query = con->query();

		if (sum>0 || !this->shipsLoaded) {
			query << "UPDATE "
				<< "	fleet "
				<< "SET "
				<< "	entity_from='" << this->getEntityFrom() << "', "
				<< "	entity_to='" << this->getEntityTo() << "', "
				<< "	next_id='" << this->getNextId() << "', "
				<< "	launchtime='" << this->getLaunchtime() << "', "
				<< "	landtime='" << this->getLandtime() << "', "
				<< "	nextactiontime='" << this->getNextactiontime() << "', "
				<< "	status='" << this->getStatus() << "', "
				<< "	pilots='" << this->getPilots() << "', "
				<< "	usage_fuel='" << this->usageFuel << "', "
				<< "	usage_food='" << this->usageFood << "', "
				<< "	usage_power='" << this->usagePower << "', "
				<< "	support_usage_fuel='" << this->supportUsageFuel << "', "
				<< "	support_usage_food='" << this->supportUsageFood << "', "
				<< "	res_metal='" << this->getResMetal() << "', "
				<< "	res_crystal='" << this->getResCrystal() << "', "
				<< "	res_plastic='" << this->getResPlastic() << "', "
				<< "	res_fuel='" << this->getResFuel() << "', "
				<< "	res_food='" << this->getResFood() << "', "
				<< "	res_power='" << this->getResPower() << "', "
				<< "	res_people='" << this->getResPeople() << "', "
				<< "	fetch_metal='0', "
				<< "	fetch_crystal='0', "
				<< "	fetch_plastic='0', "
				<< "	fetch_fuel='0', "
				<< "	fetch_food='0', "
				<< "	fetch_power='0', "
				<< "	fetch_people='0' "
				<< "WHERE "
				<< "	id='" << this->getId() << "' "
				<< "LIMIT 1;";
			RESULT_TYPE fsRes = query.store();
			query.reset();
		}
		else {
			query << "DELETE FROM "
				<< "	fleet "
				<< "WHERE "
				<< "	id='" << this->getId() << "' "
				<< "LIMIT 1;";
			query.store();
			query.reset();
		}
	}

	std::string Fleet::getLogResStart() {
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
						+ ",f,"
						+ etoa::d2s(this->fetchMetal)
						+ ":"
						+ etoa::d2s(this->fetchCrystal)
						+ ":"
						+ etoa::d2s(this->fetchPlastic)
						+ ":"
						+ etoa::d2s(this->fetchFuel)
						+ ":"
						+ etoa::d2s(this->fetchFood)
						+ ":"
						+ etoa::d2s(this->fetchPower)
						+ ":"
						+ etoa::d2s(this->fetchPeople);
		return log;
	}

	std::string Fleet::getLogResEnd() {
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
						+ ",f,0:0:0:0:0:0:0";
		return log;
	}

	std::string Fleet::getLogShipsStart() {
		return this->logFleetShipStart;
	}

	std::string Fleet::getLogShipsEnd() {
		if (this->shipsLoaded) {
			std::string log = "";
			std::vector<Object*>::iterator it;
			for (it=objects.begin() ; it < objects.end(); it++) {
				log += etoa::d2s((*it)->getTypeId())
					+ ":"
					+ etoa::d2s((*it)->getCount())
					+ ",";
			}
			return log;
		}
		else
			return "0";
	}
