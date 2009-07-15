#ifndef __DEFLIST__
#define __DEFLIST__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"


/**
* Provides functions for defense lists
* 
* \author Nicolas Perrenoud <mrcage@etoa.ch>
*/
namespace def
{
	class DefList
	{
	public:
		static void add(int planetId, int userId, int defId, int count);
	};
}
#endif
