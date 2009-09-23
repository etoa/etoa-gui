#include <syslog.h>
#include <iostream>
#include <sstream>
#include <string>
#include <fstream>

/**
* Log message to syslog
*
* @author Nicolas Perrenoud <mrcage@etoa.ch>
* @param message Log message string
* @param priority Priority of the log message according to unix syslog priorities
*
* LOG_EMERG 	system is unusable
* LOG_ALERT   action must be taken immediately
* LOG_CRIT    critical conditions
* LOG_ERR     error conditions
* LOG_WARNING warning conditions
* LOG_NOTICE  normal but significant condition
* LOG_INFO    informational
* LOG_DEBUG   debug-level messages
*
* @example 
*
*		logPrio(LOG_NOTICE);
* 	LOG(LOG_NOTICE,"This is a log message for " << 2 << " errors!");
* 
*/
void log(int priority, std::string message);
void logPrio(int priority);
bool debugEnabled();
static void inline log(int priority, const std::ostringstream& oss) { log(priority,oss.str()); }
	
#define LOG(priority,text) \
{std::ostringstream oss; log(priority,(std::ostringstream&)(oss<<text));}

