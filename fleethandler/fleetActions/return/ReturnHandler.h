
#ifndef __RETURNHANDLER__
#define __RETURNHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Return....
* After every action the fleet returns to the startplanet, that's it
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
		/**
		* User wanna have a return message or not
		**/
		bool sendMsg;
	};
}
#endif
