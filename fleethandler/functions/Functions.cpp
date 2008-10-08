#include <iomanip>
#include <iostream>
#include "Functions.h"
#include "../MysqlHandler.h"
#include "../config/ConfigHandler.h"

namespace functions
{
	/** Versendet eine Nachricht **/
	void sendMsg(int userId, int msgType, std::string subject, std::string text)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		mysqlpp::Query query = con_->query();
		query << "INSERT INTO ";
		query << "	messages ";
		query << "(";
		query << "	message_user_from, ";
		query << "	message_user_to, ";
		query << "	message_timestamp, ";
		query << "	message_cat_id ";
		query << ") ";
		query << "VALUES ";
		query << "('0', '";
		query << userId << "', '";
		query << time(0) << "', '";
		query << msgType << "' ";
		query << ");";
		query.store();
		query.reset();
		
		query << "INSERT INTO ";
		query << "	message_data ";
		query << "(";
		query << "	id, ";
		query << "	subject, ";
		query << "	text ";
		query << ") ";
		query << "VALUES ";
		query << "('" << con_->insert_id() << "', ";
		query << "'" << subject << "', ";
		query << "'" << text << "' ";
		query << ");";
		query.store();
		query.reset();
	}
	
	/** Schreibt einen Logeintrag (Alle Aktionen müssen gelogt werden!!) **/
	void addLog(int logCat, std::string logText, std::time_t logTimestamp)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		if (logTimestamp==0) {
		 	logTimestamp = std::time(0);
		}
		
		std::time_t time = std::time(0);
		
		mysqlpp::Query query = con_->query();
		query << "INSERT INTO logs ";
		query << "(";
		query << "	log_cat, ";
		query << "	log_timestamp, ";
		query << "	log_realtime, ";
		query << "	log_text ";
		query << ") ";
		query << "VALUES ";
		query << "('" << logCat << "', ";
		query << "'" << logTimestamp << "', ";
		query << "'" << time << "', ";
		query << "'" << logText << "');"; //addslashes(log_text)
		query.store();
		query.reset();
	}
	
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
	
	/** Liefert den Nich anhand der PlanetenId **/
	std::string getUserNick(int userId)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
			query << "user_nick ";
		query << "FROM ";
			query << "users ";
		query << "WHERE ";
			query << "user_id='" << userId << "';";
		mysqlpp::Result res = query.store();		
		query.reset();

		if (res) {
			int resSize = res.size();			
    	
			if (resSize>0) {
				mysqlpp::Row row;
				row = res.at(0);
				return (std::string(row["user_nick"]));
			}
			else {
				return "[i]Unbekannter Benutzer[/i]";
			}
		}
		else {
			return "[i]Unbekannter Benutzer[/i]";
		}
	}
	
	/** Liefert die User Id anhand der Planeten Id **/
	int getUserIdByPlanet(int planetId)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	planet_user_id ";
		query << "FROM ";
		query << "	planets ";
		query << "WHERE ";
		query << "	id='" << planetId << "';";
		mysqlpp::Result pRes = query.store();
		query.reset();
		
		if (pRes) {
			int pSize = pRes.size();
			
			if (pSize > 0) {
				mysqlpp::Row pRow = pRes.at(0);
				return (int)pRow["planet_user_id"];
			}
			else {
				return 0;
			}
		}
	}

	/** Liefert die Koordinaten einer Entity **/
	std::string formatCoords(int entityId, short blank)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	c.id, ";
		query << "	c.sx, ";
		query << "	c.sy, ";
		query << "	c.cx, ";
		query << "	c.cy, ";
		query << "	e.pos, ";
		query << "	e.code, ";
		query << "	p.planet_name ";
		query << "FROM entities AS e ";
		query << "INNER JOIN cells AS c ";
		query << "	ON e.cell_id = c.id ";
		query << "	AND e.id = '" << entityId << "' ";
		query << "LEFT JOIN planets AS p ";
		query << "	ON p.id = e.id ";
		mysqlpp::Result coordsRes = query.store();	
		query.reset();
						
		if (coordsRes) {
			int coordsSize = coordsRes.size();
			
			if (coordsSize > 0) {
				mysqlpp::Row coordsRow;
				coordsRow = coordsRes.at(0);

				std::string coords = (std::string)coordsRow["sx"]; 
				coords += "/";
				coords += (std::string)coordsRow["sy"];
				coords += " : ";
				coords += (std::string)coordsRow["cx"];
				coords += "/";
				coords += (std::string)coordsRow["cy"]; 
				coords += " : ";
				coords += (std::string)coordsRow["pos"];
				
				char str[2] = "";
				strcpy( str, coordsRow["code"]);
				std::string fullCoords = "";
				
				switch(str[0])
				{
					case 'a':
					{
						std::string fullCoords = "Asteroidenfeld (";
						fullCoords += coords;
						fullCoords += ")";
						return fullCoords;
						break;
					}
					case 'p':
					{
						if (blank!=2) {
							if (blank == 0) {
								if (std::string(coordsRow["planet_name"])!="")
									fullCoords = std::string(coordsRow["planet_name"]);
								else
									fullCoords = "Unbennant";
								fullCoords += " (";
								fullCoords += coords;
								fullCoords += ")";
								return(fullCoords);
							}
							else {
								return(coords);
							}
						}
						else {
							return(coords);
						}
						break;
					}
					case 'w':
					{
						std::string fullCoords = "Wurmloch (";
						fullCoords += coords;
						fullCoords += ")";
						return fullCoords;
						break;
					}
					case 's':
					{
						std::string fullCoords = "Stern (";
						fullCoords += coords;
						fullCoords += ")";
						return fullCoords;
						break;
					}
					case 'n':
					{
						std::string fullCoords = "Interstellarer Gasnebel (";
						fullCoords += coords;
						fullCoords += ")";
						return fullCoords;
						break;
					}
					case 'e':
					{
						std::string fullCoords = "Leerer Raum (";
						fullCoords += coords;
						fullCoords += ")";
						return fullCoords;
						break;
					}
					default:
					{
						return("Unendliche Weiten");
						break;
					}
				}
			}
		}
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
	
	/** Resetet einen Planeten **/
	bool resetPlanet(int planetId)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		if (planetId>0) {
			mysqlpp::Query query = con_->query();
			query << "UPDATE ";
			query << "	planets ";
			query << "SET ";
			query << "	planet_user_id=0, ";
			query << "	planet_name='', ";
			query << "	planet_user_main=0, ";
			query << "	planet_fields_used=0, ";
			query << "	planet_fields_extra=0, ";
			query << "	planet_res_metal=0, ";
			query << "	planet_res_crystal=0, ";
			query << "	planet_res_fuel=0, ";
			query << "	planet_res_plastic=0, ";
			query << "	planet_res_food=0, ";
			query << "	planet_use_power=0, ";
			query << "	planet_last_updated=0, ";
			query << "	planet_prod_metal=0, ";
			query << "	planet_prod_crystal=0, ";
			query << "	planet_prod_plastic=0, ";
			query << "	planet_prod_fuel=0, ";
			query << "	planet_prod_food=0, ";
			query << "	planet_prod_power=0, ";
			query << "	planet_store_metal=0, ";
			query << "	planet_store_crystal=0, ";
			query << "	planet_store_plastic=0, ";
			query << "	planet_store_fuel=0, ";
			query << "	planet_store_food=0, ";
			query << "	planet_people=1, ";
			query << "	planet_people_place=0, ";
			query << "	planet_desc='' ";
			query << "WHERE ";
			query << "	id='" << planetId << "';";
			query.store();
			query.reset();

			query << "DELETE FROM ";
			query << "	shiplist ";
			query << "WHERE ";
			query << "	shiplist_entity_id='" << planetId << "';";
			query.store();
			query.reset();
			
			query << "DELETE FROM ";
			query << "	buildlist ";
			query << "WHERE ";
			query << "	buildlist_entity_id='" << planetId << "';";
			query.store();
			query.reset();
			
			query << "DELETE FROM ";
			query << "	deflist ";
			query << "WHERE ";
			query << "	deflist_entity_id='" << planetId << "';";
			query.store();
			query.reset();
			
			std::string log = "Der Planet mit der ID ";
			log += planetId;
			log += " wurde zurückgesetzt!";
			addLog(6,log,std::time(0));
			return true;
		}
		else
			return false;
	}
	
	/** Invasiert einen Planeten **/
	void invasionPlanet(int entityId, int newUserId)
	{
		std::time_t time = std::time(0);
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		mysqlpp::Query query = con_->query();

        /** Planet übernehmen **/
        query << "UPDATE ";
		query << "	planets ";
		query << "SET ";
		query << "	planet_user_id='" << newUserId << "', ";
		query << "	planet_name='Unbenannt', ";
		query << "	planet_user_changed='" << time << "' ";
		query << "WHERE ";
		query << "	id='" << entityId << "';";
		query.store();
		query.reset();
		
        /** Gebäude übernehmen **/
        query << "UPDATE ";
		query << "	buildlist ";
		query << "	SET ";
		query << "	buildlist_user_id='" << newUserId << "' ";
		query << "WHERE ";
		query << "	buildlist_entity_id='" << entityId << "'; ";
		query.store();
		query.reset();
		
		/** Bestehende Schiffs-Einträge löschen **/
		query << "DELETE FROM ";
		query << "	shiplist ";
		query << "WHERE ";
		query << "	shiplist_entity_id='" << entityId << "';";
		query.store();
		query.reset();
		
		query << "DELETE FROM ";
		query << "	ship_queue ";
		query << "WHERE ";
		query << "	queue_entity_id='" << entityId << "';";
		query.store();
		query.reset();
		
		/** Bestehende Verteidigungs-Einträge löschen **/
		query << "DELETE FROM ";
		query << "	deflist ";
		query << "WHERE ";
		query << "	deflist_entity_id='" << entityId << "';";
		query.store(),
		query.reset();
		
		query << "DELETE FROM ";
		query << "	def_queue ";
		query << "WHERE ";
		query << "	queue_entity_id='" << entityId << "';";
		query.store();
		query.reset();
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
		query << "	planet_type_id='" << config.get("gasplanet", 0) << "' ";
		query << "	AND id='" << planetId << "';";
		mysqlpp::Result res = query.store();
		query.reset();
		
		if (res)  {
			int resSize = res.size();
			if (resSize>0) {
				Config &config = Config::instance();
				
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
