//////////////////////////////////////////////////
//   ____    __           ______                //
//  /\  _`\ /\ \__       /\  _  \               //
//  \ \ \L\_\ \ ,_\   ___\ \ \L\ \              //
//   \ \  _\L\ \ \/  / __`\ \  __ \             //
//    \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \            //
//     \ \____/\ \__\ \____/\ \_\ \_\           //
//      \/___/  \/__/\/___/  \/_/\/_/  	        //
//                                              //
//////////////////////////////////////////////////
// The Andromeda-Project-Browsergame            //
// Ein Massive-Multiplayer-Online-Spiel         //
// Programmiert von Nicolas Perrenoud           //
// www.nicu.ch | mail@nicu.ch                   //
// als Maturaarbeit '04 am Gymnasium Oberaargau	//
//////////////////////////////////////////////////

/**
* Main event handler file, initializes database
* connection and runs the main loop
*
* @author Nicolas Perrenoud<mrcage@etoa.ch>
* 
* Copyright (c) 2004 by EtoA Gaming, www.etoa.net
*
* $Rev$
* $Author$
* $Date$
*/

#include "etoa.h"
#include <exception>
#include "util/MemInfo.h"
#include "version.h"

void etoamain()
{
	int minLoopDuration = Config::instance().getSleep();	// Minimal loop duration
	
	LOG(LOG_DEBUG,"Entering main event-handler loop");				
	
	// TODO: Error handling
	std::time_t mtime=0;
	srand(time(0));
	
	//Load Data
	DataHandler &DataHandler = DataHandler::instance();

	// Main loop
	if (debugEnable(0))
	{
		DEBUG("Waiting 3 seconds so that you can read all messages before entering loop");
		sleep(3);
	}
	while (true)
	{
		try 
		{ 
			
			// Update the data and config, everyday once at about 02:17:00 AM
			if ((time(0)-1021)%86400==0) {
				DataHandler.reloadData();
				Config::instance().reloadConfig();
			}
		
			// Graphical bling-bling
			if (debugEnable(0))
			{
				if(system("clear") != 0) {
					DEBUG("Unable to execute 'clear' command");
				}
			}

			DEBUG("----------------------------------------------------------------");
			DEBUG("- EtoA Eventhandler, (C) 2007 by EtoA Gaming                   -");
			DEBUG("----------------------------------------------------------------");
			if (debugEnable(0)) {
				DEBUG("  Version      : " << __ETOAD_VERSION_STRING__);
				time_t rawtime;
				time ( &rawtime );
				std::string str(ctime (&rawtime));
				str.erase(std::remove(str.begin(), str.end(), '\n'), str.end());
				DEBUG("  Time         : " << str << " (" << std::time(0) << ")");
				MemInfo* mi = new MemInfo();
				DEBUG("  Memory usage : VIRT " << (mi->getVirtualMemUsedByCurrentProcess()) << " KB, PHYS " << (mi->getPhysMemUsedByCurrentProcess()) << " KB");
				delete mi;
			}
			DEBUG("----------------------------------------------------------------\n");
			
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
			delete abh;
			
			atech::aTechHandler* ath = new atech::aTechHandler();
			ath->update();
			delete ath;
			
			tech::TechHandler* th = new tech::TechHandler();
			th->update(); 
			delete th;
			
			building::BuildingHandler* bh = new building::BuildingHandler();
			bh->update();  
			
			fleet::FleetHandler* fh = new fleet::FleetHandler();
			fh->update(); 
      delete fh;
      
			ship::ShipHandler* sh = new ship::ShipHandler();
			sh->update();  
	
			def::DefHandler* dh = new def::DefHandler();
			dh->update();  
			
      // Collect id's of changed planets
      
      std::vector<int> changedPlanetIds;
    
      std::vector<int> v1;
      if (bh->changes()) {
        v1 = bh->getChangedPlanets();
      }
      delete bh;
      
      std::vector<int> v2;
      if (sh->changes()) {
        v2 = sh->getChangedPlanets();
      }
      delete sh;
     
      std::vector<int> v3;
      if (dh->changes()) {
        v3 = dh->getChangedPlanets();
      }
      delete dh;
      
      std::vector<int> v4;
      while(!EntityUpdateQueue::instance().empty()) 
      {
        v4.push_back(EntityUpdateQueue::instance().front());
        EntityUpdateQueue::instance().pop();
      }

      planet::PlanetManager pm = planet::PlanetManager();
      std::vector<int> v5 = pm.getUpdateableUserPlanets();
      
      changedPlanetIds.reserve(v1.size() + v2.size() + v3.size() + v4.size() + v5.size());
      changedPlanetIds.insert(changedPlanetIds.end(), v1.begin(), v1.end());
      changedPlanetIds.insert(changedPlanetIds.end(), v2.begin(), v2.end());
      changedPlanetIds.insert(changedPlanetIds.end(), v3.begin(), v3.end());
      changedPlanetIds.insert(changedPlanetIds.end(), v4.begin(), v4.end());
      changedPlanetIds.insert(changedPlanetIds.end(), v5.begin(), v5.end());
  
      sort(changedPlanetIds.begin(), changedPlanetIds.end());
      changedPlanetIds.erase(unique(changedPlanetIds.begin(), changedPlanetIds.end() ), changedPlanetIds.end());        
        
      pm.updatePlanets(&changedPlanetIds);
      
      DEBUG("Updated " << changedPlanetIds.size() << " planets.");

    }

		// Catch mysql exceptions
		catch (mysqlpp::BadQuery e) {
			LOG(LOG_ERR,"MySQL: Unexpected query error: " << e.what());
			sleep(10);
		}
		catch (mysqlpp::Exception e) 
		{ 
			LOG(LOG_ERR,"MySQL: General error: " << e.what()); 
			sleep(10);
		}
		
		sleep(minLoopDuration);
	}
	
	LOG(LOG_ERR,"Unexpectedly reached end of main thread!");
	exit(EXIT_FAILURE);
			
}

