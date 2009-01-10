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
// Interprocess Message queue manager class
//

#include <string>
#include <sys/ipc.h>
#include <sys/msg.h>

#ifndef IPCMESSAGEQUEUE_H
#define IPCMESSAGEQUEUE_H

/**
* Simple logging interface for EtoA
* 
* @author Nicolas Perrenoud <mrcage@etoa.ch>
*/
class IPCMessageQueue	
{
	public:
    IPCMessageQueue();
		~IPCMessageQueue();
		std::string rcv();		
		void rcvCommand(std::string* command, int* id);
	private:
    struct my_msgbuf {
        long mtype;
        char mtext[200];
    };
	
};

#endif
