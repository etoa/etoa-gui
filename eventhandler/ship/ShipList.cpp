
#include "ShipList.h"

namespace ship
{
	void ShipList::add(mysqlpp::Connection* con, int planetId, int userId, int shipId, int count)
	{
		count = count < 0 ? 0 : count;
		
    mysqlpp::Query query = con->query();
  	query << "SELECT "
		<< "	shiplist_id "
		<< "FROM "
		<< "	shiplist "
		<< "WHERE "
		<< "	shiplist_user_id=" << userId <<" "
		<< "	AND shiplist_planet_id=" << planetId <<" "
		<< "	AND shiplist_ship_id=" << shipId <<";";
    mysqlpp::Result res = query.store();		
		query.reset();

		if (res)
		{
			if (res.size()>0)
			{
				mysqlpp::Row arr = res.at(0);
					
		  	query << "UPDATE "
				<< "	shiplist "
				<< "SET "
				<< "	shiplist_count = shiplist_count+" << count << " "
				<< "WHERE "
				<< "	shiplist_id=" << (int)arr["shiplist_id"] <<";";
		    query.store();		
				query.reset();				

		std::cout << "Updated Ship: Planet: "<<planetId
			<< " User: " << userId
			<< " Ship: " << shipId
			<< " Count: " << count
			<< "\n";	


			}
			else
			{
				query << "INSERT INTO "
					<< "	shiplist ("
					<< "	shiplist_user_id, "
					<< "	shiplist_planet_id, "
					<< "	shiplist_ship_id, "
					<< "	shiplist_count "
					<< ") VALUES ( "
					<< "	" << userId << ", "
					<< "	" << planetId << ", "
					<< "	" << shipId << ", "
					<< "	" << count << ");";
				query.store();
					query.reset();

		std::cout << "Added Ship: Planet: "<<planetId
			<< " User: " << userId
			<< " Ship: " << shipId
			<< " Count: " << count
			<< "\n";	


			}			
		}
	}
}
