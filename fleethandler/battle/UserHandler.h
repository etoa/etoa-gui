
#ifndef __USERHANDLER__
#define __USERHANDLER__

#include <mysql++/mysql++.h>
#include <vector>
#include "../MysqlHandler.h"

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
		
		this->shieldTech = 1;
		this->structureTech = 1;
		this->weaponTech = 1;
		this->healTech = 1;
		this->shield = 0;
		this->structure = 0;
		this->weapon = 0;
		this -> count = 0;
		
		this->specialShipBonusCapacity = 1;
		this->capa = 0;
		
		getValues();
	}
	
	void getValues();
	
	//standart Information
	int userId;
	std::string userNick;
	int allianceId;
	
	//technologielevel
	float shieldTech, structureTech, weaponTech, healTech;
	
	//Userdata
	double shield, structure, weapon, count;
	double capa, specialShipBonusCapacity;
	std::map < int, double > fleetCapa; //Capa ordered by fleetID
};

#endif
