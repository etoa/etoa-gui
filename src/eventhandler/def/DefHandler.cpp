#include <iostream>
#include <vector>

#include <math.h>
#include <time.h>
#include <mysql++/mysql++.h>

#include "DefHandler.h"
#include "DefList.h"

namespace def
{
	void DefHandler::update()
	{
		std::time_t time = std::time(0);
		
		std::cout << "Updating defs\n";

		// Load queues who needs updating
    mysqlpp::Query query = con_->query();
  	query << "SELECT "
		<< "	queue_def_id, "
		<< "	queue_user_id, "
		<< "	queue_endtime, "
		<< "	queue_objtime, "
		<< "	queue_cnt, "
		<< "	queue_id, "
		<< "	queue_entity_id "
		<< "FROM "
		<< "	def_queue "
		<< "WHERE "
		<< "	queue_starttime<" << time <<" "
		<< "	AND queue_build_type<1 "
		<< "ORDER BY queue_entity_id;";
    mysqlpp::Result res = query.store();		
		query.reset();

		// Add changed planets to vector
    if (res) 
    {
    	int resSize = res.size();
    	bool empty=false;

    	if (resSize>0)
    	{
	      mysqlpp::Row arr;
	      int lastId = 0;
	      for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
	      {
	      	arr = res.at(i);
			
			updatePlanet = false;

	  			// Alle Schiffe als gebaut speichern da Endzeit bereits in der Vergangenheit
	  			if ((int)arr["queue_endtime"] <= time)
	  			{
	  				if ((int)arr["queue_cnt"]>0)
	  				{
	  					DefList::add(				(int)arr["queue_entity_id"], 
	  												(int)arr["queue_user_id"],
	  												(int)arr["queue_def_id"],
	  												(int)arr["queue_cnt"]);
	  					changes_=true;
						updatePlanet = true;
	  				}
	  				empty=true;
	  			}
	  			// Bau ist noch im Gang
	  			else
	  			{
	  				int obj_cnt = (int)arr["queue_cnt"] - (int)ceil((double)((int)arr["queue_endtime"] - time)/(int)arr["queue_objtime"]);
					DefList::add(				(int)arr["queue_entity_id"], 
  												(int)arr["queue_user_id"],
  												(int)arr["queue_def_id"],
  												(int)obj_cnt);	  				
				  	query << "UPDATE "
						<< "	def_queue "
						<< "SET "
						<< "	queue_cnt=queue_cnt-" << obj_cnt << " "
						<< "WHERE " 
						<< "	queue_id=" << arr["queue_id"] <<";";
				    query.store();		
						query.reset();

					if ((int)obj_cnt > 0)
					{
						changes_=true;
						updatePlanet = true;
					}
	  			}	      	
				
				// Make sure there are no duplicate planet id's
				int pid = (int)arr["queue_entity_id"];
				if (pid!=lastId && updatePlanet)
				{
					this->changedPlanets_.push_back(pid);
				}
				lastId = pid;
				}
	    	
				// Vergangene AuftrÃ¤ge lÃ¶schen
				if (empty)
				{
					query << "DELETE FROM "
						<< "	def_queue "
						<< "WHERE "
						<< "	queue_endtime<=" << time <<" "
						<< "ORDER BY queue_entity_id;";
					query.store();		
						query.reset();	  			
				}	  	    	
			}  
		}
	}	
}
