#ifndef __DEFLIST__
#define __DEFLIST__

#include <mysql++/mysql++.h>

namespace def
{
	class DefList
	{
	public:
		static void add(mysqlpp::Connection* con, int planetId, int userId, int defId, int count);
		
	};
}
#endif
