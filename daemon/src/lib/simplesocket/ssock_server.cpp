//----------------------------------------------------------------------------
//   Simple Socket Library                                   v1.0
//   Copyright (c) 2001         Wim Bokkers
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
#else
   #include <iostream>
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
      // Make a server socket listening on port 1969
      //---
      ServerSocket serversock(1969);

      //---
      // Wait for a client connection and get a
      // socket for communication
      //---
      Socket *sock = serversock.accept();


      //---
      // Get the socket stream, which can be used as a normal
      // file stream
      //---
      iostream& s = sock->getStream();

      //---
      // EXAMPLE 1:
      //    This example shows how strings, ending with a newline can
      //    be send and received
      //
      // Get a string from the client (through the socket stream)
      // (getline consumes characters until the first newline character)
      //---
      string aStr;
      getline(s, aStr);

      //---
      // EXAMPLE 2:
      //    This example shows how strings can be flushed with the
      //    flush method
      //
      // Write the string to the client (through the socket stream)
      // (The charachters written are not flushed)
      //---
      s << "Server echo: " << aStr<< "\n";

      //---
      // Flush the socket stream so the client can read the string
      //---
      s.flush();

      //---
      // EXAMPLE 3:
      //    This example shows how strings can be flushed with the
      //    endl manipulator
      //
      // Write another string to the client followed by endl.
      // Sending endl will flush the stream
      //---
      s << "This string is flushed by the endl manipulator" << endl;


      //---
      // EXAMPLE 4:
      //    This example shows how data types like int and double
      //    can be send and received.
      //
      // Write other data types to the client
      //---
      s << 3.14159 << " ";
      s << 1234 << " ";
      s << 'c';
      s.flush();


      //---
      // EXAMPLE 5:
      //    This example makes clear that data types like int and double
      //    are send and received as charachter strings.
      //    In addition it is showed that socket streams can be used
      //    as normal file streams.
      //
      // Write some other stuff to the client
      //---
      s << "Hi, client! Sending some other stuff...\n"
        << "Here is a double   : " << 3.14159 << "\n"
        << "Here is an integer : " << 123456789 << "\n"
        << "Here is a character: " << 'c' << "\n";


      //---
      // Delete the socket.
      // This causes the socket stream to flush
      //---
      delete sock;
   }
   catch(SocketException& se)
   {
      //---
      // Exceptions of type SocketException are throwed
      // when an exception occurres in the Simple Socket Library
      //---
      cerr << se << endl;
   }
   catch(string& e)
   {
      //---
      // Exceptions of type string are throwed
      // when an exception occurres in the Simple Socket Library
      //---
      cerr << e << endl;
   }
   catch(...)
   {
      cerr << "Unknown error " << endl;
   }
}
