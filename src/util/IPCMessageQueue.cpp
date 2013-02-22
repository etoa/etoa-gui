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
#include "Log.h"

IPCMessageQueue::IPCMessageQueue(std::string token)
{
	_valid = false;
	char proj_id = 'A';
	key = ftok(token.c_str(), proj_id);
	LOG(LOG_NOTICE, "Creating IPC key '" << key << "' (Token '" << token << "', project id '" << proj_id << "')");
	msgqid = msgget(key, 0666 | IPC_CREAT);
	if (msgqid < 0)
	{
		LOG(LOG_ERR, strerror(errno) << ". Error getting message queue, msgget() failed, msgqid = " << msgqid);
		return;
	}
	LOG(LOG_DEBUG, "Message queue gets id " << msgqid);
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
		LOG(LOG_ERR,strerror(errno)<<". Error getting ipc message from queue ");
		return NULL;
  }
	return std::string(buf.mtext);
}
