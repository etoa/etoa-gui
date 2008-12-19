
#ifndef __ENTITYFACTORY__
#define __ENTITYFACTORY__


/**
* EntityFactoryClass
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

#include <mysql++/mysql++.h>
#include "MysqlHandler.h"

#include "Alliance.h"
#include "Asteroidfield.h"
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

	static Entity* createEntity(char code) {
		switch (code)
		{
			case 'a':
				return new Asteroidfield;
				break;
			case 'e':
				return new Empty;
				break;
			case 'm':
				return new Market;
				break;
			case 'n':
				return new Nebula;
				break;
			case 'p':
				return new Planet;
				break;
			case 's':
				return new Star;
				break;
			case 'w':
				return new Wormhole;
				break;
			case 'x':
				return new Alliance;
				break;
			default:
				return new Unknown;
		}
	}
	
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
				char code = eRow["code"];
				
				switch (code)
				{
					case 'a':
						return new Asteroidfield(code, eRow);
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
						return new Alliance(code, eRow);
						break;
					default:
						return new Unknown('e', eRow);
				}
			}
		}
	}
};

#endif
