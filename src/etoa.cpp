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

#include "etoa.h"

void etoamain()
{         
	int minLoopDuration = 1;	// Minimal loop duration

	std::clog << "Entering etoa main loop"<<std::endl;

	// TODO: Error handling
	std::time_t mtime=0;
	srand(time(0));
	
	//Loading Configdata
	//Config &config = Config::instance();
	// TODO: Check if we need the config variable
	Config::instance();
	
	//Load Data
	DataHandler &DataHandler = DataHandler::instance();

	
	// Main loop
	while (true)
	{
		// Update the data, everyday once at about 02:17:00 AM
		if ((time(0)-1021)%86400==0) {
			DataHandler.reloadData();
		}
		
		// Graphical bling-bling
		//system("clear");
		//std::cout << "----------------------------------------------------------------\n";
		//std::cout << "- EtoA Eventhandler, (C) 2007 by EtoA Gaming, Time: "<< std::time(0) <<" -\n";
		//std::cout << "----------------------------------------------------------------\n\n";
		
		//quest::QuestHandler* qh = new quest::QuestHandler();
		//qh->update();
		
		/**
		* Start with event handling
		*/
		if ((mtime+300) < std::time(0))
		{
			market::MarketHandler* mh = new market::MarketHandler();
			mh->update();
			mtime = std::time(0);
			delete mh;
		}
		
		if ((std::time(0) + 60) % 3600 == 0)
		{
			aPoints::aPointsHandler* aph = new aPoints::aPointsHandler();
			aph->update();
			delete aph;
		}
		
		
		abuilding::aBuildingHandler* abh = new abuilding::aBuildingHandler();
		abh->update();
		
		atech::aTechHandler* ath = new atech::aTechHandler();
		ath->update();
		
		building::BuildingHandler* bh = new building::BuildingHandler();
		bh->update();  

		tech::TechHandler* th = new tech::TechHandler();
		th->update(); 
		delete th;
		
		fleet::FleetHandler* fh = new fleet::FleetHandler();
		fh->update(); 

		ship::ShipHandler* sh = new ship::ShipHandler();
		sh->update();  

		def::DefHandler* dh = new def::DefHandler();
		dh->update();  

		if (bh->changes() || dh->changes() || sh->changes() || true)
		{			
			//std::cout << "Changing planet data...\n";
			// Load id's of changed planets
			std::vector<int> v1 = bh->getChangedPlanets();
			std::vector<int> v2 = sh->getChangedPlanets();
			std::vector<int> v3 = dh->getChangedPlanets();
			delete bh;
			delete sh;
			delete dh;
			delete fh;
			
			// Merge all changed planet id's together
			for (unsigned int x=0; x<v2.size(); x++)
			{
				std::vector<int>::iterator result;
 				result = find(v1.begin(), v1.end(), v2[x]);
 				if (result == v1.end())
 				{ 
 					 v1.push_back(v2[x]);
 				}				
			}
			for (unsigned int x=0;x<v3.size();x++)
			{
				std::vector<int>::iterator result;
 				result = find( v1.begin(), v1.end(), v3[x]);
 				if (result == v1.end())
 				{
 					v1.push_back(v3[x]);
				}
			}
			planet::PlanetManager* pm = new planet::PlanetManager(&v1);
			pm->updateUserPlanets();
			delete pm;

		}
		sleep(minLoopDuration);
	}		
}

