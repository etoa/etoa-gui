
#ifndef __USERHANDLER__
#define __USERHANDLER__

#include <mysql++/mysql++.h>
#include <vector>
#include "../MysqlHandler.h"
#include "ObjectHandler.h"

/**
* User class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class UserHandler	
{
public:
	/**
	* Eventhandler constructor for all handler classes.
	* Sets the internal MySQL connection pointer
	*/
	UserHandler(int uId) {
		this->userId = uId;
		
		//getUserAlliance();
		
		shieldTech = 1;
		structureTech = 1;
		weaponTech = 1;
		healTech = 1;
		structure = 0;
		shield = 0;
		weapon = 0;
		heal = 0;
		count = 0;
		healCount = 0;
		

	}
//	void getUserAlliance();
	
	//standart Information
	int userId;
	int allianceId;
	std::string allianceTag, allianceName;

	std::vector <double> loseFleet;
		
	//Objectcontainer
	std::vector < ObjectHandler > objects;
	std::vector < ObjectHandler > defObjects;
	
	//Basicdatas
	double structure, shield, weapon, count, heal, healCount;
	
	//technologielevel
	float shieldTech, structureTech, weaponTech, healTech;
	
	//start Values
	double initStructureShield, initShield, initStructure, initWeapon, initCount;
	
	//current Values
	double cCount, cStructureShield, cWeapon, cHealPoints, cHealCount, percentage;
	int newExpInit;
	
	//Calculates cCount, cWeapon, cHealPoints, cHealCount while fighting 
	void updateValues();
	
	//Calculates Values after the Fight
	void updateValuesEnd(std::vector<double> &wf);
};

#endif
