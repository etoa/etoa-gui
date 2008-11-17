
#ifndef __TRANSPORTHANDLER__
#define __TRANSPORTHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Transport....
* Well the word transport explains itself
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace transport
{
	class TransportHandler	: FleetHandler
	{
	public:
		TransportHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();
	};
}
#endif
