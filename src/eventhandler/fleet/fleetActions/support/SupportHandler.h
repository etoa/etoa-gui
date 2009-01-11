
#ifndef __SUPPORTHANDLER__
#define __SUPPORTHANDLER__

#include "../FleetAction.h"
#include "../../../functions/Functions.h"
#include "../../../config/ConfigHandler.h"

/**
* Handles Support....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace support
{
	class SupportHandler	: public FleetAction
	{
	public:
		SupportHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();
		
	private:
		int flyingHomeTime;
		int landtime;
		int entity;
		std::string msg;
		
	};
}
#endif
