//////////////////////////////////////////////////
//		 	 ____    __           ______       			//
//			/\  _`\ /\ \__       /\  _  \      			//
//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
//																					 		//
//////////////////////////////////////////////////
// The Andromeda-Project-Browsergame				 		//
// Ein Massive-Multiplayer-Online-Spiel			 		//
// (C) by EtoA Gaming | www.etoa.ch   			 		//
//////////////////////////////////////////////////
//
// Miscelaneous helper functions
//

#ifndef FUNCTIONS_H
#define FUNCTIONS_H

#include <mysql++/mysql++.h>
#include <time.h>
#include <math.h>
#include <map>
#include <iostream>
#include <sstream>
#include <string>
#include <vector>

#include "../config/ConfigHandler.h"
#include "../MysqlHandler.h"

namespace etoa
{

	/**
	* Tries to convert anything to a std::string using stringstream buffer
	*/
	template <class T>inline std::string toString(const T& t)
	{
		std::stringstream ss;
		ss << t;
		return ss.str();
	}
	
	/**
	* Tries to convert anything to an int stringstream buffer
	*/
	template <class T>inline int toInt(const T& t)
	{
		std::istringstream isst;
		int zahl=0;
		isst.str(t);
		isst >> zahl;
		return zahl;
	}
	
	/**
	* Splits a given text by its separators and stores it in a vector
	*/
	void inline explode(std::string& text, std::string& separators, std::vector<std::string>& words)
	{
		int n = text.length();
		int start, stop;
	
		start = text.find_first_not_of(separators);
		while ((start >= 0) && (start < n)) 
		{
			stop = text.find_first_of(separators, start);
			if ((stop < 0) || (stop > n)) stop = n;
			words.push_back(text.substr(start, stop - start));
			start = text.find_first_not_of(separators, stop+1);
		}
	}

	/**
	* Liefet den Usernamen  
	*
	* @param int uid User ID 
	* @author Glaubinix
	*/
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
    
	/**
	* Liefet den Bonus durch die Temparatur 
	*
	* @param int t_min, int t_max Temparaturbegrenzung des Planeten
	* @author Glaubinix
	*/
	float getSolarFuelBonus(int t_min, int t_max)
	{
		float v = (int)floor((t_min + t_max)/25);
		return v/100;
	}
	
	/**
	* Liefet den Bonus durch die Temparatur 
	*
	* @param int t_min, int t_max Temparaturbegrenzung des Planeten
	* @author Glaubinix
	*/
	int getSolarPowerBonus(int t_min, int t_max)
	{
		int v = (int)floor((t_max + t_min)/4);
		if (v <= -100)
		{
			v = -99;
		}
		return v;
	}
	
	/**
	* Liefet den Namen der Aktion zurück 
	*
	* @param string fAction, DB eintrag der Aktion
	* @author Glaubinix
	*/
	std::string fa(std::string fAction)
	{
		return fAction;
	}
	
	/**
	* Wandelt ein Zahl in einen String um 
	*
	* @param double number
	* @author Glaubinix
	*/		
	std::string d2s(double number)
	{
		std::ostringstream Str;
		Str << std::setprecision(18);
		Str << number;
		std::string zAs(Str.str());
		return zAs;
	}
	
	/**
	* Formatiert einen Timestamp
	*
	* @param int time, Zeitangabe in Form eines Timestamps
	* @author Glaubinix
	*/
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
	
	/**
	* Formatiert ein Datum 
	*
	* @param std::string vale zu formatierende Zahl
	* @author Glaubinix
	*/
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

	/**
	* Formatiert eine Zahl mit '
	*
	* @param std::string vale zu formatierende Zahl
	* @author Glaubinix
	*/
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
	
	/**
	* Speichert Nachricht in die Tabelle
	*
	* @param int user_id 
	* @param int msg_type Kategorie
	* @param string subject
	* @param string text
	* @author Glaubinix
	*/	
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
	
	/**
	* Speichert Daten in die Log-Tabelle
	*
	* @param int log_cat Log Kategorie
	* @param string log_text Log text
	* @param time_t log_timestamp Zeit
	* @author Glaubinix
	*/
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
	
	/**
	* Liefert eine Zahl mit Anzahl Komastellen
	*
	* @param float number zu bearbeitende Zahl
	* @param int precision Anzahl Nachkommastellen
	* @author Glaubinix
	*/
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
	
	/**
	* Liefert die Distance zwischen 2 Planeten
	*
	* @param int pid1 PlanetenID Planet 1
	* @param int pid1 PlanetenID Planet 2
	* @author Glaubinix
	*/	
	double calcDistance(mysqlpp::Row rowPlanet1, mysqlpp::Row rowPlanet2)
	{
		Config &config = Config::instance();
		// Calc time and distance
		int nx = (int)config.nget("num_of_cells", 1); //$conf['num_of_cells']['p1'];		// Anzahl Zellen Y
		int ny = (int)config.nget("num_of_cells", 2); //$conf['num_of_cells']['p2'];		// Anzahl Zellen X
		int ae =(int)config.nget("cell_length", 0); //$conf['cell_length']['v'];			// Länge vom Solsys in AE
		int np = (int)config.nget("num_planets", 2);; //$conf['num_planets']['p2'];			// Max. Planeten im Solsys
		double temp = ((((int)rowPlanet2["sx"]-1) * nx) + (int)rowPlanet2["cx"]) - ((((int)rowPlanet1["sx"]-1) * nx) + (int)rowPlanet1["cx"]);
		double dx = fabs(temp);
		double dy = fabs(((((int)rowPlanet2["sy"]-1) * nx) + (int)rowPlanet1["cy"]) - ((((int)rowPlanet1["sy"]-1) * nx) + (int)rowPlanet1["cy"]));
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

	/**
	* Liefert die Distance zwischen 2 Planeten
	*
	* @param mysqlpp::Row rowPlanet1 Planetendaten Planet 1
	* @param mysqlpp::Row rowPlanet2 Planetendaten Planet 2
	* @author Glaubinix
	*/
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

}

#endif
