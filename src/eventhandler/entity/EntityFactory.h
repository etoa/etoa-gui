
#ifndef __ENTITYFACTORY__
#define __ENTITYFACTORY__


/**
* EntityFactoryClass
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

#include <mysql++/mysql++.h>
#include "../MysqlHandler.h"

#include "Asteroid.h"
#include "Base.h"
#include "Empty.h"
#include "Entity.h"
#include "Market.h"
#include "Nebula.h"
#include "Planet.h"
#include "Star.h"
#include "Unknown.h"
#include "Wormhole.h"


class EntityFactory {

public:	
	static Entity* createEntityById(int id) {
		
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();
		mysqlpp::Query query = con->query();
		query << "SELECT ";
		query << "	* ";
		query << "FROM ";
		query << "	entities ";
		query << "WHERE ";
		query << "	id='" << id << "' ";
		query << "LIMIT 1;";
		mysqlpp::Result eRes = query.store();
		query.reset();
			
		if (eRes) {
			int eSize = eRes.size();
			
			if (eSize>0) {
				mysqlpp::Row eRow = eRes.at(0);
				std::string scode = std::string(eRow["code"]);
				char code = scode[0];
				
				switch (code)
				{
					case 'a':
						return new Asteroid(code, eRow);
						break;
					case 'e':
						return new Empty(code, eRow);
						break;
					case 'm':
						return new Market(code, eRow);
						break;
					case 'n':
						return new Nebula(code, eRow);
						break;
					case 'p':
						return new Planet(code, eRow);
						break;
					case 's':
						return new Star(code, eRow);
						break;
					case 'w':
						return new Wormhole(code, eRow);
						break;
					case 'x':
						return new Base(code, eRow);
						break;
					default:
						return new Unknown('e', eRow);
				}
			}
		}
	}
};

#endif
