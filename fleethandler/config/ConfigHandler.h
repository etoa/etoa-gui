
#ifndef __CONFIGHANDLER__
#define __CONFIGHANDLER__

#include <mysql++/mysql++.h>
#include <map>
#include <vector>
#include "../MysqlHandler.h"

/**
* Config Singleton, very usefull!!!!! So use it .D
* 
* \author Stephan Vock <glaubinix@etoa.ch>
*/

	class Config
	{
	public:
		static Config& instance ()
		{
			static Config _instance;
			return _instance;
		}
		~Config () {};
		std::string get(std::string name, int value);
		double nget(std::string name, int value);
		double idget(std::string name);
	private:
		std::map<std::string, int> sConfig;
		std::map<std::string, double> idConfig;
		std::vector<std::vector<std::string> > cConfig;
		void loadConfig ()
		{
			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
				
			mysqlpp::Query query = con->query();
			query << "SELECT ";
			query << "	config_name, ";
			query << "	config_value, ";
			query << "	config_param1, ";
			query << "	config_param2 ";
			query << "FROM ";
			query << "	config;";
			mysqlpp::Result res = query.store();	
			query.reset();
			if (res) 
			{
				int resSize = res.size();
				if (resSize>0)
				{
					mysqlpp::Row row;
					cConfig.reserve(resSize);
					for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
					{
						row = res.at(i);
						sConfig[std::string(row["config_name"]) ] =  (int)i;
						std::vector<std::string> temp (3);
						temp[1]=std::string(row["config_param1"]);
						temp[2]=std::string(row["config_param2"]);
						temp[0]=std::string(row["config_value"]);
						cConfig.push_back(temp);
					}
				}
			}
			
			idConfig["SHIP_MISC_MSG_CAT_ID"] = 5;
			idConfig["SHIP_MONITOR_MSG_CAT_ID"]= 4;
			
			idConfig["MARKET_SHIP_ID"] = 16;
			idConfig["SPY_TECH_ID"] = 7;
			idConfig["TARN_TECH_ID"] = 11;
			idConfig["STRUCTURE_TECH_ID"] = 9;
			idConfig["SHIELD_TECH_ID"] = 10;
			idConfig["WEAPON_TECH_ID"] = 8;
			idConfig["REGENA_TECH_ID"] = 19;
			
			idConfig["SPY_DEFENSE_FACTOR_TECH"] = 20;
			idConfig["SPY_DEFENSE_FACTOR_SHIPS"] = 0.5;
			idConfig["SPY_DEFENSE_MAX"] = 90;
			idConfig["SPY_DEFENSE_FACTOR_TARN"] = 10;
			
			idConfig["SPY_ATTACK_SHOW_BUILDINGS"] = 1;
			idConfig["SPY_ATTACK_SHOW_RESEARCH"] = 3;
			idConfig["SPY_ATTACK_SHOW_DEFENSE"] = 5;
			idConfig["SPY_ATTACK_SHOW_SHIPS"] = 7;
			idConfig["SPY_ATTACK_SHOW_RESSOURCEN"] = 9;
		};
		static Config* _instance;
		Config () {
			loadConfig();
		 };
		Config ( const Config& );
		Config & operator = (const Config &);
	};


#endif
