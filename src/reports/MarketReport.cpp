
#include "MarketReport.h"

void MarketReport::setBuy(unsigned int res0,
					   unsigned int res1,
					   unsigned int res2,
					   unsigned int res3,
					   unsigned int res4,
					   unsigned int res5) {
	this->buy0 = res0;
	this->buy1 = res1;
	this->buy2 = res2;
	this->buy3 = res3;
	this->buy4 = res4;
	this->buy5 = res5;
}

void MarketReport::setSell(unsigned int res0,
					   unsigned int res1,
					   unsigned int res2,
					   unsigned int res3,
					   unsigned int res4,
					   unsigned int res5) {
	this->sell0 = res0;
	this->sell1 = res1;
	this->sell2 = res2;
	this->sell3 = res3;
	this->sell4 = res4;
	this->sell5 = res5;
}

void MarketReport::setFactor(double factor) {
	this->factor = factor;
}

void MarketReport::setFleet1Id(unsigned int fleet1Id) {
	this->fleet1Id = fleet1Id;
}

void MarketReport::setFleet2Id(unsigned int fleet2Id) {
	this->fleet2Id = fleet2Id;
}

void MarketReport::saveMarketReport() {
	My &my = My::instance();
	mysqlpp::Connection *con_ = my.get();
	
	mysqlpp::Query query = con_->query();
	
	try	{
		if (!this->id) throw 0;
		query << std::setprecision(18);
		query << "INSERT INTO "
			<< "	`reports_market` "
			<< "( "
			<< "	`id`, "
			<< "	`subtype`, "
			<< "	`record_id`, "
			<< "	`sell_0`, "
			<< "	`sell_1`, "
			<< "	`sell_2`, "
			<< "	`sell_3`, "
			<< "	`sell_4`, "
			<< "	`sell_5`, "
			<< "	`buy_0`, "
			<< "	`buy_1`, "
			<< "	`buy_2`, "
			<< "	`buy_3`, "
			<< "	`buy_4`, "
			<< "	`buy_5`, "
			<< "	`factor`, "
			<< "	`fleet1_id`, "
			<< "	`fleet2_id` "
			<< ") "
			<< "VALUES "
			<< "( "
			<< "	'" << this->id << "', "
			<< "	'" << this->subtype << "', "
			<< "	'" << this->recordId << "', "
			<< "	'" << this->sell0 << "', "
			<< "	'" << this->sell1 << "', "
			<< "	'" << this->sell2 << "', "
			<< "	'" << this->sell3 << "', "
			<< "	'" << this->sell4 << "', "
			<< "	'" << this->sell5 << "', "
			<< "	'" << this->buy0 << "', "
			<< "	'" << this->buy1 << "', "
			<< "	'" << this->buy2 << "', "
			<< "	'" << this->buy3 << "', "
			<< "	'" << this->buy4 << "', "
			<< "	'" << this->buy5 << "', "
			<< "	'" << this->factor << "', "
			<< "	'" << this->fleet1Id << "', "
			<< "	'" << this->fleet2Id << "' "
			<< ");";
		query.store();
		query.reset();
	}
	catch (int e)
	{
		std::cout << "MarketReport failed no Id given!" << std::endl;
	}
	catch (mysqlpp::Exception* e)
	{
		std::cout << e->what() << std::endl;
		std::cout << query.str() << std::endl;
		query.reset();
	}
}
