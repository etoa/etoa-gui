#include <iostream>

#include "DefaultHandler.h"

namespace defaul
{
	void DefaultHandler::update()
	{
	
		/**
		* Default fleet action (return fleed immediately)
		*/ 

		/** Send the fleet back home **/
		fleetReturn(1);
	}
}
