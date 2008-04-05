#include <iostream>
#include <set>
#include <vector>
#include "../MysqlHandler.h"

#include <mysql++/mysql++.h>

#include "ConfigHandler.h"

	std::string Config::get(std::string name, int value)
	{
		std::vector<std::string> temp (3);
		temp = cConfig.at(sConfig[name]);
		return(temp[value]);
	}