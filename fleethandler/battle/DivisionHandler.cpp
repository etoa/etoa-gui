#include <vector>
#include <math.h>
#include "../config/ConfigHandler.h"
#include "../functions/Functions.h"
#include "DivisionHandler.h"
	
	void DivisionHandler::initValues()
	{
		this->initCount = 0;
		this->initWeapon = 0;

		std::vector< ObjectHandler>::iterator it;
		std::map< int,UserHandler>::iterator at;
		std::map< int,ShowObjectHandler >::iterator ot;

		for ( it = objects.begin() ; it < objects.end(); it++ ) {
			at = this->users.find(it->userId);
			if (it->type==0) {
				ot = this->showObjectsDef.find(it->oId);
			}
			else {
					ot = this->showObjectsShip.find(it->oId);
			}
			if ((*ot).second.special==1) {
				//bonus von spezialschiffe dazu rechnen
				(*at).second.weaponTech += it->shipsBonusWeapon * (*ot).second.bonusWeapon;
				(*at).second.structureTech += it->shipsBonusStructure * (*ot).second.bonusStructure;
				(*at).second.shieldTech += it->shipsBonusShield * (*ot).second.bonusShield;
				(*at).second.healTech += it->shipsBonusHeal * (*ot).second.bonusHeal;
			}
			(*at).second.structure += it->cnt * (*ot).second.structure;
			(*at).second.shield += it->cnt * (*ot).second.shield;
			(*at).second.weapon += it->cnt * (*ot).second.weapon;
			(*at).second.count += it->cnt;
			(*ot).second.cnt += it->cnt;
		}
		
		for ( at = users.begin() ; at != users.end(); at++ ) {
			this->weapon = (*at).second.weapon;
			this->structure = (*at).second.structure;
			this->shield = (*at).second.shield;
			(*at).second.weapon *= (*at).second.weaponTech;
			(*at).second.structure *= (*at).second.structureTech;
			(*at).second.shield *= (*at).second.shieldTech;
			this->initCount += (*at).second.count;
			this->initWeapon += (*at).second.weapon;
			this->initStructure += (*at).second.structure;
			this->initShield += (*at).second.shield;
			this->allianceId = (*at).second.allianceId;
		}
	}
	
	void DivisionHandler::updateValues()
	{
		this->cCount = 0;
		this->cWeapon = 0;
		this->cHealPoints = 0;
		this->cHealCount = 0;
		
		this->percentage = this->cStructureShield / this->initStructureShield;
		
		std::vector< ObjectHandler>::iterator it;
		std::map< int,UserHandler>::iterator at;
		std::map< int,ShowObjectHandler >::iterator ot;
		for ( it = objects.begin() ; it < objects.end(); it++ ) {
			at = this->users.find(it->userId);
			if (it->type==0) {
				ot = this->showObjectsDef.find(it->oId);
			}
			else {
				ot = this->showObjectsShip.find(it->oId);
			}
			it->newCnt = ceil(this->percentage * it->cnt);
			if (it->newCnt > it->cnt) it->newCnt = it->cnt;
			this->cCount += it->newCnt;
			this->cWeapon += it->newCnt * (*ot).second.weapon * (*at).second.weaponTech;
			
			if ((*ot).second.heal > 0) {
				this->cHealCount += it->newCnt;
				this->cHealPoints += ceil((*ot).second.heal * (*at).second.healTech * it->newCnt);
			}
			
		}
	}
	
	void DivisionHandler::updateValuesEnd(std::vector<double> &wf)
	{
		Config &config = Config::instance();
		this->percentage = this->cStructureShield / this->initStructureShield;
		
		std::vector< ObjectHandler >::iterator it;
		std::map< int,ShowObjectHandler >::iterator ot;
		
		for ( ot = showObjectsShip.begin() ; ot != showObjectsShip.end(); ot++ ) {
			(*ot).second.cnt = 0;
		}
		for ( ot = showObjectsDef.begin() ; ot != showObjectsDef.end(); ot++ ) {
			(*ot).second.cnt = 0;
		}
		
		
		for ( it = objects.begin() ; it < objects.end(); it++ ) {
			if (it->type==0) {
				ot = this->showObjectsDef.find(it->oId);
			}
			else {
				ot = this->showObjectsShip.find(it->oId);
			}
			it->newCnt = ceil(this->percentage * it->cnt);
			
			if (it->newCnt * ((*ot).second.structure + (*ot).second.shield) <= 0) {
				it->newCnt = 0;
			}
			else if (it->newCnt > it->cnt) {
				it->newCnt = it->cnt;
			}
			
			(*ot).second.cnt += it->newCnt;
			
			if (it->type==0) {
				it->repairCnt = floor((it->cnt - it->newCnt) * config.nget("def_restore_percent",0));
				(*ot).second.repairCnt += it->repairCnt;
				it->newCnt += it->repairCnt;
			}
			
			this->loseFleet[0] += floor((it->cnt - it->newCnt) * (*ot).second.metal);
			this->loseFleet[1] += floor((it->cnt - it->newCnt) * (*ot).second.crystal);
			this->loseFleet[2] += floor((it->cnt - it->newCnt) * (*ot).second.plastic);
			this->loseFleet[3] += floor((it->cnt - it->newCnt) * (*ot).second.fuel);
			this->loseFleet[4] += floor((it->cnt - it->newCnt) * (*ot).second.food);
			
			wf[0] += floor((it->cnt - it->newCnt) * config.nget("ship_wf_percent",0) * (*ot).second.metal);
			wf[1] += floor((it->cnt - it->newCnt) * config.nget("ship_wf_percent",0) * (*ot).second.crystal);
			wf[2] += floor((it->cnt - it->newCnt) * config.nget("ship_wf_percent",0) * (*ot).second.plastic);
		}
	}
	

	void DivisionHandler::loadDefense(int entityId)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		Config &config = Config::instance();
		
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	deflist_planet_id AS planet_id, ";
		query << "	deflist_user_id AS user_id, ";
		query << "	deflist_def_id AS id, ";
		query << "	deflist_count AS cnt ";
		query << "FROM ";
		query << "	deflist ";
		query << "WHERE ";
		query << "	deflist_planet_id='" << entityId << "' ";
		query << "	AND deflist_count>'0';";
		mysqlpp::Result objectsRes = query.store();
		query.reset();
		
		if (objectsRes) {
			int objectsSize = objectsRes.size();
			
			if (objectsSize > 0) {
				mysqlpp::Row objectsRow;
				
				for (mysqlpp::Row::size_type i = 0; i<objectsSize; i++) {
					objectsRow = objectsRes.at(i);
					
					ObjectHandler object(objectsRow,0);
					this->objects.push_back(object);
					
					if (!users.count((int)objectsRow["user_id"])) {
						UserHandler user((int)objectsRow["user_id"]);
						this->users.insert ( std::pair<int,UserHandler> ( (int)objectsRow["user_id"],user) );
					}
				}
			}
		}
	}
	
	void DivisionHandler::loadShips(int entityId)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		Config &config = Config::instance();
		
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	shiplist_planet_id AS planet_id, ";
		query << "	shiplist_user_id AS user_id, ";
		query << "	shiplist_ship_id AS id, ";
		query << "	shiplist_count AS cnt, ";
		query << "	shiplist_special_ship_level AS special_ship_level, ";
		query << "	shiplist_special_ship_exp AS special_ship_exp, ";
		query << "	shiplist_special_ship_bonus_weapon AS special_ship_bonus_weapon, ";
		query << "	shiplist_special_ship_bonus_structure AS special_ship_bonus_structure, ";
		query << "	shiplist_special_ship_bonus_shield AS special_ship_bonus_shield, ";
		query << "	shiplist_special_ship_bonus_heal AS special_ship_bonus_heal, ";
		query << "	shiplist_special_ship_bonus_capacity AS special_ship_bonus_capacity, ";
		query << "	shiplist_special_ship_bonus_speed AS special_ship_bonus_speed, ";
		query << "	shiplist_special_ship_bonus_pilots AS special_ship_bonus_pilots, ";
		query << "	shiplist_special_ship_bonus_tarn AS special_ship_bonus_tarn, ";
		query << "	shiplist_special_ship_bonus_antrax AS special_ship_bonus_antrax, ";
		query << "	shiplist_special_ship_bonus_forsteal AS special_ship_bonus_forsteal, ";
		query << "	shiplist_special_ship_bonus_build_destroy AS special_ship_bonus_build_destroy, ";
		query << "	shiplist_special_ship_bonus_antrax_food AS special_ship_bonus_antrax_food, ";
		query << "	shiplist_special_ship_bonus_deactivade AS special_ship_bonus_deactivade ";
		query << "FROM ";
		query << "	shiplist ";
		query << "WHERE ";
		query << "	shiplist_planet_id='" << entityId << "' ";
		query << "	AND shiplist_count>'0';";
		mysqlpp::Result objectsRes = query.store();
		query.reset();
		
		if (objectsRes) {
			int objectsSize = objectsRes.size();
			
			if (objectsSize > 0) {
				mysqlpp::Row objectsRow;
				
				for (mysqlpp::Row::size_type i = 0; i<objectsSize; i++) {
					objectsRow = objectsRes.at(i);
					
					ObjectHandler object(objectsRow,2);
					this->objects.push_back(object);
					
					if (!users.count((int)objectsRow["user_id"])) {
						UserHandler user((int)objectsRow["user_id"]);
						this->users.insert ( std::pair<int,UserHandler> ( (int)objectsRow["user_id"],user) );
					}
				}
			}
		}
	}
	
	void DivisionHandler::loadSupport(int entityId)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		Config &config = Config::instance();
		
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	id AS fleet_id, ";
		query << "	user_id, ";
		query << "	fs_ship_id AS id, ";
		query << "	fs_ship_cnt AS cnt, ";
		query << "	fs_special_ship_level AS special_ship_level, ";
		query << "	fs_special_ship_exp AS special_ship_exp, ";
		query << "	fs_special_ship_bonus_weapon AS special_ship_bonus_weapon, ";
		query << "	fs_special_ship_bonus_structure AS special_ship_bonus_structure, ";
		query << "	fs_special_ship_bonus_shield AS special_ship_bonus_shield, ";
		query << "	fs_special_ship_bonus_heal AS special_ship_bonus_heal, ";
		query << "	fs_special_ship_bonus_capacity AS special_ship_bonus_capacity, ";
		query << "	fs_special_ship_bonus_speed AS special_ship_bonus_speed, ";
		query << "	fs_special_ship_bonus_pilots AS special_ship_bonus_pilots, ";
		query << "	fs_special_ship_bonus_tarn AS special_ship_bonus_tarn, ";
		query << "	fs_special_ship_bonus_antrax AS special_ship_bonus_antrax, ";
		query << "	fs_special_ship_bonus_forsteal AS special_ship_bonus_forsteal, ";
		query << "	fs_special_ship_bonus_build_destroy AS special_ship_bonus_build_destroy, ";
		query << "	fs_special_ship_bonus_antrax_food AS special_ship_bonus_antrax_food, ";
		query << "	fs_special_ship_bonus_deactivade AS special_ship_bonus_deactivade ";
		query << "FROM ";
		query << "	fleet ";
		query << "INNER JOIN ";
		query << "	fleet_ships ";
		query << "ON ";
		query << "	action='support' ";
		query << "	AND status='3' ";
		query << "	AND entity_to='" << entityId << "' ";
		query << "	AND fs_ship_cnt>'0';";
		mysqlpp::Result objectsRes = query.store();
		query.reset();
		
		if (objectsRes) {
			int objectsSize = objectsRes.size();
			
			if (objectsSize > 0) {
				mysqlpp::Row objectsRow;
				
				for (mysqlpp::Row::size_type i = 0; i<objectsSize; i++) {
					objectsRow = objectsRes.at(i);
					
					ObjectHandler object(objectsRow,1);
					this->objects.push_back(object);
					
					if (!users.count((int)objectsRow["user_id"])) {
						UserHandler user((int)objectsRow["user_id"]);
						this->users.insert ( std::pair<int,UserHandler> ( (int)objectsRow["user_id"],user) );
					}
				}
			}
		}
	}
	
	void DivisionHandler::loadFleet(int fId)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		Config &config = Config::instance();

		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	id AS fleet_id, ";
		query << "	user_id, ";
		query << "	fs_ship_id AS id, ";
		query << "	fs_ship_cnt AS cnt, ";
		query << "	fs_special_ship_level AS special_ship_level, ";
		query << "	fs_special_ship_exp AS special_ship_exp, ";
		query << "	fs_special_ship_bonus_weapon AS special_ship_bonus_weapon, ";
		query << "	fs_special_ship_bonus_structure AS special_ship_bonus_structure, ";
		query << "	fs_special_ship_bonus_shield AS special_ship_bonus_shield, ";
		query << "	fs_special_ship_bonus_heal AS special_ship_bonus_heal, ";
		query << "	fs_special_ship_bonus_capacity AS special_ship_bonus_capacity, ";
		query << "	fs_special_ship_bonus_speed AS special_ship_bonus_speed, ";
		query << "	fs_special_ship_bonus_pilots AS special_ship_bonus_pilots, ";
		query << "	fs_special_ship_bonus_tarn AS special_ship_bonus_tarn, ";
		query << "	fs_special_ship_bonus_antrax AS special_ship_bonus_antrax, ";
		query << "	fs_special_ship_bonus_forsteal AS special_ship_bonus_forsteal, ";
		query << "	fs_special_ship_bonus_build_destroy AS special_ship_bonus_build_destroy, ";
		query << "	fs_special_ship_bonus_antrax_food AS special_ship_bonus_antrax_food, ";
		query << "	fs_special_ship_bonus_deactivade AS special_ship_bonus_deactivade ";
		query << "FROM ";
		query << "	fleet ";
		query << "INNER JOIN ";
		query << "	fleet_ships ";
		query << "ON ";
		query << "	(leader_id='" << fId << "' ";
		query << "	OR id='" << fId << "') ";
		query << "	AND fs_ship_cnt>'0'; ";
		mysqlpp::Result objectsRes = query.store();
		query.reset();

		if (objectsRes) {
			int objectsSize = objectsRes.size();
			
			if (objectsSize > 0) {
				mysqlpp::Row objectsRow;
				
				for (mysqlpp::Row::size_type i = 0; i<objectsSize; i++) {
					objectsRow = objectsRes.at(i);
					
					ObjectHandler object(objectsRow,1);
					this->objects.push_back(object);

					if (!users.count((int)objectsRow["user_id"])) {
						UserHandler user((int)objectsRow["user_id"]);
						this->users.insert ( std::pair<int,UserHandler> ( (int)objectsRow["user_id"],user) );
					}
				}
			}
		}
	}
	
	void DivisionHandler::loadShipValues()
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		std::string ids = getIds(1);
		
		if (ids != "") {
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "	ship_id AS id, ";
			query << "	ship_name AS name, ";
			query << "	ship_structure AS structure, ";
			query << "	ship_shield AS shield, ";
			query << "	ship_weapon AS weapon, ";
			query << "	ship_heal AS heal, ";
			query << "	ship_costs_metal AS costs_metal, ";
			query << "	ship_costs_crystal AS costs_crystal, ";
			query << "	ship_costs_plastic AS costs_plastic, ";
			query << "	ship_costs_fuel AS costs_fuel, ";
			query << "	ship_costs_food AS costs_food, ";
			query << "	ship_capacity, ";
			query << "	special_ship, ";
			query << "	special_ship_need_exp, ";
			query << "	special_ship_exp_factor, ";
			query << "	special_ship_bonus_weapon, ";
			query << "	special_ship_bonus_structure, ";
			query << "	special_ship_bonus_shield, ";
			query << "	special_ship_bonus_heal, ";
			query << "	special_ship_bonus_capacity ";
			query << "FROM ";
			query << "	ships ";
			query << "WHERE ";
			query << "	ship_id IN (" << ids << ") ";
			query << "ORDER BY ";
			query << "	special_ship DESC, ";
			query << "	ship_name;";
			mysqlpp::Result res = query.store();
			query.reset();
		
			if (res) {
				int resSize = res.size();
			
				if (resSize > 0) {
					mysqlpp::Row objectsRow;
					for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
						objectsRow = res.at(i);
						ShowObjectHandler object(objectsRow,1);
						this->showObjectsShip.insert ( std::pair<int,ShowObjectHandler> ( (int)objectsRow["id"],object) );
					}
				}
			}
		}
	}
	
	void DivisionHandler::loadDefValues()
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		std::string ids = getIds(0);
		
		if (ids !="") {
			mysqlpp::Query query = con_->query();
			query << "SELECT ";
			query << "	def_id AS id, ";
			query << "	def_name AS name, ";
			query << "	def_structure AS structure, ";
			query << "	def_shield AS shield, ";
			query << "	def_weapon AS weapon, ";
			query << "	def_heal AS heal, ";
			query << "	def_costs_metal AS costs_metal, ";
			query << "	def_costs_crystal AS costs_crystal, ";
			query << "	def_costs_plastic AS costs_plastic, ";
			query << "	def_costs_fuel AS costs_fuel, ";
			query << "	def_costs_food AS costs_food ";
			query << "FROM ";
			query << "	defense ";
			query << "WHERE ";
			query << "	def_id IN (" << ids << ") ;";
			mysqlpp::Result res = query.store();
			query.reset();
		
			if (res) {
				int resSize = res.size();
			
				if (resSize > 0) {
					mysqlpp::Row objectsRow;
				
					for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
						objectsRow = res.at(i);
						ShowObjectHandler object(objectsRow,0);
						this->showObjectsDef.insert ( std::pair<int,ShowObjectHandler> ( (int)objectsRow["id"],object) );
					}
				}
			}
		}
	}
	
	std::string DivisionHandler::getIds(short type)
	{
		std::string id;
		std::vector< ObjectHandler>::iterator it;
		bool done = 0;

		for ( it = objects.begin() ; it < objects.end(); it++ ) {
			if (it->type == type) {
				if (done==1) {
					id += ",";
				}
				id += "'";
				id += functions::d2s(it->oId);
				id += "'";
				done = 1;
			}
		}
		return id;
	}

	std::string DivisionHandler::getNicks()
	{
		bool done = 0;
		std::string nicks;
		std::map< int,UserHandler>::iterator at;
		for ( at = users.begin() ; at != users.end(); at++ ) {
			nicks += (*at).second.userNick;
			if (done) {
				nicks += ", ";
			}
			done = 1;
		}
		return nicks;
	}
	
	std::string DivisionHandler::getObjects(short type, bool repair)
	{
		std::string objects;
		std::map< int,ShowObjectHandler >::iterator it;
		
		if (type==0) {
			for ( it = showObjectsDef.begin() ; it != showObjectsDef.end(); it++ ) {
				objects += (*it).second.name;
				objects += ": ";
				objects += functions::d2s((*it).second.cnt);
				if (repair) {
					objects += " (+";
					objects += functions::d2s((*it).second.repairCnt);
					objects += ")";
				}
				objects += "\n";
			}
		}
		else {
			for ( it = showObjectsShip.begin() ; it != showObjectsShip.end(); it++ ) {
				objects += (*it).second.name;
				objects += ": ";
				objects += functions::d2s((*it).second.cnt);
				objects += "\n";
			}
		}
		if (objects=="") objects = "[i]Nichts vorhanden![/i]\n";
		objects += "\n";
		return objects;
	}
	
	bool DivisionHandler::saveObjects()
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		mysqlpp::Query query = con_->query();

		std::vector < ObjectHandler >::iterator it;
		std::map< int,ShowObjectHandler >::iterator ot;
		std::map< int,UserHandler>::iterator at;
		std::map< int,double >::iterator ft;
		bool special;
		
		for ( it = objects.begin() ; it < objects.end(); it++ ) {
			if (it->type==1 && it->fleetId!=0) {
				ot = this->showObjectsShip.find(it->oId);
				at = this->users.find(it->userId);
			
				if (it->newCnt > 0 && (*ot).second.special) {
					it->shipExp += this->newExpInit;
					(*at).second.specialShipBonusCapacity += (*ot).second.bonusCapacity * it->shipsBonusCapacity;
					special = true;
					query << "INSERT INTO ";
					query << "	fleet_ships ";
					query << "(";
					query << "	fs_fleet_id, ";
					query << "	fs_ship_id, ";
					query << "	fs_ship_cnt, ";
					query << "	fs_special_ship, ";
					query << "	fs_special_ship_level, ";
					query << "	fs_special_ship_exp, ";
					query << "	fs_special_ship_bonus_weapon, ";
					query << "	fs_special_ship_bonus_structure, ";
					query << "	fs_special_ship_bonus_shield, ";
					query << "	fs_special_ship_bonus_heal, ";
					query << "	fs_special_ship_bonus_capacity, ";
					query << "	fs_special_ship_bonus_speed, ";
					query << "	fs_special_ship_bonus_pilots, ";
					query << "	fs_special_ship_bonus_tarn, ";
					query << "	fs_special_ship_bonus_antrax, ";
					query << "	fs_special_ship_bonus_forsteal, ";
					query << "	fs_special_ship_bonus_build_destroy, ";
					query << "	fs_special_ship_bonus_antrax_food, ";
					query << "	fs_special_ship_bonus_deactivade ";
					query << ") ";
					query << "VALUES ";
					query << "(";
					query << "	'" << it->fleetId << "', ";
					query << "	'" << it->oId << "', ";
					query << "	'" << it->newCnt << "', ";
					query << "	'1', ";
					query << "	'" << it->shipLevel << "', ";
					query << "	'" << it->shipExp << "', ";
					query << "	'" << it->shipsBonusWeapon << "', ";
					query << "	'" << it->shipsBonusStructure << "', ";
					query << "	'" << it->shipsBonusShield << "', ";
					query << "	'" << it->shipsBonusHeal << "', ";
					query << "	'" << it->shipsBonusCapacity << "', ";
					query << "	'" << it->shipsBonusSpeed << "', ";
					query << "	'" << it->shipsBonusPilots << "', ";
					query << "	'" << it->shipsBonusTarn << "', ";
					query << "	'" << it->shipsBonusAntrax << "', ";
					query << "	'" << it->shipsBonusForsteal << "',";
					query << "	'" << it->shipsBonusDestroy << "', ";
					query << "	'" << it->shipsBonusAntraxFood << "', ";
					query << "	'" << it->shipsBonusDeactivade << "' ";
					query << ");";
					query.store();
					query.reset();
				}
				else if (it->newCnt > 0) {
					query << "INSERT INTO ";
					query << "	fleet_ships ";
					query << "(";
					query << "	fs_fleet_id, ";
					query << "	fs_ship_id, ";
					query << "	fs_ship_cnt ";
					query << ")";
					query << "VALUES ";
					query << " (";
					query << "	'" << it->fleetId << "', ";
					query << "	'" << it->oId << "', ";
					query << "	'" << it->newCnt << "' ";
					query << ");";
					query.store();
					query.reset();
				}

				//Kapazität der überlebenden Schiffe rechnen
				(*at).second.fleetCapa[it->fleetId] += ((*ot).second.capacity * it->newCnt);
				(*at).second.capa += (*ot).second.capacity * it->newCnt;
			}
			else if (it->type==1 && it->planetId!=0) {
				ot = this->showObjectsShip.find(it->oId);
				it->shipExp += this->newExpInit;
				if (it->newCnt > 0) {
					query << "SELECT ";
					query << "	shiplist_ship_id ";
					query << "FROM ";
					query << "	shiplist ";
					query << "WHERE ";
					query << "	shiplist_planet_id='" << it->planetId << "' ";
					query << "	AND shiplist_ship_id='" << it->oId << "';";
					mysqlpp::Result slRes = query.store();
					query.reset();
				
					if (slRes) {
						int slSize = slRes.size();
						
						if (slSize > 0) {
							mysqlpp::Row slRow = slRes.at(0);
							query << "UPDATE ";
							query << "	shiplist ";
							query << "SET ";
							query << "	shiplist_count='" << it->newCnt << "' ";
							if ((*ot).second.special) query << ",	shiplist_special_ship_exp='" << it->shipExp << "' ";
							query << "WHERE ";
							query << "	shiplist_planet_id='" << it->planetId << "' ";
							query << "	AND shiplist_ship_id='" << it->oId << "';";
							query.store();
							query.reset();
						}
						else {
							query << "INSERT INTO ";
							query << "	shiplist ";
							query << "(";
							query << "	shiplist_user_id, ";
							query << "	shiplist_planet_id, ";
							query << "	shiplist_ship_id, ";
							query << "	shiplist_count ";
							if ((*ot).second.special) query << ",	shiplist_special_ship_exp ";
							query << ") ";
							query << "VALUES ";
							query << "( ";
							query << "	'" << it->userId << "', ";
							query << "	'" << it->planetId << "', ";
							query << "	'" << it->oId << "', ";
							query << "	'" << it->newCnt << "' ";
							if ((*ot).second.special) query << ", '" << it->shipExp << "' ";
							query << ");";
							query.store();
							query.reset();
						}
					}
				}
				else if ((*ot).second.special) {
					special = true;
					query << "UPDATE ";
					query << "	shiplist ";
					query << "SET ";
					query << "	shiplist_special_ship_level='0', ";
					query << "	shiplist_special_ship_exp='0', ";
					query << "	shiplist_special_ship_bonus_weapon='0', ";
					query << "	shiplist_special_ship_bonus_structure='0', ";
					query << "	shiplist_special_ship_bonus_shield='0', ";
					query << "	shiplist_special_ship_bonus_heal='0', ";
					query << "	shiplist_special_ship_bonus_capacity='0', ";
					query << "	shiplist_special_ship_bonus_speed='0', ";
					query << "	shiplist_special_ship_bonus_pilots='0', ";
					query << "	shiplist_special_ship_bonus_tarn='0', ";
					query << "	shiplist_special_ship_bonus_antrax='0', ";
					query << "	shiplist_special_ship_bonus_forsteal='0', ";
					query << "	shiplist_special_ship_bonus_build_destroy='0', ";
					query << "	shiplist_special_ship_bonus_antrax_food='0', ";
					query << "	shiplist_special_ship_bonus_deactivade='0' ";
					query << "WHERE ";
					query << "	shiplist_user_id='" << it->userId << "' ";
					query << "	AND shiplist_ship_id='" << it->oId << "';";
					query.store(),
					query.reset();
				}
			}
			else {
				if (it->newCnt > 0) {
					query << "SELECT ";
					query << "	deflist_def_id ";
					query << "FROM ";
					query << " deflist ";
					query << "WHERE ";
					query << "	deflist_planet_id='" << it->planetId << "' ";
					query << "	AND deflist_def_id='" << it->oId << "';";
					mysqlpp::Result dlRes = query.store();
					query.reset();
			
					if (dlRes) {
						int dlSize = dlRes.size();
				
						if (dlSize > 0) {
							query << "UPDATE ";
							query << "	deflist ";
							query << "SET ";
							query << "	deflist_count='" << it->newCnt << "' ";
							query << "WHERE ";
							query << "	deflist_planet_id='" << it->planetId << "' ";
							query << "	AND deflist_def_id='" << it->oId << "';";
							query.store();
							query.reset();
						}
						else {
							query << "INSERT INTO ";
							query << "	deflist ";
							query << "(";
							query << "	deflist_user_id, ";
							query << "	deflist_planet_id, ";
							query << "	deflist_def_id, ";
							query << "	deflist_count ";
							query << ")";
							query << "VALUES ";
							query << "(";
							query << "	'" << it->userId << "', ";
							query << "	'" << it->planetId << "', ";
							query << "	'" << it->oId << "', ";
							query << "	'" << it->newCnt << "'";
							query << ");";
							query.store();
							query.reset();
						}
					}
				}
			}
		}
		
		for ( at = users.begin() ; at != users.end(); at++ ) {
			(*at).second.capa *= (*at).second.specialShipBonusCapacity;
			this->capa += (*at).second.capa;
			
			for (ft = (*at).second.fleetCapa.begin(); ft != (*at).second.fleetCapa.end(); ft++) {
				(*ft).second *= (*at).second.specialShipBonusCapacity;
			}
		}

		return special;
	}
