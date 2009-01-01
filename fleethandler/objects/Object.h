
#ifndef __OBJECT__
#define __OBJECT__

#include <mysql++/mysql++.h>
#include <math.h>

#include "../MysqlHandler.h"
#include "../config/ConfigHandler.h"
#include "../data/DataHandler.h"

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
			
			this->rebuildCount = 0;
			
			this->isFaked = false;
			this->isChanged = false;
			this->rebuildIsCalced = false;
		}
		
		virtual ~Object() {}
		
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
		
		void setPercentSurvive(double percentage);
		
		virtual double getWfMetal() = 0;
		virtual double getWfCrystal() = 0;
		virtual double getWfPlastic() = 0;
		
		
		virtual void save() = 0;
		
	protected:
		int id;
		short typeId;
		int userId;
		int count, initCount, rebuildCount;
		int entityId, fleetId;
		
		bool isFaked;
		
		bool special;
		short sLevel;
		int sExp;
		short sBonusWeapon, sBonusStructure, sBonusShield, sBonusHeal, sBonusCapacity,  sBonusSpeed, sBonusPilots, sBonusTarn, sBonusAntrax, sBonusForsteal, sBonusBuildDestroy, sBonusAntraxFood, sBonusDeactivade;
		
		bool isChanged;
		bool rebuildIsCalced;
};

#endif
