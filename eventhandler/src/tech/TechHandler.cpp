
#include "TechHandler.h"
#include "../util/Debug.h"
namespace tech
{
	void TechHandler::update()
	{
		std::time_t time = std::time(0);
		
		mysqlpp::Query query = con_->query();
		
		// Load users who needs updating
		query << "SELECT "
			<< "	techlist_user_id "
			<< "FROM "
			<< "	techlist "
			<< "WHERE "
			<< "	techlist_build_type=3 "
			<< "	AND techlist_build_end_time<" << time << " "
			<< "ORDER BY "
			<< "	techlist_user_id;";
		RESULT_TYPE res = query.store();
		
		// Add changed users to vector
		if (res) {
			unsigned int resSize = res.size();
			if (resSize>0) {
				this->changes_ = true;
				mysqlpp::Row row;
				int lastId = 0;
				
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
					row = res.at(i);
					int uid = (int)row["techlist_user_id"];
					// Make sure there are no duplicate planet id's
					if (uid!=lastId) {
						this->changedUsers_.push_back(uid);
					}
					lastId = uid;
				}
			}
		}
		
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
