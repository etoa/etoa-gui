#include "Log.h"
#include "Debug.h"

void log(int priority, std::string message)
{
	if (debugEnable(0))
	{
		debugMsg(message);
	}
	if (priority <= LOG_ERR)
		std::cout << "ERR: " << message << std::endl;
	openlog (logProgam("").c_str(), LOG_CONS | LOG_PID | LOG_NDELAY, LOG_LOCAL1);
	syslog (priority, "%s", message.c_str());
	closelog ();
}

std::string logProgam(std::string roundName)
{
	static std::string progName = "etoad";
	if (roundName!="")
		progName = "etoad."+roundName;
	return progName;
}

void logPrio(int priority)
{
	setlogmask (LOG_UPTO (priority));
}
