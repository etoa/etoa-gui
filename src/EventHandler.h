
#ifndef __EVENTHANDLER__
#define __EVENTHANDLER__

#include <mysql++/mysql++.h>
#include "MysqlHandler.h"

/**
* EventHandler base class / interface
* 
* \author Nicolas Perrenoud <mrcage@etoa.ch>
*/
class EventHandler	
{
public:
	/**
	* Eventhandler constructor for all handler classes.
	* Sets the internal MySQL connection pointer
	*/
	EventHandler() {
		My &my = My::instance();
		con_ = my.get();
		
	}
		
	/**
	* Abstract class for handling the events
	* Each derived class has to implement this method
	*/
	virtual void update() = 0;

protected:
	/**
	* The connection object
	*/
	mysqlpp::Connection* con_;
};

#endif
