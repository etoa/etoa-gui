#include <iostream>
#include <vector>

#include <time.h>
#include <mysql++/mysql++.h>

#include "aTechHandler.h"

namespace atech
{
	void aTechHandler::update()
	{
		std::time_t time = std::time(0);
		
		// Load planets who needs updating
		mysqlpp::Query query = con_->query();
   
		// Perform level update
		query << "UPDATE ";
		query << "	alliance_techlist ";
		query << "SET ";
		query << "	alliance_techlist_current_level=alliance_techlist_current_level+1, ";
		query << "	alliance_techlist_build_start_time=0, ";
		query << "	alliance_techlist_build_end_time=0 ";
		query << "WHERE ";
		query << "	alliance_techlist_build_start_time>0 AND";
		query << " alliance_techlist_build_end_time<" << time << ";";
		query.store();
		std::cout << "Upgraded "<<con_->affected_rows()<<" Alliance Technologies\n";
		query.reset();    
    
	}	
}
