
#ifndef __FLEETACTION__
#define __FLEETACTION__

#include <string>
#include <mysql++/mysql++.h>

#include "../../MysqlHandler.h"
#include "../../util/Functions.h"
#include "../../objects/User.h"
#include "../../objects/Fleet.h"
#include "../../objects/Message.h"
#include "../../objects/Log.h"
#include "../../entity/Entity.h"
#include "../../entity/EntityFactory.h"
#include "../../entity/Asteroid.h"
#include "../../entity/Base.h"
#include "../../entity/Empty.h"
#include "../../entity/Entity.h"
#include "../../entity/Market.h"
#include "../../entity/Nebula.h"
#include "../../entity/Planet.h"
#include "../../entity/Star.h"
#include "../../entity/Unknown.h"
#include "../../entity/Wormhole.h"

/**
* Fleethandler base class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class FleetAction	
{
public:
	/**
	* Eventhandler constructor for all handler classes.
	* Sets the internal MySQL connection pointer
	*/
	FleetAction(mysqlpp::Row fleet) {
		this->fleet_=fleet;
		
		My &my = My::instance();
		this->con_ = my.get();
		
		this->f = new Fleet(fleet);
		
		this->startEntity = EntityFactory::createEntityById(this->f->getEntityFrom());
		if (this->f->getStatus()==3 && this->f->getNextactiontime() > 0)
			this->targetEntity = EntityFactory::createEntityById(this->f->getNextId());
		else
			this->targetEntity = EntityFactory::createEntityById(this->f->getEntityTo());
		
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
		this->actionLog->addAction(this->f->getAction(true));
		this->actionLog->addStatus(this->f->getStatus());
		
		this->msgShips = "";
		this->msgRes = "";
		
	}
	
	virtual ~FleetAction() {
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
		
		this->actionMessage->addText(this->msgShips);		
		this->actionMessage->addText(this->msgRes);
		delete this->actionMessage;
		
		delete this->targetEntity;		
		
		delete this->startEntity;
		
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
	void fleetLand(int fleetAction=0);

protected:
	/**
	* The connection object
	*/
	mysqlpp::Connection* con_;
	mysqlpp::Row fleet_;
	Fleet *f;
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
