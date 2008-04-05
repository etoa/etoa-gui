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
#include "functions/Functions.h"
#include "config/ConfigHandler.h";
#include "MysqlHandler.h"
#include "fleetActions/cancel/CancelHandler.h"
#include "fleetActions/return/ReturnHandler.h"

using namespace std;

// DB-Constants
const char* DB_SERVER = "localhost";
const char* DB_NAME = "etoa";
const char* DB_USER = "root";
const char* DB_PASSWORD = "";



main(int argc, char *argv[])
{
	
	//mysqlpp::Connection con(DB_NAME,DB_SERVER,DB_USER,DB_PASSWORD);

	

	// Main loop
	while (true)
	{	
		Config &config = Config::instance();
		std::string df = config.get("num_planets", 1);
		cout << df;
		//Timestamp
		std::time_t time = std::time(0);
		
		// Graphical bling-bling
		system("clear");
		cout << "----------------------------------------------------------------\n";
		cout << "- EtoA Fleethandler, (C) 2007 by EtoA Gaming, Time: "<< time <<" -\n";
		cout << "----------------------------------------------------------------\n\n";
		My &my = My::instance();
		mysqlpp::Connection *con;
		con = my.get();
		
		//Fleetquery
		mysqlpp::Query query = con->query();
		    query << "SELECT ";
		    query << "* ";
		    query << "FROM ";
			query << "	fleet ";
			query << "WHERE ";
			query << " fleet_landtime<" << time << " ;";
		mysqlpp::Result res = query.store();	
		query.reset();
				
				
		cout << "Updating ";
		cout << res << "\n";
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
	    			
				    char str[4] = "";
				    strcpy( str, row["fleet_action"]);
					
					// Nachprüfen ob Landezeit wirklich kleider ist als aktuelle Zeit
					if ((int)row["fleet_landtime"] < time && (int)row["fleet_updating"]==0)
					{

						// Load action
						if (str[2]=='c')
						{
							cancel::CancelHandler* ch = new cancel::CancelHandler(row);
							ch->update();
						}
						else if (str[1]=='r')
						{
							retour::ReturnHandler* rh = new retour::ReturnHandler(row);
							rh->update();
						}
						else
						{
							switch (str[0] )
							{     
								case 'f':
									fetch::FetchHandler* fh = new fetch::FetchHandler(row);
									fh->update();
									break;
								default :
									defaul::DefaultHandler* dh = new defaul::DefaultHandler(row);
									dh->update();
							} 
						}
					}

		    		
		    	}
			      
			}
			else
			{
				cout << "0 Fleets\n";
			}
		}
		sleep(4);
		
	}		

	return 0;
}