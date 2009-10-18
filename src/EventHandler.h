
#ifndef __EVENTHANDLER__
#define __EVENTHANDLER__

#define MYSQLPP_MYSQL_HEADERS_BURIED
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
	EventHandler():my(My::instance()) {
//		My &my = My::instance();
		con_ = my.get();
		
	}
	
	virtual ~EventHandler() {};
		
	/**
	* Abstract class for handling the events
	* Each derived class has to implement this method
	*/
	virtual void update() = 0;

protected:
	My &my;
	/**
	* The connection object
	*/
	mysqlpp::Connection* con_;
};

#endif
