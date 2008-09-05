
#ifndef __SHOWOBJECTHANDLER__
#define __SHOWOBJECTHANDLER__

#include <mysql++/mysql++.h>
#include "../MysqlHandler.h"

/**
* ObjectType class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class ShowObjectHandler	
{
public:
	/**
	* Eventhandler constructor for all handler classes.
	* Sets the internal MySQL connection pointer
	*/
	ShowObjectHandler(mysqlpp::Row object, short type) {
		
		this->oid = (int)object["id"];
		this->type = type;
		this->cnt = 0;
		this->repairCnt=0;
	};
	
	int oid;
	short type;
	double cnt, repairCnt;
	
private:


};

#endif
