
#include "DefList.h"

namespace def
{
	void DefList::add(int planetId, int userId, int defId, int count)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		count = count < 0 ? 0 : count;
		
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "	deflist_id "
			<< "FROM "
			<< "	deflist "
			<< "WHERE "
			<< "	deflist_user_id=" << userId <<" "
			<< "	AND deflist_planet_id=" << planetId <<" "
			<< "	AND deflist_def_id=" << defId <<";";
		mysqlpp::Result res = query.store();		
			query.reset();

		if (res)
		{
			if (res.size()>0)
			{
				mysqlpp::Row arr = res.at(0);
					
		  	query << "UPDATE "
				<< "	deflist "
				<< "SET "
				<< "	deflist_count = deflist_count+" << count << " "
				<< "WHERE "
				<< "	deflist_id=" << (int)arr["deflist_id"] <<";";
		    query.store();		
				query.reset();				

			std::cout << "Updated Def: Planet: "<<planetId
				<< " User: " << userId
				<< " Def: " << defId
				<< " Count: " << count
				<< "\n";	


			}
			else
			{
				query << "INSERT INTO "
					<< "	deflist ("
					<< "	deflist_user_id, "
					<< "	deflist_planet_id, "
					<< "	deflist_def_id, "
					<< "	deflist_count "
					<< ") VALUES ( "
					<< "	" << userId << ", "
					<< "	" << planetId << ", "
					<< "	" << defId << ", "
					<< "	" << count << ");";
				query.store();		
					query.reset();

				std::cout << "Added Defense: Planet: "<<planetId
					<< " User: " << userId
					<< " Def: " << defId
					<< " Count: " << count
					<< "\n";	


			}			
		}
	}
}
