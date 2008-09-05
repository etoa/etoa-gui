#ifndef __FUNCTIONS__
#define __FUNCTIONS__

#include <mysql++/mysql++.h>
#include <sstream>
#include <string>
#include <iostream> 


namespace functions
{
	/**
	* versenden einen Nachricht
	*
	* @param int userId, user_id des Empfängers
	* @param int msgType, die Kategroie vor genauere Infos zu den Kategorien in der DB oder im Admintool nachschauen
	* @param string subject, der Titel der Nachricht
	* @param string text Die Nachricht
	* @author Glaubinix
	**/
	void sendMsg(int userId, int msgType, std::string subject, std::string text);
	
	
	/**
	* Schreibt einen Logeintrag (Alle Aktionen müssen gelogt werden!!)
	*
	* @param int logCat, Kategorie, für genauere Infos in der DB oder im Admintool nachschauen
	* @author Glaubinix
	**/
	void addLog(int logCat, std::string logText, std::time_t logTimestamp=0);	
	
	
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
	* Liefet den Nick des userId 
	*
	* @param int userId, des Users
	* @author Glaubinix
	**/
	std::string getUserNick(int userId);
	
			
	/**
	* Liefet die User Id anhand der Planeten Id 
	*
	* @param int planetId Planetenid
	* @author Glaubinix
	**/
	int getUserIdByPlanet(int planetId);
	
	
	/**
	* Liefert die Koordinaten einer Entity
	*
	* @param int entityId, EntityId
	" @param short blank, ob mit oder ohne Namen und Klammer
	* @author Glaubinix
	**/
	std::string formatCoords(int entityId, short blank);

	
	/**
	* Formatiert einen Timestamp
	*
	* @param int time, Zeitangabe in Form eines Timestamps
	* @author Glaubinix
	**/
	std::string formatTime(int time);


	
	/**
	* Resetet einen Planeten
	*
	* @param int pId, Planetenid
	* @author Glaubinix
	**/
	bool resetPlanet(int planetId);
	
	/**
	* Invasiert einen Planeten
	*
	* @param int entityId, Entity ID
	* @param int newUserId, neuer entity User
	* @author Glaubinix
	**/
	void invasionPlanet(int entityId, int newUserId);
	
	
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
}
#endif
