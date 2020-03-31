
#ifndef __BATTLEREPORT__
#define __BATTLEREPORT__

#include <string>
#include <vector>
#include <iostream>
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "Report.h"

/**
* BattleReport class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/


class BattleReport	: public Report
{
public:
	BattleReport(int userId=0, int opponent1Id=0, int entity1Id=0, int entity2Id=0, int timestamp=0, int fleetId=0) : Report() {
		this->type = "battle";
		this->ships = "";
		this->entityShips = "";
		this->entityDef = "";
		this->shipsEnd = "";
		this->entityShipsEnd = "";
		this->entityDefEnd = "";
		
		this->res0 = 0;
		this->res1 = 0;
		this->res2 = 0;
		this->res3 = 0;
		this->res4 = 0;
		this->res5 = 0;
		
		this->wf0 = 0;
		this->wf1 = 0;
		this->wf2 = 0;
		
		this->result = 0;
		this->rounds = 0;
		this->restore = 0;
		this->restoreCivilShips = 0;
		
		this->fleetId = fleetId;
		this->timestamp = timestamp;
		this->entity1Id = entity1Id;
		this->entity2Id = entity2Id;
		this->opponent1Id = opponent1Id;
		this->addUser(userId);
	}

	BattleReport(BattleReport* report) {	}
		
	~BattleReport() {
		while (!this->users.empty()) {
                        // id saves the auto_increment value of the generated
                        // row in the Report::save() query
			this->id = this->save(this->users.back());
                        // that id is used as `reports_battle`.`id`
                        // in the saveBattleReport() query
			this->saveBattleReport();
			this->users.pop_back();
		}
		
	}
	
	void setUser(std::string user);
	void setEntityUser(std::string user);
	void setShips(std::string ships);
	void setEntityShips(std::string ships);
	void setEntityDef(std::string def);
	void setShipsEnd(std::string shipsEnd);
	void setEntityShipsEnd(std::string shipsEnd);
	void setEntityDefEnd(std::string defEnd);
	
	void setWeapon(double weapon);
	void setShield(double shield);
	void setStructure(double structure);
	void setHeal(double heal);
	void setCount(double count);
	void setExp(int exp);
	void setWeaponTech(short weaponTech);
	void setShieldTech(short shieldTech);
	void setStructureTech(short structureTech);
	
	void setEntityWeapon(double weapon);
	void setEntityShield(double shield);
	void setEntityStructure(double structure);
	void setEntityHeal(double heal);
	void setEntityCount(double count);
	void setEntityExp(int exp);
	void setEntityWeaponTech(short weaponTech);
	void setEntityShieldTech(short shieldTech);
	void setEntityStructureTech(short structureTech);
	
	void setRes(double res0=0,
				double res1=0,
				double res2=0,
				double res3=0,
				double res4=0,
				double res5=0);
	
	void setWf(double wf0, double wf1, double wf2);
	
	void setResult(short result);
	void setRounds(short rounds);
	void setRestore(short restore);
	void setRestoreCivilShips(short restoreCivilShips);
	
	void saveBattleReport();
	
private:
	std::string user,entityUser; //userId-String separated with ,'s
	std::string ships, entityShips, entityDef;
	std::string shipsEnd, entityShipsEnd, entityDefEnd;
	
	std::vector<double> weapon, heal, count, entityWeapon, entityHeal, entityCount;
	double shield, structure, entityShield, entityStructure;
	double exp, entityExp; //cant be unsigned because it will be -1 if there is no special ship in the fleet
	unsigned short weaponTech, shieldTech, structureTech, entityWeaponTech, entityShieldTech, entityStructureTech;
	
	double res0, res1, res2, res3, res4, res5;
	double wf0, wf1, wf2;
	
	unsigned int fleetId;
	unsigned short result, rounds, restore, restoreCivilShips;
};

#endif
