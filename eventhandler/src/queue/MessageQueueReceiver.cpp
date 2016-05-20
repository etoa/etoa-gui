#include "MessageQueueReceiver.h"
#include "../util/Log.h"

std::vector<MessageQueueCommand> MessageQueueReceiver::receive()
{
	std::vector<MessageQueueCommand> results;

	My &my = My::instance();
	mysqlpp::Connection* con_ = my.get();
	
	mysqlpp::Query query = con_->query();
	query << "SELECT id,cmd,arg FROM backend_message_queue;";
	RESULT_TYPE res = query.store();
	query.reset();
	if (res) {
		unsigned int resSize = res.size();
		if (resSize) {
			std::vector<int> ids;

			// Iterate over entries and add them to list
			for (mysqlpp::Row::size_type i = 0; i<resSize; i++) { 
				mysqlpp::Row row = res.at(i);
				struct MessageQueueCommand mqc;
				mqc.cmd = std::string(row["cmd"]);
				mqc.arg = std::string(row["arg"]);
				results.push_back(mqc);
				ids.push_back((int)row["id"]);
			}
			LOG(LOG_DEBUG, "Read " << resSize << " messages from command queue");

			// Remove received entries
			for(std::vector<int>::iterator it = ids.begin(); it != ids.end(); ++it) {
				query = con_->query();
				query << "DELETE FROM backend_message_queue WHERE id=" << *it << ";";
				query.store();
				query.reset();
			}
		}
	}
	
	return results;
}
