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

#ifndef ETOA_FUNCTIONS_H
#define ETOA_FUNCTIONS_H

#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>
#include <time.h>
#include <math.h>
#include <map>
#include <iostream>
#include <sstream>
#include <string>
#include <vector>
#include <ctime>

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
	* Tries to convert anything to an int using stringstream buffer
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
	
	std::string inline addslashes(std::string str)
	{
		std::string rtn;
		for (unsigned int i=0; i<str.size(); i++)
		{
			const char tmp = str[i];
			if (tmp == '\'')
				rtn += "\\'";
			else			
				rtn+= tmp;
		}
		return rtn;
	}	

	
	/**
	* Liefet den Usernamen  
	*
	* @param userId User ID 
	* @author Glaubinix
	*/
	std::string get_user_nick(int userId);
		
	/**
	* Liefet den Bonus durch die Temparatur 
	*
	* @param t_min Minimaler Temperaturbonus
	* @param t_max Maximaler Temperaturbonus
	* @author Glaubinix
	*/
	float getSolarFuelBonus(int t_min, int t_max);
	
	/**
	* Liefet den Bonus durch die Temparatur 
	*
	* @param t_min Min Bonus
	* @param t_max Max Bonus
	* @author Glaubinix
	*/
	int getSolarPowerBonus(int t_min, int t_max);
	
	/**
	* Liefet den Namen der Aktion zur�ck 
	*
	* @param fAction DB eintrag der Aktion
	* @author Glaubinix
	*/
	std::string fa(std::string fAction);
		
	/**
	* Wandelt ein Zahl in einen String um 
	*
	* @param number
	* @author Glaubinix
	*/		
	std::string d2s(double number);
		
	double s2d(std::string number);
		
	/**
	* Formatiert einen Timestamp
	*
	* @param time Zeitangabe in Form eines Timestamps
	* @author Glaubinix
	*/
	std::string formatTime(int time);									
	
	/**
	* Formatiert ein Datum 
	*
	* @param Zeitstempel Zu formatierende Zahl
	* @author Glaubinix
	*/
	std::string format_time(std::time_t Zeitstempel);	
	
	/**
	* Formatiert eine Zahl mit '
	*
	* @param value zu formatierende Zahl
	* @author Glaubinix
	*/
	std::string nf(std::string  value);
		
	/**
	* Speichert Nachricht in die Tabelle
	*
	* @param user_id 
	* @param msg_type Kategorie
	* @param subject
	* @param text
	* @author Glaubinix
	*/	
	void send_msg(int user_id, int msg_type, std::string subject, std::string text);
		
	/**
	* Speichert Daten in die Log-Tabelle
	*
	* @param facility Log Kategorie
	* @param log_text Log text
	* @param log_timestamp Zeit
	* @todo Perpahps define another overloaded function with only two arguments and automatic time choosing
	* @author Glaubinix
	*/
	void add_log(int facility, std::string log_text, std::time_t log_timestamp=0);
		
	/**
	* Liefert eine Zahl mit Anzahl Komastellen
	*
	* @param number zu bearbeitende Zahl
	* @param precision Anzahl Nachkommastellen
	* @author Glaubinix
	*/
	double s_round(float number, int precision);
	
	/**
	* Liefert die Distance zwischen 2 Planeten
	*
	* @param rowPlanet1 PlanetenID Planet 1
	* @param rowPlanet2 PlanetenID Planet 2
	* @author Glaubinix
	*/	
	double calcDistance(mysqlpp::Row rowPlanet1, mysqlpp::Row rowPlanet2);

	/**
	* Liefert die Distance zwischen 2 Planeten
	*
	* @param pid1 Planetendaten Planet 1
	* @param pid2 Planetendaten Planet 2
	* @author Glaubinix
	*/
	double calcDistanceByPlanetId(int pid1, int pid2);
	
	/**
	* F�gt die Kampfpunkte zur Statistik hinzu
	*
	* @param userId Benutzer
	* @param points Anzahlpunkte
	* @param result Kampf gewonnen?
	* @param reason Grund der Punkte
	*/
	void addBattlePoints(int userId, int points, short result, std::string reason="");
	void addSpecialiBattle(int userId, std::string reason);
		
}

#endif
