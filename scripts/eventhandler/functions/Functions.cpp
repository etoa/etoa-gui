#include <iostream>
#include <string>
#include <sstream>

#include <mysql++/mysql++.h>

#include "Functions.h"

namespace functions
{

	std::string get_user_nick(mysqlpp::Connection* con_, int pid)
	{
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
			query << "user_nick ";
		query << "FROM ";
			query << "users ";
		query << "WHERE ";
			query << "user_id='" << pid << "';";
		mysqlpp::Result res = query.store();		
		query.reset();

		if (res)
		{
			int resSize = res.size();			
    	
			if (resSize>0)
			{
				mysqlpp::Row row;
				row = res.at(0);
				return (std::string(row["user_nick"]));
			}
			else
			{
				return "<i>Unbekannter Benutzer</i>";
			}
		}
		else
		{
			return "<i>Unbekannter Benutzer</i>";
		}
	}
    
	 
	int getSolarPowerBonus(int t_min, int t_max)
	{
		int v = floor((t_max + t_min)/4);
		if (v <= -100)
		{
			v = -99;
		}
		return v;
	}
	
	
	std::string format_time(std::time_t Zeitstempel)
	{
		std::time_t zeit = Zeitstempel;
		if (zeit == 0)
		{
				zeit = time(0);
		}
		tm *now;
		now = localtime(&zeit);
		
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

	std::string nf(std::string  value)
	{
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
	
	void send_msg(mysqlpp::Connection* con, int user_id, int msg_type, std::string subject, std::string text)
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
				query << "('0', '";
				query << user_id << "', '";
				query << time(0) << "', '";
				query << msg_type << "', '";
				query << subject << "', '";
				query << text << "');";
		query.store();
		query.reset();
	
	}
	
	void add_log(mysqlpp::Connection* con_, int log_cat, std::string log_text, std::time_t log_timestamp)
	{
		if (log_timestamp==0)
		{
		 	log_timestamp = std::time(0);
		}
		
		std::time_t time = std::time(0);
		
		mysqlpp::Query query = con_->query();
		query << "INSERT INTO logs ";
			query << "(log_cat, ";
			query << "log_timestamp, ";
			query << "log_realtime, ";
			query << "log_text) ";
		query << "VALUES ";
			query << "('" << log_cat << "', ";
		 	query << "'" << log_timestamp << "', ";
		 	query << "'" << time << "', ";
		 	query << "'" << log_text << "');"; //addslashes(log_text)
		query.store();
		query.reset();
	}
	
	double s_round(float number, int precision)
	{
		double temp1, temp2, temp3;
		
		temp1 = pow(10,precision);
		temp2 = number*temp1;
		temp3 = temp2-ceil(temp2);
		
		if (temp3>0.5)
		{
			temp2++;
		}
		
		temp3 = ceil(temp2);
		temp2 = temp3/temp1;
		return temp2;
	}
	
	double calcDistance(mysqlpp::Row rowPlanet1, mysqlpp::Row rowPlanet2)
	{
		// Calc time and distance
		int nx = 10; //$conf['num_of_cells']['p1'];		// Anzahl Zellen Y
		int ny = 10; //$conf['num_of_cells']['p2'];		// Anzahl Zellen X
		int ae = 300; //$conf['cell_length']['v'];			// Länge vom Solsys in AE
		int np = 15; //$conf['num_planets']['p2'];			// Max. Planeten im Solsys
		double temp = (((int(rowPlanet2["cell_sx"])-1) * nx) + int(rowPlanet2["cell_cx"])) - (((int(rowPlanet1["cell_sx"])-1) * nx) + int(rowPlanet1["cell_cx"]));
		double dx = fabs(temp);
		double dy = fabs((((int(rowPlanet2["cell_sy"])-1) * nx) + int(rowPlanet1["cell_cy"])) - (((int(rowPlanet1["cell_sy"])-1) * nx) + int(rowPlanet1["cell_cy"])));
		double sd = sqrt(pow(dx,2)+pow(dy,2));			// Distanze zwischen den beiden Zellen
		double sae = sd * ae;	
		double ps;										// Distance in AE units
		if (int(rowPlanet1["cell_sx"])==int(rowPlanet2["cell_sx"]) && int(rowPlanet1["cell_sy"])==int(rowPlanet2["cell_sy"]) && int(rowPlanet1["cell_cx"])==int(rowPlanet2["cell_cx"]) && int(rowPlanet1["cell_cy"])==int(rowPlanet2["cell_cy"]))
		{
			ps = fabs(int(rowPlanet2["planet_solsys_pos"])-int(rowPlanet1["planet_solsys_pos"]))*ae/4/np;				// Planetendistanz wenn sie im selben Solsys sind
		}
		else
		{
			ps = (ae/2) - ((int(rowPlanet2["planet_solsys_pos"]))*ae/4/np);	// Planetendistanz wenn sie nicht im selben Solsys sind
		}
		double ssae = sae + ps;
		return ssae;	
		return 1;
	}		

	double calcDistanceByPlanetId(mysqlpp::Connection* con_, int pid1, int pid2)
	{
		mysqlpp::Row rowPlanet1, rowPlanet2;
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
			query << "cell_sx, ";
			query << "cell_sy, ";
			query << "cell_cx, ";
			query << "cell_cy, ";
			query << "planet_solsys_pos ";
		query << "FROM ";
			query << "planets ";
		query << "INNER JOIN ";
			query << "space_cells ";
			query << "ON planet_solsys_id=cell_id ";
			query << "AND planet_id='" << pid1 <<"';";
		mysqlpp::Result res1 = query.store();		
		query.reset();
		
		query << "SELECT ";
			query << "cell_sx, ";
			query << "cell_sy, ";
			query << "cell_cx, ";
			query << "cell_cy, ";
			query << "planet_solsys_pos ";
		query << "FROM ";
			query << "planets ";
		query << "INNER JOIN ";
			query << "space_cells ";
			query << "ON planet_solsys_id=cell_id ";
			query << "AND planet_id='" << pid2 <<"';";
		mysqlpp::Result res2 = query.store();		
		query.reset();
		
		if (res1) 
		{
			int res1Size = res1.size();			
    	
			if (res1Size>0)
			{
				rowPlanet1 = res1.at(0);
			}
		}
		
		if (res2) 
		{
			int res2Size = res2.size();			
    	
			if (res2Size>0)
			{
				rowPlanet2 = res2.at(0);
			}
		}
	

		double distance = calcDistance(rowPlanet1, rowPlanet2);
		return distance;
	}

}
