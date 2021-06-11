
#ifndef __ListDef__
#define __ListDef__

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"
#include "Object.h"

/**
* DefList class
*
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class ListDef : public Object
{
public:
	ListDef(mysqlpp::Row &oRow,double rebuild=1.0);
	~ListDef();

	double getWfMetal();
	double getWfCrystal();
	double getWfPlastic();

private:
	int getDefCnt();
	double rebuild;
};

#endif
