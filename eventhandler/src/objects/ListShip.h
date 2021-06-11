
#ifndef __LISTSHIP__
#define __LISTSHIP__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "Object.h"

/**
* ListShip class
*
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class ShipData;

class ListShip : public Object
{
public:
	ListShip(mysqlpp::Row &oRow);
	~ListShip();

	double getWfMetal();
	double getWfCrystal();
	double getWfPlastic();

	void save();

private:
	int getShipCnt(ShipData* data);
};

#endif
