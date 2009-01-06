#include <iomanip>
#include <iostream>
#include "Functions.h"
#include "../MysqlHandler.h"
#include "../config/ConfigHandler.h"

namespace functions
{
	
	/** Liefet den Namen der Aktion zurück **/
	std::string fa(std::string fAction)
	{
		return fAction;
	}
	
	/** Formatiert eine Zahl **/
	std::string nf(std::string  value)
	{
		/** Schneidet den Rest ab, wenn ein Punkt und Nachkommazeichen vorhanden sind **/
		std:size_t found = value.find(".");
		if (found!=std::string::npos) {
			value.erase(value.begin()+(int)found,value.end());
		}
		
		/** Fügt die Tausenderzeichen hinzu **/
		int length = value.length();
		int i=3;
		while (length > i) {
			int toDo = length-i;
			value.insert(toDo, "`");
			i += 3;
					
		}
		return(value);
	}
	
	/** Wandelt eine Zahl in einen String um **/
	std::string d2s(double number)
	{
		std::ostringstream Str;
		Str << std::setprecision(18);
		Str << number;
		std::string zAs(Str.str());
		return zAs;
	}
	
	/** Formatiert einen Timestamp **/
	std::string formatTime(int time)
	{
		time_t Zeitstempel;
		tm *now;
		Zeitstempel = time;
		now = localtime(&Zeitstempel);
		
		int day = now->tm_mday;
		int month = now->tm_mon+1;
		int year = now->tm_year+1900;
		int hour = now->tm_hour;
		int min = now->tm_min;
		int sec = now->tm_sec;
		
		std::stringstream tmp1, tmp2, tmp3, tmp4, tmp5, tmp6;
		std::string sday, smonth, syear, shour, smin, ssec;
		tmp1 << day;
		tmp1 >> sday;
		tmp2 << month;
		tmp2 >> smonth;
		tmp3 << year;
		tmp3 >> syear;
		tmp4 << hour;
		tmp4 >> shour;
		tmp5 << min;
		tmp5 >> smin;
		tmp6 << sec;
		tmp6 >> ssec;
		
		std::string ftime = sday;
		ftime += ".";
		ftime += smonth;
		ftime += ".";
		ftime += syear;
		ftime += ", ";
		if (hour < 10)
			ftime += "0";
		ftime += shour;
		ftime += ":";
		if (min < 10)
			ftime += "0";
		ftime += smin;
		ftime += ":";
		if (sec < 10)
			ftime += "0";
		ftime += ssec;
		return(ftime);

	}
	
	/** Aktualisiert die Werte eines Gasplaneten **/
	void updateGasPlanet(int planetId)
	{
	
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		Config &config = Config::instance();
		std::time_t time = std::time(0);
		
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	id, ";
		query << "	planet_res_fuel, ";
		query << "	planet_fields, ";
		query << "	planet_last_updated ";
		query << "FROM ";
		query << "	planets ";
		query << "WHERE ";
		std::cout << "ta\n";
		query << "	planet_type_id='" << config.get("gasplanet", 0) << "' ";
		query << "	 AND id='" << planetId << "';";
		std::cout << "ta\n";
		mysqlpp::Result res = query.store();
		query.reset();
		
		if (res)  {
			int resSize = res.size();
			if (resSize>0) {
				mysqlpp::Row row = res.at(0);
				int ptime = time;
				int last = (int)row["planet_last_updated"];
				if (last == 0) last = ptime;
				double tlast = ptime - last;
				double pfuel = (double)row["planet_res_fuel"];
				tlast += pfuel;
					
				double pSize = (int)config.nget("gasplanet", 2)*int(row["planet_fields"]);
				double fuel = std::min(tlast,pSize); //ToDo Gas param1 + 2
				
				query << std::setprecision(18);
				query << "UPDATE ";
				query << "	planets ";
				query << "SET ";
				query << "	planet_res_fuel='" << fuel << "', ";
				query << "	planet_last_updated='" << time << "' ";
				query << "WHERE ";
				query << "	id='" << planetId << "';";
				query.store();
				query.reset();
			}
		}
	}
	
	/** Initialisiert alle Gasplaneten mit der erstmöglichen Loginzeit **/
	void initGasPlanets()
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		Config &config = Config::instance();
		
		mysqlpp::Query query = con_->query();
		query << "UPDATE ";
		query << "	planets ";
		query << "SET ";
		query << "	planet_last_updated='" << config.get("enable_login",1) << "' ";
		query << "WHERE ";
		query << "	planet_type_id='" << config.get("gasplanet", 0) << "' ";
		query << " AND planet_last_updated<'" << config.get("enable_login",1) << "';";
		query.store();
		query.reset();
	}
}
