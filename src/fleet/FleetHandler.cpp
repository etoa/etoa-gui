
#include "FleetHandler.h"

namespace fleet
{
	void FleetHandler::update()
	{
		//Fleetquery
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	* ";
		query << "FROM ";
		query << "	fleet ";
		query << "WHERE ";
		query << "	landtime<'" << time(0) << "' ";
		query << "	AND !(action='alliance' AND status='3') ";
		//query << "	AND user_id='1' ";
		query << "ORDER BY landtime ASC;";
		mysqlpp::StoreQueryResult res = query.store();
		query.reset();

		//std::cout << "Updating ";
		//Checking queryresult
		if (res)  {
	    	unsigned int resSize = res.size();
			//Checking if there are some results
	    	if (resSize>0) {

	    		//Put res into row
	    		mysqlpp::Row row;
	    		for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
	    			row = res.at(i);

					std::string action = (std::string)row["action"];

					//std::cout << "User: " << row["user_id"] << " Zeit: " << row["landtime"] << " Aktion: " << action << " Status:" << row["status"] << "\n";


					// Nachprüfen ob Landezeit wirklich kleider ist als aktuelle Zeit
					if ((int)row["landtime"] < time(0)) {
						// Load action
						// TODO
						FleetAction* fleet = FleetFactory::createFleet((short)row["status"], action, row);
						fleet->update();
						delete fleet;
					}
		    	}
			}

			else {
			//	std::cout << "0 Fleets\n";
			}
		}
	}
}
