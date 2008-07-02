
#ifndef __ATECHHANDLER__
#define __ATECHHANDLER__

#include <mysql++/mysql++.h>

#include "../EventHandler.h"
#include "../MysqlHandler.h"

/**
* Handles technology research updates
* 
* \author Nicolas Perrenoud <mrcage@etoa.ch>
*/
namespace atech
{
	class aTechHandler	: EventHandler
	{
	public:
		aTechHandler()  : EventHandler() { }
		void update();
	};
}
#endif
