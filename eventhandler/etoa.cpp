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
* Main event handler file, initializes database
* connection and runs the main loop
*
* @author Nicolas Perrenoud<mrcage@etoa.ch>
* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
*/

#include <ctime>
#include <iostream>
#include <iomanip>
#include <cstdlib>	// For system commands
#include <vector>

#include <mysql++/mysql++.h>

#include "EventHandler.h"
#include "fleet/FleetHandler.h"
#include "building/BuildingHandler.h"
#include "tech/TechHandler.h"
#include "ship/ShipHandler.h"
#include "def/DefHandler.h"
#include "planet/PlanetManager.h"
#include "planet/Planet.h"

using namespace std;

// DB-Constants (ugly!!!)
const char* DB_SERVER = "213.133.123.35";
const char* DB_NAME = "etoatest";
const char* DB_USER = "etoatest";
const char* DB_PASSWORD = "etoatest";

float minLoopDuration = 1;	// Minimal loop duration

main(int argc, char *argv[])
{
	mysqlpp::Connection con(DB_NAME,DB_SERVER,DB_USER,DB_PASSWORD);
	// TODO: Error handling

	// Main loop
	while (true)
	{	
		// Graphical bling-bling
		system("clear");
		cout << "----------------------------------------------------------------\n";
		cout << "- EtoA Eventhandler, (C) 2007 by EtoA Gaming, Time: "<< std::time(0) <<" -\n";
		cout << "----------------------------------------------------------------\n\n";
		

		/**
		* Start with event handling
		*/
		building::BuildingHandler* bh = new building::BuildingHandler(&con);
		bh->update();  

		tech::TechHandler* th = new tech::TechHandler(&con);
		th->update();  

		ship::ShipHandler* sh = new ship::ShipHandler(&con);
		sh->update();  

		def::DefHandler* dh = new def::DefHandler(&con);
		dh->update();  

		if (bh->changes() || dh->changes() || sh->changes() || true)
		{			
			cout << "Changing planet data...\n";
			// Load id's of changed planets
			vector<int> v1 = bh->getChangedPlanets();
			vector<int> v2 = sh->getChangedPlanets();
			vector<int> v3 = dh->getChangedPlanets();

			// Merge all changed planet id's together
			for (int x=0; x<v2.size(); x++)
			{
				vector<int>::iterator result;
 				result = find(v1.begin(), v1.end(), v2[x]);
 				if (result == v1.end())
 				{ 
 					 v1.push_back(v2[x]);
 				}				
			}
			for (int x=0;x<v3.size();x++)
			{
				vector<int>::iterator result;
 				result = find( v1.begin(), v1.end(), v3[x]);
 				if (result == v1.end())
 				{
 					v1.push_back(v3[x]);
 				}						           
			}
			
			planet::PlanetManager* pm = new planet::PlanetManager(&con, &v1);
			pm->updateValues();
			
			
			
			
				
				
			//pm.updateFields();
			//pm.updateStorage();
			//pm.updateProductionRates();
			//pm.save();
		}


		// Check fleets
		/*
		fleet::FleetHandler* fh = new fleet::FleetHandler(&con);
		fh->update();*/
		
		sleep(4);
	}		

	return 0;
}
