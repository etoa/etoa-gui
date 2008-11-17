
#ifndef __FLEETHANDLER__
#define __FLEETHANDLER__

#include <mysql++/mysql++.h>
#include "MysqlHandler.h"
#include "Fleet.h"
#include <string>

/**
* Fleethandler base class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class FleetHandler	
{
public:
	/**
	* Eventhandler constructor for all handler classes.
	* Sets the internal MySQL connection pointer
	*/
	FleetHandler(mysqlpp::Row fleet) {
		this->fleet_=fleet;
		My &my = My::instance();
		this->con_ = my.get();
		this-> f = new Fleet(fleet);

	};
	
	FleetHandler(Fleet *f) {
		My &my = My::instance();
		this->con_ = my.get();
		this->f=f;
	}
		
	/**
	* Abstract class for handling the events
	* Each derived class has to implement this method
	*/
	virtual void update() = 0;
	
	/**
	* Standartflottenaktionen
	*/
	void fleetSendMain(int userId=0);
	void fleetDelete();
	void fleetReturn(int status,double resMetal=-1,double resCrystal=-1,double resPlastic=-1,double resFuel=-1,double resFood=-1,double resPeople=-1);
	void fleetLand(int fleetAction=0,bool alreadyColonialized=0,bool alreadyInvaded=0);

protected:
	/**
	* The connection object
	*/
	mysqlpp::Connection* con_;
	mysqlpp::Row fleet_;
	Fleet *f;
	std::string msgRes, msgShips, msgAllShips, text;
	
	//Fight
	int returnV;
	std::string bstat,bstat2,msgFight;
	bool returnFleet;
	
	int shipSteal;
};

#endif
