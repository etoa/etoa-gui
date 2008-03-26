#ifndef __DEFLIST__
#define __DEFLIST__

#include <mysql++/mysql++.h>

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
		static void add(mysqlpp::Connection* con, int planetId, int userId, int defId, int count);
		
	};
}
#endif
