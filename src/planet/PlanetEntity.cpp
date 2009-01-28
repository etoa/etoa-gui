
#include "PlanetEntity.h"

namespace planet
{
	void PlanetEntity::loadData() {
		My &my = My::instance();
		mysqlpp::Connection* con_ = my.get();
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "    planets.*, "
			<< "	users.user_id, "
			<< "	users.user_race_id, "
			<< "	users.user_specialist_id, "
			<< "	users.user_specialist_time, "
			<< "	stars.type_id "
			<< "FROM  "
			<< "  ( "
			<< "  	( "
			<< "		( "
			<< "		entities "
			<< "			INNER JOIN "
			<< "				planets "
			<< "			ON planets.id = entities.id "
			<< "			AND entities.id='" << this->entityId << "' "
			<< "		) "
			<< "		INNER JOIN  "
			<< "			entities AS e "
			<< "		ON e.cell_id=entities.cell_id AND e.pos=0 "
			<< "	) "
			<< "	INNER JOIN  	 "
			<< "		stars "
			<< "	ON stars.id=e.id "
			<< " ) "
			<< "	INNER JOIN  "
			<< "		users  "
			<< "	ON planets.planet_user_id = users.user_id "
			<< "LIMIT 1;";
		mysqlpp::Result pRes = query.store();
		query.reset();
		
		if (pRes) {
			int pSize = pRes.size();
			
			if (pSize) {
				mysqlpp::Row pRow = pRes.at(0);
				
				this->raceId = (int)pRow["user_race_id"];
				this->userId = (int)pRow["user_id"];
				this->solType = (int)pRow["type_id"];
				this->planetType = (int)pRow["planet_type_id"];
				this->speicalistId = (int)pRow["user_specialist_time"] < time(0) ? 0 : (int)pRow["user_specialist_id"];
				
				this->solarPowerBonus = etoa::getSolarPowerBonus((int)pRow["planet_temp_from"], (int)pRow["planet_temp_to"]);
				this->solarFuelBonus = 1 - (etoa::getSolarFuelBonus((int)pRow["planet_temp_from"], (int)pRow["planet_temp_to"]));
				
				this->t = time(0) - (int)pRow["planet_last_updated"];
				
				this->isMain = (bool)pRow["planet_user_main"];
				
				this->ressource[0] = (double)pRow["planet_res_metal"];
				this->ressource[1] = (double)pRow["planet_res_crystal"];
				this->ressource[2] = (double)pRow["planet_res_plastic"];
				this->ressource[3] = (double)pRow["planet_res_fuel"];
				this->ressource[4] = (double)pRow["planet_res_food"];
				this->ressource[5] = (double)pRow["planet_people"];
				this->ressource[6] = 0;
				
				this->store[0] = (double)pRow["planet_store_metal"];
				this->store[1] = (double)pRow["planet_store_crystal"];
				this->store[2] = (double)pRow["planet_store_plastic"];
				this->store[3] = (double)pRow["planet_store_fuel"];
				this->store[4] = (double)pRow["planet_store_food"];
				this->store[5] = (double)pRow["planet_people_place"];
				
				this->cnt[0] = (double)pRow["planet_prod_metal"];
				this->cnt[1] = (double)pRow["planet_prod_crystal"];
				this->cnt[2] = (double)pRow["planet_prod_plastic"];
				this->cnt[3] = (double)pRow["planet_prod_fuel"];
				this->cnt[4] = (double)pRow["planet_prod_food"];
				this->cnt[5] = 0;
				this->cnt[6] = 0;
				this->cnt[7] = 0;
			}
		}
	}
	
	void PlanetEntity::updateValues() {
		for (int i = 0; i < 5; i++) {
			if (this->store[i] > (this->ressource[i]+(this->cnt[i]/3600)*this->t))
				this->ressource[i] = (this->cnt[i]/3600)*this->t;
			else if (this->store[i] > this->ressource[i])
				this->ressource[i] = this->store[i] - this->ressource[i];
			else
				this->ressource[i] = 0;
		}

		this->birthRate = 1.1 + this->planet_->getTypePopulation() + this->race_->getRacePopulation() + this->sol_->getTypePopulation() + this->specialist_->getSpecialistPopulation() - 4;
		this->ressource[6] = this->ressource[5] / 50 * this->birthRate;
		
		if(this->ressource[6] <= 3)
		{
			this->ressource[6] = 3 - this->ressource[5];
		}
		if (!this->ressource[5] && this->isMain)
		{
			this->ressource[5] = 1;
		}
		else if (this->store[5] > (this->ressource[5] + (this->ressource[6] / 3600 * this->t)))
		{
			this->ressource[5] =  (this->ressource[6] / 3600) * this->t;
		}
		else if (this->store[5] <= (this->ressource[5] + (this->ressource[6] / 3600 * this->t)))
		{
			if (this->ressource[5] > this->store[5])
			{
				this->ressource[5] = 0;
			}
			else
			{
				this->ressource[5] = this->store[5] - this->ressource[5];
			}

		}
	}
	
	void PlanetEntity::loadBuildlist() {
		My &my = My::instance();
		mysqlpp::Connection* con_ = my.get();
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "	buildlist_building_id, "
			<< "	buildlist_current_level, "
			<< "	buildlist_prod_percent "
			<< "FROM "
			<< "	buildlist "
			<< "WHERE "
			<< "	buildlist_entity_id='" << this->entityId << "' "
			<< "	AND buildlist_user_id='" << this->userId << "' "
			<< "	AND buildlist_current_level>0;";
		mysqlpp::Result bRes = query.store();
		query.reset();
		
		if (bRes) {
			int bSize = bRes.size();
			
			if (bSize) {
				mysqlpp::Row bRow;
				int level;
				double prodPercent;
				
				DataHandler &DataHandler = DataHandler::instance();
				
				for (mysqlpp::Row::size_type i = 0; i<bSize; i++) { 
					bRow = bRes.at(i);
					
					this->building_ = DataHandler.getBuildingById((int)bRow["buildlist_building_id"]);
					level = (int)bRow["buildlist_current_level"];
					prodPercent = (double)bRow["buildlist_prod_percent"];
					this->fieldsUsed += level * this->building_->getFields();
					level--;
					this->fieldsExtra += this->building_->getFieldsprovide() * pow(this->building_->getProductionFactor() , level);
					
					this->store[0] += round(this->building_->getStoreMetal() * pow(this->building_->getStoreFactor() , level));
					this->store[1] += round(this->building_->getStoreCrystal() * pow(this->building_->getStoreFactor() , level));
					this->store[2] += round(this->building_->getStorePlastic() * pow(this->building_->getStoreFactor() , level));
					this->store[3] += round(this->building_->getStoreFuel() * pow(this->building_->getStoreFactor() , level));
					this->store[4] += round(this->building_->getStoreFood() * pow(this->building_->getStoreFactor() , level));
					this->store[5] += round(this->building_->getPeoplePlace() * pow(this->building_->getStoreFactor() , level));
					
					this->cnt[0] += ceil(this->building_->getProdMetal() * prodPercent * pow(this->building_->getProductionFactor() , level));
					this->cnt[1] += ceil(this->building_->getProdCrystal() * prodPercent * pow(this->building_->getProductionFactor() , level));
					this->cnt[2] += ceil(this->building_->getProdPlastic() * prodPercent * pow(this->building_->getProductionFactor() , level));
					this->cnt[3] += ceil(this->building_->getProdFuel() * prodPercent * pow(this->building_->getProductionFactor() , level));
					this->cnt[4] += ceil(this->building_->getProdFood() * prodPercent * pow(this->building_->getProductionFactor() , level));
					this->cnt[6] += ceil(this->building_->getProdPower() * prodPercent * pow(this->building_->getProductionFactor() , level));
					this->cnt[7] += floor(this->building_->getPowerUse() * prodPercent * pow(this->building_->getProductionFactor() , level));
				}
			}
		}
	}
	
	void PlanetEntity::loadShiplist() {
		My &my = My::instance();
		mysqlpp::Connection* con_ = my.get();
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "	shiplist_ship_id, "
			<< "	shiplist_count "
			<< "FROM "
			<< "	shiplist "
			<< "WHERE "
			<< "	shiplist_entity_id='" << this->entityId << "' "
			<< "	AND shiplist_user_id='" << this->userId << "' "
			<< "	AND shiplist_count>0;";
		mysqlpp::Result sRes = query.store();
		query.reset();
		
		if (sRes) {
			int sSize = sRes.size();
			
			if (sSize) {
				mysqlpp::Row sRow;
				
				DataHandler &DataHandler = DataHandler::instance();
				
				for (mysqlpp::Row::size_type i = 0; i<sSize; i++) { 
					sRow = sRes.at(i);
					
					this->ship_ = DataHandler.getShipById((int)sRow["shiplist_ship_id"]);
					
					if (this->ship_->getProdPower()) 
						this->cnt[6] += (this->ship_->getProdPower() + this->solarPowerBonus) * (double)(sRow["shiplist_count"]);
				}
			}
		}
	}
	
	void PlanetEntity::loadDeflist() {
		My &my = My::instance();
		mysqlpp::Connection* con_ = my.get();
		mysqlpp::Query query = con_->query();
		query << "SELECT "
			<< "	deflist_def_id, "
			<< "	deflist_count "
			<< "FROM "
			<< "	deflist "
			<< "WHERE "
			<< "	deflist_entity_id='" << this->entityId << "' "
			<< "	AND deflist_user_id='" << this->userId << "' "
			<< "	AND deflist_count>0;";
		mysqlpp::Result dRes = query.store();
		query.reset();
		
		if (dRes) {
			int dSize = dRes.size();
			
			if (dSize) {
				mysqlpp::Row dRow;
				
				DataHandler &DataHandler = DataHandler::instance();
				
				for (mysqlpp::Row::size_type i = 0; i<dSize; i++) { 
					dRow = dRes.at(i);
					
					this->def_ = DataHandler.getDefById((int)dRow["deflist_def_id"]);
					
					this->fieldsUsed += (int)dRow["deflist_count"] * this->def_->getFields();
				}
			}
		}
	}
	
	void PlanetEntity::addBoni() {
		this->cnt[0] += (this->cnt[0] * (this->planet_->getTypeMetal() + this->race_->getRaceMetal() + this->sol_->getTypeMetal() + this->specialist_->getSpecialistProdMetal() - 4));
		this->cnt[1] += (this->cnt[1] * (this->planet_->getTypeCrystal() + this->race_->getRaceCrystal() + this->sol_->getTypeCrystal() + this->specialist_->getSpecialistProdCrystal() - 4));
		this->cnt[2] += (this->cnt[2] * (this->planet_->getTypePlastic() + this->race_->getRacePlastic() + this->sol_->getTypePlastic() + this->specialist_->getSpecialistProdPlastic() - 4));
		this->cnt[3] += (this->cnt[3] * (this->planet_->getTypeFuel() + this->race_->getRaceFuel() + this->sol_->getTypeFuel() + this->specialist_->getSpecialistProdFuel() + this->solarFuelBonus - 5));
		this->cnt[4] += (this->cnt[4] * (this->planet_->getTypeFood() + this->race_->getRaceFood() + this->sol_->getTypeFood() + this->specialist_->getSpecialistProdFood() - 4));
		this->cnt[6] += (this->cnt[6] * (this->planet_->getTypePower() + this->race_->getRacePower() + this->sol_->getTypePower() + this->specialist_->getSpecialistPower() - 4));
		
		// Bei ungenügend Energie Anpassung vornehmen
		if (this->cnt[7]>this->cnt[6]) {
			this->cnt[0] = floor(this->cnt[0] * this->cnt[6] / this->cnt[7]);
			this->cnt[1] = floor(this->cnt[1] * this->cnt[6] / this->cnt[7]);
			this->cnt[2] = floor(this->cnt[2] * this->cnt[6] / this->cnt[7]);
			this->cnt[3] = floor(this->cnt[3] * this->cnt[6] / this->cnt[7]);
			this->cnt[4] = floor(this->cnt[4] * this->cnt[6] / this->cnt[7]);
		}
		else {
			this->cnt[0] = floor(this->cnt[0]);
			this->cnt[1] = floor(this->cnt[1]);
			this->cnt[2] = floor(this->cnt[2]);
			this->cnt[3] = floor(this->cnt[3]);
			this->cnt[4] = floor(this->cnt[4]);
			this->cnt[6] = floor(this->cnt[6]);
		}
	}
	
	void PlanetEntity::save() {
		My &my = My::instance();
		mysqlpp::Connection* con_ = my.get();
		mysqlpp::Query query = con_->query();
		query << std::setprecision(18);
		query << "UPDATE "
			<< "	planets "
			<< "SET "
			<< "	planet_fields_extra=" << this->fieldsExtra << ", "
			<< "	planet_fields_used=" << this->fieldsUsed << ", "
			<< "	planet_res_metal=planet_res_metal+'" << this->ressource[0] << "', "
			<< "	planet_res_crystal=planet_res_crystal+'" << this->ressource[1] << "', "
			<< "	planet_res_plastic=planet_res_plastic+'" << this->ressource[2] << "', "
			<< "	planet_res_fuel=planet_res_fuel+'" << this->ressource[3] << "', "
			<< "	planet_res_food=planet_res_food+'" << this->ressource[4] << "', "
			<< "	planet_use_power=" << this->cnt[7] << ", "
			<< "	planet_last_updated='" << time(0) << "', "
			<< "	planet_prod_metal=" << this->cnt[0] << ", "
			<< "	planet_prod_crystal=" << this->cnt[1] << ", "
			<< "	planet_prod_plastic=" << this->cnt[2] << ", "
			<< "	planet_prod_fuel=" << this->cnt[3] << ", "
			<< "	planet_prod_food=" << this->cnt[4] << ", "
			<< "	planet_prod_power=" << this->cnt[6] << ", "
			<< "	planet_prod_people=" << this->ressource[6] << ", "
			<< "	planet_store_metal=" << this->store[0] << ", "
			<< "	planet_store_crystal=" << this->store [1] << ", "
			<< "	planet_store_plastic=" << this->store[2] << ", "
			<< "	planet_store_fuel=" << this->store[3] << ", "
			<< "	planet_store_food=" << this->store[4] << ", "
			<< "	planet_people=planet_people+'" << this->ressource[5] << "', "
			<< "	planet_people_place=" << this->store[5] << " "
			<< "WHERE "
			<< "	id='" << this->entityId << "' "
			<< "LIMIT 1;";
		query.store();
		query.reset();
	}
	
	void PlanetEntity::saveRes() {
		My &my = My::instance();
		mysqlpp::Connection* con_ = my.get();
		mysqlpp::Query query = con_->query();
		query << std::setprecision(18);
		query << "UPDATE "
			<< "	planets "
			<< "SET "
			<< "	planet_res_metal=planet_res_metal+'" << this->ressource[0] << "', "
			<< "	planet_res_crystal=planet_res_crystal+'" << this->ressource[1] << "', "
			<< "	planet_res_plastic=planet_res_plastic+'" << this->ressource[2] << "', "
			<< "	planet_res_fuel=planet_res_fuel+'" << this->ressource[3] << "', "
			<< "	planet_res_food=planet_res_food+'" << this->ressource[4] << "', "
			<< "	planet_last_updated='" << time(0) << "', "
			<< "	planet_prod_people=" << this->ressource[6] << ", "
			<< "	planet_people=planet_people+'" << this->ressource[5] <<"' "
			<< "WHERE "
			<< "	id='" << this->entityId << "' "
			<< "LIMIT 1;";
		query.store();
		query.reset();
	}
}
