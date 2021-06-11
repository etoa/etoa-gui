
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
		static My& instance() {
			static My _instance;
			return _instance;
		}

		~My ();

		mysqlpp::Connection* get();

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
		My ();
		void loadData ();
		My ( const My& );
		My & operator = (const My &);
	};

#endif
