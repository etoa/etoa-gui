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
#include "attack/attackhandler.h"
#include "back/backhandler.h"
#include "special/specialhandler.h"


using namespace std;

// DB-Constants (ugly!!!)
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
		
		// Graphical bling-bling
		system("clear");
		cout << "----------------------------------------------------------------\n";
		cout << "- EtoA Fleethandler, (C) 2007 by EtoA Gaming, Time: "<< std::time(0) <<" -\n";
		cout << "----------------------------------------------------------------\n\n";
		
		//Timestamp
		std::time_t time = std::time(0);
		
		//Query abfragen nach Flotten
		mysqlpp::Query query = con.query();
		    query << "SELECT ";
		    query << "* ";
		    query << "FROM ";
			query << "	fleet ";
			query << "WHERE ";
			query << " fleet_landtime<" << time << " ;";
		    mysqlpp::Result res = query.store();		
				query.reset();
				
		//†berprŸfung ob res		
		int i;
		if (res) 
	    {
	    	int resSize = res.size();
	    	if (resSize>0)
		   	{
	    		//Res wird Zeilenweise in Array row Ÿbertragen
	    		mysqlpp::Row row;
	    		int lastId = 0;
	    		for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
				{
	    			row = res.at(i);
	    			
	    			string row_fleet_cat, row_fleet_action;

				    char str[30] = "";
				    strcpy( str, row["fleet_action"]);
				    char * pch;	
				    //Aktionskategorie wird gelesen und an Variable Ÿbertragen
				    pch = strtok (str,"_");
				    row_fleet_cat = pch;
				    //Aktion wird gelesen und an Variable Ÿbertragen
				    pch = strtok (NULL, " ,.-");
				    row_fleet_action = pch;
					
					
				    back::BackHandler* ba = new back::BackHandler(&con);
					ba->update(row);
		    		
		    	}
			      
			}

	    }		
		sleep(1);
		
	}		

	return 0;
}