
#include "Log.h"
	
	void Log::addFleetId(int fleetId) {
		this->fleetId = fleetId;
	}
	
	void Log::addFleetUserId(int userId) {
		this->fleetUserId = userId;
	}
	
	void Log::addEntityUserId(int userId) {
		this->entityUserId = userId;
	}
	
	void Log::addEntityToId(int entityId) {
		this->entityToId = entityId;
	}
	
	void Log::addEntityFromId(int entityId) {
		this->entityFromId = entityId;
	}
	
	void Log::addLaunchtime(int launchtime) {
		this->launchtime = launchtime;
	}
	
	void Log::addLandtime(int landtime) {
		this->landtime = landtime;
	}
	
	void Log::addAction(std::string action) {
		this->action = action;
	}
	
	void Log::addStatus(short status) {
		this->status = status;
	}
	
	void Log::addText(std::string text) {
		this->text += text + "\n";
	}
	
	void Log::addFleetResStart(std::string res) {
		if (res.length() > 1)
			this->fleetResStart = res;
	}
	
	void Log::addFleetResEnd(std::string res) {
		if (res.length() > 1)
			this->fleetResEnd = res;
	}
	
	void Log::addFleetShipsStart(std::string ships) {
		if (ships.length() > 1)
			this->fleetShipsStart = ships;
	}
	
	void Log::addFleetShipsEnd(std::string ships) {
		if (ships.length() > 1)
			this->fleetShipsEnd = ships;
	}
	
	void Log::addEntityResStart(std::string res) {
		if (res.length() > 1)
			this->entityResStart = res;
	}
	
	void Log::addEntityResEnd(std::string res) {
		if (res.length() > 1)
			this->entityResEnd = res;
	}
	
	void Log::addEntityShipsStart(std::string ships) {
		if (ships.length() > 1)
			this->entityShipsStart = ships;
	}
	
	void Log::addEntityShipsEnd(std::string ships) {
		if (ships.length() > 1)
			this->entityShipsEnd = ships;
	}
	
	void Log::save() {
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		mysqlpp::Query query = con_->query();
		
		query << "INSERT INTO "
			<< "	logs_fleet "
			<< "("
			<< " 	fleet_id, "
			<< " 	timestamp, "
			<< " 	message, "
			<< "	user_id, "
			<< " 	entity_user_id, "
			<< " 	entity_from, "
			<< " 	entity_to, "
			<< " 	launchtime, "
			<< " 	landtime, "
			<< " 	action, "
			<< " 	status, "
			<< " 	fleet_res_start, "
			<< " 	fleet_res_end, "
			<< " 	fleet_ships_start, "
			<< " 	fleet_ships_end, "
			<< "	entity_res_start, "
			<< " 	entity_res_end, "
			<< " 	entity_ships_start, "
			<< " 	entity_ships_end "
			<< ") "
			<< "VALUES "
			<< "(" << this->fleetId << ", "
			<< time(0) << ", "
			<< mysqlpp::quote << this->text << ", "
			<< this->fleetUserId << ", "
			<< this->entityUserId << ", "
			<< this->entityFromId << ", "
			<< this->entityToId << ", "
			<< this->launchtime << ", "
			<< this->landtime << ", "
			<< mysqlpp::quote << this->action << ", "
			<< mysqlpp::quote << this->status << ", "
			<< this->fleetResStart << ", "
			<< this->fleetResEnd << ", "
			<< this->fleetShipsStart << ", "
			<< this->fleetShipsEnd << ", "
			<< this->entityResStart << ", "
			<< this->entityResEnd << ", "
			<< this->entityShipsStart << ", "
			<< this->entityShipsEnd << " "
			<< ");";
		
		query.store();
		query.reset();
	}


