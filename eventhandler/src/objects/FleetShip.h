
#ifndef __FLEETSHIP__
#define __FLEETSHIP__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "Object.h"

/**
* FleetShip class
*
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class ShipData;

class FleetShip : public Object
{
public:
	FleetShip(mysqlpp::Row &oRow);
	~FleetShip();

	double getWfMetal();
	double getWfCrystal();
	double getWfPlastic();

private:
	int getShipCnt(ShipData *data);
};

#endif
