//////////////////////////////////////////////////
//		 	 ____    __           ______       			//
//			/\  _`\ /\ \__       /\  _  \      			//
//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
//																					 		//
//////////////////////////////////////////////////
// The Andromeda-Project-Browsergame				 		//
// Ein Massive-Multiplayer-Online-Spiel			 		//
// Programmiert von Nicolas Perrenoud				 		//
// www.nicu.ch | mail@nicu.ch								 		//
// als Maturaarbeit '04 am Gymnasium Oberaargau	//
//////////////////////////////////////////////////

/**
* Fleethandler; updates fleet_action, incl new Ks
* connection and runs the main loop
*
* @author Stephan Vock
* @copyright naaaaaaaa wei mir nid :D
*/


#include <ctime>
#include <iostream>
#include <iomanip>
#include <cstdlib>
#include <string>

#include <mysql++/mysql++.h>

#include "FleetHandler.h"
#include "fleetActions/FleetFactory.h"
#include "config/ConfigHandler.h"
#include "MysqlHandler.h"
#include "data/DataHandler.h"

using namespace std;


int main(int argc, char *argv[])
{	
	
	//Initialize Mysql Connection
	My &my = My::instance();
	mysqlpp::Connection *con_;
	con_ = my.get();
	mysqlpp::Query query = con_->query();
	
	//Loading Configdata
	Config &config = Config::instance();
	
	//Load Data
	DataHandler &DataHandler = DataHandler::instance();
	
	// Main loop
	while (true) {
		
		//Timestamp
		std::time_t time = std::time(0);
		srand(time);
		
		// Update the data, everyday once at about 02:17:00 AM
		if ((time-1021)%86400==0) {
			DataHandler.reloadData();
		}
		
		// Graphical bling-bling
		system("clear");
		setiosflags(ios::fixed);
		cout << "----------------------------------------------------------------\n";
		cout << "- EtoA Fleethandler, (C) 2007 by EtoA Gaming, Time: "<< time <<" -\n";
		cout << "----------------------------------------------------------------\n\n";
		
		//Fleetquery
		query << "SELECT ";
		query << "	* ";
		query << "FROM ";
		query << "	fleet ";
		query << "WHERE ";
		query << " landtime<'" << time << "';";
		mysqlpp::Result res = query.store();	
		query.reset();
		
		cout << "Updating ";
		//Checking queryresult
		if (res)  {
	    	int resSize = res.size();
			//Checking if there are some results
	    	if (resSize>0) {
				cout << resSize << " Fleet(s)\n\n";
				
	    		//Put res into row
	    		mysqlpp::Row row;
	    		int lastId = 0;
	    		for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
	    			row = res.at(i);
	    			
					std::string action = (std::string)row["action"];
					
					std::cout << "User: " << row["user_id"] << " Zeit: " << row["landtime"] << " Aktion: " << action << " Status:" << row["status"] << "\n";
					

					// Nachprüfen ob Landezeit wirklich kleider ist als aktuelle Zeit
					if ((int)row["landtime"] < time) {
						// Load action
						
						FleetHandler* fleet = FleetFactory::createFleet((short)row["status"], action, row);
						fleet->update();
						delete fleet;
					}
		    	}
			}
		
			else {
				cout << "0 Fleets\n";
			}
		}
		
		sleep(1);
	}		

	return 0;
}
