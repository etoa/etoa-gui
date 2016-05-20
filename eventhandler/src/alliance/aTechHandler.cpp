
#include "aTechHandler.h"

namespace atech
{
	void aTechHandler::update()
	{
		std::time_t time = std::time(0);
		
		mysqlpp::Query query = con_->query();
   
		// Perform level update
		query << "UPDATE "
			<< "	alliance_techlist "
			<< "SET "
			<< "	alliance_techlist_current_level=alliance_techlist_current_level+1, "
			<< "	alliance_techlist_build_start_time=0, "
			<< "	alliance_techlist_build_end_time=0 "
			<< "WHERE "
			<< "	alliance_techlist_build_start_time>0 AND"
			<< " alliance_techlist_build_end_time<" << time << ";";
		query.store();
		//std::cout << "Upgraded "<<con_->affected_rows()<<" Alliance Technologies\n";
		query.reset();    
    
	}	
}
