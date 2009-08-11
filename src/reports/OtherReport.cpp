
#include "OtherReport.h"

void OtherReport::setStatus(unsigned short status) {
	this->status = status;
}

void OtherReport::setAction(std::string action) {
	this->action = action;
}

void OtherReport::setShips(std::string ships) {
	this->ships = ships;
}

void OtherReport::setRes(double res0,
					   double res1,
					   double res2,
					   double res3,
					   double res4,
					   double res5) {
	this->res0 = res0;
	this->res1 = res1;
	this->res2 = res2;
	this->res3 = res3;
	this->res4 = res4;
	this->res5 = res5;
}

void OtherReport::setFleetId(unsigned int fleetId) {
	this->fleetId = fleetId;
}

void OtherReport::setEndtime(unsigned int endtime) {
	this->endtime = endtime;
}

void OtherReport::saveOtherReport() {
	My &my = My::instance();
	mysqlpp::Connection *con_ = my.get();
	
	mysqlpp::Query query = con_->query();
	
	try	{
		if (!this->id) throw 0;
		
		query << "INSERT INTO "
			<< "	`reports_other` "
			<< "( "
			<< "	`id`, "
			<< "	`subtype`, "
			<< "	`res_0`, "
			<< "	`res_1`, "
			<< "	`res_2`, "
			<< "	`res_3`, "
			<< "	`res_4`, "
			<< "	`res_5`, "
			<< "	`ships`, "
			<< "	`action`, "
			<< "	`status`, "
			<< "	`fleet_id` "
			<< ") "
			<< "VALUES "
			<< "( "
			<< "	'" << this->id << "', "
			<< "	'" << this->subtype << "', "
			<< "	'" << this->res0 << "', "
			<< "	'" << this->res1 << "', "
			<< "	'" << this->res2 << "', "
			<< "	'" << this->res3 << "', "
			<< "	'" << this->res4 << "', "
			<< "	'" << this->res5 << "', "
			<< "	'" << this->ships << "', "
			<< "	'" << this->action << "', "
			<< "	'" << this->status << "', "
			<< "	'" << this->fleetId << "' "
			<< ");";
		query.store();
		query.reset();
	}
	catch (int e)
	{
		std::cout << "OtherReport failed no Id given!" << std::endl;
	}
	catch (mysqlpp::Exception* e)
	{
		std::cout << e->what() << std::endl;
		std::cout << query.str() << std::endl;
		query.reset();
	}
}