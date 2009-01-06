//----------------------------------------------------------------------------
//   Simple Socket Library                                   v1.0
//   Copyright (c) 2001          Wim Bokkers
//----------------------------------------------------------------------------
//
//   e.bokkers@hetnet.nl
//
//   bokkers@go.to
//
//----------------------------------------------------------------------------
//   Permission is granted to use and distribute this library as long as the
//   copyright and this permission notice appear and provided that no changes
//   are made in any respect without contacting the author about the changes.
//
//   Any suggestions?
//   Please send an e-mail to the author with your name and e-mail address.
//----------------------------------------------------------------------------

#if defined(__sgi)
   #include <iostream.h> // No part of STL on SGI
   #include <iomanip.h>
#else
   #include <iostream>
   #include <iomanip>
#endif

#include <string>

using namespace std;

#include "SimpleSocket.h"

//---
// All classes in the SimpleSocket library are located
// in the namespace 'simple'. So don't forget to make
// the namespace visible.
//---
using namespace simple;


int main()
{
   try
   {
      //---
      // Create a socket which connects to the local host on
      // port 1969
      //---
      Socket sock("localhost", 1969);

      // Other way to connect to local host:
      // Sock sock("127.0.0.1", 1969);

      //---
      // Get the socket stream, which can be used as a normal
      // file stream
      //---
      iostream& s = sock.getStream();

      //---
      // EXAMPLE 1:
      //    This example shows how strings, ending with a newline can
      //    be send and received
      //
      // Write a string to the server (through the socket stream)
      // (The server is waits for it...)
      // Writing endl will cause the stream to flush.
      //---
      s << "Hi, server! Please echo this back" << endl;


      //---
      // EXAMPLE 2:
      //    This example shows how strings can be flushed with the
      //    flush method
      //
      // Get the server reponse (through the socket stream)
      // (getline consumes characters until the first newline character)
      // The response is printed.
      //---
      string aStr;
      getline(s, aStr);

      cout << aStr << "\n\n";

      //---
      // EXAMPLE 3:
      //    This example shows how strings can be flushed with the
      //    endl manipulator
      //
      // Get another string from the server (through the socket stream)
      // This string is printed too.
      //---
      getline(s, aStr);

      cout << aStr << "\n\n";


      //---
      // EXAMPLE 4:
      //    This example shows how data types like int and double
      //    can be send and received.
      //
      // Get other data types from the server and print them
      //---
      double aDouble;
      int    aInt;
      char   aChar;

      s >> aDouble;
      s >> aInt;
      s >> aChar;

      cout << "double = " << aDouble << "\n"
           << "int    = " << aInt << "\n"
           << "char   = " << aChar << "\n\n";


      //---
      // EXAMPLE 5:
      //    This example makes clear that data types like int and double
      //    are send and received as charachter strings.
      //    In addition it is showed that socket streams can be used
      //    as normal file streams.
      //
      // Get remainder of server response, on char by char basis.
      // (make sure that white space is not skipped)
      //---
      s.unsetf(ios::skipws);

      while(s)
      {
         s >>  aChar;
         cout << aChar;
      }

      cout << endl;
   }
   catch(SocketException& se)
   {
      //---
      // Exceptions of type SocketException are throwed
      // when an exception occurres in the Simple Socket Library
      //---
      cerr << se << endl;
   }
   catch(...)
   {
      cerr << "Unknown error "<< endl;
   }
}
