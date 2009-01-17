
#include "BuildingHandler.h"

namespace building
{
	void BuildingHandler::update()
	{
		std::time_t time = std::time(0);
		
		// Load planets who needs updating
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "	buildlist_entity_id "
			<< "FROM "
			<< "	buildlist "
			<< "WHERE "
			<< "	buildlist_build_type>2 "
			<< "	AND buildlist_build_end_time<" << time << " ORDER BY buildlist_entity_id;";
		mysqlpp::Result res = query.store();		
		query.reset();
		
		// Add changed planets to vector
		if (res) {
			int resSize = res.size();
			if (resSize>0) {
				this->changes_ = true;
				mysqlpp::Row row;
				int lastId = 0;
				
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
					row = res.at(i);
					int pid = (int)row["buildlist_entity_id"];
					// Make sure there are no duplicate planet id's
					if (pid!=lastId) {
						this->changedPlanets_.push_back(pid);
					}
					lastId = pid;
				}
			}
		}		
		
		// Perform level update
		query << "UPDATE "
			<< "	buildlist "
			<< "SET "
			<< "	buildlist_current_level=buildlist_current_level+1, "
			<< "	buildlist_build_type=0, "
			<< "	buildlist_build_start_time=0, "
			<< "	buildlist_build_end_time=0 "
			<< "WHERE "
			<< "	buildlist_build_type=3 "
			<< "	AND buildlist_build_end_time<" << time << ";";
		query.store();
		std::cout << "Upgraded "<<con_->affected_rows()<<" Buildings\n";
		query.reset();    
		
		query << "UPDATE "
			<< "	buildlist "
			<< "SET "
			<< "	buildlist_current_level=buildlist_current_level-1,"
			<< "	buildlist_build_type=0,"
			<< "	buildlist_build_start_time=0, "
			<< "	buildlist_build_end_time=0 "
			<< "WHERE "
			<< "	buildlist_build_type=4 "
			<< "	AND buildlist_build_end_time<" << time << ";";
		query.store();   	
		std::cout << "Downgraded "<<con_->affected_rows()<<" Buildings\n";		
		query.reset();
	}	
}
