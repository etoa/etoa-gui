
#ifndef __WRECKAGEHANDLER__
#define __WRECKAGEHANDLER__

#include "../FleetAction.h"
#include "../../../util/Functions.h"
#include "../../../config/ConfigHandler.h"

/**
* Handles Wreackage....
* Collect the wreckage field
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace wreckage
{
	class WreckageHandler	: public FleetAction
	{
	public:
		WreckageHandler(mysqlpp::Row fleet)  : FleetAction(fleet) { }
		void update();

	private:
		/**
		* Wreckage resources, only for Message
		**/
		double metal, crystal, plastic;
		double sum;
		
		/**
		* Percentage fleet capacity / resources on the field
		**/
		double percent;
		
	};
}
#endif
