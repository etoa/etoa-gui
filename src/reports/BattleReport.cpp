
#include "BattleReport.h"

void BattleReport::setUser(std::string user) {
	this->user = user;
}
void BattleReport::setEntityUser(std::string user) {
	this->entityUser = user;
}

void BattleReport::setShips(std::string ships) {
	this->ships = ships;
}

void BattleReport::setEntityShips(std::string ships) {
	this->entityShips = ships;
}

void BattleReport::setEntityDef(std::string def) {
	this->entityDef = def;
}

void BattleReport::setShipsEnd(std::string ships) {
	this->shipsEnd = ships;
}

void BattleReport::setEntityShipsEnd(std::string ships) {
	this->entityShipsEnd = ships;
}

void BattleReport::setEntityDefEnd(std::string def) {
	this->entityDefEnd = def;
}

void BattleReport::setWeapon(double weapon) {
	this->weapon.push_back(weapon);
}

void BattleReport::setShield(double shield) {
	this->shield = shield;
}

void BattleReport::setStructure(double structure) {
	this->structure = structure;
}

void BattleReport::setHeal(double heal) {
	this->heal.push_back(heal);
}

void BattleReport::setCount(double count) {
	this->count.push_back(count);
}

void BattleReport::setExp(double exp) {
	this->exp = exp;
}

void BattleReport::setWeaponTech(short weaponTech) {
	this->weaponTech = weaponTech;
}

void BattleReport::setShieldTech(short shieldTech) {
	this->shieldTech = shieldTech;
}

void BattleReport::setStructureTech(short structureTech) {
	this->structureTech = structureTech;
}

void BattleReport::setEntityWeapon(double weapon) {
	this->entityWeapon.push_back(weapon);
}

void BattleReport::setEntityShield(double shield) {
	this->entityShield = shield;
}

void BattleReport::setEntityStructure(double structure) {
	this->entityStructure = structure;
}

void BattleReport::setEntityHeal(double heal) {
	this->entityHeal.push_back(heal);
}

void BattleReport::setEntityCount(double count) {
	this->entityCount.push_back(count);
}

void BattleReport::setEntityExp(double exp) {
	this->entityExp = exp;
}

void BattleReport::setEntityWeaponTech(short weaponTech) {
	this->entityWeaponTech = weaponTech;
}

void BattleReport::setEntityShieldTech(short shieldTech) {
	this->entityShieldTech = shieldTech;
}

void BattleReport::setEntityStructureTech(short structureTech) {
	this->entityStructureTech = structureTech;
}

void BattleReport::setRes(double res0,
						  double res1,
						  double res2,
						  double res3,
						  double res4,
						  double res5) {
	this->res0 = res0;
	this->res1 = res1;
	this->res2 = res2;
	this->res3 = res3;
	this->res4 = res4;
	this->res5 = res5;
}

void BattleReport::setWf(double wf0, double wf1, double wf2) {
	this->wf0 = wf0;
	this->wf1 = wf1;
	this->wf2 = wf2;
}

void BattleReport::setResult(short result) {
	this->result = result;
}

void BattleReport::setRounds(short rounds) {
	this->rounds = rounds;
}

void BattleReport::setRestore(short restore) {
	this->restore = restore;
}

void BattleReport::saveBattleReport() {
	My &my = My::instance();
	mysqlpp::Connection *con_ = my.get();
	
	mysqlpp::Query query = con_->query();
	
	try	{
		if (!this->id) throw 0;
		
		query << "INSERT INTO "
			<< "	`reports_other` "
			<< "( "
			<< "	`id`, "
			<< "	`subtype`, "
			<< "	`fleet_id`, "
			<< "	`user`, "
			<< "	`entity_user`, "
			<< "	`ships`, "
			<< "	`entity_ships`, "
			<< "	`entity-def`, "
			<< "	`weapon_tech`, "
			<< "	`shield_tech`, "
			<< "	`structure_tech`, ";
		for (int i=this->rounds; i>0; --i)
			query << "	`weapon_" << i << "`, ";
		query << "	`shield`, "
			<< "	`structure`, ";
		for (int i=this->rounds; i>0; --i)
			query << "	`heal_" << i << "`, ";
		for (int i=this->rounds; i>0; --i)
			query << "	`count_" << i << "`, ";
		query << "	`exp`, "
			<< "	`entity_weapon_tech`, "
			<< "	`entity_shield_tech`, "
			<< "	`entity_structure_tech`, ";
		for (int i=this->rounds; i>0; --i)
			query << "	`entity_weapon_" << i << "`, ";
		query << "	`entity_shield`, "
			<< "	`entity_structure`, ";
		for (int i=this->rounds; i>0; --i)
			query << "	`entity_heal_" << i << "`, ";
		for (int i=this->rounds; i>0; --i)
			query << "	`entity_count_" << i << "`, ";
		query << "	`entity_exp`, "
			<< "	`res_0`, "
			<< "	`res_1`, "
			<< "	`res_2`, "
			<< "	`res_3`, "
			<< "	`res_4`, "
			<< "	`res_5`, "
			<< "	`wf_0`, "
			<< "	`wf_1`, "
			<< "	`wf_2`, "
			<< "	`ships_end`, "
			<< "	`entity_ships_end`, "
			<< "	`entity_def_end`, "
			<< "	`restore`, "
			<< "	`result` "
			<< ") "
			<< "VALUES "
			<< "( "
			<< "	'" << this->id << "', "
			<< "	'" << this->subtype << "', "
			<< "	'" << this->fleetId << "', "
			<< "	'" << this->user << "', "
			<< "	'" << this->entityUser << "', "
			<< "	'" << this->ships << "', "
			<< "	'" << this->entityShips << "', "
			<< "	'" << this->entityDef << "', "
			<< "	'" << this->weaponTech << "', "
			<< "	'" << this->shieldTech << "', "
			<< "	'" << this->structureTech << "', ";
		while (!this->weapon.empty()) {
			query << "	'" << this->weapon.back() << "', ";
			this->weapon.pop_back();
		}
		query << "	'" << this->shield << "', "
			<< "	'" << this->structure << "', ";
		while (!this->heal.empty()) {
			query << "	'" << this->heal.back() << "', ";
			this->heal.pop_back();
		}
		while (!this->count.empty()) {
			query << "	'" << this->count.back() << "', ";
			this->count.pop_back();
		}
		query << "	'" << this->exp << "', "
			<< "	'" << this->entityWeaponTech << "', "
			<< "	'" << this->entityShieldTech << "', "
			<< "	'" << this->entityStructureTech << "', ";
		while (!this->entityWeapon.empty()) {
			query << "	'" << this->entityWeapon.back() << "', ";
			this->entityWeapon.pop_back();
		}
		query << "	'" << this->entityShield << "', "
			<< "	'" << this->entityStructure << "', ";
		while (!this->entityHeal.empty()) {
			query << "	'" << this->entityHeal.back() << "', ";
			this->entityHeal.pop_back();
		}
		while (!this->entityCount.empty()) {
			query << "	'" << this->entityCount.back() << "', ";
			this->entityCount.pop_back();
		}
		query << "	'" << this->entityExp << "', "
			<< "	'" << this->res0 << "', "
			<< "	'" << this->res1 << "', "
			<< "	'" << this->res2 << "', "
			<< "	'" << this->res3 << "', "
			<< "	'" << this->res4 << "', "
			<< "	'" << this->res5 << "', "
			<< "	'" << this->wf0 << "', "
			<< "	'" << this->wf1 << "', "
			<< "	'" << this->wf2 << "', "
			<< "	'" << this->shipsEnd << "', "
			<< "	'" << this->entityShipsEnd << "', "
			<< "	'" << this->entityDefEnd << "', "
			<< "	'" << this->restore << "', "
			<< "	'" << this->result << "' "
			<< ");";
		query.store();
		query.reset();
	}
	catch (int e)
	{
		std::cout << "BattleReport failed no Id given!" << std::endl;
	}
	catch (mysqlpp::Exception* e)
	{
		std::cout << e->what() << std::endl;
		std::cout << query.str() << std::endl;
		query.reset();
	}
}