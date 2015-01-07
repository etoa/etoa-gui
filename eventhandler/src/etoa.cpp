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
	while (true)
	{
		try 
		{ 
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
				MemInfo mi;
				DEBUG("  Memory usage : VIRT " << (mi.getVirtualMemUsedByCurrentProcess()) << " KB, PHYS " << (mi.getPhysMemUsedByCurrentProcess()) << " KB");
			}
			DEBUG("----------------------------------------------------------------\n");

			bool reloadConfig = false;
			planet::PlanetManager pm = planet::PlanetManager();

			// Handle queued commands from frontend
			MessageQueueReceiver mq;
			std::vector<MessageQueueCommand> mqRes = mq.receive();
			for(std::vector<MessageQueueCommand>::iterator it = mqRes.begin(); it != mqRes.end(); ++it) {
				MessageQueueCommand mqc = *it;
				if (mqc.cmd == "planetupdate")
				{
					pm.markForUpdate(etoa::toInt(mqc.arg));
				}
				else if (mqc.cmd == "configupdate")
				{
					reloadConfig = true;
				}
			}

			// Update the data and config, everyday once at about 02:17:00 AM
			if ((time(0)-1021)%86400==0) {
				reloadConfig = true;
			}

			// Reload config
			if (reloadConfig) {
				LOG(LOG_INFO, "Reloading configuration"); 
				DataHandler.reloadData();
				Config::instance().reloadConfig();
			}

			/**
			* Start with event handling
			*/
      
      // Market update
			if ((mtime+300) < std::time(0))
			{
				market::MarketHandler* mh = new market::MarketHandler();
				mh->update();
				mtime = std::time(0);
				delete mh;
			}
			
      // Alliance points update
			if ((std::time(0) + 60) % 3600 == 0)
			{
				aPoints::aPointsHandler* aph = new aPoints::aPointsHandler();
				aph->update();
				delete aph;
			}
			
      // Update alliance buildings
			abuilding::aBuildingHandler* abh = new abuilding::aBuildingHandler();
			abh->update();
			delete abh;
			
      // Update alliance technologies
			atech::aTechHandler* ath = new atech::aTechHandler();
			ath->update();
			delete ath;
			
      // Update technologies
			tech::TechHandler* th = new tech::TechHandler();
			th->update(); 
			if (th->changes()) {
				std::vector<int> v = th->getChangedUsers();
				pm.markUsersForUpdate(&v);
			}
			delete th;
			
      // Update buildings
			building::BuildingHandler* bh = new building::BuildingHandler();
			bh->update();
      if (bh->changes()) {
        std::vector<int> v = bh->getChangedPlanets();
        pm.markForUpdate(&v);
      }
      delete bh;
      
      // Update ships
			ship::ShipHandler* sh = new ship::ShipHandler();
			sh->update();  
      if (sh->changes()) {
        std::vector<int> v = sh->getChangedPlanets();
        pm.markForUpdate(&v);
      }
      delete sh;
      
      // Update defenses
			def::DefHandler* dh = new def::DefHandler();
			dh->update();  
      if (dh->changes()) {
        std::vector<int> v = dh->getChangedPlanets();
        pm.markForUpdate(&v);
      }
      delete dh;

      // Update planets which have been marked
      pm.updatePlanets();

      // Process fleet events
			fleet::FleetHandler* fh = new fleet::FleetHandler();
			fh->update(); 
      delete fh;

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
