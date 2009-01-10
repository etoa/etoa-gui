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
// Pidfile manager class
//

#ifndef __PIDFILE_H
#define __PIDFILE_H

#include <string>

/** 
* Manages a pidfile containing the pid (posix-style process id) 
* of this program.
*
* @author Nicolas Perrenou <mrcage@etoa.ch>
*/
class PIDFile 
{
  public:
    PIDFile(const std::string &filename);
    ~PIDFile();
		/**
		* Checks if the pidfile already exists
		*/
		bool fileExists();
    /** 
    * Open and lock pidfile, write current process PID to it. 
    */
    void write();
		/**
		* Read pid from file
		*/
		int readPid();
  private:
    /** 
    * Pathname to the pidfile. 
    */
    std::string pidfile_path;
    /** 
    * File descriptor for locking the pidfile. 
    */
    int pidfile_fd;
};

#endif
