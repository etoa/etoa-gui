
#ifndef __FLEET__
#define __FLEET__

#include <string>
#include <vector>
#include <math.h>
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "../functions/Functions.h"
#include "../objectData/ObjectHandler.h"
#include "../objectData/ObjectDataHandler.h"

#include "Object.h"
#include "ObjectFactory.h"

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
	
	double capacity, actionCapacity, peopleCapacity;
	bool actionAllowed, shipsLoaded, entityLoaded, shipsChanged;
	int entityToUserId;
	
	bool changedData;
	
	std::vector<Object*> objects;
	
	std::string logFleetShipStart;

public:
	Fleet(mysqlpp::Row &fleet);
	
	~Fleet() {
		this->save();
	}
	
	int getId();
	int getUserId();
	int getEntityFrom();
	int getEntityTo();
	int getNextId();
	int getLandtime();
	int getLaunchtime();
	int getNextactiontime();
	std::string getAction();
	short getStatus();
	double getPilots();
	double getResMetal();
	double getResCrystal();
	double getResPlastic();
	double getResFuel();
	double getResFood();
	double getResPower();
	double getResPeople();
	double getResLoaded();
	double getCapacity();
	double getActionCapacity();
	double getPeopleCapacity();
	
	double addMetal(double metal);
	double addCrystal(double crystal);
	double addPlastic(double plastic);
	double addFuel(double fuel);
	double addFood(double food);
	double addPower(double power);
	double addPeople(double people);
	
	double unloadResMetal();
	double unloadResCrystal();
	double unloadResPlastic();
	double unloadResFuel(bool land=true);
	double unloadResFood(bool land=true);
	double unloadResPower();
	double unloadResPeople(bool land=true);
	
	void setReturn();
	
	int getEntityToUserId();
	std::string getEntityToUserString();
	
	std::string getActionString();
	std::string getLandtimeString();
	std::string getLaunchtimeString();
	std::string getEntityToString(short type=0);
	std::string getEntityFromString(short type=0);
	
	bool actionIsAllowed();
	
	std::string getLogResStart();
	std::string getLogResEnd();
	std::string getLogShipsStart();
	std::string getLogShipsEnd();
	
private:
	void loadShips();
	void recalcShips();
	
	void save();
};

#endif
