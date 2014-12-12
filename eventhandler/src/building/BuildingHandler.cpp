
#include "BuildingHandler.h"
#include "../util/Debug.h"
#include "../objects/User.h"
#include "../entity/EntityFactory.h"

namespace building
{
	void BuildingHandler::update()
	{
		std::time_t time = std::time(0);
		Config &config = Config::instance();
		
		// Load fleetcontrolls to update discoverymask
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "	buildlist_user_id, "
			<< "	buildlist_current_level, "
			<< "	buildlist_entity_id, "
			<< "	cells.sx, "
			<< "	cells.sy, "
			<< "	cells.cx, "
			<< "	cells.cy "
			<< "FROM "
			<< "	buildlist "
			<< "	INNER JOIN "
			<< "		entities "
			<< "	ON "
			<< "		entities.id=buildlist_entity_id "
			<< "		INNER JOIN "
			<< "			cells "
			<< "		ON "
			<< "			cells.id=entities.cell_id "
			<< "WHERE "
			<< "	buildlist_build_type>2 "
			<< "	AND buildlist_building_id='" << config.idget("FLEET_CONTROL_ID") << "' "
			<< "	AND buildlist_build_end_time<" << time << " ORDER BY buildlist_entity_id;";
		RESULT_TYPE res = query.store();

		if (res) {
			unsigned int resSize = res.size();
			if (resSize>0) {
				
				double factor = (double)config.nget("discoverymask",0);

				mysqlpp::Row row;
				
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
	    			row = res.at(i);
					
					int uid = (int)row["buildlist_user_id"];
					int eid = (int)row["buildlist_entity_id"];
					int radius = 1 + (int)((int)row["buildlist_current_level"] * factor);
				
                    Entity* e = EntityFactory::createEntityById(eid);

				    User u(uid);
                    u.setDiscovered(e->getAbsX(), e->getAbsY(), radius);

                    delete e;
				}
			}
		}
				
		// Load planets who needs updating
		query << "SELECT "
			<< "	buildlist_entity_id "
			<< "FROM "
			<< "	buildlist "
			<< "WHERE "
			<< "	buildlist_build_type>2 "
			<< "	AND buildlist_build_end_time<" << time << " ORDER BY buildlist_entity_id;";
		res = query.store();		
		
		
		// Add changed planets to vector
		if (res) {
			unsigned int resSize = res.size();
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
    int up = my.affected_rows(query);
		
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
    int down = my.affected_rows(query);
    
		DEBUG("Buildings: " << up << " upgraded, " << down << " downgraded");

	}	
}
