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
// Interprocess Message queue manager
//

#include "IPCMessageQueue.h"

IPCMessageQueue::IPCMessageQueue()
{
	
}

IPCMessageQueue::~IPCMessageQueue()
{
		
}

void IPCMessageQueue::rcvCommand(std::string* command, int* id)
{
	std::string str = rcv();
	if (str != "")
	{
		std::vector<std::string> res;
		std::string sep = ":";
		etoa::explode(str,sep,res);
		*command = res[0];
		*id = etoa::toInt(res[1]);
		return;
	}
	(*command) = "";
	(*id) = 0;
	return;
}

std::string IPCMessageQueue::rcv()
{
  struct my_msgbuf buf;
  int msqid;
  key_t key = 7543;

	try
	{
	  if ((msqid = msgget(key, 0644)) == -1) 
	  {
	 		throw ExceptionHandler("Could nod init ipc message queue with key "+etoa::toString(key));
	  }  
	
		if (msgrcv(msqid, (struct msgbuf *)&buf, sizeof(buf), 0, 0) == -1) 
		{
 			throw ExceptionHandler("Error getting ipc message from queue");
    }
    return std::string(buf.mtext);
	}
	catch (ExceptionHandler& e)	
	{
		std::cout << e.what();
	}
  return NULL;
}
