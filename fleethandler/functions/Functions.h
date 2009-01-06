#ifndef __FUNCTIONS__
#define __FUNCTIONS__

#include <mysql++/mysql++.h>
#include <sstream>
#include <string>
#include <iostream> 


namespace functions
{	
	
	/**
	* Liefet den Namen der Aktion zurück 
	*
	* @param string fAction, DB eintrag der Aktion
	* @author Glaubinix
	**/
	std::string fa(std::string fAction);	
	
	
	/**
	* Formatiert eine Zahl
	*
	* @param string value, formatierende Zahl
	* @author Glaubinix
	**/
	std::string nf(std::string value);	


	/**
	* Wandelt ein Zahl in einen String um 
	*
	* @param double number
	* @author Glaubinix
	**/	
	std::string d2s(double number);
	
	/**
	* Formatiert einen Timestamp
	*
	* @param int time, Zeitangabe in Form eines Timestamps
	* @author Glaubinix
	**/
	std::string formatTime(int time);
	
	/**
	* Aktualisiert die Werte eines Gasplaneten
	*
	* @param int pId, Planetenid
	* @author Glaubinix
	**/
	void updateGasPlanet(int planetId);
	
	
	/**
	* Initialisiert alle Gasplaneten mit der erstmöglichen Loginzeit
	*
	* @author Glaubinix
	**/
	void initGasPlanets();
};
#endif
