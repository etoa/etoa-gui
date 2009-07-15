//////////////////////////////////////////////////
//		 	 ____    __           ______       			//
//			/\  _`\ /\ \__       /\  _  \      			//
//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
//																					 		//
//////////////////////////////////////////////////
// The Andromeda-Project-Browsergame				 		//
// Ein Massive-Multiplayer-Online-Spiel			 		//
// (C) by EtoA Gaming | www.etoa.ch   			 		//
//////////////////////////////////////////////////
//
// Logging of notices and errors to a given logfile
//

#ifndef LOGGER_H
#define LOGGER_H

#include <string>
#include <fstream>
#include <sstream>
#include <iostream>
#include <time.h>
#include <streambuf>
#include <string>
#include <cstring>

#include "Mutex.h"

class LogBuf: public std::streambuf
{
	private:
	  std::streambuf	*i_sbuf;	// the actual streambuf used to read and write chars
	  unsigned int	i_len;		// the length of the prefix
	  char		*i_prfx;	// the prefix
	  bool		i_newline;	// remember whether we are at a new line
	  int		i_cache;	// may cache a read character
	  bool	skip_prefix();
		
		Mutex* mtx;
		
		std::stringstream ss;
	  std::string outFilePath;
	protected:
	  int	overflow(int);
	  int	underflow();
	  int	uflow();
		std::streamsize xsputn ( const char* s, std::streamsize n );
	public:
	  LogBuf(std::streambuf *sb, std::string filePath);
	  ~LogBuf();
};

class LogStream: public std::ostream
{
	public:
	  LogStream(std::streambuf *sb, std::string filePath);
	  ~LogStream();
};

class Logger	
{
	public:
		Logger(std::string logFilePath);
		~Logger();

	private: 
		std::streambuf* origClogBuf; 
		std::stringstream ls;
};


#endif
