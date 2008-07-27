
#ifndef __RETURNHANDLER__
#define __RETURNHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Return....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace retour
{
	class ReturnHandler	: FleetHandler
	{
	public:
		ReturnHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:
		bool sendMsg;
		int pId;
		
	};
}
#endif
