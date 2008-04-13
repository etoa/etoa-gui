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

#include "fleetActions/cancel/CancelHandler.h" //working
#include "fleetActions/default/DefaultHandler.h" //working
#include "fleetActions/return/ReturnHandler.h" //working
#include "fleetActions/asteroid/AsteroidHandler.h" //working
#include "fleetActions/colonialize/ColonializeHandler.h"
#include "fleetActions/debris/DebrisHandler.h" //working
#include "fleetActions/explore/ExploreHandler.h" //working
#include "fleetActions/fetch/FetchHandler.h" //working
#include "fleetActions/gas/GasHandler.h" //working
#include "fleetActions/market/MarketHandler.h" //working
#include "fleetActions/nebula/NebulaHandler.h" //working
#include "fleetActions/position/PositionHandler.h" //working
#include "fleetActions/spy/SpyHandler.h" //working
#include "fleetActions/transport/TransportHandler.h" //working
#include "fleetActions/wreckage/WreckageHandler.h" //working

using namespace std;


main(int argc, char *argv[])
{	

	My &my = My::instance();
	mysqlpp::Connection *con_;
	con_ = my.get();
	mysqlpp::Query query = con_->query();

	// Main loop
	while (true)
	{	
		//Timestamp
		std::time_t time = std::time(0);
		
		Config &config = Config::instance();
		
		// Graphical bling-bling
		system("clear");
		cout << "----------------------------------------------------------------\n";
		cout << "- EtoA Fleethandler, (C) 2007 by EtoA Gaming, Time: "<< time <<" -\n";
		cout << "----------------------------------------------------------------\n\n";
		
		//Fleetquery
		query << "SELECT ";
		query << "	* ";
		query << "FROM ";
		query << "	fleet ";
		query << "WHERE ";
		query << "	fleet_landtime<" << time << " ;";
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
							delete ch;
						}
						else if (str[1]=='r')
						{
							retour::ReturnHandler* rh = new retour::ReturnHandler(row);
							rh->update();
							delete rh;
						}
						else
						{
							switch (str[0] )
							{     
								case 'f':
								{
									fetch::FetchHandler* fh = new fetch::FetchHandler(row);
									fh->update();
									delete fh;
									break;
								}
								case 'g':
								{
									gas::GasHandler* gh = new gas::GasHandler(row);
									gh->update();
									delete gh;
									break;
								}
								case 'j':
								{
									explore::ExploreHandler* jh = new explore::ExploreHandler(row);
									jh->update();
									delete jh;
									break;
								}
								case 'k':
								{
									colonialize::ColonializeHandler* kh = new colonialize::ColonializeHandler(row);
									kh->update();
									delete kh;
									break;
								}
								case 'm':
								{
									market::MarketHandler* mh = new market::MarketHandler(row);
									mh->update();
									delete mh;
									break;
								}
								case 'n':
								{
									nebula::NebulaHandler* nh = new nebula::NebulaHandler(row);
									nh->update();
									delete nh;
									break;
								}
								case 'p':
								{
									position::PositionHandler* ph = new position::PositionHandler(row);
									ph->update();
									delete ph;
									break;
								}
								case 's':
								{
									spy::SpyHandler* sh = new spy::SpyHandler(row);
									sh->update();
									delete sh;
									break;
								}
								case 't':
								{
									transport::TransportHandler* th = new transport::TransportHandler(row);
									th->update();
									delete th;
									break;
								}
								case 'w':
								{
									wreckage::WreckageHandler* wh = new wreckage::WreckageHandler(row);
									wh->update();
									delete wh;
									break;
								}
								case 'y':
								{
									asteroid::AsteroidHandler* yh = new asteroid::AsteroidHandler(row);
									yh->update();
									delete yh;
									break;
								}
								case 'z':
								{
									debris::DebrisHandler* zh = new debris::DebrisHandler(row);
									zh->update();
									delete zh;
									break;
								}
								default :
								{
									defaul::DefaultHandler* dh = new defaul::DefaultHandler(row);
									dh->update();
									delete dh;
								}
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
		//sleep(4);
		
	}		

	return 0;
}
