
#ifndef __USER__
#define __USER__

#include <mysql++/mysql++.h>

#include "../MysqlHandler.h"

/**
* User class
* 
* @author Stephan Vock<glaubinx@etoa.ch>
*/

class User	
{
	public:
		User(int userId) {
			this->userId = userId;
		}
		
		~User() { }
		
		void addCollectedWf(int res);
		
	private:
		int userId;
};

#endif
