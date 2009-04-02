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

IPCMessageQueue::IPCMessageQueue(std::string token)
{
	_valid = false;
	char proj_id = 'A';
  key = ftok(token.c_str(),proj_id);
  std::clog << "Creating IPC key " << key << " from token " << token << " with project id " << proj_id << std::endl;
  msgqid=msgget(key,0666|IPC_CREAT);
  if (msgqid < 0) 
  {
    std::clog << strerror(errno) << std::endl;
    std::clog << "Error getting message queue, msgget() failed, msgqid = " << msgqid << std::endl;
    return;
  }
  std::clog << "Message queue gets id " << msgqid << std::endl;
   _valid = true;  
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

bool IPCMessageQueue::valid()
{
	return _valid;		
}

std::string IPCMessageQueue::rcv()
{
  struct my_msgbuf buf;

	if (msgrcv(msgqid, (struct msgbuf *)&buf, sizeof(buf), 0, 0) < 0) 
	{
		std::clog << strerror(errno) << std::endl;
		std::clog << "Error getting ipc message from queue " << std::endl;
		return NULL;
  }
	return std::string(buf.mtext);
}
