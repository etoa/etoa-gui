#ifndef __FUNCTIONS__
#define __FUNCTIONS__

#include <mysql++/mysql++.h>
#include <sstream>
#include <string>
#include <iostream> 


namespace functions
{

	int getUserIdByPlanet(int pid);
	std::string d2s(double number);
	void sendMsg(int userId, int msgType, std::string subject, std::string text);
	std::string formatCoords(int planetId, short blank);
	std::string nf(std::string value);
	std::string formatTime(int time);
	std::string fa(std::string fAction);
	std::string getUserNick(int pid);
	void addLog(int logCat, std::string logText, std::time_t logTimestamp=0);
	bool resetPlanet(int id);
	void updateGasPlanet(int pid);
}
#endif
