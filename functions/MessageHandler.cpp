#include "MessageHandler.h"

namespace message
{
	void addMessage::send_message(mysqlpp::Connection* con, int user_id, int msg_type, std::string subject, std::string text)
	{
		std::cout << "->adding Message to db\n";
		
		mysqlpp::Query query = con->query();
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
				query << user_id << ", ";
				query << time(0) << ", ";
				query << msg_type << ", ";
				query << subject << ", ";
				query << text << ");";
		query.store();
		query.reset();
	
	}
	
	
	std::string formatMessage::format_coords(mysqlpp::Connection* con, std::string planet_id)
	{
		std::cout << "->formating Coords\n";
		
		mysqlpp::Query query = con->query();
		query << "SELECT ";
			query << "c.cell_id, ";
			query << "c.cell_sx, ";
			query << "c.cell_sy, ";
			query << "c.cell_cx, ";
			query << "c.cell_cy, ";
			query << "p.planet_solsys_pos, ";
			query << "p.planet_name ";
		query << "FROM planets AS p ";
			query << "INNER JOIN space_cells AS c ";
			query << "ON p.planet_solsys_id = c.cell_id ";
			query << "AND p.planet_id = " << planet_id << ";";	
		mysqlpp::Result coords_res = query.store();	
		query.reset();
				
				
		mysqlpp::Row coords_row;
		coords_row = coords_res.at(0);
	
		std::string slash("/");
		std::string cell_sx = (std::string)coords_row["cell_sx"];
		std::string cell_sy = (std::string)coords_row["cell_sy"];
		std::string cell_cx = (std::string)coords_row["cell_cx"];
		std::string cell_cy = (std::string)coords_row["cell_cy"];
		std::string planet_solsys_pos = (std::string)coords_row["planet_solsys_pos"];
						
		std::string coords = (std::string)coords_row["cell_sx"]; 
		coords += "/";
		coords += (std::string)coords_row["cell_sy"];
		coords += " : ";
		coords += (std::string)coords_row["cell_cx"];
		coords += "/";
		coords += (std::string)coords_row["cell_cy"]; 
		coords += " : ";
		coords += (std::string)coords_row["planet_solsys_pos"];
								

		if (coords_row["planet_name"]!="")
		{
			std::string full_coords = (std::string)coords_row["planet_name"];
			full_coords += " (";
			full_coords += coords;
			full_coords += ")";
			return(full_coords);
		}
		else
		{
			return(coords);
		}
	}

	std::string formatMessage::format_number(std::string  value)
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
	
	std::string formatMessage::format_time()
	{
		std::cout << "->formating Time\n";
		time_t Zeitstempel;
		tm *now;
		Zeitstempel = time(0);
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