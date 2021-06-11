#include "MysqlHandler.h"

My::~My() {};

mysqlpp::Connection* My::get() {
	return &con_;
};

My::My() {
	loadData();

	mysqlpp::Connection con((mysql["database"]).c_str(),(mysql["host"]).c_str(),(mysql["user"]).c_str(),(mysql["password"]).c_str());

	if (!con) {
		std::cerr << "Database connection failed: " << con.error() << std::endl;
		exit(EXIT_FAILURE);
	}
	con_ = con;

};

void My::loadData () {

	mysql.insert ( std::pair<std::string,std::string>("host", Config::instance().getAppConfigValue("mysql","host")) );
	mysql.insert ( std::pair<std::string,std::string>("database", Config::instance().getAppConfigValue("mysql","database")) );
	mysql.insert ( std::pair<std::string,std::string>("user", Config::instance().getAppConfigValue("mysql","user")) );
	mysql.insert ( std::pair<std::string,std::string>("password", Config::instance().getAppConfigValue("mysql","password")) );

}
