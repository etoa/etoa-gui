
#include "aBuildingHandler.h"

namespace abuilding
{
	void aBuildingHandler::update()
	{
		std::time_t time = std::time(0);
		
		mysqlpp::Query query = con_->query();;
		// Perform level update
		query << "UPDATE "
			<< "	alliance_buildlist "
			<< "SET "
			<< "	alliance_buildlist_current_level=alliance_buildlist_current_level+1, "
			<< "	alliance_buildlist_build_start_time=0, "
			<< "	alliance_buildlist_build_end_time=0 "
			<< "WHERE "
			<< "	alliance_buildlist_build_end_time>0 AND"
			<< " alliance_buildlist_build_end_time<" << time << ";";
		query.store();
		//std::cout << "Upgraded "<<con_->affected_rows()<<" Alliance Buildings\n";
		query.reset();    
	}	
}
