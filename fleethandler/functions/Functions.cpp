#include "Functions.h"
#include "../MysqlHandler.h"

namespace functions
{

	std::string fa(std::string fAction)
	{
		std::cout << "Calculating fleet Action...\n";
		return fAction;
	}
	
	int getUserIdByPlanet(int pid)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
		query << "	planet_user_id ";
		query << "FROM ";
		query << "	planets ";
		query << "WHERE ";
		query << "	id='" << pid << "';";
		mysqlpp::Result pRes = query.store();
		query.reset();
		
		if (pRes)
		{
			int pSize = pRes.size();
			
			if (pSize > 0)
			{
				mysqlpp::Row pRow = pRes.at(0);
				return (int)pRow["planet_user_id"];
			}
			else
			{
				return 0;
			}
		}
	}
	
	std::string d2s(double number)
	{
		std::ostringstream Str;
		Str << number;
		std::string zAs(Str.str());
		return zAs;
	}
	
	void sendMsg(int userId, int msgType, std::string subject, std::string text)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		std::cout << "->adding Message to db\n";
		
		mysqlpp::Query query = con_->query();
		query << "INSERT INTO ";
			query << "messages ";
				query << "(message_user_from, ";
				query << "message_user_to, ";
				query << "message_timestamp, ";
				query << "message_cat_id, ";
				query << "message_subject, ";
				query << "message_text) ";
			query << "VALUES ";
				query << "('0', ";
				query << userId << ", ";
				query << time(0) << ", ";
				query << msgType << ", ";
				query << "'" << subject << "', ";
				query << "'" << text << "');";
		query.store();
		query.reset();
	
	}
	
	
	std::string formatCoords(int planetId)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		std::cout << "->formating Coords\n";
		
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
			query << "c.id, ";
			query << "c.sx, ";
			query << "c.sy, ";
			query << "c.cx, ";
			query << "c.cy, ";
			query << "e.pos, ";
			query << "p.planet_name ";
		query << "FROM entities AS e ";
			query << " ON e.id = '" << planetId << "' ";
			query << "AND p.id = e.id";
		query << "INNER JOIN planets AS p ";
			query << "INNER JOIN cells AS c ";
			query << "ON e.cell_id = c.id; ";
		mysqlpp::Result coordsRes = query.store();	
		query.reset();
				
				
		if (coordsRes)
		{
			int coordsSize = coordsRes.size();
			
			if (coordsSize > 0)
			{
			
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
								

				if (coordsRow["planet_name"]!="")
				{
					std::string fullCoords = (std::string)coordsRow["planet_name"];
					fullCoords += " (";
					fullCoords += coords;
					fullCoords += ")";
					return(fullCoords);
				}
				else
				{
					return(coords);
				}
			}
			else
			{
				return("Unendliche Weiten");
			}
		}
	}
	

	std::string nf(std::string  value)
	{
		std::cout << "->formating Number\n";
		int length = value.length();
		
		int i=3;
		while (length > i)
		{
			int to_do = length-i;
			value.insert(to_do, "`");
			i += 3;
					
		}
		return(value);
	}
	
	std::string formatTime(int time)
	{
		std::cout << "->formating Time\n";
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
		ftime += shour;
		ftime += ":";
		ftime += smin;
		ftime += ":";
		ftime += ssec;
		ftime += "\n";
		return(ftime);

	}
	
}