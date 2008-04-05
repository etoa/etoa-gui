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
	std::string formatCoords(int planetId);
	std::string nf(std::string value);
	std::string formatTime(int time);
	std::string fa(std::string fAction);
		
}
#endif
