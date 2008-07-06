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

#include "fleetActions/cancel/CancelHandler.h" //
#include "fleetActions/default/DefaultHandler.h" //working
#include "fleetActions/return/ReturnHandler.h" //
#include "fleetActions/asteroid/AsteroidHandler.h" //
#include "fleetActions/colonialize/ColonializeHandler.h"
#include "fleetActions/debris/DebrisHandler.h" //
#include "fleetActions/explore/ExploreHandler.h" //
#include "fleetActions/fetch/FetchHandler.h" //
#include "fleetActions/gas/GasHandler.h" //
#include "fleetActions/market/MarketHandler.h" //
#include "fleetActions/nebula/NebulaHandler.h" //
#include "fleetActions/position/PositionHandler.h" //
#include "fleetActions/spy/SpyHandler.h" //
#include "fleetActions/transport/TransportHandler.h" //working
#include "fleetActions/wreckage/WreckageHandler.h" //

#include "battle/BattleHandler.h"
#include "fleetActions/attack/AttackHandler.h"
#include "fleetActions/antrax/AntraxHandler.h"
#include "fleetActions/bombard/BombardHandler.h"

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
	    			
					std::string action = (std::string)row["action"];
				   // char str[] = "";
				   // strcpy( str, row["action"]);
					
					// Nachprüfen ob Landezeit wirklich kleider ist als aktuelle Zeit
					if ((int)row["landtime"] < time)
					{
						// Load action
						switch ((int)row["status"])
						{
							case 0:
							{
								if (action == "collectmetal")
								{
									asteroid::AsteroidHandler* yh = new asteroid::AsteroidHandler(row);
									yh->update();
									delete yh;
									break;
								}
								else if (action == "collectcrystal")
								{
									nebula::NebulaHandler* nh = new nebula::NebulaHandler(row);
									nh->update();
									delete nh;
									break;
								}
								else if (action == "collectdebris")
								{
									wreckage::WreckageHandler* wh = new wreckage::WreckageHandler(row);
									wh->update();
									delete wh;
									break;
								}
								else if (action == "collectgas")
								{
									gas::GasHandler* gh = new gas::GasHandler(row);
									gh->update();
									delete gh;
									break;
								}
								else if (action == "colonize")
								{
									colonialize::ColonializeHandler* kh = new colonialize::ColonializeHandler(row);
									kh->update();
									delete kh;
									break;
								}
								else if (action == "createdebris")
								{
									debris::DebrisHandler* zh = new debris::DebrisHandler(row);
									zh->update();
									delete zh;
									break;
								}
								else if (action == "explore")
								{
									explore::ExploreHandler* jh = new explore::ExploreHandler(row);
									jh->update();
									delete jh;
									break;
								}
								else if (action == "fetch")
								{
									fetch::FetchHandler* fh = new fetch::FetchHandler(row);
									fh->update();
									delete fh;
									break;
								}
								else if (action == "market")
								{
									market::MarketHandler* mh = new market::MarketHandler(row);
									mh->update();
									delete mh;
									break;
								}
								else if (action == "position")
								{
									position::PositionHandler* ph = new position::PositionHandler(row);
									ph->update();
									delete ph;
									break;
								}
								else if (actionn == "spy")
								{
									spy::SpyHandler* sh = new spy::SpyHandler(row);
									sh->update();
									delete sh;
									break;
								}
								else if (action == "transport")
								{
									transport::TransportHandler* th = new transport::TransportHandler(row);
									th->update();
									delete th;
								}
								else
								{
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
						}

						/*	switch (str[0] )
							{     
								case 'a':
								{
									attack::AttackHandler* ah = new attack::AttackHandler(row);
									ah->update();
									delete ah;
									break;
								}
								case 'b':
								{
									bombard::BombardHandler* bh = new bombard::BombardHandler(row);
									bh->update();
									delete bh;
									break;
								}
								case 'x':
								{
									antrax::AntraxHandler* xh = new antrax::AntraxHandler(row);
									xh->update();
									delete xh;
									break;
								}
							} */
					}

		    		
		    	}
			      
			}
			else
			{
				cout << "0 Fleets\n";
			}
		}
		sleep(10);
		
	}		

	return 0;
}
