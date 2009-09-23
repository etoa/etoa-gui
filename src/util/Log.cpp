#include "Log.h"

void log(int priority, std::string message)
{
	if (priority == LOG_DEBUG && LOG_UPTO (LOG_DEBUG) == setlogmask(0))
	{
		std::cout << "DBG: " << message << std::endl;
	}
	else
	{
		openlog ("etoad", LOG_CONS | LOG_PID | LOG_NDELAY, LOG_LOCAL1);
		syslog (priority, message.c_str());
		closelog ();
	}
}

bool debugEnabled()
{
	return (LOG_UPTO(LOG_DEBUG) == setlogmask(0));
}

void logPrio(int priority)
{
	if (priority == LOG_DEBUG)
	{
		std::cout << std::endl << "*** Entering debug mode! No debug data is save to system log! ***"<<std::endl<<std::endl;
	}
	setlogmask (LOG_UPTO (priority));
}
