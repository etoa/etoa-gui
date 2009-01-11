#ifndef __SHIPLIST__
#define __SHIPLIST__

#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "../data/DataHandler.h"
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
		static void add(int planetId, int userId, int shipId, int count);
	};
}
#endif
