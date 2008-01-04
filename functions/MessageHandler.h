#ifndef __MESSAGEHANDLER__
#define __MESSAGEHANDLER__

#include <mysql++/mysql++.h>


namespace message
{
	
	class addMessage
	{
	public:
		static void send_message(mysqlpp::Connection* con,  int user_id, int msg_type, std::string subject, std::string text);
		
	};	
	
	class formatMessage
	{
	public:
		static std::string format_coords(mysqlpp::Connection* con, std::string planet_id);
		static std::string format_number(std::string value);
		static std::string format_time();
	};
		
}
#endif
