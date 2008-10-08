
#ifndef __DIVISIONHANDLER__
#define __DIVISIONHANDLER__

#include <mysql++/mysql++.h>
#include <vector>
#include "../MysqlHandler.h"
#include "FightObjectHandler.h"
#include "UserHandler.h"
#include "ShowObjectHandler.h"

/**
* User class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class DivisionHandler	
{
public:

	DivisionHandler() {
		this->structure = 0;
		this->shield = 0;
		this->weapon = 0;
		this->initStructureShield = 0;
		this->initShield = 0;
		this->initStructure = 0;
		this->initWeapon = 0;
		this->initCount = 0;

		this->capa = 0;
		
		this->allianceId=-1;
	}
	
	//standart Information
	std::string allianceTag, allianceName;

	std::vector <double> loseFleet;
		
	//Objectcontainer
	std::vector < FightObjectHandler > objects;
	std::map < int,ShowObjectHandler > showObjectsShip;
	std::map < int,ShowObjectHandler > showObjectsDef;
	std::map < int, UserHandler > users;
	
	//Basicdatas
	double structure, shield, weapon, count, heal;
	
	//start Values
	double initStructureShield, initShield, initStructure, initWeapon, initCount;
	
	//current Values
	double cCount, cStructureShield, cWeapon, cHealPoints, cHealCount, percentage;
	int newExpInit;
	double capa;
	bool special;
	
	//initialize Values
	void initValues();
	
	//getter
	std::string getIds(short type);
	std::string getNicks(int entityId=0);
	std::string getObjects(short type, bool repair=false);
		
	//Calculates cCount, cWeapon, cHealPoints, cHealCount while fighting 
	void updateValues();
	
	///functions
	void updateValuesEnd(std::vector<double> &wf);
	void loadDefValues();
	void loadShipValues();
	void loadFleet(int fId);
	void loadSupport(int entityId);
	void loadShips(int entityId);
	void loadDefense(int entityId);
	bool saveObjects();
	int getAllianceId(int entityId=0);

private:
	int allianceId;
};

#endif
