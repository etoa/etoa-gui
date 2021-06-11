
#ifndef __OBJECTFACTORY__
#define __OBJECTFACTORY__


/**
* ObjectFactory Class
*
* @author Stephan Vock<glaubinx@etoa.ch>
*/

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "Object.h"
#include "ListShip.h"
#include "FleetShip.h"
#include "ListDef.h"

class ObjectFactory
{
public:
	static Object* createObject(mysqlpp::Row oRow, char type, double rebuild=1.0)
	{
		switch (type)
		{
			case 's':
				return new ListShip(oRow);
				break;
			case 'f':
				return new FleetShip(oRow);
				break;
			case 'd':
				return new ListDef(oRow,rebuild);
				break;
		}
		return NULL;
	}
};

#endif
