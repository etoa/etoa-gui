//----------------------------------------------------------------------------
//   Simple Socket                                                  v1.1
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

//   Revision history
//
//   v1.1 May 2002
//   Socket::getHandle() method added
//----------------------------------------------------------------------------

#ifndef SIMPLESOCKET_H
#define SIMPLESOCKET_H

#include <string>

#if defined(__sgi)
  #include <fstream.h> // No part of STL on SGI
  #include <iostream.h>
#else
  #include <fstream>
  #include <iostream>
  using namespace std; // needed for iostream and fstream
#endif

#include "SimpleException.h"

//---
// The Simple C++ Library
// is placed in the namespace 'simple'
//---
namespace simple
{

//-----------------------------------------------------------------------------
// A socket exception is used by the Simple Socket library for
// reporting errors
// SocketException inherits from the simple::Exception class
// (see SimpleException.h for more information)
//-----------------------------------------------------------------------------
class SocketException : public Exception
{
   public:
      SocketException(const Msg& message)
         : Exception(message) {}
};

//-----------------------------------------------------------------------------
// A socket represents an end point of a TCP/IP connection.
//
// A client program creates a socket that connects to a
// server at a given port.
//
// A server program gets a socket from a ServerSocket object
//
// Clients and servers communicate between socket using streams.
//-----------------------------------------------------------------------------
class Socket
{
public:
   Socket(int sockHandle);
   Socket(std::string hostAddr, int port) throw(SocketException);
   ~Socket();

   bool isValid();

   int getHandle();

   iostream& getStream();

private:
   fstream streamM;

   int socketHdlM;
};

//-----------------------------------------------------------------------------
// A server socket listens on a given port.
//
// The accept method creates a socket when a client connects to
// this port.
//
// The accept method can be used repeatedly to connect the server
// to more than one client. Each client connection is represented
// by its own socket.
//-----------------------------------------------------------------------------
class ServerSocket
{
public:
   ServerSocket(int port) throw(SocketException);
   ~ServerSocket();

   bool isValid();

   Socket* accept() throw(SocketException);

private:
   int socketHdlM; // handle of the server socket
};

} // end of namespace simple

#endif // SIMPLESOCKET_H
