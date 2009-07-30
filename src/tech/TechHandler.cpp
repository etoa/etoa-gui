
#include "TechHandler.h"

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
		//std::cout << "Upgraded "<<con_->affected_rows()<<" Technologies\n";
		query.reset();    
		
		if (con_->affected_rows()>0) {
			this->changes_ = true;
		}
	}	
}
