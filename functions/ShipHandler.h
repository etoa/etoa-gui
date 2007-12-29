#ifndef __SHIPHANDLER__
#define __SHIPHANDLER__

#include <mysql++/mysql++.h>

namespace ships
{
	class addShips
	{
	public:
		static void add_fleet_ships_to_planet(mysqlpp::Connection*,  mysqlpp::Row);
	};

	class deleteFleet
	{
	public:
		static void delete_fleet(mysqlpp::Connection*,  mysqlpp::Row);
		
	};
}
#endif
