#include <iostream>
#include <vector>

#include <time.h>
#include <mysql++/mysql++.h>

#include "BuildingHandler.h"

namespace building
{
	void BuildingHandler::update()
	{
		std::time_t time = std::time(0);
		
		// Load planets who needs updating
    mysqlpp::Query query = con_->query();
    query << "SELECT ";
    query << "	buildlist_planet_id ";
    query << "FROM ";
	query << "	buildlist ";
	query << "WHERE ";
	query << "	buildlist_build_type!=0 ";
	query << "	AND buildlist_build_end_time<" << time << " ORDER BY buildlist_planet_id;";
    mysqlpp::Result res = query.store();		
		query.reset();
		
		// Add changed planets to vector
    if (res) 
    {
    	int resSize = res.size();
    	if (resSize>0)
    	{
    		changes_ = true;
	      mysqlpp::Row row;
	      int lastId = 0;
	      for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
	      {
	      	row = res.at(i);
	      	int pid = (int)row["buildlist_planet_id"];
	      	// Make sure there are no duplicate planet id's
	      	if (pid!=lastId)
	      	{
	      		this->changedPlanets_.push_back(pid);
	      	}
	      	lastId = pid;
	    	}
	    }
    }		
    
    // Perform level update
    query << "UPDATE ";
		query << "	buildlist ";
    query << "SET ";
		query << "	buildlist_current_level=buildlist_current_level+1, ";
    query << "	buildlist_build_type=0, ";
		query << "	buildlist_build_start_time=0, ";
		query << "	buildlist_build_end_time=0 ";
		query << "WHERE ";
		query << "	buildlist_build_type=1 ";
		query << "	AND buildlist_build_end_time<" << time << ";";
		query.store();
   	std::cout << "Upgraded "<<con_->affected_rows()<<" Buildings\n";
    query.reset();    
 
    query << "UPDATE ";
		query << "	buildlist ";
    query << "SET ";
		query << "	buildlist_current_level=buildlist_current_level-1,";
    query << "	buildlist_build_type=0,";
		query << "	buildlist_build_start_time=0, ";
		query << "	buildlist_build_end_time=0 ";
		query << "WHERE ";
		query << "	buildlist_build_type=2 ";
		query << "	AND buildlist_build_end_time<" << time << ";";
		query.store();   	
		std::cout << "Downgraded "<<con_->affected_rows()<<" Buildings\n";		
    query.reset();
	}	
}
