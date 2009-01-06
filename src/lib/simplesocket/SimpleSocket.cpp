//----------------------------------------------------------------------------
//   Simple Socket                                                    v1.1
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

#if defined(__sgi)
   #include <strstream.h> // No part of STL on SGI
#else
   #include <strstream>
#endif

#include "SimpleSocket.h"


extern "C"
{
   void close(int filedes);
}

#include <errno.h>
#include <netinet/in.h>
#include <netdb.h>
#include <string.h>
#include <sys/socket.h> /* Added by Wim Bokkers. cygwin needs it! */


//---
// The Simple C++ Library
// is placed in the namespace 'simple'
//---
namespace simple
{

using namespace std;

//-------------------------------------------------------------------------
// S o c k e t
//-------------------------------------------------------------------------

//-------------------------------------------------------------------------
// Constructor.
// This constructor is used by the ServerSocket::accept method.
// The socket handle is set and the stream is attached to this
// handle.
//-------------------------------------------------------------------------
Socket::Socket(int sockHandle)
   : socketHdlM(sockHandle)
{
   streamM.attach(socketHdlM);
}

//-------------------------------------------------------------------------
// Constructor
// This constructor is used when creating a socket in client
// programs.
// Try to make a connection to a listening server.
//-------------------------------------------------------------------------
Socket::Socket(string hostAddr, int port) throw(SocketException)
{
   strstream errStream;

   char *host = const_cast<char*>(hostAddr.c_str());

   struct hostent * hostinfo=gethostbyname(host);
   if (hostinfo == NULL  || h_errno != 0)
   {
      throw SocketException(Msg()<<"Error searching for host "<< hostAddr);
   }

   socketHdlM = socket(AF_INET,SOCK_STREAM,0);
   if ( socketHdlM == -1)
   {
      throw SocketException("Cannot create socket");
   }

   struct in_addr * addp=(struct in_addr *)*(hostinfo->h_addr_list);

   struct sockaddr_in rsock;
   memset ((char *)&rsock,0,sizeof(rsock));

   rsock.sin_addr=*addp;
   rsock.sin_family=AF_INET;
   rsock.sin_port=htons(port);

   if ( connect(socketHdlM,(struct sockaddr *)(&rsock),sizeof(rsock)) == -1 )
   {
      throw SocketException(Msg()<<"Cannot connect "<< hostAddr <<  " on port "<< port);
   }

   streamM.attach(socketHdlM);
}

//-------------------------------------------------------------------------
// Destructor.
// Close the stream attached to the socket handle.
// This will flush the stream buffer.
//-------------------------------------------------------------------------
Socket::~Socket()
{
   if(socketHdlM != -1)
   {
     streamM.close();
   }
}

//-------------------------------------------------------------------------
// Get the sream associated with the socket.
// Communication is the same as with file streams.
//-------------------------------------------------------------------------
iostream& Socket::getStream()
{
   return streamM;
}

//-------------------------------------------------------------------------
// Get the socket handle.
//-------------------------------------------------------------------------
int Socket::getHandle()
{
   return socketHdlM;
}


//-------------------------------------------------------------------------
// Check whether the socket has a valid socket handle
//-------------------------------------------------------------------------
bool Socket::isValid()
{
  return socketHdlM != -1;
}

//-------------------------------------------------------------------------
// S e r v e r S o c k e t
//-------------------------------------------------------------------------

//-------------------------------------------------------------------------
// Constructor
// Let the server listen on the given port.
// Allow any internet address to connect.
//-------------------------------------------------------------------------
ServerSocket::ServerSocket(int port) throw(SocketException)
{
   strstream errStream;

   const char* host = NULL; // OR given as extra parameter!!!!!
   const int backlog = 10;  // OR given as extra parameter!!!!!

   struct sockaddr_in sockname;
   memset ((char *)&sockname,0,sizeof(sockname));

   struct hostent * hostinfo;
   if (host == NULL)
   {
     hostinfo = NULL;
   }
   else if ( (hostinfo=gethostbyname(host)) == NULL )
   {
      throw SocketException(Msg()<<"Cannot find host "<< host);
   }

   if( (socketHdlM = socket(AF_INET,SOCK_STREAM,0)) == -1 )
   {
      throw SocketException("Cannot open socket");
   }

   const int on = 1;
   setsockopt(socketHdlM, SOL_SOCKET, SO_REUSEADDR, &on, sizeof(on));

   if (hostinfo != NULL)
   {
        struct in_addr * addp =(struct in_addr *)*(hostinfo->h_addr_list);
        sockname.sin_addr=*addp;
   }
   else
   {
     sockname.sin_addr.s_addr=INADDR_ANY;
   }

   sockname.sin_family=AF_INET;
   sockname.sin_port=htons(port);

   if ( (bind(socketHdlM,(struct sockaddr *)&sockname,sizeof(sockname))) == -1 )
   {
     close (socketHdlM);

     throw SocketException(Msg()<<"Cannot bind port "<< port << " at "<< host);
   }

   listen(socketHdlM,backlog);
}

//-------------------------------------------------------------------------
// Destructor
// Close the handle of the server socket
//-------------------------------------------------------------------------
ServerSocket::~ServerSocket()
{
  if(socketHdlM != -1)
     ::close(socketHdlM);
}

//-------------------------------------------------------------------------
// Check whether the server socket has a valid socket handle
//-------------------------------------------------------------------------
bool ServerSocket::isValid()
{
   return socketHdlM != -1;
}

//-------------------------------------------------------------------------
// Accept a client connection. Return a socket object when a
// connection is accepted.
//
// The user is responsible to delete() the returned socket !!!
//-------------------------------------------------------------------------
Socket* ServerSocket::accept() throw(SocketException)
{
   int newsockHdl = ::accept(socketHdlM, 0, 0);

   if(newsockHdl == -1)
      throw SocketException("Unable to accept client connection");

   return new Socket(newsockHdl);
}

} // end of namespace simple