
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
	
	void Log::save() 
	{

		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		

		mysqlpp::Query query = con_->query();

	
		query << "INSERT INTO ";
		query << "	logs_fleet ";
		query << "(";
		query << " 	fleet_id, ";
		query << " 	timestamp, ";
		query << " 	message, ";
		query << "	user_id, ";
		query << " 	entity_user_id, ";
		query << " 	entity_from, ";
		query << " 	entity_to, ";
		query << " 	launchtime, ";
		query << " 	landtime, ";
		query << " 	action, ";
		query << " 	status, ";
		query << " 	fleet_res_start, ";
		query << " 	fleet_res_end, ";
		query << " 	fleet_ships_start, ";
		query << " 	fleet_ships_end, ";
		query << "	entity_res_start, ",
		query << " 	entity_res_end, ";
		query << " 	entity_ships_start, ";
		query << " 	entity_ships_end ";
		query << ") ";
		query << "VALUES ";
		query << "('" << this->fleetId << "', '";
		query << time(0) << "', '";
		query << this->text << "', '";
		query << this->fleetUserId << "', '";
		query << this->entityUserId << "', '";
		query << this->entityFromId << "', '";
		query << this->entityToId << "', '";
		query << this->launchtime << "', '";
		query << this->landtime << "', '";
		query << this->action << "', '";
		query << this->status << "', '";
		query << this->fleetResStart << "', '";
		query << this->fleetResEnd << "', '";
		query << this->fleetShipsStart << "', '";
		query << this->fleetShipsEnd << "', '";
		query << this->entityResStart << "', '";
		query << this->entityResEnd << "', '";
		query << this->entityShipsStart << "', '";
		query << this->entityShipsEnd << "' ";
		query << ");";


			query.store();
			query.reset();			
		
	}


