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
	
	/**
	* Liefet den Usernamen  
	*
	* @param int uid User ID 
	* @author Glaubinix
	*/
	std::string get_user_nick(int pid);
		
	/**
	* Liefet den Bonus durch die Temparatur 
	*
	* @param int t_min, int t_max Temparaturbegrenzung des Planeten
	* @author Glaubinix
	*/
	float getSolarFuelBonus(int t_min, int t_max);
	
	/**
	* Liefet den Bonus durch die Temparatur 
	*
	* @param int t_min, int t_max Temparaturbegrenzung des Planeten
	* @author Glaubinix
	*/
	int getSolarPowerBonus(int t_min, int t_max);
	
	/**
	* Liefet den Namen der Aktion zurück 
	*
	* @param string fAction, DB eintrag der Aktion
	* @author Glaubinix
	*/
	std::string fa(std::string fAction);
		
	/**
	* Wandelt ein Zahl in einen String um 
	*
	* @param double number
	* @author Glaubinix
	*/		
	std::string d2s(double number);
		
	/**
	* Formatiert einen Timestamp
	*
	* @param int time, Zeitangabe in Form eines Timestamps
	* @author Glaubinix
	*/
	std::string formatTime(int time);									
	
	/**
	* Formatiert ein Datum 
	*
	* @param std::string vale zu formatierende Zahl
	* @author Glaubinix
	*/
	std::string format_time(std::time_t Zeitstempel);	
	
	/**
	* Formatiert eine Zahl mit '
	*
	* @param std::string vale zu formatierende Zahl
	* @author Glaubinix
	*/
	std::string nf(std::string  value);
		
	/**
	* Speichert Nachricht in die Tabelle
	*
	* @param int user_id 
	* @param int msg_type Kategorie
	* @param string subject
	* @param string text
	* @author Glaubinix
	*/	
	void send_msg(int user_id, int msg_type, std::string subject, std::string text);
		
	/**
	* Speichert Daten in die Log-Tabelle
	*
	* @param int log_cat Log Kategorie
	* @param string log_text Log text
	* @param time_t log_timestamp Zeit
	* @author Glaubinix
	*/
	void add_log(int log_cat, std::string log_text, std::time_t log_timestamp);
		
	/**
	* Liefert eine Zahl mit Anzahl Komastellen
	*
	* @param float number zu bearbeitende Zahl
	* @param int precision Anzahl Nachkommastellen
	* @author Glaubinix
	*/
	double s_round(float number, int precision);
	
	/**
	* Liefert die Distance zwischen 2 Planeten
	*
	* @param int pid1 PlanetenID Planet 1
	* @param int pid1 PlanetenID Planet 2
	* @author Glaubinix
	*/	
	double calcDistance(mysqlpp::Row rowPlanet1, mysqlpp::Row rowPlanet2);

	/**
	* Liefert die Distance zwischen 2 Planeten
	*
	* @param mysqlpp::Row rowPlanet1 Planetendaten Planet 1
	* @param mysqlpp::Row rowPlanet2 Planetendaten Planet 2
	* @author Glaubinix
	*/
	double calcDistanceByPlanetId(int pid1, int pid2);
		
}

#endif
