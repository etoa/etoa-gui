
#ifndef __OBJECT__
#define __OBJECT__

#define MYSQLPP_MYSQL_HEADERS_BURIED
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
		}
		
		virtual ~Object() {}
		
		int getId();
		int getUserId();
		short getTypeId();
		double getCount();
		double getInitCount();
		double getRebuildCount();
		int getEntityId();
		int getFleetId();
		
		bool getIsFaked();
		
		bool getSpecial();
		short getSLevel();
		double getSExp();
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
		short getSBonusReadiness();
		
		void setPercentSurvive(double percentage, int count=-1);
		
		int removeObjects(int count);
		
		void addExp(double exp);
		
		virtual double getWfMetal() = 0;
		virtual double getWfCrystal() = 0;
		virtual double getWfPlastic() = 0;
		
	protected:
		int id;
		short typeId;
		int userId;
		double count, initCount, rebuildCount;
		int entityId, fleetId;
		
		bool isFaked;
		
		bool special;
		short sLevel;
		double sExp;
		short sBonusWeapon, sBonusStructure, sBonusShield, sBonusHeal, sBonusCapacity,  sBonusSpeed, sBonusPilots, sBonusTarn, sBonusAntrax, sBonusForsteal, sBonusBuildDestroy, sBonusAntraxFood, sBonusDeactivade, sBonusReadiness;
		
		bool isChanged;
};

#endif
