
#include "SpyReport.h"

void SpyReport::setBuildings(std::string buildings) {
	this->buildings = buildings;
}

void SpyReport::setTechnologies(std::string technologies) {
	this->technologies = technologies;
}

void SpyReport::setShips(std::string ships) {
	this->ships = ships;
}

void SpyReport::setDefense(std::string defense) {
	this->defense = defense;
}

void SpyReport::setRes(double res0,
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

void SpyReport::setFleetId(unsigned int fleetId) {
	this->fleetId = fleetId;
}

void SpyReport::setSpydefense(unsigned short spydefense) {
	this->spydefense = spydefense;
}

void SpyReport::setCoverage(unsigned short coverage) {
	this->coverage = coverage;
}

void SpyReport::saveSpyReport() {
	My &my = My::instance();
	mysqlpp::Connection *con_ = my.get();
	
	mysqlpp::Query query = con_->query();
	
	try	{
		if (!this->id) throw 0;
		query << std::setprecision(18);
		query << "INSERT INTO "
			<< "	`reports_spy` "
			<< "( "
			<< "	`id`, "
			<< "	`subtype`, "
			<< "	`buildings`, "
			<< "	`technologies`, "
			<< "	`ships`, "
			<< "	`defense`, "
			<< "	`res_0`, "
			<< "	`res_1`, "
			<< "	`res_2`, "
			<< "	`res_3`, "
			<< "	`res_4`, "
			<< "	`res_5`, "
			<< "	`spydefense`, "
			<< "	`coverage`, "
			<< "	`fleet_id` "
			<< ") "
			<< "VALUES "
			<< "( "
			<< "	'" << this->id << "', "
			<< "	'" << this->subtype << "', "
			<< "	'" << this->buildings << "', "
			<< "	'" << this->technologies << "', "
			<< "	'" << this->ships << "', "
			<< "	'" << this->defense << "', "
			<< "	'" << this->res0 << "', "
			<< "	'" << this->res1 << "', "
			<< "	'" << this->res2 << "', "
			<< "	'" << this->res3 << "', "
			<< "	'" << this->res4 << "', "
			<< "	'" << this->res5 << "', "
			<< "	'" << this->spydefense << "', "
			<< "	'" << this->coverage << "', "
			<< "	'" << this->fleetId << "' "
			<< ");";
		query.store();
		query.reset();
	}
	catch (int e)
	{
		std::cout << "SpyReport failed no Id given!" << std::endl;
	}
	catch (mysqlpp::Exception* e)
	{
		std::cout << e->what() << std::endl;
		std::cout << query.str() << std::endl;
		query.reset();
	}
}
