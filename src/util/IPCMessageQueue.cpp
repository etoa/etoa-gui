#include "ExceptionHandler.h"
#include "IPCMessageQueue.h"
#include "functions.h"

IPCMessageQueue::IPCMessageQueue()
{
	
}

IPCMessageQueue::~IPCMessageQueue()
{
		
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
	 		throw ExceptionHandler("Could nod init ipc message queue with key "+toString(key));
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
