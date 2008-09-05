#include <iostream>
#include <set>
#include <vector>
#include "../MysqlHandler.h"

#include <mysql++/mysql++.h>

#include "ConfigHandler.h"

	/** Liefert die Configwerte als string **/
	std::string Config::get(std::string name, int value)
	{
		std::vector<std::string> temp (3);
		temp = cConfig.at(sConfig[name]);
		return(temp[value]);
	}
	
	/** Liefert die Configwerte als double **/
	double Config::nget(std::string name, int value)
	{
		std::string temp = get(name, value);
		double var = atof(temp.data());
		return(var);
	}
	
	/** Liefert die Zahlenwerte gespeicherter Werte **/
	double Config::idget(std::string name)
	{
		return(idConfig[name]);
	}
	
	/**	Initialisiert die Configwerte **/
	void Config::loadConfig ()
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
		if (res) {
			int resSize = res.size();
			if (resSize>0) {
				mysqlpp::Row row;
				cConfig.reserve(resSize);
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) {
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

		/** ID Werte (müssen manuel hier reingeschrieben werden) **/
		//->Nachrichten
		idConfig["SHIP_WAR_MSG_CAT_ID"] = 3;
		idConfig["SHIP_MONITOR_MSG_CAT_ID"] = 4;
		idConfig["SHIP_MISC_MSG_CAT_ID"] = 5;
		
		//->Schiffe
		idConfig["MARKET_SHIP_ID"] = 16;
		
		//->Technologien
		idConfig["SPY_TECH_ID"] = 7;
		idConfig["STRUCTURE_TECH_ID"] = 9;
		idConfig["TARN_TECH_ID"] = 11;
		idConfig["SHIELD_TECH_ID"] = 10;
		idConfig["WEAPON_TECH_ID"] = 8;
		idConfig["BOMB_TECH_ID"] = 15;
		idConfig["Gifttechnologie"] = 18;
		idConfig["REGENA_TECH_ID"] = 19;
		
		//->Gebäude
		idConfig["FLEET_CONTROL_ID"] = 1;
		idConfig["FACTORY_ID"] = 10;
		idConfig["SHIPYARD_ID"] = 9;
		idConfig["BUILD_MISSILE_ID"] = 24;
		idConfig["BUILD_CRYPTO_ID"] = 25;		
		
		//->Spionage
		idConfig["SPY_DEFENSE_FACTOR_TECH"] = 20;
		idConfig["SPY_DEFENSE_FACTOR_SHIPS"] = 0.5;
		idConfig["SPY_DEFENSE_MAX"] = 90;
		idConfig["SPY_DEFENSE_FACTOR_TARN"] = 10;
			
		idConfig["SPY_ATTACK_SHOW_BUILDINGS"] = 1;
		idConfig["SPY_ATTACK_SHOW_RESEARCH"] = 3;
		idConfig["SPY_ATTACK_SHOW_DEFENSE"] = 5;
		idConfig["SPY_ATTACK_SHOW_SHIPS"] = 7;
		idConfig["SPY_ATTACK_SHOW_RESSOURCEN"] = 9;
	}
