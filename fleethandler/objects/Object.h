
#ifndef __OBJECT__
#define __OBJECT__

#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"

/**
* Object class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class Object	
{
	public:
		Object(mysqlpp::Row &oRow) {
			this->entityId = 0;
			this->fleetId = 0;
			this->isFaked = false;
			this->isChanged = false;
		}
		
		int getId();
		int getUserId();
		short getTypeId();
		int getCount();
		int getInitCount();
		int getEntityId();
		int getFleetId();
		
		bool getIsFaked();
		
		bool getSpecial();
		short getSLevel();
		int getSExp();
		short getSBonusWeapon();
		short getSBonusStructure();
		short getSBonusShield();
		short getSBonusHeal();
		short getSBonusCapacity();
		short getSBonusSpeed();
		short getSBonusPilots();
		short getSBonusTarn();
		short getSBonusAntrax();
		short getSBonusForsteal();
		short getSBonusBuildDestroy();
		short getSBonusAntraxFood();
		short getSBonusDeactivade();
		
		
		virtual void save() = 0;
		
	protected:
		int id;
		short typeId;
		int userId;
		int count, initCount;
		int entityId, fleetId;
		
		bool isFaked;
		
		bool special;
		short sLevel;
		int sExp;
		short sBonusWeapon, sBonusStructure, sBonusShield, sBonusHeal, sBonusCapacity,  sBonusSpeed, sBonusPilots, sBonusTarn, sBonusAntrax, sBonusForsteal, sBonusBuildDestroy, sBonusAntraxFood, sBonusDeactivade;
		
		bool isChanged;
};

#endif
