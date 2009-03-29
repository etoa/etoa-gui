
#ifndef __FLEET__
#define __FLEET__

#include <string>
#include <vector>
#include <math.h>
#include <ctime>
#include <iostream>
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "../util/Functions.h"
#include "../config/ConfigHandler.h"
#include "../data/DataHandler.h"

#include "Message.h"
#include "Object.h"
#include "ObjectFactory.h"
#include "User.h"

/**
* Fleet class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Fleet	
{	
	/**
	* Data from fleet table
	**/
	std::string action;
	int fId;
	int userId;
	int leaderId;
	int entityFrom,	entityTo, nextId;
	int launchtime, landtime, nextactiontime;
	short status;
	double pilots;
	int usageFuel, usageFood, usagePower, supportUsageFuel, supportUsageFood;
	double resMetal, resCrystal, resPlastic, resFuel, resFood, resPower, resPeople;
	double initResMetal, initResCrystal, initResPlastic, initResFuel, initResFood, initResPower, initResPeople;
	double fetchMetal, fetchCrystal, fetchPlastic, fetchFuel, fetchFood, fetchPower, fetchPeople;
	
	double capacity, actionCapacity, peopleCapacity, bounty;
	bool actionAllowed, shipsLoaded, entityLoaded, shipsChanged;
	
	double initWeapon, initShield, initStructure, initStructShield, initHeal, initCount;
	double weapon, shield, structure, heal;
	unsigned int actionCount, healCount, count;
	
	int allianceWeapon, allianceStructure, allianceShield;
	
	double exp;
	
	double antraxBonus, antraxFoodBonus, destroyBonus, empBonus, forstealBonus;
	
	bool techsAdded, allianceTechsLoaded;
	
	bool changedData;
	
	std::vector<Fleet*> fleets;
	
	std::string logFleetShipStart;

public:
	Fleet(mysqlpp::Row &fleet);
	
	~Fleet() {
		delete this->fleetUser;
		
		this->save();
	}
	
	int getId();
	int getUserId();
	int getLeaderId();
	int getEntityFrom();
	int getEntityTo();
	int getNextId();
	int getLandtime();
	int getLaunchtime();
	int getNextactiontime();
	std::string getAction(bool blank=false);
	short getStatus();
	
	void addMessageUser(Message* message);
	
	double getPilots(bool total=false);
	double getResMetal(bool total=false);
	double getResCrystal(bool total=false);
	double getResPlastic(bool total=false);
	double getResFuel(bool total=false);
	double getResFood(bool total=false);
	double getResPower(bool total=false);
	double getResPeople(bool total=false);
	double getResLoaded(bool total=false);
	double getInitResLoaded();
	double getCapacity(bool total=false);
	double getCapa();
	double getActionCapacity(bool total=false);
	double getPeopleCapacity(bool total=false);
	
	void addRaidedRes();
	
	double getBounty();
	double getBountyBonus(bool total=false);
	
	double getFetchMetal(bool total=false);
	double getFetchCrystal(bool total=false);
	double getFetchPlastic(bool total=false);
	double getFetchFuel(bool total=false);
	double getFetchFood(bool total=false);
	double getFetchPeople(bool total=false);
	double getFetchSum(bool total=false);
	
	double addMetal(double metal, bool total=false);
	double addCrystal(double crystal, bool total=false);
	double addPlastic(double plastic, bool total=false);
	double addFuel(double fuel, bool total=false);
	double addFood(double food, bool total=false);
	double addPower(double power, bool total=false);
	double addPeople(double people, bool total=false);
	
	double unloadResMetal();
	double unloadResCrystal();
	double unloadResPlastic();
	double unloadResFuel(bool land=true);
	double unloadResFood(bool land=true);
	double unloadResPower();
	double unloadResPeople(bool land=true);
	
	double getWfMetal(bool total=false);
	double getWfCrystal(bool total=false);
	double getWfPlastic(bool total=false);
	
	double getWeapon(bool total=false);
	double getShield(bool total=false);
	double getStructure(bool total=false);
	double getStructShield(bool total=false);
	double getHeal(bool total=false);
	double getInitCount(bool total=false);
	double getCount(bool total=false);
	double getHealCount(bool total=false);
	unsigned int getActionCount(bool total=false);
	
	double getWeaponBonus();
	double getShieldBonus();
	double getStructureBonus();
	double getHealBonus();
	
	void setAllianceWeapon(int weapon);
	void setAllianceStructure(int structure);
	void setAllianceShield(int shield);
	
	double addExp(double exp);
	double getExp();
	double getAddedExp();
	
	double getSpecialShipBonusAntrax();
	double getSpecialShipBonusAntraxFood();
	double getSpecialShipBonusBuildDestroy();
	double getSpecialShipBonusEMP();
	double getSpecialShipBonusForsteal();
	
	void deleteActionShip(int count);
	
	void loadShips();
	void recalcShips();
	void setPercentSurvive(double percentage, bool total=true);
	
	void setReturn();
	void setMain();
	void setSupport();
	
	std::string getActionString();
	std::string getLandtimeString();
	std::string getLaunchtimeString();
	
	std::string getUserNicks();
	std::string getUserIds();
	std::string getShieldString(bool small=true);
	std::string getStructureString(bool small=true);
	std::string getStructureShieldString();
	std::string getWeaponString(bool small=true);
	std::string getCountString(bool small=true);
	
	std::string getDestroyedShipString(std::string reason);
	std::string getResCollectedString(bool total=false, std::string suject="Rohstoffe");
	std::string getShipString();
	
	bool actionIsAllowed();
	
	std::string getLogResStart();
	std::string getLogResEnd();
	std::string getLogShipsStart();
	std::string getLogShipsEnd();
	
	User *fleetUser;
	std::vector<Object*> objects;
	std::vector<Object*> specialObjects;
	std::vector<Object*> actionObjects;
	
private:
	void loadAdditionalFleets();
	
	void addTechs();
	void loadAllianceTechs();
	
	void save();
};

#endif
