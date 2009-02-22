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

#include <mysql++/mysql++.h>
#include <boost/thread.hpp>

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

#include "lib/anyoption/anyoption.h"
#include "util/IPCMessageQueue.h"
#include "util/Logger.h"
#include "util/PidFile.h"

#ifndef __ETOAMAIN__
#define __ETOAMAIN__

void etoamain(std::string gameRound);

#endif       
