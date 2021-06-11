#include <iostream>
#include <string>
#include <sstream>

static void inline debugMsg(std::string str)
{
	std::cerr << "DEBUG: " << str << std::endl;
}

static void inline debugMsg(const std::ostringstream& oss)
{
	debugMsg(oss.str());
}

#define DEBUG(text) \
{	if (debugEnable(0)) { std::ostringstream oss; debugMsg((std::ostringstream&)(oss<<text)); } }

bool debugEnable(int enable);
