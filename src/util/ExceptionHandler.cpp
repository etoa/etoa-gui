#include "ExceptionHandler.h"

const char * ExceptionHandler::what() const throw()
{
  std::string out = s + std::endl;
  return out.c_str();
}
