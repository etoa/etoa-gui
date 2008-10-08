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
#include "objectData/ObjectDataHandler.h"

#include "fleetActions/analyze/AnalyzeHandler.h"
#include "fleetActions/antrax/AntraxHandler.h"
#include "fleetActions/asteroid/AsteroidHandler.h"
#include "fleetActions/attack/AttackHandler.h"
#include "fleetActions/bombard/BombardHandler.h"
#include "fleetActions/cancel/CancelHandler.h"
#include "fleetActions/colonialize/ColonializeHandler.h"
#include "fleetActions/debris/DebrisHandler.h"
#include "fleetActions/default/DefaultHandler.h"
#include "fleetActions/emp/EmpHandler.h"
#include "fleetActions/explore/ExploreHandler.h"
#include "fleetActions/fetch/FetchHandler.h"
#include "fleetActions/gas/GasHandler.h"
#include "fleetActions/gattack/GattackHandler.h"
#include "fleetActions/invade/InvadeHandler.h"
#include "fleetActions/market/MarketHandler.h"
#include "fleetActions/nebula/NebulaHandler.h"
#include "fleetActions/position/PositionHandler.h"
#include "fleetActions/return/ReturnHandler.h"
#include "fleetActions/spy/SpyHandler.h"
#include "fleetActions/steal/StealHandler.h"
#include "fleetActions/stealth/StealthHandler.h"
#include "fleetActions/support/SupportHandler.h"
#include "fleetActions/transport/TransportHandler.h"
#include "fleetActions/wreckage/WreckageHandler.h"
#include "fleetActions/delivery/DeliveryHandler.h"
#include "fleetActions/alliance/AllianceHandler.h"

#include "battle/BattleHandler.h"

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
	
	//Loadgin Shipdate
	objectData &objectData = objectData::instance();

	//Initialize Gasplanets
	functions::initGasPlanets();
	
	// Main loop
	while (true) {	
	
		//Timestamp
		std::time_t time = std::time(0);
		
		/** Update the data, everyday once at about 02:17:00 AM **/
		if ((time-1021)%86400==0) {
			objectData.reloadData();
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
								if (action == "alliance") {
									alliance::AllianceHandler* ah = new alliance::AllianceHandler(row);
									ah->update();
									delete ah;
								}
								else if (action == "analyze") {
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
								else if (action == "delivery") {
									delivery::DeliveryHandler* dh = new delivery::DeliveryHandler(row);
									dh->update();
									delete dh;
								}									
								else if (action == "emp") {
									emp::EmpHandler* eh = new emp::EmpHandler(row);
									eh->update();
									delete eh;
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
								else if (action == "gasattack") {
									gattack::GattackHandler* gh = new gattack::GattackHandler(row);
									gh->update();
									delete gh;
								}
								else if (action == "invade") {
									invade::InvadeHandler* ih = new invade::InvadeHandler(row);
									ih->update();
									delete ih;
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
								else if (action == "spyattack") {
									steal::StealHandler* sh = new steal::StealHandler(row);
									sh->update();
									delete sh;
								}
								else if (action == "stealthattack") {
									stealth::StealthHandler* sh = new stealth::StealthHandler(row);
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
							case 3:
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
		
		sleep(5);
	}		

	return 0;
}
