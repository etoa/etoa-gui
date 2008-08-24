
#ifndef __BATTLEHANDLER__
#define __BATTLEHANDLER__

#include <mysql++/mysql++.h>
#include <vector>

#include "../MysqlHandler.h"

/**
* Handles battles....
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/

class BattleHandler
{
	public:
		BattleHandler(mysqlpp::Connection *con,mysqlpp::Row fleet) {
			this->fleet_ = fleet;
			this->con_ = con;
			this->shipSteal = 50;
		 }
		void update();
		void battle();
		void loadSpecial();
		
		std::string msgFight, msg;
		
		double specialShipBonusAntrax;
        double specialShipBonusForsteal;
        double specialShipBonusBuildDestroy;
        double specialShipBonusAntraxFood;
        double specialShipBonusDeactivade;
		
		bool dontSteal;
		int shipSteal;
		float resRaidFactor;
		
		bool alliancesHaveWar;
		
		
		
		short runde;
		
		std::vector<double> wf;
		std::vector<double> raidRToShip;
		std::vector<double> raidR;
		
		short returnV;
		std::string bstat, bstat2;
		bool returnFleet;
		
		mysqlpp::Row fleet_;
		mysqlpp::Connection *con_;
		
};
#endif
