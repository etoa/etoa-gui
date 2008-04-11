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
	
	double Config::nget(std::string name, int value)
	{
		std::string temp = get(name, value);
		double var = atof(temp.data());
		return(var);
	}
	
	double Config::idget(std::string name)
	{
		return(idConfig[name]);
	}