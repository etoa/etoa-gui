
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
	private:
		std::map<std::string, int> sConfig;
		std::vector<std::vector<std::string> > cConfig;
		void loadConfig ()
		{
			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
				
			mysqlpp::Query query = con->query();
			query << "SELECT ";
				query << "* ";
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
						temp[0]=std::string(row["config_param1"]);
						temp[1]=std::string(row["config_param2"]);
						temp[2]=std::string(row["config_value"]);
						cConfig.push_back(temp);
					}
				}
			}
		};
		static Config* _instance;
		Config () {
			loadConfig();
		 };
		Config ( const Config& );
		Config & operator = (const Config &);
	};


#endif
