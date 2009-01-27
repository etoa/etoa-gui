#include <ctime>
#include <iostream>
#include <iomanip>
#include <cstdlib>	// For system commands
#include <vector>
#include <mysql++/mysql++.h>

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

#ifndef __ETOAMAIN__
#define __ETOAMAIN__

void etoamain();

#endif       
