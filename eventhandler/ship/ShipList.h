#ifndef __SHIPLIST__
#define __SHIPLIST__

#include <mysql++/mysql++.h>

namespace ship
{
	class ShipList
	{
	public:
		static void add(mysqlpp::Connection* con, int planetId, int userId, int shipId, int count);
		
	};
}
#endif
