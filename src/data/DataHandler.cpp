#include <map>
#include <vector>
#include <string>
#include "../MysqlHandler.h"
#define MYSQLPP_MYSQL_HEADERS_BURIED
#include <mysql++/mysql++.h>

#include "DataHandler.h"
#include "Data.h"
#include "ShipData.h"
#include "DefData.h"
#include "TechData.h"
#include "BuildingData.h"

	// Liefert eine Kopie des Datenwertes zurück
	ShipData::ShipData* DataHandler::getShipById(int id) {
		return this->shipData[idShipConverter[id] ];
	}
	
	DefData::DefData* DataHandler::getDefById(int id) {
		return this->defData[idDefConverter[id] ];
	}
	
	TechData::TechData* DataHandler::getTechById(int id) {
		return this->techData[idTechConverter[id] ];
	}
	
	BuildingData::BuildingData* DataHandler::getBuildingById(int id) {
		return this->buildingData[idBuildingConverter[id] ];
	}
	
	RaceData::RaceData* DataHandler::getRaceById(int id) {
		return this->raceData[idRaceConverter[id] ];
	}
	
	SolData::SolData* DataHandler::getSolById(int id) {
		return this->solData[idSolConverter[id] ];
	}
	
	PlanetData::PlanetData* DataHandler::getPlanetById(int id) {
		return this->planetData[idPlanetConverter[id] ];
	}
	
	SpecialistData::SpecialistData* DataHandler::getSpecialistById(int id) {
		return this->specialistData[idSpecialistConverter[id] ];
	}
		
	ShipData::ShipData* DataHandler::getShipByName(std::string name) {
		return this->shipData[nameConverter[name] ];
	}
	
	DefData::DefData* DataHandler::getDefByName(std::string name) {
		return this->defData[nameConverter[name] ];
	}
			
	TechData::TechData* DataHandler::getTechByName(std::string name) {
		return this->techData[nameConverter[name] ];
	}
			
	BuildingData::BuildingData* DataHandler::getBuildingByName(std::string name) {
		return this->buildingData[nameConverter[name] ];
	}
	
	// Clear the Data and reload it
	void DataHandler::reloadData()
	{
		this->defData.clear();
		this->shipData.clear();
		this->techData.clear();
		this->buildingData.clear();
		this->raceData.clear();
		this->solData.clear();
		this->planetData.clear();
		this->specialistData.clear();		
		this->idDefConverter.clear();
		this->idShipConverter.clear();
		this->idTechConverter.clear();
		this->idBuildingConverter.clear();
		this->idRaceConverter.clear();
		this->idSolConverter.clear();
		this->idPlanetConverter.clear();
		this->idSpecialistConverter.clear();
		
		loadData();
	}
	
	
	//	Initialisiert die Datenwerte
	void DataHandler::loadData ()
	{
		My &my = My::instance();
		mysqlpp::Connection *con = my.get();

		this->counter = 0;
		mysqlpp::Query query = con->query();
		query << "SELECT "
			<< "	def_id AS id, "
			<< "	def_name AS name, "
			<< "	def_shortcomment AS shortcomment, "
			<< "	def_longcomment AS longcomment, "
			<< "	def_costs_metal AS costs_metal, "
			<< "	def_costs_crystal AS costs_crystal, "
			<< "	def_costs_plastic AS costs_plastic, "
			<< "	def_costs_fuel AS costs_fuel, "
			<< "	def_costs_food AS costs_food, "
			<< "	def_costs_power AS costs_power, "
			<< "	def_power_use, "
			<< "	def_fuel_use, "
			<< "	def_show, "
			<< "	def_buildable, "
			<< "	def_order, "
			<< "	def_heal, "
			<< "	def_structure, "
			<< "	def_shield, "
			<< "	def_weapon, "
			<< "	def_race_id, "
			<< "	def_cat_id, "
			<< "	def_max_count, "
			<< "	def_points, "
			<< "	def_jam, "
			<< "	def_fields "
			<< "FROM "
			<< "	defense;";
		RESULT_TYPE dRes = query.store();
		query.reset();
		if (dRes) {
			unsigned int dSize = dRes.size();
			DefData* object;

			if (dSize>0) {
				mysqlpp::Row dRow;

				for (mysqlpp::Row::size_type i = 0; i<dSize; i++) {
					dRow = dRes.at(i);
					this->idDefConverter[(int)(dRow["id"]) ] =  this->counter;
					this->nameConverter[std::string(dRow["name"]) ] = this->counter;
					object = new DefData(dRow);
					this->defData.push_back(object);
					this->counter++;
				}
			}
		}
		
		this->counter = 0;
		query << "SELECT "
			<< "	ship_id AS Id, "
			<< "	ship_name AS Name, "
			<< "	ship_type_id, "
			<< "	ship_shortcomment AS shortcomment, "
			<< "	ship_longcomment AS longcomment, "
			<< "	ship_costs_metal AS costs_metal, "
			<< "	ship_costs_crystal AS costs_crystal, "
			<< "	ship_costs_plastic AS costs_plastic, "
			<< "	ship_costs_fuel AS costs_fuel, "
			<< "	ship_costs_food AS costs_food, "
			<< "	ship_costs_power AS costs_power, "
			<< "	ship_power_use, "
			<< "	ship_fuel_use, "
			<< "	ship_fuel_use_launch, "
			<< "	ship_fuel_use_landing, "
			<< "	ship_prod_power, "
			<< "	ship_capacity, "
			<< "	ship_people_capacity, "
			<< "	ship_pilots, "
			<< "	ship_speed, "
			<< "	ship_time2start, "
			<< "	ship_time2land, "
			<< "	ship_show, "
			<< "	ship_buildable, "
			<< "	ship_order, "
			<< "	ship_actions, "
			<< "	ship_bounty_bonus, "
			<< "	ship_heal, "
			<< "	ship_structure, "
			<< "	ship_shield, "
			<< "	ship_weapon, "
			<< "	ship_race_id, "
			<< "	ship_launchable, "
			<< "	ship_fieldsprovide, "
			<< "	ship_cat_id, "
			<< "	ship_fakeable, "
			<< "	special_ship, "
			<< "	ship_max_count, "
			<< "	special_ship_max_level, "
			<< "	special_ship_need_exp, "
			<< "	special_ship_exp_factor, "
			<< "	special_ship_bonus_weapon, "
			<< "	special_ship_bonus_structure, "
			<< "	special_ship_bonus_shield, "
			<< "	special_ship_bonus_heal, "
			<< "	special_ship_bonus_capacity, "
			<< "	special_ship_bonus_speed, "
			<< "	special_ship_bonus_pilots, "
			<< "	special_ship_bonus_tarn, "
			<< "	special_ship_bonus_antrax, "
			<< "	special_ship_bonus_forsteal, "
			<< "	special_ship_bonus_build_destroy, "
			<< "	special_ship_bonus_antrax_food, "
			<< "	special_ship_bonus_deactivade, "
			<< "	ship_points, "
			<< "	ship_alliance_shipyard_level, "
			<< "	ship_alliance_costs "
			<< "FROM "
			<< "	ships;";
		RESULT_TYPE sRes = query.store();
		query.reset();
		if (sRes) {
			unsigned int sSize = sRes.size();
			ShipData* object;

			if (sSize>0) {
				mysqlpp::Row sRow;
				
				for (mysqlpp::Row::size_type i = 0; i<sSize; i++) {
					sRow = sRes.at(i);
					this->idShipConverter[(int)(sRow["id"]) ] =  this->counter;
					this->nameConverter[std::string(sRow["name"]) ] = this->counter;
					object = new ShipData(sRow);
					this->shipData.push_back(object);
					this->counter++;
					/** Save the id in the action container **/
					
					
				}
			}
		}
		
		this->counter = 0;
		query << "SELECT "
			<< "	tech_id AS Id, "
			<< "	tech_name AS Name, "
			<< "	tech_type_id, "
			<< "	tech_shortcomment AS shortcomment, "
			<< "	tech_longcomment AS longcomment, "
			<< "	tech_costs_metal AS costs_metal, "
			<< "	tech_costs_crystal AS costs_crystal, "
			<< "	tech_costs_plastic AS costs_plastic, "
			<< "	tech_costs_fuel AS costs_fuel, "
			<< "	tech_costs_food AS costs_food, "
			<< "	tech_costs_power AS costs_power, "
			<< "	tech_build_costs_factor, "
			<< "	tech_last_level, "
			<< "	tech_show, "
			<< "	tech_order, "
			<< "	tech_stealable "
			<< "FROM "
			<< "	technologies;";
		RESULT_TYPE tRes = query.store();
		query.reset();
		if (tRes) {
			unsigned int tSize = tRes.size();
			TechData* object;

			if (tSize>0) {
				mysqlpp::Row tRow;
				
				for (mysqlpp::Row::size_type i = 0; i<tSize; i++) {
					tRow = tRes.at(i);
					this->idTechConverter[(int)(tRow["id"]) ] =  this->counter;
					this->nameConverter[std::string(tRow["name"]) ] = this->counter;
					object = new TechData(tRow);
					this->techData.push_back(object);
					this->counter++;
				}
			}
		}
		
		this->counter = 0;	 	 	 	 	 	 	 	 	 	 	 	 	 	
		query << "SELECT "
			<< "	building_id AS Id, "
			<< "	building_name AS Name, "
			<< "	building_type_id, "
			<< "	building_shortcomment AS shortcomment, "
			<< "	building_longcomment AS longcomment, "
			<< "	building_costs_metal AS costs_metal, "
			<< "	building_costs_crystal AS costs_crystal, "
			<< "	building_costs_plastic AS costs_plastic, "
			<< "	building_costs_fuel AS costs_fuel, "
			<< "	building_costs_food AS costs_food, "
			<< "	building_costs_power AS costs_power, "
			<< "	building_build_costs_factor, "
			<< "	building_demolish_costs_factor, "
			<< "	building_power_use, "
			<< "	building_power_req, "
			<< "	building_fuel_use, "
			<< "	building_prod_metal, "
			<< "	building_prod_crystal, "
			<< "	building_prod_plastic, "
			<< "	building_prod_fuel, "
			<< "	building_prod_food, "
			<< "	building_prod_power, "
			<< "	building_production_factor, "
			<< "	building_store_metal, "
			<< "	building_store_crystal, "
			<< "	building_store_plastic, "
			<< "	building_store_fuel, "
			<< "	building_store_food, "
			<< "	building_store_factor, "
			<< "	building_people_place, "
			<< "	building_last_level, "
			<< "	building_fields, "
			<< "	building_show, "
			<< "	building_order, "
			<< "	building_fieldsprovide, "
			<< "	building_workplace, "
			<< "	building_bunker_res, "
			<< "	building_bunker_fleet_count, "
			<< "	building_bunker_fleet_space "
			<< "FROM "
			<< "	buildings;";
		RESULT_TYPE bRes = query.store();
		query.reset();
		if (bRes) {
			unsigned int bSize = bRes.size();
			BuildingData* object;

			if (bSize>0) {
				mysqlpp::Row bRow;
				
				for (mysqlpp::Row::size_type i = 0; i<bSize; i++) {
					bRow = bRes.at(i);
					this->idBuildingConverter[(int)(bRow["id"]) ] =  this->counter;
					this->nameConverter[std::string(bRow["name"]) ] = this->counter;
					object = new BuildingData(bRow);
					this->buildingData.push_back(object);
					this->counter++;
				}
			}
		}
		
		this->counter = 0;	 	 	 	 	 	 	 	 	 	 	
		query << "SELECT "
			<< "	* "
			<< "FROM "
			<< "	races;";
		RESULT_TYPE rRes = query.store();
		query.reset();
		if (rRes) {
			unsigned int rSize = rRes.size();
			RaceData* object;

			if (rSize>0) {
				mysqlpp::Row rRow;
				
				for (mysqlpp::Row::size_type i = 0; i<rSize; i++) {
					rRow = rRes.at(i);
					this->idRaceConverter[(int)(rRow["race_id"]) ] =  this->counter;
					object = new RaceData(rRow);
					this->raceData.push_back(object);
					this->counter++;
				}
			}
		}
		
		this->counter = 0;	 	 	 	 	 	 	 	 	 	 	 	 	 	
		query << "SELECT "
			<< "	* "
			<< "FROM "
			<< "	sol_types;";
		RESULT_TYPE slRes = query.store();
		query.reset();
		if (slRes) {
			unsigned int slSize = slRes.size();
			SolData* object;

			if (slSize>0) {
				mysqlpp::Row slRow;
				
				for (mysqlpp::Row::size_type i = 0; i<slSize; i++) {
					slRow = slRes.at(i);
					this->idSolConverter[(int)(slRow["sol_type_id"]) ] =  this->counter;
					object = new SolData(slRow);
					this->solData.push_back(object);
					this->counter++;
				}
			}
		}
		
		this->counter = 0;	 	 	 	 	 	 	 	 	 	 	 	 	 	
		query << "SELECT "
			<< "	* "
			<< "FROM "
			<< "	planet_types;";
		RESULT_TYPE pRes = query.store();
		query.reset();
		if (pRes) {
			unsigned int pSize = pRes.size();
			PlanetData* object;

			if (pSize>0) {
				mysqlpp::Row pRow;
				
				for (mysqlpp::Row::size_type i = 0; i<pSize; i++) {
					pRow = pRes.at(i);
					this->idPlanetConverter[(int)(pRow["type_id"]) ] =  this->counter;
					object = new PlanetData(pRow);
					this->planetData.push_back(object);
					this->counter++;
				}
			}
		}
		
		this->counter = 0;	 	 	 	 	 	 	 	 	 	 	 	 	 	
		query << "SELECT "
			<< "	* "
			<< "FROM "
			<< "	specialists;";
		RESULT_TYPE spRes = query.store();
		query.reset();
		if (spRes) {
			unsigned int spSize = spRes.size();
			SpecialistData* object;

			if (spSize>0) {
				mysqlpp::Row spRow;
				
				for (mysqlpp::Row::size_type i = 0; i<spSize; i++) 
				{
					spRow = spRes.at(i);
					this->idSpecialistConverter[(int)(spRow["specialist_id"]) ] =  this->counter;
					object = new SpecialistData(spRow);
					this->specialistData.push_back(object);
					this->counter++;
				}
			}
		}
	}
