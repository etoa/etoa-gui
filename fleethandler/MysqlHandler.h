
#ifndef __MYSQLHANDLER__
#define __MYSQLHANDLER__

#include <mysql++/mysql++.h>
#include <map>
#include <vector>

/**
* Mysql Singleton for the Connection Objects
* 
*
* \author Stephan Vock <glaubinix@etoa.ch>
*/

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
		std::string host, user, pwd, db;
		static My* _instance;
		My () {
		
				/*std::cout << "Guten Tag, sehr geehrter User. Bite quälen Sie sich durch das Anmeldeverfahren, danke\nHost?\n";
				std::cin >> host;
				const char* DB_SERVER = host.c_str();
				std::cout << "User?\n";
				std::cin >> user;
				const char* DB_USER = user.c_str();
				std::cout << "Password? (j)a || (n)ein\n";
				std::cin >> pwd;
				if (pwd=="j")
				{
					std::cin >> pwd;
				}
				else pwd = "";
				const char* DB_PASSWORD = pwd.c_str();
				std::cout << "Database?\n";
				std::cin >> db;
				const char* DB_NAME = db.c_str();
				std::cout << "Vielen Dank, Sie werden jetzt mit der dem Server verbunden\n";*/
		
				mysqlpp::Connection con("etoatest","perseus.etoa.ch","etoatest","etoatest");
				
				con_ = con;
			
		 }; 
		 My ( const My& );
		 My & operator = (const My &);
	};

#endif
