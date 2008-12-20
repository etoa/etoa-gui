#include "Fleet.h"
#include "MysqlHandler.h"
#include "functions/Functions.h"

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
		
		this->capacity = 0;
		this->peopleCapacity = 0;
		this->actionCapacity = 0;
		
		this->actionAllowed = false;
		this->shipsLoaded = false;
		this->entityLoaded = false;
		this->shipsChanged = false;
		this->entityToUserId = 0;
	}
	
	int Fleet::getId() {
		return this->fId;
	}
	
	int Fleet::getUserId() {
		return this->userId;
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
	
	double Fleet::getPilots() {
		return this->pilots;
	}
	
	double Fleet::getResMetal() {
		return this->resMetal;
	}
	
	double Fleet::getResCrystal() {
		return this->resCrystal;
	}
	
	double Fleet::getResPlastic() {
		return this->resPlastic;
	}
	
	double Fleet::getResFuel() {
		return this->resFuel;
	}
	
	double Fleet::getResFood() {
		return this->resFood;
	}
	
	double Fleet::getResPower() {
		return this->resPower;
	}
	
	double Fleet::getResPeople() {
		return this->resPeople;
	}
	
	double Fleet::getResLoaded() {
		return this->getResMetal()
				+ this->getResCrystal()
				+ this->getResPlastic()
				+ this->getResFuel()
				+ this->getResFood();
	}
	
	int Fleet::getEntityToUserId() {
		if(!this->entityToUserId)
			this->entityToUserId = functions::getUserIdByPlanet(this->getEntityTo());
		return this->entityToUserId;
	}
	
	std::string Fleet::getEntityToUserString() {
		return functions::getUserNick(this->getEntityToUserId());
	}
	
	std::string Fleet::getActionString() {
		return this->getAction();
	}
	
	std::string Fleet::getLandtimeString() {
		return functions::formatTime(this->getLandtime());
	}
	
	std::string Fleet::getLaunchtimeString() {
		return  functions::formatTime(this->getLaunchtime());
	}
	
	std::string Fleet::getEntityToString(short type) {
		return functions::formatCoords(this->getEntityFrom(),type);
	}
	
	std::string Fleet::getEntityFromString(short type) {
		return functions::formatCoords(this->getEntityTo(),type);
	}
	
	bool Fleet::actionIsAllowed() {
		if (!this->shipsLoaded)
			this->loadShips();
		else if (this->shipsChanged)
			this->recalcShips();
		return this->actionAllowed;
	}
	
	void Fleet::loadShips() {
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		mysqlpp::Query query = con->query();
		query << "SELECT ";
		query << "	fs_ship_id, ";
		query << " fsShip_cnt ";
		query << "FROM ";
		query << "	fleet_ships ";
		query << "WHERE ";
		query << "	fs_fleet_id='" << this->getId() << "' ";
		query << "	AND fs_ship_faked='0';";
		mysqlpp::Result fsRes = query.store();
		query.reset();
			
		if (fsRes) {
			int fsSize = fsRes.size();
			
			if (fsSize>0) {
				objectData &objectData = objectData::instance();
				mysqlpp::Row fsRow;
				
				for (int i=0; i<fsSize; i++) {
					fsRow = fsRes.at(0);
					ObjectHandler::ObjectHandler object = objectData.get((int)fsRow["fs_ship_id"]);
					
					this->capacity += (int)fsRow["fs_ship_cnt"] * object.capacity();
					this->peopleCapacity += (int)fsRow["fs_ship_cnt"] * object.peopleCapacity();
					
					if (object.actions(this->action)) {
						this->actionAllowed = true;
						this->actionCapacity += (int)fsRow["fs_ship_cnt"] * object.capacity();
					}
				}
			}
		}
	}
	
	void Fleet::recalcShips() {
		this->actionCapacity = 0;
		this->capacity = 0;
		this->peopleCapacity = 0;

	}
	