
#ifndef __FLEET__
#define __FLEET__

#include <mysql++/mysql++.h>
#include "MysqlHandler.h"
#include <string>

/**
* Fleet class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Fleet	
{
	std::string action;
	int fId;
	int userId;
	int leaderId;
	int entityFrom;
	int entityTo;
	int nextId;
	int launchtime;
	int landtime;
	int nextactiontime;
	short status;
	double pilots;
	int usageFuel;
	int usageFood;
	int usagePower;
	int supportUsageFuel;
	int supportUsageFood;
	double resMetal, resCrystal, resPlastic, resFuel, resFood, resPower, resPeople;
	double fetchMetal, fetchCrystal, fetchPlastic, fetchFuel, fetchFood, fetchPower, fetchPeople;
	
	int entityToUserId;

public:
	Fleet(mysqlpp::Row &fleet);
	int getFId();
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
	int getEntityToUserId();
	std::string getEntityToUserString();
	
	std::string getActionString();
	std::string getLandtimeString();
	std::string getLaunchtimeString();
	std::string getEntityToString(short type=0);
	std::string getEntityFromString(short type=0);
};

#endif
