
#include "DefList.h"

namespace def
{
	void DefList::add(int planetId, int userId, int defId, int count)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		count = count < 0 ? 0 : count;

		mysqlpp::Query query = con_->query();
		query << "INSERT INTO "
			<< "	deflist ("
			<< "	deflist_user_id, "
			<< "	deflist_entity_id, "
			<< "	deflist_def_id, "
			<< "	deflist_count "
			<< ") VALUES ( "
			<< "	" << userId << ", "
			<< "	" << planetId << ", "
			<< "	" << defId << ", "
			<< "	" << count << ") "
			<< "ON DUPLICATE KEY "
			<< "	UPDATE "
			<< "		deflist_count=deflist_count+VALUES(deflist_count);";
		query.store();
		query.reset();
	}
}
