
#ifndef __MYSQLHANDLER__
#define __MYSQLHANDLER__

#define MYSQLPP_MYSQL_HEADERS_BURIED

#include <mysql++/mysql++.h>
#include <map>
#include <vector>
#include <string>
#include <fstream>

#include "config/ConfigHandler.h"
#include "util/ConfigFile.h"

/**
* Mysql Singleton for the Connection Objects
* 
*
* \author Stephan Vock <glaubinix@etoa.ch>
*/

	class Config;
	
	class My
	{
	public:
		static My& instance ()
		{
			static My _instance;
			return _instance;
		}
		~My () {};
		
		mysqlpp::Connection* get()
		{
			return &con_;
		};

#if MYSQLPP_HEADER_VERSION <= MYSQLPP_VERSION(2,3,2)
#define RESULT_TYPE mysqlpp::Result
		inline my_ulonglong insert_id(mysqlpp::Query &q) {
			return con_.insert_id();
		}
		inline my_ulonglong affected_rows(mysqlpp::Query &q) {
			return con_.affected_rows();
		}
#else
#define RESULT_TYPE mysqlpp::StoreQueryResult
		inline my_ulonglong insert_id(mysqlpp::Query &q) {
			return q.insert_id();
		}
		inline my_ulonglong affected_rows(mysqlpp::Query &q) {
			return q.affected_rows();
		}
#endif

	private:
		mysqlpp::Connection con_;
		std::map < std::string,std::string > mysql;
		static My* _instance;
		My () {
			loadData();

			mysqlpp::Connection con((mysql["database"]).c_str(),(mysql["host"]).c_str(),(mysql["user"]).c_str(),(mysql["password"]).c_str());
			
			if (!con) {
				std::cerr << "Database connection failed: " << con.error() << std::endl;
				exit(EXIT_FAILURE);
			}
			con_ = con;
			
		}; 
		void loadData () {
			Config &config = Config::instance();
			std::ifstream datein;
			std::string dbCfgFile = config.getConfigDir()+"/db.cfg";
			
		  	ConfigFile cf(dbCfgFile);
			mysql.insert ( std::pair<std::string,std::string>("host",cf.Value("mysql","host")) );
			mysql.insert ( std::pair<std::string,std::string>("database",cf.Value("mysql","database")) );
			mysql.insert ( std::pair<std::string,std::string>("user",cf.Value("mysql","user")) );
			mysql.insert ( std::pair<std::string,std::string>("password",cf.Value("mysql","password")) );

		}
		My ( const My& );
		My & operator = (const My &);
	};

#endif
