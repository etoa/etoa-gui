
#include "ListDef.h"

	void ListDef::save() {
		if (this->isChanged) {

			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
			mysqlpp::Query query = con->query();

			if (this->getCount() > 0) {
				query << "UPDATE ";
				query << "	deflist ";
				query << "SET ";
				query << "	deflist_count='" << this->getCount() << "' ";
				query << "WHERE ";
				query << "	deflist_id='" << this->getId() << "' ";
				query << "	AND deflist_user_id='" << this->getUserId() << "' ";
				query << "LIMIT 1;";
				query.store();
				query.reset();
			}
			else {
				query << "DELETE FROM ";
				query << " deflist ";
				query << "WHERE ";
				query << "	deflist_id='" << this->getId() << "' ";
				query << "	AND deflist_user_id='" << this->getUserId() << "' ";
				query << "LIMIT 1;";
				query.store();
				query.reset();
			}
		}
	}
