
#ifndef __WRECKAGEHANDLER__
#define __WRECKAGEHANDLER__

#include <mysql++/mysql++.h>

#include "../../FleetHandler.h"
#include "../../MysqlHandler.h"

/**
* Handles Wreackage....
* Collect the wreckage field
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/
namespace wreckage
{
	class WreckageHandler	: public FleetHandler
	{
	public:
		WreckageHandler(mysqlpp::Row fleet)  : FleetHandler(fleet) { }
		void update();

	private:
		/**
		* Wreckage resources
		**/
		double metal, crystal, plastic;
		double sum;
		
		/**
		* Fleet capacity and percentage fleet capacity / resources on the field
		**/
		double capa, percent;
		
	};
}
#endif
