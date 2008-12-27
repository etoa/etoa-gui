
#include "FleetShip.h"

	void FleetShip::save() {
		if (this->isChanged) {

			My &my = My::instance();
			mysqlpp::Connection *con = my.get();
			mysqlpp::Query query = con->query();

			if (this->getCount() > 0) {
				query << "UPDATE ";
				query << "	fleet_ships ";
				query << "SET ";
				query << "	fs_ship_cnt='" << this->getCount() << "', ";
				query << "	fs_special_ship_exp = '" << this->getSExp() << "' ";
				query << "WHERE ";
				query << "	fs_id='" << this->getId() << "' ";
				query << "LIMIT 1;";
				query.store();
				query.reset();
			}
			else {
				query << "DELETE FROM ";
				query << " fleet_ships ";
				query << "WHERE ";
				query << "	fs_id='" << this->getId() << "' ";
				query << "LIMIT 1;";
				query.store();
				query.reset();
			}
		}
	}
