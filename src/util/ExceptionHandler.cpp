#include "ExceptionHandler.h"

#include <execinfo.h>
#include <signal.h>
#include <iostream>
#include <sstream>

const char * ExceptionHandler::what() const throw()
{
  std::stringstream sout;
  sout << s;
  	
	void * array[25];
	int nSize = backtrace(array, 25);
	char ** symbols = backtrace_symbols(array, nSize);

	for (int i = 0; i < nSize; i++)
	{
   	sout << symbols[i] << std::endl;
	}
  free(symbols);

  return sout.str().c_str();
}
