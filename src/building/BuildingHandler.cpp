
#include "BuildingHandler.h"

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
		mysqlpp::Result res = query.store();		
		query.reset();
		
		if (res) {
			unsigned int resSize = res.size();
			if (resSize>0) {
				
				int sxNum = (int)config.nget("num_of_sectors",1);
				int cxNum = (int)config.nget("num_of_cells",1);
				int syNum = (int)config.nget("num_of_sectors",2);
				int cyNum = (int)config.nget("num_of_cells",2);
				
				// the mask
				char mask[10000] = "";
				
				mysqlpp::Row row;
				
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
	    			row = res.at(i);
					
					query << "SELECT "
					<< "	discoverymask "
					<< "FROM "
					<< "	users "
					<< "WHERE "
					<< "	user_id='" << (int)row["buildlist_user_id"] << "' "
					<< "LIMIT 1;";
					mysqlpp::Result maskRes = query.store();
					query.reset();
					
					if (maskRes) {
						int maskSize = maskRes.size();
						
						if (maskSize > 0) {
							mysqlpp::Row maskRow = maskRes.at(0);
							strcpy( mask, maskRow["discoverymask"]);
						}
					}
					
					int radius = 1 + (int)((int)row["buildlist_current_level"] * config.nget("discoverymask",0));
					int absX = (10 * ((int)row["sx"] - 1) + (int)row["cx"]);
					int absY = (10 * ((int)row["sy"] - 1) + (int)row["cy"]);
					
					for (int x = absX - radius; x <= absX + radius; x++) {
						for (int y = absY - radius; y <= absY + radius; y++) {
							int pos = x + (cyNum * syNum) * (y - 1) - 1;
							if (pos >= 0 && pos <= sxNum * syNum * cxNum * cyNum) {
								mask[pos] = '1';
							}
						}
					}
					
					// Update the mask
					query << "UPDATE "
					<< "	users "
					<< "SET "
					<< " discoverymask='" << mask << "' "
					<< "WHERE "
					<< "	user_id='" << (int)row["buildlist_user_id"] << "' "
					<< "LIMIT 1;";
					query.store();
					query.reset();
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
		query.reset();
		
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
