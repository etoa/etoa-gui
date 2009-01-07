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

	private:
    struct my_msgbuf {
        long mtype;
        char mtext[200];
    };
	
};

#endif
