#include <iostream>
#include <vector>

#include <time.h>
#include <mysql++/mysql++.h>

#include "TechHandler.h"

namespace tech
{
	void TechHandler::update()
	{
		std::time_t time = std::time(0);
		
		// Load planets who needs updating
    mysqlpp::Query query = con_->query();
   
    // Perform level update
    query << "UPDATE ";
		query << "	techlist ";
    query << "SET ";
		query << "	techlist_current_level=techlist_current_level+1, ";
    query << "	techlist_build_type=0, ";
		query << "	techlist_build_start_time=0, ";
		query << "	techlist_build_end_time=0 ";
		query << "WHERE ";
		query << "	techlist_build_type=1 ";
		query << "	AND techlist_build_end_time<" << time << ";";
		query.store();
   	std::cout << "Upgraded "<<con_->affected_rows()<<" Technologies\n";
    query.reset();    
    
    if (con_->affected_rows()>0)
    {
    	this->changes_ = true;
    }
	}	
}
