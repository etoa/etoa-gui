#ifndef LOGGER_H
#define LOGGER_H

#include <string>
#include <fstream>

/**
* Simple logging interface for EtoA
* 
* @author Nicolas Perrenoud <mrcage@etoa.ch>
*/
class Logger	
{
	public:
    static Logger* getInstance();
		~Logger();
		void add(std::string msg);		
		void setFile(std::string filePath);

	private:
		Logger();
		Logger(const Logger& cc);

		std::string _filePath;
		std::ofstream _logfileStream;
	
};

#endif
