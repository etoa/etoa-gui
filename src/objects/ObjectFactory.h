
#ifndef __OBJECTFACTORY__
#define __OBJECTFACTORY__


/**
* ObjectFactory Class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

#include <mysql++/mysql++.h>

#include "Object.h"
#include "ListShip.h"
#include "FleetShip.h"
#include "ListDef.h"

class ObjectFactory {

public:	
	static Object* createObject(mysqlpp::Row oRow, char type) 
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
				return new ListDef(oRow);
				break;
		}
		// TODO: What happens if none of the above is true? This method must 
		// return something or throw an error
	}
};

#endif
