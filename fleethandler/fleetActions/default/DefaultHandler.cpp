#include <iostream>

#include <mysql++/mysql++.h>

#include "DefaultHandler.h"
#include "../../MysqlHandler.H"
#include "../../functions/Functions.h"

namespace defaul
{
	void DefaultHandler::update()
	{
	
		/**
		* Default fleet action (return fleed immediately)
		*/ 
	
		// Select correct action
		std::string action;
		if(strlen(fleet_["fleet_action"])>0)
		{
			char str[4] = "";
			strcpy( str, fleet_["fleet_action"]);
			action = str[0] + "r";
		}
		else
			action = "_r";

		// Flotte zurückschicken
		fleetReturn(action);
	}
}

