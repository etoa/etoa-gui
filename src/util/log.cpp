#include "log.h"

void log(int priority, std::string message)
{
	std::cout << message << std::endl;

	openlog ("etoad", LOG_CONS | LOG_PID | LOG_NDELAY, LOG_LOCAL1);

	syslog (priority, message.c_str());
	closelog ();
}

void logPrio(int priority)
{
	setlogmask (LOG_UPTO (priority));
	
}
