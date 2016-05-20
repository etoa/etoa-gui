#ifndef MESSAGEQUEUECOMMAND_H
#define MESSAGEQUEUECOMMAND_H

#include <string>
#include <sys/ipc.h>
#include <sys/msg.h>
#include <vector>
#include <sys/errno.h>

#include "../util/ExceptionHandler.h"
#include "../util/Functions.h"

struct MessageQueueCommand {
	std::string cmd;
	std::string arg;
};

/**
* Receives command messages from a queue
*/
class MessageQueueReceiver	
{
	public:
		MessageQueueReceiver() {}
		~MessageQueueReceiver() {}
		std::vector<MessageQueueCommand> receive();
	private:
	
};

#endif