//   Simple Exception                                                v1.0
//   Copyright (c) 2001          Wim Bokkers
//----------------------------------------------------------------------------
//
//   e.bokkers@hetnet.nl
//
//   bokkers@go.to
//
//----------------------------------------------------------------------------
//   This file is part of the Simple C++ Library
//
//   Permission is granted to use and distribute this library as long as the
//   copyright and this permission notice appear and provided that no changes
//   are made in any respect without contacting the author about the changes.
//
//   Any suggestions?
//   Please send an e-mail to the author with your name and e-mail address.
//----------------------------------------------------------------------------

#ifndef SIMPLE_EXCEPTION_H
#define SIMPLE_EXCEPTION_H

#include <string>

#if defined(__sgi)
  #include <iostream.h>
#else
  #include <iostream>
  using namespace std; // needed for iostream and fstream
#endif

#include "SimpleMessage.h"

//---
// The Simple C++ Library
// is placed in the namespace 'simple'
//---
namespace simple
{

class Exception
{
   public:
      friend ostream& operator<<(ostream& os, Exception& ex);
      
      Exception(const Msg& message)
      {
         messageM = message.str();
      }
   
   private:
      std::string messageM;
};

inline ostream& operator<<(ostream& os, Exception& ex)
{
   os << ex.messageM;
   
   return os;
}

} // end of namespace simple

#endif
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              