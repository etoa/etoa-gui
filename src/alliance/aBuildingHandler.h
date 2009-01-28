
#ifndef __ABUILDINGHANDLER__
#define __ABUILDINGHANDLER__

#include <mysql++/mysql++.h>

#include <ctime>

#include "../EventHandler.h"
#include "../MysqlHandler.h"

/**
* Handles building updates
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace abuilding
{
	class aBuildingHandler	: EventHandler
	{
	public:
		aBuildingHandler()  : EventHandler() { }
		void update();
	};
}
#endif
