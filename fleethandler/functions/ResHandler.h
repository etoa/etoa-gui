#ifndef __RESHANDLER__
#define __RESHANDLER__

#include <mysql++/mysql++.h>

namespace res
{
	class addRes
	{
	public:
		static std::string add_fleet_res_to_planet_res(mysqlpp::Connection* con,  mysqlpp::Row);
		
	};
	
		
}
#endif
