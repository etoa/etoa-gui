#include "Debug.h"

bool debugEnable(int enable)
{
	static int enabled = 0;
	if (enable == 1)
	{
			std::cout << std::endl << "*** DEBUG MODE ENABLED ***" << std::endl << std::endl;
			enabled = 1;
	}

	if (enabled==1)
		return true;
	return false;
}
