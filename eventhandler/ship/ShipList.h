#ifndef __SHIPLIST__
#define __SHIPLIST__

#include <mysql++/mysql++.h>

/**
* Provides shiplist functions
* 
* \author Nicolas Perrenoud <mrcage@etoa.ch>
*/
namespace ship
{
	class ShipList
	{
	public:
		static void add(mysqlpp::Connection* con, int planetId, int userId, int shipId, int count);
		
	};
}
#endif
