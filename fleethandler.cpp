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
#include "functions/MessageHandler.h"
//#include "special/SpecialHandler.h"
#include "back/BackHandler.h"
//#include "attack/AttackHandler.h"


using namespace std;

// DB-Constants
const char* DB_SERVER = "localhost";
const char* DB_NAME = "etoa";
const char* DB_USER = "root";
const char* DB_PASSWORD = "";



main(int argc, char *argv[])
{
	
	mysqlpp::Connection con(DB_NAME,DB_SERVER,DB_USER,DB_PASSWORD);

	// Main loop
	while (true)
	{	
		
		//Timestamp
		std::time_t time = std::time(0);
		
		// Graphical bling-bling
		system("clear");
		cout << "----------------------------------------------------------------\n";
		cout << "- EtoA Fleethandler, (C) 2007 by EtoA Gaming, Time: "<< time <<" -\n";
		cout << "----------------------------------------------------------------\n\n";
		

		//Fleetquery
		mysqlpp::Query query = con.query();
		    query << "SELECT ";
		    query << "* ";
		    query << "FROM ";
			query << "	fleet ";
			query << "WHERE ";
			query << " fleet_landtime<" << time << " ;";
		mysqlpp::Result res = query.store();	
		query.reset();
				
				
		cout << "Updating ";
		//Checking queryresult
		if (res) 
	    {
	    	int resSize = res.size();
			//Checking if there are some results
	    	if (resSize>0)
		   	{
				cout << resSize << " Fleet(s)\n\n";
				
	    		//Put res into row
	    		mysqlpp::Row row;
	    		int lastId = 0;
	    		for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
				{
	    			row = res.at(i);
	    			
	    			string fleet_cat, fleet_action;

				    char str[30] = "";
				    strcpy( str, row["fleet_action"]);
				    char * pch;	
				    //Reading category -> put into fleet_cat
				    pch = strtok (str,"_");
				    fleet_cat = pch;
				    //Reading action -> put into fleet_action
				    pch = strtok (NULL, " ,.-");
				    fleet_action = pch;
					
					
					//cout << to_go;
					
					cout << fleet_action << "(" << fleet_cat << ")\n";
				    back::BackHandler* ba = new back::BackHandler(&con);
					ba->update(row);
		    		
		    	}
			      
			}

	    }
		else
		{
			cout << "0 Fleets";
		}
		sleep(10);
		
	}		

	return 0;
}