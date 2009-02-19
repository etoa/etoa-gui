
#include "Functions.h"

namespace etoa
{
	
	std::string get_user_nick(int pid)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
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
    
	float getSolarFuelBonus(int t_min, int t_max)
	{
		float v = (int)floor((t_min + t_max)/25.0);
		return v/100;
	}
	
	int getSolarPowerBonus(int t_min, int t_max)
	{
		int v = (int)floor((t_max + t_min)/4.0);
		if (v <= -100)
		{
			v = -99;
		}
		return v;
	}
	
	std::string fa(std::string fAction)
	{
		return fAction;
	}
	
	std::string d2s(double number)
	{
		std::ostringstream Str;
		Str << std::setprecision(18);
		Str << number;
		std::string zAs(Str.str());
		return zAs;
	}
	
	double s2d(std::string number)
	{
		std::istringstream i(number);
		double x;
		i >> x;
		return x;
	}
	
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
		/** Schneidet den Rest ab, wenn ein Punkt und Nachkommazeichen vorhanden sind **/
		std::size_t found = value.find(".");
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
	
	void send_msg(int user_id, int msg_type, std::string subject, std::string text)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		std::cout << "->adding Message to db\n";
		
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
		query << user_id << "', '";
		query << time(0) << "', '";
		query << msg_type << "' ";
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
	
	void add_log(int log_cat, std::string log_text, std::time_t log_timestamp)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
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
		Config &config = Config::instance();
		// Calc time and distance
		int nx = (int)config.nget("num_of_cells", 1); //$conf['num_of_cells']['p1'];		// Anzahl Zellen Y
		int ny = (int)config.nget("num_of_cells", 2); //$conf['num_of_cells']['p2'];		// Anzahl Zellen X
		int ae =(int)config.nget("cell_length", 0); //$conf['cell_length']['v'];			// Länge vom Solsys in AE
		int np = (int)config.nget("num_planets", 2);; //$conf['num_planets']['p2'];			// Max. Planeten im Solsys

		double dx = fabs(((((int)rowPlanet2["sx"]-1) * nx) + (int)rowPlanet2["cx"]) - ((((int)rowPlanet1["sx"]-1) * nx) + (int)rowPlanet1["cx"]));
		double dy = fabs(((((int)rowPlanet2["sy"]-1) * ny) + (int)rowPlanet1["cy"]) - ((((int)rowPlanet1["sy"]-1) * ny) + (int)rowPlanet1["cy"]));
		double sd = sqrt(pow(dx,2)+pow(dy,2));			// Distanze zwischen den beiden Zellen
		double sae = sd * ae;	
		double ps;										// Distance in AE units
		
		if (int(rowPlanet1["sx"])==int(rowPlanet2["sx"]) && int(rowPlanet1["sy"])==int(rowPlanet2["sy"]) && int(rowPlanet1["cx"])==int(rowPlanet2["cx"]) && int(rowPlanet1["cy"])==int(rowPlanet2["cy"]))
		{
			ps = fabs(int(rowPlanet2["pos"])-int(rowPlanet1["pos"]))*ae/4/np;				// Planetendistanz wenn sie im selben Solsys sind
		}
		else
		{
			ps = (ae/2) - ((int(rowPlanet2["pos"]))*ae/4/np);	// Planetendistanz wenn sie nicht im selben Solsys sind
		}
		double ssae = sae + ps;
		return ssae;	
		return 1;
	}		

	double calcDistanceByPlanetId(int pid1, int pid2)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		mysqlpp::Row rowPlanet1, rowPlanet2;
		mysqlpp::Query query = con_->query();
		query << "SELECT ";
			query << "cells.sx, ";
			query << "cells.sy, ";
			query << "cells.cx, ";
			query << "cells.cy, ";
			query << "entities.pos ";
		query << "FROM ";
			query << "entities ";
		query << "INNER JOIN ";
			query << "cells ";
			query << "ON cells.id=entities.cell_id ";
			query << "AND entities.id='" << pid1 <<"';";
		mysqlpp::Result res1 = query.store();		
		query.reset();
		
		query << "SELECT ";
			query << "cells.sx, ";
			query << "cells.sy, ";
			query << "cells.cx, ";
			query << "cells.cy, ";
			query << "entities.pos ";
		query << "FROM ";
			query << "entities ";
		query << "INNER JOIN ";
			query << "cells ";
			query << "ON cells.id=entities.cell_id ";
			query << "AND entities.id='" << pid2 <<"';";
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
	
	void addBattlePoints(int userId, int points, bool won, std::string reason) {
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		mysqlpp::Query query = con_->query();
		query << "UPDATE "
			<< "	user_ratings "
			<< "SET "
			<< "	battles_fought=battles_fought+1, ";
		if (won)
			query << "	battles_won=battles_won+1, ";
		else
			query << "	battles_lost=battles_lost+1, ";
		query << "	battle_rating=battle_rating+" << points << " "
			<< "WHERE "
			<< "	id=" << userId << ";";
		query.store();
		query.reset();
		std::string text = "Der Spieler " + etoa::d2s(userId) +" erhŠlt " + etoa::d2s(points) + " Kampfpunkt(e). Grund: " + reason;
		add_log(17,text,0);
	}
	
	void addSpecialiBattle(int userId, std::string reason="") {
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		
		mysqlpp::Query query = con_->query();
		query << "UPDATE "
			<< "	user_ratings "
			<< "SET "
			<< "	battle_rating=battle_rating+1 "
			<< "WHERE "
			<< "	id=" << userId << ";";
		query.store();
		query.reset();
		std::string text = "Der Spieler " + etoa::d2s(userId) +" erhŠlt 1 Kampfpunkte. Grund: " + reason;
		add_log(17,text,0);
	}
}
