#include <iostream>
#include <map>

#include <mysql++/mysql++.h>

#include "ConfigHandler.h"


	/*void Config::loadConfig(mysqlpp::Connection* con)
	{
		this->con_ = con;
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
			query << "* ";
		query << "FROM ";
			query << "	config;";
		mysqlpp::Result res = query.store();	
		query.reset();
		
		std::cout << "home\n";
	
		if (res) 
		{
			int resSize = res.size();
			if (resSize>0)
			{
				mysqlpp::Row row;
				for (mysqlpp::Row::size_type i = 0; i<resSize; i++) 
				{
					row = res.at(i);
					std::map<std::string, std::string> temp;
					temp.insert(std::pair<std::string, std::string>("p1", std::string(row["config_param1"])));
					temp.insert(std::pair<std::string, std::string>("p2", std::string(row["config_param2"])));
					temp.insert(std::pair<std::string, std::string>("v", std::string(row["config_value"])));
					//mpconfig.insert(std::pair<std::string, std::map<std::string, std::string> >(std::string(row["config_name"]), temp));
				}
			}
		}
	}
*/
