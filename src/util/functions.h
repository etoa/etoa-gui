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
// Miscelaneous helper functions
//

#ifndef FUNCTIONS_H
#define FUNCTIONS_H

#include <sstream>
#include <string>
#include <vector>

/**
* Tries to convert anything to a std::string using stringstream buffer
*/
template <class T>inline std::string toString(const T& t)
{
	std::stringstream ss;
	ss << t;
	return ss.str();
}

/**
* Tries to convert anything to an int stringstream buffer
*/
template <class T>inline int toInt(const T& t)
{
	std::istringstream isst;
	int zahl=0;
	isst.str(t);
	isst >> zahl;
	return zahl;
}







/**
* Splits a given text by its separators and stores it in a vector
*/
void explode(std::string& text, std::string& separators, std::vector<std::string>& words)
{
	int n = text.length();
	int start, stop;

	start = text.find_first_not_of(separators);
	while ((start >= 0) && (start < n)) 
	{
		stop = text.find_first_of(separators, start);
		if ((stop < 0) || (stop > n)) stop = n;
		words.push_back(text.substr(start, stop - start));
		start = text.find_first_not_of(separators, stop+1);
	}
}

#endif
