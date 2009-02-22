
#ifndef __MYSQLHANDLER__
#define __MYSQLHANDLER__

#include <mysql++/mysql++.h>
#include <map>
#include <vector>
#include <string>
#include <fstream>

#include "config/ConfigHandler.h"
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
	private:
		mysqlpp::Connection con_;
		std::map < std::string,std::string > mysql;
		static My* _instance;
		My () {
			loadData();
		
			mysqlpp::Connection con((mysql["DB_DATABASE"]).c_str(),(mysql["DB_SERVER"]).c_str(),(mysql["DB_USER"]).c_str(),(mysql["DB_PASSWORD"]).c_str());

			con_ = con;
			
		}; 
		void loadData () {
			Config &config = Config::instance();
			std::ifstream datein;
			std::string datei = config.getFrontendPath() + "conf.inc.php";
			std::string zeichen;
			std::string value, key;
			std::size_t defineFound, middleFound, endFound;
			datein.open(datei.c_str());
	
			if (datein == false) 
				datein.open("/Applications/xampp/htdocs/etoa/trunk/conf.inc.php");

			while (datein.eof() != true) {
				zeichen += datein.get();
			}
		
			defineFound = zeichen.find("define('");
			while (defineFound!=std::string::npos) {
				middleFound = zeichen.find("','");
				endFound = zeichen.find("');");
				key = "";
				value = "";

				key += zeichen.substr((int)defineFound + 8, (int)middleFound - (int)defineFound - 8);
				value += zeichen.substr((int)middleFound + 3, endFound - (int)middleFound -3);
				mysql.insert ( std::pair<std::string,std::string>(key,value) );

				zeichen.erase(0,endFound+3);
				defineFound = zeichen.find("define('");
			}
		}
		My ( const My& );
		My & operator = (const My &);
	};

#endif
