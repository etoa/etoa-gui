#ifndef __PLANETHANDLER__
#define __PLANETHANDLER__

#include <mysql++/mysql++.h>

namespace planet
{
	class changePlanet
	{
	public:
		static void changePlanetUserId(mysqlpp::Connection* con,  mysqlpp::Row);
		static int countUserPlanets(mysqlpp::Connection* con, mysqlpp::Row);
		static void colonizePlanet(mysqlpp::Connection* con, mysqlpp::Row);
	};
	
		
}
#endif
