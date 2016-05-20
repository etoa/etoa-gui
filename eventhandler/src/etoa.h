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
* Main header file
*
* @author Nicolas Perrenoud<mrcage@etoa.ch>
* 
* Copyright (c) 2004 by EtoA Gaming, www.etoa.net
*
* $Rev$
* $Author$
* $Date$
*/

#define MYSQLPP_MYSQL_HEADERS_BURIED

#include <ctime>
#include <iostream>
#include <iomanip>
#include <cstdlib>	// For system commands
#include <vector>
#include <signal.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <sys/errno.h>
#include <sstream>

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>
#include <boost/thread.hpp>
#include <boost/regex.hpp>
#include <boost/filesystem.hpp>

#include "EventHandler.h"

#include "data/DataHandler.h"
#include "config/ConfigHandler.h"

#include "fleet/FleetHandler.h"
#include "building/BuildingHandler.h"
#include "tech/TechHandler.h"
#include "ship/ShipHandler.h"
#include "def/DefHandler.h"
#include "planet/PlanetManager.h"
#include "market/MarketHandler.h"
//#include "quest/QuestHandler.h"

#include "alliance/aTechHandler.h"
#include "alliance/aBuildingHandler.h"
#include "alliance/aPointsHandler.h"

#include "queue/MessageQueueReceiver.h"

#include "lib/anyoption/anyoption.h"
#include "util/PidFile.h"
#include "util/Log.h"
#include "util/Debug.h"
#include "util/ConfigFile.h"

#ifndef __ETOAMAIN__
#define __ETOAMAIN__

void etoamain();
void msgQueueThread();

#endif       
