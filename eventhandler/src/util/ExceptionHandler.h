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
// Exception handling class
//

#ifndef EXCEPTIONHANDLER_H
#define EXCEPTIONHANDLER_H

#include <exception>
#include <iostream>
#include <string>
#include <execinfo.h>
#include <signal.h>
#include <iostream>
#include <sstream>

/**
* Provides a simple exception handling
*
* @author Nicolas Perrenoud <mrcage@etoa.ch>
*/
class ExceptionHandler: public std::exception
{
	public:
		/**
		* Exception handler constructor
		*/
	  ExceptionHandler(std::string msg) {this->s = msg;}
	  /**
	  * Exception handler destructor
	  */	  
		virtual ~ExceptionHandler() throw() {}
		/**
		* Returns error message
		*/
	  //virtual const char * what() const throw();
	private:
		std::string s;
};

#endif
