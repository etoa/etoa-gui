
#ifndef __COLONIALIZEHANDLER__
#define __COLONIALIZEHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Colonialize....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace colonialize
{
	class ColonializeHandler	: FleetHandler
	{
	public:
		ColonializeHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
		
	};
}
#endif
