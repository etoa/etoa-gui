
#include "ShipList.h"

namespace ship
{
	void ShipList::add(int planetId, int userId, int shipId, int count)
	{
		My &my = My::instance();
		mysqlpp::Connection *con_ = my.get();
		count = count < 0 ? 0 : count;
		
		mysqlpp::Query query = con_->query();
		
		DataHandler &DataHandler = DataHandler::instance();
		ShipData *data = DataHandler.getShipById(shipId);
		
		if (data->getSpecial()) {
			query << "INSERT INTO "
				<< "	shiplist ("
				<< "	shiplist_user_id, "
				<< "	shiplist_entity_id, "
				<< "	shiplist_ship_id, "
				<< "	shiplist_count, "
				<< "	shiplist_special_ship "
				<< ") VALUES ( "
				<< "	" << userId << ", "
				<< "	" << planetId << ", "
				<< "	" << shipId << ", "
				<< "	" << count << ", "
				<< "	" << data->getSpecial() << ") "
				<< "ON DUPLICATE KEY "
				<< "	UPDATE "
				<< "		shiplist.`shiplist_count` = shiplist.`shiplist_count` + VALUES(shiplist.`shiplist_count`),"
				<< "		shiplist.`shiplist_special_ship_level` = '0' , "
				<< "		shiplist.`shiplist_special_ship_exp` = '0' , "
				<< "		shiplist.`shiplist_special_ship_bonus_weapon` = '0' , "
				<< "		shiplist.`shiplist_special_ship_bonus_structure` = '0' , "
				<< "		shiplist.`shiplist_special_ship_bonus_shield` = '0' , "
				<< "		shiplist.`shiplist_special_ship_bonus_heal` = '0' , "
				<< "		shiplist.`shiplist_special_ship_bonus_capacity` = '0' , "
				<< "		shiplist.`shiplist_special_ship_bonus_speed` = '0' , "
				<< "		shiplist.`shiplist_special_ship_bonus_pilots` = '0' , "
				<< "		shiplist.`shiplist_special_ship_bonus_tarn` = '0' , "
				<< "		shiplist.`shiplist_special_ship_bonus_antrax` = '0' , "
				<< "		shiplist.`shiplist_special_ship_bonus_forsteal` = '0' , "
				<< "		shiplist.`shiplist_special_ship_bonus_build_destroy` = '0' , "
				<< "		shiplist.`shiplist_special_ship_bonus_antrax_food` = '0' , "
				<< "		shiplist.`shiplist_special_ship_bonus_deactivade` = '0';";
		}
		else
		{
			query << "INSERT INTO "
				<< "	shiplist ("
				<< "	shiplist_user_id, "
				<< "	shiplist_entity_id, "
				<< "	shiplist_ship_id, "
				<< "	shiplist_count "
				<< ") VALUES ( "
				<< "	" << userId << ", "
				<< "	" << planetId << ", "
				<< "	" << shipId << ", "
				<< "	" << count << ") "
				<< "ON DUPLICATE KEY "
				<< "	UPDATE "
				<< "		shiplist.`shiplist_count` = shiplist.`shiplist_count` + VALUES(shiplist.`shiplist_count`);";
		}
		query.store();
		query.reset();
	}
}
