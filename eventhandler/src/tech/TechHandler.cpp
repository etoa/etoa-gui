
#include "TechHandler.h"
#include "../util/Debug.h"
namespace tech
{
	void TechHandler::update()
	{
		std::time_t time = std::time(0);
		
		// Load planets who needs updating
		mysqlpp::Query query = con_->query();
		
		// Perform level update
		query << "UPDATE "
			<< "	techlist "
			<< "SET "
			<< "	techlist_current_level=techlist_current_level+1, "
			<< "	techlist_build_type=0, "
			<< "	techlist_build_start_time=0, "
			<< "	techlist_build_end_time=0 "
			<< "WHERE "
			<< "	techlist_build_type=3 "
			<< "	AND techlist_build_end_time<" << time << ";";
		query.store();
		DEBUG("Technologies: "<< my.affected_rows(query) <<" upgraded");

		if( my.affected_rows(query) > 0 ) {
			this->changes_ = true;
		}
	}	
}
