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
		query << "SELECT ";
		query << "	def_id AS id, ";
		query << "	def_name AS name, ";
		query << "	def_shortcomment AS shortcomment, ";
		query << "	def_longcomment AS longcomment, ";
		query << "	def_costs_metal AS costs_metal, ";
		query << "	def_costs_crystal AS costs_crystal, ";
		query << "	def_costs_plastic AS costs_plastic, ";
		query << "	def_costs_fuel AS costs_fuel, ";
		query << "	def_costs_food AS costs_food, ";
		query << "	def_costs_power AS costs_power, ";
		query << "	def_power_use, ";
		query << "	def_fuel_use, ";
		query << "	def_show, ";
		query << "	def_buildable, ";
		query << "	def_order, ";
		query << "	def_heal, ";
		query << "	def_structure, ";
		query << "	def_shield, ";
		query << "	def_weapon, ";
		query << "	def_race_id, ";
		query << "	def_cat_id, ";
		query << "	def_max_count, ";
		query << "	def_points, ";
		query << "	def_jam, ";
		query << "	def_fields ";
		query << "FROM ";
		query << "	defense;";
		mysqlpp::Result dRes = query.store();	
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
		query << "SELECT ";
		query << "	ship_id AS Id, ";
		query << "	ship_name AS Name, ";
		query << "	ship_type_id, ";
		query << "	ship_shortcomment AS shortcomment, ";
		query << "	ship_longcomment AS longcomment, ";
		query << "	ship_costs_metal AS costs_metal, ";
		query << "	ship_costs_crystal AS costs_crystal, ";
		query << "	ship_costs_plastic AS costs_plastic, ";
		query << "	ship_costs_fuel AS costs_fuel, ";
		query << "	ship_costs_food AS costs_food, ";
		query << "	ship_costs_power AS costs_power, ";
		query << "	ship_power_use, ";
		query << "	ship_fuel_use, ";
		query << "	ship_fuel_use_launch, ";
		query << "	ship_fuel_use_landing, ";
		query << "	ship_prod_power, ";
		query << "	ship_capacity, ";
		query << "	ship_people_capacity, ";
		query << "	ship_pilots, ";
		query << "	ship_speed, ";
		query << "	ship_time2start, ";
		query << "	ship_time2land, ";
		query << "	ship_show, ";
		query << "	ship_buildable, ";
		query << "	ship_order, ";
		query << "	ship_actions, ";
		query << "	ship_bounty_bonus, ";
		query << "	ship_heal, ";
		query << "	ship_structure, ";
		query << "	ship_shield, ";
		query << "	ship_weapon, ";
		query << "	ship_race_id, ";
		query << "	ship_launchable, ";
		query << "	ship_fieldsprovide, ";
		query << "	ship_cat_id, ";
		query << "	ship_fakeable, ";
		query << "	special_ship, ";
		query << "	ship_max_count, ";
		query << "	special_ship_max_level, ";
		query << "	special_ship_need_exp, ";
		query << "	special_ship_exp_factor, ";
		query << "	special_ship_bonus_weapon, ";
		query << "	special_ship_bonus_structure, ";
		query << "	special_ship_bonus_shield, ";
		query << "	special_ship_bonus_heal, ";
		query << "	special_ship_bonus_capacity, ";
		query << "	special_ship_bonus_speed, ";
		query << "	special_ship_bonus_pilots, ";
		query << "	special_ship_bonus_tarn, ";
		query << "	special_ship_bonus_antrax, ";
		query << "	special_ship_bonus_forsteal, ";
		query << "	special_ship_bonus_build_destroy, ";
		query << "	special_ship_bonus_antrax_food, ";
		query << "	special_ship_bonus_deactivade, ";
		query << "	ship_points, ";
		query << "	ship_alliance_shipyard_level, ";
		query << "	ship_alliance_costs ";
		query << "FROM ";
		query << "	ships;";
		mysqlpp::Result sRes = query.store();	
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
		query << "SELECT ";
		query << "	tech_id AS Id, ";
		query << "	tech_name AS Name, ";
		query << "	tech_type_id, ";
		query << "	tech_shortcomment AS shortcomment, ";
		query << "	tech_longcomment AS longcomment, ";
		query << "	tech_costs_metal AS costs_metal, ";
		query << "	tech_costs_crystal AS costs_crystal, ";
		query << "	tech_costs_plastic AS costs_plastic, ";
		query << "	tech_costs_fuel AS costs_fuel, ";
		query << "	tech_costs_food AS costs_food, ";
		query << "	tech_costs_power AS costs_power, ";
		query << "	tech_build_costs_factor, ";
		query << "	tech_last_level, ";
		query << "	tech_show, ";
		query << "	tech_order, ";
		query << "	tech_stealable ";
		query << "FROM ";
		query << "	technologies;";
		mysqlpp::Result tRes = query.store();	
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
		mysqlpp::Result bRes = query.store();	
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
		query << "SELECT ";
		query << "	* ";
		query << "FROM ";
		query << "	races;";
		mysqlpp::Result rRes = query.store();	
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
		query << "SELECT ";
		query << "	* ";
		query << "FROM ";
		query << "	sol_types;";
		mysqlpp::Result slRes = query.store();	
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
		query << "SELECT ";
		query << "	* ";
		query << "FROM ";
		query << "	planet_types;";
		mysqlpp::Result pRes = query.store();	
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
		query << "SELECT ";
		query << "	* ";
		query << "FROM ";
		query << "	specialists;";
		mysqlpp::Result spRes = query.store();	
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
