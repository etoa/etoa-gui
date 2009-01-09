#ifndef EXCEPTIONHANDLER_H
#define EXCEPTIONHANDLER_H

#include <exception>
#include <iostream>
#include <string>

class ExceptionHandler: public std::exception
{
	public:
	  ExceptionHandler(std::string msg) {this->s = msg;}
		virtual ~ExceptionHandler() throw() {}
	  virtual const char * what() const throw();
	private:
	    std::string s;
};

#endif
