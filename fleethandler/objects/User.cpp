
#include "User.h"

	void User::addCollectedWf(int res) {
		if (res>0) {
			My &my = My::instance();
			mysqlpp::Connection *con_ = my.get();
			
			mysqlpp::Query query = con_->query();
			query << "UPDATE ";
			query << "	users ";
			query << "SET ";
			query << "	user_res_from_tf=user_res_from_tf+'" << res << "' ";
			query << "WHERE ";
			query << "	user_id='" << this->userId << "';";
			query.store();
			query.reset();
		}
	}