
#include "ListShip.h"

	void ListShip::save() {
		if (this->isChanged) {

			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
			mysqlpp::Query query = con->query();

			if (this->getCount() > 0) {
				query << "UPDATE ";
				query << "	shilist ";
				query << "SET ";
				query << "	shiplist_count='" << this->getCount() << "', ";
				query << "	shiplist_special_ship_exp = '" << this->getSExp() << "' ";
				query << "WHERE ";
				query << "	shiplist_id='" << this->getId() << "' ";
				query << "	AND shiplist_user_id='" << this->getUserId() << "' ";
				query << "LIMIT 1;";
				query.store();
				query.reset();
			}
			else {
				query << "DELETE FROM ";
				query << " shiplist ";
				query << "WHERE ";
				query << "	shiplist_id='" << this->getId() << "' ";
				query << "	AND shiplist_user_id='" << this->getUserId() << "' ";
				query << "LIMIT 1;";
				query.store();
				query.reset();
			}
		}
	}
