
#ifndef __FLEETHANDLER__
#define __FLEETHANDLER__

#include <string>
#include <mysql++/mysql++.h>

#include "MysqlHandler.h"
#include "objects/User.h"
#include "objects/Fleet.h"
#include "objects/Message.h"
#include "objects/Log.h"
#include "entity/Entity.h"
#include "entity/EntityFactory.h"
#include "entity/Asteroid.h"
#include "entity/Base.h"
#include "entity/Empty.h"
#include "entity/Entity.h"
#include "entity/Market.h"
#include "entity/Nebula.h"
#include "entity/Planet.h"
#include "entity/Star.h"
#include "entity/Unknown.h"
#include "entity/Wormhole.h"

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
		
		this->f = new Fleet(fleet);
		
		this->fleetUser = new User(f->getUserId());
		
		this->startEntity = EntityFactory::createEntityById(this->f->getEntityFrom());
		this->targetEntity = EntityFactory::createEntityById(this->f->getEntityTo());
		
		this->actionMessage = new Message();
		this->actionMessage->addFleetId(this->f->getId());
		this->actionMessage->addEntityId(this->f->getEntityTo());
		this->actionMessage->addUserId(this->f->getUserId());
		
		this->actionLog->addFleetId(this->f->getId());
		this->actionLog->addFleetUserId(this->f->getUserId());
		this->actionLog->addEntityToId(this->f->getEntityTo());
		this->actionLog->addEntityFromId(this->f->getEntityFrom());
		this->actionLog->addLaunchtime(this->f->getLaunchtime());
		this->actionLog->addLandtime(this->f->getLandtime());
		this->actionLog->addAction(this->f->getAction());
		this->actionLog->addStatus(this->f->getStatus());
	};
	
	FleetHandler(Fleet *f) {
		My &my = My::instance();
		this->con_ = my.get();
		
		this->f=f;
		
		this->fleetUser = new User(f->getUserId());
		
		this->actionMessage = new Message();
		this->actionMessage->addFleetId(this->f->getId());
		this->actionMessage->addEntityId(this->f->getEntityTo());
		this->actionMessage->addUserId(this->f->getUserId());
		
		this->actionLog = new Log();
		this->actionLog->addFleetId(this->f->getId());
		this->actionLog->addFleetUserId(this->f->getUserId());
		this->actionLog->addEntityToId(this->f->getEntityTo());
		this->actionLog->addEntityFromId(this->f->getEntityFrom());
		this->actionLog->addLaunchtime(this->f->getLaunchtime());
		this->actionLog->addLandtime(this->f->getLandtime());
		this->actionLog->addAction(this->f->getAction());
		this->actionLog->addStatus(this->f->getStatus());
	}
	
	~FleetHandler() {
		this->actionLog->addEntityUserId(this->targetEntity->getUserId());
		this->actionLog->addFleetResStart(this->f->getLogResStart());
		this->actionLog->addFleetResEnd(this->f->getLogResEnd());
		this->actionLog->addFleetShipsStart(this->f->getLogShipsStart());
		this->actionLog->addFleetShipsEnd(this->f->getLogShipsEnd());
		this->actionLog->addEntityResStart(this->targetEntity->getLogResStart());
		this->actionLog->addEntityResEnd(this->targetEntity->getLogResEnd());
		this->actionLog->addEntityShipsStart(this->targetEntity->getLogShipsStart());
		this->actionLog->addEntityShipsEnd(this->targetEntity->getLogShipsEnd());
		delete this->actionLog;
		
		delete this->actionMessage;
		
		delete this->targetEntity;		
		delete this->startEntity;
		
		delete this->fleetUser;
		
		delete this->f;
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
	User *fleetUser;
	Entity* startEntity;
	Entity* targetEntity;
	Message *actionMessage;
	Log *actionLog;
	std::string msgRes, msgShips, msgAllShips, text;
	
	//Fight
	int returnV;
	std::string bstat,bstat2,msgFight;
	bool returnFleet;
	
	int shipSteal;
};

#endif
