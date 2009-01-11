
#ifndef __ABUILDINGHANDLER__
#define __ABUILDINGHANDLER__

#include <mysql++/mysql++.h>

#include "../EventHandler.h"
#include "../MysqlHandler.h"

/**
* Handles building updates
* 
* \author Nicolas Perrenoud <mrcage@etoa.ch>
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
