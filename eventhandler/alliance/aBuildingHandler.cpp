#include <iostream>
#include <vector>

#include <time.h>
#include <mysql++/mysql++.h>

#include "aBuildingHandler.h"

namespace abuilding
{
	void aBuildingHandler::update()
	{
		std::time_t time = std::time(0);
		
		mysqlpp::Query query = con_->query();;
		// Perform level update
		query << "UPDATE ";
		query << "	alliance_buildlist ";
		query << "SET ";
		query << "	alliance_buildlist_current_level=alliance_buildlist_current_level+1, ";
		//query << "	alliance_buildlist_build_type=0, ";
		query << "	alliance_buildlist_build_start_time=0, ";
		query << "	alliance_buildlist_build_end_time=0 ";
		query << "WHERE ";
		//query << "	alliance_buildlist_build_type=1 AND";
		query << " alliance_buildlist_build_end_time<" << time << ";";
		query.store();
		std::cout << "Upgraded "<<con_->affected_rows()<<" Alliance Buildings\n";
		query.reset();    
 
		/*query << "UPDATE ";
		query << "	alliance_buildlist ";
		query << "SET ";
		query << "	alliance_buildlist_current_level=alliance_buildlist_current_level-1,";
		query << "	alliance_buildlist_build_type=0,";
		query << "	alliance_buildlist_build_start_time=0, ";
		query << "	alliance_buildlist_build_end_time=0 ";
		query << "WHERE ";
		query << "	alliance_buildlist_build_type=2 ";
		query << "	AND alliance_buildlist_build_end_time<" << time << ";";
		query.store();   	
		std::cout << "Downgraded "<<con_->affected_rows()<<" Alliance Buildings\n";		
		query.reset();*/
	}	
}
