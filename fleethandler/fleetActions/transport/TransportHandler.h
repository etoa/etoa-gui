
#ifndef __TRANSPORTHANDLER__
#define __TRANSPORTHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Transport....
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

	private:
		bool sendMsg;
		
	};
}
#endif
