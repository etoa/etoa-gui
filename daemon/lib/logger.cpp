#include <time.h>
#include <iostream>
#include <fstream>

#include "logger.h"

using namespace std;

Logger::Logger()
{
	_filePath = "";
}

Logger::~Logger()
{
	
}

Logger* Logger::getInstance()
{
  static Logger instance; 
  return &instance; 
}

void Logger::setFile(std::string filePath)
{
	_filePath = filePath;
}

void Logger::add(std::string msg)
{
	if (_filePath != "")
	{
	  _logfileStream.open(_filePath.c_str(), ios::out | ios::app);
	  if (_logfileStream.is_open())
  	{
			struct tm *current;
			time_t now;
			time(&now);
			current = localtime(&now);
			char* a = asctime(current);
			a[strlen(a) - 1] = '\0';		

			_logfileStream << "["<< a <<"] ";
		  _logfileStream << msg<<endl;
		  _logfileStream.close();
		}
		else
			std::cout << "Error writing logfile "<<_filePath<<endl;
	}
	else
		std::cout << "Consider setting a logfile path first!"<<endl;
		
}


