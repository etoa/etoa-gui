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
#include "config/ConfigHandler.h"
#include "MysqlHandler.h"

#include "fleetActions/analyze/AnalyzeHandler.h" // working
#include "fleetActions/asteroid/AsteroidHandler.h" //tested, working, startvalues included
#include "fleetActions/return/ReturnHandler.h" //tested, working
#include "fleetActions/cancel/CancelHandler.h" //tested, working
#include "fleetActions/default/DefaultHandler.h" //working
#include "fleetActions/colonialize/ColonializeHandler.h"
#include "fleetActions/debris/DebrisHandler.h" //tested, working
#include "fleetActions/explore/ExploreHandler.h" //working (without settings)
#include "fleetActions/fetch/FetchHandler.h" //
#include "fleetActions/gas/GasHandler.h" //tested, working
#include "fleetActions/market/MarketHandler.h" //
#include "fleetActions/nebula/NebulaHandler.h" // tested, working
#include "fleetActions/position/PositionHandler.h" //tested, working
#include "fleetActions/spy/SpyHandler.h" //
#include "fleetActions/transport/TransportHandler.h" //working
#include "fleetActions/wreckage/WreckageHandler.h" //working, tested
#include "fleetActions/support/SupportHandler.h" //

#include "battle/BattleHandler.h"
#include "fleetActions/attack/AttackHandler.h"
#include "fleetActions/antrax/AntraxHandler.h"
#include "fleetActions/bombard/BombardHandler.h"

using namespace std;


main(int argc, char *argv[])
{	

	//Initialize Mysql Connection
	My &my = My::instance();
	mysqlpp::Connection *con_;
	con_ = my.get();
	mysqlpp::Query query = con_->query();
	
	//Loading Configdata
	Config &config = Config::instance();

	//Initialize Gasplanets
	functions::initGasPlanets();
	
	// Main loop
	while (true) {	
		//Timestamp
		std::time_t time = std::time(0);
		
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
		query << "	landtime<" << time << " ;";
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
					
					std::cout << "User: " << row["user_id"] << " Zeit: " << row["landtime"] << " Aktion: " << action << "\n";
					
					// Nachprüfen ob Landezeit wirklich kleider ist als aktuelle Zeit
					if ((int)row["landtime"] < time) {
						// Load action
						switch ((int)row["status"])
						{
							case 0:
							{
								if (action == "analyze") {
									analyze::AnalyzeHandler* lh = new analyze::AnalyzeHandler(row);
									lh->update();
									delete lh;
								}
								else if (action == "antrax") {
									antrax::AntraxHandler* xh = new antrax::AntraxHandler(row);
									xh->update();
									delete xh;
								}
								else if (action == "attack") {
									attack::AttackHandler* ah = new attack::AttackHandler(row);
									ah->update();
									delete ah;
								}
								else if (action == "bombard") {
									bombard::BombardHandler* bh = new bombard::BombardHandler(row);
									bh->update();
									delete bh;
								}
								else if (action == "collectmetal") {
									asteroid::AsteroidHandler* yh = new asteroid::AsteroidHandler(row);
									yh->update();
									delete yh;
								}
								else if (action == "collectcrystal") {
									nebula::NebulaHandler* nh = new nebula::NebulaHandler(row);
									nh->update();
									delete nh;
								}
								else if (action == "collectdebris") {
									wreckage::WreckageHandler* wh = new wreckage::WreckageHandler(row);
									wh->update();
									delete wh;
								}
								else if (action == "collectfuel") {
									gas::GasHandler* gh = new gas::GasHandler(row);
									gh->update();
									delete gh;
								}
								else if (action == "colonize") {
									colonialize::ColonializeHandler* kh = new colonialize::ColonializeHandler(row);
									kh->update();
									delete kh;
								}
								else if (action == "createdebris") {
									debris::DebrisHandler* zh = new debris::DebrisHandler(row);
									zh->update();
									delete zh;
								}
								else if (action == "explore") {
									explore::ExploreHandler* jh = new explore::ExploreHandler(row);
									jh->update();
									delete jh;
								}
								else if (action == "fetch") {
									fetch::FetchHandler* fh = new fetch::FetchHandler(row);
									fh->update();
									delete fh;
								}
								else if (action == "market") {
									market::MarketHandler* mh = new market::MarketHandler(row);
									mh->update();
									delete mh;
								}
								else if (action == "position") {
									position::PositionHandler* ph = new position::PositionHandler(row);
									ph->update();
									delete ph;
								}
								else if (action == "spy") {
									spy::SpyHandler* sh = new spy::SpyHandler(row);
									sh->update();
									delete sh;
								}
								else if (action == "support") {
									support::SupportHandler* ch = new support::SupportHandler(row);
									ch->update();
									delete ch;
								}
								else if (action == "transport") {
									transport::TransportHandler* th = new transport::TransportHandler(row);
									th->update();
									delete th;
								}
								else {
									defaul::DefaultHandler* dh = new defaul::DefaultHandler(row);
									dh->update();
									delete dh;
								}
								break;
							}
							case 1:
							{
								retour::ReturnHandler* rh = new retour::ReturnHandler(row);
								rh->update();
								delete rh;
								break;
							}
							case 2:
							{
								cancel::CancelHandler* ch = new cancel::CancelHandler(row);
								ch->update();
								delete ch;
								break;
							}
							case 4:
							{
								if (action == "support")
								{
									support::SupportHandler* ch = new support::SupportHandler(row);
									ch->update();
									delete ch;
								}
								break;
							}
						}
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
